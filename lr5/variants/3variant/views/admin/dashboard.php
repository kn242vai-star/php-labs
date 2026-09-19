<?php
$stats = $stats ?? [];
$modules = $modules ?? [];
?>

<div class="admin-dashboard">
    <div class="admin-dashboard__header">
        <div>
            <p class="admin-auth__eyebrow">Адміністративний режим</p>
            <h1>Панель керування сайтом</h1>
        </div>
        <div class="admin-dashboard__actions">
            <a href="index.php?route=admin/toggle_mode" class="btn btn--secondary">Перейти в режим користувача</a>
            <a href="index.php?route=admin/logout" class="btn btn--danger">Вийти</a>
        </div>
    </div>

    <section class="stats-grid admin-stats">
        <article class="stat-card">
            <span class="stat-card__label">Фільми</span>
            <strong class="stat-card__value"><?= (int)($stats['movies'] ?? 0) ?></strong>
        </article>
        <article class="stat-card">
            <span class="stat-card__label">Користувачі</span>
            <strong class="stat-card__value"><?= (int)($stats['users'] ?? 0) ?></strong>
        </article>
        <article class="stat-card">
            <span class="stat-card__label">Бронювання</span>
            <strong class="stat-card__value"><?= (int)($stats['bookings'] ?? 0) ?></strong>
        </article>
        <article class="stat-card">
            <span class="stat-card__label">Сеанси</span>
            <strong class="stat-card__value"><?= (int)($stats['shows'] ?? 0) ?></strong>
        </article>
    </section>

    <section class="content-section admin-panel">
        <div class="section-heading">
            <h2>Модулі системи</h2>
        </div>
        <div class="card-grid">
            <?php foreach ($modules as $module): ?>
                <div class="card admin-module">
                    <h3 class="card__title"><?= htmlspecialchars($module['title']) ?></h3>
                    <p class="card__text"><?= htmlspecialchars($module['description']) ?></p>
                    <a href="index.php?route=<?= htmlspecialchars($module['route']) ?>" class="btn btn--small">Відкрити</a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="content-section">
        <div class="section-heading">
            <h2>JSON API</h2>
        </div>
        <div class="api-panel">
            <p>Асинхронний обмін даними через JSON для оновлення інформації без перезавантаження сторінки.</p>
            <div class="api-panel__actions">
                <button type="button" class="btn btn--small" data-json-module="overview">Завантажити статистику</button>
                <button type="button" class="btn btn--small btn--secondary" data-json-module="movies">Фільми</button>
                <button type="button" class="btn btn--small btn--secondary" data-json-module="users">Користувачі</button>
                <button type="button" class="btn btn--small btn--secondary" data-json-module="bookings">Бронювання</button>
            </div>
            <pre id="json-output" class="json-output">Натисніть кнопку, щоб завантажити дані JSON.</pre>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const output = document.getElementById('json-output');
        const buttons = document.querySelectorAll('[data-json-module]');

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                const module = button.dataset.jsonModule;
                output.textContent = 'Завантаження...';

                fetch('index.php?route=admin/json&module=' + encodeURIComponent(module), {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('Запит не вдався');
                        }
                        return response.json();
                    })
                    .then(function (data) {
                        output.textContent = JSON.stringify(data, null, 2);
                    })
                    .catch(function () {
                        output.textContent = 'Не вдалося завантажити JSON-дані.';
                    });
            });
        });
    });
</script>
