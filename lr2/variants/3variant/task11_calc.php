<?php
/**
 * Завдання 11: Калькулятор — форма введення
 *
 * Варіант 3: X = 4, Y = 2
 */
require_once __DIR__ . '/layout.php';

ob_start();
?>
<div class="demo-card">
    <h2>Калькулятор</h2>
    <p class="demo-subtitle">sin(x), cos(x), tg(x), sin(x)/cos(x), x^y, x!</p>

    <form method="post" action="task11_result.php" class="demo-form">
        <div class="form-row">
            <div>
                <label for="x">Значення X</label>
                <input type="number" id="x" name="x" step="any" value="<?= htmlspecialchars($_GET['x'] ?? '4') ?>" placeholder="Введіть X" required>
            </div>
            <div>
                <label for="y">Значення Y</label>
                <input type="number" id="y" name="y" step="any" value="<?= htmlspecialchars($_GET['y'] ?? '2') ?>" placeholder="Введіть Y" required>
            </div>
        </div>
        <button type="submit" class="btn-submit">Обчислити</button>
    </form>

    <div class="demo-section">
        <h3>Доступні функції</h3>
        <table class="demo-table">
            <thead>
                <tr>
                    <th>Функція</th>
                    <th>Опис</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>sin(x)</code></td>
                    <td>Синус x (в радіанах)</td>
                </tr>
                <tr>
                    <td><code>cos(x)</code></td>
                    <td>Косинус x (в радіанах)</td>
                </tr>
                <tr>
                    <td><code>tg(x)</code></td>
                    <td>Тангенс x (вбудований tan)</td>
                </tr>
                <tr>
                    <td><code>my_tg(x)</code></td>
                    <td>Тангенс x через sin/cos</td>
                </tr>
                <tr>
                    <td><code>x^y</code></td>
                    <td>Піднесення x до степеня y</td>
                </tr>
                <tr>
                    <td><code>x!</code></td>
                    <td>Факторіал x (для цілих >= 0)</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="demo-code">// Function/func.php
function my_sin(float $x): float
{
    return sin($x);
}

function my_cos(float $x): float
{
    return cos($x);
}

function my_tg(float $x): string|float
{
    $cosX = cos($x);
    if (abs($cosX) < 1e-10) {
        return 'Не визначено (cos(x) = 0)';
    }
    return sin($x) / $cosX;
}

function my_pow(float $x, float $y): float
{
    return pow($x, $y);
}

function my_factorial(int $n): string|int
{
    if ($n < 0) {
        return 'Не визначено (x < 0)';
    }
    if ($n > 20) {
        return 'Занадто велике число (x > 20)';
    }
    if ($n <= 1) {
        return 1;
    }
    return $n * my_factorial($n - 1);
}
</div>
</div>
<?php
$content = ob_get_clean();
renderVariantLayout($content, 'Завдання 11');
