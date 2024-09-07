<?php
require_once 'database.php';
// arquivo de criaçao do usuario no bd
class User {
    private $conn;
    private $chave;

    //metodo construtor é chamado automaticamente quando um objeto da classe User é criado
    public function __construct() {
        //Aqui, um novo objeto da classe Database é criado.
        // O método connect() da classe Database é chamado automaticamente pelo construtor da classe Database,
        // estabelecendo uma conexão com o banco de dados. A propriedade conn do objeto Database
        // (que contém a conexão com o banco de dados) é então atribuída à propriedade $conn da classe User.
        $this->conn = (new Database())->conn;
        $this->chave = getenv('CHAVE_CRIPTOGRAFIA');
    }

    //funçao para registro do usuario encriptografando os dados pessoais como senha e cpf
    public function register($nome, $email, $senha, $account_type, $cpf_cnpf, $endereco) {
        $senhaCriptografada = openssl_encrypt($senha, 'aes-256-cbc', $this->chave, 0, str_repeat('0', 16));
        $cpfCnpjCriptografado = openssl_encrypt($cpf_cnpf, 'aes-256-cbc', $this->chave, 0, str_repeat('0', 16));
        
        $sql = "INSERT INTO usuario (nome, email, senha, account_type, cpf_cnpf, endereco) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssssss', $nome, $email, $senhaCriptografada, $account_type, $cpfCnpjCriptografado, $endereco);
        return $stmt->execute();
    }

    //funçao para login desincriptografando a senha 
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
