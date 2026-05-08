<?php
declare(strict_types=1);
require_once __DIR__ . "\..\..\Framework.php";
require_once __DIR__ . "\..\model\class.MangasModel.php";
require_once __DIR__ . "\..\model\class.AutoresModel.php";

class MangasDAO {
    static function GetTodos(){
        return DAO::Get()->Table("Mangas")->Execute();            
    }   
    static function Get($where){
        return DAO::Get()->Table("Mangas")->Where($where)->Execute();
    }
    static function Post(Mangas $dados){
        return DAO::Post()->Table("Mangas")->Dados($dados)->Execute();
    }
    static function Delete($where){
        return DAO::Delete()->Table("Mangas")->Where($where)->Execute();     
    }
    static function Put(Mangas $dados,$where){
        return DAO::Put()->Table("Mangas")->Dados($dados)->Where($where)->Execute();   
    }
    static function Describe(){
        return DAO::Describe()->Table("Mangas")->Execute();   
    }

    static function ExisteTitulo(string $titulo, ?int $ignorarId = null): bool {
        $pdo = Banco::BuscarConexao();
        $sql = "SELECT ID FROM MANGAS WHERE LOWER(TRIM(TITULO)) = LOWER(TRIM(:titulo))";
        if(!is_null($ignorarId)) $sql .= " AND ID <> :id";
        $sql .= " LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':titulo', $titulo);
        if(!is_null($ignorarId)) $stmt->bindValue(':id', $ignorarId, PDO::PARAM_INT);
        $stmt->execute();
        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }

    static function MangaExiste(int $id): bool {
        $pdo = Banco::BuscarConexao();
        $stmt = $pdo->prepare("SELECT ID FROM MANGAS WHERE ID = :id LIMIT 1");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }

    static function AutorExiste(int $id): bool {
        $pdo = Banco::BuscarConexao();
        $stmt = $pdo->prepare("SELECT ID FROM AUTORES WHERE ID = :id LIMIT 1");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }

    static function PostSeguro(Mangas $manga){
        $pdo = Banco::BuscarConexao();
        try {
            $id = self::InserirMangaComPdo($pdo, $manga);
            $resultado = self::BuscarMangaPorIdComPdo($pdo, $id);
            if($resultado) return Response::Success("Mangá cadastrado com sucesso!", [$resultado]);
            return Response::Fail("O mangá foi inserido, mas não foi possível buscar o cadastro salvo.");
        } catch(PDOException $e){
            return Response::Fail(self::MensagemAmigavelBanco($e), self::DetalheBanco('MANGAS', $e));
        }
    }

    static function PutSeguro(Mangas $manga, array $where){
        $pdo = Banco::BuscarConexao();
        $id = (int) ($where['id'] ?? 0);
        if($id <= 0) return Response::Fail("ID do mangá inválido.", [['campo' => 'ID', 'mensagem' => 'O ID precisa ser maior que zero.']]);

        try {
            self::AtualizarMangaComPdo($pdo, $manga, $id);
            $resultado = self::BuscarMangaPorIdComPdo($pdo, $id);
            if($resultado) return Response::Success("Mangá atualizado com sucesso!", [$resultado]);
            return Response::Fail("Nenhum mangá foi encontrado com esse ID.", [['campo' => 'ID', 'mensagem' => 'Confira se o mangá ainda existe no banco.']]);
        } catch(PDOException $e){
            return Response::Fail(self::MensagemAmigavelBanco($e), self::DetalheBanco('MANGAS', $e));
        }
    }

    static function PostComAutor(Mangas $manga, Autores $autor){
        $pdo = Banco::BuscarConexao();

        try {
            $pdo->beginTransaction();
            $autorId = self::InserirAutorComPdo($pdo, $autor);
            $manga->AlterarAutoresId($autorId);
            $mangaId = self::InserirMangaComPdo($pdo, $manga);
            $resultado = self::BuscarMangaPorIdComPdo($pdo, $mangaId);
            $pdo->commit();

            if($resultado) return Response::Success("Autor e mangá cadastrados com sucesso!", [$resultado]);
            return Response::Fail("O cadastro foi salvo, mas não foi possível buscar o mangá depois.");
        } catch(PDOException $e){
            if($pdo->inTransaction()) $pdo->rollBack();
            return Response::Fail(self::MensagemAmigavelBanco($e), self::DetalheBanco('MANGAS', $e));
        } catch(Throwable $e){
            if($pdo->inTransaction()) $pdo->rollBack();
            return Response::Fail($e->getMessage());
        }
    }

    static function PutComAutor(Mangas $manga, Autores $autor, array $where){
        $pdo = Banco::BuscarConexao();
        $id = (int) ($where['id'] ?? 0);
        if($id <= 0) return Response::Fail("ID do mangá inválido.", [['campo' => 'ID', 'mensagem' => 'O ID precisa ser maior que zero.']]);

        try {
            $pdo->beginTransaction();
            $autorId = self::InserirAutorComPdo($pdo, $autor);
            $manga->AlterarAutoresId($autorId);
            self::AtualizarMangaComPdo($pdo, $manga, $id);
            $resultado = self::BuscarMangaPorIdComPdo($pdo, $id);
            $pdo->commit();

            if($resultado) return Response::Success("Autor cadastrado e mangá atualizado com sucesso!", [$resultado]);
            return Response::Fail("Nenhum mangá foi encontrado com esse ID.", [['campo' => 'ID', 'mensagem' => 'Confira se o mangá ainda existe no banco.']]);
        } catch(PDOException $e){
            if($pdo->inTransaction()) $pdo->rollBack();
            return Response::Fail(self::MensagemAmigavelBanco($e), self::DetalheBanco('MANGAS', $e));
        } catch(Throwable $e){
            if($pdo->inTransaction()) $pdo->rollBack();
            return Response::Fail($e->getMessage());
        }
    }

    private static function InserirAutorComPdo(PDO $pdo, Autores $autor): int {
        $sql = "INSERT INTO AUTORES (NOME, BIOGRAFIA, DATA_NASCIMENTO, NACIONALIDADE)
                VALUES (:NOME, :BIOGRAFIA, :DATA_NASCIMENTO, :NACIONALIDADE)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':NOME', $autor->NOME);
        self::BindNullable($stmt, ':BIOGRAFIA', $autor->BIOGRAFIA);
        self::BindNullable($stmt, ':DATA_NASCIMENTO', $autor->DATA_NASCIMENTO);
        self::BindNullable($stmt, ':NACIONALIDADE', $autor->NACIONALIDADE);
        $stmt->execute();
        return (int) $pdo->lastInsertId();
    }

    private static function InserirMangaComPdo(PDO $pdo, Mangas $manga): int {
        $sql = "INSERT INTO MANGAS (TITULO, AUTORES_ID, DATA_PUBLICACAO, SINOPSE, TIPO, STATUS, CAPA_URL)
                VALUES (:TITULO, :AUTORES_ID, :DATA_PUBLICACAO, :SINOPSE, :TIPO, :STATUS, :CAPA_URL)";
        $stmt = $pdo->prepare($sql);
        self::BindManga($stmt, $manga);
        $stmt->execute();
        return (int) $pdo->lastInsertId();
    }

    private static function AtualizarMangaComPdo(PDO $pdo, Mangas $manga, int $id): void {
        $sql = "UPDATE MANGAS SET
                    TITULO = :TITULO,
                    AUTORES_ID = :AUTORES_ID,
                    DATA_PUBLICACAO = :DATA_PUBLICACAO,
                    SINOPSE = :SINOPSE,
                    TIPO = :TIPO,
                    STATUS = :STATUS,
                    CAPA_URL = :CAPA_URL
                WHERE ID = :ID";
        $stmt = $pdo->prepare($sql);
        self::BindManga($stmt, $manga);
        $stmt->bindValue(':ID', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    private static function BindManga(PDOStatement $stmt, Mangas $manga): void {
        $stmt->bindValue(':TITULO', $manga->TITULO);
        $stmt->bindValue(':AUTORES_ID', $manga->AUTORES_ID, PDO::PARAM_INT);
        $stmt->bindValue(':DATA_PUBLICACAO', $manga->DATA_PUBLICACAO);
        $stmt->bindValue(':SINOPSE', $manga->SINOPSE);
        $stmt->bindValue(':TIPO', $manga->TIPO);
        $stmt->bindValue(':STATUS', $manga->STATUS);
        $stmt->bindValue(':CAPA_URL', $manga->CAPA_URL);
    }

    private static function BindNullable(PDOStatement $stmt, string $param, ?string $valor): void {
        if($valor === null || $valor === '') $stmt->bindValue($param, null, PDO::PARAM_NULL);
        else $stmt->bindValue($param, $valor);
    }

    private static function BuscarMangaPorIdComPdo(PDO $pdo, int $id): ?array {
        $stmt = $pdo->prepare("SELECT * FROM MANGAS WHERE ID = :ID LIMIT 1");
        $stmt->bindValue(':ID', $id, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ?: null;
    }

    private static function MensagemAmigavelBanco(PDOException $e): string {
        $mensagem = $e->getMessage();
        if(str_contains($mensagem, 'Duplicate entry') || $e->getCode() === '23000'){
            if(str_contains($mensagem, 'TITULO')) return 'Já existe um mangá cadastrado com esse título.';
            return 'Já existe um registro com valor único igual no banco.';
        }
        if(str_contains($mensagem, 'foreign key constraint')){
            return 'O autor selecionado não existe ou foi removido do banco.';
        }
        return 'Erro ao salvar no banco de dados.';
    }

    private static function DetalheBanco(string $campoPadrao, PDOException $e): array {
        $mensagem = self::MensagemAmigavelBanco($e);
        $campo = str_contains($mensagem, 'título') ? 'TITULO' : $campoPadrao;
        if(str_contains($mensagem, 'autor')) $campo = 'AUTORES_ID';
        return [['campo' => $campo, 'mensagem' => $mensagem]];
    }

    static function GetMangasGeneros(){
        $pdo = Banco::BuscarConexao();

        $sql = "SELECT 
                    M.ID,
                    M.TITULO,
                    M.AUTORES_ID,
                    M.DATA_PUBLICACAO,
                    M.SINOPSE,
                    M.CRIADO_QUANDO,
                    M.TIPO,
                    M.STATUS,
                    M.CAPA_URL,
                    G.NOME as G_NOME,
                    G.ID as G_ID
                FROM MANGAS M
                LEFT JOIN GENEROSMANGAS GM ON M.ID = GM.MANGAS_ID
                LEFT JOIN GENEROS G ON G.ID = GM.GENEROS_ID
                ORDER BY M.ID DESC;";

        $stmt = $pdo->query($sql);
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $mangas = [];

        foreach($resultado as $linha){
            $id = $linha['ID'];
            if(!isset($mangas[$id])){
                $mangas[$id] = [
                    'ID' => $linha['ID'],
                    'TITULO' => $linha['TITULO'],
                    'AUTORES_ID' => $linha['AUTORES_ID'],
                    'DATA_PUBLICACAO' => $linha['DATA_PUBLICACAO'],
                    'SINOPSE' => $linha['SINOPSE'],
                    'CRIADO_QUANDO' => $linha['CRIADO_QUANDO'],
                    'TIPO' => $linha['TIPO'],
                    'STATUS' => $linha['STATUS'],
                    'CAPA_URL' => $linha['CAPA_URL'],
                    'GENEROS' => [],
                ]; 
            }

            if(!empty($linha['G_ID'])){
                $mangas[$id]['GENEROS'][] = [
                    'NOME' => $linha['G_NOME'],
                    'ID' => $linha['G_ID'],
                ];
            }
        }
        $resultadoFinal = array_values($mangas);
        return Response::Success("Dados de gêneros e mangás encontrados com sucesso",$resultadoFinal);       
    } 

    static function GetMangaGeneroAutorCapitulos($url){
        $pdo = Banco::BuscarConexao();
        $titulo = str_ireplace("-"," ",$url['titulo']);
        
        $sqlManga = "SELECT * FROM MANGAS WHERE TITULO = :titulo LIMIT 1;";
        $stmt = $pdo->prepare($sqlManga);
        $stmt->bindValue(':titulo', $titulo);
        $stmt->execute();
        $manga = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$manga){
            return Response::Fail("Mangá não encontrado!");
        }

        $id = (int) $manga['ID'];
        $sqlGenero = "SELECT G.* FROM GENEROS G
        INNER JOIN GENEROSMANGAS GM ON GM.GENEROS_ID = G.ID
        WHERE GM.MANGAS_ID = :id;";
        $stmt = $pdo->prepare($sqlGenero);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $generos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $autoresId = (int) $manga['AUTORES_ID'];
        $sqlAutor = "SELECT * FROM AUTORES WHERE ID = :autoresId LIMIT 1;";
        $stmt = $pdo->prepare($sqlAutor);
        $stmt->bindValue(':autoresId', $autoresId, PDO::PARAM_INT);
        $stmt->execute();
        $autor = $stmt->fetch(PDO::FETCH_ASSOC);

        $sqlCapitulos = "SELECT * FROM CAPITULOS WHERE MANGAS_ID = :id ORDER BY NUMERO_CAPITULO + 0 ASC;";
        $stmt = $pdo->prepare($sqlCapitulos);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $capitulos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $manga['GENEROS'] = $generos; 
        $manga['AUTOR'] = $autor ?: ['ID' => $autoresId, 'NOME' => 'Autor desconhecido']; 
        $manga['CAPITULOS'] = $capitulos; 
        return Response::Success("Dados de gêneros, autor e capítulos encontrados com sucesso",$manga);       
    }
}

?>
