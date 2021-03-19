<main class="page__main page__main--publication">
    <div class="container">
        <h1 class="page__title page__title--publication"><?= htmlspecialchars($post_details['title']) ?></h1>
        <section class="post-details">
            <h2 class="visually-hidden">Публикация</h2>
            <div class="post-details__wrapper post-<?= $post_details['class_name'] ?>">
                <div class="post-details__main-block post post--details">

                    <?= get_post_template($post_details) ?>

                    <div class="post__indicators">
                        <div class="post__buttons">
                            <a class="post__indicator post__indicator--likes button"<?= !$post_details['is_like'] ? ' href="/like.php?id=' . $post_details['id'] . '"' : '' ?>
                               title="Лайк">
                                <svg class="post__indicator-icon" width="20" height="17">
                                    <use xlink:href="#icon-heart"></use>
                                </svg>
                                <svg class="post__indicator-icon post__indicator-icon--like-active" width="20"
                                     height="17">
                                    <use xlink:href="#icon-heart-active"></use>
                                </svg>
                                <span><?= $post_details['likes_count'] ?></span>
                                <span class="visually-hidden">количество лайков</span>
                            </a>
                            <a class="post__indicator post__indicator--comments button" title="Комментарии"
                               href="#last-comment">
                                <svg class="post__indicator-icon" width="19" height="17">
                                    <use xlink:href="#icon-comment"></use>
                                </svg>
                                <span><?= $post_details['comments_count'] ?></span>
                                <span class="visually-hidden">количество комментариев</span>
                            </a>
                            <a class="post__indicator post__indicator--repost button"
                               href="/repost.php?id=<?= $post_details['id'] ?>" title="Репост">
                                <svg class="post__indicator-icon" width="19" height="17">
                                    <use xlink:href="#icon-repost"></use>
                                </svg>
                                <span><?= $post_details['num_reposts'] ?></span>
                                <span class="visually-hidden">количество репостов</span>
                            </a>
                        </div>
                        <span
                            class="post__view"><?= $post_details['views_count'] . ' ' . get_noun_plural_form($post_details['views_count'],
                                'просмотр', 'просмотра', 'просмотров') ?> </span>
                    </div>

                    <ul class="post__tags">
                        <?php foreach ($tags as $tag): ?>
                            <li>
                                <a href="/search.php?search_request=%23<?= $tag['tag_name'] ?>">#<?= $tag['tag_name'] ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="comments">

                        <form class="comments__form form" action="/post.php?id=<?= $post_details['id'] ?>"
                              method="post">
                            <input type="hidden" name="post_id" value="<?= $post_details['id'] ?>"/>
                            <div class="comments__my-avatar">
                                <img class="comments__picture"
                                     src="uploads/<?= $_SESSION['avatar'] ? htmlspecialchars($_SESSION['avatar']) : 'unnamed.png' ?>"
                                     alt="Аватар пользователя">
                            </div>
                            <div
                                class="form__input-section<?= count($errors) > 0 ? ' form__input-section--error' : '' ?>">
                                <textarea class="comments__textarea form__textarea form__input"
                                          placeholder="Ваш комментарий" id="comment_text"
                                          name="content"><?= htmlspecialchars(get_post_val('content')) ?></textarea>
                                <label class="visually-hidden" for="comment_text">Ваш комментарий</label>
                                <button class="form__error-button button" type="button">!</button>
                                <?php if (count($errors) > 0): ?>
                                    <div class="form__error-text">
                                        <h3 class="form__error-title">Ошибка</h3>
                                        <p class="form__error-desc"><?= $errors['content'] ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <button class="comments__submit button button--green" type="submit">Отправить</button>
                        </form>

                        <?php if ($post_details['comments_count'] > 0): ?>

                            <div class="comments__list-wrapper">
                                <ul class="comments__list">
                                    <?php foreach ($post_details['comments'] as $index => $comment) : ?>
                                        <li class="comments__item user"<?= $index === count($post_details['comments']) - 1 ? ' id="last-comment"' : '' ?>>
                                            <div class="comments__avatar">
                                                <a class="user__avatar-link"
                                                   href="/profile.php?id=<?= $comment['user_id'] ?>">
                                                    <img class="comments__picture"
                                                         src="/uploads/<?= $comment['avatar'] ? htmlspecialchars($comment['avatar']) : 'unnamed.png' ?>"
                                                         alt="Аватар пользователя">
                                                </a>
                                            </div>
                                            <div class="comments__info">
                                                <div class="comments__name-wrapper">
                                                    <a class="comments__user-name"
                                                       href="/profile.php?id=<?= $comment['user_id'] ?>">
                                                        <span><?= htmlspecialchars($comment['login']) ?></span>
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

                        <?php endif; ?>
                    </div>

                </div>
                <div class="post-details__user user">
                    <div class="post-details__user-info user__info">
                        <div class="post-details__avatar user__avatar">
                            <a class="post-details__avatar-link user__avatar-link"
                               href="/profile.php?id=<?= $post_details['user_id'] ?>">
                                <img class="post-details__picture user__picture"
                                     src="uploads/<?= $post_details['avatar'] ? htmlspecialchars($post_details['avatar']) : 'unnamed.png' ?>"
                                     alt="Аватар пользователя">
                            </a>
                        </div>
                        <div class="post-details__name-wrapper user__name-wrapper">
                            <a class="post-details__name user__name"
                               href="/profile.php?id=<?= $post_details['user_id'] ?>">
                                <span><?= htmlspecialchars($post_details['login']) ?></span>
                            </a>
                            <time class="post-details__time user__time"
                                  datetime="<?= $post_details['user_datetime'] ?>"><?= get_how_much_time($post_details['user_datetime']) ?>
                                на сайте
                            </time>
                        </div>
                    </div>
                    <div class="post-details__rating user__rating">
                        <p class="post-details__rating-item user__rating-item user__rating-item--subscribers">
                            <span
                                class="post-details__rating-amount user__rating-amount"><?= $user_num_of_subscribers ?></span>
                            <span class="post-details__rating-text user__rating-text">подписчиков</span>
                        </p>
                        <p class="post-details__rating-item user__rating-item user__rating-item--publications">
                            <span
                                class="post-details__rating-amount user__rating-amount"><?= $user_num_of_posts ?></span>
                            <span class="post-details__rating-text user__rating-text">публикаций</span>
                        </p>
                    </div>

                    <?php if ($_SESSION['user_id'] !== $post_details['user_id']): ?>
                        <div class="post-details__user-buttons user__buttons">

                            <a class="profile__user-button user__button user__button--subscription button button--main"
                               href="/subscribe.php?id=<?= $post_details['user_id'] ?>"><?= $is_subscribe ? 'Отписаться' : 'Подписаться' ?></a>
                            <?php if ($is_subscribe): ?>
                                <a class="profile__user-button user__button user__button--writing button button--green"
                                   href="#">Сообщение</a>
                            <?php endif; ?>

                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </section>
    </div>
</main>
