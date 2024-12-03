$(document).ready(function() {
    // Abrir pop-up de adicionar livro
    $('#add-book-btn').click(function() {
        $('#add-book-popup').addClass('open-popup');
    });

    // Fechar pop-up de adicionar livro
    $('#close-add-book-popup').click(function() {
        $('#add-book-popup').removeClass('open-popup');
    });

    // Abrir pop-up de pesquisa por ISBN
    $('#show-isbn-search').click(function() {
        $('#isbn-search-popup').addClass('open-popup');
    });

    // Fechar pop-up de pesquisa por ISBN
    $('#close-isbn-search-popup').click(function() {
        $('#isbn-search-popup').removeClass('open-popup');
    });

    // Abrir pop-up de troca de livro
    $('.swap-book-btn').click(function() {
        var postId = $(this).data('id');
        var imageUrl = $(this).data('image');
        $('#id_post').val(postId);
        $('#imagem_post').attr('src', imageUrl);
        $('#swap-book-popup').addClass('open-popup');
    });

    // Fechar pop-up de troca de livro
    $('#close-swap-book-popup').click(function() {
        $('#swap-book-popup').removeClass('open-popup');
    });
});
