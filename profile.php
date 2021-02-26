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

}

$content = include_template('user-profile.php', [
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
