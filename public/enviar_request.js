$(document).ready(function () {
    $('.send-request-btn').click(function () {
        var image = $(this).data('image');
        var id_post = $(this).data('id');
        var nome_dono_post = $(this).data('nome');
        var postOwnerId = $(this).data('owner-id');

        $('#imagem_post').attr('src', image);
        $('#id_post').val(id_post);
        $('#post-owner-name').text(nome_dono_post);

        // Buscar a foto do usuário dono do post
        fetch(`../controllers/user_controller.php?acao=getFotoPerfilById&id_usuario=${postOwnerId}`)
            .then(response => response.json())
            .then(data => {
                if (data.foto_perfil) {
                    $('#post-owner-image').attr('src', data.foto_perfil);
                } else {
                    $('#post-owner-image').attr('src', '../public/imagens/user-icon.png'); // Imagem padrão
                }
            })
            .catch(error => {
                console.error('Erro:', error);
            });

        $('#send-request-popup').addClass('open-popup');
    });

    $('#close-send-request-popup').click(function () {
        $('#send-request-popup').removeClass('open-popup');
    });

    $('#send-request-form').submit(function (e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: '../controllers/post_actions.php',
            data: formData,
            success: function (response) {
                var data = JSON.parse(response);
                if (data.status === 'success') {
                    alert(data.message);
                    $('#send-request-popup').removeClass('open-popup');
                } else {
                    alert(data.message);
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });
});
