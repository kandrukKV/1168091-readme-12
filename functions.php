<?php

function vardump($var) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
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
};
