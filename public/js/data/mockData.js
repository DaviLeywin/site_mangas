export const generosMock = [
    { ID: 1, NOME: "Ação" },
    { ID: 2, NOME: "Aventura" },
    { ID: 3, NOME: "Comédia" },
    { ID: 4, NOME: "Drama" },
    { ID: 5, NOME: "Fantasia" },
    { ID: 6, NOME: "Romance" },
    { ID: 7, NOME: "Terror" },
    { ID: 8, NOME: "Ficção Científica" },
    { ID: 9, NOME: "Mistério" },
    { ID: 10, NOME: "Slice of Life" },
    { ID: 11, NOME: "Esporte" },
    { ID: 12, NOME: "Histórico" },
    { ID: 13, NOME: "Sobrenatural" },
    { ID: 14, NOME: "Mecha" },
    { ID: 15, NOME: "Artes Marciais" },
    { ID: 16, NOME: "Psicológico" },
    { ID: 17, NOME: "Suspense" },
    { ID: 18, NOME: "Escolar" },
    { ID: 19, NOME: "Isekai" },
    { ID: 20, NOME: "Magia" },
    { ID: 21, NOME: "Super-heróis" },
    { ID: 22, NOME: "Samurai" },
    { ID: 23, NOME: "Fantasia Sombria" },
    { ID: 24, NOME: "Policial" },
    { ID: 25, NOME: "Shounen" },
    { ID: 26, NOME: "Seinen" },
    { ID: 27, NOME: "Magical Girl" },
    { ID: 28, NOME: "Shoujo" }
];

export const mangasMock = [
    criarManga(1, "One Piece", "imagens/capas_seed/one-piece.jpg", "Em andamento", [1, 2, 3, 5, 25], "Eiichiro Oda", "A aventura de Monkey D. Luffy e dos Piratas do Chapéu de Palha em busca do tesouro One Piece.", "1997-07-22", "MANGA", 1),
    criarManga(2, "Naruto", "imagens/capas_seed/naruto.jpg", "Concluído", [1, 2, 15, 5, 25], "Masashi Kishimoto", "Naruto Uzumaki busca reconhecimento em sua vila e sonha em se tornar Hokage.", "1999-09-21", "MANGA", 2),
    criarManga(3, "Attack on Titan", "imagens/capas_seed/attack-on-titan.jpg", "Concluído", [1, 4, 9, 17, 23, 26], "Hajime Isayama", "A humanidade luta pela sobrevivência contra criaturas gigantes conhecidas como Titãs.", "2009-09-09", "MANGA", 3),
    criarManga(4, "Demon Slayer: Kimetsu no Yaiba", "imagens/capas_seed/demon-slayer-kimetsu-no-yaiba.jpg", "Concluído", [1, 2, 4, 5, 13, 25], "Koyoharu Gotouge", "Tanjiro Kamado enfrenta demônios para salvar sua irmã Nezuko.", "2016-02-15", "MANGA", 5),
    criarManga(5, "Bleach", "imagens/capas_seed/bleach.jpg", "Concluído", [1, 2, 5, 13, 25], "Tite Kubo", "Ichigo Kurosaki recebe poderes de Ceifador de Almas e passa a enfrentar ameaças espirituais.", "2001-08-07", "MANGA", 7),
    criarManga(6, "Dragon Ball", "imagens/capas_seed/dragon-ball.jpg", "Concluído", [1, 2, 3, 15, 5, 25], "Akira Toriyama", "Goku vive aventuras, treina artes marciais e procura as Esferas do Dragão.", "1984-12-03", "MANGA", 8),
    criarManga(7, "Tokyo Ghoul", "imagens/capas_seed/tokyo-ghoul.jpg", "Concluído", [1, 4, 7, 13, 16, 17, 26], "Sui Ishida", "Kaneki Ken se torna meio humano e meio ghoul após um encontro que muda sua vida.", "2011-09-08", "MANGA", 14),
    criarManga(8, "Death Note", "imagens/capas_seed/death-note.jpg", "Concluído", [4, 9, 13, 16, 17, 26], "Tsugumi Ohba e Takeshi Obata", "Light Yagami encontra um caderno sobrenatural capaz de matar pessoas pelo nome.", "2003-12-01", "MANGA", 15),
    criarManga(9, "Berserk", "imagens/capas_seed/berserk.jpg", "Em andamento", [1, 2, 4, 7, 23, 26], "Kentaro Miura", "Guts atravessa um mundo brutal de fantasia sombria marcado por guerra, ambição e monstros.", "1989-08-25", "MANGA", 11),
    criarManga(10, "Sailor Moon", "imagens/capas_seed/sailor-moon.jpg", "Concluído", [2, 5, 6, 13, 27, 28], "Naoko Takeuchi", "Usagi Tsukino desperta como uma guerreira mágica destinada a proteger o mundo.", "1991-12-28", "MANGA", 10),
    criarManga(11, "Hunter x Hunter", "imagens/capas_seed/hunter-x-hunter.jpg", "Hiato", [1, 2, 5, 25], "Yoshihiro Togashi", "Gon Freecss busca se tornar Hunter e encontrar seu pai.", "1998-03-03", "MANGA", 6),
    criarManga(12, "Black Butler (Kuroshitsuji)", "imagens/capas_seed/black-butler-kuroshitsuji.jpg", "Em andamento", [4, 9, 13, 17, 23, 26], "Yana Toboso", "Ciel Phantomhive faz um pacto com um mordomo demoníaco para buscar vingança.", "2006-09-16", "MANGA", 12),
    criarManga(13, "Ah! My Goddess", "imagens/capas_seed/ah-my-goddess.jpg", "Concluído", [3, 5, 6, 13, 26], "Kosuke Fujishima", "A vida de Keiichi Morisato muda quando ele conhece a deusa Belldandy.", "1988-08-25", "MANGA", 13),
    criarManga(14, "Cardcaptor Sakura", "imagens/capas_seed/cardcaptor-sakura.jpg", "Concluído", [2, 5, 6, 13, 27, 28], "CLAMP", "Sakura Kinomoto precisa capturar cartas mágicas que escaparam de um livro misterioso.", "1996-05-01", "MANGA", 9),
    criarManga(15, "Ranma ½", "imagens/capas_seed/ranma-12.jpg", "Concluído", [1, 3, 6, 15, 25], "Rumiko Takahashi", "Ranma Saotome vive confusões depois de passar a se transformar ao contato com água fria.", "1987-08-19", "MANGA", 4),
    criarManga(16, "Inuyasha", "imagens/capas_seed/inuyasha.jpg", "Concluído", [1, 2, 4, 5, 6, 13, 25], "Rumiko Takahashi", "Kagome Higurashi viaja para o Japão feudal e encontra o meio-demônio Inuyasha.", "1996-11-13", "MANGA", 4),
    criarManga(17, "Yu Yu Hakusho", "imagens/capas_seed/yu-yu-hakusho.jpg", "Concluído", [1, 2, 3, 13, 15, 25], "Yoshihiro Togashi", "Yusuke Urameshi torna-se detetive espiritual após morrer salvando uma criança.", "1990-12-03", "MANGA", 6),
    criarManga(18, "xxxHOLiC", "imagens/capas_seed/xxxholic.webp", "Concluído", [4, 9, 5, 13, 16, 26], "CLAMP", "Kimihiro Watanuki trabalha para Yuko Ichihara, uma mulher capaz de realizar desejos.", "2003-02-24", "MANGA", 9),
    criarManga(19, "Youre Under Arrest", "imagens/capas_seed/youre-under-arrest.jpg", "Concluído", [1, 3, 24, 26], "Kosuke Fujishima", "Duas policiais enfrentam casos e confusões em uma delegacia de trânsito.", "1986-11-01", "MANGA", 13),
    criarManga(20, "Bakuman", "imagens/capas_seed/bakuman.jpg", "Concluído", [3, 4, 10, 18, 25], "Tsugumi Ohba e Takeshi Obata", "Dois jovens aspirantes a mangakás tentam alcançar sucesso na indústria de mangás.", "2008-08-11", "MANGA", 15),
    criarManga(21, "The Promised Neverland", "imagens/capas_seed/the-promised-neverland.jpg", "Concluído", [4, 9, 16, 17, 25], "Kaiu Shirai e Posuka Demizu", "Crianças descobrem o segredo de seu orfanato e planejam uma fuga perigosa.", "2016-08-01", "MANGA", 16),
    criarManga(22, "My Hero Academia", "imagens/capas_seed/my-hero-academia.jpg", "Concluído", [1, 3, 18, 21, 25], "Kohei Horikoshi", "Izuku Midoriya entra em uma escola para heróis em um mundo dominado por superpoderes.", "2014-07-07", "MANGA", 17),
    criarManga(23, "Jujutsu Kaisen", "imagens/capas_seed/jujutsu-kaisen.jpg", "Concluído", [1, 4, 13, 23, 25], "Gege Akutami", "Yuji Itadori entra no mundo das maldições após ingerir um objeto amaldiçoado.", "2018-03-05", "MANGA", 18),
    criarManga(24, "Fairy Tail", "imagens/capas_seed/fairy-tail.jpg", "Concluído", [1, 2, 3, 5, 20, 25], "Hiro Mashima", "A guilda Fairy Tail vive aventuras mágicas cheias de amizade e batalhas.", "2006-08-02", "MANGA", 19),
    criarManga(25, "Fullmetal Alchemist", "imagens/capas_seed/fullmetal-alchemist.webp", "Concluído", [1, 2, 4, 5, 25], "Hiromu Arakawa", "Dois irmãos alquimistas buscam a Pedra Filosofal para recuperar seus corpos.", "2001-07-12", "MANGA", 20),
    criarManga(26, "Soul Eater", "imagens/capas_seed/soul-eater.jpg", "Concluído", [1, 3, 5, 13, 25], "Atsushi Ohkubo", "Estudantes treinam armas vivas e artesãos em uma academia comandada pela Morte.", "2004-05-12", "MANGA", 21),
    criarManga(27, "Black Clover", "imagens/capas_seed/black-clover.jpg", "Em andamento", [1, 2, 3, 5, 20, 25], "Yūki Tabata", "Asta e Yuno competem para se tornar o Rei Mago em um mundo movido por magia.", "2015-02-16", "MANGA", 22),
    criarManga(28, "D.Gray-man", "imagens/capas_seed/d-gray-man.jpg", "Em andamento", [1, 4, 13, 23, 25], "Katsura Hoshino", "Allen Walker combate Akuma usando uma arma anti-Akuma chamada Inocência.", "2004-05-31", "MANGA", 23),
    criarManga(29, "Gintama", "imagens/capas_seed/gintama.jpg", "Concluído", [1, 3, 8, 12, 25], "Hideaki Sorachi", "Gintoki Sakata vive aventuras cômicas em um Japão alternativo dominado por alienígenas.", "2003-12-08", "MANGA", 24),
    criarManga(30, "Vagabond", "imagens/capas_seed/vagabond.jpg", "Hiato", [1, 4, 12, 15, 22, 26], "Takehiko Inoue", "A obra acompanha uma versão ficcional da vida do espadachim Miyamoto Musashi.", "1998-09-03", "MANGA", 25),
    criarManga(31, "Claymore", "imagens/capas_seed/claymore.jpg", "Concluído", [1, 2, 4, 5, 7, 23, 25], "Norihiro Yagi", "Guerreiras meio-humanas enfrentam monstros devoradores de pessoas conhecidos como Yoma.", "2001-05-08", "MANGA", 26),
    criarManga(32, "Noragami", "imagens/capas_seed/noragami.jpg", "Concluído", [1, 3, 5, 13, 25], "Adachitoka", "Yato, um deus menor, tenta ganhar seguidores e mudar seu destino.", "2010-12-06", "MANGA", 27),
    criarManga(33, "Blue Exorcist", "imagens/capas_seed/blue-exorcist.jpg", "Em andamento", [1, 5, 13, 25], "Kazue Kato", "Rin Okumura descobre ser filho de Satanás e decide se tornar exorcista.", "2009-04-04", "MANGA", 28),
    criarManga(34, "Katekyo Hitman Reborn!", "imagens/capas_seed/katekyo-hitman-reborn.jpg", "Concluído", [1, 3, 13, 24, 25], "Akira Amano", "Tsuna Sawada descobre que foi escolhido para herdar uma família mafiosa.", "2004-05-24", "MANGA", 29),
    criarManga(35, "Shaman King", "imagens/capas_seed/shaman-king.jpg", "Concluído", [1, 2, 13, 25], "Hiroyuki Takei", "Yoh Asakura compete para se tornar o Rei dos Xamãs.", "1998-06-30", "MANGA", 30),
    criarManga(36, "Mob Psycho 100", "imagens/capas_seed/mob-psycho-100.jpg", "Concluído", [1, 3, 10, 13, 16, 25], "ONE", "Shigeo Kageyama, um jovem com poderes psíquicos, tenta levar uma vida normal.", "2012-04-18", "MANGA", 31),
    criarManga(37, "Made in Abyss", "imagens/capas_seed/made-in-abyss.jpg", "Em andamento", [2, 4, 5, 7, 17, 26], "Akihito Tsukushi", "Riko desce ao Abismo em busca de sua mãe e descobre seus perigos.", "2012-10-20", "MANGA", 32),
    criarManga(38, "The Quintessential Quintuplets", "imagens/capas_seed/the-quintessential-quintuplets.jpg", "Concluído", [3, 6, 10, 18, 25], "Negi Haruba", "Um tutor ajuda cinco irmãs gêmeas a melhorar seus estudos.", "2017-08-09", "MANGA", 33),
    criarManga(39, "Erased", "imagens/capas_seed/erased.jpg", "Concluído", [4, 9, 16, 17, 26], "Kei Sanbe", "Satoru Fujinuma volta no tempo para impedir uma tragédia de sua infância.", "2012-06-04", "MANGA", 34),
    criarManga(40, "Kaguya-sama: Love is War", "imagens/capas_seed/kaguya-sama-love-is-war.jpg", "Concluído", [3, 6, 16, 18, 26], "Aka Akasaka", "Dois estudantes tentam fazer o outro confessar amor primeiro em uma disputa psicológica.", "2015-05-19", "MANGA", 35),
    criarManga(41, "That Time I Got Reincarnated as a Slime", "imagens/capas_seed/that-time-i-got-reincarnated-as-a-slime.jpg", "Em andamento", [1, 2, 3, 5, 19], "Fuse", "Um homem reencarna como slime em um mundo de fantasia e constrói uma nova comunidade.", "2013-05-30", "NOVEL", 36),
    criarManga(42, "Sword Art Online", "imagens/capas_seed/sword-art-online.jpg", "Em andamento", [1, 2, 6, 8, 19], "Reki Kawahara", "Jogadores são presos em um MMORPG de realidade virtual onde morrer no jogo significa morrer de verdade.", "2009-04-10", "NOVEL", 37),
    criarManga(43, "Overlord", "imagens/capas_seed/overlord.jpg", "Em andamento", [1, 5, 19, 23], "Kugane Maruyama", "Um jogador fica preso como seu avatar poderoso em um mundo inspirado em um MMORPG.", "2012-07-30", "NOVEL", 38),
    criarManga(44, "Re:Zero - Starting Life in Another World", "imagens/capas_seed/re-zero-starting-life-in-another-world.jpg", "Em andamento", [4, 5, 16, 19], "Tappei Nagatsuki", "Subaru Natsuki é levado a outro mundo e descobre que retorna no tempo ao morrer.", "2014-01-25", "NOVEL", 39),
    criarManga(45, "The Rising of the Shield Hero", "imagens/capas_seed/the-rising-of-the-shield-hero.jpg", "Em andamento", [1, 2, 4, 5, 19], "Aneko Yusagi", "Naofumi Iwatani é convocado para outro mundo como o Herói do Escudo.", "2013-08-22", "NOVEL", 40)
];

function criarManga(ID, TITULO, CAPA_URL, STATUS, generosIds, autor, sinopse, dataPublicacao, tipo = 'MANGA', autoresId = ID) {
    const GENEROS = generosIds.map((id) => generosMock.find((genero) => genero.ID === id)).filter(Boolean);

    return {
        ID,
        TITULO,
        CAPA_URL,
        STATUS,
        TIPO: tipo,
        CRIADO_QUANDO: dataPublicacao,
        DATA_PUBLICACAO: dataPublicacao,
        SINOPSE: sinopse,
        AUTOR: { ID: autoresId, NOME: autor },
        AUTORES_ID: autoresId,
        GENEROS,
        CAPITULOS: [1, 2, 3, 4, 5].map((numero) => ({
            ID: `${ID}-${numero}`,
            MANGAS_ID: ID,
            NUMERO_CAPITULO: String(numero),
            TITULO_CAPITULO: `Capítulo ${numero} — ${numero === 1 ? 'Início da jornada' : 'Novo acontecimento'}` ,
            DATA_LANCAMENTO: dataPublicacao,
        })),
    };
}

export function buscarMangaMockPorTitulo(tituloUrl) {
    const tituloNormalizado = decodeURIComponent(tituloUrl).replaceAll('-', ' ').toLowerCase();
    return mangasMock.find((manga) => manga.TITULO.toLowerCase() === tituloNormalizado) || null;
}

export function buscarMangasMockPorGenero(nomeGeneroUrl) {
    const nome = decodeURIComponent(nomeGeneroUrl).replaceAll('-', ' ').toLowerCase();
    return mangasMock.filter((manga) => manga.GENEROS.some((genero) => genero.NOME.toLowerCase() === nome));
}

export function obterGenerosMock() {
    return generosMock;
}

export function obterMangasComGenerosMock() {
    return mangasMock.map((manga) => ({
        ID: manga.ID,
        TITULO: manga.TITULO,
        CAPA_URL: manga.CAPA_URL,
        STATUS: manga.STATUS,
        TIPO: manga.TIPO,
        GENEROS: manga.GENEROS,
    }));
}

export function obterDestaquesMock() {
    return mangasMock.slice(0, 6);
}

export function criarHistoricoMock() {
    return mangasMock.slice(0, 3).map((manga, index) => ({
        id: `${manga.ID}-${index}`,
        manga,
        capitulo: index + 1,
        progresso: 35 + index * 20,
        atualizadoEm: Date.now() - index * 86400000,
    }));
}

export function criarFavoritosMock() {
    return mangasMock.slice(3, 8).map((manga) => ({
        id: manga.ID,
        manga,
        criadoEm: Date.now(),
    }));
}
