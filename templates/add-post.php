<main class="page__main page__main--adding-post">
    <div class="page__main-section">
        <div class="container">
            <h1 class="page__title page__title--adding-post">Добавить публикацию</h1>
        </div>
        <div class="adding-post container">
            <div class="adding-post__tabs-wrapper tabs">
                <div class="adding-post__tabs filters">
                    <ul class="adding-post__tabs-list filters__list tabs__list">
                        <?php foreach ($content_types as $content_type): ?>
                            <li class="adding-post__tabs-item filters__item">
                                <a class="
                                adding-post__tabs-link filters__button
                                filters__button--<?= $content_type['class_name'] ?>
                                tabs__item button
                                <?= $content_type['class_name'] === $current_tab ? 'filters__button--active tabs__item--active' : '' ?>"
                                   href="add.php?tab=<?= $content_type['class_name'] ?>">
                                    <svg class="filters__icon" width="22" height="18">
                                        <use xlink:href="#icon-filter-photo"></use>
                                    </svg>
                                    <span><?= $content_type['type_name'] ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="adding-post__tab-content">
                    <?php foreach ($content_types as $content_type): ?>
                        <section
                            class="adding-post__photo tabs__content<?= $current_tab === $content_type['class_name'] ? ' tabs__content--active' : '' ?>">
                            <h2 class="visually-hidden">Форма добавления <?= $content_type['type_name'] ?></h2>
                            <form class="adding-post__form form"
                                  action="add.php?tab=<?= $current_tab ?>"
                                  method="post"<?= $content_type['class_name'] === 'photo' || $content_type['class_name'] === 'video' ? ' enctype="multipart/form-data"' : '' ?>>
                                <input type="hidden" name="content_type" value="<?= $content_type['class_name'] ?>">
                                <div class="form__text-inputs-wrapper">
                                    <div class="form__text-inputs">
                                        <div class="adding-post__input-wrapper form__input-wrapper">
                                            <label class="adding-post__label form__label"
                                                   for="<?= $content_type['class_name'] ?>-heading">Заголовок
                                                <span class="form__input-required">*</span>
                                            </label>
                                            <div
                                                class="form__input-section<?= $current_tab === $content_type['class_name'] && isset($errors['title']) ? ' form__input-section--error' : '' ?>">
                                                <input
                                                    class="adding-post__input form__input"
                                                    id="<?= $content_type['class_name'] ?>-heading"
                                                    type="text"
                                                    name="title"
                                                    placeholder="Введите заголовок"
                                                    value="<?= $current_tab === $content_type['class_name'] ? get_post_val('title') : '' ?>">
                                                <button class="form__error-button button" type="button">!<span
                                                        class="visually-hidden">Информация об ошибке</span></button>

                                                <?php if ($current_tab === $content_type['class_name']) : ?>
                                                    <div class="form__error-text">
                                                        <h3 class="form__error-title">Заголовок сообщения</h3>
                                                        <p class="form__error-desc"><?= $errors['title'] ?? '' ?></p>
                                                    </div>
                                                <?php endif; ?>

                                            </div>
                                        </div>

                                        <?php if ($content_type['class_name'] === 'photo'): ?>
                                            <div class="adding-post__input-wrapper form__input-wrapper">
                                                <label class="adding-post__label form__label"
                                                       for="<?= $content_type['class_name'] ?>-url">Ссылка из
                                                    интернета</label>
                                                <div
                                                    class="form__input-section<?= $current_tab === $content_type['class_name'] && isset($errors['content']) ? ' form__input-section--error' : '' ?>">
                                                    <input
                                                        class="adding-post__input form__input"
                                                        id="<?= $content_type['class_name'] ?>-url"
                                                        type="text" name="content"
                                                        placeholder="Введите ссылку"
                                                        value="<?= get_post_val('content') ?>">
                                                    <button class="form__error-button button" type="button">
                                                        !<span class="visually-hidden">Информация об ошибке</span>
                                                    </button>
                                                    <div class="form__error-text">
                                                        <h3 class="form__error-title">Ошибка</h3>
                                                        <p class="form__error-desc"><?= $errors['content'] ?? '' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($content_type['class_name'] === 'video'): ?>
                                            <div class="adding-post__input-wrapper form__input-wrapper">
                                                <label class="adding-post__label form__label"
                                                       for="<?= $content_type['class_name'] ?>-url">Ссылка youtube <span
                                                        class="form__input-required">*</span></label>
                                                <div
                                                    class="form__input-section <?= $current_tab === $content_type['class_name'] && isset($errors['content']) ? ' form__input-section--error' : '' ?>">
                                                    <input
                                                        class="adding-post__input form__input"
                                                        id="<?= $content_type['class_name'] ?>-url"
                                                        type="text" name="content"
                                                        placeholder="Введите ссылку"
                                                        value="<?= get_post_val('content') ?>">
                                                    <button class="form__error-button button" type="button">!<span
                                                            class="visually-hidden">Информация об ошибке</span></button>
                                                    <div class="form__error-text">
                                                        <h3 class="form__error-title">Заголовок сообщения</h3>
                                                        <p class="form__error-desc"><?= $errors['content'] ?? '' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($content_type['class_name'] === 'text'): ?>
                                            <div class="adding-post__textarea-wrapper form__textarea-wrapper">
                                                <label
                                                    class="adding-post__label form__label"
                                                    for="post-<?= $content_type['class_name'] ?>">
                                                    Текст поста
                                                    <span class="form__input-required">*</span>
                                                </label>
                                                <div
                                                    class="form__input-section<?= $current_tab === $content_type['class_name'] && isset($errors['content']) ? ' form__input-section--error' : '' ?>">
                                            <textarea
                                                class="adding-post__textarea form__textarea form__input"
                                                id="post-<?= $content_type['class_name'] ?>"
                                                name="content"
                                                placeholder="Введите текст публикации"><?= get_post_val('content') ?></textarea>
                                                    <button class="form__error-button button" type="button">!
                                                        <span class="visually-hidden">Информация об ошибке</span>
                                                    </button>
                                                    <div class="form__error-text">
                                                        <h3 class="form__error-title">Заголовок сообщения</h3>
                                                        <p class="form__error-desc"><?= $errors['content'] ?? '' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($content_type['class_name'] === 'quote'): ?>
                                            <div class="adding-post__textarea-wrapper form__textarea-wrapper">
                                                <label
                                                    class="adding-post__label form__label"
                                                    for="post-<?= $content_type['class_name'] ?>">
                                                    Текст цитаты
                                                    <span class="form__input-required">*</span>
                                                </label>
                                                <div
                                                    class="form__input-section<?= $current_tab === $content_type['class_name'] && isset($errors['content']) ? ' form__input-section--error' : '' ?>">
                                            <textarea
                                                class="adding-post__textarea adding-post__textarea--quote form__textarea form__input"
                                                id="post-<?= $content_type['class_name'] ?>"
                                                name="content"
                                                placeholder="Текст цитаты"><?= get_post_val('content') ?></textarea>
                                                    <button class="form__error-button button" type="button">!
                                                        <span class="visually-hidden">Информация об ошибке</span>
                                                    </button>
                                                    <div class="form__error-text">
                                                        <h3 class="form__error-title">Заголовок сообщения</h3>
                                                        <p class="form__error-desc"><?= $errors['content'] ?? '' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="adding-post__input-wrapper form__input-wrapper">
                                                <label
                                                    class="adding-post__label form__label"
                                                    for="<?= $content_type['class_name'] ?>-author">Автор
                                                    <span class="form__input-required">*</span>
                                                </label>
                                                <div
                                                    class="form__input-section<?= $current_tab === $content_type['class_name'] && isset($errors['author']) ? ' form__input-section--error' : '' ?>">
                                                    <input
                                                        class="adding-post__input form__input"
                                                        id="<?= $content_type['class_name'] ?>-author"
                                                        type="text"
                                                        name="author"
                                                        value="<?= get_post_val('author') ?>">
                                                    <button class="form__error-button button" type="button">!
                                                        <span class="visually-hidden">Информация об ошибке</span>
                                                    </button>
                                                    <div class="form__error-text">
                                                        <h3 class="form__error-title">Заголовок сообщения</h3>
                                                        <p class="form__error-desc"><?= $errors['author'] ?? '' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($content_type['class_name'] === 'link'): ?>
                                            <div class="adding-post__input-wrapper form__input-wrapper">
                                                <label class="adding-post__label form__label"
                                                       for="post-<?= $content_type['class_name'] ?>">Ссылка <span
                                                        class="form__input-required">*</span></label>
                                                <div
                                                    class="form__input-section<?= $current_tab === $content_type['class_name'] && isset($errors['content']) ? ' form__input-section--error' : '' ?>">
                                                    <input
                                                        class="adding-post__input form__input"
                                                        id="post-<?= $content_type['class_name'] ?>"
                                                        type="text"
                                                        name="content"
                                                        value="<?= get_post_val('content') ?>">
                                                    <button class="form__error-button button" type="button">
                                                        !<span class="visually-hidden">Информация об ошибке</span>
                                                    </button>
                                                    <div class="form__error-text">
                                                        <h3 class="form__error-title">Заголовок сообщения</h3>
                                                        <p class="form__error-desc"><?= $errors['content'] ?? '' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <div class="adding-post__input-wrapper form__input-wrapper">
                                            <label class="adding-post__label form__label"
                                                   for="<?= $content_type['class_name'] ?>-tags">Теги</label>
                                            <div
                                                class="form__input-section <?= $current_tab === $content_type['class_name'] && isset($errors['tags']) ? 'form__input-section--error' : '' ?>">
                                                <input
                                                    class="adding-post__input form__input"
                                                    id="<?= $content_type['class_name'] ?>-tags"
                                                    type="text"
                                                    name="tags"
                                                    placeholder="Введите теги"
                                                    value="<?= $current_tab === $content_type['class_name'] ? get_post_val('tags') : '' ?>">
                                                <button class="form__error-button button" type="button">!<span
                                                        class="visually-hidden">Информация об ошибке</span></button>
                                                <?php if ($current_tab === $content_type['class_name']): ?>
                                                    <div class="form__error-text">
                                                        <h3 class="form__error-title">Ошибка</h3>
                                                        <p class="form__error-desc"><?= $errors['tags'] ?? '' ?></p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if ($current_tab === $content_type['class_name'] && count($errors) > 0) : ?>
                                        <div class="form__invalid-block">
                                            <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                                            <ul class="form__invalid-list">
                                                <?php foreach ($errors as $error): ?>
                                                    <li class="form__invalid-item"><?= $error ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>

                                </div>

                                <?php if ($content_type['class_name'] === 'photo'): ?>
                                    <div
                                        class="adding-post__input-file-container form__input-container form__input-container--file">
                                        <input class="adding-post__input-file" id="userpic-file-photo" type="file"
                                               name="photo-file">
                                    </div>
                                <?php endif; ?>

                                <div class="adding-post__buttons">
                                    <button class="adding-post__submit button button--main" type="submit">Опубликовать
                                    </button>
                                    <a class="adding-post__close" href="/index.php">Закрыть</a>
                                </div>

                            </form>
                        </section>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</main>
