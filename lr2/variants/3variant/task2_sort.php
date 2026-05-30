<?php
/**
 * Завдання 2: Сортування міст
 * Працює з пробілами, комами та зберігає назву "Біла Церква"
 */
require_once __DIR__ . '/layout.php';

function sortCities(string $input): array
{
    $prepared = str_replace(['Біла Церква', 'Біла церква'], 'Біла_Церква', $input);
 
    $cities = preg_split('/[\s,]+/', $prepared, -1, PREG_SPLIT_NO_EMPTY);

    $cities = array_map(fn($c) => str_replace('_', ' ', $c), $cities);

    sort($cities, SORT_LOCALE_STRING);
    
    return $cities;
}

$defaultCities = 'Запоріжжя Дніпро Миколаїв Херсон Біла Церква Кременчук Маріуполь Мелітополь';
$input = $_POST['cities'] ?? $defaultCities;
$submitted = isset($_POST['cities']);

$rawInput = str_replace(['Біла Церква', 'Біла церква'], 'Біла_Церква', $input);
$inputArray = array_map(fn($c) => str_replace('_', ' ', $c), preg_split('/[\s,]+/', $rawInput, -1, PREG_SPLIT_NO_EMPTY));

$sorted = sortCities($input);

ob_start();
?>
<div class="demo-card">
    <h2>Сортування міст</h2>
    <p class="demo-subtitle">Введіть міста через пробіл або кому</p>

    <form method="post" class="demo-form">
        <div>
            <label for="cities">Міста</label>
            <input type="text" id="cities" name="cities" value="<?= htmlspecialchars($input) ?>">
        </div>
        <button type="submit" class="btn-submit">Сортувати</button>
    </form>

    <?php if (!empty($sorted)): ?>
    <div class="demo-section">
        <h3>Вхідні дані</h3>
        <div class="array-display">
            <?php foreach ($inputArray as $city): ?>
                <span class="array-item"><?= htmlspecialchars($city) ?></span>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="array-arrow">&#8595;</div>

    <div>
        <h3 class="demo-section-title-success">Відсортовані (А→Я)</h3>
        <div class="array-display">
            <?php foreach ($sorted as $city): ?>
                <span class="array-item array-item-unique"><?= htmlspecialchars($city) ?></span>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="demo-code">
        Біла Церква, Дніпро, Запоріжжя, Кременчук, Маріуполь, Мелітополь, Миколаїв, Херсон
    </div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
renderVariantLayout($content, 'Завдання 2');