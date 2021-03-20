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

$con = connect_to_database();

$list_id = $_GET['list_id'] ?? null;
$is_show_form = true;

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

$members = get_members($con, $user_id);

for ($i = 0; $i < count($members); $i++) {
    $last_message = get_last_message($con, $user_id, $members[$i]['member_id']);
    $members[$i]['last_message_date'] = $last_message['datetime'];
    $members[$i]['last_message'] = $last_message['content'];
    $members[$i]['new_msg_count'] = $last_message['new_msg_count'];
}

$dateArray = [];

foreach ($members as $key => $arr) {
    $dateArray[$key] = $arr['last_message_date'];
}

array_multisort($dateArray, SORT_DESC, $members);

$messages = $list_id ? get_correspondence($con, $user_id, $list_id) : [];

if ($list_id && count($messages) === 0 && $list_id != $user_id) {
    $recipient = get_user_info($con, $list_id);

    if (!$recipient) {
        echo "Страница не найдена";
        http_response_code(404);
        exit();
    }

    $member = [];
    $member['member_id'] = $recipient['id'];
    $member['login'] = $recipient['login'];
    $member['avatar'] = $recipient['avatar'];
    $member['last_message'] = null;

    array_unshift($members, $member);
}

set_is_not_new_message($con, $user_id, $list_id);

$content = include_template('messages.php', [
    'members' => $members,
    'list_id' => $list_id,
    'messages' => $messages,
    'errors' => $errors,
    'is_show_form' => $is_show_form
]);

print(include_template('layout.php', [
        'title' => 'readme: популярное',
        'content' => $content,
        'user_name' => $_SESSION['login'],
        'header_type' => 'messages',
        'all_msg_count' => get_count_my_massages($con, $user_id)
    ]));
