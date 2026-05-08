import { irPara, formatarTituloParaUrl } from '../router.js';
import { buscarMangasMockPorGenero, obterGenerosMock } from '../data/mockData.js';
import { fetchJsonComFallback } from '../utils/api.js';
import { criarImagemOtimizada, obterCaminhoCapa } from '../utils/dom.js';

export async function RenderPageGender(params) {
    const nomeGenero = decodeURIComponent(params).replaceAll('-', ' ');
    const mangas = await buscarMangasByGenero(params);
    criarHtmlGenero(mangas, nomeGenero);
}

async function buscarMangasByGenero(params) {
    return fetchJsonComFallback(
        `/GetGeneroPorNome/${params}`,
        async () => {
            const genero = obterGenerosMock().find((item) => item.NOME.toLowerCase() === decodeURIComponent(params).replaceAll('-', ' ').toLowerCase());
            if (!genero) return buscarMangasMockPorGenero(params);
            return buscarMangasMockPorGenero(params);
        },
        {
            cacheKey: `genero-nome:${params}`,
            timeout: 800,
        }
    ).then(async (respostaGenero) => {
        const genero = Array.isArray(respostaGenero) ? respostaGenero[0] : respostaGenero;

        if (!genero?.ID) {
            return buscarMangasMockPorGenero(params);
        }

        return fetchJsonComFallback(
            `/GetMangaPorId/${genero.ID}`,
            () => buscarMangasMockPorGenero(params),
            {
                cacheKey: `genero-mangas:${genero.ID}`,
                timeout: 900,
            }
        );
    }).catch(() => buscarMangasMockPorGenero(params));
}

function criarHtmlGenero(mangas, nomeGenero) {
    const mangasHtml = document.getElementById('mangas');
    const generoHtml = document.getElementById('genero');

    generoHtml.textContent = nomeGenero;
    mangasHtml.innerHTML = '';

    if (!mangas.length) {
        mangasHtml.innerHTML = '<p class="empty-state">Nenhum mangá encontrado para este gênero.</p>';
        return;
    }

    mangas.forEach((manga) => {
        const mangaHtml = document.createElement('article');
        const capaHtml = document.createElement('div');
        const imagemCapaHtml = criarImagemOtimizada(obterCaminhoCapa(manga.CAPA_URL), `Capa de ${manga.TITULO}`, 'imagem-da-capa');
        const dadosDoMangaHtml = document.createElement('div');
        const nomeDoMangaHtml = document.createElement('span');
        const statusDoMangaHtml = document.createElement('span');

        mangaHtml.classList.add('manga');
        capaHtml.classList.add('capa');
        dadosDoMangaHtml.classList.add('dados-do-manga');
        nomeDoMangaHtml.classList.add('nome-do-manga');
        statusDoMangaHtml.classList.add('status-do-manga');

        mangaHtml.dataset.titulo = manga.TITULO;
        nomeDoMangaHtml.textContent = manga.TITULO;
        statusDoMangaHtml.textContent = manga.STATUS || 'Disponível';

        capaHtml.appendChild(imagemCapaHtml);
        dadosDoMangaHtml.appendChild(nomeDoMangaHtml);
        dadosDoMangaHtml.appendChild(statusDoMangaHtml);
        mangaHtml.appendChild(capaHtml);
        mangaHtml.appendChild(dadosDoMangaHtml);
        mangasHtml.appendChild(mangaHtml);

        mangaHtml.addEventListener('click', () => {
            irPara(`/Manga/${formatarTituloParaUrl(manga.TITULO)}`);
        });
    });
}
