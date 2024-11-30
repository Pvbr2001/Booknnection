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
        $database = Database::getInstance();
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
        $sql = "SELECT *
                FROM vw_posts
                WHERE cidade = ?";
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
    public function curtirPost($id_post, $id_usuario) {
        // Verifica se o usuário já curtiu o post
        $sqlCheck = "SELECT * FROM curtidas WHERE id_usuario = ? AND id_post = ?";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->bind_param('ii', $id_usuario, $id_post);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();
    
        if ($resultCheck->num_rows > 0) {
            // Se já curtiu, remover curtida
            $sqlDelete = "DELETE FROM curtidas WHERE id_usuario = ? AND id_post = ?";
            $stmtDelete = $this->conn->prepare($sqlDelete);
            $stmtDelete->bind_param('ii', $id_usuario, $id_post);
            return $stmtDelete->execute();
        } else {
            // Se não curtiu, adicionar curtida
            $sqlInsert = "INSERT INTO curtidas (id_usuario, id_post) VALUES (?, ?)";
            $stmtInsert = $this->conn->prepare($sqlInsert);
            $stmtInsert->bind_param('ii', $id_usuario, $id_post);
            return $stmtInsert->execute();
        }
    }
    
    

    // Function to save a post
    public function salvarPost($id_usuario, $id_post) {
        // Verifica se o usuário já salvou o post
        $sqlCheck = "SELECT * FROM posts_salvos WHERE id_usuario = ? AND id_post = ?";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->bind_param('ii', $id_usuario, $id_post);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();
    
        if ($resultCheck->num_rows > 0) {
            // Se já salvou, remover o post salvo
            $sqlDelete = "DELETE FROM posts_salvos WHERE id_usuario = ? AND id_post = ?";
            $stmtDelete = $this->conn->prepare($sqlDelete);
            $stmtDelete->bind_param('ii', $id_usuario, $id_post);
            return $stmtDelete->execute();
        } else {
            // Se não salvou, adicionar o post aos salvos
            $sqlInsert = "INSERT INTO posts_salvos (id_usuario, id_post) VALUES (?, ?)";
            $stmtInsert = $this->conn->prepare($sqlInsert);
            $stmtInsert->bind_param('ii', $id_usuario, $id_post);
            return $stmtInsert->execute();
        }
    }

    // Function to display saved posts of the user
    public function exibirPostsSalvos($id_usuario) {
        $sql = "SELECT p.id, p.titulo, p.descricao, l.caminho_capa
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
        $sql = "SELECT p.id, p.titulo, p.descricao, l.caminho_capa
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

    public function getNome($id_post) {
        // Query the database to retrieve the post owner's name
        $sql = "SELECT nome FROM usuario WHERE id = (SELECT id_usuario FROM posts WHERE id = ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_post);
        $stmt->execute();
        $result = $stmt->get_result();
        $nome = $result->fetch_assoc()['nome'];
        return $nome;
    }

    public function getTitulo($id_post) {
        $sql = "SELECT titulo FROM posts WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_post);
        $stmt->execute();
        $result = $stmt->get_result();
        $titulo = $result->fetch_assoc()['titulo'];
        return $titulo;
    }
}
