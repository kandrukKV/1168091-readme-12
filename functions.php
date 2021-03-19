<?php

/**
 * Обрезает текст
 *
 * @param string $text  текст
 * @param int $text_limit максимальное количество символов
 *
 * @return string
 */

function crop_text($text, $text_limit = 300)
{
    if (mb_strlen($text) <= $text_limit) {
        return $text;
    }

    $words = explode(' ', $text);
    $temp = [];
    $current_length_text = 0;

    for ($i = 0; $i < count($words); $i++) {

        $current_length_text += mb_strlen($words[$i]);

        if ($current_length_text <= $text_limit) {
            $temp[] = $words[$i];
        } else {
            break;
        }

        $current_length_text++;
    }

    return implode(' ', $temp) . '...';
}

/**
 * Возвращает в текстовом виде количество времени которое прошло с момента $time
 *
 * @param string $time дата и время
 *
 * @return string
 */

function get_how_much_time($time)
{
    $diffTime = time() - strtotime($time);

    $diffMinutes = floor($diffTime / 60);

    if ($diffMinutes < 60) {
        return $diffMinutes . get_noun_plural_form($diffMinutes, ' минута', ' минуты', ' минут');
    };

    $diffHours = floor($diffMinutes / 60);

    if ($diffHours < 24) {
        return $diffHours . get_noun_plural_form($diffHours, ' час', ' часа', ' часов');
    };

    $diffDays = floor($diffHours / 24);

    if ($diffDays < 7) {
        return $diffDays . get_noun_plural_form($diffDays, ' день', ' дня', ' дней');
    };

    $diffWeeks = floor($diffDays / 7);

    if ($diffWeeks < 5) {
        return $diffWeeks . get_noun_plural_form($diffWeeks, ' неделя', ' недели', ' недель');
    };

    $diffMounts = floor($diffWeeks / 4);

    return $diffMounts . get_noun_plural_form($diffMounts, ' месяц', ' месяца', ' месяцев');
}

/**
 * Возврашает поле из массива $_POST
 *
 * @param string $name  название поля
 *
 * @return string | int | bool
 */

function get_post_val($name)
{
    return $_POST[$name] ?? "";
}

/**
 * Возврашает массив хештегов и валидирует их
 *
 * @param string $str  строка с хештегами
 *
 * @return array | false
 */


function hash_tags_validation($str)
{
    $tags = explode(' ', trim($str));
    for ($i = 0; $i < count($tags); $i++) {
        if (!preg_match("/^[a-zа-яё]+$/iu", $tags[$i])) {
            return false;
        }
    }
    return $tags;
}

/**
 * Возвращает html разметку поста в зависимости от его типа
 *
 * @param array $post  строка из таблицы posts
 *
 * @return string
 */


function get_post_template($post)
{
    switch ($post['class_name']) {
        case 'text':
            $content_inner = include_template('post-text.php', [
                'content' => $post['content']
            ]);
            break;
        case 'photo':
            $content_inner = include_template('post-photo.php', [
                'content' => $post['content']
            ]);
            break;
        case 'quote':
            $content_inner = include_template('post-quote.php', [
                'content' => $post['content'],
                'quote_author' => $post['quote_author']
            ]);
            break;
        case 'link':
            $content_inner = include_template('post-link.php', [
                'content' => $post['content'],
                'link' => $post['link']

            ]);
            break;
        case 'video':
            $content_inner = include_template('post-video.php', [
                'content' => $post['content']
            ]);
            break;
        default:
            $content_inner = '';
    }

    return $content_inner;
}
