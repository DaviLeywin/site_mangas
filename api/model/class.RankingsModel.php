<?php
require_once __DIR__ . '\..\validator\class.RankingsValidator.php';
require_once __DIR__ . '\..\validator\class.BaseValidator.php';


class Rankings {
    const POPULARIDADE = "POPULARIDADE";
    const AVALIACAO = "AVALIACAO";
    const VENDAS = "VENDAS";
    public int $MANGAS_ID;
    public string $TIPO_RANKING;
    public int $POSICAO;
    public string $DATA_RANKING;
    
    function AlterarMangaID(int $MangasId){
        if(!BaseValidator::ValidarPositivoInteiro($MangasId)){
            throw new InvalidArgumentException("MANGAS_ID tem que ser inteiro positivo");
        }
        $this->MANGAS_ID = $MangasId;
    }
    
    function AlterarTipoRanking(string $TipoRanking){
        if(!RankingsValidator::ValidarTipo($TipoRanking)){
            throw new InvalidArgumentException("Tipo de ranking invalido, tipos validos:".self::POPULARIDADE.self::AVALIACAO.self::VENDAS);
        }
        $this->TIPO_RANKING = $TipoRanking;
    }
    
    function AlterarPosicao(int $Posicao){
        if(!BaseValidator::ValidarPositivoInteiro($Posicao)){
            throw new InvalidArgumentException("Posicao tem que ser um numero inteiro positivo maior que zero!");
        }
        $this->POSICAO = $Posicao;
    }
    
    function AlterarDataRanking(string $DataRanking){
        if(!BaseValidator::ValidarData($DataRanking)){
            throw new InvalidArgumentException("Data invalida!");
        }
        $this->DATA_RANKING = $DataRanking;
    }

    function GetMangaID(){
        return $this->MANGAS_ID;
    }
    function GetTipoRanking(){
        return $this->TIPO_RANKING;
    }
    function GetPosicao(){
        return $this->POSICAO;
    }
    function GetDataRanking(){
        return $this->DATA_RANKING;
    }
}
?>