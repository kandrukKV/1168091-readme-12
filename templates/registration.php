<main class="page__main page__main--registration">
    <div class="container">
        <h1 class="page__title page__title--registration">Регистрация</h1>
    </div>
    <section class="registration container">
        <h2 class="visually-hidden">Форма регистрации</h2>
        <form class="registration__form form" action="/registration.php" method="post" enctype="multipart/form-data">
            <div class="form__text-inputs-wrapper">
                <div class="form__text-inputs">
                    <?php foreach ($form_fields as $key => $value) : ?>
                        <div class="registration__input-wrapper form__input-wrapper">
                            <label class="registration__label form__label"
                                   for="registration-<?= $key ?>"><?= $value['label'] ?> <span
                                    class="form__input-required">*</span></label>
                            <div
                                class="form__input-section<?= isset($errors[$key]) ? ' form__input-section--error' : '' ?>">
                                <input
                                    class="registration__input form__input"
                                    id="registration-<?= $key ?>"
                                    type="<?= $value['type'] ?>"
                                    name="<?= $key ?>"
                                    placeholder="<?= $value['placeholder'] ?>"
                                    value="<?= get_post_val($key) ?>">

                                <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span>
                                </button>
                                <?php if (isset($error[$key])) : ?>
                                    <div class="form__error-text">
                                        <h3 class="form__error-title">Заголовок сообщения</h3>
                                        <p class="form__error-desc"><?= $error[$key] ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (count($errors) > 0) : ?>
                    <div class="form__invalid-block">
                        <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                        <ul class="form__invalid-list">
                            <?php foreach ($errors as $error) : ?>
                                <li class="form__invalid-item"><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

            <div class="registration__input-file-container form__input-container form__input-container--file">
                <div class="registration__input-file-wrapper form__input-file-wrapper">
                    <div class="registration__file-zone form__file-zone dropzone">
                        <input class="registration__input-file form__input-file" id="userpic-file" type="file"
                               name="userpic-file" title="">
                        <div class="form__file-zone-text">
                            <span>Перетащите фото сюда</span>
                        </div>
                    </div>
                    <button class="registration__input-file-button form__input-file-button button" type="button">
                        <span>Выбрать фото</span>
                        <svg class="registration__attach-icon form__attach-icon" width="10" height="20">
                            <use xlink:href="#icon-attach"></use>
                        </svg>
                    </button>
                </div>
                <div class="registration__file form__file dropzone-previews">

                </div>
            </div>
            <button class="registration__submit button button--main" type="submit">Отправить</button>
        </form>
    </section>
</main>
