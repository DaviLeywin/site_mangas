const API_BASE = '/SiteLivros/api';
const CACHE_TTL_MS = 1000 * 60 * 5;
const memoryCache = new Map();

export function limparCache(prefix = null) {
    if (!prefix) {
        memoryCache.clear();
        return;
    }

    [...memoryCache.keys()].forEach((key) => {
        if (String(key).startsWith(prefix)) memoryCache.delete(key);
    });
}

export async function fetchJsonComFallback(endpoint, fallback, options = {}) {
    const {
        timeout = 900,
        cacheKey = endpoint,
        cacheTtl = CACHE_TTL_MS,
        forceRefresh = false,
    } = options;

    const cached = memoryCache.get(cacheKey);

    if (!forceRefresh && cached && Date.now() - cached.createdAt < cacheTtl) {
        return cached.data;
    }

    try {
        const data = await fetchJsonComTimeout(`${API_BASE}${endpoint}`, timeout);
        const resposta = normalizarResposta(data);

        memoryCache.set(cacheKey, {
            data: resposta,
            createdAt: Date.now(),
        });

        return resposta;
    } catch (erro) {
        console.warn(`Usando fallback para ${endpoint}:`, erro);
        return typeof fallback === 'function' ? fallback() : fallback;
    }
}

export async function enviarJson(endpoint, metodo = 'POST', payload = null, options = {}) {
    const { timeout = 5000 } = options;
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), timeout);

    try {
        const response = await fetch(`${API_BASE}${endpoint}`, {
            method: metodo,
            signal: controller.signal,
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
            body: payload ? JSON.stringify(payload) : undefined,
        });

        const data = await lerRespostaJson(response, 'A API retornou uma resposta inválida.');

        if (!response.ok) {
            throw criarErroApi(data, `Erro HTTP ${response.status}`);
        }

        return normalizarResposta(data);
    } catch (erro) {
        if (erro.name === 'AbortError') {
            throw new Error('A API demorou demais para responder. Verifique se o Apache/MySQL estão ligados.');
        }

        throw erro;
    } finally {
        clearTimeout(timer);
    }
}

export async function enviarFormulario(endpoint, formData, options = {}) {
    const { timeout = 8000 } = options;
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), timeout);

    try {
        const response = await fetch(`${API_BASE}${endpoint}`, {
            method: 'POST',
            signal: controller.signal,
            headers: {
                Accept: 'application/json',
            },
            body: formData,
        });

        const data = await lerRespostaJson(response, 'A API retornou uma resposta inválida no upload.');

        if (!response.ok) {
            throw criarErroApi(data, `Erro HTTP ${response.status}`);
        }

        return normalizarResposta(data);
    } catch (erro) {
        if (erro.name === 'AbortError') {
            throw new Error('A API demorou demais para responder ao upload. Verifique o tamanho da imagem e o servidor.');
        }

        throw erro;
    } finally {
        clearTimeout(timer);
    }
}

export async function fetchJsonComTimeout(url, timeout = 900) {
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), timeout);

    try {
        const response = await fetch(url, {
            signal: controller.signal,
            headers: {
                Accept: 'application/json',
            },
        });

        const data = await lerRespostaJson(response, `Erro HTTP ${response.status}`);
        if (!response.ok) throw criarErroApi(data, `Erro HTTP ${response.status}`);
        return data;
    } finally {
        clearTimeout(timer);
    }
}

async function lerRespostaJson(response, mensagemPadrao) {
    const texto = await response.text();
    if (!texto) return {};

    try {
        return JSON.parse(texto);
    } catch (erro) {
        const apiErro = new Error(`${mensagemPadrao} O servidor respondeu com texto/HTML em vez de JSON.`);
        apiErro.respostaBruta = texto.slice(0, 400);
        throw apiErro;
    }
}

function normalizarResposta(data) {
    if (data?.sucesso === true) {
        return data.resposta ?? [];
    }

    if (data?.erro || data?.sucesso === false) {
        throw criarErroApi(data, 'A API retornou erro.');
    }

    return data;
}

function criarErroApi(data, fallback) {
    const erro = new Error(formatarMensagemApi(data, fallback));
    erro.detalhes = data?.resposta || data?.detalhes || null;
    erro.resposta = data?.resposta || null;
    erro.tipo = data?.tipo || null;
    erro.erros = normalizarDetalhesEmErros(data?.resposta || data?.detalhes || null);
    return erro;
}

function formatarMensagemApi(data, fallback) {
    if (!data) return fallback;
    if (typeof data.mensagem === 'string' && data.mensagem.trim()) return data.mensagem;
    if (typeof data.message === 'string' && data.message.trim()) return data.message;
    return fallback;
}

function normalizarDetalhesEmErros(detalhes) {
    if (!detalhes) return [];

    if (Array.isArray(detalhes)) {
        return detalhes.map((item) => ({
            campo: item.campo || item.field || item.Field || 'TITULO',
            mensagem: item.mensagem || item.message || item.erro || String(item),
        }));
    }

    if (typeof detalhes === 'object') {
        return Object.entries(detalhes).flatMap(([campo, valor]) => {
            if (Array.isArray(valor)) {
                return valor.map((item) => ({ campo, mensagem: typeof item === 'string' ? item : JSON.stringify(item) }));
            }
            if (typeof valor === 'object' && valor !== null) {
                return [{ campo, mensagem: valor.mensagem || valor.message || JSON.stringify(valor) }];
            }
            return [{ campo, mensagem: String(valor) }];
        });
    }

    return [];
}
