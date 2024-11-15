<?php
session_start();

// Required files
require_once '../models/user.php';
require_once '../models/Post.php'; 
require_once '../config/database.php';

// Redirect to authentication check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../controllers/user_controller.php?acao=check_auth");
    exit();
}

// Database connection
$database = new Database();
$conn = $database->getConnection(); // Obtain the database connection

// Load user and fetch posts by user's city
$user = new User();
$user->loadById($_SESSION['user_id']);
$cidade = $user->getCidade();

$post = new Post(); 
$posts = $post->exibirPostsPorCidade($cidade);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booknnection - Feed</title>
    <!-- Stylesheets -->
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
        <!-- Left Sidebar -->
        <?php include '../views/partials/sidebar_left.php'; ?>

        <!-- Main section -->
        <main class="profile-content">
            <!-- Post section -->
            <div class="feed">
                <?php foreach ($posts as $post): ?>
                    <?php
                    // Query to count likes for each post
                    $sqlCurtidas = "SELECT COUNT(*) as totalCurtidas FROM curtidas WHERE id_post = ?";
                    $stmtCurtidas = $conn->prepare($sqlCurtidas);
                    $stmtCurtidas->bind_param("i", $post['id']);
                    $stmtCurtidas->execute();
                    $resultCurtidas = $stmtCurtidas->get_result();
                    $curtidas = $resultCurtidas->fetch_assoc()['totalCurtidas'];
                    ?>

                    <div class="post">
                        <h2><?= htmlspecialchars($post['titulo']); ?></h2>
                        <p><?= htmlspecialchars($post['descricao']); ?></p>
                        <img src="<?= htmlspecialchars($post['caminho_capa']); ?>" alt="Capa do Livro">

                        <!-- Like button with dynamic counter -->
                        <form action="../controllers/post_actions.php" method="POST" style="display:inline;">
                            <input type="hidden" name="acao" value="curtir_post">
                            <input type="hidden" name="id_post" value="<?= $post['id']; ?>">
                            <button type="submit">Curtir (<?= $curtidas; ?>)</button>
                        </form>

                        <!-- Other actions (swap book, save post) -->
                        <form action="../controllers/post_actions.php" method="POST" style="display:inline;" id="swap-book-form">
                            <input type="hidden" name="acao" value="trocar_livro">
                            <input type="hidden" name="id_post" value="<?= $post['id']; ?>">
                            <button type="button" id="swap-book-btn" class="swap-book-btn" data-image="<?= htmlspecialchars($post['caminho_capa']); ?>">Trocar Livro</button>
                        </form>

                        <form action="../controllers/post_actions.php" method="POST" style="display:inline;">
                            <input type="hidden" name="acao" value="salvar_post">
                            <input type="hidden" name="id_post" value="<?= $post['id']; ?>">
                            <button type="submit">Salvar</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>

        <!-- Right Sidebar -->
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

