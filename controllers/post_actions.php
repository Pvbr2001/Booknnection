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
        $livros_troca = $_POST['livros_troca'];
    
        // Verificar se o ID do post e o ID do usuário atual são válidos
        if (empty($id_post) || empty($id_usuario_atual) || empty($livros_troca)) {
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
                // Inserir a troca na tabela trocas
                $sql_insert_troca = "INSERT INTO trocas (id_post, id_usuario_solicitante, id_usuario_dono, status) VALUES (?, ?, ?, 'pendente')";
                $stmt = $conn->prepare($sql_insert_troca);
                $stmt->bind_param("iii", $id_post, $id_usuario_atual, $id_usuario_dono);
                if ($stmt->execute()) {
                    // Inserir os livros selecionados na tabela trocas_livros
                    $id_troca = $stmt->insert_id;
                    foreach ($livros_troca as $livro_id) {
                        $sql_insert_livro_troca = "INSERT INTO trocas_livros (id_troca, id_livro) VALUES (?, ?)";
                        $stmt = $conn->prepare($sql_insert_livro_troca);
                        $stmt->bind_param("ii", $id_troca, $livro_id);
                        $stmt->execute();
                    }
                    echo json_encode(['status' => 'success', 'message' => 'Notificação enviada com sucesso para o dono do post!']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Erro ao inserir troca: ' . $stmt->error]);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erro ao enviar notificação: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao preparar a consulta: ' . $conn->error]);
        }
    
        $conn->close();
    }
     elseif ($acao === 'adicionar_comentario') {
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
    } elseif ($acao === 'confirmar_troca') {
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

        // Inserir ou atualizar a confirmação de troca
        $sql_insert_confirmacao = "INSERT INTO confirmacoes_troca (id_post, id_usuario, confirmado) VALUES (?, ?, 1)
                                   ON DUPLICATE KEY UPDATE confirmado = 1";
        $stmt = $conn->prepare($sql_insert_confirmacao);
        $stmt->bind_param("ii", $id_post, $id_usuario_atual);

        if ($stmt->execute()) {
            // Verificar se ambos os usuários confirmaram a troca
            $sql_check_confirmacoes = "SELECT COUNT(*) as total FROM confirmacoes_troca WHERE id_post = ? AND confirmado = 1";
            $stmt = $conn->prepare($sql_check_confirmacoes);
            $stmt->bind_param("i", $id_post);
            $stmt->execute();
            $result = $stmt->get_result();
            $total_confirmacoes = $result->fetch_assoc()['total'];

            if ($total_confirmacoes == 2) {
                // Ambos os usuários confirmaram a troca, finalizar a troca
                $sql_finalizar_troca = "UPDATE trocas SET status = 'finalizada' WHERE id_post = ?";
                $stmt = $conn->prepare($sql_finalizar_troca);
                $stmt->bind_param("i", $id_post);
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
                            // Trocar os livros entre os usuários
                            $sql_trocar_livros = "UPDATE lista_livros SET id_usuario = CASE
                                                    WHEN id_usuario = ? THEN ?
                                                    WHEN id_usuario = ? THEN ?
                                                  END
                                                  WHERE id_livro IN (
                                                    SELECT id_livro FROM lista_livros WHERE id_usuario = ? OR id_usuario = ?
                                                  )";
                            $stmt = $conn->prepare($sql_trocar_livros);
                            $stmt->bind_param("iiiiii", $id_usuario_atual, $id_usuario_dono, $id_usuario_dono, $id_usuario_atual, $id_usuario_atual, $id_usuario_dono);
                            if ($stmt->execute()) {
                                echo json_encode(['status' => 'success', 'message' => 'Troca finalizada com sucesso e livros trocados!']);
                            } else {
                                echo json_encode(['status' => 'error', 'message' => 'Erro ao trocar livros: ' . $stmt->error]);
                            }
                        } else {
                            echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir o post: ' . $stmt->error]);
                        }
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir notificações: ' . $stmt->error]);
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Erro ao finalizar a troca: ' . $stmt->error]);
                }
            } else {
                echo json_encode(['status' => 'success', 'message' => 'Troca confirmada. Aguardando o outro usuário.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao confirmar a troca: ' . $stmt->error]);
        }

        $stmt->close();
        $conn->close();
    } elseif ($acao === 'selecionar_livro_troca') {
        $id_post = $_POST['id_post'];
        $id_livro_troca = $_POST['livro_troca'];
        $id_usuario_atual = $_SESSION['user_id'];

        // Obter a instância da conexão com o banco de dados
        $database = Database::getInstance();
        $conn = $database->getConnection();

        // Atualizar o status da troca para 'confirmada'
        $sql_update_troca = "UPDATE trocas SET status = 'confirmada', id_livro_solicitante = ? WHERE id_post = ? AND id_usuario_solicitante = ?";
        $stmt = $conn->prepare($sql_update_troca);
        $stmt->bind_param("iii", $id_livro_troca, $id_post, $id_usuario_atual);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Livro selecionado para troca com sucesso!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao selecionar livro para troca: ' . $stmt->error]);
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