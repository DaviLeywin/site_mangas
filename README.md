# MangaVerse / SiteLivros — versão otimizada

Esta versão foi ajustada para carregar mais rápido e ficar mais apresentável visualmente.

## Principais melhorias desta versão

- Requisições para a API PHP agora têm timeout curto. Se o banco/API demorar ou falhar, o site usa dados mockados rapidamente em vez de ficar travado carregando.
- Templates HTML das páginas agora ficam em cache no navegador durante a navegação.
- Home carrega gêneros e mangás em paralelo.
- Cards usam imagens com `loading="lazy"` e `decoding="async"` para reduzir travamentos.
- Busca no topo filtra cards enquanto o usuário digita.
- Home ganhou resumo com total de mangás, gêneros, favoritos e leituras em progresso.
- Home ganhou seção de histórico de leitura.
- Favoritos continuam salvos no `localStorage`.
- Progresso de leitura continua salvo no `localStorage`.
- Histórico de leitura agora também fica salvo no `localStorage`.
- Página de detalhes mostra capítulos lidos e capítulo atual em progresso.
- Leitor salva progresso com menos frequência para evitar excesso de escrita no navegador.
- CSS foi refeito com visual mais consistente, moderno e responsivo.
- Header ganhou atalhos para Catálogo, Continuar, Favoritos e Histórico.

## Como rodar

Coloque a pasta `SiteLivros` dentro de:

```txt
C:\xampp\htdocs\
```

Depois acesse:

```txt
http://localhost/SiteLivros/Inicio
```

## Banco de dados

O projeto ainda mantém a API PHP existente dentro de `api/`.

Os scripts SQL estão em:

```txt
api/db/db.sql
api/db/seed.sql
```

Se a API não estiver configurada ou o banco estiver lento/fora do ar, o front-end usa dados mockados automaticamente para o trabalho não quebrar.


## Atualização dos dados-base

Os inserts principais também estão dentro de:

```txt
api/config/config.php
```

Nesta versão, os mangás do seed foram revisados para ter `CAPA_URL` válido, autores mais coerentes, status padronizado e gêneros mais realistas. As capas locais criadas para o seed ficam em:

```txt
public/assets/imagens/capas_seed/
```

Para ver os dados corrigidos no navegador, recrie o banco depois de ligar o MySQL. Se o banco antigo continuar existindo, ele pode manter os registros velhos.

## Arquivos principais alterados

```txt
public/index.html
public/css/global.css
public/pages/home.html
public/js/router.js
public/js/pages/home.js
public/js/pages/manga.js
public/js/pages/gender.js
public/js/pages/chapter.js
public/js/utils/api.js
public/js/utils/dom.js
public/js/utils/storage.js
```
