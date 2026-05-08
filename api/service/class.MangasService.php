<?php 
require_once __DIR__ . "\..\model\class.MangasModel.php";
require_once __DIR__ . "\..\model\class.AutoresModel.php";
require_once __DIR__ . "\..\dao\class.MangasDAO.php";
require_once __DIR__ . '\..\validator\class.BaseValidator.php';

class MangasService {
    static function GetTodos(){
        return MangasDAO::GetTodos();
    }

    static function GetMangasGeneros(){
        return MangasDAO::GetMangasGeneros();
    }

    static function GetMangaGeneroAutorCapitulos($url){
        return MangasDAO::GetMangaGeneroAutorCapitulos($url);
    }
    
    static function Get($url){
        return MangasDAO::Get($url);
    }

    static function Post($request){
        $erros = self::ValidarMangaRequest($request, null, true);
        if($erros) return Response::Fail("Não foi possível cadastrar o mangá. Corrija os campos destacados.", $erros);

        $Mangas = self::CriarModelManga($request, (int) $request['AUTORES_ID']);
        return MangasDAO::PostSeguro($Mangas);
    }

    static function PostComAutor($request){
        if(!isset($request['AUTOR']) || !is_array($request['AUTOR'])){
            return Response::Fail("Informe os dados do novo autor.", [
                ['campo' => 'NOVO_AUTOR_NOME', 'mensagem' => 'Escolha um autor existente ou preencha o nome do novo autor.']
            ]);
        }

        $autorRequest = $request['AUTOR'];
        unset($request['AUTOR'], $request['AUTORES_ID']);

        $erros = array_merge(
            self::ValidarMangaRequest($request, null, false),
            self::ValidarAutorRequest($autorRequest, true)
        );

        if($erros) return Response::Fail("Não foi possível cadastrar o mangá e o autor. Corrija os campos destacados.", $erros);

        $Autores = self::CriarModelAutor($autorRequest);
        $Mangas = self::CriarModelManga($request, 1);

        return MangasDAO::PostComAutor($Mangas, $Autores);
    }

    static function Put($request, $url){
        $id = (int) ($url['id'] ?? 0);
        $erros = self::ValidarIdManga($id);
        $erros = array_merge($erros, self::ValidarMangaRequest($request, $id, true));
        if($erros) return Response::Fail("Não foi possível atualizar o mangá. Corrija os campos destacados.", $erros);

        $Mangas = self::CriarModelManga($request, (int) $request['AUTORES_ID']);
        return MangasDAO::PutSeguro($Mangas, $url);
    }

    static function PutComAutor($request, $url){
        $id = (int) ($url['id'] ?? 0);
        if(!isset($request['AUTOR']) || !is_array($request['AUTOR'])){
            return Response::Fail("Informe os dados do novo autor.", [
                ['campo' => 'NOVO_AUTOR_NOME', 'mensagem' => 'Preencha o nome do novo autor antes de atualizar o mangá.']
            ]);
        }

        $autorRequest = $request['AUTOR'];
        unset($request['AUTOR'], $request['AUTORES_ID']);

        $erros = array_merge(
            self::ValidarIdManga($id),
            self::ValidarMangaRequest($request, $id, false),
            self::ValidarAutorRequest($autorRequest, true)
        );

        if($erros) return Response::Fail("Não foi possível atualizar o mangá e cadastrar o autor. Corrija os campos destacados.", $erros);

        $Autores = self::CriarModelAutor($autorRequest);
        $Mangas = self::CriarModelManga($request, 1);

        return MangasDAO::PutComAutor($Mangas, $Autores, $url);
    }
    
    static function Delete($url){
        return MangasDAO::Delete($url);
    }

    private static function ValidarIdManga(int $id): array {
        $erros = [];
        if($id <= 0){
            $erros[] = ['campo' => 'ID', 'mensagem' => 'O ID do mangá precisa ser maior que zero.'];
            return $erros;
        }

        if(!MangasDAO::MangaExiste($id)){
            $erros[] = ['campo' => 'ID', 'mensagem' => 'Esse mangá não foi encontrado no banco. Talvez ele tenha sido removido.'];
        }
        return $erros;
    }

    private static function ValidarMangaRequest(array $request, ?int $idAtual, bool $exigirAutorExistente): array {
        $erros = [];
        $permitidos = ['TITULO', 'AUTORES_ID', 'DATA_PUBLICACAO', 'SINOPSE', 'TIPO', 'STATUS', 'CAPA_URL'];

        foreach($request as $campo => $valor){
            if(!in_array($campo, $permitidos, true)){
                $erros[] = ['campo' => $campo, 'mensagem' => "O campo $campo não é aceito no cadastro de mangá."];
            }
        }

        $titulo = self::Str($request['TITULO'] ?? '');
        $dataPublicacao = self::Str($request['DATA_PUBLICACAO'] ?? '');
        $sinopse = self::Str($request['SINOPSE'] ?? '');
        $tipo = self::Str($request['TIPO'] ?? '');
        $status = self::Str($request['STATUS'] ?? '');
        $capaUrl = self::Str($request['CAPA_URL'] ?? '');
        $autoresId = $request['AUTORES_ID'] ?? null;

        if(mb_strlen($titulo) < 2) $erros[] = ['campo' => 'TITULO', 'mensagem' => 'O título precisa ter pelo menos 2 caracteres.'];
        if(mb_strlen($titulo) > 200) $erros[] = ['campo' => 'TITULO', 'mensagem' => 'O título não pode passar de 200 caracteres.'];
        if($titulo && MangasDAO::ExisteTitulo($titulo, $idAtual)){
            $erros[] = ['campo' => 'TITULO', 'mensagem' => 'Já existe um mangá cadastrado com esse título. Use outro título ou edite o cadastro existente.'];
        }

        if(!self::DataReal($dataPublicacao)){
            $erros[] = ['campo' => 'DATA_PUBLICACAO', 'mensagem' => 'A data de publicação precisa ser uma data real no formato AAAA-MM-DD.'];
        }

        if(!MangasValidator::ValidarTipo($tipo)){
            $erros[] = ['campo' => 'TIPO', 'mensagem' => 'O tipo precisa ser MANGA ou NOVEL.'];
        }

        if(!MangasValidator::ValidarStatus($status)){
            $erros[] = ['campo' => 'STATUS', 'mensagem' => 'Escolha um status válido: '.implode(', ', MangasValidator::StatusValidos()).'.'];
        }

        if(mb_strlen($sinopse) > 2000){
            $erros[] = ['campo' => 'SINOPSE', 'mensagem' => 'A sinopse não pode passar de 2000 caracteres.'];
        }

        if($capaUrl === ''){
            $erros[] = ['campo' => 'CAPA_ARQUIVO', 'mensagem' => 'Envie uma imagem de capa antes de cadastrar o mangá.'];
        } else {
            if(mb_strlen($capaUrl) > 255){
                $erros[] = ['campo' => 'CAPA_URL', 'mensagem' => 'O caminho da capa não pode passar de 255 caracteres.'];
            }
            if(str_contains($capaUrl, '..') || str_starts_with($capaUrl, '/') || str_starts_with(strtolower($capaUrl), 'file:')){
                $erros[] = ['campo' => 'CAPA_URL', 'mensagem' => 'O caminho da capa não pode apontar para fora da pasta de imagens.'];
            }
            if(!preg_match('/^(imagens\/|https?:\/\/)/i', $capaUrl)){
                $erros[] = ['campo' => 'CAPA_URL', 'mensagem' => 'O caminho da capa precisa começar com imagens/ ou ser uma URL http/https.'];
            }
        }

        if($exigirAutorExistente){
            if(!is_numeric($autoresId) || (int) $autoresId <= 0){
                $erros[] = ['campo' => 'AUTORES_ID', 'mensagem' => 'Selecione um autor válido ou escolha cadastrar um novo autor.'];
            } else if(!MangasDAO::AutorExiste((int) $autoresId)){
                $erros[] = ['campo' => 'AUTORES_ID', 'mensagem' => 'O autor selecionado não existe mais no banco. Cadastre um novo autor ou escolha outro.'];
            }
        }

        return $erros;
    }

    private static function ValidarAutorRequest(array $request, bool $camposDoFormularioManga = false): array {
        $erros = [];
        $permitidos = ['NOME', 'BIOGRAFIA', 'DATA_NASCIMENTO', 'NACIONALIDADE'];
        $mapaCampo = $camposDoFormularioManga ? [
            'NOME' => 'NOVO_AUTOR_NOME',
            'BIOGRAFIA' => 'NOVO_AUTOR_BIOGRAFIA',
            'DATA_NASCIMENTO' => 'NOVO_AUTOR_DATA_NASCIMENTO',
            'NACIONALIDADE' => 'NOVO_AUTOR_NACIONALIDADE',
        ] : [
            'NOME' => 'NOME',
            'BIOGRAFIA' => 'BIOGRAFIA',
            'DATA_NASCIMENTO' => 'DATA_NASCIMENTO',
            'NACIONALIDADE' => 'NACIONALIDADE',
        ];

        foreach($request as $campo => $valor){
            if(!in_array($campo, $permitidos, true)){
                $erros[] = ['campo' => $campo, 'mensagem' => "O campo $campo não é aceito no cadastro de autor."];
            }
        }

        $nome = self::Str($request['NOME'] ?? '');
        $biografia = self::Str($request['BIOGRAFIA'] ?? '');
        $dataNascimento = self::Str($request['DATA_NASCIMENTO'] ?? '');
        $nacionalidade = self::Str($request['NACIONALIDADE'] ?? '');

        if(mb_strlen($nome) < 2){
            $erros[] = ['campo' => $mapaCampo['NOME'], 'mensagem' => 'O nome do autor precisa ter pelo menos 2 caracteres.'];
        }
        if(mb_strlen($nome) > 200){
            $erros[] = ['campo' => $mapaCampo['NOME'], 'mensagem' => 'O nome do autor não pode passar de 200 caracteres.'];
        }
        if(mb_strlen($nacionalidade) > 100){
            $erros[] = ['campo' => $mapaCampo['NACIONALIDADE'], 'mensagem' => 'A nacionalidade não pode passar de 100 caracteres.'];
        }
        if($dataNascimento !== '' && !self::DataReal($dataNascimento)){
            $erros[] = ['campo' => $mapaCampo['DATA_NASCIMENTO'], 'mensagem' => 'A data de nascimento do autor precisa ser uma data real no formato AAAA-MM-DD.'];
        }
        if(mb_strlen($biografia) > 2000){
            $erros[] = ['campo' => $mapaCampo['BIOGRAFIA'], 'mensagem' => 'A biografia não pode passar de 2000 caracteres.'];
        }

        return $erros;
    }

    private static function CriarModelManga(array $request, int $autoresId): Mangas {
        $Mangas = new Mangas();
        $Mangas->AlterarTitulo(self::Str($request['TITULO']));
        $Mangas->AlterarAutoresId($autoresId);
        $Mangas->AlterarDataPublicacao(self::Str($request['DATA_PUBLICACAO']));
        $Mangas->AlterarSinopse(self::Str($request['SINOPSE'] ?? ''));
        $Mangas->AlterarTipo(self::Str($request['TIPO']));
        $Mangas->AlterarStatus(self::Str($request['STATUS']));
        $Mangas->AlterarCapaUrl(self::Str($request['CAPA_URL']));
        return $Mangas;
    }

    private static function CriarModelAutor(array $request): Autores {
        $Autores = new Autores();
        $Autores->AlterarNome(self::Str($request['NOME']));
        $Autores->AlterarBiografia(self::Str($request['BIOGRAFIA'] ?? '') ?: null);
        $Autores->AlterarDataNascimento(self::Str($request['DATA_NASCIMENTO'] ?? '') ?: null);
        $Autores->AlterarNacionalidade(self::Str($request['NACIONALIDADE'] ?? '') ?: null);
        return $Autores;
    }

    private static function Str($valor): string {
        return trim((string) $valor);
    }

    private static function DataReal(string $valor): bool {
        if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $valor)) return false;
        [$ano, $mes, $dia] = array_map('intval', explode('-', $valor));
        return checkdate($mes, $dia, $ano);
    }
}
?>
