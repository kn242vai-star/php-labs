<?php
$images = $images ?? [];
$message = $message ?? '';
$error = $error ?? '';
?>

<h1>Завантаження зображень</h1>
<p>Завантажте зображення (JPEG, PNG, GIF, WebP, до 5 МБ). Файли зберігаються у <code>data/uploads/</code>.</p>

<?php if ($message !== ''): ?>
    <div class="alert alert--success"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php if ($error !== ''): ?>
    <div class="alert alert--error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" action="index.php?route=upload/index" enctype="multipart/form-data" class="form">
    <div class="form__group">
        <label for="upload_image" class="form__label">Оберіть зображення <span class="required">*</span></label>
        <input type="file" id="upload_image" name="image" class="form__input" accept="image/*">
    </div>

    <div class="form__actions">
        <button type="submit" class="btn">Завантажити</button>
    </div>
</form>

<h2>Галерея (<?= count($images) ?>)</h2>

<?php if (empty($images)): ?>
    <p class="text-muted">Зображень ще немає.</p>
<?php else: ?>
    <div class="gallery">
        <?php foreach ($images as $img): ?>
            <div class="gallery__item">
                <img src="<?= htmlspecialchars($img['url']) ?>" alt="<?= htmlspecialchars($img['name']) ?>" class="gallery__img">
                <div class="gallery__info">
                    <span class="gallery__name"><?= htmlspecialchars($img['display_name']) ?></span>
                    <span class="gallery__meta"><?= htmlspecialchars($img['date']) ?> &middot; <?= round($img['size'] / 1024) ?> КБ</span>
                </div>
                <form method="POST" action="index.php?route=upload/rename" class="gallery__rename">
                    <input type="hidden" name="current_name" value="<?= htmlspecialchars($img['name']) ?>">
                    <label class="form__label" for="rename_<?= md5($img['name']) ?>">Новa назва <small>(без розширення)</small></label>
                    <input id="rename_<?= md5($img['name']) ?>" type="text" name="new_name" class="form__input form__input--small" value="<?= htmlspecialchars(pathinfo($img['name'], PATHINFO_FILENAME)) ?>">
                    <button type="submit" class="btn btn--small btn--secondary">Перейменувати</button>
                </form>
                <form method="POST" action="index.php?route=upload/delete" class="gallery__action" onsubmit="return confirm('Видалити це зображення?');">
                    <input type="hidden" name="image" value="<?= htmlspecialchars($img['name']) ?>">
                    <button type="submit" class="btn btn--small btn--danger">Видалити</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
