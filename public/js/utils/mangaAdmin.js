import { enviarJson, enviarFormulario, fetchJsonComFallback, limparCache } from './api.js';

const BASE_PATH = '/SiteLivros';
let modal = null;
let autoresCache = [];

const STATUS_OPTIONS = [
    'Em andamento',
    'Concluído',
    'Hiato',
    'Cancelado',
    'Não iniciado',
];

const TIPOS_OPTIONS = ['MANGA', 'NOVEL'];
const FORMATOS_CAPA_PERMITIDOS = ['image/png', 'image/jpeg', 'image/webp', 'image/gif'];
const LIMITE_CAPA_MB = 5;

const ROTULOS_CAMPOS = {
    TITULO: 'Título',
    AUTORES_ID: 'Autor existente',
    NOVO_AUTOR_NOME: 'Nome do autor',
    NOVO_AUTOR_NACIONALIDADE: 'Nacionalidade do autor',
    NOVO_AUTOR_DATA_NASCIMENTO: 'Data de nascimento do autor',
    NOVO_AUTOR_BIOGRAFIA: 'Biografia do autor',
    DATA_PUBLICACAO: 'Data de publicação',
    TIPO: 'Tipo',
    STATUS: 'Status',
    CAPA_ARQUIVO: 'Imagem de capa',
    CAPA_URL: 'Caminho da capa',
    SINOPSE: 'Sinopse',
};

export async function abrirFormularioManga({ modo = 'criar', manga = null, onSuccess = null } = {}) {
    modal = garantirModal();
    const form = modal.querySelector('#form-manga-admin');
    const tituloModal = modal.querySelector('#manga-admin-titulo-modal');
    const mensagem = modal.querySelector('#manga-admin-mensagem');
    const botaoSalvar = modal.querySelector('#manga-admin-salvar');
    const autorSelect = modal.querySelector('#manga-admin-autor');
    const modoAutor = modal.querySelector('#manga-admin-modo-autor');
    const capaInput = modal.querySelector('#manga-admin-capa-arquivo');
    const capaPreview = modal.querySelector('#manga-admin-capa-preview');
    const capaNome = modal.querySelector('#manga-admin-capa-nome');

    tituloModal.textContent = modo === 'editar' ? 'Editar mangá' : 'Adicionar mangá';
    botaoSalvar.textContent = modo === 'editar' ? 'Salvar alterações' : 'Cadastrar mangá';
    limparMensagem(mensagem);
    limparErrosDoFormulario(form);

    await carregarAutores(autorSelect);
    preencherFormulario(form, manga, modo);
    atualizarCamposAutor(form);
    atualizarPreviewCapa(capaPreview, capaNome, obterValor(form, 'CAPA_URL'));
    abrirModal();

    modoAutor.onchange = () => {
        atualizarCamposAutor(form);
        limparErrosDoFormulario(form);
        limparMensagem(mensagem);
    };

    form.oninput = (evento) => {
        limparErroCampo(form, evento.target?.name);
        limparMensagem(mensagem, true);
    };

    form.onchange = (evento) => {
        limparErroCampo(form, evento.target?.name);
        limparMensagem(mensagem, true);
    };

    capaInput.onchange = () => {
        limparErroCampo(form, 'CAPA_ARQUIVO');
        limparMensagem(mensagem, true);

        const arquivo = capaInput.files?.[0];
        if (!arquivo) {
            atualizarPreviewCapa(capaPreview, capaNome, obterValor(form, 'CAPA_URL'));
            return;
        }

        const validacaoArquivo = validarArquivoCapa(arquivo);
        if (validacaoArquivo) {
            mostrarErrosFormulario(form, [{ campo: 'CAPA_ARQUIVO', mensagem: validacaoArquivo }]);
            mostrarMensagem(mensagem, 'A capa escolhida não pode ser usada. Veja o aviso no campo de imagem.', 'erro');
            capaInput.value = '';
            atualizarPreviewCapa(capaPreview, capaNome, obterValor(form, 'CAPA_URL'));
            return;
        }

        const urlLocal = URL.createObjectURL(arquivo);
        atualizarPreviewCapa(capaPreview, capaNome, urlLocal, arquivo.name);
        capaPreview.onload = () => URL.revokeObjectURL(urlLocal);
    };

    form.onsubmit = async (evento) => {
        evento.preventDefault();
        limparErrosDoFormulario(form);
        limparMensagem(mensagem);

        const validacao = validarFormularioManga(form, modo);
        if (!validacao.valido) {
            mostrarErrosFormulario(form, validacao.erros);
            mostrarMensagem(mensagem, montarResumoErros(validacao.erros), 'erro', true);
            focarPrimeiroErro(form, validacao.erros);
            return;
        }

        botaoSalvar.disabled = true;
        botaoSalvar.textContent = modo === 'editar' ? 'Salvando...' : 'Cadastrando...';

        try {
            mostrarMensagem(mensagem, 'Validando título e dados antes de salvar...', 'info');
            const payload = montarPayload(form);

            await validarTituloDisponivel(payload.TITULO, manga?.ID);

            const arquivoCapa = elemento(form, 'CAPA_ARQUIVO')?.files?.[0];
            if (arquivoCapa) {
                mostrarMensagem(mensagem, 'Enviando a capa para a pasta public/assets/imagens...', 'info');
                const upload = await enviarCapa(arquivoCapa);
                payload.CAPA_URL = upload.CAPA_URL || upload.caminho || upload.url || payload.CAPA_URL;
            }

            const usandoNovoAutor = obterValor(form, '__USAR_NOVO_AUTOR') === 'sim';
            let resposta;

            if (usandoNovoAutor) {
                payload.AUTOR = montarPayloadAutor(form);
                delete payload.AUTORES_ID;

                mostrarMensagem(mensagem, modo === 'editar'
                    ? 'Cadastrando autor e atualizando mangá...'
                    : 'Cadastrando autor e mangá juntos...', 'info');

                resposta = modo === 'editar'
                    ? await enviarJson(`/UpdateMangaComAutor/${manga.ID}`, 'PUT', payload, { timeout: 7000 })
                    : await enviarJson('/InsertMangaComAutor', 'POST', payload, { timeout: 7000 });
            } else {
                mostrarMensagem(mensagem, modo === 'editar' ? 'Atualizando mangá...' : 'Cadastrando mangá...', 'info');
                resposta = modo === 'editar'
                    ? await enviarJson(`/UpdateMangas/${manga.ID}`, 'PUT', payload, { timeout: 7000 })
                    : await enviarJson('/InsertMangas', 'POST', payload, { timeout: 7000 });
            }

            autoresCache = [];
            limparCache();
            mostrarMensagem(mensagem, modo === 'editar' ? 'Mangá atualizado com sucesso.' : 'Mangá cadastrado com sucesso.', 'sucesso');
            fecharModal();

            const mangaSalvo = Array.isArray(resposta) ? resposta[0] : resposta;
            if (typeof onSuccess === 'function') {
                await onSuccess(mangaSalvo || payload);
            } else {
                navegarPara(`/Manga/${formatarTituloParaUrl((mangaSalvo || payload).TITULO)}`);
            }
        } catch (erro) {
            const detalhes = normalizarErrosApi(erro);
            if (detalhes.length) {
                mostrarErrosFormulario(form, detalhes);
                mostrarMensagem(mensagem, montarResumoErros(detalhes), 'erro', true);
                focarPrimeiroErro(form, detalhes);
            } else {
                mostrarMensagem(mensagem, erro.message || 'Não foi possível salvar o mangá.', 'erro');
            }
        } finally {
            botaoSalvar.disabled = false;
            botaoSalvar.textContent = modo === 'editar' ? 'Salvar alterações' : 'Cadastrar mangá';
        }
    };
}

export async function confirmarExclusaoManga(manga, onSuccess = null) {
    if (!manga?.ID) {
        alert('Não foi possível identificar o mangá para excluir. Recarregue a página e tente de novo.');
        return;
    }

    const confirmou = confirm(`Tem certeza que deseja excluir "${manga.TITULO}"? Essa ação remove o mangá e os vínculos dele no banco.`);
    if (!confirmou) return;

    try {
        await enviarJson(`/DeleteMangas/${manga.ID}`, 'DELETE', null, { timeout: 5000 });
        limparCache();
        alert('Mangá excluído com sucesso.');

        if (typeof onSuccess === 'function') {
            await onSuccess();
        } else {
            navegarPara('/Inicio');
        }
    } catch (erro) {
        alert(erro.message || 'Não foi possível excluir o mangá. Verifique se ele ainda existe no banco.');
    }
}

function garantirModal() {
    const existente = document.getElementById('manga-admin-modal');
    if (existente) return existente;

    const wrapper = document.createElement('div');
    wrapper.id = 'manga-admin-modal';
    wrapper.className = 'manga-admin-modal hidden';
    wrapper.innerHTML = `
        <div class="manga-admin-backdrop" data-close-manga-modal></div>
        <section class="manga-admin-panel" role="dialog" aria-modal="true" aria-labelledby="manga-admin-titulo-modal">
            <div class="manga-admin-header">
                <div>
                    <span class="section-kicker">CRUD</span>
                    <h2 id="manga-admin-titulo-modal">Adicionar mangá</h2>
                </div>
                <button type="button" class="modal-close" data-close-manga-modal aria-label="Fechar formulário">×</button>
            </div>

            <form id="form-manga-admin" class="manga-admin-form" novalidate>
                <input type="hidden" name="CAPA_URL">
                <input type="hidden" name="__USAR_NOVO_AUTOR" value="nao">

                <div class="form-help form-full">
                    <strong>O que pode cadastrar:</strong>
                    <span>título único, autor existente ou novo autor, tipo MANGA/NOVEL, status nas opções e capa PNG/JPG/WEBP/GIF de até ${LIMITE_CAPA_MB}MB.</span>
                    <strong>O que não pode:</strong>
                    <span>título repetido, campos obrigatórios vazios, autor inválido, data fora do formato ou imagem muito pesada.</span>
                </div>

                <div class="form-grid">
                    <label data-field="TITULO">
                        <span>Título *</span>
                        <input type="text" name="TITULO" maxlength="200" placeholder="Ex: One Piece" required>
                    </label>

                    <label data-field="TIPO">
                        <span>Tipo *</span>
                        <select name="TIPO" required>
                            <option value="MANGA">MANGA</option>
                            <option value="NOVEL">NOVEL</option>
                        </select>
                    </label>

                    <label data-field="DATA_PUBLICACAO">
                        <span>Data de publicação *</span>
                        <input type="date" name="DATA_PUBLICACAO" required>
                    </label>

                    <label data-field="STATUS">
                        <span>Status *</span>
                        <select name="STATUS" id="manga-admin-status" required>
                            ${STATUS_OPTIONS.map((status) => `<option value="${status}">${status}</option>`).join('')}
                        </select>
                    </label>
                </div>

                <section class="form-card form-full">
                    <div class="form-card-header">
                        <div>
                            <span>Autor *</span>
                            <strong>Escolha um autor existente ou cadastre um novo agora</strong>
                        </div>
                        <select id="manga-admin-modo-autor" class="compact-select">
                            <option value="existente">Usar autor existente</option>
                            <option value="novo">Cadastrar novo autor</option>
                        </select>
                    </div>

                    <div class="form-grid autor-existente-area">
                        <label data-field="AUTORES_ID">
                            <span>Autor existente</span>
                            <select name="AUTORES_ID" id="manga-admin-autor" required>
                                <option value="">Carregando autores...</option>
                            </select>
                        </label>
                    </div>

                    <div class="form-grid novo-autor-area hidden">
                        <label data-field="NOVO_AUTOR_NOME">
                            <span>Nome do autor *</span>
                            <input type="text" name="NOVO_AUTOR_NOME" maxlength="200" placeholder="Ex: Eiichiro Oda">
                        </label>

                        <label data-field="NOVO_AUTOR_NACIONALIDADE">
                            <span>Nacionalidade</span>
                            <input type="text" name="NOVO_AUTOR_NACIONALIDADE" maxlength="100" placeholder="Ex: Japonês">
                        </label>

                        <label data-field="NOVO_AUTOR_DATA_NASCIMENTO">
                            <span>Data de nascimento</span>
                            <input type="date" name="NOVO_AUTOR_DATA_NASCIMENTO">
                        </label>

                        <label class="form-full" data-field="NOVO_AUTOR_BIOGRAFIA">
                            <span>Biografia</span>
                            <textarea name="NOVO_AUTOR_BIOGRAFIA" rows="3" placeholder="Pequena descrição do autor"></textarea>
                        </label>
                    </div>
                </section>

                <section class="form-card form-full">
                    <div class="form-card-header">
                        <div>
                            <span>Capa *</span>
                            <strong>Escolha uma imagem do computador</strong>
                        </div>
                    </div>

                    <div class="cover-upload-row">
                        <div class="cover-preview-box">
                            <img id="manga-admin-capa-preview" src="" alt="Prévia da capa">
                        </div>

                        <div class="cover-upload-controls" data-field="CAPA_ARQUIVO">
                            <label class="file-picker">
                                <span>Selecionar imagem</span>
                                <input type="file" name="CAPA_ARQUIVO" id="manga-admin-capa-arquivo" accept="image/png, image/jpeg, image/webp, image/gif">
                            </label>
                            <small id="manga-admin-capa-nome">Nenhuma imagem selecionada.</small>
                            <p>A imagem será enviada para <code>public/assets/imagens</code> e o sistema salvará o caminho correto no banco.</p>
                        </div>
                    </div>
                </section>

                <label class="form-full" data-field="SINOPSE">
                    <span>Sinopse</span>
                    <textarea name="SINOPSE" rows="5" maxlength="2000" placeholder="Resumo do mangá"></textarea>
                </label>

                <p id="manga-admin-mensagem" class="form-message" aria-live="polite"></p>

                <div class="form-actions">
                    <button type="button" class="secondary-action" data-close-manga-modal>Cancelar</button>
                    <button type="submit" class="primary-action" id="manga-admin-salvar">Cadastrar mangá</button>
                </div>
            </form>
        </section>
    `;

    wrapper.addEventListener('click', (evento) => {
        if (evento.target.matches('[data-close-manga-modal]')) fecharModal();
    });

    document.addEventListener('keydown', (evento) => {
        if (evento.key === 'Escape' && !wrapper.classList.contains('hidden')) fecharModal();
    });

    document.body.appendChild(wrapper);
    return wrapper;
}

async function carregarAutores(select) {
    if (!select) return;

    try {
        autoresCache = await fetchJsonComFallback('/GetAllAutores', [], {
            cacheKey: 'autores',
            timeout: 1500,
            forceRefresh: !autoresCache.length,
        });
    } catch (erro) {
        autoresCache = [];
    }

    select.innerHTML = '';

    if (!Array.isArray(autoresCache) || !autoresCache.length) {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'Nenhum autor cadastrado';
        select.appendChild(option);
        return;
    }

    const placeholder = document.createElement('option');
    placeholder.value = '';
    placeholder.textContent = 'Selecione um autor';
    select.appendChild(placeholder);

    autoresCache.forEach((autor) => {
        const option = document.createElement('option');
        option.value = autor.ID;
        option.textContent = `${autor.NOME} #${autor.ID}`;
        select.appendChild(option);
    });
}

function preencherFormulario(form, manga, modo) {
    const hoje = new Date().toISOString().slice(0, 10);
    form.reset();
    definirValor(form, 'TITULO', manga?.TITULO || '');
    definirValor(form, 'AUTORES_ID', manga?.AUTORES_ID || manga?.AUTOR?.ID || '');
    definirValor(form, 'DATA_PUBLICACAO', normalizarDataInput(manga?.DATA_PUBLICACAO || manga?.CRIADO_QUANDO) || hoje);
    definirValor(form, 'TIPO', TIPOS_OPTIONS.includes(manga?.TIPO) ? manga.TIPO : 'MANGA');
    definirValor(form, 'STATUS', STATUS_OPTIONS.includes(manga?.STATUS) ? manga.STATUS : 'Em andamento');
    definirValor(form, 'CAPA_URL', manga?.CAPA_URL || '');
    definirValor(form, 'SINOPSE', manga?.SINOPSE || '');
    definirValor(form, '__USAR_NOVO_AUTOR', 'nao');
    form.querySelector('#manga-admin-modo-autor').value = 'existente';

    if (modo === 'criar' && !obterValor(form, 'AUTORES_ID') && autoresCache.length === 0) {
        definirValor(form, '__USAR_NOVO_AUTOR', 'sim');
        form.querySelector('#manga-admin-modo-autor').value = 'novo';
    }
}

function montarPayload(form) {
    return {
        TITULO: obterValor(form, 'TITULO'),
        AUTORES_ID: Number(obterValor(form, 'AUTORES_ID')),
        DATA_PUBLICACAO: obterValor(form, 'DATA_PUBLICACAO'),
        SINOPSE: obterValor(form, 'SINOPSE'),
        TIPO: obterValor(form, 'TIPO') || 'MANGA',
        STATUS: obterValor(form, 'STATUS') || 'Em andamento',
        CAPA_URL: obterValor(form, 'CAPA_URL'),
    };
}

function montarPayloadAutor(form) {
    const payload = {
        NOME: obterValor(form, 'NOVO_AUTOR_NOME'),
    };

    const biografia = obterValor(form, 'NOVO_AUTOR_BIOGRAFIA');
    const dataNascimento = obterValor(form, 'NOVO_AUTOR_DATA_NASCIMENTO');
    const nacionalidade = obterValor(form, 'NOVO_AUTOR_NACIONALIDADE');

    if (biografia) payload.BIOGRAFIA = biografia;
    if (dataNascimento) payload.DATA_NASCIMENTO = dataNascimento;
    if (nacionalidade) payload.NACIONALIDADE = nacionalidade;

    return payload;
}

async function enviarCapa(arquivo) {
    const formData = new FormData();
    formData.append('capa', arquivo);
    return enviarFormulario('/UploadImagemManga', formData, { timeout: 10000 });
}

async function validarTituloDisponivel(titulo, idAtual = null) {
    const mangas = await fetchJsonComFallback('/GetMangasGeneros', [], {
        cacheKey: `mangas-validacao-${Date.now()}`,
        timeout: 1800,
        forceRefresh: true,
    });

    if (!Array.isArray(mangas)) return;

    const tituloNormalizado = normalizarComparacao(titulo);
    const duplicado = mangas.find((manga) =>
        normalizarComparacao(manga.TITULO) === tituloNormalizado &&
        String(manga.ID) !== String(idAtual || '')
    );

    if (duplicado) {
        const erro = new Error(`Já existe um mangá cadastrado com o título "${titulo}". Use outro título ou edite o cadastro existente.`);
        erro.erros = [{ campo: 'TITULO', mensagem: erro.message }];
        throw erro;
    }
}

function validarFormularioManga(form, modo) {
    const payload = montarPayload(form);
    const usandoNovoAutor = obterValor(form, '__USAR_NOVO_AUTOR') === 'sim';
    const arquivo = elemento(form, 'CAPA_ARQUIVO')?.files?.[0];
    const erros = [];

    if (payload.TITULO.length < 2) {
        erros.push({ campo: 'TITULO', mensagem: 'O título precisa ter pelo menos 2 caracteres.' });
    }

    if (payload.TITULO.length > 200) {
        erros.push({ campo: 'TITULO', mensagem: 'O título não pode passar de 200 caracteres.' });
    }

    if (usandoNovoAutor) {
        const autor = montarPayloadAutor(form);
        if (autor.NOME.length < 2) {
            erros.push({ campo: 'NOVO_AUTOR_NOME', mensagem: 'O nome do novo autor precisa ter pelo menos 2 caracteres.' });
        }

        if (autor.NOME.length > 200) {
            erros.push({ campo: 'NOVO_AUTOR_NOME', mensagem: 'O nome do autor não pode passar de 200 caracteres.' });
        }

        if (autor.NACIONALIDADE && autor.NACIONALIDADE.length > 100) {
            erros.push({ campo: 'NOVO_AUTOR_NACIONALIDADE', mensagem: 'A nacionalidade do autor não pode passar de 100 caracteres.' });
        }

        if (autor.DATA_NASCIMENTO && !dataValida(autor.DATA_NASCIMENTO)) {
            erros.push({ campo: 'NOVO_AUTOR_DATA_NASCIMENTO', mensagem: 'A data de nascimento do autor precisa ser uma data real.' });
        }
    } else if (!Number.isInteger(payload.AUTORES_ID) || payload.AUTORES_ID <= 0) {
        erros.push({ campo: 'AUTORES_ID', mensagem: 'Selecione um autor válido ou escolha cadastrar um novo autor.' });
    }

    if (!dataValida(payload.DATA_PUBLICACAO)) {
        erros.push({ campo: 'DATA_PUBLICACAO', mensagem: 'Informe uma data de publicação real.' });
    }

    if (!TIPOS_OPTIONS.includes(payload.TIPO)) {
        erros.push({ campo: 'TIPO', mensagem: 'O tipo precisa ser MANGA ou NOVEL.' });
    }

    if (!STATUS_OPTIONS.includes(payload.STATUS)) {
        erros.push({ campo: 'STATUS', mensagem: 'Escolha uma opção válida para o status.' });
    }

    if (payload.SINOPSE.length > 2000) {
        erros.push({ campo: 'SINOPSE', mensagem: 'A sinopse não pode passar de 2000 caracteres.' });
    }

    if (payload.CAPA_URL.length > 255) {
        erros.push({ campo: 'CAPA_URL', mensagem: 'O caminho da capa não pode passar de 255 caracteres.' });
    }

    if (arquivo) {
        const erroArquivo = validarArquivoCapa(arquivo);
        if (erroArquivo) erros.push({ campo: 'CAPA_ARQUIVO', mensagem: erroArquivo });
    }

    if (modo === 'criar' && !arquivo && !payload.CAPA_URL) {
        erros.push({ campo: 'CAPA_ARQUIVO', mensagem: 'Selecione uma imagem de capa para cadastrar o mangá.' });
    }

    return { valido: erros.length === 0, erros };
}

function validarArquivoCapa(arquivo) {
    if (!FORMATOS_CAPA_PERMITIDOS.includes(arquivo.type)) {
        return 'A capa precisa ser PNG, JPG, JPEG, WEBP ou GIF.';
    }

    if (arquivo.size > LIMITE_CAPA_MB * 1024 * 1024) {
        return `A imagem da capa não pode passar de ${LIMITE_CAPA_MB}MB.`;
    }

    return '';
}

function atualizarCamposAutor(form) {
    const modoAutor = form.querySelector('#manga-admin-modo-autor');
    const autorExistente = form.querySelector('.autor-existente-area');
    const novoAutor = form.querySelector('.novo-autor-area');
    const usandoNovo = modoAutor.value === 'novo';

    definirValor(form, '__USAR_NOVO_AUTOR', usandoNovo ? 'sim' : 'nao');
    autorExistente.classList.toggle('hidden', usandoNovo);
    novoAutor.classList.toggle('hidden', !usandoNovo);
    elemento(form, 'AUTORES_ID').required = !usandoNovo;
    elemento(form, 'NOVO_AUTOR_NOME').required = usandoNovo;
}

function atualizarPreviewCapa(img, texto, caminho, nomeArquivo = '') {
    if (!img || !texto) return;

    if (!caminho) {
        img.src = '/SiteLivros/public/assets/imagens/download.jpg';
        texto.textContent = 'Nenhuma imagem selecionada.';
        return;
    }

    img.src = caminho.startsWith('blob:') || caminho.startsWith('http')
        ? caminho
        : `/SiteLivros/public/assets/${caminho}`;
    texto.textContent = nomeArquivo || caminho;
}

function mostrarErrosFormulario(form, erros = []) {
    erros.forEach(({ campo, mensagem }) => {
        const alvo = form.querySelector(`[data-field="${campo}"]`);
        if (!alvo) return;

        alvo.classList.add('campo-com-erro');
        let erro = alvo.querySelector('.field-error');
        if (!erro) {
            erro = document.createElement('small');
            erro.className = 'field-error';
            alvo.appendChild(erro);
        }
        erro.textContent = mensagem;
    });
}

function limparErrosDoFormulario(form) {
    form.querySelectorAll('.campo-com-erro').forEach((campo) => campo.classList.remove('campo-com-erro'));
    form.querySelectorAll('.field-error').forEach((erro) => erro.remove());
}

function limparErroCampo(form, nomeCampo) {
    if (!nomeCampo) return;
    const alvo = form.querySelector(`[data-field="${nomeCampo}"]`);
    if (!alvo) return;
    alvo.classList.remove('campo-com-erro');
    alvo.querySelector('.field-error')?.remove();
}

function montarResumoErros(erros = []) {
    if (!erros.length) return 'Não foi possível salvar. Verifique os campos destacados.';
    const itens = erros
        .map((erro) => `<li><strong>${ROTULOS_CAMPOS[erro.campo] || 'Campo'}:</strong> ${escaparHtml(erro.mensagem)}</li>`)
        .join('');
    return `<strong>Não deu para salvar por estes motivos:</strong><ul>${itens}</ul>`;
}

function normalizarErrosApi(erro) {
    if (Array.isArray(erro?.erros)) return erro.erros;

    const detalhes = erro?.detalhes || erro?.resposta;
    if (!detalhes) return [];

    if (Array.isArray(detalhes)) {
        return detalhes.map((item) => ({
            campo: item.campo || item.Field || item.nome || 'TITULO',
            mensagem: item.mensagem || item.message || String(item),
        }));
    }

    if (typeof detalhes === 'object') {
        return Object.entries(detalhes).flatMap(([campo, valor]) => {
            if (Array.isArray(valor)) {
                return valor.map((item) => ({ campo, mensagem: typeof item === 'string' ? item : JSON.stringify(item) }));
            }
            if (typeof valor === 'object' && valor !== null) {
                return [{ campo, mensagem: valor.mensagem || JSON.stringify(valor) }];
            }
            return [{ campo, mensagem: String(valor) }];
        });
    }

    return [];
}

function mostrarMensagem(elemento, conteudo, tipo = 'info', html = false) {
    if (!elemento) return;
    elemento.className = `form-message ${tipo}`;
    if (html) elemento.innerHTML = conteudo;
    else elemento.textContent = conteudo;
}

function limparMensagem(elemento, somenteSeErro = false) {
    if (!elemento) return;
    if (somenteSeErro && !elemento.classList.contains('erro')) return;
    elemento.textContent = '';
    elemento.className = 'form-message';
}

function focarPrimeiroErro(form, erros = []) {
    const primeiro = erros.find((erro) => erro.campo)?.campo;
    if (!primeiro) return;
    const campo = elemento(form, primeiro) || form.querySelector(`[data-field="${primeiro}"] input, [data-field="${primeiro}"] select, [data-field="${primeiro}"] textarea`);
    campo?.focus?.();
}

function elemento(form, nome) {
    return form.elements.namedItem(nome);
}

function obterValor(form, nome) {
    const campo = elemento(form, nome);
    return String(campo?.value || '').trim();
}

function definirValor(form, nome, valor) {
    const campo = elemento(form, nome);
    if (campo) campo.value = valor ?? '';
}

function abrirModal() {
    modal.classList.remove('hidden');
    document.body.classList.add('modal-open');
    requestAnimationFrame(() => elemento(modal.querySelector('#form-manga-admin'), 'TITULO')?.focus());
}

function fecharModal() {
    document.getElementById('manga-admin-modal')?.classList.add('hidden');
    document.body.classList.remove('modal-open');
}

function normalizarDataInput(valor) {
    if (!valor) return '';
    const data = String(valor).slice(0, 10);
    return dataValida(data) ? data : '';
}

function dataValida(valor) {
    if (!/^\d{4}-\d{2}-\d{2}$/.test(valor)) return false;
    const data = new Date(`${valor}T00:00:00`);
    if (Number.isNaN(data.getTime())) return false;
    return data.toISOString().slice(0, 10) === valor;
}

function normalizarComparacao(valor) {
    return String(valor || '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .trim()
        .replace(/\s+/g, ' ')
        .toLowerCase();
}

function escaparHtml(texto) {
    return String(texto)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function navegarPara(caminho) {
    history.pushState(null, '', `${BASE_PATH}${caminho}`);
    window.dispatchEvent(new PopStateEvent('popstate'));
}

function formatarTituloParaUrl(titulo) {
    return encodeURIComponent(String(titulo).trim().replaceAll(' ', '-'));
}
