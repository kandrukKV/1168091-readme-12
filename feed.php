<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location:' . 'index.php');
    exit();
}

include_once('helpers.php');
include_once('functions.php');
include('sql-requests.php');

$con = connect_to_database();

$user_id = $_SESSION['user_id'];

$current_content_type_id = $_GET['content_type'] ?? 'all';

$posts = get_user_feed($con, $user_id, $current_content_type_id);

for ($i = 0; $i < count($posts); $i++) {
    $posts[$i]['num_reposts'] = get_num_reposts($con, $posts[$i]['id']);
    $posts[$i]['is_like'] = is_like($con, $posts[$i]['id'], $_SESSION['user_id']);
}

$all_posts = include_template('posts.php', [
    'posts' => $posts,
    'post_type' => 'feed'
]);

$content_types = get_content_types($con);

$content = include_template('feed.php', [
    'all_posts' => $all_posts,
    'content_types' => $content_types,
    'current_content_type_id' => $current_content_type_id
]);

print(include_template('layout.php', [
    'title' => 'readme: моя лента',
    'content' => $content,
    'user_name' => $_SESSION['login'],
    'user_id' => $_SESSION['user_id'],
    'header_type' => 'feed',
    'all_msg_count' => get_count_my_massages($con, $user_id)
]));
