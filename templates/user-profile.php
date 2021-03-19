<main class="page__main page__main--profile">
    <h1 class="visually-hidden">Профиль</h1>
    <div class="profile profile--default">
        <div class="profile__user-wrapper">
            <div class="profile__user user container">
                <div class="profile__user-info user__info">
                    <div class="profile__avatar user__avatar">
                        <img class="profile__picture user__picture"
                             src="/uploads/<?= $user['avatar'] ? htmlspecialchars($user['avatar']) : 'unnamed.png' ?>"
                             alt="Аватар пользователя">
                    </div>
                    <div class="profile__name-wrapper user__name-wrapper">
                        <span
                            class="profile__name user__name"><?= isset($user['login']) ? htmlspecialchars($user['login']) : '' ?></span>
                        <time class="profile__user-time user__time"
                              datetime="<?= $user['datetime'] ?>"><?= get_how_much_time($user['datetime']) ?> на сайте
                        </time>
                    </div>
                </div>
                <div class="profile__rating user__rating">
                    <p class="profile__rating-item user__rating-item user__rating-item--publications">
                        <span class="user__rating-amount"><?= $user_num_of_posts ?? '0' ?></span>
                        <span
                            class="profile__rating-text user__rating-text"><?= get_noun_plural_form($user_num_of_posts ?? 0,
                                'публикация', 'публикации', 'публикаций') ?></span>
                    </p>
                    <p class="profile__rating-item user__rating-item user__rating-item--subscribers">
                        <span class="user__rating-amount"><?= $user_num_of_subscribers ?? '0' ?></span>
                        <span
                            class="profile__rating-text user__rating-text"><?= get_noun_plural_form($user_num_of_subscribers ?? 0,
                                'подписчик', 'подписчика', 'подписчиков') ?></span>
                    </p>
                </div>

                <?php if ($_SESSION['user_id'] !== $user['id']): ?>
                    <div class="profile__user-buttons user__buttons">

                        <a class="profile__user-button user__button user__button--subscription button button--main"
                           href="/subscribe.php?id=<?= $user['id'] ?>"><?= $is_subscribe ? 'Отписаться' : 'Подписаться' ?></a>
                        <?php if ($is_subscribe): ?>
                            <a class="profile__user-button user__button user__button--writing button button--green"
                               href="/messages.php?list_id=<?= $user['id'] ?>">Сообщение</a>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="profile__tabs-wrapper tabs">
            <div class="container">
                <div class="profile__tabs filters">
                    <b class="profile__tabs-caption filters__caption">Показать:</b>
                    <ul class="profile__tabs-list filters__list tabs__list">
                        <li class="profile__tabs-item filters__item">
                            <a class="profile__tabs-link filters__button tabs__item button
                            <?= $active_tab === 'posts' ? ' filters__button--active tabs__item--active' : '' ?>"
                                <?= $active_tab === 'posts' ? '' : ' href="profile.php?id=' . $user['id'] . '&tab=posts"' ?>
                            >Посты</a>
                        </li>
                        <li class="profile__tabs-item filters__item">
                            <a class="profile__tabs-link filters__button tabs__item button
                            <?= $active_tab === 'likes' ? ' filters__button--active tabs__item--active' : '' ?>"
                                <?= $active_tab === 'likes' ? '' : ' href="profile.php?id=' . $user['id'] . '&tab=likes"' ?>
                            >Лайки</a>
                        </li>
                        <li class="profile__tabs-item filters__item">
                            <a class="profile__tabs-link filters__button tabs__item button
                            <?= $active_tab === 'subscribers' ? ' filters__button--active tabs__item--active' : '' ?>"
                                <?= $active_tab === 'subscribers' ? '' : ' href="profile.php?id=' . $user['id'] . '&tab=subscribers"' ?>
                            >Подписки</a>
                        </li>
                    </ul>
                </div>
                <div class="profile__tab-content">

                    <?php if ($active_tab === 'posts') : ?>
                        <section
                            class="profile__posts tabs__content<?= $active_tab === 'posts' ? ' tabs__content--active' : '' ?>">
                            <h2 class="visually-hidden">Публикации</h2>
                            <?php foreach ($posts as $post) : ?>
                                <article class="profile__post post post-<?= $post['class_name'] ?>">
                                    <header class="post__header">

                                        <?php if ($post['is_repost']): ?>
                                            <div class="post__author">
                                                <a class="post__author-link"
                                                   href="/profile.php?id=<?= $post['author_id'] ?>" title="Автор">
                                                    <div class="post__avatar-wrapper post__avatar-wrapper--repost">
                                                        <img class="post__author-avatar"
                                                             src="/uploads/<?= $post['author_avatar'] ? htmlspecialchars($post['author_avatar']) : 'unnamed.png' ?>"
                                                             alt="Аватар пользователя">
                                                    </div>
                                                    <div class="post__info">
                                                        <b class="post__author-name">Репост: <?= $post['author_login'] ?></b>
                                                        <time class="post__time"
                                                              datetime="<?= $post['real_time'] ?>"><?= get_how_much_time($post['real_time']) ?>
                                                            назад
                                                        </time>
                                                    </div>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        <h2 <?= $post['class_name'] === 'text' ? 'style="padding: 29px 40px 26px;"' : '' ?>>
                                            <a href="/post.php?id=<?= $post['id'] ?>"><?= $post['title'] ?></a></h2>
                                    </header>

                                    <?= get_post_template($post) ?>

                                    <footer class="post__footer">
                                        <div class="post__indicators">
                                            <div class="post__buttons">
                                                <a class="post__indicator post__indicator--likes button"<?= !$post['is_like'] ? ' href="/like.php?id=' . $post['id'] . '"' : '' ?>
                                                   title="Лайк">
                                                    <svg class="post__indicator-icon" width="20" height="17">
                                                        <use xlink:href="#icon-heart"></use>
                                                    </svg>
                                                    <svg class="post__indicator-icon post__indicator-icon--like-active"
                                                         width="20" height="17">
                                                        <use xlink:href="#icon-heart-active"></use>
                                                    </svg>
                                                    <span><?= $post['likes_count'] ?></span>
                                                    <span class="visually-hidden">количество лайков</span>
                                                </a>
                                                <a class="post__indicator post__indicator--repost button"
                                                   href="/repost.php?id=<?= $post['id'] ?>" title="Репост">
                                                    <svg class="post__indicator-icon" width="19" height="17">
                                                        <use xlink:href="#icon-repost"></use>
                                                    </svg>
                                                    <span><?= $post['num_reposts'] ?></span>
                                                    <span class="visually-hidden">количество репостов</span>
                                                </a>
                                            </div>
                                            <time class="post__time"
                                                  datetime="<?= $post['datetime'] ?>"><?= get_how_much_time($post['datetime']) ?>
                                                назад
                                            </time>
                                        </div>

                                        <ul class="post__tags">
                                            <?php foreach ($post['tags'] as $tag): ?>
                                                <li>
                                                    <a href="/search.php?search_request=%23<?= $tag['tag_name'] ?>">#<?= $tag['tag_name'] ?></a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>

                                        <?php if (isset($_GET['show']) && $post['id'] == $_GET['show']): ?>

                                            <?php if ($post['comments_count'] > 0): ?>

                                                <div class="comments">
                                                    <div class="comments__list-wrapper">
                                                        <ul class="comments__list">
                                                            <?php foreach ($post['comments'] as $comment): ?>
                                                                <li class="comments__item user">
                                                                    <div class="comments__avatar">
                                                                        <a class="user__avatar-link"
                                                                           href="/profile.php?id=<?= $comment['user_id'] ?>">
                                                                            <img class="comments__picture"
                                                                                 src="uploads/<?= $comment['avatar'] ? htmlspecialchars($comment['avatar']) : 'unnamed.png' ?>"
                                                                                 alt="Аватар пользователя">
                                                                        </a>
                                                                    </div>
                                                                    <div class="comments__info">
                                                                        <div class="comments__name-wrapper">
                                                                            <a class="comments__user-name"
                                                                               href=/profile.php?id=<?= $comment['user_id'] ?>">
                                                                                <span>Лариса Роговая</span>
                                                                            </a>
                                                                            <time class="comments__time"
                                                                                  datetime="<?= $comment['datetime'] ?>"><?= get_how_much_time($comment['datetime']) ?>
                                                                                назад
                                                                            </time>
                                                                        </div>
                                                                        <p class="comments__text">
                                                                            <?= htmlspecialchars($comment['content']) ?>
                                                                        </p>
                                                                    </div>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                </div>

                                            <?php endif; ?>

                                        <?php elseif ($post['comments_count'] > 0): ?>

                                            <div class="comments">
                                                <a class="comments__button button"
                                                   href="/profile.php?id=<?= $post['user_id'] ?>&show=<?= $post['id'] ?>">Показать
                                                    комментарии</a>
                                            </div>

                                        <?php endif; ?>

                                    </footer>
                                </article>
                            <?php endforeach; ?>
                        </section>
                    <?php endif; ?>

                    <?php if ($active_tab === 'likes') : ?>
                        <section
                            class="profile__likes tabs__content<?= $active_tab === 'likes' ? ' tabs__content--active' : '' ?>">
                            <h2 class="visually-hidden">Лайки</h2>
                            <ul class="profile__likes-list">
                                <?php foreach ($posts as $post): ?>
                                    <li class="post-mini post-mini--photo post user">
                                        <div class="post-mini__user-info user__info">
                                            <div class="post-mini__avatar user__avatar">
                                                <a class="user__avatar-link"
                                                   href="/profile.php?id=<?= $post['user_id'] ?>">
                                                    <img class="post-mini__picture user__picture"
                                                         src="/uploads/<?= $post['avatar'] ? htmlspecialchars($post['avatar']) : 'unnamed.png' ?>"
                                                         alt="Аватар пользователя">
                                                </a>
                                            </div>
                                            <div class="post-mini__name-wrapper user__name-wrapper">
                                                <a class="post-mini__name user__name"
                                                   href="/profile.php?id=<?= $post['user_id'] ?>">
                                                    <span><?= htmlspecialchars($post['login']) ?></span>
                                                </a>
                                                <div class="post-mini__action">
                                                    <span class="post-mini__activity user__additional">Лайкнул вашу публикацию</span>
                                                    <time class="post-mini__time user__additional"
                                                          datetime="<?= $post['datetime'] ?>"><?= get_how_much_time($post['datetime']) ?>
                                                        назад
                                                    </time>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="post-mini__preview">
                                            <a class="post-mini__link" href="/post.php?id=<?= $post['id'] ?>"
                                               title="Перейти на публикацию">

                                                <?php if ($post['class_name'] === 'photo'): ?>
                                                    <div class="post-mini__image-wrapper">
                                                        <img class="post-mini__image"
                                                             src="uploads/<?= htmlspecialchars($post['content']) ?>"
                                                             width="109" height="109" alt="Превью публикации">
                                                    </div>
                                                    <span class="visually-hidden">Фото</span>
                                                <?php endif; ?>

                                                <?php if ($post['class_name'] === 'text'): ?>
                                                    <span class="visually-hidden">Текст</span>
                                                    <svg class="post-mini__preview-icon" width="20" height="21">
                                                        <use xlink:href="#icon-filter-text"></use>
                                                    </svg>
                                                <?php endif; ?>

                                                <?php if ($post['class_name'] === 'video'): ?>
                                                    <div class="post-mini__image-wrapper">
                                                        <?= embed_youtube_cover(htmlspecialchars($post['content'])) ?>
                                                        <span class="post-mini__play-big">
                                                        <svg class="post-mini__play-big-icon" width="12" height="13">
                                                          <use xlink:href="#icon-video-play-big"></use>
                                                        </svg>
                                                    </span>
                                                    </div>
                                                    <span class="visually-hidden">Видео</span>
                                                <?php endif; ?>

                                                <?php if ($post['class_name'] === 'quote'): ?>
                                                    <span class="visually-hidden">Цитата</span>
                                                    <svg class="post-mini__preview-icon" width="21" height="20">
                                                        <use xlink:href="#icon-filter-quote"></use>
                                                    </svg>
                                                <?php endif; ?>

                                                <?php if ($post['class_name'] === 'link'): ?>
                                                    <span class="visually-hidden">Ссылка</span>
                                                    <svg class="post-mini__preview-icon" width="21" height="18">
                                                        <use xlink:href="#icon-filter-link"></use>
                                                    </svg>
                                                <?php endif; ?>


                                            </a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </section>
                    <?php endif; ?>

                    <?php if ($active_tab === 'subscribers') : ?>
                        <section
                            class="profile__subscriptions tabs__content<?= $active_tab === 'subscribers' ? ' tabs__content--active' : '' ?>">
                            <h2 class="visually-hidden">Подписки</h2>
                            <ul class="profile__subscriptions-list">
                                <?php foreach ($posts as $post) : ?>
                                    <li class="post-mini post-mini--photo post user">
                                        <div class="post-mini__user-info user__info">
                                            <div class="post-mini__avatar user__avatar">
                                                <a class="user__avatar-link"
                                                   href="/profile.php?id=<?= $post['sub_id'] ?>">
                                                    <img class="post-mini__picture user__picture"
                                                         src="/uploads/<?= $post['avatar'] ? htmlspecialchars($post['avatar']) : 'unnamed.png' ?>"
                                                         alt="Аватар пользователя">
                                                </a>
                                            </div>
                                            <div class="post-mini__name-wrapper user__name-wrapper">
                                                <a class="post-mini__name user__name"
                                                   href="/profile.php?id=<?= $post['sub_id'] ?>">
                                                    <span><?= $post['login'] ?></span>
                                                </a>
                                                <time class="post-mini__time user__additional"
                                                      datetime="<?= $post['datetime'] ?>"><?= get_how_much_time($post['datetime']) ?>
                                                    на сайте
                                                </time>
                                            </div>
                                        </div>
                                        <div class="post-mini__rating user__rating">
                                            <p class="post-mini__rating-item user__rating-item user__rating-item--publications">
                                                <span
                                                    class="post-mini__rating-amount user__rating-amount"><?= $post['posts_count'] ?></span>
                                                <span
                                                    class="post-mini__rating-text user__rating-text"><?= get_noun_plural_form($post['posts_count'],
                                                        'публикация', 'публикации', 'публикаций') ?></span>
                                            </p>
                                            <p class="post-mini__rating-item user__rating-item user__rating-item--subscribers">
                                                <span
                                                    class="post-mini__rating-amount user__rating-amount"><?= $post['sub_count'] ?></span>
                                                <span
                                                    class="post-mini__rating-text user__rating-text"><?= get_noun_plural_form($post['sub_count'],
                                                        'подписчик', 'подписчика', 'подписчиков') ?></span>
                                            </p>
                                        </div>
                                        <div class="post-mini__user-buttons user__buttons">
                                            <a class="post-mini__user-button user__button user__button--subscription button <?= $post['is_subscribe'] ? 'button--quartz' : 'button--main' ?> button--main"
                                               type="button"
                                               href="/subscribe.php?id=<?= $post['sub_id'] ?>"><?= $post['is_subscribe'] ? 'Отписаться' : 'Подписаться' ?></a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </section>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>
