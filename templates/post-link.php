<div class="post__main">
    <div class="post-link__wrapper" style="border-top: none;">
        <a class="post-link__external" href="<?= htmlspecialchars($content) ?>" title="Перейти по ссылке">
            <div class="post-link__icon-wrapper">
                <img src="https://www.google.com/s2/favicons?domain=<?= htmlspecialchars($content) ?>" alt="Иконка">
            </div>
            <div class="post-link__info">
                <h3><?= htmlspecialchars($content) ?></h3>
            </div>
            <svg class="post-link__arrow" width="11" height="16">
                <use xlink:href="#icon-arrow-right-ad"></use>
            </svg>
        </a>
    </div>
</div>

