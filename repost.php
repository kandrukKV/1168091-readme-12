<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location:' . 'index.php');
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location:' . $_SERVER['HTTP_REFERER'] ?? 'index.php');
    exit();
}

include_once('helpers.php');
include_once('functions.php');
include_once('sql-requests.php');

$user_id = $_SESSION['user_id'];
$post_id = $_GET['id'];

$con = connect_to_database();

$post = get_all_fields_from_posts_by_id($con, $post_id);

if ($post) {

    $result = make_repost($con, $user_id, $post);

    if (!$result) {
        echo "Ошибка сервера";
        http_response_code(500);
        exit();
    }
}

header('Location:' . 'profile.php?id=' . $user_id);
exit();






