<?php
include_once ('helpers.php');
date_default_timezone_set('Europe/Moscow');

$is_auth = rand(0, 1);

$user_name = 'Mr.Constantine'; // укажите здесь ваше имя

$posts = [
    [
        'title' => 'Цитата',
        'type' => 'post-quote',
        'content' => 'Мы в жизни любим только раз, а после ищем лишь похожих.',
        'user_name' => 'Лариса',
        'avatar' => 'userpic-larisa-small.jpg'
    ],
    [
        'title' => 'Игра престолов',
        'type' => 'post-text',
        'content' => 'Озеро Байкал – огромное древнее озеро в горах Сибири к северу от монгольской границы. Байкал
                        считается самым глубоким озером в мире. Он окружен сетью пешеходных маршрутов, называемых
                        Большой байкальской тропой. Деревня Листвянка, расположенная на западном берегу озера, –
                        популярная отправная точка для летних экскурсий. Зимой здесь можно кататься на коньках и
                        собачьих упряжках.',
        'user_name' => 'Владик',
        'avatar' => 'userpic.jpg'
    ],
    [
        'title' => 'Наконец, обработал фотки!',
        'type' => 'post-photo',
        'content' => 'rock-medium.jpg',
        'user_name' => 'Виктор',
        'avatar' => 'userpic-mark.jpg'
    ],
    [
        'title' => 'Моя мечта',
        'type' => 'post-photo',
        'content' => 'coast-medium.jpg',
        'user_name' => 'Лариса',
        'avatar' => 'userpic-larisa-small.jpg'
    ],
    [
        'title' => 'Лучшие курсы',
        'type' => 'post-link',
        'content' => 'www.htmlacademy.ru',
        'user_name' => 'Владик',
        'avatar' => 'userpic.jpg'
    ]
];

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

$content = include_template('main.php', ['posts' => $posts]);

print (include_template('layout.php', [
        'title' => 'readme: популярное',
        'content' => $content,
        'is_auth' => $is_auth,
        'user_name' => $user_name
    ]
));
