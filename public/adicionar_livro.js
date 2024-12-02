$(document).ready(function() {
    // Abrir pop-up de adicionar livro
    $('#add-book-btn').click(function() {
        $('#popup').addClass('open-popup');
    });

    // Fechar pop-up de adicionar livro
    $('#close-popup').click(function() {
        $('#popup').removeClass('open-popup');
    });

    // Abrir pop-up de pesquisa por ISBN
    $('#show-isbn-search').click(function() {
        $('#isbn-search-popup').addClass('open-popup');
    });

    // Fechar pop-up de pesquisa por ISBN
    $('#close-isbn-search-popup').click(function() {
        $('#isbn-search-popup').removeClass('open-popup');
    });

    // Abrir pop-up de criar post
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
