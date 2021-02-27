<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location:' . 'index.php');
    exit();
}

$user_id = $_GET['id'] ?? $_SESSION['user_id'];

include_once ('helpers.php');
include_once ('functions.php');

$con = connect_to_database();

$user = get_user_info($con, $user_id);

if (!$user) {
    echo "Страница не найдена";
    http_response_code(404);
    exit();
}

$posts = get_posts_of_user($con, $user_id);

for ($i = 0; $i < count($posts); $i++) {
    $posts[$i]['tags'] = get_tags($con, $posts[$i]['id']);
    $posts[$i]['comments'] = get_comments($con, $posts[$i]['id']);
    $posts[$i]['num_likes'] = get_num_likes($con, $posts[$i]['id']);
    $posts[$i]['num_reposts'] = get_num_reposts($con, $posts[$i]['id']);
    $posts[$i]['is_like'] = is_like($con, $posts[$i]['id'], $_SESSION['user_id']);
}

$isSubscribe = true;

if ($_SESSION['user_id'] !== $user_id) {
    $author = $_SESSION['user_id'];
    $sql = "SELECT * FROM subscribers WHERE author=? AND subscription=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $author, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $isSubscribe = mysqli_fetch_row($result);
}

$content = include_template('user-profile.php', [
    'isSubscribe' => $isSubscribe,
    'user' => $user,
    'posts' => $posts,
    'user_num_of_posts' => get_num_posts($con, $user['id']),
    'user_num_of_subscribers' => get_num_subscribers($con, $user['id']),
    'active_tab' => 'posts'
]);

print (include_template('layout.php', [
    'title' => 'readme: профиль',
    'content' => $content,
    'user_name' => $_SESSION['login'],
    'header_type' => 'post',
]));
