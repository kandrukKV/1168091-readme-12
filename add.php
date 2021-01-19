<?php

include_once ('functions.php');
include_once ('helpers.php');

$is_auth = 1;
$user_name = 'Mr.Constantine'; // укажите здесь ваше имя

$con = mysqli_connect("localhost", "root", "","readme");

if (!$con) {
    echo "Ошибка подключения к базе данных";
    http_response_code(500);
    exit;
}

mysqli_set_charset($con, "utf8");

$sql = "SELECT type_name, class_name FROM content_type";

$result = mysqli_query($con, $sql);

$content_types = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

$tab_name = $_POST['content_type'] ?? 'photo';



if (isset($_POST['submit'])) {
    vardump($_POST);
}

$content = include_template('add-post.php', [
    'content_types' => $content_types,
    'tab_name' => $tab_name
]);

print (include_template('layout.php', [
        'title' => 'readme: добавление публикации',
        'content' => $content,
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'header_type' => 'add_post'
    ]
));
