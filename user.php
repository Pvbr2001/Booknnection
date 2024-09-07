<?php
require_once 'database.php';

class User {
    private $conn;
    private $chave;

    public function __construct() {
        $this->conn = (new Database())->conn;
        $this->chave = getenv('CHAVE_CRIPTOGRAFIA');
    }

    public function register($nome, $email, $senha, $account_type, $cpf_cnpf, $endereco) {
        $senhaCriptografada = openssl_encrypt($senha, 'aes-256-cbc', $this->chave, 0, str_repeat('0', 16));
        $cpfCnpjCriptografado = openssl_encrypt($cpf_cnpf, 'aes-256-cbc', $this->chave, 0, str_repeat('0', 16));
        
        $sql = "INSERT INTO usuario (nome, email, senha, account_type, cpf_cnpf, endereco) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssssss', $nome, $email, $senhaCriptografada, $account_type, $cpfCnpjCriptografado, $endereco);
        return $stmt->execute();
    }

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
