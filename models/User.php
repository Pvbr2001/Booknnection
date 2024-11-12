<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

class User {
    private $conn;
    private $chave;
    private $id;
    private $username;
    private $email;
    private $nome;
    private $cpf_cnpf;
    private $endereco;
    private $cidade;
    private $account_type;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->chave = getenv('CHAVE_CRIPTOGRAFIA');
    }

    // Function to register a user, encrypting personal data such as password and CPF
    public function register($nome, $email, $senha, $account_type, $cpf_cnpf, $endereco, $cidade) {
    if ($this->userExists($email)) {
        return 'UserExists';
    }

    // Generate a salt for CPF/CNPJ to avoid duplicates
    $salt = bin2hex(random_bytes(16)); // 16-byte salt
    $cpfCnpjCriptografado = openssl_encrypt($cpf_cnpf . $salt, 'aes-256-cbc', $this->chave, 0, str_repeat('0', 16));

    // Encrypt password
    $senhaCriptografada = openssl_encrypt($senha, 'aes-256-cbc', $this->chave, 0, str_repeat('0', 16));

    // Insert into database
    $sql = "INSERT INTO usuario (nome, email, senha, account_type, cpf_cnpf, endereco, cidade) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param('sssssss', $nome, $email, $senhaCriptografada, $account_type, $cpfCnpjCriptografado, $endereco, $cidade);

    if ($stmt->execute()) {
        return 'Success';
    } else {
        return 'RegistrationFailed';
    }
}
    // Function to check if the user already exists in the database
    public function userExists($email) {
        $sql = "SELECT id FROM usuario WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        return $stmt->num_rows > 0;
    }

    // Function to login, decrypting the password
    public function login($email, $senha) {
        $sql = "SELECT id, senha, nome, email, account_type, cpf_cnpf, endereco, cidade FROM usuario WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            return false;
        }

        $stmt->bind_result($id, $senhaCriptografada, $nome, $email, $account_type, $cpf_cnpf, $endereco, $cidade);
        $stmt->fetch();

        $senhaDescriptografada = openssl_decrypt($senhaCriptografada, 'aes-256-cbc', $this->chave, 0, str_repeat('0', 16));

        if ($senha === $senhaDescriptografada) {
            $this->id = $id;
            $this->username = $nome;
            $this->email = $email;
            $this->nome = $nome;
            $this->cpf_cnpf = $cpf_cnpf;
            $this->endereco = $endereco;
            $this->cidade = $cidade;
            $this->account_type = $account_type;
            return true;
        }

        return false;
    }

    // Function to load user information by ID
    public function loadById($id) {
        $sql = "SELECT id, nome, email, account_type, cpf_cnpf, endereco, cidade FROM usuario WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $nome, $email, $account_type, $cpf_cnpf, $endereco, $cidade);
            $stmt->fetch();

            $this->id = $id;
            $this->username = $nome;
            $this->email = $email;
            $this->nome = $nome;
            $this->cpf_cnpf = $cpf_cnpf;
            $this->endereco = $endereco;
            $this->cidade = $cidade;
            $this->account_type = $account_type;
        }
    }

    // Methods to get user information
    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getCpfCnpj() {
        return $this->cpf_cnpf;
    }

    public function getEndereco() {
        return $this->endereco;
    }

    public function getCidade() {
        // retrieve the city from the database or wherever it's stored
        $cidade = $this->cidade; // or $this->getCidadeFromDatabase();
        return $cidade;
    }

    public function getAccountType() {
        return $this->account_type;
    }

    // Function to check if the ISBN is already registered
    public function verificarISBN($isbn) {
        $sql = "SELECT id FROM livros WHERE isbn = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $isbn);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    // Function to add a book
    public function adicionarLivro($titulo, $autor, $isbn, $capa_tipo, $ano_lancamento, $caminhoCapa) {
        $sql = "INSERT INTO livros (titulo, autor, isbn, capa_tipo, ano_lancamento, caminho_capa) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssssss', $titulo, $autor, $isbn, $capa_tipo, $ano_lancamento, $caminhoCapa);
        $stmt->execute();
        return $this->conn->insert_id;  // Return the ID of the inserted book
    }

    // Function to add the book to the user's list
    public function adicionarLivroLista($user_id, $livro_id) {
        $sql = "INSERT INTO lista_livros (id_usuario, id_livro) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $user_id, $livro_id);
        return $stmt->execute();
    }

    // Function to display the saved books in the user's feed
    public function exibirLivrosFeed($id_usuario) {
        $sql = "SELECT livros.id, livros.titulo, livros.autor, livros.caminho_capa FROM livros
                INNER JOIN lista_livros ON livros.id = lista_livros.id_livro
                WHERE lista_livros.id_usuario = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        $livros = [];
        while ($row = $result->fetch_assoc()) {
            $livros[] = $row;
        }
        return $livros;
    }
}
