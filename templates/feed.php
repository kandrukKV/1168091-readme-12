<main class="page__main page__main--feed">
    <div class="container">
        <h1 class="page__title page__title--feed">Моя лента</h1>
    </div>
    <div class="page__main-wrapper container">
        <section class="feed">
            <h2 class="visually-hidden">Лента</h2>
            <div class="feed__main-wrapper">
                <div class="feed__wrapper">
                    <?php if (isset($posts) && count($posts) > 0): ?>
                    <?php foreach ($posts as $post): ?>
                        <article class="feed__post post post-<?= $post['class_name'] ?>">
                            <header class="post__header post__author">
                                <a class="post__author-link" href="#" title="Автор">
                                    <div class="post__avatar-wrapper">
                                        <img class="post__author-avatar" src="/uploads/<?= $post['avatar'] ? htmlspecialchars($post['avatar']) : 'unnamed.png'?>" alt="Аватар пользователя" width="60" height="60">
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
                                    <h2><a href="/post.php?id=<?= htmlspecialchars($post['id'])?>"><?= htmlspecialchars($post['title'])?></a></h2>
                                    <div class="post-photo__image-wrapper">
                                        <img src="uploads/<?= htmlspecialchars($post['content'])?>" alt="Фото от пользователя" width="760" height="396">
                                    </div>
                                </div>

                            <?php break; case 'text': ?>
                                <div class="post__main">
                                    <h2><a href="<?= htmlspecialchars($post['id'])?>"><?= htmlspecialchars($post['title'])?></a></h2>
                                    <p><?= htmlspecialchars($post['content'])?></p>
                                    <a class="post-text__more-link" href="#">Читать далее</a>
                                </div>

                            <?php break; case 'video': ?>
                                <div class="post__main">
                                    <div class="post-video__block">
                                        <div class="post-video__preview">
                                            <iframe
                                                width="560"
                                                height="315"
                                                src="<?= htmlspecialchars($post['content'])?>"
                                                frameborder="0"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                allowfullscreen>
                                            </iframe>
                                        </div>
                                    </div>
                                </div>

                            <?php break; case 'quote': ?>
                            <div class="post__main">
                                        <blockquote>
                                            <p><?= $post['content']?></p>
                                            <cite><?= $post['quote_author']?></cite>
                                        </blockquote>
                                    </div>

                            <?php break; case 'link': ?>
                            <div class="post__main">
                                        <div class="post-link__wrapper">
                                            <a class="post-link__external" href="<?= $post['content']?>" title="Перейти по ссылке">
                                                <div class="post-link__icon-wrapper">
                                                    <img src="img/logo-vita.jpg" alt="Иконка">
                                                </div>
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
                                    <a class="post__indicator post__indicator--likes button" href="#" title="Лайк">
                                        <svg class="post__indicator-icon" width="20" height="17">
                                            <use xlink:href="#icon-heart"></use>
                                        </svg>
                                        <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                                            <use xlink:href="#icon-heart-active"></use>
                                        </svg>
                                        <span>250</span>
                                        <span class="visually-hidden">количество лайков</span>
                                    </a>
                                    <a class="post__indicator post__indicator--comments button" href="#" title="Комментарии">
                                        <svg class="post__indicator-icon" width="19" height="17">
                                            <use xlink:href="#icon-comment"></use>
                                        </svg>
                                        <span>25</span>
                                        <span class="visually-hidden">количество комментариев</span>
                                    </a>
                                    <a class="post__indicator post__indicator--repost button" href="#" title="Репост">
                                        <svg class="post__indicator-icon" width="19" height="17">
                                            <use xlink:href="#icon-repost"></use>
                                        </svg>
                                        <span>5</span>
                                        <span class="visually-hidden">количество репостов</span>
                                    </a>
                                </div>
                            </footer>
                        </article>

                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <ul class="feed__filters filters">

                <li class="feed__filters-item filters__item">
                    <a class="filters__button<?= $current_content_type_id === 'all' ? ' filters__button--active' : '' ?>" href="/feed.php">
                        <span>Все</span>
                    </a>
                </li>

                <?php foreach ($content_types as $content_type) : ?>
                    <li class="feed__filters-item filters__item">
                        <a class="filters__button filters__button--<?= $content_type['class_name'] ?> button<?= $current_content_type_id === $content_type['id'] ? ' filters__button--active' : '' ?>" href="/feed.php?content_type=<?= $content_type['id']?>">
                            <span class="visually-hidden"><?= $content_type['type_name']?></span>
                            <svg class="filters__icon" width="22" height="18">
                                <use xlink:href="#icon-filter-<?= $content_type['class_name'] ?>"></use>
                            </svg>
                        </a>
                    </li>
                <?php endforeach;?>



            </ul>
        </section>
        <aside class="promo">
            <article class="promo__block promo__block--barbershop">
                <h2 class="visually-hidden">Рекламный блок</h2>
                <p class="promo__text">
                    Все еще сидишь на окладе в офисе? Открой свой барбершоп по нашей франшизе!
                </p>
                <a class="promo__link" href="#">
                    Подробнее
                </a>
            </article>
            <article class="promo__block promo__block--technomart">
                <h2 class="visually-hidden">Рекламный блок</h2>
                <p class="promo__text">
                    Товары будущего уже сегодня в онлайн-сторе Техномарт!
                </p>
                <a class="promo__link" href="#">
                    Перейти в магазин
                </a>
            </article>
            <article class="promo__block">
                <h2 class="visually-hidden">Рекламный блок</h2>
                <p class="promo__text">
                    Здесь<br> могла быть<br> ваша реклама
                </p>
                <a class="promo__link" href="#">
                    Разместить
                </a>
            </article>
        </aside>
    </div>
</main>
