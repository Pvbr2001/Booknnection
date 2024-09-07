<?php
require_once 'user.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    $user = new User();

    if ($acao === 'login') {
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        
        if ($user->login($email, $senha)) {
            header('Location: index.html');
        } else {
            echo "Email ou senha incorretos!";
        }
    } elseif ($acao === 'cadastro') {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        $confirmarSenha = $_POST['confirmar_senha'];
        $account_type = $_POST['account_type'];
        $cpf_cnpf = $_POST['cpf_cnpf'];
        $endereco = $_POST['endereco'];
        
        if ($senha !== $confirmarSenha) {
            echo "As senhas não coincidem!";
        } else {
            if ($user->register($nome, $email, $senha, $account_type, $cpf_cnpf, $endereco)) {
                header('Location: sucesso_cadastro.html');
            } else {
                echo "Erro ao realizar o cadastro!";
            }
        }
    } else {
        echo "Ação inválida!";
    }
} else {
    echo "Método de requisição inválido!";
}
?>
