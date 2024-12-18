// Alterna a exibição do submenu
function toggleMenu(menuId) {
    const submenu = document.getElementById(menuId);
    if (submenu.style.display === "block") {
        submenu.style.display = "none";
    } else {
        submenu.style.display = "block";
    }
    showArticles(menuId);
}

// Redireciona para a posição dos artigos do topico correspondente
function scrollToTopic(event, topicId) {
    if (event) {
        event.preventDefault();
    }
    const targetArticle = document.getElementById(topicId);
    if (targetArticle) {
        targetArticle.scrollIntoView({ behavior: "smooth" });
        const topic = topicId.split('-')[0];
        showArticles(topic);
    }
}

// redireciona para o artigo bemvindo  ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    scrollToTopic(null, 'header');
    showArticles('intro');
});
//mostra o botão de voltar ao topo quando o usuario descer a pagina
window.onscroll = function() {
    const scrollToTopBtn = document.getElementById("scrollToTopBtn");
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        scrollToTopBtn.style.display = "block";
    } else {
        scrollToTopBtn.style.display = "none";
    }
};

//funçao de subir com animaçao scroll
document.getElementById("scrollToTopBtn").addEventListener("click", function() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

//mostra apenas os artigos do tópico selecionado
function showArticles(topic) {
    const articles = document.querySelectorAll('.content article');
    articles.forEach(article => {
        if (article.classList.contains(topic)) {
            article.style.display = 'block';
        } else {
            article.style.display = 'none';
        }
    });
}

// função para filtrar artigos com base na pesquisa
document.getElementById("searchInput").addEventListener("input", function() {
    const query = this.value.toLowerCase();
    const articles = document.querySelectorAll('.content article');

    articles.forEach(article => {
        if (!article.dataset.originalContent) {
            article.dataset.originalContent = article.innerHTML;
        }

        const text = article.dataset.originalContent.toLowerCase();
        if (query) {
            if (text.includes(query)) {
                article.style.display = 'block';
                //marcar os textos achados
                const regex = new RegExp(`(${query})`, 'gi');
                article.innerHTML = article.dataset.originalContent.replace(regex, '<mark>$1</mark>');
            } else {
                article.style.display = 'none';
            }
        } else {
            // voltar ao normal quando o input ficar sem nada
            article.style.display = 'block';
            article.innerHTML = article.dataset.originalContent;
        }
    });
});

//abrir imagem com um pop up
function openImagePopup(src) {
    const popup = document.getElementById('imagePopup');
    const popupImage = document.getElementById('popupImage');
    popupImage.src = src;
    popup.style.display = 'flex';
}

//fechar pop up
function closeImagePopup() {
    const popup = document.getElementById('imagePopup');
    popup.style.display = 'none';
}

//evento de clique para as imagens
document.querySelectorAll('.content article img').forEach(img => {
    img.addEventListener('click', function() {
        openImagePopup(this.src);
    });
});

document.querySelector('.close-btn').addEventListener('click', closeImagePopup);

document.querySelector('.popup-image').addEventListener('click', toggleZoom);

document.getElementById('imagePopup').addEventListener('click', function(event) {
    if (event.target === this) {
        closeImagePopup();
    }
});
