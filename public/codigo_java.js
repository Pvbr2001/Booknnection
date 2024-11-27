document.addEventListener('DOMContentLoaded', function() {
    // Função para abrir o pop-up de adicionar livro
    const addBookBtn = document.getElementById("add-book-btn");
    const popup = document.getElementById("popup");
    const closePopup = document.getElementById("close-popup");

    if (addBookBtn && popup && closePopup) {
        addBookBtn.onclick = function() {
            popup.classList.add("open-popup");
        }

        closePopup.onclick = function() {
            closePopupFunction();
        }

        window.onclick = function(event) {
            if (event.target == popup) {
                closePopupFunction();
            }
        }

        function closePopupFunction() {
            popup.classList.remove("open-popup");
        }
    }

    // Função para abrir o pop-up de criar post
    window.openCreatePostPopup = function(id_livro) {
        const createPostPopup = document.getElementById("create-post-popup");
        if (createPostPopup) {
            document.getElementById('id_livro').value = id_livro;
            createPostPopup.classList.add("open-popup");
        }
    }

    const closeCreatePostPopup = document.getElementById("close-create-post-popup");
    if (closeCreatePostPopup) {
        closeCreatePostPopup.onclick = function() {
            closeCreatePostPopupFunction();
        }

        function closeCreatePostPopupFunction() {
            const createPostPopup = document.getElementById("create-post-popup");
            if (createPostPopup) {
                createPostPopup.classList.remove("open-popup");
            }
        }
    }

    // Função para abrir o pop-up de troca de livro
    const swapBookBtns = document.querySelectorAll('#swap-book-btn');
    const swapBookPopup = document.getElementById("swap-book-popup");
    const closeSwapBookPopup = document.getElementById("close-swap-book-popup");

    if (swapBookBtns && swapBookPopup && closeSwapBookPopup) {
        swapBookBtns.forEach(function(btn) {
            btn.onclick = function() {
                const idPost = this.parentNode.querySelector('input[name="id_post"]').value;
                const imageUrl = this.getAttribute('data-image');
                document.getElementById("id_post").value = idPost;
                document.getElementById("imagem_post").src = imageUrl;
                swapBookPopup.classList.add("open-popup");
            }
        });

        closeSwapBookPopup.onclick = function() {
            closeSwapBookPopupFunction();
        }

        window.onclick = function(event) {
            if (event.target == swapBookPopup) {
                closeSwapBookPopupFunction();
            }
        }

        function closeSwapBookPopupFunction() {
            swapBookPopup.classList.remove("open-popup");
        }
    }

    // Funções para navegar entre feed, lista de livros e posts salvos
    function showFeed() {
        document.getElementById('feed').style.display = 'block';
        document.getElementById('books').style.display = 'none';
        document.getElementById('saved-posts').style.display = 'none';
    }

    function showBooks() {
        document.getElementById('feed').style.display = 'none';
        document.getElementById('books').style.display = 'block';
        document.getElementById('saved-posts').style.display = 'none';
    }

    function showSavedPosts() {
        document.getElementById('feed').style.display = 'none';
        document.getElementById('books').style.display = 'none';
        document.getElementById('saved-posts').style.display = 'block';
    }

    document.getElementById('feed-btn').addEventListener('click', showFeed);
    document.getElementById('books-btn').addEventListener('click', showBooks);
    document.getElementById('saved-posts-btn').addEventListener('click', showSavedPosts);

    // Função para abrir o pop-up de configurações
    const settingsBtn = document.getElementById("settings-btn");
    const settingsPopup = document.getElementById("settings-popup");
    const closeSettingsPopup = document.getElementById("close-settings-popup");

    if (settingsBtn && settingsPopup && closeSettingsPopup) {
        settingsBtn.onclick = function() {
            settingsPopup.classList.add("open-popup");
        }

        closeSettingsPopup.onclick = function() {
            closeSettingsPopupFunction();
        }

        window.onclick = function(event) {
            if (event.target == settingsPopup) {
                closeSettingsPopupFunction();
            }
        }

        function closeSettingsPopupFunction() {
            settingsPopup.classList.remove("open-popup");
        }
    }

    // Função para abrir o pop-up de adicionar livro ao clicar na imagem do livro
    const bookIcons = document.querySelectorAll('.book-icon');
    const createPostPopup = document.getElementById('create-post-popup');
    const idLivroInput = document.getElementById('id_livro');

    bookIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            const livroId = this.getAttribute('data-id');
            idLivroInput.value = livroId;
            createPostPopup.classList.add('open-popup');
        });
    });
});
