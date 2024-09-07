<?php
require_once 'user.php';

// Verifica se a requisição é um POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtém o valor da ação
    $acao = $_POST['acao'] ?? '';

    // Verifica se a classe User foi carregada corretamente
    if (!class_exists('User')) {
        die("Classe User não encontrada!");
    }
    
    //criando uma instacia de user
    $user = new User();

    //processando as informaçoes de login
    if ($acao === 'login') {
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';
        
        // Verifica o login
        if ($user->login($email, $senha)) {
            header('Location: pagina_inicial.html');
            exit();
        } else {
            echo "Email ou senha incorretos!";
        }
    } 
    // Processa a ação de cadastro
    elseif ($acao === 'cadastro') {
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';
        $confirmarSenha = $_POST['confirmar_senha'] ?? '';
        $account_type = $_POST['account_type'] ?? '';
        $cpf_cnpf = $_POST['cpf_cnpf'] ?? '';
        $endereco = $_POST['endereco'] ?? '';
        
        // Verifica se as senhas coincidem
        if ($senha !== $confirmarSenha) {
            echo "As senhas não coincidem!";
        } else {
            //resgistro do usuario e sendo redirecionado para pagina principal(temporarario enquanto nao tem o market/introduçao da plataforma)
            if ($user->register($nome, $email, $senha, $account_type, $cpf_cnpf, $endereco)) {
                header('Location: pagina_inicial.html');
                exit();
            } else {
                echo "Erro ao realizar o cadastro!";
            }
        }
    } else {
        echo "Ação inválida!";
    }
} else {
    // caso metodo post nao seja reconhecido corretamente
    echo "Método de requisição inválido!";
}
?>
