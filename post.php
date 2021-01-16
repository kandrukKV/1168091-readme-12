<?php
include_once ('functions.php');
include_once ('helpers.php');

$is_auth = 1;

$user_name = 'Mr.Constantine'; // укажите здесь ваше имя

if (!isset($_GET['id'])) {
    echo "Страница не найдена";
    http_response_code(404);
    exit;
}

$post_id = $_GET['id'];

$con = mysqli_connect("localhost", "root", "","readme");

if (!$con) {
    echo "Ошибка подключения к базе данных";
    http_response_code(500);
    exit;
}

mysqli_set_charset($con, "utf8");

$sql = "SELECT p.datetime, p.title, p.content, p.link, p.views_count, p.user_id, p.quote_author, p.views_count, u.login, u.avatar, u.datetime as user_datetime, c_t.class_name FROM posts p
    JOIN users u ON p.user_id = u.id
    JOIN content_type c_t ON p.content_type_id = c_t.id
    WHERE p.id = ?";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, 'i', $post_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$post_details = mysqli_fetch_array($result, MYSQLI_ASSOC);

if (!$post_details) {
    echo "Страница не найдена";
    http_response_code(404);
    exit;
}

$user_id = $post_details['user_id'];
$sql = "SELECT * FROM posts WHERE user_id = $user_id";
$result = mysqli_query($con, $sql);
$user_num_of_posts = mysqli_num_rows($result) ? mysqli_num_rows($result) : '0';

$sql = "SELECT * FROM subscribers WHERE author = $user_id";
$result = mysqli_query($con, $sql);
$user_num_of_subscribers = mysqli_num_rows($result) ? mysqli_num_rows($result) : '0';

switch ($post_details['class_name']) {
    case 'text':
        $content_inner = include_template('post-text.php', [
            'content' => $post_details['content']
        ]);
        break;
    case 'photo':
        $content_inner = include_template('post-photo.php', [
            'content' => $post_details['content']
        ]);
        break;
    case 'quote':
        $content_inner = include_template('post-quote.php', [
            'content' => $post_details['content'],
            'quote_author' => $post_details['quote_author']
        ]);
        break;
    case 'link':
        $content_inner = include_template('post-link.php', [
            'content' => $post_details['content'],
            'link' => $post_details['link']

        ]);
        break;
    case 'video':
        $content_inner = include_template('post-video.php', [
            'content' => $post_details['content']
        ]);
}

$content = include_template('post-details.php', [
    'content_inner' => $content_inner,
    'post_details' => $post_details,
    'user_num_of_posts' => $user_num_of_posts,
    'user_num_of_subscribers' => $user_num_of_subscribers
]);

print (include_template('layout.php', [
        'title' => 'readme: публикация',
        'content' => $content,
        'is_auth' => $is_auth,
        'user_name' => $user_name
    ]
));
