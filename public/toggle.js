$(document).ready(function() {
    // Alternar visibilidade dos elementos com a classe toggle-header
    $('.toggle-header').click(function() {
        var target = $(this).data('target');
        $('#' + target).toggle();
    });
});
