<?php
$movies = $movies ?? [];
$genres = $genres ?? [];
$search = $search ?? '';
$genre = $genre ?? '';
$sort = $sort ?? 'newest';
$isAdminMode = !empty($_SESSION['is_admin']) && !empty($_SESSION['admin_mode']);
?>

<div class="catalog-page">
    <div class="catalog-header">
        <div>
            <h1>Афіша фільмів</h1>
            <p class="page-home__subtitle">Пошук, сортування та швидке бронювання місць у залі.</p>
        </div>
        <a href="index.php?route=movie/create" class="btn">Додати фільм</a>
    </div>

    <form method="GET" class="movie-filter" action="index.php">
        <input type="hidden" name="route" value="movie/list">
        <div class="movie-filter__field movie-filter__field--wide">
            <label for="search">Пошук</label>
            <input id="search" name="search" type="text" class="form__input" value="<?= htmlspecialchars($search) ?>" placeholder="Назва, режисер або жанр">
        </div>
        <div class="movie-filter__field">
            <label for="genre">Жанр</label>
            <select id="genre" name="genre" class="form__select">
                <option value="">Усі жанри</option>
                <?php foreach ($genres as $g): ?>
                    <option value="<?= htmlspecialchars($g) ?>" <?= $genre === $g ? 'selected' : '' ?>><?= htmlspecialchars($g) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="movie-filter__field">
            <label for="sort">Сортування</label>
            <select id="sort" name="sort" class="form__select">
                <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Новіші</option>
                <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Старіші</option>
                <option value="title" <?= $sort === 'title' ? 'selected' : '' ?>>За назвою</option>
                <option value="duration" <?= $sort === 'duration' ? 'selected' : '' ?>>За тривалістю</option>
            </select>
        </div>
        <div class="movie-filter__actions">
            <button type="submit" class="btn btn--small">Застосувати</button>
            <a href="index.php?route=movie/list" class="btn btn--small btn--secondary">Скинути</a>
        </div>
    </form>

    <?php if (empty($movies)): ?>
        <div class="empty-state">
            <p>Фільмів за таким запитом не знайдено.</p>
        </div>
    <?php else: ?>
        <div class="movie-grid movie-grid--compact">
            <?php foreach ($movies as $m): ?>
                <article class="movie-card movie-card--list">
                    <div class="movie-card__poster movie-card__poster--small">
                        <?php if (!empty($m['poster_url'])): ?>
                            <img src="<?= htmlspecialchars($m['poster_url']) ?>" alt="<?= htmlspecialchars($m['title']) ?> poster">
                        <?php else: ?>
                            <span><?= htmlspecialchars(substr($m['title'], 0, 2)) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="movie-card__body">
                        <div class="movie-card__meta">
                            <span><?= htmlspecialchars($m['genre'] ?: 'Фільм') ?></span>
                            <span><?= (int)$m['year'] ?></span>
                        </div>
                        <h3><?= htmlspecialchars($m['title']) ?></h3>
                        <p><?= htmlspecialchars($m['director']) ?></p>
                        <div class="movie-card__footer movie-card__footer--stacked">
                            <span><?= (int)$m['duration_min'] ?> хв</span>
                            <span>ID: <?= (int)$m['id'] ?></span>
                        </div>
                        <div class="table__actions movie-card__actions">
                            <?php if ($isAdminMode): ?>
                                <a href="index.php?route=movie/edit&id=<?= (int)$m['id'] ?>" class="btn btn--small">Редагувати</a>
                            <?php endif; ?>
                            <a href="index.php?route=ticket/booking" class="btn btn--small btn--secondary">Бронювати</a>
                            <?php if ($isAdminMode): ?>
                                <form method="POST" action="index.php?route=movie/delete" style="display:inline" onsubmit="return confirm('Видалити фільм?')">
                                    <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                                    <button type="submit" class="btn btn--small btn--danger">Видалити</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
