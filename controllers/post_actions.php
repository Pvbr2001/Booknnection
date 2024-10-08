<?php
session_start();
require_once '../models/user.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if (!class_exists('User')) {
        die("Classe User não encontrada!");
    }

    $user = new User();

    if ($acao === 'curtir_post') {
        $id_post = $_POST['id_post'];

        if ($user->curtirPost($id_post)) {
            echo "<script>alert('Post curtido com sucesso'); window.location.href = document.referrer;</script>";
        } else {
            echo "<script>alert('Erro ao curtir post'); window.location.href = document.referrer;</script>";
        }
    } elseif ($acao === 'salvar_post') {
        $id_usuario = $_SESSION['user_id'];
        $id_post = $_POST['id_post'];

        if ($user->salvarPost($id_usuario, $id_post)) {
            echo "<script>alert('Post salvo com sucesso'); window.location.href = document.referrer;</script>";
        } else {
            echo "<script>alert('Erro ao salvar post'); window.location.href = document.referrer;</script>";
        }
    } else {
        echo "<script>alert('Ação inválida'); window.location.href = document.referrer;</script>";
    }
} else {
    echo "<script>alert('Método de requisição inválido'); window.location.href = document.referrer;</script>";
}
?>
