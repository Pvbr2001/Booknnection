document.addEventListener('DOMContentLoaded', function() {
    // Função para abrir o pop-up de troca de livro
    const swapBookBtns = document.querySelectorAll('.swap-book-btn');
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
});
