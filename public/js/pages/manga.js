import { irPara, formatarTituloParaUrl } from '../router.js';
import { buscarMangaMockPorTitulo } from '../data/mockData.js';
import { alternarFavorito, mangaEstaFavoritado, obterHistorico, obterProgressoDoManga } from '../utils/storage.js';
import { fetchJsonComFallback } from '../utils/api.js';
import { abrirFormularioManga, confirmarExclusaoManga } from '../utils/mangaAdmin.js';
import { formatarData, limitarTexto, obterCaminhoCapa } from '../utils/dom.js';

export async function RenderPageManga(titulo) {
    const manga = await buscarManga(titulo);

    if (!manga) {
        renderizarErroManga();
        return;
    }

    const mangaNormalizado = normalizarManga(manga);
    criarHtmlManga(mangaNormalizado);
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
        AUTOR: manga.AUTOR || { NOME: manga.AUTOR_NOME || 'Autor desconhecido' },
        GENEROS: manga.GENEROS || [],
        CAPITULOS: (manga.CAPITULOS || []).sort((a, b) => Number(a.NUMERO_CAPITULO) - Number(b.NUMERO_CAPITULO)),
    };
}

function criarHtmlManga(manga) {
    preencherTexto('titulo', manga.TITULO);
    preencherTexto('autor', manga.AUTOR?.NOME || 'Autor desconhecido');
    preencherTexto('tipo', manga.TIPO || 'MANGA');
    preencherTexto('status', manga.STATUS || 'Disponível');
    preencherTexto('sinopse', manga.SINOPSE || 'Este mangá ainda não possui sinopse cadastrada.');
    preencherTexto('criado-quando', formatarData(manga.CRIADO_QUANDO || manga.DATA_PUBLICACAO));

    const capa = document.getElementById('capa-do-manga');
    if (capa) {
        capa.src = obterCaminhoCapa(manga.CAPA_URL);
        capa.alt = `Capa de ${manga.TITULO}`;
        capa.loading = 'eager';
        capa.decoding = 'async';
    }

    renderizarGeneros(manga);
    configurarFavorito(manga);
    renderizarCapitulos(manga);
    configurarBotoesLeitura(manga);
    renderizarProgresso(manga);
    configurarComentariosFake();
    configurarAdministracaoManga(manga);
}

function configurarAdministracaoManga(manga) {
    const botaoEditar = document.getElementById('editar-manga');
    const botaoDeletar = document.getElementById('deletar-manga');
    const botaoAdicionar = document.getElementById('adicionar-manga');

    if (botaoEditar) {
        botaoEditar.onclick = () => abrirFormularioManga({
            modo: 'editar',
            manga,
            onSuccess: async (mangaSalvo) => {
                const titulo = mangaSalvo?.TITULO || manga.TITULO;
                irPara(`/Manga/${formatarTituloParaUrl(titulo)}`);
            },
        });
    }

    if (botaoDeletar) {
        botaoDeletar.onclick = () => confirmarExclusaoManga(manga, () => irPara('/Inicio'));
    }

    if (botaoAdicionar) {
        botaoAdicionar.onclick = () => abrirFormularioManga({
            modo: 'criar',
            onSuccess: async (mangaSalvo) => {
                const titulo = mangaSalvo?.TITULO;
                if (titulo) irPara(`/Manga/${formatarTituloParaUrl(titulo)}`);
            },
        });
    }
}

function preencherTexto(id, valor) {
    const elemento = document.getElementById(id);
    if (elemento) elemento.textContent = valor || 'Vazio';
}

function renderizarGeneros(manga) {
    const generos = document.getElementById('generos');
    if (!generos) return;

    generos.innerHTML = '';

    if (!manga.GENEROS.length) {
        generos.textContent = 'Sem gêneros cadastrados';
        return;
    }

    manga.GENEROS.forEach((genero) => {
        const linkGenero = document.createElement('button');
        linkGenero.classList.add('genero-de-generos');
        linkGenero.textContent = genero.NOME;
        linkGenero.addEventListener('click', () => {
            irPara(`/Genero/${formatarTituloParaUrl(genero.NOME)}`);
        });
        generos.appendChild(linkGenero);
    });
}

function configurarFavorito(manga) {
    const checkbox = document.getElementById('favoritado');
    const label = checkbox?.closest('.favorite-toggle');
    if (!checkbox) return;

    atualizarEstadoFavorito(checkbox, label, mangaEstaFavoritado(manga.ID));

    checkbox.onchange = () => {
        const favoritado = alternarFavorito(manga);
        atualizarEstadoFavorito(checkbox, label, favoritado);
    };
}

function atualizarEstadoFavorito(checkbox, label, favoritado) {
    checkbox.checked = favoritado;
    label?.classList.toggle('is-active', favoritado);
    const texto = label?.querySelector('span');
    if (texto) texto.textContent = favoritado ? 'Favoritado' : 'Favoritar';
}

function renderizarCapitulos(manga) {
    const capitulos = document.getElementById('container-capitulos');
    if (!capitulos) return;

    capitulos.innerHTML = '';

    if (!manga.CAPITULOS.length) {
        capitulos.innerHTML = '<p class="empty-state">Nenhum capítulo cadastrado para este mangá.</p>';
        return;
    }

    const progresso = obterProgressoDoManga(manga.ID);
    const historico = obterHistorico();

    manga.CAPITULOS.forEach((capitulo) => {
        const containerCapitulo = document.createElement('button');
        const numeroCapituloContainer = document.createElement('div');
        const textoCapitulo = document.createElement('span');
        const numeroCapitulo = document.createElement('span');
        const dados = document.createElement('div');
        const tituloCapitulo = document.createElement('span');
        const metaCapitulo = document.createElement('small');

        const foiLido = historico.some((item) =>
            String(item.mangaId) === String(manga.ID) &&
            String(item.numeroCapitulo) === String(capitulo.NUMERO_CAPITULO)
        );

        const eAtual = progresso && String(progresso.numeroCapitulo) === String(capitulo.NUMERO_CAPITULO);

        containerCapitulo.classList.add('container-capitulo');
        containerCapitulo.classList.toggle('capitulo-lido', foiLido);
        containerCapitulo.classList.toggle('capitulo-atual', eAtual);
        containerCapitulo.dataset.titulo = `Capítulo ${capitulo.NUMERO_CAPITULO} ${capitulo.TITULO_CAPITULO || ''}`;
        numeroCapituloContainer.classList.add('numero-capitulo-container');
        textoCapitulo.classList.add('texto-capitulo');
        numeroCapitulo.classList.add('numero-capitulo');
        dados.classList.add('capitulo-dados');
        tituloCapitulo.classList.add('titulo-capitulo');
        metaCapitulo.classList.add('meta-capitulo');

        textoCapitulo.textContent = 'Capítulo';
        numeroCapitulo.textContent = capitulo.NUMERO_CAPITULO;
        tituloCapitulo.textContent = capitulo.TITULO_CAPITULO || 'Sem título';
        metaCapitulo.textContent = eAtual
            ? `Continuar em ${Math.round(progresso.scrollPercent || 0)}%`
            : foiLido
                ? 'Lido recentemente'
                : limitarTexto(formatarData(capitulo.DATA_LANCAMENTO || capitulo.createdAt), 40);

        numeroCapituloContainer.appendChild(textoCapitulo);
        numeroCapituloContainer.appendChild(numeroCapitulo);
        dados.appendChild(tituloCapitulo);
        dados.appendChild(metaCapitulo);
        containerCapitulo.appendChild(numeroCapituloContainer);
        containerCapitulo.appendChild(dados);
        capitulos.appendChild(containerCapitulo);

        containerCapitulo.addEventListener('click', () => abrirCapitulo(manga, capitulo));
    });
}

function configurarBotoesLeitura(manga) {
    const botaoPrimeiro = document.getElementById('botao-primeiro-capitulo');
    const botaoContinuar = document.getElementById('botao-continuar');
    const primeiroCapitulo = manga.CAPITULOS[0];
    const progresso = obterProgressoDoManga(manga.ID);

    if (primeiroCapitulo && botaoPrimeiro) {
        botaoPrimeiro.classList.remove('hidden');
        botaoPrimeiro.onclick = () => abrirCapitulo(manga, primeiroCapitulo);
    }

    if (progresso && botaoContinuar) {
        botaoContinuar.classList.remove('hidden');
        botaoContinuar.textContent = `Continuar no capítulo ${progresso.numeroCapitulo}`;
        botaoContinuar.onclick = () => {
            irPara(`/Manga/${formatarTituloParaUrl(manga.TITULO)}/Capitulo/${progresso.numeroCapitulo}`);
        };
    } else if (botaoContinuar) {
        botaoContinuar.classList.add('hidden');
    }
}

function renderizarProgresso(manga) {
    const texto = document.getElementById('manga-progresso-texto');
    const progresso = obterProgressoDoManga(manga.ID);

    if (!texto) return;

    if (!progresso) {
        texto.textContent = 'Você ainda não começou este mangá. Abra um capítulo para salvar o progresso automaticamente.';
        return;
    }

    texto.textContent = `Última leitura: capítulo ${progresso.numeroCapitulo}, aproximadamente ${Math.round(progresso.scrollPercent || 0)}% do capítulo.`;
}

function configurarComentariosFake() {
    const botao = document.getElementById('comentarios');
    if (!botao) return;

    botao.onclick = () => {
        alert('Comentários ficam como melhoria futura. O foco atual é catálogo, favoritos, histórico e progresso de leitura.');
    };
}

function abrirCapitulo(manga, capitulo) {
    irPara(`/Manga/${formatarTituloParaUrl(manga.TITULO)}/Capitulo/${capitulo.NUMERO_CAPITULO}`, {
        mangaId: manga.ID,
        titulo: manga.TITULO,
        numeroCapitulo: capitulo.NUMERO_CAPITULO,
    });
}

function renderizarErroManga() {
    const pagina = document.getElementById('manga-page');
    pagina.innerHTML = `
        <section class="error-panel">
            <h1>Mangá não encontrado</h1>
            <p>Não foi possível carregar os dados deste mangá.</p>
            <button class="primary-action" id="voltar-home">Voltar para início</button>
        </section>
    `;
    document.getElementById('voltar-home').onclick = () => irPara('/Inicio');
}
