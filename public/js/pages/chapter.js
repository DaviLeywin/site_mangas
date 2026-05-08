import { irPara, formatarTituloParaUrl } from '../router.js';
import { buscarMangaMockPorTitulo } from '../data/mockData.js';
import { obterProgressoDoManga, salvarHistorico, salvarProgresso } from '../utils/storage.js';
import { fetchJsonComFallback } from '../utils/api.js';
import { criarImagemOtimizada, escaparHtml, obterCaminhoCapa } from '../utils/dom.js';

let capituloAtual = null;
let salvamentoTimer = null;
let ultimoSalvamento = 0;

export async function RenderPageChapter(params) {
    const titulo = params?.titulo;
    const numeroCapitulo = params?.numeroCapitulo;

    const manga = await buscarManga(titulo);

    if (!manga) {
        renderizarErro('Mangá não encontrado.');
        return;
    }

    const mangaNormalizado = normalizarManga(manga);
    capituloAtual = encontrarCapitulo(mangaNormalizado, numeroCapitulo);

    if (!capituloAtual) {
        renderizarErro('Capítulo não encontrado.');
        return;
    }

    renderizarLeitor(mangaNormalizado, capituloAtual);
    configurarControles(mangaNormalizado, capituloAtual);
    configurarSalvamentoDeProgresso(mangaNormalizado, capituloAtual);
    restaurarProgresso(mangaNormalizado);
}

async function buscarManga(titulo) {
    return fetchJsonComFallback(
        `/GetMangaGeneroAutorCapitulos/${titulo}`,
        () => buscarMangaMockPorTitulo(titulo),
        {
            cacheKey: `manga:${titulo}`,
            timeout: 900,
        }
    );
}

function normalizarManga(manga) {
    return {
        ...manga,
        AUTOR: manga.AUTOR || { NOME: 'Autor desconhecido' },
        CAPITULOS: (manga.CAPITULOS || []).sort((a, b) => Number(a.NUMERO_CAPITULO) - Number(b.NUMERO_CAPITULO)),
    };
}

function encontrarCapitulo(manga, numeroCapitulo) {
    return manga.CAPITULOS.find((capitulo) => String(capitulo.NUMERO_CAPITULO) === String(numeroCapitulo)) || null;
}

function renderizarLeitor(manga, capitulo) {
    document.getElementById('reader-manga-title').textContent = manga.TITULO;
    document.getElementById('reader-chapter-title').textContent = `Capítulo ${capitulo.NUMERO_CAPITULO} — ${capitulo.TITULO_CAPITULO || 'Sem título'}`;

    const pagesContainer = document.getElementById('reader-pages');
    pagesContainer.innerHTML = '';

    const paginas = gerarPaginasDoCapitulo(manga, capitulo);
    
    paginas.forEach((pagina, index) => {
        const page = document.createElement('section');
        page.classList.add('reader-page');
        page.dataset.pageIndex = index;

        if (ehImagem(pagina)) {
            page.classList.add('reader-page-image');
            const img = criarImagemOtimizada(pagina, `Página ${index + 1} do capítulo ${capitulo.NUMERO_CAPITULO}`);
            page.appendChild(img);
        } else {
            page.innerHTML = criarPaginaPlaceholder(manga, capitulo, pagina, index);
        }

        pagesContainer.appendChild(page);
    });
}

function gerarPaginasDoCapitulo(manga, capitulo) {
    const paginas = capitulo.PAGES || capitulo.PAGINAS || capitulo.pages;

    if (Array.isArray(paginas) && paginas.length) {
        return paginas;
    }

    return [
        `Abertura de ${manga.TITULO}`,
        'Desenvolvimento da cena principal',
        'Conflito, descoberta ou virada do capítulo',
        'Momento de tensão antes do final',
        'Gancho para o próximo capítulo',
    ];
}

function criarPaginaPlaceholder(manga, capitulo, texto, index) {
    const capa = obterCaminhoCapa(manga.CAPA_URL);

    return `
        <div class="reader-page-inner" style="--reader-cover: url('${capa}')">
            <span>Página ${index + 1}</span>
            <h2>${escaparHtml(manga.TITULO)}</h2>
            <p>Capítulo ${escaparHtml(capitulo.NUMERO_CAPITULO)}</p>
            <small>${escaparHtml(texto)}</small>
        </div>
    `;
}

function ehImagem(valor) {
    return typeof valor === 'string' && /\.(png|jpe?g|webp|gif|avif)(\?.*)?$/i.test(valor);
}

function configurarControles(manga, capitulo) {
    const indiceAtual = manga.CAPITULOS.findIndex((item) => String(item.NUMERO_CAPITULO) === String(capitulo.NUMERO_CAPITULO));
    const anterior = indiceAtual > 0 ? manga.CAPITULOS[indiceAtual - 1] : null;
    const proximo = indiceAtual < manga.CAPITULOS.length - 1 ? manga.CAPITULOS[indiceAtual + 1] : null;

    const botaoVoltar = document.getElementById('voltar-manga');
    const botaoAnterior = document.getElementById('capitulo-anterior');
    const botaoProximo = document.getElementById('proximo-capitulo');
    const botaoTopo = document.getElementById('reader-top');

    botaoVoltar.onclick = () => irPara(`/Manga/${formatarTituloParaUrl(manga.TITULO)}`);
    botaoTopo.onclick = () => window.scrollTo({ top: 0, behavior: 'smooth' });

    botaoAnterior.disabled = !anterior;
    botaoProximo.disabled = !proximo;

    botaoAnterior.onclick = () => {
        if (anterior) abrirCapitulo(manga, anterior);
    };

    botaoProximo.onclick = () => {
        if (proximo) abrirCapitulo(manga, proximo);
    };
}

function configurarSalvamentoDeProgresso(manga, capitulo) {
    window.onscroll = () => {
        clearTimeout(salvamentoTimer);
        salvamentoTimer = setTimeout(() => salvarPosicaoAtual(manga, capitulo), 700);
    };

    salvarPosicaoAtual(manga, capitulo);
}

function salvarPosicaoAtual(manga, capitulo) {
    const agora = Date.now();
    if (agora - ultimoSalvamento < 500) return;
    ultimoSalvamento = agora;

    const scrollTop = window.scrollY || document.documentElement.scrollTop;
    const alturaTotal = document.documentElement.scrollHeight - window.innerHeight;
    const scrollPercent = alturaTotal > 0 ? Math.min(100, Math.max(0, (scrollTop / alturaTotal) * 100)) : 0;
    const pageIndex = calcularPaginaAtual();

    const item = {
        mangaId: manga.ID,
        numeroCapitulo: capitulo.NUMERO_CAPITULO,
        chapterId: capitulo.ID,
        pageIndex,
        scrollPercent,
        manga: {
            ID: manga.ID,
            TITULO: manga.TITULO,
            CAPA_URL: manga.CAPA_URL,
            STATUS: manga.STATUS,
        },
    };

    salvarProgresso(item);
    salvarHistorico({ manga, capitulo, scrollPercent, pageIndex });
}

function calcularPaginaAtual() {
    const paginas = [...document.querySelectorAll('.reader-page')];
    let paginaAtual = 0;

    paginas.forEach((pagina, index) => {
        const rect = pagina.getBoundingClientRect();
        if (rect.top <= window.innerHeight * 0.45) {
            paginaAtual = index;
        }
    });

    return paginaAtual;
}

function restaurarProgresso(manga) {
    const progresso = obterProgressoDoManga(manga.ID);

    if (!progresso || String(progresso.numeroCapitulo) !== String(capituloAtual.NUMERO_CAPITULO)) {
        window.scrollTo({ top: 0 });
        return;
    }

    setTimeout(() => {
        const alturaTotal = document.documentElement.scrollHeight - window.innerHeight;
        const destino = alturaTotal * ((progresso.scrollPercent || 0) / 100);
        window.scrollTo({ top: destino, behavior: 'smooth' });
    }, 280);
}

function abrirCapitulo(manga, capitulo) {
    salvarPosicaoAtual(manga, capituloAtual);
    irPara(`/Manga/${formatarTituloParaUrl(manga.TITULO)}/Capitulo/${capitulo.NUMERO_CAPITULO}`, {
        mangaId: manga.ID,
        titulo: manga.TITULO,
        numeroCapitulo: capitulo.NUMERO_CAPITULO,
    });
}

function renderizarErro(mensagem) {
    const page = document.getElementById('chapter-page');
    page.innerHTML = `
        <section class="error-panel">
            <h1>Erro no leitor</h1>
            <p>${mensagem}</p>
            <button class="primary-action" id="voltar-home">Voltar para início</button>
        </section>
    `;
    document.getElementById('voltar-home').onclick = () => irPara('/Inicio');
}
