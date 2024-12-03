<?php
require_once '../models/Post.php';
require_once '../config/database.php';

if (isset($_GET['id_post'])) {
    $post_id = $_GET['id_post'];

    $database = Database::getInstance();
    $conn = $database->getConnection();

    $sqlComentarios = "SELECT c.id, c.texto, c.data_comentario, u.nome AS nome_usuario
                       FROM comentarios c
                       JOIN usuario u ON c.id_usuario = u.id
                       WHERE c.id_post = ?";
    $stmtComentarios = $conn->prepare($sqlComentarios);
    $stmtComentarios->bind_param("i", $post_id);
    $stmtComentarios->execute();
    $resultComentarios = $stmtComentarios->get_result();
    $comentarios = $resultComentarios->fetch_all(MYSQLI_ASSOC);

    foreach ($comentarios as $comentario) {
        echo '<div class="comment">';
        echo '<strong>' . htmlspecialchars($comentario['nome_usuario']) . '</strong>';
        echo '<p>' . htmlspecialchars($comentario['texto']) . '</p>';
        echo '<small class="text-muted">' . date('d/m/Y H:i', strtotime($comentario['data_comentario'])) . '</small>';
        echo '</div>';
    }
}
?>
