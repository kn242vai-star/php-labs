<?php
/**
 * Завдання 6.2: 5 червоних квадратів на чорному тлі
 */
require_once __DIR__ . '/layout.php';

function generateSquares(int $n): string
{
    $html = "<div class='shapes-container' style='
        position: relative; 
        width: 100%; 
        height: 400px; 
        background: #000; 
        overflow: hidden; 
        display: block;
        margin: 20px 0;
        border-radius: 8px;
    '>";

    for ($i = 0; $i < $n; $i++) {
        $size = 60; 
       
        $row = intdiv($i, 3); 
        $col = $i % 3;        
        
        $top = 20 + ($row * 120);  
        $left = 20 + ($col * 120); 
        
        $opacity = mt_rand(70, 100) / 100;

        $html .= "<div style='
            position: absolute;
            top: {$top}px;
            left: {$left}px;
            width: {$size}px;
            height: {$size}px;
            background-color: #ef4444;
            opacity: {$opacity};
            border: 2px solid #fff;
            box-shadow: 0 0 15px rgba(239, 68, 68, 0.7);
            z-index: 10;
        '></div>";
    }

    $html .= "</div>";
    return $html;
}

$n = 5;
$squares = generateSquares($n);

$content = '
    <div style="padding: 20px;">
        <h1>🟥 Завдання 6.2: Квадрати</h1>
        ' . $squares . '
        <p style="color: #666;">Згенеровано 5 квадратів за допомогою циклу for</p>
    </div>';

renderVariantLayout($content, 'Завдання 6.2', 'task7-squares-body');
    