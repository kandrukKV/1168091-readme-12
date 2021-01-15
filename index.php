<?php

include_once ('functions.php');

include_once ('helpers.php');

date_default_timezone_set('Europe/Moscow');

$is_auth = rand(0, 1);

$user_name = 'Mr.Constantine'; // укажите здесь ваше имя

$con = mysqli_connect("localhost", "root", "","readme");

if (!$con) {
    echo "Ошибка подключения к базе данных";
    http_response_code(500);
    exit;
}

mysqli_set_charset($con, "utf8");

$sql = "SELECT id, type_name, class_name FROM content_type";

$result = mysqli_query($con, $sql);

$content_types = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

$posts_sql = "SELECT p.id, p.datetime, p.title, p.content, p.link, p.quote_author, u.login, u.avatar, c_t.type_name, c_t.class_name FROM posts p
    JOIN users u ON p.user_id = u.id
    JOIN content_type c_t ON p.content_type_id = c_t.id";

if (isset($_GET['content_type'])) {
    $content_type_id = $_GET['content_type'];
    $sql = "SELECT * FROM content_type WHERE id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $content_type_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if (!$rows) {
        echo "Страница не найдена";
        http_response_code(404);
        exit;
    }

    $posts_sql .= " WHERE content_type_id = ? ORDER BY views_count DESC";
    $stmt = mysqli_prepare($con, $posts_sql);
    mysqli_stmt_bind_param($stmt, 'i', $content_type_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $posts_sql .= " ORDER BY views_count DESC";
    $result = mysqli_query($con, $posts_sql);
}

$posts = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

$content = include_template('main.php', [
    'posts' => $posts,
    'content_types' => $content_types
]);

print (include_template('layout.php', [
        'title' => 'readme: популярное',
        'content' => $content,
        'is_auth' => $is_auth,
        'user_name' => $user_name,
    ]
));
