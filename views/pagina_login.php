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
    <header></header>

    <div class="container-wrapper">
        <!-- Container para Login -->
        <div class="form-container login-container">
            <h1>Booknnection</h1>
            <button class="google-btn">Entrar com Google</button>
            <form id="login-form" method="POST" action="../controllers/user_controller.php">
                <input type="hidden" name="acao" value="login">
                <div class="floating-label">
                    <input type="email" name="email" placeholder=" " required>
                    <label for="email">E-mail</label>
                </div>
                <div class="floating-label">
                    <input type="password" name="senha" placeholder=" " required>
                    <label for="senha">Senha</label>
                </div>
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
                <div class="floating-label">
                    <input type="text" name="nome" placeholder=" " required>
                    <label for="nome">Nome</label>
                </div>
                <div class="floating-label">
                    <input type="email" name="email" placeholder=" " required>
                    <label for="email">E-mail</label>
                </div>
                <div class="floating-label">
                    <input type="password" name="senha" placeholder=" " required>
                    <label for="senha">Senha</label>
                </div>
                <div class="floating-label">
                    <input type="password" name="confirmar_senha" placeholder=" " required>
                    <label for="confirmar_senha">Confirme a Senha</label>
                </div>
                <div class="floating-label">
                    <input type="text" name="cpf" placeholder=" " required>
                    <label for="cpf">CPF</label>
                </div>
                <div class="floating-label">
                    <input type="text" name="endereco" placeholder=" " required>
                    <label for="endereco">Endereço</label>
                </div>
                <div class="floating-label">
                    <input type="text" name="cidade" placeholder=" " required>
                    <label for="cidade">Cidade</label>
                </div>
                <select name="account_type" class="floating-label" required>
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
            e.preventDefault();

            const loginContainer = document.querySelector('.login-container');
            const cadastroContainer = document.querySelector('.cadastro-container');

            // Esconde o login e mostra o cadastro
            loginContainer.classList.add('hidden');
            cadastroContainer.classList.remove('hidden');
            cadastroContainer.classList.add('active');

            loginContainer.style.zIndex = 1;
            cadastroContainer.style.zIndex = 2;
        });

        // Função para Mostrar Login
        document.getElementById('showLogin').addEventListener('click', function(e) {
            e.preventDefault();

            const loginContainer = document.querySelector('.login-container');
            const cadastroContainer = document.querySelector('.cadastro-container');

            // Esconde o cadastro e mostra o login
            cadastroContainer.classList.add('hidden');
            loginContainer.classList.remove('hidden');
            cadastroContainer.classList.remove('active');

            loginContainer.style.zIndex = 2;
            cadastroContainer.style.zIndex = 1;
        });

        if (window.location.hash === '#showCadastro') {
            const loginContainer = document.querySelector('.login-container');
            const cadastroContainer = document.querySelector('.cadastro-container');

            loginContainer.classList.add('hidden');
            cadastroContainer.classList.remove('hidden');
            cadastroContainer.classList.add('active');

            loginContainer.style.zIndex = 1;
            cadastroContainer.style.zIndex = 2;
        }

        // Adiciona a animação de deslocamento do texto para cima do input
        document.querySelectorAll('.floating-label input').forEach(input => {
            input.addEventListener('focus', () => {
                input.parentElement.classList.add('active');
            });
            input.addEventListener('blur', () => {
                if (!input.value) {
                    input.parentElement.classList.remove('active');
                }
            });
        });
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

  
