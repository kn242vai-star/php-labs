<?php
/**
 * Завдання 1: Форматований текст
 *
 * Вірш про художника з форматуванням: <b>, <i>, margin-left
 */
require_once __DIR__ . '/layout.php';

ob_start();
?>
<div class="poem">
    <?php
    echo "<p style='margin-left: 20px;'>Пензлик <b>художника</b> торкає полотно,</p>";
    echo "<p style='margin-left: 20px;'>Фарби <i>змішуються</i> у дивний танок,</p>";
    echo "<p style='margin-left: 20px;'>Картина оживає поволі й чудно,</p>";
    echo "<p style='margin-left: 20px;'>А муза шепоче свій тихий знак.</p>";
    ?>
</div>
<?php
$content = ob_get_clean();

renderVariantLayout($content, 'Завдання 1', 'task2-body');
