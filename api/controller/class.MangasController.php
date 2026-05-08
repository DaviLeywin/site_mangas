<?php 
require_once __DIR__ . "\..\service\class.MangasService.php";

class MangasController {
    static function GetTodos($request, $url){
        return MangasService::GetTodos();
    }

    static function GetMangasGeneros($request, $url){
        return MangasService::GetMangasGeneros();
    }

    static function GetMangaGeneroAutorCapitulos($request, $url){
        return MangasService::GetMangaGeneroAutorCapitulos($url);
    }
    
    static function Get($request, $url){
        $url["id"] = (int) $url["id"];
        return MangasService::Get($url);
    }    

    static function Post($request, $url){
        if(empty($request->BODY)){
            return Response::Fail("Dados vazios!");
        }
        return MangasService::Post($request->BODY);
    }


    static function PostComAutor($request, $url){
        if(empty($request->BODY)){
            return Response::Fail("Dados vazios!");
        }
        return MangasService::PostComAutor($request->BODY);
    }

    static function Put($request, $url){
        $url["id"] = (int) $url["id"];
        if(empty($request->BODY)){
            return Response::Fail("Dados vazios!");
        }
        return MangasService::Put($request->BODY, $url);
    }    


    static function PutComAutor($request, $url){
        $url["id"] = (int) $url["id"];
        if(empty($request->BODY)){
            return Response::Fail("Dados vazios!");
        }
        return MangasService::PutComAutor($request->BODY, $url);
    }    

    static function UploadImagem($request, $url){
        if(empty($_FILES['capa'])){
            return Response::Fail("Nenhuma imagem foi enviada!", [["campo" => "CAPA_ARQUIVO", "mensagem" => "Selecione uma imagem de capa antes de salvar."]]);
        }

        $arquivo = $_FILES['capa'];

        if(!empty($arquivo['error'])){
            return Response::Fail("Erro ao receber a imagem enviada.", [["campo" => "CAPA_ARQUIVO", "mensagem" => "O navegador não conseguiu enviar esse arquivo. Tente escolher a imagem novamente."]]);
        }

        if($arquivo['size'] > 5 * 1024 * 1024){
            return Response::Fail("A imagem não pode passar de 5MB.", [["campo" => "CAPA_ARQUIVO", "mensagem" => "Escolha uma imagem menor que 5MB."]]);
        }

        $tiposPermitidos = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ];

        $mime = mime_content_type($arquivo['tmp_name']);
        if(!isset($tiposPermitidos[$mime]) || !getimagesize($arquivo['tmp_name'])){
            return Response::Fail("Formato inválido. Use PNG, JPG, JPEG, WEBP ou GIF.", [["campo" => "CAPA_ARQUIVO", "mensagem" => "A capa precisa ser PNG, JPG, JPEG, WEBP ou GIF."]]);
        }

        $pastaDestino = realpath(__DIR__ . '/../../public/assets/imagens');
        if(!$pastaDestino){
            return Response::Fail("A pasta de imagens não foi encontrada.", [["campo" => "CAPA_ARQUIVO", "mensagem" => "Crie ou restaure a pasta public/assets/imagens no projeto."]]);
        }

        $nomeOriginal = pathinfo($arquivo['name'], PATHINFO_FILENAME);
        $nomeLimpo = preg_replace('/[^a-zA-Z0-9_-]+/', '-', strtolower($nomeOriginal));
        $nomeLimpo = trim($nomeLimpo, '-');
        if($nomeLimpo === '') $nomeLimpo = 'capa-manga';

        $extensao = $tiposPermitidos[$mime];
        $nomeArquivo = $nomeLimpo . '-' . uniqid() . '.' . $extensao;
        $destino = $pastaDestino . DIRECTORY_SEPARATOR . $nomeArquivo;

        if(!move_uploaded_file($arquivo['tmp_name'], $destino)){
            return Response::Fail("Não foi possível salvar a imagem na pasta do projeto.", [["campo" => "CAPA_ARQUIVO", "mensagem" => "Verifique a permissão da pasta public/assets/imagens."]]);
        }

        return Response::Success("Imagem enviada com sucesso!", [
            'CAPA_URL' => 'imagens/' . $nomeArquivo,
        ]);
    }

    static function Delete($request, $url){
        $url["id"] = (int) $url["id"];
        return MangasService::Delete($url);
    }
}