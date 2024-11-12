document.addEventListener('DOMContentLoaded', function() {
            // Função para aplicar a transição de fade-in
            function fadeIn(element, duration) {
                element.style.opacity = 0;
                element.style.transition = `opacity ${duration}s`;
                setTimeout(() => {
                    element.style.opacity = 1;
                }, 0);
            }

            // Função para aplicar a transição de baixo para cima
            function slideUp(element, duration) {
                element.style.transform = 'translateY(100%)';
                element.style.transition = `transform ${duration}s`;
                setTimeout(() => {
                    element.style.transform = 'translateY(0)';
                }, 0);
            }

            // Aplicar a transição de fade-in aos elementos
            const profileContent = document.querySelector('.profile-content');
            const sidebarLeft = document.querySelector('.sidebar-left');
            const sidebarRight = document.querySelector('.sidebar-right');
            const header = document.querySelector('header');

            fadeIn(profileContent, 1.5);
            fadeIn(sidebarLeft, 1.5);
            fadeIn(sidebarRight, 1.5);
            fadeIn(header, 1.5);

            // Aplicar a transição de baixo para cima ao feed e ao profile-header
            const feed = document.querySelector('.feed');
            slideUp(feed, 1.5);

            // Aplicar a transição de baixo para cima ao pop-up de troca de livro
            const swapBookPopup = document.getElementById("swap-book-popup");
            slideUp(swapBookPopup, 1.5);

             //abrir e fechar o pop up
            const popup = document.getElementById("popup");
            const addBookBtn = document.getElementById("add-book-btn");
            const closePopup = document.getElementById("close-popup");
            const mainContent = document.querySelector('.main-container');

            //função para abrir o pop-up com animação
            addBookBtn.onclick = function() {
                popup.classList.add("open-popup");
                mainContent.classList.add("darken");
            }

            // Fechar o pop-up ao clicar no botão de fechar ou fora dele
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
                mainContent.classList.remove("darken");
            }

            // Abrir e fechar o pop-up de troca de livro
            const swapBookBtns = document.querySelectorAll('.swap-book-btn');
            const closeSwapBookPopup = document.getElementById("close-swap-book-popup");

            swapBookBtns.forEach(function(btn) {
                btn.onclick = function() {
                    const idPost = this.parentNode.querySelector('input[name="id_post"]').value;
                    const imageUrl = this.getAttribute('data-image');
                    document.getElementById("id_post").value = idPost;
                    document.getElementById("imagem_post").src = imageUrl;
                    swapBookPopup.classList.add("open-popup");
                    mainContent.classList.add("darken");
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
                mainContent.classList.remove("darken");
            }
        });



        // JavaScript para o side pop-up
        jQuery(document).ready(function($) {
            $('.customization_popup_trigger').on('click', function(event) {
                event.preventDefault();
                $('.customization_popup').addClass('is-visible');
            });
            $('.customization_popup').on('click', function(event) {
                if ($(event.target).is('.customization_popup_close') || $(event.target).is('.customization_popup')) {
                    event.preventDefault();
                    $(this).removeClass('is-visible');
                }
            });
            $(document).keyup(function(event) {
                if (event.which == '27') {
                    $('.customization_popup').removeClass('is-visible');
                }
            });
        });










        document.addEventListener('DOMContentLoaded', function() {
            // Função para transição de fade-in
            function fadeIn(element, duration) {
                element.style.opacity = 0;
                element.style.transition = `opacity ${duration}s`;
                setTimeout(() => {
                    element.style.opacity = 1;
                }, 0);
            }
            // Função para abrir o pop-up de criar post
            window.openCreatePostPopup = function(id_livro) {
                document.getElementById('id_livro').value = id_livro;
                const createPostPopup = document.getElementById("create-post-popup");
                createPostPopup.classList.add("open-popup");
                mainContent.classList.add("darken");
            }

            // Fechar o pop-up 
            const closeCreatePostPopup = document.getElementById("close-create-post-popup");
            closeCreatePostPopup.onclick = function() {
                closeCreatePostPopupFunction();
            }

            window.onclick = function(event) {
                if (event.target == createPostPopup) {
                    closeCreatePostPopupFunction();
                }
            }

            function closeCreatePostPopupFunction() {
                createPostPopup.classList.remove("open-popup");
                mainContent.classList.remove("darken");
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
        });




        document.addEventListener('DOMContentLoaded', function() {
            // Função para abrir o pop-up de criação de post
            const addBookBtn = document.getElementById("add-book-btn");
            const popup = document.getElementById("popup");
            const mainContent = document.querySelector('.main-container');
            const closePopup = document.getElementById("close-popup");
        
            // Função para abrir o pop-up
            addBookBtn.onclick = function() {
                popup.classList.add("open-popup");
                mainContent.classList.add("darken");
            }
        
            // Fechar o pop-up
            closePopup.onclick = function() {
                closePopupFunction();
            }
        
            // Função para fechar o pop-up
            function closePopupFunction() {
                popup.classList.remove("open-popup");
                mainContent.classList.remove("darken");
            }
        
            // Fechar o pop-up ao clicar fora dele
            window.onclick = function(event) {
                if (event.target === popup) {
                    closePopupFunction();
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
        });
        
