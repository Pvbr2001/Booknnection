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
            echo json_encode(['status' => 'success', 'message' => 'Post curtido com sucesso']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao curtir post']);
        }
    } elseif ($acao === 'salvar_post') {
        $id_usuario = $_SESSION['user_id'];
        $id_post = $_POST['id_post'];

        if ($post->salvarPost($id_usuario, $id_post)) {
            echo json_encode(['status' => 'success', 'message' => 'Post salvo com sucesso']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar post']);
        }
    } elseif ($acao === 'trocar_livro') {
        $id_post = $_POST['id_post'];
        $id_usuario_atual = $_SESSION['user_id'];

        // Verificar se o ID do post e o ID do usuário atual são válidos
        if (empty($id_post) || empty($id_usuario_atual)) {
            echo json_encode(['status' => 'error', 'message' => 'Erro: Dados inválidos.']);
            exit;
        }

        // Obter a instância da conexão com o banco de dados
        $database = Database::getInstance();
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
        $sql = "INSERT INTO notificacoes (id_usuario, id_usuario_emissor, tipo, id_post) VALUES (?, ?, 'troca', ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("iii", $id_usuario_dono, $id_usuario_atual, $id_post); // Passando o id do emissor
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Notificação enviada com sucesso para o dono do post!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erro ao enviar notificação: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao preparar a consulta: ' . $conn->error]);
        }

        $conn->close();
    } elseif ($acao === 'adicionar_comentario') {
        $id_post = $_POST['id_post'];
        $id_usuario = $_SESSION['user_id'];
        $comentario = $_POST['comentario'];

        // Obter a instância da conexão com o banco de dados
        $database = Database::getInstance();
        $conn = $database->getConnection();

        // Inserir o comentário no banco de dados
        $sql = "INSERT INTO comentarios (id_post, id_usuario, texto) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("iis", $id_post, $id_usuario, $comentario);
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Comentário adicionado com sucesso!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erro ao adicionar comentário: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao preparar a consulta: ' . $conn->error]);
        }

        $conn->close();
    } elseif ($acao === 'aceitar_troca') {
        $id_post = $_POST['id_post'];
        $id_usuario_atual = $_SESSION['user_id'];

        // Obter a instância da conexão com o banco de dados
        $database = Database::getInstance();
        $conn = $database->getConnection();

        // Buscar o ID do dono do post (usuário 2)
        $sql_dono_post = "SELECT id_usuario FROM posts WHERE id = ?";
        $stmt = $conn->prepare($sql_dono_post);
        $stmt->bind_param("i", $id_post);
        $stmt->execute();
        $result = $stmt->get_result();
        $post_dono = $result->fetch_assoc();
        $id_usuario_dono = $post_dono['id_usuario'];  // Dono do post (usuário 2)

        // Buscar o ID do livro do post
        $sql_livro_post = "SELECT id_livro FROM posts WHERE id = ?";
        $stmt = $conn->prepare($sql_livro_post);
        $stmt->bind_param("i", $id_post);
        $stmt->execute();
        $result = $stmt->get_result();
        $livro_post = $result->fetch_assoc();
        $id_livro_post = $livro_post['id_livro'];  // ID do livro do post

        // Buscar o ID do livro do usuário atual
        $sql_livro_usuario = "SELECT id_livro FROM lista_livros WHERE id_usuario = ? LIMIT 1";
        $stmt = $conn->prepare($sql_livro_usuario);
        $stmt->bind_param("i", $id_usuario_atual);
        $stmt->execute();
        $result = $stmt->get_result();
        $livro_usuario = $result->fetch_assoc();
        $id_livro_usuario = $livro_usuario['id_livro'];  // ID do livro do usuário atual

        // Trocar os livros entre os usuários
        $sql_troca = "UPDATE lista_livros SET id_livro = CASE
                        WHEN id_usuario = ? THEN ?
                        WHEN id_usuario = ? THEN ?
                      END
                      WHERE id_usuario IN (?, ?)";
        $stmt = $conn->prepare($sql_troca);
        $stmt->bind_param("iiiiii", $id_usuario_atual, $id_livro_post, $id_usuario_dono, $id_livro_usuario, $id_usuario_atual, $id_usuario_dono);

        if ($stmt->execute()) {
            // Excluir as notificações relacionadas ao post
            $sql_excluir_notificacoes = "DELETE FROM notificacoes WHERE id_post = ?";
            $stmt = $conn->prepare($sql_excluir_notificacoes);
            $stmt->bind_param("i", $id_post);
            if ($stmt->execute()) {
                // Excluir o post após a troca
                $sql_excluir_post = "DELETE FROM posts WHERE id = ?";
                $stmt = $conn->prepare($sql_excluir_post);
                $stmt->bind_param("i", $id_post);
                if ($stmt->execute()) {
                    echo json_encode(['status' => 'success', 'message' => 'Troca realizada com sucesso e post excluído!']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir o post: ' . $stmt->error]);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir notificações: ' . $stmt->error]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao realizar a troca: ' . $stmt->error]);
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Ação inválida']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método de requisição inválido']);
}
?>
