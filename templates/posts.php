<?php if (isset($posts) && count($posts) > 0): ?>

    <?php foreach ($posts as $post): ?>
        <article class="feed__post post post-<?= $post['class_name'] ?>">
            <header class="post__header post__author">
                <a class="post__author-link" href="/profile.php?id=<?= $post['user_id'] ?>" title="Автор">
                    <div class="post__avatar-wrapper">
                        <img class="post__author-avatar"
                             src="/uploads/<?= $post['avatar'] ? htmlspecialchars($post['avatar']) : 'unnamed.png' ?>"
                             alt="Аватар пользователя" width="60" height="60">
                    </div>
                    <div class="post__info">
                        <b class="post__author-name"><?= htmlspecialchars($post['login']) ?></b>
                        <span class="post__time"><?= get_how_much_time($post['datetime']) ?></span>
                    </div>
                </a>
            </header>

            <?php switch ($post['class_name']) { ?>
<?php case 'photo': ?>
                    <div class="post__main">
                        <h2>
                            <a href="/post.php?id=<?= htmlspecialchars($post['id']) ?>"><?= htmlspecialchars($post['title']) ?></a>
                        </h2>
                        <div class="post-photo__image-wrapper">
                            <img src="uploads/<?= htmlspecialchars($post['content']) ?>" alt="Фото от пользователя"
                                 width="760" height="396">
                        </div>
                    </div>

                    <?php break;
                case 'text': ?>
                    <div class="post__main">
                        <h2>
                            <a href="/post.php?id=<?= htmlspecialchars($post['id']) ?>"><?= htmlspecialchars($post['title']) ?></a>
                        </h2>
                        <p><?= htmlspecialchars($post['content']) ?></p>
                        <a class="post-text__more-link" href="/post.php?id=<?= htmlspecialchars($post['id']) ?>">Читать
                            далее</a>
                    </div>

                    <?php break;
                case 'video': ?>
                    <div class="post__main">
                        <h2>
                            <a href="/post.php?id=<?= htmlspecialchars($post['id']) ?>"><?= htmlspecialchars($post['title']) ?></a>
                        </h2>
                        <div class="post-video__block">
                            <div class="post-video__preview">
                                <?= embed_youtube_video(htmlspecialchars($post['content'])) ?>
                            </div>
                        </div>
                    </div>

                    <?php break;
                case 'quote': ?>
                    <div class="post__main">
                        <h2>
                            <a href="/post.php?id=<?= htmlspecialchars($post['id']) ?>"><?= htmlspecialchars($post['title']) ?></a>
                        </h2>
                        <blockquote>
                            <p><?= $post['content'] ?></p>
                            <cite><?= $post['quote_author'] ?></cite>
                        </blockquote>
                    </div>

                    <?php break;
                case 'link': ?>
                    <div class="post__main">
                        <h2>
                            <a href="/post.php?id=<?= htmlspecialchars($post['id']) ?>"><?= htmlspecialchars($post['title']) ?></a>
                        </h2>
                        <div class="post-link__wrapper">
                            <a class="post-link__external" href="/post.php?id=<?= $post['id'] ?>"
                               title="Перейти по ссылке">
                                <div class="post-link__info">
                                    <h3><?= htmlspecialchars($post['content']); ?></h3>
                                    <span><?= htmlspecialchars($post['link']); ?></span>
                                </div>
                                <svg class="post-link__arrow" width="11" height="16">
                                    <use xlink:href="#icon-arrow-right-ad"></use>
                                </svg>
                            </a>
                        </div>
                    </div>

                <?php } ?>

            <footer class="post__footer post__indicators">
                <div class="post__buttons">
                    <a class="post__indicator post__indicator--likes button"<?= !$post['is_like'] ? ' href="/like.php?id=' . $post['id'] . '"' : '' ?>
                       title="Лайк">
                        <svg class="post__indicator-icon" width="20" height="17">
                            <use xlink:href="#icon-heart"></use>
                        </svg>
                        <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                            <use xlink:href="#icon-heart-active"></use>
                        </svg>
                        <span><?= $post['likes_count'] ?></span>
                        <span class="visually-hidden">количество лайков</span>
                    </a>
                    <a class="post__indicator post__indicator--comments button" href="/post.php?id=<?= $post['id'] ?>"
                       title="Комментарии">
                        <svg class="post__indicator-icon" width="19" height="17">
                            <use xlink:href="#icon-comment"></use>
                        </svg>
                        <span><?= $post['comments_count'] ?></span>
                        <span class="visually-hidden">количество комментариев</span>
                    </a>
                    <a class="post__indicator post__indicator--repost button" href="/repost.php?id=<?= $post['id'] ?>"
                       title="Репост">
                        <svg class="post__indicator-icon" width="19" height="17">
                            <use xlink:href="#icon-repost"></use>
                        </svg>
                        <span><?= $post['num_reposts'] ?></span>
                        <span class="visually-hidden">количество репостов</span>
                    </a>
                </div>
            </footer>
        </article>

    <?php endforeach; ?>
<?php endif; ?>
