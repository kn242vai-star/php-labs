<?php
/**
 * Завдання 4: Клонування об'єктів
 * Демонстрація: __clone() задає значення за замовчанням при копіюванні об'єкта Doctor
 */

require_once __DIR__ . '/layout.php';

class Doctor
{
    public string $name;
    public string $specialization;
    public string $licenseNumber;

    public function __construct(string $name, string $specialization, string $licenseNumber)
    {
        $this->name = $name;
        $this->specialization = $specialization;
        $this->licenseNumber = $licenseNumber;
    }

    /**
     * Метод __clone() — викликається автоматично при використанні ключового слова clone
     */
    public function __clone(): void
    {
        $this->name = "Лікар";
        $this->specialization = "";
        $this->licenseNumber = "";
    }

    public function getInfo(): string
    {
        return "Лікар: {$this->name}, Спеціалізація: {$this->specialization}, Ліцензія: {$this->licenseNumber}";
    }
}

// Оригінальний об'єкт (через конструктор)
$doctor3 = new Doctor('Максим Олійник', 'Хірург', 'LIC-2190');

// Клонуємо — __clone() автоматично змінить дані в $doctor4
$doctor4 = clone $doctor3;

ob_start();
?>

<div class="task-header">
    <h1>Завдання 4 — Клонування</h1>
    <p>Використання <code>__clone()</code> для встановлення значень за замовчанням</p>
</div>

<div class="code-block">
<span class="code-comment">// Метод __clone() у класі Doctor</span>
<span class="code-keyword">public function</span> <span class="code-method">__clone</span>(): <span class="code-class">void</span>
{
    <span class="code-variable">$this</span><span class="code-arrow">-></span><span class="code-method">name</span> = <span class="code-string">"Лікар"</span>;
    <span class="code-variable">$this</span><span class="code-arrow">-></span><span class="code-method">specialization</span> = <span class="code-string">""</span>;
    <span class="code-variable">$this</span><span class="code-arrow">-></span><span class="code-method">licenseNumber</span> = <span class="code-string">""</span>;
}

<span class="code-comment">// Створення клону</span>
<span class="code-variable">$doctor4</span> = <span class="code-keyword">clone</span> <span class="code-variable">$doctor3</span>;
</div>

<div class="section-divider">
    <span class="section-divider-text">Порівняння: Оригінал vs Клон</span>
</div>

<div class="comparison-wrapper">
    <div class="users-grid">
        <div class="user-card">
            <div class="user-card-header">
                <div class="user-card-avatar avatar-amber">М</div>
                <div>
                    <div class="user-card-name"><?= htmlspecialchars($doctor3->name) ?></div>
                    <div class="user-card-label">$doctor3 <span class="user-card-badge badge-constructor">original</span></div>
                </div>
            </div>
            <div class="user-card-body">
                <div class="user-card-field">
                    <span class="user-card-field-label">specialization</span>
                    <span class="user-card-field-value"><?= htmlspecialchars($doctor3->specialization) ?></span>
                </div>
                <div class="user-card-field">
                    <span class="user-card-field-label">licenseNumber</span>
                    <span class="user-card-field-value"><?= htmlspecialchars($doctor3->licenseNumber) ?></span>
                </div>
            </div>
        </div>

        <div class="user-card">
            <div class="user-card-header">
                <div class="user-card-avatar avatar-rose">Л</div>
                <div>
                    <div class="user-card-name"><?= htmlspecialchars($doctor4->name) ?></div>
                    <div class="user-card-label">$doctor4 <span class="user-card-badge badge-clone">clone</span></div>
                </div>
            </div>
            <div class="user-card-body">
                <div class="user-card-field">
                    <span class="user-card-field-label">specialization</span>
                    <span class="user-card-field-value"><em>порожньо</em></span>
                </div>
                <div class="user-card-field">
                    <span class="user-card-field-label">licenseNumber</span>
                    <span class="user-card-field-value"><em>порожньо</em></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="section-divider">
    <span class="section-divider-text">Результат getInfo()</span>
</div>

<div class="info-output">
    <div class="info-output-header">Виклик методу для обох об'єктів:</div>
    <div class="info-output-body">
        <div class="info-output-row">
            <span class="info-output-label">$doctor3:</span>
            <span class="info-output-text"><?= htmlspecialchars($doctor3->getInfo()) ?></span>
        </div>
        <div class="info-output-row">
            <span class="info-output-label">$doctor4:</span>
            <span class="info-output-text"><?= htmlspecialchars($doctor4->getInfo()) ?></span>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
renderDemoLayout($content, 'Завдання 4', 'task4-body');