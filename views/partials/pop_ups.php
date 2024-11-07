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