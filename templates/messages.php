<main class="page__main page__main--messages">
    <h1 class="visually-hidden">Личные сообщения</h1>
    <section class="messages tabs">
        <h2 class="visually-hidden">Сообщения</h2>
        <div class="messages__contacts">
            <ul class="messages__contacts-list tabs__list">

                <?php foreach ($members as $index => $member) : ?>
                    <li class="messages__contacts-item <?= isset($member['last_message']) && $member['new_msg_count'] > 0 ? 'messages__contacts-item--new' : '' ?>">
                        <a class="messages__contacts-tab tabs__item
                    <?= $list_id == $member['member_id'] ? ' messages__contacts-tab--active tabs__item--active' : '' ?>"
                            <?= $list_id != $member['member_id'] ? 'href="/messages.php?list_id=' . $member['member_id'] . '"' : '' ?>>
                            <div class="messages__avatar-wrapper">
                                <img class="messages__avatar"
                                     src="uploads/<?= $member['avatar'] ? htmlspecialchars($member['avatar']) : 'unnamed.png' ?>"
                                     alt="Аватар пользователя">

                                <?php if (isset($member['last_message']) && $member['new_msg_count'] > 0) : ?>
                                    <i class="messages__indicator"><?= $member['new_msg_count'] ?></i>
                                <?php endif; ?>

                            </div>
                            <div class="messages__info">
                  <span class="messages__contact-name">
                    <?= htmlspecialchars($member['login']) ?>
                  </span>
                                <?php if ($member['last_message']) : ?>
                                    <div class="messages__preview">
                                        <p class="messages__preview-text">
                                            <?= $member['last_message'] ?>
                                        </p>
                                        <time class="messages__preview-time"
                                              datetime="<?= $member['last_message_date'] ?>">
                                            <?= date_format(date_create($member['last_message_date']), 'H:i') ?>
                                        </time>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </a>
                    </li>
                <?php endforeach; ?>

            </ul>
        </div>
        <div class="messages__chat">

            <?php if (count($messages) > 0) : ?>
                <div class="messages__chat-wrapper">
                    <ul class="messages__list tabs__content tabs__content--active">
                        <?php foreach ($messages as $message) : ?>
                            <li class="messages__item <?= $_SESSION['user_id'] == $message['user_id'] ? 'messages__item--my' : '' ?>">
                                <div class="messages__info-wrapper">
                                    <div class="messages__item-avatar">
                                        <a class="messages__author-link" href="#">
                                            <img class="messages__avatar"
                                                 src="uploads/<?= $message['avatar'] ? htmlspecialchars($message['avatar']) : 'unnamed.png' ?>"
                                                 alt="Аватар пользователя">
                                        </a>
                                    </div>
                                    <div class="messages__item-info">
                                        <a class="messages__author" href="#">
                                            <?= htmlspecialchars($message['login']) ?>
                                        </a>
                                        <time class="messages__time" datetime="<?= $message['datetime'] ?>">
                                            <?= get_how_much_time($message['datetime']) ?> назад
                                        </time>
                                    </div>
                                </div>
                                <p class="messages__text">
                                    <?= htmlspecialchars($message['content']) ?>
                                </p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($list_id): ?>
                <div class="comments">
                    <form class="comments__form form" action="/messages.php?list_id=<?= $list_id ?>" method="post">
                        <div class="comments__my-avatar">
                            <img class="comments__picture"
                                 src="uploads/<?= $_SESSION['avatar'] ? htmlspecialchars($_SESSION['avatar']) : 'unnamed.png' ?>"
                                 alt="Аватар пользователя">
                        </div>
                        <div class="form__input-section <?= count($errors) > 0 ? 'form__input-section--error' : '' ?>">
                            <textarea id="comment" class="comments__textarea form__textarea form__input"
                                      placeholder="Ваше сообщение"
                                      name="comment"><?= get_post_val('comment') ?></textarea>
                            <label for="comment" class="visually-hidden">Ваше сообщение</label>
                            <button class="form__error-button button" type="button">!</button>
                            <?php if (count($errors) > 0) : ?>
                                <div class="form__error-text">
                                    <h3 class="form__error-title">Ошибка</h3>
                                    <p class="form__error-desc"><?= $errors['content'] ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button class="comments__submit button button--green" type="submit">Отправить</button>
                    </form>
                </div>
            <?php else : ?>
                <p>Выберите адресата слева</p>
            <?php endif; ?>

        </div>
    </section>
</main>
