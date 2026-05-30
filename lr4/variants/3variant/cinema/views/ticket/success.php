<div class="success-page">
    <h1>Квиток придбано</h1>

    <div class="alert alert--success">
        <p>Дякуємо! Ваше бронювання успішно оформлено.</p>
    </div>

    <div class="info-block">
        <h2>Деталі квитка</h2>
        <table class="table">
            <tr>
                <th>Фільм</th>
                <td><?= htmlspecialchars($ticket['show']['movie']) ?></td>
            </tr>
            <tr>
                <th>Зал</th>
                <td><?= htmlspecialchars($ticket['show']['hall']) ?></td>
            </tr>
            <tr>
                <th>Час</th>
                <td><?= htmlspecialchars($ticket['show']['time']) ?></td>
            </tr>
            <tr>
                <th>Місця</th>
                <td><?= htmlspecialchars(implode(', ', $ticket['seats'])) ?></td>
            </tr>
            <tr>
                <th>Сума</th>
                <td><?= htmlspecialchars($ticket['total']) ?> грн</td>
            </tr>
            <tr>
                <th>Дата бронювання</th>
                <td><?= htmlspecialchars($ticket['booked_at']) ?></td>
            </tr>
        </table>
    </div>

    <div class="success-page__actions">
        <a href="index.php?route=ticket/booking" class="btn">Забронювати ще квиток</a>
        <a href="index.php" class="btn btn--secondary">На головну</a>
    </div>
</div>
