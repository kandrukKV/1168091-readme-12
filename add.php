<?php

include_once ('functions.php');
include_once ('helpers.php');

$is_auth = 1;
$user_name = 'Mr.Constantine'; // укажите здесь ваше имя

$con = mysqli_connect("localhost", "root", "","readme");

if (!$con) {
    echo "Ошибка подключения к базе данных";
    http_response_code(500);
    exit;
}

mysqli_set_charset($con, "utf8");

$sql = "SELECT `type_name`, `class_name` FROM `content_type`";

$result = mysqli_query($con, $sql);

$content_types = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

$current_tab = $_POST['content_type'] ?? 'photo';

$errors = [];

if (isset($_POST['submit'])) {

    if (empty($_POST[$current_tab . '-title'])) {
        $errors[$current_tab . '-title'] = 'Заголовок. Это поле должно быть заполнено.';
    }

    if (!empty($_POST[$current_tab . '-tags'])) {
        $tags = hash_tags_validation($_POST[$current_tab . '-tags']);
        if (!$tags) {
            $errors[$current_tab . '-tags'] = 'Хештеги. Неверный формат хештегов';
        }
    }

    if ($current_tab === 'photo' || $current_tab === 'video' || $current_tab === 'link') {
        if (!empty($_POST[$current_tab . '-content']) && !filter_var($_POST[$current_tab . '-content'], FILTER_VALIDATE_URL)) {
            $errors[$current_tab . '-content'] = 'Ссылка. Неверный формат ссылки.';
        }
    }

    if ($current_tab !== 'photo') {
        if (empty($_POST[$current_tab . '-content'])) {
            $errors[$current_tab . '-content'] = 'Контент. Это поле должно быть заполнено.';
        }
    }

    switch ($current_tab) {
        case 'photo':

            if ($_FILES['photo-file']['error'] !== 0 && empty($_POST['photo-content'])) {
                $errors['photo-content'] = 'Контент. Необходимо указать ссылку или прикрепить файл с изображением.';
            }

            if (isset($_FILES['photo-file']) && $_FILES['photo-file']['error'] === 0) {

                $file_info = finfo_open(FILEINFO_MIME_TYPE);
                $file_name = $_FILES['photo-file']['tmp_name'];
                $file_size = $_FILES['photo-file']['size'];

                $file_type = finfo_file($file_info, $file_name);

                if ($file_type !== 'image/gif' && $file_type !== 'image/png' && $file_type !== 'image/jpeg') {
                    $errors['photo-file'] = 'Неверный формат файла';
                } elseif ($file_size > 2000000) {
                    $errors['photo-file'] = 'Размер файла превышает 2 Мб';
                }
            }
            break;
        case 'video':
            if (!empty($_POST['video-content'])) {
                $video_error = check_youtube_url($_POST['video-content']);
                if ($video_error !== true) {
                    $errors['video-content'] = $video_error;
                }
            }
            break;
        case 'quote':
            if (empty($_POST[$current_tab . '-author'])) {
                $errors[$current_tab . '-author'] = 'Автор. Это поле должно быть заполнено.';
            }
            break;
    }

    if (count($errors) === 0) {
        echo $current_tab;
        switch ($current_tab){
            case 'photo':
                $file_path = __DIR__ . '/uploads/';
                $file_is_saved = false;

                if ($_FILES['photo-file']['error'] === 0) {
                    $file_name = uniqid() . $_FILES['photo-file']['name'];
                    $file_is_saved = move_uploaded_file($_FILES['photo-file']['tmp_name'], $file_path . $file_name);

                } elseif (!empty($_POST['photo-content'])) {
                    $img = file_get_contents($_POST['photo-content']);
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
                            $errors['photo-content'] = 'Неверный формат файла';
                        } else {
                            $file_is_saved = file_put_contents($file_path . $file_name, $img);
                        }
                    } else {
                        $errors['photo-content'] = 'Невозможно загрузить файл';
                    }
                }

                if ($file_is_saved) {
                    $add_result = add_post($con, $_POST['photo-title'], $file_name, $current_tab, 1);
                }
                break;
            case 'video':
                $add_result = add_post($con, $_POST['video-title'], $_POST['video-content'], $current_tab, 2);
                break;
            case 'text':
                $add_result = add_post($con, $_POST['text-title'], $_POST['text-content'], $current_tab, 3);
                break;
            case 'quote':
                $add_result = add_quote_post($con, $_POST['quote-title'], $_POST['quote-content'], $_POST['quote-author'], $current_tab, 3);
                break;
            case 'link':
                $add_result = add_post($con, $_POST['link-title'], $_POST['link-content'], $current_tab, 3);
                break;

        }
        if ($add_result) {
            header('Location:' . 'post.php?id=' . mysqli_insert_id($con));
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
