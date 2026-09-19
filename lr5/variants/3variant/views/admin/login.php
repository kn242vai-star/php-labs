<?php
$error = $error ?? '';
?>

<div class="admin-auth">
    <div class="admin-auth__card">
        <p class="admin-auth__eyebrow">Адміністративний доступ</p>
        <h1>Вхід до системи адміністрування</h1>

        <?php if ($error !== ''): ?>
            <div class="alert alert--error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?route=admin/login" class="form">
            <div class="form__group">
                <label class="form__label" for="login">Логін адміністратора</label>
                <input id="login" name="login" type="text" class="form__input" required>
            </div>

            <div class="form__group">
                <label class="form__label" for="password">Пароль</label>
                <input id="password" name="password" type="password" class="form__input" required>
            </div>

            <div class="form__actions">
                <button type="submit" class="btn">Увійти в панель</button>
                <a href="index.php?route=index/main" class="btn btn--secondary">Повернутися на сайт</a>
            </div>
        </form>

        <p class="admin-auth__hint">Типовий логін: <strong>admin</strong> · пароль: <strong>admin123</strong></p>
    </div>
</div>
