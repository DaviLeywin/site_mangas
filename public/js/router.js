import { RenderPageHome } from '/SiteLivros/public/js/pages/home.js';
import { RenderPageManga } from '/SiteLivros/public/js/pages/manga.js';
import { RenderPageGender } from '/SiteLivros/public/js/pages/gender.js';
import { RenderPageChapter } from '/SiteLivros/public/js/pages/chapter.js';

const BASE_PATH = '/SiteLivros';
const pageCache = new Map();

async function MudarHtml(documento) {
    const root = document.getElementById('root');
    root.innerHTML = '<div class="page-loader"><span></span><p>Carregando...</p></div>';

    if (pageCache.has(documento)) {
        root.innerHTML = pageCache.get(documento);
        return;
    }

    const resposta = await fetch(`${BASE_PATH}/public/pages/${documento}.html`, {
        cache: 'force-cache',
    });

    if (!resposta.ok) {
        throw new Error(`Não foi possível carregar a página ${documento}.html`);
    }

    const dados = await resposta.text();
    pageCache.set(documento, dados);
    root.innerHTML = dados;
}

async function CarregarPagina(nomePagina, parametro = null) {
    const relacoes = {
        home: RenderPageHome,
        gender: RenderPageGender,
        chapter: RenderPageChapter,
        manga: RenderPageManga,
    };

    const render = relacoes[nomePagina];

    if (!render) {
        await irPara('/Inicio');
        return;
    }

    window.onscroll = null;
    await MudarHtml(nomePagina);
    await render(parametro);
    configurarAcoesGlobais();
}

class Rotas {
    constructor() {
        this.rotas = [
            {
                regex: /^\/$|^\/Inicio$/,
                pagina: 'home',
                extrairParametro: () => null,
            },
            {
                regex: /^\/Historico$/,
                pagina: 'home',
                extrairParametro: () => ({ scrollTo: 'historico-home' }),
            },
            {
                regex: /^\/Manga\/([^<>"'\\|?*\[\]{}]+)\/Capitulo\/([^/]+)$/u,
                pagina: 'chapter',
                extrairParametro: (match) => ({ titulo: match[1], numeroCapitulo: match[2] }),
            },
            {
                regex: /^\/Manga\/([^<>"'\\|?*\[\]{}]+)$/u,
                pagina: 'manga',
                extrairParametro: (match) => match[1],
            },
            {
                regex: /^\/Genero\/([^<>"'\\|?*\[\]{}]+)$/u,
                pagina: 'gender',
                extrairParametro: (match) => match[1],
            },
        ];
    }

    async Execute() {
        const caminho = window.location.pathname.replace(BASE_PATH, '') || '/Inicio';
        const urlFormatada = decodeURIComponent(caminho);

        for (const rota of this.rotas) {
            const match = urlFormatada.match(rota.regex);

            if (match) {
                const parametro = rota.extrairParametro(match);
                await CarregarPagina(rota.pagina, parametro);
                return;
            }
        }

        history.replaceState(null, '', `${BASE_PATH}/Inicio`);
        await CarregarPagina('home');
    }
}

export const router = new Rotas();

export async function irPara(caminho, state = null) {
    history.pushState(state, '', `${BASE_PATH}${caminho}`);
    await router.Execute();
}

export function formatarTituloParaUrl(titulo) {
    return encodeURIComponent(String(titulo).trim().replaceAll(' ', '-'));
}

function configurarAcoesGlobais() {
    document.querySelectorAll('[data-route]').forEach((elemento) => {
        elemento.onclick = async () => {
            await irPara(elemento.dataset.route);
        };
    });

    document.querySelectorAll('[data-scroll]').forEach((elemento) => {
        elemento.onclick = async () => {
            const destino = elemento.dataset.scroll;

            if (!document.getElementById(destino)) {
                await irPara('/Inicio');
            }

            requestAnimationFrame(() => {
                document.getElementById(destino)?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start',
                });
            });
        };
    });

    const formPesquisa = document.getElementById('form-pesquisa');
    const inputPesquisa = document.getElementById('barra-de-pesquisa');

    if (formPesquisa && inputPesquisa) {
        formPesquisa.onsubmit = (evento) => {
            evento.preventDefault();
            filtrarCards(inputPesquisa.value);
        };

        inputPesquisa.oninput = () => filtrarCards(inputPesquisa.value);
    }
}

function filtrarCards(valor) {
    const termo = valor?.trim().toLowerCase() || '';
    let visiveis = 0;

    document.querySelectorAll('.manga, .special-card, .ranking-item').forEach((card) => {
        const titulo = card.dataset.titulo?.toLowerCase() || card.textContent.toLowerCase();
        const deveOcultar = Boolean(termo) && !titulo.includes(termo);
        card.classList.toggle('hidden', deveOcultar);
        if (!deveOcultar) visiveis += 1;
    });

    document.querySelectorAll('[data-search-empty]').forEach((elemento) => {
        elemento.classList.toggle('hidden', !termo || visiveis > 0);
    });
}

function preCarregarTemplates() {
    ['home', 'manga', 'gender', 'chapter'].forEach((pagina) => {
        fetch(`${BASE_PATH}/public/pages/${pagina}.html`, { cache: 'force-cache' })
            .then((resposta) => resposta.ok ? resposta.text() : '')
            .then((html) => {
                if (html) pageCache.set(pagina, html);
            })
            .catch(() => {});
    });
}

window.addEventListener('popstate', () => router.Execute());
window.addEventListener('load', () => {
    router.Execute();
    if ('requestIdleCallback' in window) {
        requestIdleCallback(preCarregarTemplates);
    } else {
        setTimeout(preCarregarTemplates, 600);
    }
});
