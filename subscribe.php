<?php

include_once ('helpers.php');
include_once ('functions.php');
include_once ('mail.php');

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

$sql = "SELECT * FROM users WHERE id=?";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, 'i', $subscriber);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user) {

    $sql = "SELECT * FROM subscribers WHERE author=? AND subscription=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $author, $subscriber);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $isSubscribe = mysqli_fetch_row($result);

    if ($isSubscribe) {
        $sql = "DELETE FROM subscribers WHERE author=? AND subscription=?";
    } else {
        $sql = "INSERT INTO subscribers (author, subscription) VALUES (?, ?)";
    }

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $author, $subscriber);

    if (mysqli_stmt_execute($stmt)) {

        if (!$isSubscribe) {
            $target_email = [];
            array_push($target_email, $user['email']);

            $body = 'Здравствуйте, '
                . $user['login'] . ' на вас подписался новый пользователь '
                . $_SESSION['login'] . '. Вот ссылка на его профиль '
                . $_SERVER['REQUEST_SCHEME']
                . '://'
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
