import { irPara, formatarTituloParaUrl } from '../router.js';
import { obterGenerosMock, obterMangasComGenerosMock } from '../data/mockData.js';
import { obterFavoritos, obterHistorico, obterProgresso } from '../utils/storage.js';
import { fetchJsonComFallback } from '../utils/api.js';
import { criarImagemOtimizada, limitarTexto, obterCaminhoCapa } from '../utils/dom.js';
import { abrirFormularioManga, confirmarExclusaoManga } from '../utils/mangaAdmin.js';

export async function RenderPageHome(params = null) {
    const [generos, mangas] = await Promise.all([
        buscarGeneros(),
        buscarMangas(),
    ]);

    renderizarHero(mangas);
    renderizarResumo(generos, mangas);
    renderizarContinuarLendo(mangas);
    renderizarFavoritos();
    renderizarHistorico();
    renderizarGeneros(generos, mangas);
    renderizarRanking(mangas);
    configurarAdicionarManga();

    if (params?.scrollTo) {
        requestAnimationFrame(() => {
            document.getElementById(params.scrollTo)?.scrollIntoView({ behavior: 'smooth' });
        });
    }
}

async function buscarGeneros() {
    return fetchJsonComFallback('/GetAllGeneros', obterGenerosMock, {
        cacheKey: 'generos',
        timeout: 800,
    });
}

async function buscarMangas() {
    return fetchJsonComFallback('/GetMangasGeneros', obterMangasComGenerosMock, {
        cacheKey: 'mangas-generos',
        timeout: 1000,
    });
}

function renderizarHero(mangas) {
    const hero = document.getElementById('hero-home');
    const destaque = [...mangas].sort((a, b) => Number(b.VIEWS || b.ID || 0) - Number(a.VIEWS || a.ID || 0))[0] || mangas[0];

    if (!hero || !destaque) return;

    const capa = obterCaminhoCapa(destaque.CAPA_URL);
    hero.style.backgroundImage = `linear-gradient(90deg, rgba(5, 6, 13, .95), rgba(5, 6, 13, .45), rgba(5, 6, 13, .88)), url('${capa}')`;

    const titulo = hero.querySelector('h1');
    const texto = hero.querySelector('p');

    titulo.textContent = destaque.TITULO;
    texto.textContent = limitarTexto(destaque.SINOPSE || 'Continue explorando títulos, gêneros e capítulos em uma interface mais organizada e moderna.', 160);
}

function renderizarResumo(generos, mangas) {
    const favoritos = obterFavoritos();
    const progresso = obterProgresso();

    preencherNumero('total-mangas', mangas.length);
    preencherNumero('total-generos', generos.length);
    preencherNumero('total-favoritos', favoritos.length);
    preencherNumero('total-progresso', progresso.length);
}

function preencherNumero(id, valor) {
    const elemento = document.getElementById(id);
    if (elemento) elemento.textContent = valor;
}

function configurarAdicionarManga() {
    const botao = document.getElementById('abrir-form-novo-manga');
    if (!botao) return;

    botao.onclick = () => abrirFormularioManga({
        modo: 'criar',
        onSuccess: async (mangaSalvo) => {
            const titulo = mangaSalvo?.TITULO;
            if (titulo) {
                irPara(`/Manga/${formatarTituloParaUrl(titulo)}`);
            } else {
                RenderPageHome();
            }
        },
    });
}

function renderizarContinuarLendo(mangas) {
    const section = document.getElementById('continuar-lendo');
    const lista = document.getElementById('continuar-lista');
    const progresso = obterProgresso();

    if (!section || !lista) return;

    lista.innerHTML = '';

    if (!progresso.length) {
        section.classList.add('hidden');
        return;
    }

    section.classList.remove('hidden');

    progresso.slice(0, 6).forEach((item) => {
        const manga = mangas.find((m) => String(m.ID) === String(item.mangaId)) || item.manga;
        if (!manga) return;

        const card = criarCardEspecial({
            titulo: manga.TITULO,
            imagem: manga.CAPA_URL,
            legenda: `Capítulo ${item.numeroCapitulo} • ${Math.round(item.scrollPercent || 0)}% lido`,
        });

        card.addEventListener('click', () => {
            irPara(`/Manga/${formatarTituloParaUrl(manga.TITULO)}/Capitulo/${item.numeroCapitulo}`);
        });

        lista.appendChild(card);
    });
}

function renderizarFavoritos() {
    const section = document.getElementById('favoritos-home');
    const lista = document.getElementById('favoritos-lista');
    const favoritos = obterFavoritos();

    if (!section || !lista) return;

    lista.innerHTML = '';

    if (!favoritos.length) {
        section.classList.add('hidden');
        return;
    }

    section.classList.remove('hidden');

    favoritos.slice(0, 8).forEach((manga) => {
        const card = criarCardEspecial({
            titulo: manga.TITULO,
            imagem: manga.CAPA_URL,
            legenda: manga.STATUS || 'Favoritado',
        });

        card.addEventListener('click', () => {
            irPara(`/Manga/${formatarTituloParaUrl(manga.TITULO)}`);
        });

        lista.appendChild(card);
    });
}

function renderizarHistorico() {
    const section = document.getElementById('historico-home');
    const lista = document.getElementById('historico-lista');
    const historico = obterHistorico();

    if (!section || !lista) return;

    lista.innerHTML = '';

    if (!historico.length) {
        section.classList.add('hidden');
        return;
    }

    section.classList.remove('hidden');

    historico.slice(0, 8).forEach((item) => {
        const card = criarCardEspecial({
            titulo: item.manga?.TITULO || 'Mangá',
            imagem: item.manga?.CAPA_URL,
            legenda: `Cap. ${item.numeroCapitulo} • ${formatarTempoRelativo(item.lidoEm)}`,
        });

        card.addEventListener('click', () => {
            irPara(`/Manga/${formatarTituloParaUrl(item.manga?.TITULO || '')}/Capitulo/${item.numeroCapitulo}`);
        });

        lista.appendChild(card);
    });
}

function renderizarGeneros(generos, mangas) {
    const container = document.getElementById('mangas');
    if (!container) return;

    container.innerHTML = '';

    generos.forEach((genero) => {
        const mangasDoGenero = mangas.filter((manga) =>
            manga.GENEROS?.some((generoDoManga) => String(generoDoManga.ID) === String(genero.ID))
        );

        if (!mangasDoGenero.length) return;

        const section = document.createElement('section');
        const heading = document.createElement('div');
        const grupoTitulo = document.createElement('div');
        const kicker = document.createElement('span');
        const titulo = document.createElement('h2');
        const botaoGenero = document.createElement('button');
        const wrapper = document.createElement('div');
        const btnEsq = document.createElement('button');
        const btnDir = document.createElement('button');
        const carousel = document.createElement('div');

        section.classList.add('genero');
        heading.classList.add('section-heading');
        kicker.classList.add('section-kicker');
        botaoGenero.classList.add('ghost-link');
        wrapper.classList.add('carousel-wrapper');
        carousel.classList.add('carousel');
        btnEsq.classList.add('seta', 'esquerda');
        btnDir.classList.add('seta', 'direita');

        kicker.textContent = `${mangasDoGenero.length} título${mangasDoGenero.length > 1 ? 's' : ''}`;
        titulo.textContent = genero.NOME;
        botaoGenero.textContent = 'Ver gênero';
        btnEsq.textContent = '‹';
        btnDir.textContent = '›';
        btnEsq.setAttribute('aria-label', `Voltar na linha ${genero.NOME}`);
        btnDir.setAttribute('aria-label', `Avançar na linha ${genero.NOME}`);

        botaoGenero.addEventListener('click', () => {
            irPara(`/Genero/${formatarTituloParaUrl(genero.NOME)}`);
        });

        mangasDoGenero.forEach((manga, index) => carousel.appendChild(criarCardManga(manga, index)));

        btnDir.addEventListener('click', () => carousel.scrollBy({ left: carousel.clientWidth * 0.9, behavior: 'smooth' }));
        btnEsq.addEventListener('click', () => carousel.scrollBy({ left: -carousel.clientWidth * 0.9, behavior: 'smooth' }));

        grupoTitulo.appendChild(kicker);
        grupoTitulo.appendChild(titulo);
        heading.appendChild(grupoTitulo);
        heading.appendChild(botaoGenero);
        wrapper.appendChild(btnEsq);
        wrapper.appendChild(carousel);
        wrapper.appendChild(btnDir);
        section.appendChild(heading);
        section.appendChild(wrapper);
        container.appendChild(section);
    });
}

function renderizarRanking(mangas) {
    const lista = document.getElementById('ranking-lista');
    if (!lista) return;

    lista.innerHTML = '';

    const ordenados = [...mangas].sort((a, b) => Number(b.VIEWS || b.ID || 0) - Number(a.VIEWS || a.ID || 0));

    ordenados.slice(0, 8).forEach((manga, index) => {
        const item = document.createElement('button');
        item.classList.add('ranking-item');
        item.dataset.titulo = manga.TITULO;
        item.innerHTML = `
            <span>${String(index + 1).padStart(2, '0')}</span>
            <strong>${manga.TITULO}</strong>
        `;
        item.addEventListener('click', () => irPara(`/Manga/${formatarTituloParaUrl(manga.TITULO)}`));
        lista.appendChild(item);
    });
}

function criarCardManga(manga, index = 0) {
    const card = document.createElement('article');
    const capa = criarImagemOtimizada(obterCaminhoCapa(manga.CAPA_URL), `Capa de ${manga.TITULO}`, 'capa-manga');
    const info = document.createElement('div');
    const titulo = document.createElement('h3');
    const meta = document.createElement('span');
    const acoes = document.createElement('div');
    const editar = document.createElement('button');
    const excluir = document.createElement('button');

    card.classList.add('manga');
    card.dataset.idManga = manga.ID;
    card.dataset.titulo = manga.TITULO;
    info.classList.add('manga-card-info');
    titulo.classList.add('titulo-manga');
    meta.classList.add('manga-meta');
    acoes.classList.add('manga-card-actions');
    editar.classList.add('mini-action');
    excluir.classList.add('mini-action', 'danger-action');

    if (index < 4) {
        capa.loading = 'eager';
        capa.fetchPriority = 'high';
    }

    titulo.textContent = limitarTexto(manga.TITULO, 38);
    meta.textContent = manga.STATUS || 'Disponível';
    editar.textContent = 'Editar';
    excluir.textContent = 'Excluir';
    editar.type = 'button';
    excluir.type = 'button';

    editar.addEventListener('click', (evento) => {
        evento.stopPropagation();
        abrirFormularioManga({
            modo: 'editar',
            manga,
            onSuccess: () => RenderPageHome(),
        });
    });

    excluir.addEventListener('click', (evento) => {
        evento.stopPropagation();
        confirmarExclusaoManga(manga, () => RenderPageHome());
    });

    acoes.appendChild(editar);
    acoes.appendChild(excluir);
    info.appendChild(titulo);
    info.appendChild(meta);
    info.appendChild(acoes);
    card.appendChild(capa);
    card.appendChild(info);

    card.addEventListener('click', () => {
        irPara(`/Manga/${formatarTituloParaUrl(manga.TITULO)}`);
    });

    return card;
}

function criarCardEspecial({ titulo, imagem, legenda }) {
    const card = document.createElement('button');
    const img = criarImagemOtimizada(obterCaminhoCapa(imagem), `Capa de ${titulo}`);
    const div = document.createElement('div');
    const strong = document.createElement('strong');
    const span = document.createElement('span');

    card.classList.add('special-card');
    card.dataset.titulo = titulo;
    strong.textContent = titulo;
    span.textContent = legenda;

    div.appendChild(strong);
    div.appendChild(span);
    card.appendChild(img);
    card.appendChild(div);

    return card;
}

function formatarTempoRelativo(valor) {
    const data = new Date(valor);
    if (Number.isNaN(data.getTime())) return 'lido recentemente';

    const minutos = Math.max(1, Math.round((Date.now() - data.getTime()) / 60000));
    if (minutos < 60) return `há ${minutos} min`;

    const horas = Math.round(minutos / 60);
    if (horas < 24) return `há ${horas} h`;

    const dias = Math.round(horas / 24);
    return `há ${dias} dia${dias > 1 ? 's' : ''}`;
}
