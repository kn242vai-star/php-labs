<?php
/**
 * Завдання 6.1: Кольорова таблиця 3x6
 */
require_once __DIR__ . '/layout.php';

function generateMultiColorTable(int $rows, int $cols, string $color1, string $color2): string
{
    $html = "<table class='chessboard'>";
    for ($i = 0; $i < $rows; $i++) {
        $html .= "<tr>";
        for ($j = 0; $j < $cols; $j++) {
            $bgColor = (($i + $j) % 2 === 0) ? $color1 : $color2;
            
            $html .= "<td style='background-color:{$bgColor}; border: 1px solid #ddd; width: 40px; height: 40px;'></td>";
        }
        $html .= "</tr>";
    }
    $html .= "</table>";
    return $html;
}

$rows = 3;
$cols = 6;
$color1 = '#6366f1'; 
$color2 = '#f43f5e'; 

$table = generateMultiColorTable($rows, $cols, $color1, $color2);

$content = '
    <h1>🎨 Кольорова таблиця ' . $rows . 'x' . $cols . '</h1>
    <div class="params">generateStripedTable(' . $rows . ', ' . $cols . ')</div>
    ' . $table;

renderVariantLayout($content, 'Завдання 6.1', 'task7-table-body');