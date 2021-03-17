<?php

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

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = [])
{
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            } else {
                if (is_string($value)) {
                    $type = 's';
                } else {
                    if (is_double($value)) {
                        $type = 'd';
                    }
                }
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
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

function add_post ($con, $title, $content, $author, $content_type, $user_id)
{
    $sql = "SELECT `id` FROM `content_type` WHERE `class_name` = '$content_type'";

    $res = mysqli_query($con, $sql);
    $content_type_id = mysqli_fetch_assoc($res)['id'];

    if (!$content_type_id) {
        return false;
    }

    $sql = "INSERT INTO `posts` (`title`, `content`, `quote_author`, `user_id`, `content_type_id`) VALUES (?, ?, ?, ?, ?)";

    $stmt = db_get_prepare_stmt($con, $sql, [$title, $content, $author, $user_id, $content_type_id]);

    return mysqli_stmt_execute($stmt);
}

function add_user ($con, $email, $login, $pass, $avatar)
{
    $passwordHash = password_hash($pass, PASSWORD_DEFAULT);
    $sql = "INSERT INTO `users` (`email`, `pass`, `login`, `avatar`) VALUES (?, ?, ?, ?)";
    $stmt = db_get_prepare_stmt($con, $sql, [$email, $passwordHash, $login, $avatar]);

    return mysqli_stmt_execute($stmt);
}

function add_view ($con, $post_id)
{
    $sql = "UPDATE `posts` SET `views_count` = `views_count` + 1 WHERE id = ?";
    $stmt = db_get_prepare_stmt($con, $sql, [$post_id]);

    return mysqli_stmt_execute($stmt);
}

function add_comment ($con, $content, $user_id, $post_id)
{
    $sql = "INSERT INTO `comments` (`content`, `user_id`, `post_id`) VALUES (?, ?, ?)";
    $stmt = db_get_prepare_stmt($con, $sql, [$content,$user_id, $post_id]);
    return mysqli_stmt_execute($stmt);
}

function add_like ($con, $user_id, $post_id) {
    $sql = "INSERT INTO likes (user_id, post_id) VALUES (?, ?)";
    $stmt = db_get_prepare_stmt($con, $sql, [$user_id, $post_id]);
    return mysqli_stmt_execute($stmt);
}

function checkEmail ($con, $email)
{
    $sql = "SELECT `email` FROM `users` WHERE `email` = ?";
    $stmt = db_get_prepare_stmt($con, $sql, [$email]);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

function content_type_id_is_correct ($con, $content_type_id)
{
    $sql = "SELECT * FROM content_type WHERE id = ?";

    $stmt = db_get_prepare_stmt($con, $sql, [$content_type_id]);

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_array($result, MYSQLI_ASSOC);
}

function is_like ($con, $post_id, $user_id) {
    $sql = "SELECT * FROM likes WHERE post_id = ? AND user_id = ?";
    $stmt = db_get_prepare_stmt($con, $sql, [$post_id, $user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($result);
}

function is_there_post_with_id ($con, $post_id) {
    $sql = "SELECT * FROM posts WHERE id = ?";

    $stmt = db_get_prepare_stmt($con, $sql, [$post_id]);

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_row($result);
}

function is_subscribe ($con, $user_one, $user_two)
{
    $sql = "SELECT * FROM subscribers WHERE author = ? AND subscription = ?";
    $stmt = db_get_prepare_stmt($con, $sql, [$user_one, $user_two]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_row($result);
}

function get_user_info ($con, $user_id)
{
    $sql = "SELECT u.id, u.datetime, u.login, u.email, u.avatar
        FROM users u
        WHERE id = ?";

    $stmt = db_get_prepare_stmt($con, $sql, [$user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_array($result, MYSQLI_ASSOC);
}

function get_user_by_email ($con, $email)
{
    $sql = "SELECT `id`, `pass`, `login`, `avatar` FROM `users` WHERE `email` = ?";
    $stmt = db_get_prepare_stmt($con, $sql, [$email]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($res);
}

function get_posts_of_user ($con, $user_id)
{
    $sql = "
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

    $stmt = db_get_prepare_stmt($con, $sql, [$user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

function get_post_by_id ($con, $post_id)
{
    $sql = "
    SELECT
           p.id,
           p.datetime,
           p.title,
           p.content,
           p.link,
           p.views_count,
           p.user_id,
           p.quote_author,
           p.views_count,
           u.login,
           u.avatar,
           u.datetime as user_datetime,
           c_t.class_name,
           (SELECT count(*) FROM likes WHERE post_id = p.id) AS likes_count,
           (SELECT count(*) FROM comments WHERE post_id = p.id) AS comments_count
    FROM posts p
    JOIN users u ON p.user_id = u.id
    JOIN content_type c_t ON p.content_type_id = c_t.id
    WHERE p.id = ?";

    $stmt = $stmt = db_get_prepare_stmt($con, $sql, [$post_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_array($result, MYSQLI_ASSOC);
}

function get_tags ($con, $post_id)
{
    $sql = "SELECT ht.tag_name FROM posts_hashtags ph JOIN hash_tags ht ON ht.id = ph.hashtag_id WHERE post_id = ?";
    $stmt = db_get_prepare_stmt($con, $sql, [$post_id]);
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

    $stmt = db_get_prepare_stmt($con, $sql, [$post_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $comments = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $comments ? $comments : [];
}

function get_likes_of_user ($con, $user_id)
{
    $sql = "SELECT
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

    $stmt = db_get_prepare_stmt($con, $sql, [$user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

function get_subscriptions ($con, $user_id)
{
    $sql = "
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
    $stmt = db_get_prepare_stmt($con, $sql, [$user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

function get_subscribers ($con, $user_id)
{
    $sql = 'SELECT u.email, u.login FROM subscribers sc JOIN users u ON sc.author = u.id WHERE sc.subscription = ?';
    $stmt = db_get_prepare_stmt($con, $sql, [$user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

function get_user_feed ($con, $user_id, $current_content_type_id)
{
    $sql = "
    SELECT
           p.id,
           p.datetime,
           p.title,
           p.content,
           p.link,
           p.quote_author,
           p.user_id,
           u.login,
           u.avatar,
           c_t.type_name,
           c_t.class_name,
           (SELECT count(*) FROM likes WHERE post_id = p.id) AS likes_count,
           (SELECT count(*) FROM comments WHERE post_id = p.id) AS comments_count
    FROM posts p
    JOIN users u ON p.user_id = u.id
    JOIN content_type c_t ON p.content_type_id = c_t.id
    JOIN subscribers sub ON  sub.subscription = p.user_id AND sub.author = ?";


    if ($current_content_type_id !== 'all') {

        if (!content_type_id_is_correct($con, $current_content_type_id)) {
            echo "Страница не найдена";
            http_response_code(404);
            exit();
        }

        $sql .= " WHERE content_type_id = ? ORDER BY datetime DESC";
        $stmt = db_get_prepare_stmt($con, $sql, [$user_id, $current_content_type_id]);
    } else {
        $sql .= " ORDER BY datetime DESC";
        $stmt = db_get_prepare_stmt($con, $sql, [$user_id]);
    }

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

function get_all_posts_count ($con, $content_type_id)
{
    if ($content_type_id) {

        $sql = "SELECT * FROM posts  WHERE content_type_id = ?";
        $stmt = db_get_prepare_stmt($con, $sql, [$content_type_id]);
        mysqli_stmt_execute($stmt);
        $all_posts_result = mysqli_stmt_get_result($stmt);

    } else {

        $sql = "SELECT * FROM posts";
        $all_posts_result = mysqli_query($con, $sql);

    }

    $all_posts = $all_posts_result ? mysqli_fetch_all($all_posts_result, MYSQLI_ASSOC) : [];
    return count($all_posts);
}

function get_popular_posts ($con, $content_type_id, $order_sort, $order_line, $limit, $offset)
{
    $sql = "
    SELECT
           p.id,
           p.datetime,
           p.title,
           p.content,
           p.link,
           p.quote_author,
           p.user_id,
           u.login,
           u.avatar,
           c_t.type_name,
           c_t.class_name,
           (SELECT count(*) FROM likes WHERE post_id = p.id) AS likes_count,
           (SELECT count(*) FROM comments WHERE post_id = p.id) AS comments_count
    FROM posts p
    JOIN users u ON p.user_id = u.id
    JOIN content_type c_t ON p.content_type_id = c_t.id";

    if ($content_type_id) {
        $sql .= " WHERE content_type_id = ? ORDER BY ". $order_sort . " " . $order_line . " LIMIT ? OFFSET ?";
        $stmt = db_get_prepare_stmt($con, $sql, [$content_type_id, $limit, $offset]);
    } else {
        $sql .= " ORDER BY ". $order_sort . " " . $order_line . " LIMIT ? OFFSET ?";
        $stmt = db_get_prepare_stmt($con, $sql, [$limit, $offset]);
    }

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

    $stmt = db_get_prepare_stmt($con, $sql, [$user_id, $member_id, $member_id, $user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

}

function get_members ($con, $user_id)
{
    $sql = "
        SELECT sender as member_id, u.login, u.avatar
        FROM messages
        JOIN users u ON u.id = sender
        WHERE recipient = ?
        UNION
        SELECT recipient as member_id, u.login, u.avatar FROM messages
        JOIN users u ON u.id = recipient
        WHERE sender = ?";

    $stmt = db_get_prepare_stmt($con, $sql, [$user_id, $user_id]);
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

    $stmt = db_get_prepare_stmt($con, $sql, [$member_id, $user_id, $user_id, $member_id, $member_id, $user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result ? mysqli_fetch_array($result, MYSQLI_ASSOC) : [];
}

function get_count_my_massages ($con, $user_id)
{
    $sql = "SELECT count(*) as count FROM messages WHERE recipient = ? AND is_new_message = 1";
    $stmt = db_get_prepare_stmt($con, $sql, [$user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_array($result, MYSQLI_ASSOC)['count'];
}

function get_content_types ($con)
{
    $sql = "SELECT `id`, `type_name`, `class_name` FROM `content_type`";

    $result = mysqli_query($con, $sql);

    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

function get_num_posts ($con, $user_id)
{
    $sql = "SELECT * FROM `posts` WHERE `user_id` = ?";

    $stmt = db_get_prepare_stmt($con, $sql, [$user_id]);

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($result);
}

function get_num_subscribers ($con, $user_id)
{
    $sql = "SELECT * FROM `subscribers` WHERE `subscription` = ?";
    $stmt = db_get_prepare_stmt($con, $sql, [$user_id]);

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($result);
}

function get_num_reposts ($con, $post_id) {
    $sql = "SELECT * FROM posts WHERE original_post_id = ?";
    $stmt = db_get_prepare_stmt($con, $sql, [$post_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($result);
}

function send_message ($con, $content, $sender, $recipient)
{
    $sql = "INSERT INTO messages (content, sender, recipient) VALUES (?, ?, ?);";
    $stmt = db_get_prepare_stmt($con, $sql, [$content, $sender,  $recipient]);
    return mysqli_stmt_execute($stmt);
}

function set_is_new_message ($con, $user_id, $member_id) {
    $sql = "UPDATE messages SET is_new_message = 0
            WHERE (recipient = ? and sender = ?) AND is_new_message = 1";
    $stmt = db_get_prepare_stmt($con, $sql, [$user_id,  $member_id]);
    mysqli_stmt_execute($stmt);
}

function set_subscriber ($con, $author, $subscriber, $isSubscribe)
{
    if ($isSubscribe) {
        $sql = "DELETE FROM subscribers WHERE author=? AND subscription=?";
    } else {
        $sql = "INSERT INTO subscribers (author, subscription) VALUES (?, ?)";
    }

    $stmt = db_get_prepare_stmt($con, $sql, [$author, $subscriber]);

    return mysqli_stmt_execute($stmt);
}

function get_found_posts ($con, $search_request)
{
    $sql = "
        SELECT p.id,
               p.datetime,
               p.title,
               p.content,
               p.link,
               p.quote_author,
               p.user_id,
               u.login,
               u.avatar,
               c_t.type_name,
               c_t.class_name,
               (SELECT count(*) FROM likes WHERE post_id = p.id) AS likes_count,
               (SELECT count(*) FROM comments WHERE post_id = p.id) AS comments_count
        FROM posts p
        JOIN users u ON p.user_id = u.id
        JOIN content_type c_t ON p.content_type_id = c_t.id
        WHERE MATCH(title, content) AGAINST(?)";

    if (count(explode(" ", $search_request)) === 1 && preg_match("/^[#]/", $search_request)) {

        $search_request = substr($search_request, 1);

        $sql = "
        SELECT
            p_ht.post_id,
            p_ht.hashtag_id,
            p.id,
            p.datetime,
            p.title,
            p.content,
            p.link,
            p.quote_author,
            p.user_id,
            u.login,
            u.avatar,
            c_t.type_name,
            c_t.class_name,
            h_t.id,
            h_t.tag_name,
            (SELECT count(*) FROM likes WHERE post_id = p.id) AS likes_count,
            (SELECT count(*) FROM comments WHERE post_id = p.id) AS comments_count
        FROM posts_hashtags p_ht
        JOIN posts p ON p_ht.post_id = p.id
        JOIN users u ON p.user_id = u.id
        JOIN content_type c_t ON p.content_type_id = c_t.id
        JOIN hash_tags h_t ON p_ht.hashtag_id = h_t.id
        WHERE h_t.tag_name = ?";
    }

    $stmt = db_get_prepare_stmt($con, $sql, [$search_request]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function get_all_fields_from_posts_by_id ($con, $post_id)
{
    $sql = "SELECT * FROM posts WHERE id = ?";

    $stmt = db_get_prepare_stmt($con, $sql, [$post_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result);
}

function make_repost ($con, $user_id, $post)
{
    $title = $post['title'];
    $content = $post['content'];
    $quote_author = $post['quote_author'];
    $picture = $post['picture'];
    $video = $post['video'];
    $link = $post['link'];
    $views_count = $post['views_count'];
    $content_type_id = $post['content_type_id'];
    $original_post_id = $post['id'];

    $sql = "INSERT INTO posts
            SET
            title = '$title',
            content = '$content',
            quote_author = '$quote_author',
            picture = '$picture',
            video = '$video',
            link = '$link',
            views_count = $views_count,
            user_id = '$user_id',
            content_type_id = '$content_type_id',
            is_repost = 1,
            original_post_id = ?";

    $stmt = db_get_prepare_stmt($con, $sql, [$original_post_id]);

    return mysqli_stmt_execute($stmt);
}
