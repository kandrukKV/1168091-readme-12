<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location:' . 'index.php');
    exit();
}

include_once ('functions.php');
include_once ('helpers.php');

if (!isset($_GET['id'])) {
    echo "Страница не найдена";
    http_response_code(404);
    exit;
}

$post_id = $_GET['id'];

$con = connect_to_database();

$sql = "SELECT p.id, p.datetime, p.title, p.content, p.link, p.views_count, p.user_id, p.quote_author, p.views_count, u.login, u.avatar, u.datetime as user_datetime, c_t.class_name FROM posts p
    JOIN users u ON p.user_id = u.id
    JOIN content_type c_t ON p.content_type_id = c_t.id
    WHERE p.id = ?";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, 'i', $post_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$post_details = mysqli_fetch_array($result, MYSQLI_ASSOC);

$post_details['comments'] = get_comments($con, $post_id);
$post_details['num_likes'] = get_num_likes($con, $post_id);
$post_details['num_comments'] = count($post_details['comments']);
$post_details['num_reposts'] = get_num_reposts($con, $post_id);
$post_details['is_like'] = is_like($con, $post_id, $_SESSION['user_id']);

if (!$post_details) {
    echo "Страница не найдена";
    http_response_code(404);
    exit;
}

$errors = [];

$content = include_template('post-details.php', [
    'errors' => $errors,
    'tags' => get_tags($con, $post_id),
    'post_details' => $post_details,
    'user_num_of_posts' => get_num_posts($con, $post_details['user_id']),
    'user_num_of_subscribers' => get_num_subscribers($con, $post_details['user_id']),
]);

print (include_template('layout.php', [
        'title' => 'readme: публикация',
        'content' => $content,
        'user_name' => $_SESSION['login'],
        'header_type' => 'post'
    ]
));
