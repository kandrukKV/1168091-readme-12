<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location:' . 'index.php');
    exit();
}

include_once ('functions.php');
include_once ('helpers.php');

date_default_timezone_set('Europe/Moscow');

$con = connect_to_database();

$content_types = get_content_types($con);

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
    JOIN content_type c_t ON p.content_type_id = c_t.id";

$content_type_id = null;
$page = $_GET['page'] ?? '1';
$limit = 9;

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


$offset = ($page - 1) * $limit;

$all_posts_sql = "SELECT * FROM posts";

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

$order_line = $line === 'down' ? 'DESC' : 'ASC';


if (isset($_GET['content_type'])) {
    $content_type_id = $_GET['content_type'];

    if (!content_type_id_is_correct($con, $content_type_id)) {
        echo "Страница не найдена";
        http_response_code(404);
        exit();
    }

    $all_posts_sql .= " WHERE content_type_id = ?";
    $stmt = mysqli_prepare($con, $all_posts_sql);
    mysqli_stmt_bind_param($stmt, 'i', $content_type_id);
    mysqli_stmt_execute($stmt);
    $all_posts_result = mysqli_stmt_get_result($stmt);
    $all_posts = $all_posts_result ? mysqli_fetch_all($all_posts_result, MYSQLI_ASSOC) : [];

    $posts_sql .= " WHERE content_type_id = ? ORDER BY ". $order_sort . " " . $order_line . " LIMIT ? OFFSET ?";

    $stmt = mysqli_prepare($con, $posts_sql);
    mysqli_stmt_bind_param($stmt, 'iii', $content_type_id,$limit, $offset);
    mysqli_stmt_execute($stmt);

} else {

    $all_posts_result = mysqli_query($con, $all_posts_sql);
    $all_posts = $all_posts_result ? mysqli_fetch_all($all_posts_result, MYSQLI_ASSOC) : [];

    $posts_sql .= " ORDER BY ". $order_sort . " " . $order_line . " LIMIT ? OFFSET ?";

    $stmt = mysqli_prepare($con, $posts_sql);
    mysqli_stmt_bind_param($stmt, 'ii', $limit, $offset);
    mysqli_stmt_execute($stmt);
}

$result = mysqli_stmt_get_result($stmt);

$posts = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

$all_posts_num = count($all_posts);
$num_pages = ceil($all_posts_num / $limit);

for ($i = 0; $i < count($posts); $i++) {
    $posts[$i]['is_like'] = is_like($con, $posts[$i]['id'], $_SESSION['user_id']);
}


$content = include_template('popular.php', [
    'posts' => $posts,
    'content_types' => $content_types,
    'content_type_id' => $content_type_id,
    'all_posts_num' => $all_posts_num,
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
        'header_type' => 'popular'
    ]
));
