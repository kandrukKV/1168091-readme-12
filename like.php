<?php
include_once ('helpers.php');
include_once ('functions.php');

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

$sql = "SELECT * FROM posts WHERE id = ?";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, 'i', $post_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_fetch_row($result) && !is_like($con, $post_id, $user_id)) {
    $sql = "INSERT INTO likes (user_id, post_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $user_id, $post_id);

    if (mysqli_stmt_execute($stmt)) {
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
