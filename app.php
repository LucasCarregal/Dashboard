<?php

class Dashboard {

    public $data_inicio;
    public $data_fim;

    public $numeroVendas;
    public $totalVendas;

    public $clientesAtivos;
    public $clientesInativos;

    public $totalReclamacoes;
    public $totalElogios;
    public $totalSugestoes;

    public $totalDespesas;

    public function __get($atr){
        return $this->$atr;
    }

    public function __set($atr, $val){
        $this->$atr = $val;
        return $this;
    }
}

class Conexao {

    private $host = 'localhost';
    private $dbname = 'db_dashboard';
    private $user = 'root';
    private $pass = '';

    public function conectar(){
        try{
            $conexao = new PDO (
                "mysql:host=$this->host;dbname=$this->dbname",
                "$this->user",
                "$this->pass"
                );

            $conexao->exec('set charset utf8');

            return $conexao;

        } catch (PDOException $e) {
            // echo '<p>'.$e->getMessege().'</p>';
        }
    }

}

class Bd {

    private $conexao;
    private $dashboard;

    public function __construct(Conexao $conexao, Dashboard $dashboard){
        $this->conexao = $conexao->conectar();
        $this->dashboard = $dashboard;
    }

    public function getNumeroVendas(){
        $query = '
            SELECT 
                count(*) as numero_vendas
            FROM
                tb_vendas
            WHERE
                data_venda BETWEEN :data_inicio AND :data_fim';

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
        $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;

    }

    public function getTotalVendas(){
        $query = '
        SELECT 
            SUM(total) as total_vendas
        FROM
            tb_vendas
        WHERE
            data_venda BETWEEN :data_inicio AND :data_fim';

    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
    $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
    }

    public function getClientesAtivos(){
        $query = '
        SELECT 
            count(*) as clientes_ativos
        FROM
            tb_clientes
        WHERE
            cliente_ativo = 1';

    $stmt = $this->conexao->prepare($query);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ)->clientes_ativos;
    }

    public function getClientesInativos(){
        $query = '
        SELECT 
            count(*) as clientes_inativos
        FROM
            tb_clientes
        WHERE
            cliente_ativo = 0';

    $stmt = $this->conexao->prepare($query);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ)->clientes_inativos;
    }

    public function getTotalReclamacoes(){
        $query = '
        SELECT 
            count(*) as total_reclamacoes
        FROM
            tb_contatos
        WHERE
            tipo_contato = 1';

    $stmt = $this->conexao->prepare($query);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ)->total_reclamacoes;
    }

    public function getTotalElogios(){
        $query = '
        SELECT 
            count(*) as total_elogios
        FROM
            tb_contatos
        WHERE
            tipo_contato = 2';

    $stmt = $this->conexao->prepare($query);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ)->total_elogios;
    }

    public function getTotalSugestoes(){
        $query = '
        SELECT 
            count(*) as total_sugestoes
        FROM
            tb_contatos
        WHERE
            tipo_contato = 3';

    $stmt = $this->conexao->prepare($query);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ)->total_sugestoes;
    }

    public function getTotalDespesas(){
        $query = '
        SELECT 
            SUM(total) as total_despesas
        FROM
            tb_despesas
        WHERE
            data_despesa BETWEEN :data_inicio AND :data_fim';

    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
    $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ)->total_despesas;
    }
}

$dashboard = new Dashboard();

$conexao = new Conexao();

$competencia = explode('-',$_GET['competencia']);

$ano = $competencia[0];
$mes = $competencia[1];
$dias_do_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

$dashboard->__set('data_inicio', $ano.'-'.$mes.'-01');
$dashboard->__set('data_fim', $ano.'-'.$mes.'-'.$dias_do_mes);

$bd = new Bd($conexao, $dashboard);

$dashboard->__set('numeroVendas', $bd->getNumeroVendas());
$dashboard->__set('totalVendas', $bd->getTotalVendas());

$dashboard->__set('clientesAtivos', $bd->getClientesAtivos());
$dashboard->__set('clientesInativos', $bd->getClientesInativos());

$dashboard->__set('totalReclamacoes', $bd->getTotalReclamacoes());
$dashboard->__set('totalElogios', $bd->getTotalElogios());
$dashboard->__set('totalSugestoes', $bd->getTotalSugestoes());

$dashboard->__set('totalDespesas', $bd->getTotalDespesas());

echo json_encode($dashboard);

?>