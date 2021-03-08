<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location:' . 'index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

include_once ('functions.php');
include_once ('helpers.php');

$con = connect_to_database();

$list_id = $_GET['list_id'] ?? '0';
$errors = [];

if ($_SERVER['REQUEST_METHOD']=='POST') {

    if (empty($_POST['comment'])) {
        $errors['comment'] = 'Это поле должно быть заполнено.';
    }

    if (count($errors) === 0) {
        $res = send_message($con, $_POST['comment'], $user_id, $list_id);
        if ($res) {
            header('Location:' . 'messages.php?list_id=' . $list_id);
            exit();
        } else {
            $errors['content'] = 'Неудачная попытка отправки';
        }
    }

}


$messages = get_correspondence($con, $user_id, $list_id);
$members = get_members($con, $user_id);

set_is_new_message($con, $user_id, $list_id);

for ($i = 0; $i < count($members); $i++) {
    $members[$i]['last_message'] = get_last_message($con, $user_id, $members[$i]['member_id']);
}

$content = include_template('messages.php', [
    'members' => $members,
    'list_id' => $list_id,
    'messages' => $messages,
    'errors' => $errors
]);

print (include_template('layout.php', [
        'title' => 'readme: популярное',
        'content' => $content,
        'user_name' => $_SESSION['login'],
        'header_type' => 'messages',
        'all_msg_count' => get_count_my_massages($con, $user_id)
    ]
));
