<?php
session_start();
require_once '../models/user.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../controllers/user_controller.php?acao=check_auth");
    exit();
}

$user = new User();
$user->loadById($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booknnection - Perfil de Usuário</title>
    <link rel="stylesheet" href="../public/estilos_css/style_perfil_usuario.css">
    <link rel="stylesheet" href="../public/estilos_css/style_pagina_principal.css">
    <link rel="stylesheet" href="../public/estilos_css/popup.css">
</head>
<body>
    <header class="is-transitioned">
        <div class="header-content">
            <h1>
                <a href="../views/pagina_principal.html" class="title-link">Booknnection</a>
            </h1>
            <form class="search-form">
                <input type="text" placeholder="Pesquisar...">
                <button type="submit">Buscar</button>
            </form>
            <div class="profile-icon">
                <a href="../views/pagina_perfil.php">
                    <img src="../public/imagens/Corgi.png" alt="Ícone de Perfil">
                </a>
            </div>
        </div>
    </header>

    <!-- Main container -->
    <div class="main-container main-content is-transitioned">
        <!-- Sidebar esquerda reutilizada -->
        <aside class="sidebar-left">
            <nav>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Explorar</a></li>
                    <li><a href="#">Mensagens</a></li>
                    <li><a href="#">Notificações</a></li>
                    <li><a href="#">Perfil</a></li>
                    <li><a href="#">Configurações</a></li>
                </ul>
            </nav>
            <button id="add-book-btn" class="sidebar-button">Adicionar Livro!</button>
        </aside>

        <!-- Seção de perfil do usuário -->
        <main class="profile-content">
            <div class="profile-header">
                <div class="profile-banner">
                    <img src="../public/imagens/paisagem.jpg" alt="Banner do Usuário">
                </div>
                <div class="profile-info">
                    <div class="profile-avatar">
                        <img src="../public/imagens/Corgi.png" alt="Avatar do Usuário">
                    </div>
                    <div class="profile-details">
                        <h2><?php echo $user->getUsername(); ?></h2>
                        <p>@<?php echo $user->getUsername(); ?></p>
                        <button class="follow-button">Seguir</button>
                    </div>
                </div>
            </div>

            <!-- Seção de postagens -->
            <div class="feed">
                <div class="post">
                    <h2>Postagem do Usuário</h2>
                    <p>Este é um exemplo de uma postagem...</p>
                </div>
                <div class="post">
                    <h2>Postagem do Usuário</h2>
                    <p>Conteúdo da postagem 2...</p>
                </div>
            </div>
        </main>

        <!-- Sidebar direita reutilizada -->
        <aside class="sidebar-right">
            <div class="suggestions">
                <h2>Talvez você curta</h2>
                <ul>
                    <li><a href="#">Usuário A</a> <button class="follow-suggestion">Seguir</button></li>
                    <li><a href="#">Usuário B</a> <button class="follow-suggestion">Seguir</button></li>
                    <li><a href="#">Usuário C</a> <button class="follow-suggestion">Seguir</button></li>
                </ul>
            </div>
        </aside>
    </div>

    <!-- Footer reutilizado -->
    <footer class="is-transitioned">
        <p>&copy; 2024 Booknnection</p>
    </footer>

    <!-- Pop-up -->
    <div id="popup" class="popup-container">
        <div class="popup-content">
            <span id="close-popup" class="popup-close">&times;</span>
            <div class="popup-image-container">
                <img src="../public/imagens/book-cover.jpg" alt="Book Cover" class="popup-image">
            </div>
            <h2>Descrição:</h2>
            <input type="text" placeholder="Adicione a descrição aqui...">
            <button>Salvar</button>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Remove the transition class for the main content after animation ends
            setTimeout(function() {
                document.querySelector('.main-content').classList.remove('is-transitioned');
                document.querySelector('header').classList.remove('is-transitioned');
                document.querySelector('footer').classList.remove('is-transitioned');
            }, 5000); // Same duration as the animation (2s)
        });

        // Abrir e fechar o pop-up
        const popup = document.getElementById("popup");
        const addBookBtn = document.getElementById("add-book-btn");
        const closePopup = document.getElementById("close-popup");
        const mainContent = document.querySelector('.main-container');

        // Função para abrir o pop-up com animação
        addBookBtn.onclick = function() {
            popup.classList.add("open-popup");
            mainContent.classList.add("darken");
        }

        // Fechar o pop-up ao clicar no botão de fechar ou fora dele
        closePopup.onclick = function() {
            closePopupFunction();
        }

        window.onclick = function(event) {
            if (event.target == popup) {
                closePopupFunction();
            }
        }

        function closePopupFunction() {
            popup.classList.remove("open-popup");
            mainContent.classList.remove("darken");
        }
    </script>
</body>
</html>
