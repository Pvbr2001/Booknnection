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
$cidade = $user->getCidade();
$post = new Post();
$posts = $post->exibirPostsPorCidade($cidade);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Booknnection - Feed</title>
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
    <div class="container-fluid">
        <div class="row">
            <div class="col-left sidebar" id="sidebar-left">
                <div class="card mb-4">
                    <div class="card-header toggle-header card-title-no-underline" data-target="categorias">
                        Categorias
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header toggle-header card-title-no-underline" data-target="topicos-populares">
                        Tópicos Populares
                    </div>
                    <div class="card-body" id="topicos-populares">
                        <ul>
                            <li><a href="#">Tópico 1</a></li>
                            <li><a href="#">Tópico 2</a></li>
                            <li><a href="#">Tópico 3</a></li>
                        </ul>
                        <button id="add-book-btn" class="btn btn-primary">Adicionar Livro!</button>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header toggle-header card-title-no-underline" data-target="ultimo-livro-trocado">
                        &Uacute;ltimo Livro Trocado
                    </div>
                    <div class="card-body" id="ultimo-livro-trocado">
                        <img class="img-fluid" src="../public/imagens/book-cover.jpg" alt="Capa do &Uacute;ltimo Livro Trocado" style="width: 50%; opacity: 0.5; margin: 0 auto;">
                        <p class="mt-3">Ultimo livro trocado</p>
                    </div>
                </div>
            </div>
            <div class="col-middle">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="mb-4">
                            <small>For You</small>
                        </h1>
                        <main class="profile-content">
                            <div class="feed">
                                <?php foreach ($posts as $post): ?>
                                    <?php
                                    $sqlCurtidas = "SELECT COUNT(*) as totalCurtidas FROM curtidas WHERE id_post = ?";
                                    $stmtCurtidas = $conn->prepare($sqlCurtidas);
                                    $stmtCurtidas->bind_param("i", $post['id']);
                                    $stmtCurtidas->execute();
                                    $resultCurtidas = $stmtCurtidas->get_result();
                                    $curtidas = $resultCurtidas->fetch_assoc()['totalCurtidas'];
                                    ?>
                                    <div class="card mb-4">
                                        <img class="card-img-top" src="<?= htmlspecialchars($post['caminho_capa']); ?>" alt="Capa do Livro" style="width: auto; height: 500px; object-fit: cover; margin: 0 auto;">
                                        <div class="card-body">
                                            <h5 class="card-title"><?= htmlspecialchars($post['titulo']); ?></h5>
                                            <p class="card-text"><?= htmlspecialchars($post['descricao']); ?></p>
                                            <div class="d-flex gap-2">
                                                <form action="../controllers/post_actions.php" method="POST" style="display:inline;">
                                                    <input type="hidden" name="acao" value="curtir_post">
                                                    <input type="hidden" name="id_post" value="<?= $post['id']; ?>">
                                                    <button class="btn btn-primary" type="submit">
                                                        <i class="material-icons">thumb_up</i> Curtir (<?= $curtidas; ?>)
                                                    </button>
                                                </form>
                                                <form action="../controllers/post_actions.php" method="POST" style="display:inline;" id="send-request-form">
                                                    <input type="hidden" name="acao" value="trocar_livro">
                                                    <input type="hidden" name="id_post" value="<?= $post['id']; ?>">
                                                    <button type="button" id="send-request-btn" class="btn btn-secondary ml-2 send-request-btn" data-image="<?= htmlspecialchars($post['caminho_capa']); ?>">
                                                        <i class="material-icons">swap_horiz</i> Enviar Solicitação
                                                    </button>
                                                </form>
                                                <form action="../controllers/post_actions.php" method="POST" style="display:inline;">
                                                    <input type="hidden" name="acao" value="salvar_post">
                                                    <input type="hidden" name="id_post" value="<?= $post['id']; ?>">
                                                    <button class="btn btn-success ml-2" type="submit">
                                                        <i class="material-icons">bookmark</i> Salvar
                                                    </button>
                                                </form>
                                                <form action="post_aberto.php" method="GET" style="display:inline;">
                                                    <input type="hidden" name="id_post" value="<?= $post['id']; ?>">
                                                    <button class="btn btn-info ml-2" type="submit">
                                                        <i class="material-icons">comment</i> Ver Comentários
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="card-footer text-muted">
                                            Postado por <a href="#">Usuário</a> no dia <?= date('d/m/Y', strtotime($post['data_post'])); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </main>
                        <div id="add-book-popup" class="popup-container">
                            <div class="popup-content form-container">
                                <span id="close-add-book-popup" class="popup-close">&times;</span>
                                <h2>Adicionar Livro</h2>
                                <form action="../controllers/user_controller.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="acao" value="adicionar_livro">
                                    <div class="floating-label">
                                        <input type="text" name="titulo" id="titulo" required>
                                        <label for="titulo">Título do livro</label>
                                    </div>
                                    <div class="floating-label">
                                        <input type="text" name="autor" id="autor" required>
                                        <label for="autor">Autor</label>
                                    </div>
                                    <div class="floating-label">
                                        <input type="text" name="isbn" id="isbn" required>
                                        <label for="isbn">ISBN</label>
                                    </div>
                                    <div class="floating-label">
                                        <input type="text" name="capa_tipo" id="capa_tipo">
                                        <label for="capa_tipo">Tipo de Capa</label>
                                    </div>
                                    <div class="floating-label">
                                        <input type="number" name="ano_lancamento" id="ano_lancamento" required>
                                        <label for="ano_lancamento">Ano de Lançamento</label>
                                    </div>
                                    <div class="floating-label">
                                        <input type="file" name="capa" id="capa">
                                        <label for="capa">Capa do Livro</label>
                                    </div>
                                    <button type="submit" class="btn-submit">Adicionar Livro ao Banco</button>
                                    <button type="submit" name="adicionar_lista" value="1" class="btn-submit">Adicionar Livro ao Banco e à Lista</button>
                                    <a href="#" id="show-isbn-search">Pesquisar por ISBN</a>
                                </form>
                            </div>
                        </div>
                        <div id="send-request-popup" class="popup-container">
                            <div class="popup-content">
                                <span id="close-send-request-popup" class="popup-close">&times;</span>
                                <h2>Enviar Solicitação</h2>
                                <form id="send-request-form" action="../controllers/post_actions.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="acao" value="trocar_livro">
                                    <input type="hidden" name="id_post" id="id_post" value="">
                                    <div class="post-info">
                                        <img src="" alt="Capa do Livro" id="imagem_post" style="width: auto; height: 300px;">
                                        <h1>Usuário atual: <span id="current-user"><?php echo $user->getNome(); ?></span> </h1>
                                        <h1>Dono do post: <span id="post-owner"><?php echo $post['nome']; ?></span> </h1>
                                    </div>
                                    <button type="submit" class="green-button">Confirmar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-right sidebar" id="sidebar-right">
                <div class="card mb-4">
                    <div class="card-header toggle-header card-title-no-underline" data-target="pesquisar">
                        Pesquisar
                    </div>
                    <div class="card-body" id="pesquisar">
                        <div class="input-group">
                            <input class="form-control" type="text" placeholder="Digite aqui...">
                            <button class="btn btn-secondary ml-2">Buscar</button>
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header toggle-header card-title-no-underline" data-target="notificacoes">
                        Notificações
                    </div>
                    <div class="card-body" id="notificacoes">
                        <aside class="sidebar-right">
                            <div class="sidebar-section">
                                <ul>
                                    <?php
                                    $id_usuario = $_SESSION['user_id'];
                                    $sql = "SELECT u.nome AS nome_usuario, ue.nome AS nome_usuario_emissor, p.titulo AS titulo_post, l.caminho_capa AS caminho_capa, n.id_post
                                            FROM notificacoes n
                                            JOIN posts p ON p.id = n.id_post
                                            JOIN usuario u ON u.id = n.id_usuario
                                            JOIN usuario ue ON ue.id = n.id_usuario_emissor
                                            JOIN livros l ON p.id_livro = l.id
                                            WHERE n.id_usuario = ?
                                            ORDER BY n.data_criacao DESC";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("i", $id_usuario);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<li class='notification-item' data-id='" . htmlspecialchars($row['id_post']) . "' data-image='" . htmlspecialchars($row['caminho_capa']) . "'>";
                                            echo "<strong>" . htmlspecialchars($row['nome_usuario_emissor']) . "</strong> solicitou uma troca para o livro: ";
                                            echo "<a href='troca_processo.php?id_post=" . htmlspecialchars($row['id_post']) . "'>" . htmlspecialchars($row['titulo_post']) . "</a>";
                                            echo "</li>";
                                        }
                                    } else {
                                        echo "<li>Nenhuma notificação recente.</li>";
                                    }
                                    $stmt->close();
                                    ?>
                                </ul>
                            </div>
                        </aside>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header toggle-header card-title-no-underline" data-target="solicitacoes">
                        Solicitações Enviadas
                    </div>
                    <div class="card-body" id="solicitacoes">
                        <aside class="sidebar-right">
                            <div class="sidebar-section">
                                <ul>
                                    <?php
                                    $sql = "SELECT u.nome AS nome_usuario, ue.nome AS nome_usuario_emissor, p.titulo AS titulo_post, l.caminho_capa AS caminho_capa, n.id_post
                                            FROM notificacoes n
                                            JOIN posts p ON p.id = n.id_post
                                            JOIN usuario u ON u.id = n.id_usuario
                                            JOIN usuario ue ON ue.id = n.id_usuario_emissor
                                            JOIN livros l ON p.id_livro = l.id
                                            WHERE n.id_usuario_emissor = ?
                                            ORDER BY n.data_criacao DESC";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("i", $id_usuario);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<li class='notification-item' data-id='" . htmlspecialchars($row['id_post']) . "' data-image='" . htmlspecialchars($row['caminho_capa']) . "'>";
                                            echo "<strong>" . htmlspecialchars($row['nome_usuario']) . "</strong> solicitou uma troca para o livro: ";
                                            echo "<a href='troca_processo.php?id_post=" . htmlspecialchars($row['id_post']) . "'>" . htmlspecialchars($row['titulo_post']) . "</a>";
                                            echo "</li>";
                                        }
                                    } else {
                                        echo "<li>Nenhuma solicitação enviada.</li>";
                                    }
                                    $stmt->close();
                                    ?>
                                </ul>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="isbn-search-popup" class="popup-container">
        <div class="popup-content">
            <span id="close-isbn-search-popup" class="popup-close">&times;</span>
            <h2>Pesquisar por ISBN</h2>
            <form action="../controllers/user_controller.php" method="POST">
                <input type="hidden" name="acao" value="pesquisar_isbn">
                <label for="isbn_search">ISBN:</label>
                <input type="text" name="isbn" id="isbn_search" required>
                <button type="submit">Pesquisar</button>
            </form>
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
</body>
</html>
