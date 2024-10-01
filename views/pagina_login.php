<?php
session_start();

// Verifica se o usuário já está autenticado
if (isset($_SESSION['user_id'])) {
    // Redireciona o usuário para a página principal, pois já está autenticado
    header('Location: pagina_principal.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/estilos_css/login.css">
    <title>Booknnection - Login/Cadastro</title>
</head>
<body>
    <header>
        <!-- Header sem funcionalidade até o momento -->
    </header>

    <div class="container-wrapper">
        <!-- Container para Login -->
        <div class="form-container login-container">
            <h1>Booknnection</h1>
            <button class="google-btn">Entrar com Google</button>
            <form id="login-form" method="POST" action="../controllers/user_controller.php">
                <input type="hidden" name="acao" value="login">
                <input type="email" name="email" placeholder="E-mail" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit">Entrar</button>
            </form>
            <p>Não tem uma conta? <a href="#" id="showCadastro">Cadastrar</a></p>
        </div>

        <!-- Container para Cadastro -->
        <div class="form-container cadastro-container hidden">
            <h1>Booknnection</h1>
            <button class="google-btn">Entrar com Google</button>
            <form id="cadastro-form" method="POST" action="../controllers/user_controller.php">
                <input type="hidden" name="acao" value="cadastro">
                <input type="text" name="nome" placeholder="Nome" required>
                <input type="email" name="email" placeholder="E-mail" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <input type="password" name="confirmar_senha" placeholder="Confirme a Senha" required>
                <input type="text" name="cpf_cnpf" placeholder="CPF/CNPJ" required>
                <input type="text" name="endereco" placeholder="Endereço" required>
                <input type="text" name="cidade" placeholder="Cidade" required>
                <select name="account_type" required>
                    <option value="fisica">Pessoa Física</option>
                    <option value="juridica">Pessoa Jurídica</option>
                </select>
                <button type="submit">Cadastrar</button>
            </form>
            <p>Já tem uma conta? <a href="#" id="showLogin">Conecte-se</a></p>
        </div>
    </div>

    <script>
        // Função para Mostrar Cadastro
        document.getElementById('showCadastro').addEventListener('click', function(e) {
            e.preventDefault(); // Impede o comportamento padrão do link

            const loginContainer = document.querySelector('.login-container');
            const cadastroContainer = document.querySelector('.cadastro-container');

            // Esconde o login e mostra o cadastro
            loginContainer.classList.add('hidden');
            cadastroContainer.classList.remove('hidden');
            cadastroContainer.classList.add('active');

            // Garante que o z-index seja corretamente manipulado
            loginContainer.style.zIndex = 1;
            cadastroContainer.style.zIndex = 2;
        });

        // Função para Mostrar Login
        document.getElementById('showLogin').addEventListener('click', function(e) {
            e.preventDefault(); // Impede o comportamento padrão do link

            const loginContainer = document.querySelector('.login-container');
            const cadastroContainer = document.querySelector('.cadastro-container');

            // Esconde o cadastro e mostra o login
            cadastroContainer.classList.add('hidden');
            loginContainer.classList.remove('hidden');
            cadastroContainer.classList.remove('active');

            // Garante que o z-index seja corretamente manipulado
            loginContainer.style.zIndex = 2;
            cadastroContainer.style.zIndex = 1;
        });

        if (window.location.hash === '#showCadastro') {
            const loginContainer = document.querySelector('.login-container');
            const cadastroContainer = document.querySelector('.cadastro-container');

            // Esconde o login e mostra o cadastro
            loginContainer.classList.add('hidden');
            cadastroContainer.classList.remove('hidden');
            cadastroContainer.classList.add('active');

            // Garante que o z-index seja corretamente manipulado
            loginContainer.style.zIndex = 1;
            cadastroContainer.style.zIndex = 2;
        }
    </script>
</body>
</html>


<script type="module">
    import Typebot from 'https://cdn.jsdelivr.net/npm/@typebot.io/js@0.3.12/dist/web.js'
  
    Typebot.initBubble({
      typebot: "suporte-ao-cliente-squxxd3",
      apiHost: "http://34.132.245.158:8080",
      previewMessage: { message: "Está precisando de ajuda?", autoShowDelay: 5000 },
      theme: {
        button: { backgroundColor: "#0042DA" },
        chatWindow: { backgroundColor: "#fff" },
      },
    });
  </script>

  