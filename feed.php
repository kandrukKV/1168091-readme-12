<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location:' . 'index.php');
    exit();
}

include_once ('helpers.php');
include_once ('functions.php');

$con = connect_to_database();

$user_id = $_SESSION['user_id'];

$current_content_type_id = $_GET['content_type'] ?? 'all';

$posts_sql = "
    SELECT
           p.id,
           p.datetime,
           p.title,
           p.content,
           p.link,
           p.quote_author,
           p.user_id,
           u.login,
           u.avatar,
           c_t.type_name,
           c_t.class_name,
           (SELECT count(*) FROM likes WHERE post_id = p.id) AS likes_count,
           (SELECT count(*) FROM comments WHERE post_id = p.id) AS comments_count
    FROM posts p
    JOIN users u ON p.user_id = u.id
    JOIN content_type c_t ON p.content_type_id = c_t.id
    JOIN subscribers sub ON  sub.subscription = p.user_id AND sub.author = ?";


if ($current_content_type_id !== 'all') {

    if (!content_type_id_is_correct($con, $current_content_type_id)) {
        echo "Страница не найдена";
        http_response_code(404);
        exit();
    }

    $posts_sql .= " WHERE content_type_id = ? ORDER BY datetime DESC";
    $stmt = mysqli_prepare($con, $posts_sql);
    mysqli_stmt_bind_param($stmt, 'ii', $user_id, $current_content_type_id);
} else {
    $posts_sql .= " ORDER BY datetime DESC";
    $stmt = mysqli_prepare($con, $posts_sql);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$posts = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

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

print (include_template('layout.php', [
    'title' => 'readme: моя лента',
    'content' => $content,
    'user_name' => $_SESSION['login'],
    'user_id' => $_SESSION['user_id'],
    'header_type' => 'feed',
]));
