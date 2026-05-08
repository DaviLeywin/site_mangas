<?php
require_once __DIR__ . '\..\validator\class.GenerosMangasValidator.php';
require_once __DIR__ . '\..\validator\class.BaseValidator.php';


class GenerosMangas {
    public int $MANGAS_ID;
    public int $GENEROS_ID;
    
    function AlterarMangasId(string $MangasId){
        if(!BaseValidator::ValidarPositivoInteiro($MangasId)){
            throw new InvalidArgumentException("MANGAS_ID tem que ser inteiro positivo");
        }
        $this->MANGAS_ID = $MangasId;
    }
    
    function AlterarGenerosId(string $GenerosId){
        if(!BaseValidator::ValidarPositivoInteiro($GenerosId)){
            throw new InvalidArgumentException("GENEROOS_ID tem que ser inteiro positivo");
        }
        $this->GENEROS_ID = $GenerosId;
    }

    function GetMangasId(){
        return $this->MANGAS_ID;
    }

    function GetGenerosId(){
        return $this->GENEROS_ID;
    }
}
?>