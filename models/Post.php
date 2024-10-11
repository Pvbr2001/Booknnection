<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

class Post {
    private $conn;
    private $chave;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->chave = getenv('CHAVE_CRIPTOGRAFIA');
    }

    // Function to create a post
    public function criarPost($id_usuario, $id_livro, $titulo, $descricao, $cidade) {
        $sql = "INSERT INTO posts (id_usuario, id_livro, titulo, descricao, cidade) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('iisss', $id_usuario, $id_livro, $titulo, $descricao, $cidade);
        return $stmt->execute();
    }

    // Function to display posts based on the user's city
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

    // Function to like a post
    public function curtirPost($id_post) {
        $sql = "UPDATE posts SET curtidas = curtidas + 1 WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id_post);
        return $stmt->execute();
    }

    // Function to save a post
    public function salvarPost($id_usuario, $id_post) {
        $sql = "INSERT INTO posts_salvos (id_usuario, id_post) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $id_usuario, $id_post);
        return $stmt->execute();
    }

    // Function to display saved posts of the user
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

    // Function to display all posts created by the user
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

    // Function to get the name of the post owner
    public function getNomeDonoPost($id_post) {
        $sql = "SELECT u.nome FROM posts p JOIN usuario u ON p.id_usuario = u.id WHERE p.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id_post);
        $stmt->execute();
        $result = $stmt->get_result();

        $nomeDonoPost = '';
        if ($row = $result->fetch_assoc()) {
            $nomeDonoPost = $row['nome'];
        }

        return $nomeDonoPost;
    }
}
