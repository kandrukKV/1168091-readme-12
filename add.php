<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location:' . 'index.php');
    exit();
}

include_once ('functions.php');
include_once ('helpers.php');

$is_auth = 1;
$user_name = 'Mr.Constantine'; // укажите здесь ваше имя

$con = connect_to_database();

$sql = "SELECT `type_name`, `class_name` FROM `content_type`";

$result = mysqli_query($con, $sql);

$content_types = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

$current_tab = $_POST['content_type'] ?? 'photo';

$errors = [];

if ($_SERVER['REQUEST_METHOD']=='POST') {

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
                $file_name = uniqid();
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
                $add_result = add_post($con, $_POST['title'], $file_name, null, $current_tab, 3);
            } else {
                $errors['content'] = 'Ошибка записи файла';
            }
        } else {
            $add_result = add_post($con, $_POST['title'], $_POST['content'], $_POST['author'] ?? null, $current_tab, 3);
        }


        if ($add_result) {
            $last_index = mysqli_insert_id($con);

            if (isset($_POST['tags'])) {
                add_tags($con, $tags, $last_index);
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

print (include_template('layout.php', [
        'title' => 'readme: добавление публикации',
        'content' => $content,
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'header_type' => 'add_post'
    ]
));
