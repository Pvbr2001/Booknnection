<?php
session_start();
require_once '../models/user.php';
require_once '../models/post.php';
require_once '../config/database.php';

// Verifica se é uma requisição GET para o logout ou check_auth
if (isset($_GET['acao'])) {
    if ($_GET['acao'] === 'logout') {
        // Destruir a sessão e redirecionar para a página de login
        session_unset();
        session_destroy();
        header("Location: ../views/pagina_login.php");
        exit();
    } elseif ($_GET['acao'] === 'check_auth') {
        // Verifica a autenticação do usuário
        if (isset($_SESSION['user_id'])) {
            $user = new User();
            $user->loadById($_SESSION['user_id']);
            header('Location: ../views/pagina_perfil.php?username=' . urlencode($user->getUsername()));
        } else {
            header('Location: ../views/pagina_login.php');
        }
        exit();
    }
}

// Verificar se é utilizado o método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if (!class_exists('User')) {
        die("Classe User não encontrada!");
    }

    $user = new User();

    if ($acao === 'login') {
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';

        if ($user->login($email, $senha)) {
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['username'] = $user->getUsername();
            header('Location: ../views/pagina_principal.php');
        } else {
            echo 'LoginFailed';
        }
    } elseif ($acao === 'cadastro') {
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';
        $confirmarSenha = $_POST['confirmar_senha'] ?? '';
        $account_type = $_POST['account_type'] ?? '';
        $cpf_cnpj = $_POST['cpf_cnpj'] ?? '';
        $endereco = $_POST['endereco'] ?? '';
        $cidade = $_POST['cidade'] ?? '';
        $telefone = $_POST['telefone'] ?? '';

        if ($senha !== $confirmarSenha) {
            echo 'PasswordsDoNotMatch';
        } else {
            $result = $user->register($nome, $email, $senha, $account_type, $cpf_cnpj, $endereco, $cidade, $telefone);
            echo $result;
        }
    } elseif ($acao === 'adicionar_livro') {
        $titulo = $_POST['titulo'];
        $autor = $_POST['autor'];
        $isbn = $_POST['isbn'];
        $capa_tipo = $_POST['capa_tipo'];
        $ano_lancamento = $_POST['ano_lancamento'];

        //upload da capa
        $caminhoCapa = null;
        if (isset($_FILES['capa']) && $_FILES['capa']['error'] === UPLOAD_ERR_OK) {
            $extensao = pathinfo($_FILES['capa']['name'], PATHINFO_EXTENSION);
            $caminhoCapa = '../public/imagens/' . uniqid().rand(0, 100000) . '.' . $extensao;
            move_uploaded_file($_FILES['capa']['tmp_name'], $caminhoCapa);
        }

        // Verifica se o ISBN já existe
        if ($user->verificarISBN($isbn)) {
            echo "<script>alert('ISBN do livro já existe');</script>";
        } else {
            // Adiciona o livro
            $id_livro = $user->adicionarLivro($titulo, $autor, $isbn, $capa_tipo, $ano_lancamento, $caminhoCapa);

            // Verifica se deve adicionar à lista de livros do usuário
            if (isset($_POST['adicionar_lista'])) {
                $user->adicionarLivroLista($_SESSION['user_id'], $id_livro);
            }

            echo "<script>alert('Livro adicionado com sucesso');</script>";
        }
    } elseif ($acao === 'criar_post') {
        if (!class_exists('Post')) {
            die("Classe Post não encontrada!");
        }

        $post = new Post();
        $id_usuario = $_SESSION['user_id'];
        $id_livro = $_POST['id_livro'];
        $titulo = $_POST['titulo'];
        $descricao = $_POST['descricao'];
        $cidade = $_POST['cidade'];

        if ($post->criarPost($id_usuario, $id_livro, $titulo, $descricao, $cidade)) {
            echo "<script>alert('Post criado com sucesso');</script>";
        } else {
            echo "<script>alert('Erro ao criar post');</script>";
        }
    } elseif ($acao === 'curtir_post') {
        if (!class_exists('Post')) {
            die("Classe Post não encontrada!");
        }

        $post = new Post();
        $id_post = $_POST['id_post'];
        $id_usuario = $_SESSION['user_id'];

        if ($post->curtirPost($id_post, $id_usuario)) {
            echo "<script>alert('Post curtido com sucesso');</script>";
        } else {
            echo "<script>alert('Erro ao curtir o post');</script>";
        }
    } elseif ($acao === 'salvar_post') {
        if (!class_exists('Post')) {
            die("Classe Post não encontrada!");
        }

        $post = new Post();
        $id_usuario = $_SESSION['user_id'];
        $id_post = $_POST['id_post'];

        if ($post->salvarPost($id_usuario, $id_post)) {
            echo "<script>alert('Post salvo com sucesso');</script>";
        } else {
            echo "<script>alert('Erro ao salvar o post');</script>";
        }
    } elseif ($acao === 'atualizar_telefone') {
        $id_usuario = $_SESSION['user_id'];
        $telefone = $_POST['telefone'];

        if ($user->atualizarTelefone($id_usuario, $telefone)) {
            echo "<script>alert('Telefone atualizado com sucesso');</script>";
        } else {
            echo "<script>alert('Erro ao atualizar o telefone');</script>";
        }
    } elseif ($acao === 'finalizar_troca') {
        $id_post = $_POST['id_post'];
        $id_usuario = $_SESSION['user_id'];

        // Verificar se a troca já está finalizada
        $sql = "SELECT status FROM trocas WHERE id_post = ? AND (id_usuario_solicitante = ? OR id_usuario_dono = ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $id_post, $id_usuario, $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $troca = $result->fetch_assoc();

        if ($troca && $troca['status'] === 'finalizada') {
            echo json_encode(['status' => 'error', 'message' => 'Troca já finalizada.']);
        } else {
            // Atualizar o status da troca
            $sql = "UPDATE trocas SET status = 'finalizada' WHERE id_post = ? AND (id_usuario_solicitante = ? OR id_usuario_dono = ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iii", $id_post, $id_usuario, $id_usuario);
            $stmt->execute();

            // Verificar se ambos os usuários finalizaram a troca
            $sql = "SELECT COUNT(*) as total FROM trocas WHERE id_post = ? AND status = 'finalizada'";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_post);
            $stmt->execute();
            $result = $stmt->get_result();
            $total = $result->fetch_assoc()['total'];

            if ($total == 2) {
                // Remover o post e os livros associados
                $sql = "DELETE FROM posts WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id_post);
                $stmt->execute();

                $sql = "DELETE FROM lista_livros WHERE id_livro = (SELECT id_livro FROM posts WHERE id = ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id_post);
                $stmt->execute();

                echo json_encode(['status' => 'success', 'message' => 'Troca finalizada com sucesso.']);
            } else {
                echo json_encode(['status' => 'success', 'message' => 'Troca confirmada. Aguardando o outro usuário.']);
            }
        }
    } elseif ($acao === 'alterar_foto_perfil') {
        $id_usuario = $_SESSION['user_id'];
        $caminhoFoto = null;

        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            $extensao = pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION);
            $caminhoFoto = '../public/imagens/' . uniqid().rand(0, 100000) . '.' . $extensao;
            move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $caminhoFoto);
        }

        if ($user->alterarFotoPerfil($id_usuario, $caminhoFoto)) {
            echo "<script>alert('Foto de perfil alterada com sucesso');</script>";
        } else {
            echo "<script>alert('Erro ao alterar a foto de perfil');</script>";
        }
    } elseif ($acao === 'trocar_senha') {
        $id_usuario = $_SESSION['user_id'];
        $senhaAtual = $_POST['senha_atual'];
        $novaSenha = $_POST['nova_senha'];

        if ($user->trocarSenha($id_usuario, $senhaAtual, $novaSenha)) {
            echo "<script>alert('Senha alterada com sucesso');</script>";
        } else {
            echo "<script>alert('Senha atual incorreta');</script>";
        }
    } elseif ($acao === 'desativar_conta') {
        $id_usuario = $_SESSION['user_id'];

        if ($user->desativarConta($id_usuario)) {
            session_unset();
            session_destroy();
            echo "<script>alert('Conta desativada com sucesso. Todas as informações foram apagadas de forma permanente.');</script>";
            echo "<script>window.location.href = '../views/pagina_login.php';</script>";
        } else {
            echo "<script>alert('Erro ao desativar a conta');</script>";
        }
    } else {
        echo 'InvalidAction';
    }
} else {
    echo 'InvalidRequestMethod';
}
?>
