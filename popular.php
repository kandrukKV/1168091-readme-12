<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location:' . 'index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

include_once('functions.php');
include_once('helpers.php');
include('sql-requests.php');

$con = connect_to_database();

$content_types = get_content_types($con);

$content_type_id = null;
$page = $_GET['page'] ?? '1';
$limit = 9;
$offset = ($page - 1) * $limit;

$sort = $_GET['sort'] ?? 'popular';
$line = $_GET['line'] ?? 'down';

if ($sort !== 'popular' && $sort !== 'likes' && $sort !== 'date') {
    echo "Страница не найдена";
    http_response_code(404);
    exit();
}

if ($line !== 'up' && $line !== 'down') {
    echo "Страница не найдена";
    http_response_code(404);
    exit();
}

$order_line = $line === 'down' ? 'DESC' : 'ASC';

switch ($sort) {
    case 'likes':
        $order_sort = 'likes_count';
        break;
    case 'date':
        $order_sort = 'datetime';
        break;
    default:
        $order_sort = 'views_count';
}

$content_type_id = $_GET['content_type'] ?? null;


if ($content_type_id && !content_type_id_is_correct($con, $content_type_id)) {
    echo "Страница не найдена";
    http_response_code(404);
    exit();
}

$posts = get_popular_posts($con, $content_type_id, $order_sort, $order_line, $limit, $offset);

$all_posts_count = get_all_posts_count($con, $content_type_id);

$num_pages = ceil($all_posts_count / $limit);

for ($i = 0; $i < count($posts); $i++) {
    $posts[$i]['is_like'] = is_like($con, $posts[$i]['id'], $_SESSION['user_id']);
}

$content = include_template('popular.php', [
    'posts' => $posts,
    'content_types' => $content_types,
    'content_type_id' => $content_type_id,
    'all_posts_num' => $all_posts_count,
    'num_pages' => $num_pages,
    'page' => $page,
    'sort' => $sort,
    'line' => $line,
    'limit' => $limit
]);

print (include_template('layout.php', [
        'title' => 'readme: популярное',
        'content' => $content,
        'user_name' => $_SESSION['login'],
        'header_type' => 'popular',
        'all_msg_count' => get_count_my_massages($con, $user_id)
    ]
));
