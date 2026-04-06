<?php
/**
 * Завдання 1: Створення класів та об'єктів
 *
 * Клас Doctor з властивостями: name, specialization, licenseNumber
 */
require_once __DIR__ . '/layout.php';

class Doctor
{
    public string $name;
    public string $specialization;
    public string $licenseNumber;
}

// Створюємо 3 об'єкти Doctor з наведеними значеннями
$doc1 = new Doctor();
$doc1->name = 'Андрій Кравченко';
$doc1->specialization = 'Кардіолог';
$doc1->licenseNumber = 'LIC-4521';

$doc2 = new Doctor();
$doc2->name = 'Людмила Савченко';
$doc2->specialization = 'Терапевт';
$doc2->licenseNumber = 'LIC-7834';

$doc3 = new Doctor();
$doc3->name = 'Максим Олійник';
$doc3->specialization = 'Хірург';
$doc3->licenseNumber = 'LIC-2190';

$doctors = [
    ['obj' => $doc1, 'avatar' => 'avatar-indigo', 'initial' => 'А'],
    ['obj' => $doc2, 'avatar' => 'avatar-green', 'initial' => 'Л'],
    ['obj' => $doc3, 'avatar' => 'avatar-amber', 'initial' => 'М'],
];

ob_start();
?>

<div class="task-header">
    <h1>Завдання 1 — Клас Doctor</h1>
    <p>Клас <code>Doctor</code> з властивостями: name, specialization, licenseNumber</p>
</div>

<div class="code-block"><span class="code-comment">// Приклад: створення об'єкта Doctor</span>
<span class="code-variable">$doc1</span> = <span class="code-keyword">new</span> <span class="code-class">Doctor</span>();
<span class="code-variable">$doc1</span><span class="code-arrow">-></span><span class="code-method">name</span> = <span class="code-string">'Андрій Кравченко'</span>;
<span class="code-variable">$doc1</span><span class="code-arrow">-></span><span class="code-method">specialization</span> = <span class="code-string">'Кардіолог'</span>;
<span class="code-variable">$doc1</span><span class="code-arrow">-></span><span class="code-method">licenseNumber</span> = <span class="code-string">'LIC-4521'</span>;</div>

<div class="section-divider">
    <span class="section-divider-text">3 об'єкти Doctor</span>
</div>

<div class="users-grid">
    <?php foreach ($doctors as $i => $data): ?>
    <div class="user-card">
        <div class="user-card-header">
            <div class="user-card-avatar <?= $data['avatar'] ?>"><?= $data['initial'] ?></div>
            <div>
                <div class="user-card-name"><?= htmlspecialchars($data['obj']->name) ?></div>
                <div class="user-card-label">Doctor #<?= $i + 1 ?></div>
            </div>
        </div>
        <div class="user-card-body">
            <div class="user-card-field">
                <span class="user-card-field-label">name</span>
                <span class="user-card-field-value"><?= htmlspecialchars($data['obj']->name) ?></span>
            </div>
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

<?php
$content = ob_get_clean();
renderDemoLayout($content, 'Завдання 1', 'task1-body');