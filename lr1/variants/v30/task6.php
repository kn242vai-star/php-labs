<?php
/**
 * Завдання 5: Тризначне число
 *
 * Число 581: сума цифр=14, зворотне=185, паліндром=ні
 * Варіант 30 (Sub3): перевірка на паліндром замість найбільшого числа
 */
require_once __DIR__ . '/layout.php';

/**
 * Обчислює суму цифр числа
 */
function sumOfDigits(int $number): int
{
    $d1 = (int) floor($number / 100);
    $d2 = (int) floor(($number % 100) / 10);
    $d3 = $number % 10;
    return $d1 + $d2 + $d3;
}

/**
 * Повертає число в зворотному порядку
 */
function reverseNumber(int $number): int
{
    $d1 = (int) floor($number / 100);
    $d2 = (int) floor(($number % 100) / 10);
    $d3 = $number % 10;
    return $d3 * 100 + $d2 * 10 + $d1;
}

/**
 * Перевіряє чи є число паліндромом
 */
function isPalindrome(int $number): bool
{
    return $number === reverseNumber($number);
}

// Вхідні дані (варіант 30)
$number = 581;

// Розбиваємо число на цифри
$d1 = (int)($number / 100);
$d2 = (int)(($number % 100) / 10);
$d3 = $number % 10;

// Обчислення
$sum = sumOfDigits($number);
$reversed = reverseNumber($number);
$palindrome = isPalindrome($number);
$palindromeText = $palindrome ? "так" : "ні";
$palindromeColor = $palindrome ? "#10b981" : "#ef4444";

$content = '<div class="task6-container">
    <div class="card">
        <h3>🔢 Тризначне число</h3>
        <div class="number-display">' . $number . '</div>
        <div class="digits-row">
            <div class="digit-box">' . $d1 . '</div>
            <div class="digit-box">' . $d2 . '</div>
            <div class="digit-box">' . $d3 . '</div>
        </div>
    </div>

    <div class="card" style="margin-top:20px;">
        <h3>📊 Результати</h3>
        <div class="result-row">
            <div>
                <span>1. Сума цифр</span>
                <div class="func">sumOfDigits(' . $number . ')</div>
            </div>
            <span class="result-value">' . $sum . '</span>
        </div>
        <div class="result-row">
            <div>
                <span>2. Зворотне число</span>
                <div class="func">reverseNumber(' . $number . ')</div>
            </div>
            <span class="result-value">' . $reversed . '</span>
        </div>
        <div class="result-row">
            <div>
                <span>3. Паліндром?</span>
                <div class="func">isPalindrome(' . $number . ')</div>
            </div>
            <span class="result-value" style="color:' . $palindromeColor . '">' . $palindromeText . '</span>
        </div>
    </div>
</div>';

renderVariantLayout($content, 'Завдання 5', 'task6-body');
