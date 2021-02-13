<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location:' . 'index.php');
    exit();
}

include_once ('helpers.php');
include_once ('functions.php');

$con = connect_to_database();

$search_results = [];

if ($_SERVER['REQUEST_METHOD']=='GET' && isset($_GET['search_request'])) {

    $search_request = trim($_GET['search_request']);

    $sql = "
        SELECT p.id,
               p.datetime,
               p.title,
               p.content,
               p.link,
               p.quote_author,
               u.login,
               u.avatar,
               c_t.type_name,
               c_t.class_name
        FROM posts p
        JOIN users u ON p.user_id = u.id
        JOIN content_type c_t ON p.content_type_id = c_t.id
        WHERE MATCH(title, content) AGAINST(?)";

    if (count(explode(" ", $search_request)) === 1 && preg_match("/^[#]/", $search_request)) {

        $search_request = substr($search_request, 1);

        $sql = "
        SELECT
            p_ht.post_id,
            p_ht.hashtag_id,
            p.id,
            p.datetime,
            p.title,
            p.content,
            p.link,
            p.quote_author,
            u.login,
            u.avatar,
            c_t.type_name,
            c_t.class_name,
            h_t.id,
            h_t.tag_name
        FROM posts_hashtags p_ht
        JOIN posts p ON p_ht.post_id = p.id
        JOIN users u ON p.user_id = u.id
        JOIN content_type c_t ON p.content_type_id = c_t.id
        JOIN hash_tags h_t ON p_ht.hashtag_id = h_t.id
        WHERE h_t.tag_name = ?";
    }

    $stmt = mysqli_prepare($con, $sql);

    mysqli_stmt_bind_param($stmt, 's', $search_request);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $search_results = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

$all_posts = include_template('posts.php', [
    'posts' => $search_results
]);

if (count($search_results) > 0) {
    $content = include_template('search-result.php', [
        'all_posts' => $all_posts
    ]);
} else {
    $content = include_template('no-results.php');
}

print (include_template('layout.php', [
    'title' => 'readme: страница результатов поиска',
    'content' => $content,
    'user_name' => $_SESSION['login'],
    'header_type' => 'search',
]));