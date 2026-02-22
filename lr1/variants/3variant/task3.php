<?php
/**
 * Завдання 2: Конвертер валют (UAH → USD)
 * * Сума: 25000 грн
 * Курс: 1 долар = 42.10 грн
 */
require_once __DIR__ . '/layout.php';

function convertUahToUsd(float $uah, float $rate): float
{
    return round($uah / $rate, 2);
}

// Вхідні дані
$uah = 25000;
$rate = 42.10;

$usdAmount = convertUahToUsd($uah, $rate);

$content = '<div class="card">
    <h2>💵 Конвертер UAH → USD</h2>
    <p><strong>Курс:</strong> 1 USD = ' . $rate . ' грн</p>
    <div class="result">' . $uah . ' грн. можна обміняти на ' . $usdAmount . ' доларів</div>
    <hr>
    <p class="info"><strong>Логіка розрахунку:</strong></p>
    <p class="info">Сума (' . $uah . ') / Курс (' . $rate . ') = ' . $usdAmount . '</p>
</div>';

renderVariantLayout($content, 'Завдання 2', 'task2-body');