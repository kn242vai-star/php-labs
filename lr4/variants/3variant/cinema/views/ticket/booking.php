<div class="page-ticket">
    <h1>Бронювання квитків</h1>

    <p class="text-muted">Оберіть сеанс та зручні місця в залі. Після підтвердження квиток буде куплено та місце заброньовано.</p>

    <?php if (!empty($errors)): ?>
        <div class="alert alert--error">
            <strong>Будь ласка, виправте помилки:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="index.php?route=ticket/booking" method="post" class="form">
        <div class="form__group">
            <label class="form__label" for="show_id">Оберіть сеанс</label>
            <select id="show_id" name="show_id" class="form__select">
                <option value="">-- Виберіть сеанс --</option>
                <?php foreach ($shows as $show): ?>
                    <option value="<?= htmlspecialchars($show['id']) ?>"
                        <?= $old['show_id'] === $show['id'] ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars("{$show['movie']} — {$show['hall']} ({$show['time']}) — {$show['price']} грн") ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form__group">
            <label class="form__label">Оберіть місця</label>
            <div class="seat-legend">
                <div><span class="seat seat--selected"></span> Ваш вибір</div>
                <div><span class="seat seat--taken"></span> Заброньовано</div>
                <div><span class="seat"></span> Вільне</div>
            </div>
            <?php foreach ($seatRows as $row): ?>
                <div class="seat-row">
                    <div class="seat-row__label"><?= htmlspecialchars($row) ?></div>
                    <?php for ($i = 1; $i <= $seatsPerRow; $i++):
                        $seatCode = $row . $i;
                        $isReserved = in_array($seatCode, $reserved[$old['show_id']] ?? [], true);
                        $isSelected = in_array($seatCode, $old['seats'], true);
                    ?>
                        <label class="seat<?= $isReserved ? ' seat--taken' : '' ?><?= $isSelected ? ' seat--selected' : '' ?>">
                            <input type="checkbox" name="seat[]" value="<?= htmlspecialchars($seatCode) ?>"
                                   <?= $isReserved ? 'disabled' : '' ?>
                                   <?= $isSelected ? 'checked' : '' ?>>
                            <?= htmlspecialchars($seatCode) ?>
                        </label>
                    <?php endfor; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="form__actions">
            <button type="submit" class="btn">Забронювати та оплатити</button>
            <a href="index.php" class="btn btn--secondary">Повернутись на головну</a>
        </div>
    </form>
</div>
