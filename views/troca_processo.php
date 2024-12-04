<?php
session_start();
require_once '../models/user.php';
require_once '../models/Post.php';
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../controllers/user_controller.php?acao=check_auth");
    exit();
}

$database = Database::getInstance();
$conn = $database->getConnection();
$user = new User();
$user->loadById($_SESSION['user_id']);
$post = new Post();

$id_post = $_GET['id_post'];
$post_info = $post->exibirPostPorId($id_post);
$post_owner = $post->getNome($id_post);
$post_title = $post->getTitulo($id_post);

$sql = "SELECT u.nome AS nome_usuario_emissor, u.telefone AS telefone_emissor
        FROM notificacoes n
        JOIN usuario u ON u.id = n.id_usuario_emissor
        WHERE n.id_post = ? AND n.id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_post, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$emissor_info = $result->fetch_assoc();

$livros_usuario = $user->exibirLivrosFeed($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Booknnection - Troca de Livro</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../public/estilos_css/principal.css">
</head>
<body>
    <header class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Booknnection</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Alterna navegação">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="#">For You<span class="sr-only">(Página atual)</span></a>
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
            <img class="card-img-top" src="<?= htmlspecialchars($post_info['caminho_capa']); ?>" alt="Capa do Livro" style="width: auto; height: 500px; object-fit: cover; margin: 0 auto;">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($post_info['titulo']); ?></h5>
                <p class="card-text"><?= htmlspecialchars($post_info['descricao']); ?></p>
                <div class="d-flex gap-2">
                    <form action="../controllers/post_actions.php" method="POST" style="display:inline;">
                        <input type="hidden" name="acao" value="aceitar_troca">
                        <input type="hidden" name="id_post" value="<?= $post_info['id']; ?>">
                        <button class="btn btn-success" type="submit">
                            <i class="material-icons">check</i> Aceitar Troca
                        </button>
                    </form>
                    <a href="https://wa.me/<?= $emissor_info['telefone_emissor']; ?>" class="btn btn-primary ml-2">
                        <i class="material-icons">whatsapp</i> Entrar em Contato
                    </a>
                    <button class="btn btn-danger finalizar-troca-btn" data-id-post="<?= $post_info['id']; ?>">
                        <i class="material-icons">check</i> Finalizar Troca
                    </button>
                </div>
            </div>
            <div class="card-footer text-muted">
                Postado por <a href="#"><?= htmlspecialchars($post_owner); ?></a> no dia <?= date('d/m/Y', strtotime($post_info['data_post'])); ?>
            </div>
        </div>
    </div>
    <footer class="footer">
        <p>Desenvolvido Para TCC Senai</p>
    </footer>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../public/adicionar_livro.js"></script>
    <script src="../public/enviar_request.js"></script>
    <script src="../public/toggle.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            $('.finalizar-troca-btn').click(function(e) {
                e.preventDefault();
                var id_post = $(this).data('id-post');

                $.ajax({
                    type: 'POST',
                    url: '../controllers/user_controller.php',
                    data: {
                        acao: 'finalizar_troca',
                        id_post: id_post
                    },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.status === 'success') {
                            alert(data.message);
                            // Atualizar o bloco de troca
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>
</body>
</html>
