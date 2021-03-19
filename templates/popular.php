<?php

$p_sort = isset($sort) ? '&sort=' . $sort : '';
$p_line = isset($line) ? '&line=' . $line : '';
$p_content_type = isset($content_type_id) ? 'content_type=' . $content_type_id : '';
$p_page = isset($page) ? '&page=' . $page : '';

?>


<section class="page__main page__main--popular">
    <div class="container">
        <h1 class="page__title page__title--popular">Популярное</h1>
    </div>
    <div class="popular container">
        <div class="popular__filters-wrapper">
            <div class="popular__sorting sorting">
                <b class="popular__sorting-caption sorting__caption">Сортировка:</b>
                <ul class="popular__sorting-list sorting__list">
                    <li class="sorting__item sorting__item--popular">
                        <a class="sorting__link<?= $sort === 'popular' ? ' sorting__link--active' : '' ?>  <?= $line === 'up' ? 'sorting__link--reverse' : '' ?>"
                           href="/popular.php?<?= $p_content_type . $p_page . '&sort=popular' ?>&line=<?= $line === 'down' && $sort === 'popular' ? 'up' : 'down' ?>">
                            <span>Популярность</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="sorting__item">
                        <a class="sorting__link<?= $sort === 'likes' ? ' sorting__link--active' : '' ?> <?= $line === 'up' ? 'sorting__link--reverse' : '' ?>"
                           href="/popular.php?<?= $p_content_type . $p_page . '&sort=likes' ?>&line=<?= $line === 'down' && $sort === 'likes' ? 'up' : 'down' ?>">
                            <span>Лайки</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="sorting__item">
                        <a class="sorting__link<?= $sort === 'date' ? ' sorting__link--active' : '' ?> <?= $line === 'up' ? 'sorting__link--reverse' : '' ?>"
                           href="/popular.php?<?= $p_content_type . $p_page . '&sort=date' ?>&line=<?= $line === 'down' && $sort === 'date' ? 'up' : 'down' ?>">
                            <span>Дата</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="popular__filters filters">
                <b class="popular__filters-caption filters__caption">Тип контента:</b>
                <ul class="popular__filters-list filters__list">

                    <li class="popular__filters-item popular__filters-item--all filters__item filters__item--all">
                        <a class="filters__button filters__button--ellipse filters__button--all <?= empty($content_type_id) ? 'filters__button--active' : '' ?>"
                           href="/popular.php">
                            <span>Все</span>
                        </a>
                    </li>

                    <?php foreach ($content_types as $content_type): ?>
                        <li class="popular__filters-item filters__item">
                            <a class="filters__button filters__button--<?= $content_type['class_name'] ?> button <?= $content_type_id === $content_type['id'] ? 'filters__button--active' : '' ?>"
                               href="/popular.php?content_type=<?= $content_type['id'] . $p_sort ?>">
                                <span class="visually-hidden"><?= $content_type['type_name'] ?></span>
                                <svg class="filters__icon" width="22" height="18">
                                    <use xlink:href="#icon-filter-<?= $content_type['class_name'] ?>"></use>
                                </svg>
                            </a>
                        </li>
                    <?php endforeach; ?>

                </ul>
            </div>
        </div>
        <div class="popular__posts">

            <?php foreach ($posts as $post): ?>
                <article class="popular__post post post-<?= $post['class_name'] ?>">
                    <header class="post__header">
                        <h2><a href="post.php?id=<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></h2>
                    </header>
                    <div class="post__main">

                        <?php
                        switch ($post['class_name']) :
                            case 'text':
                                $result = crop_text($post['content']); ?>

                                <p><?= htmlspecialchars($result); ?></p>

                                <?php if ($post['content'] !== $result): ?>
                                <div class="post-text__more-link-wrapper">
                                    <a class="post-text__more-link" href="/post.php?id=<?= $post['id'] ?>">Читать
                                        далее</a>
                                </div>
                            <?php endif; ?>


                                <?php break;

                            case 'quote': ?>

                                <blockquote>
                                    <p><?= htmlspecialchars($post['content']) ?></p>
                                    <cite><?= htmlspecialchars($post['quote_author']) ?></cite>
                                </blockquote>

                                <?php break;

                            case 'link': ?>

                                <div class="post-link__wrapper">
                                    <a class="post-link__external" href="<?= htmlspecialchars($post['content']) ?>"
                                       title="Перейти по ссылке">
                                        <div class="post-link__info-wrapper">
                                            <div class="post-link__icon-wrapper">
                                                <img
                                                    src="https://www.google.com/s2/favicons?domain=<?= htmlspecialchars($post['content']) ?>"
                                                    alt="Иконка">
                                            </div>
                                            <div class="post-link__info">
                                                <h3><?= htmlspecialchars($post['content']); ?></h3>
                                            </div>
                                        </div>
                                        <span><?= htmlspecialchars($post['link']); ?></span>
                                    </a>
                                </div>
                                <?php break;

                            case 'photo': ?>

                                <div class="post-photo__image-wrapper">
                                    <img src="uploads/<?= htmlspecialchars($post['content']); ?>"
                                         alt="Фото от пользователя" width="360" height="240">
                                </div>

                                <?php break;
                            case 'video': ?>

                                <div class="post-video__block">
                                    <div class="post-video__preview">
                                        <?= embed_youtube_cover(htmlspecialchars($post['content'])); ?>
                                    </div>
                                    <a href="post.php?id=<?= $post['id'] ?>" class="post-video__play-big button">
                                        <svg class="post-video__play-big-icon" width="14" height="14">
                                            <use xlink:href="#icon-video-play-big"></use>
                                        </svg>
                                        <span class="visually-hidden">Запустить проигрыватель</span>
                                    </a>
                                </div>

                                <?php break;
                        endswitch;
                        ?>
                    </div>
                    <footer class="post__footer">
                        <div class="post__author">
                            <a class="post__author-link" href="/profile.php?id=<?= $post['user_id'] ?>" title="Автор">
                                <div class="post__avatar-wrapper">
                                    <img class="post__author-avatar"
                                         src="uploads/<?= $post['avatar'] ? htmlspecialchars($post['avatar']) : 'unnamed.png' ?>"
                                         alt="Аватар пользователя">
                                </div>
                                <div class="post__info">
                                    <b class="post__author-name"><?= htmlspecialchars($post['login']) ?></b>
                                    <time class="post__time" datetime="<?= $post['datetime'] ?>"
                                          title="<?= date('d.m.Y H:i', strtotime($post['datetime'])) ?>">
                                        <?= get_how_much_time($post['datetime']) ?>
                                    </time>
                                </div>
                            </a>
                        </div>
                        <div class="post__indicators">
                            <div class="post__buttons">
                                <a class="post__indicator post__indicator--likes button"<?= !$post['is_like'] ? ' href="/like.php?id=' . $post['id'] . '"' : '' ?>
                                   title="Лайк">
                                    <svg class="post__indicator-icon" width="20" height="17">
                                        <use xlink:href="#icon-heart"></use>
                                    </svg>
                                    <svg class="post__indicator-icon post__indicator-icon--like-active" width="20"
                                         height="17">
                                        <use xlink:href="#icon-heart-active"></use>
                                    </svg>
                                    <span><?= $post['likes_count'] ?></span>
                                    <span class="visually-hidden">количество лайков</span>
                                </a>
                                <a class="post__indicator post__indicator--comments button"
                                   href="/post.php?id=<?= $post['id'] ?>" title="Комментарии">
                                    <svg class="post__indicator-icon" width="19" height="17">
                                        <use xlink:href="#icon-comment"></use>
                                    </svg>
                                    <span><?= $post['comments_count'] ?></span>
                                    <span class="visually-hidden">количество комментариев</span>
                                </a>
                            </div>
                        </div>
                    </footer>
                </article>
            <?php endforeach; ?>

        </div>

        <?php if ($all_posts_num > $limit):

            $link_right = 'href="/popular.php?' . $p_content_type . '&page=' . ($page + 1) . $p_sort . $p_line . '"';
            $link_left = 'href="/popular.php?' . $p_content_type . '&page=' . ($page - 1) . $p_sort . $p_line . '"';

            ?>

            <div class="popular__page-links">
                <a class="popular__page-link popular__page-link--prev button button--gray" <?= $page > 1 ? $link_left : '' ?>>Предыдущая
                    страница</a>
                <a class="popular__page-link popular__page-link--next button button--gray" <?= $page < $num_pages ? $link_right : '' ?>>Следующая
                    страница</a>
            </div>

        <?php endif; ?>

    </div>
</section>
