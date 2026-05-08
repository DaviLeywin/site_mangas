export function criarImagemOtimizada(src, alt, className = '') {
    const img = document.createElement('img');
    img.src = src;
    img.alt = alt;
    img.loading = 'lazy';
    img.decoding = 'async';

    if (className) {
        img.className = className;
    }

    return img;
}

export function escaparHtml(texto = '') {
    return String(texto)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

export function limitarTexto(texto = '', limite = 80) {
    const valor = String(texto);
    return valor.length > limite ? `${valor.slice(0, limite - 3)}...` : valor;
}

export function obterCaminhoCapa(capaUrl) {
    const capa = String(capaUrl || '').trim();
    if (!capa) return '/SiteLivros/public/assets/imagens/capas_seed/sem-capa.png';
    if (/^https?:\/\//i.test(capa)) return capa;

    const caminhoLimpo = capa
        .replace(/^\/SiteLivros\/public\/assets\//, '')
        .replace(/^\/public\/assets\//, '')
        .replace(/^assets\//, '')
        .replace(/^\/+/, '');

    return `/SiteLivros/public/assets/${caminhoLimpo}`;
}

export function formatarData(valor) {
    if (!valor) return 'Data desconhecida';

    const data = new Date(valor);
    if (Number.isNaN(data.getTime())) return valor;

    return data.toLocaleDateString('pt-BR');
}
