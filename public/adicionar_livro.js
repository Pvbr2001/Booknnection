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

    // Função para abrir o pop-up de pesquisa por ISBN
    const showIsbnSearch = document.getElementById('show-isbn-search');
    const isbnSearchPopup = document.getElementById('isbn-search-popup');
    const closeIsbnSearchPopup = document.getElementById('close-isbn-search-popup');

    if (showIsbnSearch && isbnSearchPopup && closeIsbnSearchPopup) {
        showIsbnSearch.addEventListener('click', function(e) {
            e.preventDefault();
            isbnSearchPopup.classList.add('open-popup');
            popup.classList.remove('open-popup');
        });

        closeIsbnSearchPopup.addEventListener('click', function() {
            isbnSearchPopup.classList.remove('open-popup');
        });
    }
});
