<?php 
require_once __DIR__ . "\..\model\class.AutoresModel.php";
require_once __DIR__ . "\..\dao\class.AutoresDAO.php";
require_once __DIR__ . '\..\validator\class.BaseValidator.php';

class AutoresService {
    static function GetTodos(){
        return AutoresDAO::GetTodos();
    }
    
    static function Get($url){
        return AutoresDAO::Get($url);
    }

    static function Post($request){
        $erros = self::ValidarAutorRequest($request);
        if($erros) return Response::Fail("Não foi possível cadastrar o autor. Corrija os campos destacados.", $erros);

        $Autores = self::CriarModelAutor($request);
        return AutoresDAO::Post($Autores);
    }

    static function Put($request, $url){
        $erros = self::ValidarAutorRequest($request);
        if($erros) return Response::Fail("Não foi possível atualizar o autor. Corrija os campos destacados.", $erros);

        $Autores = self::CriarModelAutor($request);
        return AutoresDAO::Put($Autores, $url);
    }
    
    static function Delete($url){
        return AutoresDAO::Delete($url);
    }

    private static function ValidarAutorRequest(array $request): array {
        $erros = [];
        $permitidos = ['NOME', 'BIOGRAFIA', 'DATA_NASCIMENTO', 'NACIONALIDADE'];

        foreach($request as $campo => $valor){
            if(!in_array($campo, $permitidos, true)){
                $erros[] = ['campo' => $campo, 'mensagem' => "O campo $campo não é aceito no cadastro de autor."];
            }
        }

        $nome = self::Str($request['NOME'] ?? '');
        $biografia = self::Str($request['BIOGRAFIA'] ?? '');
        $dataNascimento = self::Str($request['DATA_NASCIMENTO'] ?? '');
        $nacionalidade = self::Str($request['NACIONALIDADE'] ?? '');

        if(mb_strlen($nome) < 2) $erros[] = ['campo' => 'NOME', 'mensagem' => 'O nome do autor precisa ter pelo menos 2 caracteres.'];
        if(mb_strlen($nome) > 200) $erros[] = ['campo' => 'NOME', 'mensagem' => 'O nome do autor não pode passar de 200 caracteres.'];
        if(mb_strlen($nacionalidade) > 100) $erros[] = ['campo' => 'NACIONALIDADE', 'mensagem' => 'A nacionalidade não pode passar de 100 caracteres.'];
        if($dataNascimento !== '' && !self::DataReal($dataNascimento)) $erros[] = ['campo' => 'DATA_NASCIMENTO', 'mensagem' => 'A data de nascimento precisa ser uma data real no formato AAAA-MM-DD.'];
        if(mb_strlen($biografia) > 2000) $erros[] = ['campo' => 'BIOGRAFIA', 'mensagem' => 'A biografia não pode passar de 2000 caracteres.'];

        return $erros;
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
