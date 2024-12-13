document.addEventListener('DOMContentLoaded', function() {
    const sendRequestButtons = document.querySelectorAll('.send-request-btn');
    const sendRequestPopup = document.getElementById('send-request-popup');
    const closeSendRequestPopup = document.getElementById('close-send-request-popup');
    const sendRequestForm = document.getElementById('send-request-form');
    const imagemPost = document.getElementById('imagem_post');
    const idPostInput = document.getElementById('id_post');
    const currentUser = document.getElementById('current-user');
    const postOwnerName = document.getElementById('post-owner-name');
    const postOwnerImage = document.getElementById('post-owner-image');

    sendRequestButtons.forEach(button => {
        button.addEventListener('click', function() {
            const imageSrc = button.getAttribute('data-image');
            const postId = button.getAttribute('data-id');
            const postOwner = button.getAttribute('data-nome');
            const postOwnerId = button.getAttribute('data-owner-id');

            imagemPost.src = imageSrc;
            idPostInput.value = postId;
            postOwnerName.textContent = postOwner;

            // Buscar a foto do usuário dono do post
            fetch(`../controllers/user_controller.php?acao=getFotoPerfilById&id_usuario=${postOwnerId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.foto_perfil) {
                        postOwnerImage.src = data.foto_perfil;
                    } else {
                        postOwnerImage.src = '../public/imagens/user-icon.png'; // Imagem padrão
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                });

            sendRequestPopup.classList.add('open-popup');
        });
    });

    closeSendRequestPopup.addEventListener('click', function() {
        sendRequestPopup.classList.remove('open-popup');
    });

    sendRequestForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(sendRequestForm);
        fetch('../controllers/post_actions.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                sendRequestPopup.classList.remove('open-popup');
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
        });
    });
});
