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
});
