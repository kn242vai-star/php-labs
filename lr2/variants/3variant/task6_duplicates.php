<?php
/**
 * - Масив: [9, 1, 5, 9, 3, 7, 1, 5, 11, 3, 7, 15]
 * - Очікуваний результат: [9, 1, 5, 3, 7]
 */
require_once __DIR__ . '/layout.php';

function findDuplicates(array $arr): array
{
    if (empty($arr)) {
        return [];
    }

    $counts = array_count_values($arr);

    $duplicates = array_filter($counts, fn($count) => $count > 1);

    return array_keys($duplicates);
}

$defaultInput = '9, 1, 5, 9, 3, 7, 1, 5, 11, 3, 7, 15';
$input = $_POST['array'] ?? $defaultInput;
$submitted = isset($_POST['array']);

$arr = array_map('trim', explode(',', $input));
$arr = array_filter($arr, fn($v) => $v !== '');

$duplicates = findDuplicates($arr);

ob_start();
?>
<div class="demo-card">
    <h2>Пошук дублікатів</h2>
    <p class="demo-subtitle">Виділяє елементи, які зустрічаються в масиві 2 або більше разів</p>

    <form method="post" class="demo-form">
        <div>
            <label for="array">Масив (через кому)</label>
            <input type="text" id="array" name="array" value="<?= htmlspecialchars($input) ?>" placeholder="9, 1, 5, 9, 3">
        </div>
        <button type="submit" class="btn-submit">Знайти дублікати</button>
    </form>

    <?php if (!empty($arr)): ?>
    <div class="demo-section">
        <h3>Вхідний масив</h3>
        <div class="array-display">
            <?php foreach ($arr as $item): ?>
            <span class="array-item <?= in_array($item, $duplicates) ? 'array-item-unique' : '' ?>">
                <?= htmlspecialchars($item) ?>
            </span>
            <?php endforeach; ?>
        </div>
        <small>* Підсвічено елементи, що мають дублікати</small>
    </div>

    <div class="demo-result">
        <h3>Знайдені дублікати</h3>
        <div class="demo-result-value">
            <?php if (!empty($duplicates)): ?>
                [<?= implode(', ', $duplicates) ?>]
            <?php else: ?>
                Дублікатів не знайдено
            <?php endif; ?>
        </div>
    </div>

    <div class="demo-section">
        <h3>Аналіз частоти</h3>
        <table class="demo-table">
            <thead>
                <tr>
                    <th>Елемент</th>
                    <th>Кількість входжень</th>
                    <th>Статус</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $allCounts = array_count_values($arr);
                arsort($allCounts);
                foreach ($allCounts as $value => $count):
                ?>
                <tr>
                    <td><?= htmlspecialchars($value) ?></td>
                    <td><?= $count ?></td>
                    <td>
                        <?php if ($count > 1): ?>
                            <span class="demo-tag demo-tag-success">Дублікат</span>
                        <?php else: ?>
                            <span class="demo-tag demo-tag-primary">Унікальний</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="demo-code">
        findDuplicates([<?= htmlspecialchars(implode(', ', $arr)) ?>])<br>
        // Результат: [<?= implode(', ', $duplicates) ?>]
    </div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
renderVariantLayout($content, 'Завдання 6');