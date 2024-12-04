document.addEventListener('DOMContentLoaded', function() {
    const sendRequestButtons = document.querySelectorAll('.send-request-btn');
    const sendRequestPopup = document.getElementById('send-request-popup');
    const closeSendRequestPopup = document.getElementById('close-send-request-popup');
    const sendRequestForm = document.getElementById('send-request-form');
    const imagemPost = document.getElementById('imagem_post');
    const idPostInput = document.getElementById('id_post');
    const currentUser = document.getElementById('current-user');
    const postOwner = document.getElementById('post-owner');

    sendRequestButtons.forEach(button => {
        button.addEventListener('click', function() {
            const imageSrc = button.getAttribute('data-image');
            const postId = button.closest('form').querySelector('input[name="id_post"]').value;
            const postOwnerName = button.closest('.card').querySelector('.card-footer a').textContent;

            imagemPost.src = imageSrc;
            idPostInput.value = postId;
            postOwner.textContent = postOwnerName;

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
