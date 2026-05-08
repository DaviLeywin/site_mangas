<?php
require_once "api\banco\class.Banco.php";

set_exception_handler(function (Throwable $e) {

    if (!headers_sent()) header('Content-Type: application/json; charset=utf-8');

    $code = 500;
    $tipo = 'ERRO_INTERNO';
    $mensagem = 'Erro inesperado';

    if ($e instanceof TypeError || $e instanceof ValueError) {
        $code = 400;
        $tipo = 'ERRO_DE_TIPO';
        $mensagem = 'Tipo de dado inválido';
    }
    elseif ($e instanceof InvalidArgumentException || $e instanceof DomainException) {
        $code = 422;
        $tipo = 'ERRO_DE_REGRA';
        $mensagem = $e->getMessage();
    }
    elseif ($e instanceof PDOException || $e instanceof DatabaseException) {
        $code = 500;
        $tipo = 'ERRO_DE_BANCO';
        $mensagem = 'Erro ao acessar o banco de dados';
    }
    elseif ($e instanceof LogicException) {
        $code = 500;
        $tipo = 'ERRO_DE_LOGICA';
        $mensagem = 'Erro interno de lógica';
    }
    elseif ($e instanceof Error) {
        $code = 500;
        $tipo = 'ERRO_DE_EXECUCAO';
        $mensagem = 'Erro interno de execução';
    }

    http_response_code($code);

    echo json_encode([
        'erro' => true,
        'tipo' => $tipo,
        'mensagem' => $mensagem,
        'detalhes' => [
            'mensagem' => $e->getMessage(),
            'arquivo' => $e->getFile(),
            'linha' => $e->getLine(),
        ]
    ], JSON_PRETTY_PRINT);

    exit;
});

enum Ordem: string {
    case ASC = 'ASC';
    case DESC = 'DESC';
}

class DAO {
    public $tipo;
    public $pdo;
    public $tabela;
    public $groupBy;
    public $orderBy;
    public $wheres = [];
    public $dados = [];
    public $dadosGet = [];


    public function init(){
        $this->pdo = Banco::BuscarConexao();
        if (!$this->pdo) throw new InvalidArgumentException("me matei n deu!");
        return $this->pdo;
    }


    public static function __callStatic($metodo,$params){
        $valido = ['get','put','post','delete','describe'];
        $metodo = strtolower($metodo);
        $inst = new self();
        if(in_array($metodo,$valido)){
            $inst->tipo = $metodo;    
            foreach($params as $param){
                if($param)$inst->dadosGet = $params;    
            }
            return $inst;
        }else {  
            throw new InvalidArgumentException("Nenhum metodo $metodo encontrado!");
        } 
    }
    
    public function Table(string $tabela){
        $pdo = $this->init();
        global $ConnB;
        $banco = $GLOBALS['conn']["banco"];
        $tabela = strtolower($tabela);
        $SqlValTabela = 'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = :banco AND table_name = :tabela';
        $stmt = $this->pdo->prepare($SqlValTabela);
        $stmt->execute([':banco' =>$banco,  ':tabela' =>$tabela, ]);
        if(!(bool) $stmt->fetchColumn()){
            throw new InvalidArgumentException("Tabela $tabela nao existe no banco $banco");
        }
        $this->tabela = $tabela;
        return $this;
    }

    public function OrderBy(string $campo = null, Ordem $tipo = Ordem::ASC){
        $campo = strtolower($campo);
        if (!preg_match('/^[a-z0-9_]+$/', $campo)) throw new InvalidArgumentException("Order by: aceita apenas um campo válido");
        $this->orderBy = " ORDER BY $campo {$tipo->value};";
        return $this;
    }

    public function GroupBy($groupBy = null){
        $groupBy = strtolower($groupBy);
        if(!preg_match('/^[a-z0-9_]+$/',$groupBy))throw new InvalidArgumentException("Group by deve conter uma string/campo!");
        $this->groupBy = " GROUP BY $groupBy;";
        return $this;
    }

    public function Dados(object $dados = null){
        if(empty($dados)) throw new InvalidArgumentException("Dados nao podem estar vazios!");
        $dados = (array) $dados;
        foreach($dados as $nome => $value){
            $this->dados[$nome] = $value;
        }
        return $this;
    }

    public function Where(array $dados = []){
        if(empty($dados)) throw new InvalidArgumentException("Dados do where nao pode estar vazio!");
        foreach($dados as $index => $value){
            $this->wheres[$index] = $value;
        }
        return $this;
    }

    function CriarGet(){
        $sql = "SELECT * FROM ".$this->tabela;
        $sql .=  $this->CriarWhere($this->wheres);
        $stmt = $this->CriarBind($sql, $this->wheres);
        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($resultado) return Response::Success("Dados encontrados com sucesso!",$resultado);
        return Response::Fail("Nenhum registro encontrado!");
    }

    static function CriarWhere($wheres){
        if(empty($wheres))return "";
        $array = [];
        foreach($wheres as $index => $value){
            $index = strtoupper($index);
            $array[] = " $index = :$index ";
        }
        $sql = ' WHERE'. implode("and",$array);
        return $sql;
    }

    static function CriarBind($sql, $wheres){
        $pdo = Banco::BuscarConexao();
        $stmt = $pdo->prepare($sql);
        if(empty($wheres))return $stmt;
        foreach($wheres as $index => $value){
            $index = strtoupper($index);
            $stmt->BindValue(":$index",$value);
        }
        return $stmt;
    }

    function CriarDelete(){
        if(empty($this->wheres['id'])) throw new InvalidArgumentException("Dados do where nao pode estar vazio!");
        $sql = "DELETE FROM ".$this->tabela." WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":id",$this->wheres['id'],PDO::PARAM_INT);
        $stmt->execute();
        if($stmt->rowCount() > 0) return Response::Success("Dados apagados com sucesso!");
        return Response::Fail("Falha ao apagar os dados!");
    }

    function CriarDescribe(){
        $sql = "DESCRIBE ".$this->tabela.";";
        $stmt = $this->pdo->query($sql);
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($resultado) return Response::Success("Descricao encontrada com sucesso!",$resultado);
        return Response::Success("Falha ao buscar descricao!");
    }

    function GetById($tabela, $pdo, $id = null){
        $id = !is_null($id) ? $id : $pdo->lastInsertId();
        $sql = "SELECT * FROM $tabela WHERE ID = :ID;";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":ID",$id);
        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $resultado;
    }

    function CriarPost(){
        $dados = $this->dados;
        if(empty($dados)) throw new InvalidArgumentException("Campo dados e obrigatorio para tipo post!");
        $camposArray = array_keys($dados);
        $campos = implode(',', $camposArray);
        $placeholders = ':' . implode(',:', $camposArray);
        $tabela = $this->tabela;
        $pdo = $this->pdo;
        $sql = "INSERT INTO $tabela ($campos) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        foreach($dados as $campo => $valor){
            $stmt->bindValue(":$campo", $valor);
        }
        $stmt->execute();
        $resultado = $this->GetById($tabela, $pdo);
        if($resultado) return Response::Success("Insercao feita com sucesso!",$resultado);
        return Response::Fail("Falha ao inserir dados!");

    }

    function CriarPut(){
        $dados = $this->dados;
        if(is_null($dados)) throw new InvalidArgumentException("Campo dados e obrigatorio para tipo post!");
        if(empty($this->wheres)) throw new InvalidArgumentException("Campo where e obrigatorio para tipo post!");
        $tabela = $this->tabela;
        $set = [];
        foreach($dados as $index => $valor){  $set[] = " $index = :$index";}
        $sql = "UPDATE $tabela SET " . implode(",",$set) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        foreach($dados as $index => $value){ $stmt->BindValue(":$index",$value);}
        $stmt->BindParam(":id",$this->wheres['id']);
        $stmt->execute();
        $resultado = $this->GetById($tabela, $this->pdo, $this->wheres['id']);
        if($resultado) return Response::Success("atualizacao feita com sucesso!",$resultado);
        return Response::Fail("registro nao encontrado para atualizar!");
    }


    public function Execute(){
        if(is_null($this->tabela)) throw new InvalidArgumentException("Campo tabela e obrigatório!");
        if(is_null($this->tipo)) throw new InvalidArgumentException("Campo tipo e obrigatório!");
        return match($this->tipo){
            'put' => $this->CriarPut(),
            'delete' => $this->CriarDelete(),
            'get' => $this->CriarGet(),
            'post' => $this->CriarPost(),
            'describe' => $this->CriarDescribe(),
        };
    }

}

class Documento {
    public $documento;
    public $caminho;
    public $criar;
    public $tipo;
    
    static function Local(string $local = ""){
        $caminho = str_ireplace("/","\\",__DIR__ ."\\". $local);
        if(!file_exists($caminho)) throw new InvalidArgumentException("Caminho inexistente!");   
        $inst = new self();
        $inst->caminho = $caminho;
        return $inst;
    }
    
    function Pastas(array $pastas):self{
        if(empty($pastas)) throw new InvalidArgumentException("Campo obrigatorio faltando em Pastas!");
        $this->criar = 'PASTAS';
        $this->documento = $pastas;
        return $this;
    }

    function Arquivos(array $arquivos):self{
        if(empty($arquivos)) throw new InvalidArgumentException("Campo obrigatorio faltando em Arquivos!");
        $this->criar = 'ARQUIVOS';
        $this->documento = $arquivos;
        return $this;
    }

    function Tipo(string $tipo){
        if(empty($tipo)) throw new InvalidArgumentException("Campo tipo nao pode estar vazio!");   
        if(in_array(strtolower($tipo), ['model','htaccess','dao','controller','service','banco','rota','request','response','index'])){
            $this->tipo = strtolower($tipo);
            return $this;
        }
        throw new InvalidArgumentException("Tipo invalido! tipos validos: service, daoe controller.");
    }

    function CriarPastas(){
        $erro = [];
        foreach($this->documento as $pasta){
            if(file_exists($this->caminho.DIRECTORY_SEPARATOR . $pasta)){
                $erro[] = "Pasta $pasta ja existe nesse local!";
            }else if(!file_exists($this->caminho.DIRECTORY_SEPARATOR . $pasta)){
                mkdir($this->caminho.DIRECTORY_SEPARATOR . $pasta);
                $erro[] = "Pasta $pasta criada com sucesso!";
            }
        }
        return $erro;
    }

    function CriarArquivos(){
        $dados = [];
        foreach($this->documento as $arquivo1){
            if(file_exists($this->caminho.DIRECTORY_SEPARATOR . $arquivo1)){
                $dados[] = "Arquivo $arquivo1 ja existe nesse local!";
                continue;
            }
            else if($this->tipo){
                if($this->tipo == 'controller'){
                    $arquivo = "class." . $arquivo1 . "Controller.php";
                    $arquivo = $this->caminho . DIRECTORY_SEPARATOR . $arquivo; 
                    $texto = $this->ArquivosDados('controller',$arquivo1);
                    $arquivo = fopen($arquivo,"w");
                    fwrite($arquivo,$texto);
                }
                else if($this->tipo == 'dao'){
                    $arquivo = "class." . $arquivo1 . "DAO.php";
                    $arquivo = $this->caminho . DIRECTORY_SEPARATOR . $arquivo; 
                    $texto = $this->ArquivosDados('dao',$arquivo1);
                    $arquivo = fopen($arquivo,"w");
                    fwrite($arquivo,$texto);
                }
                else if($this->tipo == 'service'){
                    $arquivo = "class." . $arquivo1 . "Service.php";
                    $arquivo = $this->caminho . DIRECTORY_SEPARATOR . $arquivo;
                    $texto = $this->ArquivosDados('service',$arquivo1);
                    $arquivo = fopen($arquivo,"w");
                    fwrite($arquivo,$texto);
                }
                else if($this->tipo == 'model'){
                    $arquivo = "class." . $arquivo1 . "Model.php";
                    $arquivo = $this->caminho . DIRECTORY_SEPARATOR . $arquivo;
                    $texto = $this->ArquivosDados('model',$arquivo1);
                    $arquivo = fopen($arquivo,"w");
                    fwrite($arquivo,$texto);
                }else if(in_array($this->tipo , ['htaccess','banco','rota','request','response','index'])){
                    $arquivo = $this->caminho . DIRECTORY_SEPARATOR . $arquivo1;
                    $texto = $this->ArquivosDados($this->tipo,$arquivo1);
                    $arquivo = fopen($arquivo,"w");
                    fwrite($arquivo,$texto);
                }
                $dados[] = "Arquivo $arquivo1 criado com sucesso!";
            }
            else if(!isset($this->tipo)){
                $arquivo = $this->caminho . DIRECTORY_SEPARATOR . $arquivo1; 
                fopen($arquivo,"w");
                $dados[] = "Arquivo $arquivo1 criado com sucesso!";
            }

        }
        return $dados;
    }

    function Exec(){
        if(empty($this->criar)) throw new InvalidArgumentException("Nenhum documento definido para criacao!");
        if(empty($this->caminho)) throw new InvalidArgumentException("Caminho nao definido para criacao!");
        if(empty($this->documento)) throw new InvalidArgumentException("Nenhum documento definido para criacao!");
        if($this->criar == 'ARQUIVOS'){
            return $this->CriarArquivos();
        }
        else if($this->criar == 'PASTAS'){
            return $this->CriarPastas();
        }
    }

    function ArquivosDados(string $tipo, string $arquivo):string{
        $relacoes = [
            'controller' => "eJzFkstOwzAQRfeR8g+D1UUiQT6g4SFBBGIFKt0gUlkmnQpLxg5+VAKUfyep0+Ii+lhQMRvL9szcc8c+vahfaogjjW+Oa6RKVgiUFrcjSiEDUmZZaVDPeYVlJZgx2Zg9o2AP/ixrq0keR3G0uAR/eaWk1UoI1PAZR9CGsczyCmZOVpYrCTdox2qqTDLohNHYYxg4LdI+vQuN1mkJa3LD4aowzX1q45eNMlsUuv0T4VMygTNIuLRpcJTvQZIsOi5BPMbvIPfKbCPhswRfa/u+Sjk5v7wrHtMwJyAZoamVNC3ENeMiIQVrJwJz9sGVOSJpgN7scrEG1qt+T3aDGfcXU/1Hz+6n5d7FPi9ZoECLB/tVy/YhThw1X7SwDeo=",
            'dao' => 'eJyVk8FLwzAUxu+F/g8h9JDCRu9TpsOKeNLDxHPWvrlCSEqSojD6v/vSxNlqu245hfL93vde3tfbu/pQx1EcFYIbQ7Z8B4LnmxdyjCOCx1huq4LsG1nYSknyBHarSmVYGgTuaLCNlgSx1QoFLF2usZAARn09ih8ev6BoLLD0hvSOr9GebiN2LPk8gIZr/d4d9MP27YPluN+rMpb5IiQpOQ465dspx4xzR7EAX2ycgwALM7MG0VXj9p95Yubmz8iL8104/QWDn+to5ilMoasdTEbsVzCXs5NP6yKeaGW5Wa4/MDI0c0nuqOxYlS1dhBoPSlqthAB9jwrq1vaP2wgRDMep7g8ZoLVLC82epQFtp1kXqgFX+o3TzK9+pmEvGho3zvetLvksjXtFFN/sG5ZhOjg=',
            'service' => 'eJztVU1PwkAQvZPwH9aGAySkP6BoDIoaD6JB4omkGdohbLLs1tktagz/3S0LtHwINXDwwF7aTN+8eTudt3t5nYwTVq0QvqecMFQyQhaGncdeGDKfeQPfH0xUjGIQCdDa78MQBTxlEd9meq39qTGotcRO+7lM2hQEj8EoWiTfgMa3ZWxJUK3MPzLH/Io05Zbpu1phdmkDhkdslMrIcCXZA5q+ipWuNxaAbBGalCRbSQuCHNZysJl7/Epar6UkDnI6VE65m+5FaYvM+oLaFDlrMeqIeASKXRWZO/PwEOdqC3BCnVgusOi1zgXBLUwS9aqGBDJWq1rNQoWlymzxUX3F1VjurJcFpMYguAcu6t6cUjP8NAT6wmvmGQWmUuLcK3WV6aZCnEbdHZFitm9uoohFTq20IZkKdRLBfZ6oNhF87ZTc9Nwf847WbmwdZodzsYlTKIcJyPEe8cdrdhX+JNv1y4qW+LEY940J3zaZ844LHDZaaoq73XBwKbedvfavvdYsHrhnw+2XvcNN2wYpcx92UKDBEldiEZgTz34AQFOhng==',
            'banco' => 'eJztVt9v2jAQfkfif/AsHoJE6esUBlUg2YREC0vyUKmqImMOiAQ22I7aauJ/r+NEKAkprTZW7WERUsj98Hd339nnbze79a7ZELBPYgERZxRQFLljP4pQF+HrbveacraMV/mrq81xr9loNuiGSImGhFGOfjUbSD9SERVT1NoteK8skftNRRIzCUJVhGsuqyIJbE0qskQmRMRVjHkaigmtIF0mjKqYMxSzWFntPFBjAJulbRtI1EetH5Pp0JkED1jnyfDjA04V+LF34mACqvUwmjqXPN5ap1xX52YSqnUymtrg9puSg/6uM8uKX7LMREfjw1t1HImYCMN6TTWzIhfwlHgpGKVPK81BIzN4QjN3auHtiw7STqvdx90CKV3co2siJKh+opZfcadczE6JjyLmEeVqoJ0dpUQ8TxRYGs22nTD0I8/3b6eu10FGlH9F3v3Im4Xj6V27V1yqsnBWYTzyPSf0kOuEztAJPDT+ju6mIfLux0EYoGMihiidCa6PD56BWumS1fgFqEQw5IPccU2MbQcJpSClhbMNRzULC/3iWySTVMG/4OIaB0oUXVveM4Wdoa0F7QoRai34k6HBJYrMiYSjtYU9ITgiGY4wMByZXGydWwuuBitQtxqWrMBql4DfaR8XNqD+NxDCrj+dldrns1qH7MjqI71z0hW/20MG8KJNNEwkJWLEGTyTT+iixZyRLfTLzPwb3ZSTbdb/G9tfl5iqy5JnBkiw39QNY31x0JxkHxWSz7K55AKITrgwBYk0W62adhnszDY6nEXIB2gKkv39GE5u+zbUZc/t4OfkT9nygZ4b+OXj/OSiUbws1CtNIxRU751eAs6NPp2G/t0MXgH8C/1r',
            'rota' => 'eJylVe9O2zAQ/47EOxgTiUTLmiFtXyiFsZFtaCBYWpBQCJZJr220NMkch3Wq+jR7lL3Yzk5S0jaID1iVGt+/39n3u/PhcTbJtrcE/CoiAYSGMc/zjodbyGUHdbS71LI0CZVJmkiRxjEIp7Q+KeTk81JYO21vaS3xUsnz+fYWwZUVD3EUEkMombZR0lGRhDJKE8IYxs6lKEJpWnNiyEmUvz3SxqRH/KBLFhs+Y5CmUYjYJgYPeUpsI0MHGEdDjj4jHudgzatIfDhs2tqEfnUHtOlidRdr8bM0fw3A1WX/RYTiVQDXL8UfQgwSXgFx6p67A3cTZQ1nw9GYgkyHiEYacEscXVY/QPwwnWYcS07RmdpUOeNf6YwfS1/ahgozCAvJBRKm1KhliJLAGDyB36Sis2l1GyZVcj1isL7r3bieTz33x7XbH7ALd/Dt8pQGTXPM7RPPAe2p048kXPBkzB2eRXTNCi2Qwgz7JYt5WN678rQptTehrr0zGjTzchwiQBYi0cE2Y5d/KgtKjsvdARFSRNPy8lHeDDdKBfBwYq70Ev50CzavTK1oZGq5X19+QHZ69U1ZRDV+lBTQfc7rqVLBemh9gkceo1IRsEdWh8bBwY3WiUH6E5KVQjVhlgF8CkKk7TBqTaTMmIA8w3kCOFWGYL5/t98WVq36wpfhn7GDWSRbVItV0doWuTiGmeLN7j3tZLhjFTVYyOP4gYc/TTTbc+7mpn9/twjeWHcLZw+7pua4aUxxGlZJUvP46pB2jKm/H3TokX/voAPtLlSXlVVQXRRYHWrs0s1Kafwpl4oSOjGbVD2bccGx0iLNrdbiPenxLFwI/oeNoliCMBsqe4TZhhP+CBbpHZGdKGdRImuRfeJ5J7fsy9n5wPXYdd9l393btqL4xtMjY1f8U5MCZlmsakk/Yi+Vp9XTImgLsvpqdZyNd6sBUj9azzBDFYoVOQimasL06c3WJG1SFkCdvnkvtJpIpaLatKbdQrIGoxqfrRz/0AxZZV81i0LGtxXUaE1yPoapFlH1QJPk39+UQKJPxLGB7Xr4ISD+/gNl+1Wu',
            'response' => 'eJzVkb9OwzAQh/dKfYer1cGR+galrRhAYmCBsYqQ6zqJkWNHPptSAe/OOcq/AiMDnKIokX/58t3d1a6pmvlMGoEIDwobZ1HB23wGVBhE0BKKaGXQzsJjlFIhcgxe2xKWtbIoSlXDBmw0ZgXCe3F+d4dnJQMsfcIRg473edYxU+mCa3xKn/CBkU0DqULl3QmsOsGdfRFGH699GSkdbl6lapIPZ/e9wFEBxuTmwB28LkVwXosFy9Yj9GN8JDUKWJfMLv/KOgyDzRaCj2r15bz3bQOD/SSVry8aXai6CWc+DONbn73LnvURlpMXb2eZjVOcdtLex3ciRG8HUpekfn9e463Q5m/usBCmEr+5QQLi/14hXbvtJ+SFBcY=',
            'request' => 'eJxtkV9LwzAUxd8H+w53ow8NVPY+rUNRfJyoLyISYna7RbI05g8ypN/dpO3W0O2+JNx7cvLLyc1K7/R0wiWzFl7wx6N18DedQCjtv6TgANmdlFDCx+f1qH+/fni/OHh6fLvYf16/ngbdqPKKO1EroJTXyjrjuctJDxArczthr247x4zGdbWEwTvR9O4ZbTcnVaIz7DcIKiGRbtHFGx0qZ/N5CGG5WAilvZuT1PnbBrYS4kI3yOsN5tGlgECKqVJUkHfqWVmC8lISSJ6RYPapteLEoEFp8fwI7nUa5bE0MxZpyKvHaYVkJIpMs9B3h7ybkxFCbKYIZ5F2P8+MYQe6R7MNrx+yLoa/KRLjI0UTo2/+AcTPm20=',
            "htaccess" => "eJwLSi0vyixJdc1Lz8xLVfDP4+UKgog45+elKKhWB7kGhroGh8S7efq4+jn6utYqKOqmEaMoBa4oqDQnVSFOQ09LU0UhMy8ltUKvIKNAITow2FHHJxYAgOUogQ==",
            'model' => 'eJyVjcEKwjAMhu+DvUMOg+nBvsBkY04QD6KIeBqUrlZXqG1tOy/iu7uuThBP/gmBJH++zAvd6jgy7NZxw7CSlAHGy/UeY0CQ1gjVdyL4iThlaiqItehAGibIcZyiHpBm/yEWxLIfgI9hDeEDPOIIeumuEZyCdYbLCySUXLXKwirUcyep40pCKRwzxFTeMRn9Qzd9s57+ydfNirngHy1ehrnOSEhcy+0sr8rNbpt9AH0W+QtDN1+y',
            "index" => "eJx9jD0LwjAURfdC/0MoDslgwLlWlyK4di6EkF5o/UjieynVf29bcXGQO95zzv4Y+5hnHdzNEiQnGlwy6RXB1U6VeUZ4jAPBBO8gjKnPjTFCi6LVuj2RvWMKdNVzpPiFiznJrJuQLP8FwDF4xpfJsw0tjqiExyRWX6r1WAbXB3Hh4A28Cx3kh94e8IQbkyWpVPkGSyZGUw==",
        ];
        
        if(in_array($tipo, ['controller','dao','service','model'])){
            $str = $relacoes[$tipo];
            $original = gzuncompress(base64_decode($str));
            $original = str_replace("Tabela",$arquivo,$original);
            return $original;
        }
        else if(in_array($tipo, ['htaccess','banco','rota','request','response','index'])){
            $str = $relacoes[$tipo];
            $original = gzuncompress(base64_decode($str));
            return $original;
        }
    }

}
// print_r(DAO::GetPica(['tipo'=>'manga',]));
?>