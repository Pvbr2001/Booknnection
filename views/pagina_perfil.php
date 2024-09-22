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
    <link rel="stylesheet" href="../public/estilos_css/header.css">
    <link rel="stylesheet" href="../public/estilos_css/feed.css">
    <link rel="stylesheet" href="../public/estilos_css/popup.css">
    <link rel="stylesheet" href="../public/estilos_css/sidebar.css">
</head>
<body>
    <header class="header is-transitioned">
        <div class="header-content">
            <h1>
                <a href="../views/pagina_principal.html" class="title-link">Booknnection</a>
            </h1>
            <form class="search-form">
                <input type="text" placeholder="Pesquisar...">
                <button type="submit">Buscar</button>
            </form>
            <div class="header-icon">
                <a href="../views/pagina_perfil.php">
                    <img src="../public/imagens/Corgi.png" alt="Ícone de header">
                </a>
            </div>
        </div>
    </header>

    <!-- Main container -->
    <div class="main-container">
        <!-- Sidebar esquerda -->
        <aside class="sidebar-left is-transitioned">
            <div class="sidebar-section">
                <h2>Tópicos Populares</h2>
                <ul>
                    <li><a href="#">Tópico 1</a></li>
                    <li><a href="#">Tópico 2</a></li>
                    <li><a href="#">Tópico 3</a></li>
                </ul>
            </div>
            <button id="add-book-btn" class="sidebar-button">Adicionar Livro!</button>
            <button class="sidebar-button">Botão Lateral 1</button>
            <button class="sidebar-button">Botão Lateral 2</button>
        </aside>

        <!-- Seção de perfil do usuário -->
        <main class="profile-content is-transitioned">
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
                <?php
                $livros = $user->exibirLivrosFeed($_SESSION['user_id']);
                foreach ($livros as $livro) {
                    echo "<div class='post'>";
                    echo "<h2>" . htmlspecialchars($livro['titulo']) . "</h2>";
                    echo "<p>Autor: " . htmlspecialchars($livro['autor']) . "</p>";
                    echo "<img src='" . htmlspecialchars($livro['caminho_capa']) . "' alt='Capa do Livro'>";
                    echo "</div>";
                }
                ?>
            </div>
        </main>

        <!-- Sidebar direita -->
        <aside class="sidebar-right is-transitioned">
            <div class="sidebar-section">
                <h2>Tópicos Recentes</h2>
                <ul>
                    <li><a href="#">Tópico A</a></li>
                    <li><a href="#">Tópico B</a></li>
                    <li><a href="#">Tópico C</a></li>
                </ul>
            </div>
            <button class="sidebar-button">Botão Lateral A</button>
            <button class="sidebar-button">Botão Lateral B</button>
        </aside>
    </div>


    <!-- Pop-up -->
    <div id="popup" class="popup-container">
        <div class="popup-content">
            <span id="close-popup" class="popup-close">&times;</span>
            <h2>Adicionar Livro</h2>
            <form action="../controllers/user_controller.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="acao" value="adicionar_livro">
                <label for="titulo">Título do livro:</label>
                <input type="text" name="titulo" id="titulo" required>

                <label for="autor">Autor:</label>
                <input type="text" name="autor" id="autor" required>

                <label for="isbn">ISBN:</label>
                <input type="text" name="isbn" id="isbn" required>

                <label for="capa_tipo">Tipo de Capa:</label>
                <input type="text" name="capa_tipo" id="capa_tipo">

                <label for="ano_lancamento">Ano de Lançamento:</label>
                <input type="number" name="ano_lancamento" id="ano_lancamento" required>

                <label for="capa">Capa do Livro:</label>
                <input type="file" name="capa" id="capa">

                <button type="submit">Adicionar Livro ao Banco</button>
                <button type="submit" name="adicionar_lista" value="1">Adicionar Livro ao Banco e à Lista</button>
            </form>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Remove the transition class for the main content after animation ends
            setTimeout(function() {
                document.querySelector('.profile-content').classList.remove('is-transitioned');
                document.querySelector('header').classList.remove('is-transitioned');
                document.querySelector('footer').classList.remove('is-transitioned');
                document.querySelector('.sidebar-left').classList.remove('is-transitioned');
                document.querySelector('.sidebar-right').classList.remove('is-transitioned');
            }, 5000); // Duração da animação (5s)

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
        });
    </script>
</body>
</html>
