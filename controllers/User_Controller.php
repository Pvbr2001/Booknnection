<?php
session_start();
require_once '../models/user.php';

// Verifica se a requisição é um POST
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
            header('Location: ../views/pagina_principal.html');
        } else {
            echo 'LoginFailed';
        }
    } elseif ($acao === 'cadastro') {
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';
        $confirmarSenha = $_POST['confirmar_senha'] ?? '';
        $account_type = $_POST['account_type'] ?? '';
        $cpf_cnpf = $_POST['cpf_cnpf'] ?? '';
        $endereco = $_POST['endereco'] ?? '';

        if ($senha !== $confirmarSenha) {
            echo 'PasswordsDoNotMatch';
        } else {
            $result = $user->register($nome, $email, $senha, $account_type, $cpf_cnpf, $endereco);
            echo $result;
        }
    } elseif ($acao === 'check_auth') {
        if (isset($_SESSION['user_id'])) {
            $user->loadById($_SESSION['user_id']);
            header('Location: ../views/pagina_perfil.php?username=' . urlencode($user->getUsername()));
        } else {
            header('Location: ../views/pagina_login.html');
        }
    } else {
        echo 'InvalidAction';
    }

    if ($acao === 'adicionar_livro') {
        $titulo = $_POST['titulo'];
        $autor = $_POST['autor'];
        $isbn = $_POST['isbn'];
        $capa_tipo = $_POST['capa_tipo'];
        $ano_lancamento = $_POST['ano_lancamento'];

        // Lidar com o upload da capa
        $caminhoCapa = null;
        if (isset($_FILES['capa']) && $_FILES['capa']['error'] === UPLOAD_ERR_OK) {
            $extensao = pathinfo($_FILES['capa']['name'], PATHINFO_EXTENSION);
            $caminhoCapa = '../public/imagens/' . uniqid() . '.' . $extensao;
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
    }
} else {
    echo 'InvalidRequestMethod';
}
?>
