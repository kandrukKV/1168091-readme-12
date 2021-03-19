# добавляем список типов контента для поста
INSERT INTO content_type (type_name, class_name)
VALUES ('Фото', 'photo');
INSERT INTO content_type (type_name, class_name)
VALUES ('Видео', 'video');
INSERT INTO content_type (type_name, class_name)
VALUES ('Текст', 'text');
INSERT INTO content_type (type_name, class_name)
VALUES ('Цитата', 'quote');
INSERT INTO content_type (type_name, class_name)
VALUES ('Ссылка', 'link');

# добавление пользователей
INSERT INTO `users`
    (`email`, `login`, `pass`, `avatar`)
VALUES ('viktor@viktor.ru', 'Виктор', 'pass1', 'userpic-mark.jpg');

INSERT INTO `users`
    (`email`, `login`, `pass`, `avatar`)
VALUES ('larisa@larisa.ru', 'Лариса', 'pass1', 'userpic-larisa-small.jpg');

INSERT INTO `users`
    (`email`, `login`, `pass`, `avatar`)
VALUES ('vladik@vladik.ru', 'Владик', 'pass1', 'userpic.jpg');

# добавление существующий список постов
# добавление цитаты
INSERT INTO `posts`
    (`title`, `content`, `quote_author`, `user_id`, `content_type_id`)
VALUES ('Цитата',
        'Мы в жизни любим только раз, а после ищем лишь похожих.',
        'Неизвестный Автор',
        2,
        4);

# добавление текстового поста
INSERT INTO `posts`
    (`title`, `content`, `user_id`, `content_type_id`)
VALUES ('Озеро Байкал',
        'зеро Байкал – огромное древнее озеро в горах Сибири к северу от монгольской границы. Байкал
           считается самым глубоким озером в мире. Он окружен сетью пешеходных маршрутов, называемых
           Большой байкальской тропой. Деревня Листвянка, расположенная на западном берегу озера, –
           популярная отправная точка для летних экскурсий. Зимой здесь можно кататься на коньках и
           собачьих упряжках.',
        3,
        3);

# добавление фото поста
INSERT INTO `posts`
    (`title`, `content`, `user_id`, `content_type_id`)
VALUES ('Наконец, обработал фотки!',
        'rock-medium.jpg',
        1,
        1);

INSERT INTO `posts`
    (`title`, `content`, `user_id`, `content_type_id`)
VALUES ('Моя мечта!',
        'coast-medium.jpg',
        2,
        1);

# добавление ссылки
INSERT INTO `posts`
    (`title`, `content`, `user_id`, `content_type_id`)
VALUES ('Лучшие курсы',
        'www.htmlacademy.ru',
        3,
        5);

# добавление комментариев к разным постам
INSERT INTO `comments`
    (`content`, `user_id`, `post_id`)
VALUES ('Классное место! Был там много раз.',
        1,
        2);

INSERT INTO `comments`
    (`content`, `user_id`, `post_id`)
VALUES ('Неплохо получилось!',
        2,
        2);


# получить список постов с сортировкой по популярности и вместе с именами авторов и типом контента
SELECT p.id, p.datetime, p.title, p.content, u.login, c_t.type_name
FROM posts p
         JOIN users u ON p.user_id = u.id
         JOIN content_type c_t ON p.content_type_id = c_t.id
ORDER BY views_count DESC;

# получить список постов для конкретного пользователя
SELECT id, datetime, title, content
FROM posts
WHERE user_id = 1;

# получить список комментариев для одного поста, в комментариях должен быть логин пользователя;
SELECT c.content, c.datetime, u.login
FROM comments c
         JOIN users u ON c.user_id = u.id
WHERE c.post_id = 2;

# добавить лайк к посту
INSERT INTO `likes`
    (`user_id`, `post_id`)
VALUES (1, 2);

# подписаться на пользователя
INSERT INTO `subscribers`
    (`author`, `subscription`)
VALUES (1, 2);
