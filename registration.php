<?php

session_start();
if (isset($_SESSION['user_id'])) {
    header('Location:' . 'index.php');
    exit();
}

include_once('helpers.php');
include_once('functions.php');
include_once('sql-requests.php');

$form_fields = [
    'email' => [
        'type' => 'email',
        'label' => 'Электронная почта',
        'placeholder' => 'Укажите электронную почту'
    ],
    'login' => [
        'type' => 'text',
        'label' => 'Логин',
        'placeholder' => 'Укажите логин'
    ],
    'password' => [
        'type' => 'password',
        'label' => 'Пароль',
        'placeholder' => 'Укажите пароль'
    ],
    'password-repeat' => [
        'type' => 'password',
        'label' => 'Повтор пароля',
        'placeholder' => 'Повторите ввод пароля'
    ]
];

$con = connect_to_database();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['email'])) {
        $errors['email'] = 'Email. Это поле не должно быть пустым.';
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email. Неверный формат электронного адреса.';
    } else {
        $res = checkEmail($con, $_POST['email']);

        if (mysqli_num_rows($res) > 0) {
            $errors['email'] = 'Email. Такой адрес уже был зарегистрирован.';
        }
    }

    if (empty($_POST['login'])) {
        $errors['login'] = 'Логин. Это поле не должно быть пустым.';
    }

    if (empty($_POST['password'])) {
        $errors['password'] = 'Пароль. Это поле не должно быть пустым.';
    }

    if (empty($_POST['password-repeat'])) {
        $errors['password-repeat'] = 'Повтор пароля. Это поле не должно быть пустым.';
    }

    if (!empty($_POST['password-repeat']) && !empty($_POST['password'])) {
        if ($_POST['password-repeat'] !== $_POST['password']) {
            $errors['password-repeat'] = 'Повтор пароля. Пароли не совпадают.';
        }
    }

    if (isset($_FILES['userpic-file']) && $_FILES['userpic-file']['error'] === 0) {
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $file_name = $_FILES['userpic-file']['tmp_name'];

        $file_type = finfo_file($file_info, $file_name);

        if ($file_type !== 'image/gif' && $file_type !== 'image/png' && $file_type !== 'image/jpeg') {
            $errors['photo-file'] = 'Неверный формат файла';
        }
    }

    if (count($errors) === 0) {
        $file_is_saved = false;

        if (isset($_FILES['userpic-file']) && $_FILES['userpic-file']['error'] === 0) {
            $file_path = __DIR__ . '/uploads/';
            $file_name = uniqid() . $_FILES['userpic-file']['name'];
            $file_is_saved = move_uploaded_file($_FILES['userpic-file']['tmp_name'], $file_path . $file_name);
        }

        $add_user = add_user($con, $_POST['email'], $_POST['login'], $_POST['password'], $file_is_saved ? $file_name : null);

        if ($add_user) {
            $last_index = mysqli_insert_id($con);

            $_SESSION['user_id'] = $last_index;
            $_SESSION['login'] = $_POST['login'];
            $_SESSION['avatar'] = $file_is_saved ? $file_name : null;

            header('Location:' . 'index.php');
            exit();
        } else {
            $errors['reg'] = "Ошибка при регистрации нового пользователя";
        }
    }

}

$content = include_template('registration.php', [
    'form_fields' => $form_fields,
    'errors' => $errors
]);

print(include_template('layout.php', [
    'title' => 'readme: регистрация',
    'content' => $content,
    'header_type' => 'registration',
]));
