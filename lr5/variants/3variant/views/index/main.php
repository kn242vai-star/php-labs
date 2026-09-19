<?php
$featuredMovies = $featuredMovies ?? [];
$upcomingShows = $upcomingShows ?? [];
$moviesCount = $moviesCount ?? 0;
$showsCount = $showsCount ?? 0;
$usersCount = $usersCount ?? 0;
$reservationsCount = $reservationsCount ?? 0;
?>

<div class="cinema-home">
    <section class="hero">
        <div class="hero__content">
            <span class="hero__eyebrow">Кінотеатр нового покоління</span>
            <h1>Прем’єри, зірки й комфортний перегляд <span>у одному місці</span></h1>
            <p>Обирайте фільми, бронюйте місця, переглядайте зручний розклад і насолоджуйтесь випуском новинок у стильному інтерфейсі кінотеатру.</p>
            <div class="hero__actions">
                <a href="index.php?route=movie/list" class="btn">До афіші</a>
                <a href="index.php?route=ticket/booking" class="btn btn--secondary">Забронювати квиток</a>
            </div>
        </div>

        <div class="hero__aside">
            <div class="hero__panel">
                <div class="hero__panel-header">
                    <span class="dots"><span></span><span></span><span></span></span>
                    <span>Сьогодні в прокаті</span>
                </div>
                <div class="hero__movie">
                    <div class="hero__poster" style="background-image: url('data/uploads/Інтерстеллар.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
                    <div class="hero__movie-info">
                        <p class="hero__tag">Найпопулярніше</p>
                        <h3>Інтерстеллар</h3>
                        <p>Наукова фантастика · 169 хв · 12+</p>
                    </div>
                </div>
                <div class="hero__schedule">
                    <div>
                        <strong>18:00</strong>
                        <span>Зал 3</span>
                    </div>
                    <div>
                        <strong>20:30</strong>
                        <span>Зал 3</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="stats-grid" aria-label="Статистика кінотеатру">
        <article class="stat-card">
            <span class="stat-card__label">Фільмів</span>
            <strong class="stat-card__value"><?= (int)$moviesCount ?></strong>
        </article>
        <article class="stat-card">
            <span class="stat-card__label">Сеансів</span>
            <strong class="stat-card__value"><?= (int)$showsCount ?></strong>
        </article>
        <article class="stat-card">
            <span class="stat-card__label">Користувачів</span>
            <strong class="stat-card__value"><?= (int)$usersCount ?></strong>
        </article>
        <article class="stat-card">
            <span class="stat-card__label">Бронювань</span>
            <strong class="stat-card__value"><?= (int)$reservationsCount ?></strong>
        </article>
    </section>

    <section class="feature-strip" aria-label="Переваги кінотеатру">
        <div>
            <strong>3D / IMAX</strong>
            <span>Сучасний звук та якість картинки</span>
        </div>
        <div>
            <strong>VIP зали</strong>
            <span>Комфортні крісла та індивідуальний сервіс</span>
        </div>
        <div>
            <strong>Інтерактивне бронювання</strong>
            <span>Вибір місця без зайвих клопотів</span>
        </div>
    </section>

    <section class="content-section">
        <div class="section-heading">
            <h2>Сьогодні в прокаті</h2>
            <a href="index.php?route=movie/list">Усі фільми</a>
        </div>

        <div class="movie-grid">
            <?php foreach ($featuredMovies as $movie): ?>
                <?php if (trim((string)($movie['title'] ?? '')) === 'Початок') continue; ?>
                <article class="movie-card">
                    <div class="movie-card__poster">
                        <?php if (!empty($movie['poster_url'])): ?>
                            <img src="<?= htmlspecialchars($movie['poster_url']) ?>" alt="<?= htmlspecialchars($movie['title']) ?> poster">
                        <?php else: ?>
                            <div style="<?= htmlspecialchars($movie['poster_style'] ?? '') ?>">
                                <span><?= htmlspecialchars(substr($movie['title'], 0, 2)) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="movie-card__body">
                        <div class="movie-card__meta">
                            <span><?= htmlspecialchars($movie['genre'] ?: 'Фільм') ?></span>
                            <span><?= (int)$movie['year'] ?></span>
                        </div>
                        <h3><?= htmlspecialchars($movie['title']) ?></h3>
                        <p><?= htmlspecialchars($movie['director']) ?></p>
                        <div class="movie-card__footer">
                            <span><?= (int)$movie['duration_min'] ?> хв</span>
                            <a href="index.php?route=ticket/booking" class="movie-card__link">Бронювати</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="content-section">
        <div class="section-heading">
            <h2>Розклад сеансів</h2>
            <a href="index.php?route=ticket/booking">Перейти до бронювання</a>
        </div>

        <div class="schedule-list">
            <?php foreach ($upcomingShows as $show): ?>
                <article class="schedule-item">
                    <div>
                        <span class="schedule-item__label"><?= htmlspecialchars($show['hall']) ?></span>
                        <h3><?= htmlspecialchars($show['movie_title']) ?></h3>
                    </div>
                    <div class="schedule-item__info">
                        <span><?= date('d.m.Y', strtotime($show['show_time'])) ?></span>
                        <strong><?= date('H:i', strtotime($show['show_time'])) ?></strong>
                    </div>
                    <a href="index.php?route=ticket/booking&show_id=<?= (int)$show['id'] ?>" class="btn btn--small">Вибрати</a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="content-section content-section--lab">
        <h2>Лабораторна частина</h2>
        <div class="card-grid">
            <div class="card">
                <h3 class="card__title">Гостьова книга</h3>
                <p class="card__text">Залишайте відгуки про фільми. Коментарі зберігаються у текстовому файлі.</p>
                <a href="index.php?route=guestbook/index" class="btn btn--small">Відгуки</a>
            </div>

            <div class="card">
                <h3 class="card__title">Постери фільмів</h3>
                <p class="card__text">Завантажуйте постери фільмів. Галерея кіношних шедеврів.</p>
                <a href="index.php?route=upload/index" class="btn btn--small">Галерея</a>
            </div>

            <div class="card">
                <h3 class="card__title">Каталоги глядачів</h3>
                <p class="card__text">Персональні папки для глядачів з колекціями відео, музики та фото.</p>
                <a href="index.php?route=folder/create" class="btn btn--small">Каталоги</a>
            </div>

            <div class="card">
                <h3 class="card__title">Фільми (CRUD)</h3>
                <p class="card__text">Колекція фільмів з режисером, жанром, роком та тривалістю. PDO + SQLite.</p>
                <a href="index.php?route=movie/list" class="btn btn--small">До фільмів</a>
            </div>

            <div class="card">
                <h3 class="card__title">Квитки та бронювання</h3>
                <p class="card__text">Оберіть фільм, час сеансу та зручні місця у залі. Бронювання через БД.</p>
                <a href="index.php?route=ticket/booking" class="btn btn--small">Квитки</a>
            </div>

            <div class="card">
                <h3 class="card__title">Акаунт глядача</h3>
                <p class="card__text">Реєстрація, вхід, профіль. Хешування паролів, сесійна авторизація.</p>
                <a href="index.php?route=auth/login" class="btn btn--small">Увійти</a>
            </div>
        </div>
    </section>
</div>
