<?php
/**
 * Завдання 2: Метод getInfo()
 * Демонстрація: метод об'єкта Doctor, що повертає форматовану інформацію
 */

require_once __DIR__ . '/layout.php';

// Оголошуємо клас Doctor безпосередньо або переконайтеся, що він у Doctor.php
class Doctor
{
    public string $name;
    public string $specialization;
    public string $licenseNumber;

    /**
     * Повертає рядок з інформацією про лікаря
     */
    public function getInfo(): string
    {
        return "Лікар: {$this->name}, Спеціалізація: {$this->specialization}, Ліцензія: {$this->licenseNumber}";
    }
}

// Створюємо 3 об'єкти з даними з завдання
$doctor1 = new Doctor();
$doctor1->name = 'Андрій Кравченко';
$doctor1->specialization = 'Кардіолог';
$doctor1->licenseNumber = 'LIC-4521';

$doctor2 = new Doctor();
$doctor2->name = 'Людмила Савченко';
$doctor2->specialization = 'Терапевт';
$doctor2->licenseNumber = 'LIC-7834';

$doctor3 = new Doctor();
$doctor3->name = 'Максим Олійник';
$doctor3->specialization = 'Хірург';
$doctor3->licenseNumber = 'LIC-2190';

$doctors = [$doctor1, $doctor2, $doctor3];
$labels = ['$doctor1', '$doctor2', '$doctor3'];

ob_start();
?>

<div class="task-header">
    <h1>Завдання 2 — Метод getInfo()</h1>
    <p>Вивід інформації про об'єкти класу <code>Doctor</code> через метод</p>
</div>

<div class="code-block">
<span class="code-comment">// Реалізація методу в класі Doctor</span>
<span class="code-keyword">public function</span> <span class="code-method">getInfo</span>(): <span class="code-class">string</span>
{
    <span class="code-keyword">return</span> <span class="code-string">"Лікар: {$this->name}, Спеціалізація: {$this->specialization}, Ліцензія: {$this->licenseNumber}"</span>;
}

<span class="code-comment">// Виклик методу:</span>
echo <span class="code-variable">$doctor1</span><span class="code-arrow">-></span><span class="code-method">getInfo</span>();
</div>

<div class="section-divider">
    <span class="section-divider-text">Результат виконання getInfo()</span>
</div>

<div class="info-output">
    <div class="info-output-header">Очікуваний результат виводу:</div>
    <div class="info-output-body">
        <?php foreach ($doctors as $i => $doc): ?>
        <div class="info-output-row">
            <span class="info-output-label"><?= $labels[$i] ?>:</span>
            <span class="info-output-text"><?= htmlspecialchars($doc->getInfo()) ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="section-divider">
    <span class="section-divider-text">Візуалізація об'єктів</span>
</div>

<div class="users-grid">
    <?php
    $avatars = ['avatar-indigo', 'avatar-green', 'avatar-amber'];
    $initials = ['А', 'Л', 'М'];
    foreach ($doctors as $i => $doc):
    ?>
    <div class="user-card">
        <div class="user-card-header">
            <div class="user-card-avatar <?= $avatars[$i] ?>"><?= $initials[$i] ?></div>
            <div>
                <div class="user-card-name"><?= htmlspecialchars($doc->name) ?></div>
                <div class="user-card-label"><?= $labels[$i] ?></div>
            </div>
        </div>
        <div class="user-card-body">
            <div class="user-card-field">
                <span class="user-card-field-label">specialization</span>
                <span class="user-card-field-value"><?= htmlspecialchars($doc->specialization) ?></span>
            </div>
            <div class="user-card-field">
                <span class="user-card-field-label">licenseNumber</span>
                <span class="user-card-field-value"><?= htmlspecialchars($doc->licenseNumber) ?></span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php
$content = ob_get_clean();
renderDemoLayout($content, 'Завдання 2', 'task2-body');