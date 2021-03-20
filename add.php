<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location:' . 'index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

include_once('functions.php');
include_once('helpers.php');
include('sql-requests.php');
include_once('mail.php');

$add_result = null;

$con = connect_to_database();

$content_types = get_content_types($con);

$current_tab = $_GET['tab'] ?? 'photo';

$is_correct_current_tab = false;

for ($i = 0; $i < count($content_types); $i++) {
    if ($content_types[$i]['class_name'] === $current_tab) {
        $is_correct_current_tab = true;
        break;
    }
}

if (!$is_correct_current_tab) {
    echo "Страница не найдена";
    http_response_code(404);
    exit();
}


$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['title'])) {
        $errors['title'] = 'Заголовок. Это поле должно быть заполнено.';
    }

    if (!empty($_POST['tags'])) {
        $tags = hash_tags_validation($_POST['tags']);
        if (!$tags) {
            $errors['tags'] = 'Хештеги. Неверный формат хештегов';
        }
    }

    if ($current_tab === 'photo' || $current_tab === 'video' || $current_tab === 'link') {
        if (!empty($_POST['content']) && !filter_var($_POST['content'], FILTER_VALIDATE_URL)) {
            $errors['content'] = 'Ссылка. Неверный формат ссылки.';
        }
    }

    if ($current_tab !== 'photo') {
        if (empty($_POST['content'])) {
            $errors['content'] = 'Контент. Это поле должно быть заполнено.';
        }
    }

    $file_name = '';

    switch ($current_tab) {
        case 'photo':

            if ($_FILES['photo-file']['error'] !== 0 && empty($_POST['content'])) {
                $errors['content'] = 'Контент. Необходимо указать ссылку или прикрепить файл с изображением.';
            }

            if (isset($_FILES['photo-file']) && $_FILES['photo-file']['error'] === 0) {
                $file_info = finfo_open(FILEINFO_MIME_TYPE);
                $file_name = $_FILES['photo-file']['tmp_name'];

                $file_type = finfo_file($file_info, $file_name);

                if ($file_type !== 'image/gif' && $file_type !== 'image/png' && $file_type !== 'image/jpeg') {
                    $errors['photo-file'] = 'Неверный формат файла';
                }
            }
            break;
        case 'video':
            if (!empty($_POST['content'])) {
                $video_error = check_youtube_url($_POST['content']);
                if ($video_error !== true) {
                    $errors['content'] = $video_error;
                }
            }
            break;
        case 'quote':
            if (empty($_POST['author'])) {
                $errors['author'] = 'Автор. Это поле должно быть заполнено.';
            }
            break;
    }

    if (count($errors) === 0) {
        if ($current_tab === 'photo') {
            $file_path = __DIR__ . '/uploads/';
            $file_is_saved = false;

            if ($_FILES['photo-file']['error'] === 0) {
                $file_name = uniqid() . $_FILES['photo-file']['name'];
                $file_is_saved = move_uploaded_file($_FILES['photo-file']['tmp_name'], $file_path . $file_name);
            } elseif (!empty($_POST['content'])) {
                $img = file_get_contents($_POST['content']);
                if ($img) {
                    $file_info = finfo_open(FILEINFO_MIME_TYPE);
                    $mime_type = finfo_buffer($file_info, $img);

                    switch ($mime_type) {
                        case 'image/gif':
                            $file_name .= '.gif';
                            break;
                        case 'image/png':
                            $file_name .= '.png';
                            break;
                        case 'image/jpeg':
                            $file_name .= '.jpg';
                            break;
                        default:
                            $file_name = '';
                    }

                    if (!$file_name) {
                        $errors['content'] = 'Неверный формат файла';
                    } else {
                        $file_is_saved = file_put_contents($file_path . $file_name, $img);
                    }
                } else {
                    $errors['content'] = 'Невозможно загрузить файл';
                }
            }

            if ($file_is_saved) {
                $add_result = add_post($con, $_POST['title'], $file_name, null, $current_tab, $_SESSION['user_id']);
            } else {
                $errors['content'] = 'Ошибка записи файла';
            }
        } else {
            $add_result = add_post($con, $_POST['title'], $_POST['content'], $_POST['author'] ?? null, $current_tab, $_SESSION['user_id']);
        }


        if ($add_result) {
            $last_index = mysqli_insert_id($con);

            if (!empty($_POST['tags'])) {
                add_tags($con, $tags, $last_index);
            }

            $subscribers = get_subscribers($con, $user_id);

            foreach ($subscribers as $subscriber) {
                $target_email = [];

                array_push($target_email, $subscriber['email']);

                $body = 'Здравствуйте, ' . $subscriber['login'] . '. Пользователь '
                    . $_SESSION['login'] . ' только что опубликовал новую запись ' . $_POST['title']
                    . '. Посмотрите её на странице пользователя '
                    . 'http://'
                    . $_SERVER['SERVER_NAME']
                    . '/profile.php?id=' . $_SESSION['user_id'] . '.';

                $subject = 'Новая публикация от пользователя ' . $_SESSION['login'];

                $message = (new Swift_Message($subject));
                $message ->setFrom(['7d559571f8-35eba0@inbox.mailtrap.io' => 'readme: оповещение']);
                $message->setTo($target_email)->setBody($body);

                $mailer->send($message);
            }

            header('Location:' . 'post.php?id=' . $last_index);
            exit();
        }
    }
}

$content = include_template('add-post.php', [
    'content_types' => $content_types,
    'current_tab' => $current_tab,
    'errors' => $errors
]);

print(include_template('layout.php', [
    'title' => 'readme: добавление публикации',
    'content' => $content,
    'user_name' => $_SESSION['login'],
    'user_id' => $_SESSION['user_id'],
    'header_type' => 'add_post',
    'all_msg_count' => get_count_my_massages($con, $user_id)]));
