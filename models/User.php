<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Arquivo de criação do usuário no bd
class User {
    private $conn;
    private $chave;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->chave = getenv('CHAVE_CRIPTOGRAFIA');
    }

    // Função para registro do usuário encriptografando os dados pessoais como senha e CPF
    public function register($nome, $email, $senha, $account_type, $cpf_cnpf, $endereco) {
        if ($this->userExists($email)) {
            return 'UserExists'; // Usuário já existe
        }

        $senhaCriptografada = openssl_encrypt($senha, 'aes-256-cbc', $this->chave, 0, str_repeat('0', 16));
        $cpfCnpjCriptografado = openssl_encrypt($cpf_cnpf, 'aes-256-cbc', $this->chave, 0, str_repeat('0', 16));
        
        $sql = "INSERT INTO usuario (nome, email, senha, account_type, cpf_cnpf, endereco) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssssss', $nome, $email, $senhaCriptografada, $account_type, $cpfCnpjCriptografado, $endereco);
        
        if ($stmt->execute()) {
            return 'Success'; // Cadastro bem-sucedido
        } else {
            return 'RegistrationFailed'; // Falha no cadastro
        }
    }

    // Função para verificar se o usuário já existe no banco de dados
    public function userExists($email) {
        $sql = "SELECT id FROM usuario WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        return $stmt->num_rows > 0;
    }

    // Função para login desencriptografando a senha 
    public function login($email, $senha) {
        $sql = "SELECT senha FROM usuario WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            return false;
        }

        $stmt->bind_result($senhaCriptografada);
        $stmt->fetch();

        $senhaDescriptografada = openssl_decrypt($senhaCriptografada, 'aes-256-cbc', $this->chave, 0, str_repeat('0', 16));
        
        return $senha === $senhaDescriptografada;
    }
}
?>
