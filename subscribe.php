<?php

include_once('helpers.php');
include_once('functions.php');
include_once('sql-requests.php');
include_once('mail.php');

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location:' . 'index.php');
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location:' . $_SERVER['HTTP_REFERER'] ?? 'index.php');
    exit();
}

$con = connect_to_database();

$author = $_SESSION['user_id'];
$subscriber = $_GET['id'];

$user = get_user_info($con, $subscriber);
$isSubscribe = is_subscribe($con, $author, $subscriber);


if ($user) {

    if (set_subscriber($con, $author, $subscriber, $isSubscribe)) {

        if (!$isSubscribe) {
            $target_email = [];
            array_push($target_email, $user['email']);

            $body = 'Здравствуйте, '
                . $user['login'] . ' на вас подписался новый пользователь '
                . $_SESSION['login'] . '. Вот ссылка на его профиль '
                . 'http://'
                . $_SERVER['SERVER_NAME']
                . '/profile.php?id=' . $_SESSION['user_id'];

            $subject = 'У вас новый подписчик.';

            $message = (new Swift_Message($subject))
                ->setFrom(['7d559571f8-35eba0@inbox.mailtrap.io' => 'readme: оповещение'])
                ->setTo($target_email)
                ->setBody($body);

            $mailer->send($message);
        }

        header('Location:' . $_SERVER['HTTP_REFERER'] ?? 'index.php');
        exit();
    } else {
        echo "Ошибка сервера";
        http_response_code(500);
        exit();
    }

} else {
    header('Location:' . $_SERVER['HTTP_REFERER'] ?? 'index.php');
    exit();
}
