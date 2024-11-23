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
    $database = new Database();
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

                    <div class='post'>
                        <h2><?= htmlspecialchars($post['titulo']); ?></h2>
                        <p><?= htmlspecialchars($post['descricao']); ?></p>
                        <img src="<?= htmlspecialchars($post['caminho_capa']); ?>" alt="Capa do Livro">
                        
                        <!-- Botão de curtir com contador dinâmico -->
                        <form action='../controllers/post_actions.php' method='POST' style='display:inline;'>
                            <input type='hidden' name='acao' value='curtir_post'>
                            <input type='hidden' name='id_post' value='<?= $post['id']; ?>'>
                            <button type='submit'>Curtir (<?= $curtidas; ?>)</button>
                        </form>
                        
                        <!-- Outras ações (trocar livro, salvar post) -->
                        <form action='../controllers/post_actions.php' method='POST' style='display:inline;' id='swap-book-form'>
                            <input type='hidden' name='acao' value='trocar_livro'>
                            <input type='hidden' name='id_post' value='<?= $post['id']; ?>'>
                        </form>

                        <form action='../controllers/post_actions.php' method='POST' style='display:inline;'>
                            <input type='hidden' name='acao' value='salvar_post'>
                            <input type='hidden' name='id_post' value='<?= $post['id']; ?>'>
                            <button type='submit'>Salvar</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Seção de livros -->
            <div id="books" class="feed" style="display:none;">
                <?php foreach ($livros as $livro): ?>
                    <div class='post'>
                        <h2><?= htmlspecialchars($livro['titulo']); ?></h2>
                        <p>Autor: <?= htmlspecialchars($livro['autor']); ?></p>
                        <img src='<?= htmlspecialchars($livro['caminho_capa']); ?>' alt='Capa do Livro'>
                        <button onclick='openCreatePostPopup(<?= $livro['id']; ?>)'>Criar Post</button>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Seção de posts salvos -->
            <div id="saved-posts" class="feed" style="display:none;">
                <?php foreach ($postsSalvos as $post): ?>
                    <div class='post'>
                        <h2><?= htmlspecialchars($post['titulo']); ?></h2>
                        <p><?= htmlspecialchars($post['descricao']); ?></p>
                        <img src='<?= htmlspecialchars($post['caminho_capa']); ?>' alt='Capa do Livro'>
                        <form action='../controllers/post_actions.php' method='POST' style='display:inline;'>
                            <input type='hidden' name='acao' value='curtir_post'>
                            <input type='hidden' name='id_post' value='<?= $post['id']; ?>'>
                            <button type='submit'>Curtir (<?= $post['curtidas']; ?>)</button>
                        </form>
                        <form action='../controllers/post_actions.php' method='POST' style='display:inline;'>
                            <input type='hidden' name='acao' value='salvar_post'>
                            <input type='hidden' name='id_post' value='<?= $post['id']; ?>'>
                            <button type='submit'>Salvar</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>

        <!-- Sidebar direita -->
        <aside class="sidebar-right">
    <div class="sidebar-section">
        <h2>Notificações Recentes</h2>
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
    <button class="sidebar-button">Botão Lateral A</button>
    <button class="sidebar-button">Botão Lateral B</button>
</aside>

    </div>

    <!-- Pop-up -->
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
                <li><a href="../views/pagina_home.html">HOME</a></li>
                <li><a href="../controllers/User_Controller.php?acao=logout">Logout</a></li>
            </ul>
            <a href="#0" class="customization_popup_close img-replace">X</a>
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
