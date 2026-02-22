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
    echo "<p class='poem-indent-1'>Осінній <b>дощ</b> стукає в шибку,</p>";
    echo "<p class='poem-indent-1'>Листя кружляє <i>повільно</i> в дворі,</p>";
    echo "<p class='poem-indent-1'>Каштани впали на стежку,</p>";
    echo "<p class='poem-indent-1'>Туман лягає на гори.</p>";
    ?>
</div>
<?php
$content = ob_get_clean();

renderVariantLayout($content, 'Завдання 1', 'task2-body');
