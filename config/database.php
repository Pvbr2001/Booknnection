<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

class Database {
    private $conn;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        $host = $_ENV['DB_HOST'];
        $user = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];
        $database = $_ENV['DB_DATABASE'];
        $port = $_ENV['DB_PORT'];

        // Cria a conexão com o banco de dados
        $this->conn = new mysqli($host, $user, $password, $database, $port);

        // Verifica se houve erro na conexão
        if ($this->conn->connect_error) {
            die("Falha na conexão: " . $this->conn->connect_error);
        }
    }

    // Método para acessar a conexão
    public function getConnection() {
        return $this->conn;
    }
}
?>
