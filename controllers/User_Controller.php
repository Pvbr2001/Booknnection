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
            header('Location: ../views/login.html');
        }
    } else {
        echo 'InvalidAction';
    }
} else {
    echo 'InvalidRequestMethod';
}
?>
