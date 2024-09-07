<?php
class Database {
    private $host = 'localhost';
    private $username = 'usuario';
    private $password = 'senha';
    private $database = 'banco_de_dados';
    public $conn;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);

        if ($this->conn->connect_error) {
            die('Erro na conexão: ' . $this->conn->connect_error);
        }
    }
}
?>
