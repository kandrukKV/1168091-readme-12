<?php
include_once('helpers.php');
include_once('functions.php');
include('sql-requests.php');

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location:' . 'index.php');
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location:' . $_SERVER['HTTP_REFERER'] ?? 'index.php');
    exit();
}

$con = connect_to_database();

$post_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

if (is_there_post_with_id($con, $post_id) && !is_like($con, $post_id, $user_id)) {

    if (add_like($con, $user_id, $post_id)) {
        header('Location:' . $_SERVER['HTTP_REFERER'] ?? 'index.php');
        exit();
    } else {
        echo "Ошибка сервера";
        http_response_code(500);
        exit();
    }
} else {
    header('Location:' . $_SERVER['HTTP_REFERER'] ?? 'index.php');
    exit();
}
