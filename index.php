<?php
include_once ('helpers.php');
date_default_timezone_set('Europe/Moscow');

$is_auth = rand(0, 1);

$user_name = 'Mr.Constantine'; // укажите здесь ваше имя

$con = mysqli_connect("localhost", "root", "","readme");

mysqli_set_charset($con, "utf8");

if ($con == false) {
    print("Ошибка подключения: " . mysqli_connect_error());
}
else {
    $sql = "SELECT type_name, class_name FROM content_type";
    $result = mysqli_query($con, $sql);
    $content_types = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $sql = "SELECT p.id, p.datetime, p.title, p.content, u.login, u.avatar, c_t.type_name, c_t.class_name FROM posts p
        JOIN users u ON p.user_id = u.id
        JOIN content_type c_t ON p.content_type_id = c_t.id
        ORDER BY views_count DESC";

    $result = mysqli_query($con, $sql);

    $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

}

function cropText ($text, $textLimit = 300)
{
    if (mb_strlen($text) <= $textLimit) {
        return $text;
    }

    $words = explode(' ', $text);
    $temp = [];
    $currentLengthText = 0;

    for ($i = 0; $i < count($words); $i++) {

        $currentLengthText += mb_strlen($words[$i]);

        if ($currentLengthText <= $textLimit) {
            $temp[] = $words[$i];
        } else {
            break;
        }

        $currentLengthText++;
    }

    return implode(' ', $temp) . '...';
};

function getHowMuchTime ($time)
{
    $diffTime = time() - strtotime($time);
    $diffMinuts = floor($diffTime / 60);

    if ($diffMinuts < 60) {

        return $diffMinuts . get_noun_plural_form($diffMinuts, ' минута', ' минуты', ' минут') . ' назад';
    };

    $diffHours = floor($diffMinuts / 60);

    if ($diffHours < 24) {
        return $diffHours . get_noun_plural_form($diffHours, ' час', ' часа', ' часов') . ' назад';
    };

    $diffDays = floor($diffHours / 24);

    if ($diffDays < 7) {
        return $diffDays . get_noun_plural_form($diffDays, ' день', ' дня', ' дней') . ' назад';
    };

    $diffWeeks = floor($diffDays / 7);

    if ($diffWeeks < 5) {
        return $diffWeeks . get_noun_plural_form($diffWeeks, ' неделя', ' недели', ' недель') . ' назад';
    };

    $diffMonts = floor($diffWeeks / 4);

    return $diffMonts . get_noun_plural_form($diffMonts, ' месяц', ' месяца', ' месяцев') . ' назад';
};

if (!$posts || !$content_types) {
    $content = '';
} else {
    $content = include_template('main.php', [
        'posts' => $posts,
        'content_types' => $content_types
    ]);
}

print (include_template('layout.php', [
        'title' => 'readme: популярное',
        'content' => $content,
        'is_auth' => $is_auth,
        'user_name' => $user_name
    ]
));
