<?php

function vardump($var)
{
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}

function crop_text($text, $text_limit = 300)
{
    if (mb_strlen($text) <= $text_limit) {
        return $text;
    }

    $words = explode(' ', $text);
    $temp = [];
    $current_length_text = 0;

    for ($i = 0; $i < count($words); $i++) {

        $current_length_text += mb_strlen($words[$i]);

        if ($current_length_text <= $text_limit) {
            $temp[] = $words[$i];
        } else {
            break;
        }

        $current_length_text++;
    }

    return implode(' ', $temp) . '...';
}

function get_how_much_time($time)
{

    $diffTime = time() - strtotime($time);

    $diffMinutes = floor($diffTime / 60);

    if ($diffMinutes < 60) {
        return $diffMinutes . get_noun_plural_form($diffMinutes, ' минута', ' минуты', ' минут');
    };

    $diffHours = floor($diffMinutes / 60);

    if ($diffHours < 24) {
        return $diffHours . get_noun_plural_form($diffHours, ' час', ' часа', ' часов');
    };

    $diffDays = floor($diffHours / 24);

    if ($diffDays < 7) {
        return $diffDays . get_noun_plural_form($diffDays, ' день', ' дня', ' дней');
    };

    $diffWeeks = floor($diffDays / 7);

    if ($diffWeeks < 5) {
        return $diffWeeks . get_noun_plural_form($diffWeeks, ' неделя', ' недели', ' недель');
    };

    $diffMounts = floor($diffWeeks / 4);

    return $diffMounts . get_noun_plural_form($diffMounts, ' месяц', ' месяца', ' месяцев');
}

function get_post_val($name)
{
    return $_POST[$name] ?? "";
}

function hash_tags_validation($str)
{
    $tags = explode(' ', trim($str));
    for ($i = 0; $i < count($tags); $i++) {
        if (!preg_match("/^[a-zа-яё]+$/iu", $tags[$i])) {
            return false;
        }
    }

    return $tags;
}

function add_post ($con, $title, $content, $author, $content_type, $user_id)
{
    $sql = "SELECT `id` FROM `content_type` WHERE `class_name` = '$content_type'";

    $res = mysqli_query($con, $sql);
    $content_type_id = mysqli_fetch_assoc($res)['id'];

    if (!$content_type_id) {
        return false;
    }

    $sql = "INSERT INTO `posts` (`title`, `content`, `quote_author`, `user_id`, `content_type_id`) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'sssii', $title, $content, $author, $user_id, $content_type_id);

    return mysqli_stmt_execute($stmt);
}

function add_tags ($con, $tags, $post_id)
{

    $tmp = implode(', ', array_map(function ($item) {
        return "'" . $item . "'";
    }, $tags));

    $res = mysqli_query($con, "SELECT `tag_name` FROM `hash_tags` WHERE `tag_name` in ($tmp)");

    $new_tags = [];

    $medium_tags = mysqli_fetch_all($res);

    foreach ($medium_tags as $value) {
        array_push($new_tags, $value[0]);
    }

    $array_diff = array_diff($tags, $new_tags);

    if (count($array_diff) > 0) {
        $sql = "INSERT INTO `hash_tags` (`tag_name`) VALUES (?)";
        $stmt = mysqli_prepare($con, $sql);

        foreach ($array_diff as $item) {
            mysqli_stmt_bind_param($stmt, 's', $item);
            mysqli_stmt_execute($stmt);
        }
    }

    $sql = "INSERT INTO `posts_hashtags` (`post_id`, `hashtag_id`) VALUES (?, ?)";
    $stmt = mysqli_prepare($con, $sql);

    foreach ($tags as $tag) {
        $res = mysqli_query($con, "SELECT `id` FROM `hash_tags` WHERE `tag_name` = '$tag'");
        $tag_id = mysqli_fetch_assoc($res)['id'];

        if ($tag_id) {
            mysqli_stmt_bind_param($stmt, 'ii', $post_id, $tag_id);
            mysqli_stmt_execute($stmt);
        }
    }
}

function add_user ($con, $email, $login, $pass, $avatar)
{
    $passwordHash = password_hash($pass, PASSWORD_DEFAULT);
    $sql = "INSERT INTO `users` (`email`, `pass`, `login`, `avatar`) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ssss', $email, $passwordHash, $login, $avatar);

    return mysqli_stmt_execute($stmt);
}

function add_view ($con, $post_id)
{
    $sql = "UPDATE `posts` SET `views_count` = `views_count` + 1 WHERE id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $post_id);
    return mysqli_stmt_execute($stmt);
}

function add_comment ($con, $content, $user_id, $post_id)
{
    $sql = "INSERT INTO `comments` (`content`, `user_id`, `post_id`) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'sii', $content,$user_id, $post_id);
    return mysqli_stmt_execute($stmt);
}

function connect_to_database ()
{
    $con = mysqli_connect("localhost", "root", "","readme");

    if (!$con) {
        echo "Ошибка подключения к базе данных";
        http_response_code(500);
        exit();
    }

    mysqli_set_charset($con, "utf8");

    return $con;
}

function checkEmail ($con, $email)
{
    $sql = "SELECT `email` FROM `users` WHERE `email` = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

function get_content_types ($con)
{
    $sql = "SELECT `id`, `type_name`, `class_name` FROM `content_type`";

    $result = mysqli_query($con, $sql);

    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

function content_type_id_is_correct ($con, $content_type_id)
{
    $sql = "SELECT * FROM content_type WHERE id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $content_type_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_array($result, MYSQLI_ASSOC);
}

function get_num_posts ($con, $user_id)
{
    $sql = "SELECT * FROM posts WHERE user_id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($result);
}

function get_num_subscribers ($con, $user_id)
{
    $sql = "SELECT * FROM subscribers WHERE subscription = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($result);
}

function get_num_reposts ($con, $post_id) {
    $sql = "SELECT * FROM posts WHERE original_post_id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $post_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($result);
}

function is_like ($con, $post_id, $user_id) {
    $sql = "SELECT * FROM likes WHERE post_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $post_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($result);
}

function is_subscribe ($con, $user_one, $user_two)
{
    $sql = "SELECT * FROM subscribers WHERE author = ? AND subscription = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $user_one, $user_two);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_row($result);
}

function get_user_info ($con, $user_id)
{
    $sql = "SELECT u.id, u.datetime, u.login, u.email, u.avatar
        FROM users u
        WHERE id = ?";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_array($result, MYSQLI_ASSOC);
}

function get_posts_of_user ($con, $user_id)
{
    $posts_sql = "
                SELECT
            p.id,
            p.datetime,
            p.title,
            p.content,
            p.link,
            p.quote_author,
            p.user_id,
            p.is_repost,
            p.original_post_id,
            pp.user_id as author_id,
            pp.datetime as real_time,
            u_p.login as user_login,
            u_p.avatar as user_avatar,
            u_a.login as author_login,
            u_a.avatar as author_avatar,
            c_t.type_name,
            c_t.class_name,
            (SELECT count(*) FROM likes WHERE post_id = p.id) AS likes_count,
            (SELECT count(*) FROM comments WHERE post_id = p.id) AS comments_count
        FROM posts p
        LEFT JOIN posts pp ON p.original_post_id = pp.id
        JOIN users u_p ON p.user_id = u_p.id
        LEFT JOIN users u_a ON pp.user_id = u_a.id
        JOIN content_type c_t ON p.content_type_id = c_t.id
        WHERE p.user_id = ?";

    $stmt = mysqli_prepare($con, $posts_sql);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

function get_post_template ($post)
{
    switch ($post['class_name']) {
        case 'text':
            $content_inner = include_template('post-text.php', [
                'content' => $post['content']
            ]);
            break;
        case 'photo':
            $content_inner = include_template('post-photo.php', [
                'content' => $post['content']
            ]);
            break;
        case 'quote':
            $content_inner = include_template('post-quote.php', [
                'content' => $post['content'],
                'quote_author' => $post['quote_author']
            ]);
            break;
        case 'link':
            $content_inner = include_template('post-link.php', [
                'content' => $post['content'],
                'link' => $post['link']

            ]);
            break;
        case 'video':
            $content_inner = include_template('post-video.php', [
                'content' => $post['content']
            ]);
            break;
        default:
            $content_inner = '';
    }

    return $content_inner;
}

function get_tags ($con, $post_id)
{
    $sql = "SELECT ht.tag_name FROM posts_hashtags ph JOIN hash_tags ht ON ht.id = ph.hashtag_id WHERE post_id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $post_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $tags = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $tags ? $tags : [];
}

function get_comments ($con, $post_id) {
    $sql = "
        SELECT
            c.datetime,
            c.content,
            c.user_id,
            u.login,
            u.avatar
        FROM comments c
        JOIN users u ON u.id = c.user_id
        WHERE c.post_id = ?
    ";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $post_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $comments = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $comments ? $comments : [];
}

function get_likes_of_user ($con, $user_id)
{
    $posts_sql = "SELECT
                p.id,
                p.title,
                p.content,
                p.link,
                p.quote_author,
                lk.datetime,
                u.id as user_id,
                u.login,
                u.avatar,
                c_t.class_name
            FROM posts p
            JOIN likes lk ON lk.post_id = p.id
            JOIN users u ON u.id = lk.user_id
            JOIN content_type c_t ON p.content_type_id = c_t.id
            WHERE p.user_id = ?
            ORDER BY lk.datetime DESC";

    $stmt = mysqli_prepare($con, $posts_sql);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

function get_subscribers ($con, $user_id)
{
    $posts_sql = "
            SELECT
            sc.subscription as sub_id,
            u.id,
            u.datetime,
            u.login,
            u.avatar,
            (SELECT count(*) FROM posts WHERE user_id = sc.subscription) AS posts_count,
            (SELECT count(*) FROM subscribers WHERE subscription = sc.subscription) AS sub_count
            FROM subscribers sc
            JOIN users u ON u.id = sc.subscription
            WHERE sc.author = ?";
    $stmt = mysqli_prepare($con, $posts_sql);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

function get_correspondence ($con, $member_id, $user_id)
{
    $sql = "SELECT ms.id as ms_id, ms.datetime, ms.content, us.id as user_id, us.login, us.avatar FROM messages ms
            JOIN users us ON sender = us.id
            WHERE (recipient = ? and sender = ?) OR (recipient = ? and sender = ?)
            ORDER BY ms.datetime DESC";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'iiii', $user_id, $member_id, $member_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

}

function get_members ($con, $user_id)
{
    $posts_sql = "
        SELECT sender as member_id, u.login, u.avatar
        FROM messages
        JOIN users u ON u.id = sender
        WHERE recipient = ?
        UNION
        SELECT recipient as member_id, u.login, u.avatar FROM messages
        JOIN users u ON u.id = recipient
        WHERE sender = ?";

    $stmt = mysqli_prepare($con, $posts_sql);
    mysqli_stmt_bind_param($stmt, 'ii', $user_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

function get_last_message ($con, $member_id, $user_id)
{
    $sql = "SELECT ms.id, ms.datetime, ms.content,
            (SELECT count(*) FROM messages
            WHERE ((recipient = ? and sender = ?)) AND is_new_message = 1) as new_msg_count
            FROM messages ms
            WHERE (recipient = ? and sender = ?) OR (recipient = ? and sender = ?)
            ORDER BY ms.datetime DESC LIMIT 1";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'iiiiii',  $member_id, $user_id, $user_id, $member_id, $member_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_array($result, MYSQLI_ASSOC) : [];
}

function get_count_my_massages ($con, $user_id)
{
    $sql = "SELECT count(*) as count FROM messages WHERE recipient = ? AND is_new_message = 1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_array($result, MYSQLI_ASSOC)['count'];
}

function set_is_new_message ($con, $user_id, $member_id) {
    $sql = "UPDATE messages SET is_new_message = 0
            WHERE (recipient = ? and sender = ?) AND is_new_message = 1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ii',  $user_id,  $member_id);
    mysqli_stmt_execute($stmt);
}

function send_message ($con, $content, $sender, $recipient)
{
    $sql = "INSERT INTO messages (content, sender, recipient) VALUES (?, ?, ?);";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'sii',  $content, $sender,  $recipient);
    return mysqli_stmt_execute($stmt);
}

function send_mail ($target_email, $body, $subject)
{

    $transport = (new Swift_SmtpTransport('phpdemo.ru', '25'))
        ->setUsername('keks@phpdemo.ru')
        ->setPassword('htmlacademy');

    $mailer = new Swift_Mailer($transport);

    $message = (new Swift_Message($subject))
        ->setFrom(['keks@phpdemo.ru' => 'readme: оповещение'])
        ->setTo($target_email)
        ->setBody($body);

    return $mailer->send($message);
}
