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
$conn = $database->getConnection();

// Load user and fetch posts by user's city
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

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f9;
        }

        .navbar {
            margin-bottom: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-weight: bold;
        }

        .card-footer {
            background-color: #f8f9fa;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            transition: background-color 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 15px 0;
        }

        .rounded-circle {
            max-width: 100px;
            border: 3px solid #007bff;
        }

        .sidebar {
            padding: 20px;
            background-color: #f8f9fa;
            height: 100%;
            border-right: 1px solid #ddd;
        }

        .col-left,
        .col-right {
            flex: 0 0 25%;
            max-width: 25%;
        }

        .col-middle {
            flex: 0 0 50%;
            max-width: 50%;
        }

        .input-group {
            margin-bottom: 15px;
        }

        .input-group input {
            border-radius: 4px 0 0 4px;
        }

        .input-group button {
            border-radius: 0 4px 4px 0;
        }

        .list-unstyled a:hover {
            text-decoration: underline;
        }

        /* Estilos para o pop-up */
        .popup-container {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            transition: all 0.4s ease;
        }

        .popup-content {
            position: relative;
            margin: 10% auto;
            padding: 20px;
            width: 300px;
            background-color: white;
            border-radius: 8px;
            text-align: center;
            animation: slide-up 0.4s ease forwards;
        }

        @keyframes slide-up {
            from {
                transform: translateY(100%);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .open-popup {
            display: block;
        }

        .darken {
            filter: brightness(50%);
        }

        .popup-close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            cursor: pointer;
            color: #2c3e50;
        }

        /* Estilo para o container da imagem */
        .popup-image-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 15px;
        }

        /* Estilo para a imagem do livro */
        .popup-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
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
            <div class="col-left sidebar">
                <div class="card mb-4">
                    <div class="card-header">
                        Categorias
                    </div>
                    <div class="card-body">
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
                    <div class="card-header">
                        Tópicos Populares
                    </div>
                    <aside class="sidebar-left">
                        <div class="sidebar-section">
                            <ul>
                                <li><a href="#">Tópico 1</a></li>
                                <li><a href="#">Tópico 2</a></li>
                                <li><a href="#">Tópico 3</a></li>
                            </ul>
                        </div>
                        <button id="add-book-btn" class="btn btn-primary">Adicionar Livro!</button>
                    </aside>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        Artigos Recentes
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li><a href="#">Aprenda como criar um site responsivo</a></li>
                            <li><a href="#">3 coisas que você precisa saber sobre CSS</a></li>
                            <li><a href="#">5 recursos para aprender JavaScript</a></li>
                        </ul>
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

                                                <!-- Swap book button -->
                                                <form action="../controllers/post_actions.php" method="POST" style="display:inline;" id="swap-book-form">
                                                    <input type="hidden" name="acao" value="trocar_livro">
                                                    <input type="hidden" name="id_post" value="<?= $post['id']; ?>">
                                                    <button type="button" id="swap-book-btn" class="btn btn-secondary" data-image="<?= htmlspecialchars($post['caminho_capa']); ?>">Trocar Livro</button>
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
                        </main>

                        <!--popup de adicionar livro-->
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

                        <?php if (is_array($post)) {?>
                        <!-- Pop-up para troca de livro -->
                        <div id="swap-book-popup" class="popup-container pending-popup">
                            <div class="popup-content">
                                <span id="close-swap-book-popup" class="popup-close">&times;</span>
                                <h2>Trocar Livro</h2>
                                <form action="../controllers/post_actions.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="acao" value="trocar_livro">
                                    <input type="hidden" name="id_post" id="id_post" value="">
                                    <div class="post-info">
                                        <img src="<?php echo $post['caminho_capa']; ?>" alt="Capa do Livro" id="imagem_post">
                                        <h1>Usuário atual: <?php echo $user->getNome(); ?> </h1>
                                        <h1>Dono do post: <?php echo $post['nome'];?> </h1>
                                    </div>
                                    <label for="livro_para_trocar">Livro para Trocar:</label>
                                    <input type="text" name="livro_para_trocar" id="livro_para_trocar" required>
                                    <button type="submit" class="green-button">Confirmar</button>
                                </form>
                            </div>
                        </div>
                        <?php } ?>

                    </div>
                </div>
            </div>

            <div class="col-right sidebar">
                <div class="card mb-4">
                    <div class="card-header">
                        Pesquisar
                    </div>
                    <div class="card-body">
                        <div class="input-group">
                            <input class="form-control" type="text" placeholder="Digite aqui...">
                            <button class="btn btn-secondary ml-2">Buscar</button>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        Notificações
                    </div>
                    <aside class="sidebar-right">
                        <div class="sidebar-section">
                            <ul>
                                <?php
                                require_once '../config/database.php';

                                // Conectar ao banco de dados
                                $database = new Database();
                                $conn = $database->getConnection();

                                // Buscar notificações para o usuário atual
                                $id_usuario = $_SESSION['user_id'];
                                $sql = "SELECT u.nome AS nome_usuario, ue.nome AS nome_usuario_emissor, p.titulo AS titulo_post
                                        FROM notificacoes n
                                        JOIN posts p ON p.id = n.id_post
                                        JOIN usuario u ON u.id = n.id_usuario -- Refeição do usuário que receberá a notificação
                                        JOIN usuario ue ON ue.id = n.id_usuario_emissor -- Refeição do usuário que enviou a notificação
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
                    </aside>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>Desenvolvido Para TCC Senai</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../public/codigo_java.js"></script>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
        crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
        crossorigin="anonymous"></script>
</body>

</html>
