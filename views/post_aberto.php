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

$database = Database::getInstance();
$conn = $database->getConnection();

$user = new User();
$user->loadById($_SESSION['user_id']);

$post_id = $_GET['id_post'];
$post = new Post();
$post = $post->exibirPostPorId($post_id);

// Query to count likes for the post
$sqlCurtidas = "SELECT COUNT(*) as totalCurtidas FROM curtidas WHERE id_post = ?";
$stmtCurtidas = $conn->prepare($sqlCurtidas);
$stmtCurtidas->bind_param("i", $post_id);
$stmtCurtidas->execute();
$resultCurtidas = $stmtCurtidas->get_result();
$curtidas = $resultCurtidas->fetch_assoc()['totalCurtidas'];

// Query to get comments for the post
$sqlComentarios = "SELECT c.id, c.texto, c.data_comentario, u.nome AS nome_usuario
                   FROM comentarios c
                   JOIN usuario u ON c.id_usuario = u.id
                   WHERE c.id_post = ?";
$stmtComentarios = $conn->prepare($sqlComentarios);
$stmtComentarios->bind_param("i", $post_id);
$stmtComentarios->execute();
$resultComentarios = $stmtComentarios->get_result();
$comentarios = $resultComentarios->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Booknnection - Post</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../public/estilos_css/post_aberto.css">
</head>

<body>

    <header class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Booknnection</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Alterna navegação">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="pagina_principal.php">For You</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pagina_perfil.php">Perfil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Manual</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pagina_home.html">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pagina_configuracoes.php">Configurações</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../controllers/User_Controller.php?acao=logout">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <div class="container mt-4">
        <div class="back-icon">
            <a href="pagina_principal.php" class="btn btn-link">
                <i class="material-icons">arrow_back</i>
            </a>
        </div>
        <div class="card mb-4">
            <img class="card-img-top" src="<?= htmlspecialchars($post['caminho_capa']); ?>" alt="Capa do Livro" style="width: auto; height: 500px; object-fit: cover; margin: 0 auto;">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($post['titulo']); ?></h5>
                <p class="card-text"><?= htmlspecialchars($post['descricao']); ?></p>
                <div class="d-flex gap-2">
                    <!-- Like button with dynamic counter -->
                    <form action="../controllers/post_actions.php" method="POST" style="display:inline;">
                        <input type="hidden" name="acao" value="curtir_post">
                        <input type="hidden" name="id_post" value="<?= $post['id']; ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="material-icons">thumb_up</i> Curtir (<?= $curtidas; ?>)
                        </button>
                    </form>

                    <!-- Save post button -->
                    <form action="../controllers/post_actions.php" method="POST" style="display:inline;">
                        <input type="hidden" name="acao" value="salvar_post">
                        <input type="hidden" name="id_post" value="<?= $post['id']; ?>">
                        <button class="btn btn-success ml-2" type="submit">
                            <i class="material-icons">bookmark</i> Salvar
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-footer text-muted">
                Postado por <a href="#"><?= htmlspecialchars($post['nome']); ?></a> no dia <?= date('d/m/Y', strtotime($post['data_post'])); ?>
            </div>
        </div>

        <!-- Comments section -->
        <div class="card mb-4">
            <div class="card-header">
                Comentários
            </div>
            <div class="card-body" id="comments-section">
                <?php foreach ($comentarios as $comentario): ?>
                    <div class="comment">
                        <strong><?= htmlspecialchars($comentario['nome_usuario']); ?></strong>
                        <p><?= htmlspecialchars($comentario['texto']); ?></p>
                        <small class="text-muted"><?= date('d/m/Y H:i', strtotime($comentario['data_comentario'])); ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="card-footer">
                <button id="add-comment-btn" class="btn btn-primary">Adicionar Comentário</button>
                <div id="comment-form" style="display: none;">
                    <form id="comment-submit-form">
                        <input type="hidden" name="acao" value="adicionar_comentario">
                        <input type="hidden" name="id_post" value="<?= $post['id']; ?>">
                        <div class="form-group">
                            <label for="comentario">Adicionar Comentário</label>
                            <textarea class="form-control" id="comentario" name="comentario" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Comentar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>Desenvolvido Para TCC Senai</p>
    </footer>

    <!-- Incluir jQuery completo -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha384-tsQFqpEReu7ZLhBV2VZlAu7zcOV+rXbYlF2cqB8txI/8aZajjp4Bqd+V6D5IgvKT"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
        crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
        crossorigin="anonymous"></script>
    <script>
        document.getElementById('add-comment-btn').addEventListener('click', function() {
            var commentForm = document.getElementById('comment-form');
            if (commentForm.style.display === 'none') {
                commentForm.style.display = 'block';
            } else {
                commentForm.style.display = 'none';
            }
        });

        $('#comment-submit-form').on('submit', function(event) {
            event.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                type: 'POST',
                url: '../controllers/post_actions.php',
                data: formData,
                success: function(response) {
                    // Limpar o formulário
                    $('#comment-submit-form')[0].reset();
                    // Recarregar os comentários
                    loadComments(<?= $post['id']; ?>);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        function loadComments(postId) {
            $.ajax({
                type: 'GET',
                url: '../controllers/load_comments.php',
                data: { id_post: postId },
                success: function(response) {
                    $('#comments-section').html(response);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }

        // Carregar comentários inicialmente
        loadComments(<?= $post['id']; ?>);
    </script>
</body>

</html>
