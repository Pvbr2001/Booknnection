<?php
session_start();
require_once '../models/user.php';
require_once '../models/post.php';
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../controllers/user_controller.php?acao=check_auth");
    exit();
}
$database = new Database();
$conn = $database->getConnection(); // Método para obter a conexão

require_once '../models/user.php';
require_once '../models/post.php';

if (!isset($_SESSION['user_id'])) {
   header("Location: ../controllers/user_controller.php?acao=check_auth");
   exit();
}

$post = new Post(); 
$user = new User();
$user->loadById($_SESSION['user_id']);
$livros = $user->exibirLivrosFeed($_SESSION['user_id']);
$postsSalvos = $post->exibirPostsSalvos($_SESSION['user_id']);
$postsDoUsuario = $post->exibirPostsDoUsuario($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booknnection - Perfil</title>
    <link rel="stylesheet" href="../public/estilos_css/header.css">
    <link rel="stylesheet" href="../public/estilos_css/feed.css">
    <link rel="stylesheet" href="../public/estilos_css/popup.css">
    <link rel="stylesheet" href="../public/estilos_css/sidebar.css">
    <link rel="stylesheet" href="../public/estilos_css/side_popup.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>
<body>
    
    <?php include '../views/partials/header.php'; ?>

    <div class="main-container">
        <!-- Sidebar esquerda -->
        <?php include '../views/partials/sidebar_left.php'; ?>

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

            <!-- Navegação entre feed, lista de livros e posts salvos -->
            <div class="navigation">
                <button class="nav-button" id="feed-btn">Feed</button>
                <button class="nav-button" id="books-btn">Lista de Livros</button>
                <button class="nav-button" id="saved-posts-btn">Posts Salvos</button>
            </div>

            <!-- Seção de postagens -->
            <div id="feed" class="feed">
            <?php
                foreach ($postsDoUsuario as $post) {
                    // Contar o número de curtidas para cada post
                    $sqlCurtidas = "SELECT COUNT(*) as totalCurtidas FROM curtidas WHERE id_post = ?";
                    $stmtCurtidas = $conn->prepare($sqlCurtidas);
                    $stmtCurtidas->bind_param("i", $post['id']);
                    $stmtCurtidas->execute();
                    $resultCurtidas = $stmtCurtidas->get_result();
                    $curtidas = $resultCurtidas->fetch_assoc()['totalCurtidas'];

                    echo "<div class='post'>";
                    echo "<h2>" . htmlspecialchars($post['titulo']) . "</h2>";
                    echo "<p>" . htmlspecialchars($post['descricao']) . "</p>";
                    echo "<img src='" . htmlspecialchars($post['caminho_capa']) . "' alt='Capa do Livro'>";
                    
                    // Botão de curtir com contador dinâmico
                    echo "<form action='../controllers/post_actions.php' method='POST' style='display:inline;'>";
                    echo "<input type='hidden' name='acao' value='curtir_post'>";
                    echo "<input type='hidden' name='id_post' value='" . $post['id'] . "'>";
                    echo "<button type='submit'>Curtir (" . $curtidas . ")</button>";
                    echo "</form>";
                    
                    // Outras ações (trocar livro, salvar post)
                    echo "<form action='../controllers/post_actions.php' method='POST' style='display:inline;' id='swap-book-form'>";
                    echo "<input type='hidden' name='acao' value='trocar_livro'>";
                    echo "<input type='hidden' name='id_post' value='" . $post['id'] . "'>";
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

            <!-- Seção de livros -->
            <div id="books" class="feed" style="display:none;">
                <?php
                foreach ($livros as $livro) {
                    echo "<div class='post'>";
                    echo "<h2>" . htmlspecialchars($livro['titulo']) . "</h2>";
                    echo "<p>Autor: " . htmlspecialchars($livro['autor']) . "</p>";
                    echo "<img src='" . htmlspecialchars($livro['caminho_capa']) . "' alt='Capa do Livro'>";
                    echo "<button onclick='openCreatePostPopup(" . $livro['id'] . ")'>Criar Post</button>";
                    echo "</div>";
                }
                ?>
            </div>

            <!-- Seção de posts salvos -->
            <div id="saved-posts" class="feed" style="display:none;">
                <?php
                foreach ($postsSalvos as $post) {
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
        <?php include '../views/partials/sidebar_right.php'; ?>
    </div>

    <!-- Pop-up -->
    <?php include '../views/partials/pop_ups.php'; ?>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../public/codigo_java.js"></script>
    
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
