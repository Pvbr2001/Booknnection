<?php
session_start();
require_once '../models/user.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../controllers/user_controller.php?acao=check_auth");
    exit();
}

$user = new User();
$user->loadById($_SESSION['user_id']);
$cidade = $user->getCidade();
$posts = $user->exibirPostsPorCidade($cidade);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booknnection - Página Principal</title>
    <link rel="stylesheet" href="../public/estilos_css/header.css">
    <link rel="stylesheet" href="../public/estilos_css/feed.css">
    <link rel="stylesheet" href="../public/estilos_css/popup.css">
    <link rel="stylesheet" href="../public/estilos_css/sidebar.css">
    <link rel="stylesheet" href="../public/estilos_css/side_popup.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>
<body>
    <header class="header">
        <div class="header-content">
            <h1>
                <a href="../views/pagina_principal.php" class="title-link">Booknnection</a>
            </h1>
            <form class="search-form">
                <input type="text" placeholder="Pesquisar...">
                <button type="submit">Buscar</button>
            </form>
            <div class="header-icon">
                <a href="../views/pagina_perfil.php">
                    <img src="../public/imagens/Corgi.png" alt="Ícone de header">
                </a>
                <button id="open-side-popup" class="customization_popup_trigger">
                <span class="material-symbols-outlined">menu</span>
                </button>

            </div>
        </div>
    </header>

    <!-- Main container -->
    <div class="main-container">
        <!-- Sidebar esquerda -->
        <aside class="sidebar-left">
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

        <!-- Seção principal -->
        <main class="profile-content">
            <!-- Seção de postagens -->
            <div class="feed">
                <?php
                foreach ($posts as $post) {
                    echo "<div class='post'>";
                    echo "<h2>" . htmlspecialchars($post['titulo']) . "</h2>";
                    echo "<p>" . htmlspecialchars($post['descricao']) . "</p>";
                    echo "<img src='" . htmlspecialchars($post['caminho_capa']) . "' alt='Capa do Livro'>";
                    echo "<form action='../controllers/post_actions.php' method='POST' style='display:inline;'>";
                    echo "<input type='hidden' name='acao' value='curtir_post'>";
                    echo "<input type='hidden' name='id_post' value='" . $post['id'] . "'>";
                    echo "<button type='submit'>Curtir (" . $post['curtidas'] . ")</button>";
                    echo "</form>";
                    echo "<form action='../controllers/post_actions.php' method='POST' style='display:inline;'>";
                    echo "<input type='hidden' name='acao' value='salvar_post'>";
                    echo "<input type='hidden' name='id_post' value='" . $post['id'] . "'>";
                    echo "<button type='submit'>Salvar</button>";
                    echo "</form>";
                    echo "</div>";
                }
                ?>
            </div>
        </main>

        <!-- Sidebar direita -->
        <aside class="sidebar-right">
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

    <!-- Pop-up para criar post -->
    <div id="create-post-popup" class="popup-container">
        <div class="popup-content">
            <span id="close-create-post-popup" class="popup-close">&times;</span>
            <h2>Criar Post</h2>
            <form action="../controllers/user_controller.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="acao" value="criar_post">
                <input type="hidden" name="id_livro" id="id_livro" value="">
                <input type="hidden" name="cidade" id="cidade" value="<?php echo $user->getCidade(); ?>">
                <label for="titulo_post">Título do Post:</label>
                <input type="text" name="titulo" id="titulo_post" required>

                <label for="descricao_post">Descrição do Post:</label>
                <textarea name="descricao" id="descricao_post" required></textarea>

                <button type="submit">Criar Post</button>
            </form>
        </div>
    </div>

    <!-- Side Pop-up -->
    <div class="customization_popup" role="alert">
        <div class="customization_popup_container">
            <p>Opções de Configurações</p>
            <ul>
                <li><a href="#">Configuração 1</a></li>
                <li><a href="#">Configuração 2</a></li>
                <li><a href="../controllers/User_Controller.php?acao=logout">Logout</a></li>
            </ul>
            <a href="#0" class="customization_popup_close img-replace">X</a>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Função para aplicar a transição de fade-in
            function fadeIn(element, duration) {
                element.style.opacity = 0;
                element.style.transition = `opacity ${duration}s`;
                setTimeout(() => {
                    element.style.opacity = 1;
                }, 0);
            }

            // Função para aplicar a transição de baixo para cima
            function slideUp(element, duration) {
                element.style.transform = 'translateY(100%)';
                element.style.transition = `transform ${duration}s`;
                setTimeout(() => {
                    element.style.transform = 'translateY(0)';
                }, 0);
            }

            // Aplicar a transição de fade-in aos elementos
            const profileContent = document.querySelector('.profile-content');
            const sidebarLeft = document.querySelector('.sidebar-left');
            const sidebarRight = document.querySelector('.sidebar-right');
            const header = document.querySelector('header');

            fadeIn(profileContent, 1.5);
            fadeIn(sidebarLeft, 1.5);
            fadeIn(sidebarRight, 1.5);
            fadeIn(header, 1.5);

            // Aplicar a transição de baixo para cima ao feed e ao profile-header
            const feed = document.querySelector('.feed');
            slideUp(feed, 1.5);

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

        // JavaScript para o side pop-up
        jQuery(document).ready(function($) {
            $('.customization_popup_trigger').on('click', function(event) {
                event.preventDefault();
                $('.customization_popup').addClass('is-visible');
            });
            $('.customization_popup').on('click', function(event) {
                if ($(event.target).is('.customization_popup_close') || $(event.target).is('.customization_popup')) {
                    event.preventDefault();
                    $(this).removeClass('is-visible');
                }
            });
            $(document).keyup(function(event) {
                if (event.which == '27') {
                    $('.customization_popup').removeClass('is-visible');
                }
            });
        });
    </script>
</body>
</html>
<script type="module">
  import Typebot from 'https://cdn.jsdelivr.net/npm/@typebot.io/js@0.3/dist/web.js'

  Typebot.initBubble({
    typebot: "customer-support-s9a4usx",
    theme: {
      button: { backgroundColor: "#0042DA" },
      chatWindow: { backgroundColor: "#fff" },
    },
  });
</script>

