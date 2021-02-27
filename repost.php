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

include_once ('helpers.php');
include_once ('functions.php');

$user_id = $_SESSION['user_id'];
$post_id = $_GET['id'];

$con = connect_to_database();

$sql = "SELECT * FROM posts WHERE id = ?";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, 'i', $post_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$post = mysqli_fetch_assoc($result);

if ($post) {
    $title = $post['title'];
    $content = $post['content'];
    $quote_author = $post['quote_author'];
    $picture = $post['picture'];
    $video = $post['video'];
    $link = $post['link'];
    $views_count = $post['views_count'];
    $content_type_id = $post['content_type_id'];
    $is_repost = 1;
    $original_post_id = $post['id'];


   echo $sql = "INSERT INTO posts
            SET
                title = '$title',
                content = '$content',
                quote_author = '$quote_author',
                picture = '$picture',
                video = '$video',
                link = '$link',
                views_count = $views_count,
                user_id = '$user_id',
                content_type_id = '$content_type_id',
                is_repost = 1,
                original_post_id = ?";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $post_id);

    if (!mysqli_stmt_execute($stmt)) {
        echo "Ошибка сервера";
        http_response_code(500);
        exit();
    }
}

header('Location:' . 'profile.php?id=' . $user_id);
exit();






