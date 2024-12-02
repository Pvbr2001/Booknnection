$(document).ready(function() {
    // Alternar entre as seções dos sidebars
    $('.toggle-header').click(function() {
        var target = $(this).data('target');
        $('#' + target).toggle();
    });
});
