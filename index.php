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

$sql = "SELECT p.id, p.datetime, p.title, p.content, p.link, p.quote_author, u.login, u.avatar, c_t.type_name, c_t.class_name FROM posts p
    JOIN users u ON p.user_id = u.id
    JOIN content_type c_t ON p.content_type_id = c_t.id";

if (isset($_GET['content_type'])) {
    $content_type_id = (int) $_GET['content_type'];
    $sql .= " WHERE content_type_id = " . $content_type_id;
}

$sql .= " ORDER BY views_count DESC";

$result = mysqli_query($con, $sql);
$posts = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

$content = include_template('main.php', [
    'posts' => $posts,
    'content_types' => $content_types
]);

$header = include_template('header.php', [
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);

$footer = include_template('footer.php');


print (include_template('layout.php', [
        'title' => 'readme: популярное',
        'content' => $content,
        'header' => $header,
        'footer' => $footer
    ]
));
