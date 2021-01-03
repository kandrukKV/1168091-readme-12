# получаем список типов контента
SELECT `type_name` FROM `content_type`;

# добавление пользователя
INSERT INTO `users`
    (`email`, `login`, `pass`, `avatar`) VALUES
    ('viktor@viktor.ru', 'Виктор', 'pass1', 'userpic-mark.jpg');

# добавление цитаты
INSERT INTO `posts`
(`title`, `content`, `quote_author`, `user_id`, `content_type_id`) VALUES
('Цитата',
 'Мы в жизни любим только раз, а после ищем лишь похожих.',
 'Неизвестный Автор',
 2,
 2);

# добавление текстового поста
INSERT INTO `posts`
(`title`, `content`, `user_id`, `content_type_id`) VALUES
('Озеро Байкал',
 'зеро Байкал – огромное древнее озеро в горах Сибири к северу от монгольской границы. Байкал
    считается самым глубоким озером в мире. Он окружен сетью пешеходных маршрутов, называемых
    Большой байкальской тропой. Деревня Листвянка, расположенная на западном берегу озера, –
    популярная отправная точка для летних экскурсий. Зимой здесь можно кататься на коньках и
    собачьих упряжках.',
 3,
 1);

# добавление фото поста
INSERT INTO `posts`
(`title`, `content`, `user_id`, `content_type_id`) VALUES
('Наконец, обработал фотки!',
 'rock-medium.jpg',
 1,
 3);

# добавляем комментарий к посту
INSERT INTO `comments`
(`content`, `user_id`, `post_id`) VALUES
('Классное место! Был там много раз.',
 1,
 2);

# получить список постов с сортировкой по популярности и вместе с именами авторов и типом контента
SELECT p.id, p.datetime, p.title, p.content, u.login, c_t.type_name FROM posts p
JOIN users u ON p.user_id = u.id
JOIN content_type c_t ON p.content_type_id = c_t.id;

# получить список постов для конкретного пользователя
SELECT id, datetime, title, content FROM posts WHERE user_id = 1;

# получить список комментариев для одного поста, в комментариях должен быть логин пользователя;
SELECT c.content, c.datetime, u.login FROM comments c
JOIN users u ON c.user_id = u.id
JOIN posts p ON c.post_id = p.id
WHERE c.post_id = 2;

# добавить лайк к посту
UPDATE posts SET views_count = views_count + 1
WHERE id = 1;

# подписаться на пользователя
INSERT INTO `subscribers`
(`author`, `subscription`) VALUES (1, 2);
