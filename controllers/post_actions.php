<?php
session_start();
require_once '../models/post.php'; 
require_once '../config/database.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if (!class_exists('Post')) {
        die("Classe Post não encontrada!");
    }

    $post = new Post(); 

    if ($acao === 'curtir_post') {
        $id_post = $_POST['id_post'];
        $id_user = $_SESSION['user_id']; 

        if ($post->curtirPost($id_post, $id_user)) {
            echo "<script>alert('Post curtido com sucesso'); window.location.href = document.referrer;</script>";
        } else {
            echo "<script>alert('Erro ao curtir post'); window.location.href = document.referrer;</script>";
        }
    } elseif ($acao === 'salvar_post') {
        $id_usuario = $_SESSION['user_id'];
        $id_post = $_POST['id_post'];

        if ($post->salvarPost($id_usuario, $id_post)) {
            echo "<script>alert('Post salvo com sucesso'); window.location.href = document.referrer;</script>";
        } else {
            echo "<script>alert('Erro ao salvar post'); window.location.href = document.referrer;</script>";
        }
    } elseif ($acao === 'trocar_livro') {
        $id_post = $_POST['id_post'];
        $id_usuario_atual = $_SESSION['user_id'];

        // Verifique se o ID do post e o ID do usuário atual são válidos
        if (empty($id_post) || empty($id_usuario_atual)) {
            echo "<script>alert('Erro: Dados inválidos.'); window.location.href = document.referrer;</script>";
            exit;
        }

        // Conectar ao banco de dados
        $database = new Database();
        $conn = $database->getConnection();

        // Buscar o ID do dono do post (usuário 2)
        $sql_dono_post = "SELECT id_usuario FROM posts WHERE id = ?";
        $stmt = $conn->prepare($sql_dono_post);
        $stmt->bind_param("i", $id_post);
        $stmt->execute();
        $result = $stmt->get_result();
        $post_dono = $result->fetch_assoc();
        $id_usuario_dono = $post_dono['id_usuario'];  // Dono do post (usuário 2)
        
        // Inserir a notificação para o dono do post (usuário 2)
        $sql = "INSERT INTO notificacoes (id_usuario, tipo, id_post) VALUES (?, 'troca', ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ii", $id_usuario_dono, $id_post);
            if ($stmt->execute()) {
                echo "<script>alert('Notificação enviada com sucesso para o dono do post!'); window.location.href = document.referrer;</script>";
            } else {
                echo "<script>alert('Erro ao enviar notificação: " . $stmt->error . "'); window.location.href = document.referrer;</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Erro ao preparar a consulta: " . $conn->error . "'); window.location.href = document.referrer;</script>";
        }

        $conn->close();
    } else {
        echo "<script>alert('Ação inválida'); window.location.href = document.referrer;</script>";
    }
} else {
    echo "<script>alert('Método de requisição inválido'); window.location.href = document.referrer;</script>";
}
?>
