<?php
class Conectar {
    protected $dbh;

    protected function Conexion() {
        try {
            $conectar = $this->dbh = new PDO("mysql:local=127.0.0.1;dbname=inventario", "root", "");
            return $conectar;
        } catch (PDOException $e) {
            echo "¡Error DB!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    public function set_names() {
        return $this->dbh->query("SET NAMES 'utf8'");
    }
}
?>
