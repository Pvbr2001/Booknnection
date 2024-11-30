<?php
session_start();
require_once '../models/user.php';
require_once '../models/post.php';
require_once '../config/database.php'; // Incluir o arquivo de configuração do banco de dados

// Verifica se é uma requisição GET para o logout ou check_auth
if (isset($_GET['acao'])) {
    if ($_GET['acao'] === 'logout') {
        // Destruir a sessão e redirecionar para a página de login
        session_unset(); // Limpa todas as variáveis de sessão
        session_destroy(); // Destrói a sessão
        header("Location: ../views/pagina_login.php"); // Redireciona para a página de login
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
        exit(); // Para evitar que o resto do código seja executado
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
        $cpf_cnpf = $_POST['cpf_cnpf'] ?? '';
        $endereco = $_POST['endereco'] ?? '';
        $cidade = $_POST['cidade'] ?? '';

        if ($senha !== $confirmarSenha) {
            echo 'PasswordsDoNotMatch';
        } else {
            $result = $user->register($nome, $email, $senha, $account_type, $cpf_cnpf, $endereco, $cidade);
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
    } else {
        echo 'InvalidAction';
    }
} else {
    echo 'InvalidRequestMethod';
}
?>
