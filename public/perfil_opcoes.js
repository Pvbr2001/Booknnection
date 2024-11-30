document.addEventListener('DOMContentLoaded', function() {
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
