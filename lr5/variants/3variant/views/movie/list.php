<?php
$movies = $movies ?? [];
?>

<h1>Фільми</h1>
<p>Колекція фільмів кінотеатру. CRUD через PDO (prepared statements).</p>

<div class="form__actions" style="margin-bottom: 20px">
    <a href="index.php?route=movie/create" class="btn">Додати фільм</a>
</div>

<?php if (empty($movies)): ?>
    <p class="text-muted">Фільмів ще немає.</p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Назва</th>
                <th>Режисер</th>
                <th>Жанр</th>
                <th>Рік</th>
                <th>Тривалість (хв)</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($movies as $m): ?>
                <tr>
                    <td><?= (int)$m['id'] ?></td>
                    <td><?= htmlspecialchars($m['title']) ?></td>
                    <td><?= htmlspecialchars($m['director']) ?></td>
                    <td><?= htmlspecialchars($m['genre']) ?></td>
                    <td><?= (int)$m['year'] ?></td>
                    <td><?= (int)$m['duration_min'] ?></td>
                    <td class="table__actions">
                        <a href="index.php?route=movie/edit&id=<?= (int)$m['id'] ?>" class="btn btn--small">Редагувати</a>
                        <form method="POST" action="index.php?route=movie/delete" style="display:inline"
                              onsubmit="return confirm('Видалити фільм?')">
                            <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                            <button type="submit" class="btn btn--small btn--danger">Видалити</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
