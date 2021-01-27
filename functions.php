<?php

function vardump($var)
{
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}

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

function get_how_much_time($time)
{
    $diffTime = time() - strtotime($time);
    $diffMinutes = floor($diffTime / 60);

    if ($diffMinutes < 60) {
        return $diffMinutes . get_noun_plural_form($diffMinutes, ' минута', ' минуты', ' минут') . ' назад';
    };

    $diffHours = floor($diffMinutes / 60);

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

    $diffMounts = floor($diffWeeks / 4);

    return $diffMounts . get_noun_plural_form($diffMounts, ' месяц', ' месяца', ' месяцев') . ' назад';
}

function get_post_val($name)
{
    return $_POST[$name] ?? "";
}

function hash_tags_validation($str)
{
    $tags = explode(' ', trim($str));
    for ($i = 0; $i < count($tags); $i++) {
        if (!preg_match("/(^|\B)#(?![0-9_]+\b)([a-zA-Z0-9_]{1,19})(\b|\r)/", $tags[$i])) {
            return false;
        }
    }
    return $tags;
}

function add_post ($con, $title, $content, $content_type, $user_id)
{
    $sql = "SELECT `id` FROM `content_type` WHERE `class_name` = '$content_type'";

    $res = mysqli_query($con, $sql);
    $content_type_id = mysqli_fetch_assoc($res)['id'];

    if (!$content_type_id) {
        return false;
    }

    vardump($content_type_id);
    $sql = "INSERT INTO `posts` (`title`, `content`, `user_id`, `content_type_id`) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ssii', $title, $content, $user_id, $content_type_id);

    return mysqli_stmt_execute($stmt);
}

function add_quote_post ($con, $title, $content, $author, $content_type, $user_id) {
    $sql = "SELECT `id` FROM `content_type` WHERE `class_name` = '$content_type'";

    $res = mysqli_query($con, $sql);
    $content_type_id = mysqli_fetch_assoc($res)['id'];

    if (!$content_type_id) {
        return false;
    }

    vardump($content_type_id);
    $sql = "INSERT INTO `posts` (`title`, `content`, `quote_author`, `user_id`, `content_type_id`) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'sssii', $title, $content, $author, $user_id, $content_type_id);

    return mysqli_stmt_execute($stmt);
}
