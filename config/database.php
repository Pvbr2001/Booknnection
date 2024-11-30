<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

class Database {
    private static $conn;
    private static $instance;

    // Construtor privado para evitar instanciação direta
    private function __construct() {
        $this->connect();
    }

    // Método estático para obter a instância única da classe
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    private function connect() {
        $host = $_ENV['DB_HOST'];
        $user = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];
        $database = $_ENV['DB_DATABASE'];
        $port = $_ENV['DB_PORT'];

        // Cria a conexão com o banco de dados
        self::$conn = new mysqli($host, $user, $password, $database, $port);

        // Verifica se houve erro na conexão
        if (self::$conn->connect_error) {
            die("Falha na conexão: " . self::$conn->connect_error);
        }
    }

    // Método para acessar a conexão
    public function getConnection() {
        return self::$conn;
    }
}
?>

