export const STORAGE_KEYS = {
    favoritos: 'mangaverse:favoritos',
    progresso: 'mangaverse:progresso',
    historico: 'mangaverse:historico',
    preferencias: 'mangaverse:preferencias',
};

export function lerStorage(chave, valorPadrao = []) {
    try {
        const valor = localStorage.getItem(chave);
        return valor ? JSON.parse(valor) : valorPadrao;
    } catch (erro) {
        console.warn('Erro ao ler localStorage:', erro);
        return valorPadrao;
    }
}

export function salvarStorage(chave, valor) {
    try {
        localStorage.setItem(chave, JSON.stringify(valor));
    } catch (erro) {
        console.warn('Erro ao salvar localStorage:', erro);
    }
}

export function obterFavoritos() {
    return lerStorage(STORAGE_KEYS.favoritos, []);
}

export function mangaEstaFavoritado(mangaId) {
    return obterFavoritos().some((favorito) => String(favorito.ID) === String(mangaId));
}

export function alternarFavorito(manga) {
    const favoritos = obterFavoritos();
    const existe = favoritos.some((favorito) => String(favorito.ID) === String(manga.ID));

    if (existe) {
        const atualizados = favoritos.filter((favorito) => String(favorito.ID) !== String(manga.ID));
        salvarStorage(STORAGE_KEYS.favoritos, atualizados);
        return false;
    }

    favoritos.unshift(criarResumoManga(manga, { favoritadoEm: new Date().toISOString() }));
    salvarStorage(STORAGE_KEYS.favoritos, limitarListaUnica(favoritos, 'ID', 80));
    return true;
}

export function obterProgresso() {
    return lerStorage(STORAGE_KEYS.progresso, []);
}

export function salvarProgresso(item) {
    const progresso = obterProgresso();
    const semAtual = progresso.filter((registro) => String(registro.mangaId) !== String(item.mangaId));

    semAtual.unshift({
        ...item,
        atualizadoEm: new Date().toISOString(),
    });

    salvarStorage(STORAGE_KEYS.progresso, semAtual.slice(0, 30));
}

export function obterProgressoDoManga(mangaId) {
    return obterProgresso().find((registro) => String(registro.mangaId) === String(mangaId)) || null;
}

export function obterHistorico() {
    return lerStorage(STORAGE_KEYS.historico, []);
}

export function salvarHistorico({ manga, capitulo, scrollPercent = 0, pageIndex = 0 }) {
    if (!manga || !capitulo) return;

    const historico = obterHistorico();
    const idRegistro = `${manga.ID}:${capitulo.NUMERO_CAPITULO}`;
    const semAtual = historico.filter((registro) => registro.idRegistro !== idRegistro);

    semAtual.unshift({
        idRegistro,
        manga: criarResumoManga(manga),
        mangaId: manga.ID,
        chapterId: capitulo.ID,
        numeroCapitulo: capitulo.NUMERO_CAPITULO,
        tituloCapitulo: capitulo.TITULO_CAPITULO || `Capítulo ${capitulo.NUMERO_CAPITULO}`,
        scrollPercent,
        pageIndex,
        lidoEm: new Date().toISOString(),
    });

    salvarStorage(STORAGE_KEYS.historico, semAtual.slice(0, 50));
}

export function limparDadosLocais() {
    salvarStorage(STORAGE_KEYS.favoritos, []);
    salvarStorage(STORAGE_KEYS.progresso, []);
    salvarStorage(STORAGE_KEYS.historico, []);
}

function criarResumoManga(manga, extras = {}) {
    return {
        ID: manga.ID,
        TITULO: manga.TITULO,
        CAPA_URL: manga.CAPA_URL,
        STATUS: manga.STATUS,
        GENEROS: manga.GENEROS || [],
        ...extras,
    };
}

function limitarListaUnica(lista, campo, limite) {
    const vistos = new Set();
    const resultado = [];

    for (const item of lista) {
        const chave = String(item[campo]);
        if (vistos.has(chave)) continue;
        vistos.add(chave);
        resultado.push(item);
    }

    return resultado.slice(0, limite);
}
