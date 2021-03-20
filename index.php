<?php

session_start();
if (isset($_SESSION['user_id'])) {
    header('Location:' . 'feed.php');
    exit();
}

include_once('helpers.php');
include_once('functions.php');
include_once('sql-requests.php');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $con = connect_to_database();

    if (empty($_POST['email'])) {
        $errors['email'] = 'Email. Это поле не должно быть пустым.';
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email. Неверный формат электронного адреса.';
    } else {
        $res = checkEmail($con, $_POST['email']);
        if (mysqli_num_rows($res) === 0) {
            $errors['email'] = 'Такой пользователь не зарегистрирован.';
        }
    }

    if (empty($_POST['password'])) {
        $errors['password'] = 'Пароль. Это поле не должно быть пустым.';
    }

    if (count($errors) === 0) {
        $user_data = get_user_by_email($con, $_POST['email']);
        $passwordHash = $user_data['pass'];

        if (password_verify($_POST['password'], $passwordHash)) {
            $_SESSION['user_id'] = $user_data['id'];
            $_SESSION['login'] = $user_data['login'];
            $_SESSION['avatar'] = $user_data['avatar'];

            header('Location:' . 'feed.php');
            exit();
        } else {
            $errors['password'] = 'Неправильный пароль.';
        }
    }
}

print(include_template('main.php', [
    'errors' => $errors
]));


