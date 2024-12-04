<?php
session_start();
require_once '../models/user.php';
require_once '../models/post.php';
require_once '../config/database.php';

// Verifica se o usuário está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../controllers/user_controller.php?acao=check_auth");
    exit();
}

// Conexão com o banco de dados
$database = Database::getInstance();
$conn = $database->getConnection();

// Instanciação de objetos das classes User e Post
$user = new User();
$post = new Post();

// Carrega os dados do usuário atual
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
    <!-- Inclusão de estilos CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="../public/estilos_css/perfil.css">
</head>
<body>

<header class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="../views/pagina_principal.php">Booknnection</a>
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
        <!-- Sidebar esquerda -->
        <div class="col-left sidebar">
            <div class="card mb-4">
                <div class="card-header toggle-header card-title-no-underline" data-target="categorias">
                    Categorias
                </div>
                <div class="card-body" id="categorias">
                    <div class="row">
                        <div class="col-lg-6">
                            <ul class="list-unstyled">
                                <li><a href="#">Web Design</a></li>
                                <li><a href="#">JavaScript</a></li>
                                <li><a href="#">CSS</a></li>
                            </ul>
                        </div>
                        <div class="col-lg-6">
                            <ul class="list-unstyled">
                                <li><a href="#">HTML</a></li>
                                <li><a href="#">React</a></li>
                                <li><a href="#">Node.js</a></li>
                            </ul>
                        </div>
                    </div>
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
                <div class="card-header toggle-header card-title-no-underline" data-target="artigos-recentes">
                    Artigos Recentes
                </div>
                <div class="card-body" id="artigos-recentes">
                    <ul class="list-unstyled">
                        <li><a href="#">Aprenda como criar um site responsivo</a></li>
                        <li><a href="#">3 coisas que você precisa saber sobre CSS</a></li>
                        <li><a href="#">5 recursos para aprender JavaScript</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Seção de perfil do usuário -->
        <div class="col-middle">
            <div class="profile-header">
                <div class="profile-banner">
                    <img src="../public/imagens/paisagem.jpg" alt="Banner do Usuário" class="img-fluid">
                </div>
                <div class="profile-info">
                    <div class="profile-avatar">
                        <img src="<?php echo htmlspecialchars($user->getFotoPerfil()); ?>" alt="Avatar do Usuário" class="rounded-circle">
                    </div>
                    <div class="profile-details">
                        <h2><?php echo $user->getUsername(); ?></h2>
                        <p>@<?php echo $user->getUsername(); ?></p>
                        <button class="btn btn-primary">Seguir</button>
                    </div>
                </div>
            </div>

            <!-- Navegação entre feed, lista de livros e posts salvos -->
            <div class="navigation">
                <button class="btn btn-secondary nav-button" id="feed-btn">Feed</button>
                <button class="btn btn-secondary nav-button" id="books-btn">Lista de Livros</button>
                <button class="btn btn-secondary nav-button" id="saved-posts-btn">Posts Salvos</button>
            </div>

            <!-- Seção de postagens -->
            <div id="feed" class="feed">
                <?php foreach ($postsDoUsuario as $post): ?>
                    <?php
                        // Contar o número de curtidas para cada post
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
                                <!-- Like button with dynamic counter -->
                                <form action="../controllers/post_actions.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="acao" value="curtir_post">
                                    <input type="hidden" name="id_post" value="<?= $post['id']; ?>">
                                    <button class="btn btn-primary" type="submit">Curtir (<?= $curtidas; ?>)</button>
                                </form>

                                <!-- Save post button -->
                                <form action="../controllers/post_actions.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="acao" value="salvar_post">
                                    <input type="hidden" name="id_post" value="<?= $post['id']; ?>">
                                    <button class="btn btn-success" type="submit">Salvar</button>
                                </form>
                            </div>
                        </div>
                        <div class="card-footer text-muted">
                            Postado por <a href="#">Usuário</a> no dia <?= date('d/m/Y', strtotime($post['data_post'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Seção de livros -->
            <div id="books" class="feed" style="display:none;">
                <div class="row">
                    <?php foreach ($livros as $livro): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <img class="card-img-top book-icon" src="<?= htmlspecialchars($livro['caminho_capa']); ?>" alt="Capa do Livro" data-id="<?= $livro['id']; ?>" style="width: 100%; height: auto; object-fit: cover; cursor: pointer;">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($livro['titulo']); ?></h5>
                                    <p class="card-text">Autor: <?= htmlspecialchars($livro['autor']); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Seção de posts salvos -->
            <div id="saved-posts" class="feed" style="display:none;">
                <?php foreach ($postsSalvos as $post): ?>
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
                                    <button class="btn btn-primary" type="submit">Curtir (<?= $post['curtidas']; ?>)</button>
                                </form>

                                <!-- Save post button -->
                                <form action="../controllers/post_actions.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="acao" value="salvar_post">
                                    <input type="hidden" name="id_post" value="<?= $post['id']; ?>">
                                    <button class="btn btn-success" type="submit">Salvar</button>
                                </form>
                            </div>
                        </div>
                        <div class="card-footer text-muted">
                            Postado por <a href="#">Usuário</a> no dia <?= date('d/m/Y', strtotime($post['data_post'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Sidebar direita -->
        <div class="col-right sidebar">
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
                    <ul>
                        <?php
                        require_once '../config/database.php';

                        // Conectar ao banco de dados
                        $database = Database::getInstance();
                        $conn = $database->getConnection();

                        // Buscar notificações para o usuário atual
                        $id_usuario = $_SESSION['user_id'];
                        $sql = "SELECT u.nome AS nome_usuario, ue.nome AS nome_usuario_emissor, p.titulo AS titulo_post
                                FROM notificacoes n
                                JOIN posts p ON p.id = n.id_post
                                JOIN usuario u ON u.id = n.id_usuario
                                JOIN usuario ue ON ue.id = n.id_usuario_emissor
                                WHERE n.id_usuario = ?
                                ORDER BY n.data_criacao DESC";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $id_usuario);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Exibir notificações
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<li>";
                                echo "<strong>" . htmlspecialchars($row['nome_usuario_emissor']) . "</strong> solicitou uma troca para o livro: ";
                                echo "<a href='#'>" . htmlspecialchars($row['titulo_post']) . "</a>";
                                echo "</li>";
                            }
                        } else {
                            echo "<li>Nenhuma notificação recente.</li>";
                        }

                        $stmt->close();
                        $conn->close();
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Popup de adicionar livro -->
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

<!-- Pop-up de pesquisa por ISBN -->
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

<!-- Pop-up para atualizar telefone -->
<div id="update-telefone-popup" class="popup-container">
    <div class="popup-content">
        <span id="close-update-telefone-popup" class="popup-close">&times;</span>
        <h2>Atualizar Telefone</h2>
        <form action="../controllers/user_controller.php" method="POST">
            <input type="hidden" name="acao" value="atualizar_telefone">
            <label for="telefone">Telefone:</label>
            <input type="text" name="telefone" id="telefone" required>
            <button type="submit">Atualizar</button>
        </form>
    </div>
</div>

<footer class="footer">
    <p>Desenvolvido Para TCC Senai</p>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../public/adicionar_livro.js"></script>
<script src="../public/perfil_opcoes.js"></script>
<script src="../public/toggle.js"></script>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

</body>
</html>
