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

    // Função para registro do usuário encriptografando os dados pessoais como senha e CPF
    public function register($nome, $email, $senha, $account_type, $cpf_cnpf, $endereco, $cidade) {
        if ($this->userExists($email)) {
            return 'UserExists'; 
        }

        $senhaCriptografada = openssl_encrypt($senha, 'aes-256-cbc', $this->chave, 0, str_repeat('0', 16));
        $cpfCnpjCriptografado = openssl_encrypt($cpf_cnpf, 'aes-256-cbc', $this->chave, 0, str_repeat('0', 16));

        $sql = "INSERT INTO usuario (nome, email, senha, account_type, cpf_cnpf, endereco, cidade) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('sssssss', $nome, $email, $senhaCriptografada, $account_type, $cpfCnpjCriptografado, $endereco, $cidade);

        if ($stmt->execute()) {
            return 'Success'; 
        } else {
            return 'RegistrationFailed'; 
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

    //função para carregar as informações do usuário pelo ID
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

    // Métodos para obter as informações do usuário
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
        return $this->cidade;
    }

    public function getAccountType() {
        return $this->account_type;
    }

    // Função para verificar se o ISBN já está cadastrado
    public function verificarISBN($isbn) {
        $sql = "SELECT id FROM livros WHERE isbn = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $isbn);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    // Função para adicionar livro
    public function adicionarLivro($titulo, $autor, $isbn, $capa_tipo, $ano_lancamento, $caminhoCapa) {
        $sql = "INSERT INTO livros (titulo, autor, isbn, capa_tipo, ano_lancamento, caminho_capa) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssssss', $titulo, $autor, $isbn, $capa_tipo, $ano_lancamento, $caminhoCapa);
        $stmt->execute();
        return $this->conn->insert_id;  //Retornar o ID do livro inserido
    }

    // Função para adicionar o livro à lista de um usuário
    public function adicionarLivroLista($user_id, $livro_id) {
        $sql = "INSERT INTO lista_livros (id_usuario, id_livro) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $user_id, $livro_id);
        return $stmt->execute();
    }

    // Função para exibir os livros salvos no feed do usuário
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

    // Função para exibir todos os livros
    public function exibirTodosLivros() {
        $sql = "SELECT titulo, autor, caminho_capa FROM livros";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        $livros = [];
        while ($row = $result->fetch_assoc()) {
            $livros[] = $row;
        }
        return $livros;
    }

    // Função para criar um post
    public function criarPost($id_usuario, $id_livro, $titulo, $descricao, $cidade) {
        $sql = "INSERT INTO posts (id_usuario, id_livro, titulo, descricao, cidade) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('iisss', $id_usuario, $id_livro, $titulo, $descricao, $cidade);
        return $stmt->execute();
    }

    // Função para exibir posts baseados na cidade do usuário
    public function exibirPostsPorCidade($cidade) {
        $sql = "SELECT p.id, p.titulo, p.descricao, p.curtidas, l.caminho_capa
                FROM posts p
                JOIN usuario u ON p.id_usuario = u.id
                JOIN livros l ON p.id_livro = l.id
                WHERE u.cidade = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $cidade);
        $stmt->execute();
        $result = $stmt->get_result();

        $posts = [];
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
        return $posts;
    }

    // Função para curtir um post
    public function curtirPost($id_post) {
        $sql = "UPDATE posts SET curtidas = curtidas + 1 WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id_post);
        return $stmt->execute();
    }

    // Função para salvar um post
    public function salvarPost($id_usuario, $id_post) {
        $sql = "INSERT INTO posts_salvos (id_usuario, id_post) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $id_usuario, $id_post);
        return $stmt->execute();
    }

    // Função para exibir posts salvos do usuário
    public function exibirPostsSalvos($id_usuario) {
        $sql = "SELECT p.id, p.titulo, p.descricao, p.curtidas, l.caminho_capa
                FROM posts_salvos ps
                JOIN posts p ON ps.id_post = p.id
                JOIN livros l ON p.id_livro = l.id
                WHERE ps.id_usuario = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        $posts = [];
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
        return $posts;
    }

    // Função para exibir todos os posts que o usuário criou
    public function exibirPostsDoUsuario($id_usuario) {
        $sql = "SELECT p.id, p.titulo, p.descricao, p.curtidas, l.caminho_capa
                FROM posts p
                JOIN livros l ON p.id_livro = l.id
                WHERE p.id_usuario = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        $posts = [];
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
        return $posts;
    }
}
?>
