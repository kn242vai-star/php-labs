<?php
/**
 * Завдання 3: Конструктор
 * Демонстрація: конструктор задає початкові значення name, specialization, licenseNumber
 */

require_once __DIR__ . '/layout.php';

class Doctor
{
    public string $name;
    public string $specialization;
    public string $licenseNumber;

    /**
     * Конструктор класу Doctor
     */
    public function __construct(string $name, string $specialization, string $licenseNumber)
    {
        $this->name = $name;
        $this->specialization = $specialization;
        $this->licenseNumber = $licenseNumber;
    }

    /**
     * Метод getInfo() з попереднього завдання
     */
    public function getInfo(): string
    {
        return "Лікар: {$this->name}, Спеціалізація: {$this->specialization}, Ліцензія: {$this->licenseNumber}";
    }
}

// Створюємо 3 об'єкти через конструктор (ті самі дані)
$doc1 = new Doctor('Андрій Кравченко', 'Кардіолог', 'LIC-4521');
$doc2 = new Doctor('Людмила Савченко', 'Терапевт', 'LIC-7834');
$doc3 = new Doctor('Максим Олійник', 'Хірург', 'LIC-2190');

$doctors = [
    ['obj' => $doc1, 'avatar' => 'avatar-indigo', 'initial' => 'А', 'var' => '$doctor1'],
    ['obj' => $doc2, 'avatar' => 'avatar-green', 'initial' => 'Л', 'var' => '$doctor2'],
    ['obj' => $doc3, 'avatar' => 'avatar-amber', 'initial' => 'М', 'var' => '$doctor3'],
];

ob_start();
?>

<div class="task-header">
    <h1>Завдання 3 — Конструктор</h1>
    <p>Використання методу <code>__construct</code> для ініціалізації властивостей</p>
</div>

<div class="code-block">
<span class="code-comment">// Конструктор класу Doctor</span>
<span class="code-keyword">public function</span> <span class="code-method">__construct</span>(<span class="code-variable">$name</span>, <span class="code-variable">$spec</span>, <span class="code-variable">$lic</span>)
{
    <span class="code-variable">$this</span><span class="code-arrow">-></span><span class="code-method">name</span> = <span class="code-variable">$name</span>;
    <span class="code-variable">$this</span><span class="code-arrow">-></span><span class="code-method">specialization</span> = <span class="code-variable">$spec</span>;
    <span class="code-variable">$this</span><span class="code-arrow">-></span><span class="code-method">licenseNumber</span> = <span class="code-variable">$lic</span>;
}

<span class="code-comment">// Створення об'єктів одним рядком:</span>
<span class="code-variable">$doc1</span> = <span class="code-keyword">new</span> <span class="code-class">Doctor</span>(<span class="code-string">'Андрій Кравченко'</span>, <span class="code-string">'Кардіолог'</span>, <span class="code-string">'LIC-4521'</span>);
</div>

<div class="section-divider">
    <span class="section-divider-text">Об'єкти, ініціалізовані через конструктор</span>
</div>

<div class="users-grid">
    <?php foreach ($doctors as $data): ?>
    <div class="user-card">
        <div class="user-card-header">
            <div class="user-card-avatar <?= $data['avatar'] ?>"><?= $data['initial'] ?></div>
            <div>
                <div class="user-card-name"><?= htmlspecialchars($data['obj']->name) ?></div>
                <div class="user-card-label"><?= $data['var'] ?> <span class="user-card-badge badge-constructor">constructor</span></div>
            </div>
        </div>
        <div class="user-card-body">
            <div class="user-card-field">
                <span class="user-card-field-label">specialization</span>
                <span class="user-card-field-value"><?= htmlspecialchars($data['obj']->specialization) ?></span>
            </div>
            <div class="user-card-field">
                <span class="user-card-field-label">licenseNumber</span>
                <span class="user-card-field-value"><?= htmlspecialchars($data['obj']->licenseNumber) ?></span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="section-divider">
    <span class="section-divider-text">Результат getInfo()</span>
</div>

<div class="info-output">
    <div class="info-output-body">
        <?php foreach ($doctors as $data): ?>
        <div class="info-output-row">
            <span class="info-output-label"><?= $data['var'] ?>:</span>
            <span class="info-output-text"><?= htmlspecialchars($data['obj']->getInfo()) ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
renderDemoLayout($content, 'Завдання 3', 'task3-body');