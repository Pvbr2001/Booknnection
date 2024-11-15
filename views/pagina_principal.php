<?php
session_start();

require_once '../models/user.php';
require_once '../models/Post.php'; 
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../controllers/user_controller.php?acao=check_auth");
    exit();
}
$database = new Database();
$conn = $database->getConnection(); // Método para obter a conexão


require_once '../models/user.php';
require_once '../models/Post.php'; // Include the post.php file

if (!isset($_SESSION['user_id'])) {
    header("Location: ../controllers/user_controller.php?acao=check_auth");
    exit();
}


$user = new User();
$user->loadById($_SESSION['user_id']);
$cidade = $user->getCidade();

$post = new Post(); 
$posts = $post->exibirPostsPorCidade($cidade);
//var_dump($posts);
//exit();

$post = new Post(); // Create an instance of the Post class
$posts = $post->exibirPostsPorCidade($cidade); // Use the Post class to fetch posts

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booknnection - Feed</title>
    <link rel="stylesheet" href="../public/estilos_css/header.css">
    <link rel="stylesheet" href="../public/estilos_css/feed.css">
    <link rel="stylesheet" href="../public/estilos_css/popup.css">
    <link rel="stylesheet" href="../public/estilos_css/sidebar.css">
    <link rel="stylesheet" href="../public/estilos_css/side_popup.css">
    <link rel="stylesheet" href="../public/estilos_css/pending.css">
    <link rel="stylesheet" href="../public/estilos_css/popup_troca.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>
<body>

    <?php include '../views/partials/header.php'; ?>

    <!-- Main container -->
    <div class="main-container">
        <!-- Sidebar esquerda -->
        <?php include '../views/partials/sidebar_left.php'; ?>

        <!-- Seção principal -->
        <main class="profile-content">
            <!-- Seção de postagens -->
            <div class="feed">
                <?php
                foreach ($posts as $post) {
                    // Código para exibir o post caso ele exista
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
                    echo "<button type='button' id='swap-book-btn' class='swap-book-btn' data-image='" . htmlspecialchars($post['caminho_capa']) . "'>Trocar Livro</button>";
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

