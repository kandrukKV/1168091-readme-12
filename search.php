<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location:' . 'index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

include_once('helpers.php');
include_once('functions.php');
include('sql-requests.php');

$con = connect_to_database();

$search_results = [];

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['search_request'])) {

    $search_request = trim($_GET['search_request']);

    $search_results = get_found_posts($con, $search_request);
}

for ($i = 0; $i < count($search_results); $i++) {
    $search_results[$i]['tags'] = get_tags($con, $search_results[$i]['id']);
    $search_results[$i]['num_reposts'] = get_num_reposts($con, $search_results[$i]['id']);
    $search_results[$i]['is_like'] = is_like($con, $search_results[$i]['id'], $_SESSION['user_id']);
}

$all_posts = include_template('posts.php', [
    'posts' => $search_results,
    'post_type' => 'feed'
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
    'user_id' => $_SESSION['user_id'],
    'header_type' => 'search',
    'all_msg_count' => get_count_my_massages($con, $user_id)
]));
