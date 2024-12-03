$(document).ready(function() {
    // Alternar entre as seções do perfil
    $('#feed-btn').click(function() {
        $('#feed').show();
        $('#books').hide();
        $('#saved-posts').hide();
    });

    $('#books-btn').click(function() {
        $('#feed').hide();
        $('#books').show();
        $('#saved-posts').hide();
    });

    $('#saved-posts-btn').click(function() {
        $('#feed').hide();
        $('#books').hide();
        $('#saved-posts').show();
    });

    // Abrir pop-up de criar post ao clicar em um livro
    $('.book-icon').click(function() {
        var livroId = $(this).data('id');
        $('#id_livro').val(livroId);
        $('#create-post-popup').addClass('open-popup');
    });

    // Fechar pop-up de criar post
    $('#close-create-post-popup').click(function() {
        $('#create-post-popup').removeClass('open-popup');
    });
});
