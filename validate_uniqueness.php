<?php
/**
 * Variant Uniqueness Validator
 *
 * Validates that all vN.md files in a lab have unique data
 * and correct math (where applicable).
 *
 * Usage: php validate_uniqueness.php lr1
 *        php validate_uniqueness.php lr1 --verbose
 */

$lab = $argv[1] ?? null;
$verbose = in_array('--verbose', $argv ?? []);

if (!$lab) {
    echo "\033[1mUsage:\033[0m php validate_uniqueness.php <lab> [--verbose]\n";
    echo "  Example: php validate_uniqueness.php lr1\n";
    exit(1);
}

$labDir = __DIR__ . "/{$lab}/variants";
if (!is_dir($labDir)) {
    echo "\033[31mError:\033[0m Directory not found: {$labDir}\n";
    exit(1);
}

// Find all vN.md files
$files = glob("{$labDir}/v*.md");
usort($files, function ($a, $b) {
    preg_match('/v(\d+)\.md$/', $a, $ma);
    preg_match('/v(\d+)\.md$/', $b, $mb);
    return (int)($ma[1] ?? 0) - (int)($mb[1] ?? 0);
});

if (empty($files)) {
    echo "\033[31mError:\033[0m No variant files found in {$labDir}\n";
    exit(1);
}

echo "\033[1m=== Variant Uniqueness Validator ===\033[0m\n";
echo "Lab: \033[1m{$lab}\033[0m | Files: \033[1m" . count($files) . "\033[0m\n\n";

// Parse variants based on lab
$variants = [];
$errors = [];
$warnings = [];

foreach ($files as $file) {
    $basename = basename($file, '.md');
    $content = file_get_contents($file);
    $data = parseVariant($content, $lab);
    $data['file'] = $basename;
    $variants[$basename] = $data;
}

// Check uniqueness
checkUniqueness($variants, $errors, $warnings, $verbose);

// Verify math (LR1)
if ($lab === 'lr1') {
    verifyMath($variants, $errors);
}

// Report
echo "\033[1m--- Results ---\033[0m\n";

if (empty($errors) && empty($warnings)) {
    echo "\033[32m✓ All " . count($files) . " variants pass uniqueness and math checks\033[0m\n";
} else {
    foreach ($warnings as $w) {
        echo "\033[33m⚠ {$w}\033[0m\n";
    }
    foreach ($errors as $e) {
        echo "\033[31m✗ {$e}\033[0m\n";
    }
    echo "\n";
    $ec = count($errors);
    $wc = count($warnings);
    if ($ec > 0) {
        echo "\033[31m{$ec} error(s)\033[0m";
    }
    if ($wc > 0) {
        echo ($ec > 0 ? ", " : "") . "\033[33m{$wc} warning(s)\033[0m";
    }
    echo "\n";
}

exit(empty($errors) ? 0 : 1);

// ===== Functions =====

function parseVariant(string $content, string $lab): array
{
    $data = [];

    switch ($lab) {
        case 'lr1':
            $data = parseLR1($content);
            break;
        default:
            // Generic: extract all numbers and key text blocks
            $data = parseGeneric($content);
            break;
    }

    return $data;
}

function parseLR1(string $content): array
{
    $data = [];

    // Currency amount: "Сума: 1500 грн"
    if (preg_match('/Сума:\s*([\d]+)\s*грн/u', $content, $m)) {
        $data['amount'] = (int)$m[1];
    }

    // Exchange rate: "Курс: 1 долар = 41.25 грн"
    if (preg_match('/Курс:\s*1 долар\s*=\s*([\d.]+)\s*грн/u', $content, $m)) {
        $data['rate'] = (float)$m[1];
    }

    // Expected conversion result
    if (preg_match('/обміняти на\s*([\d.]+)\s*долар/u', $content, $m)) {
        $data['conversion_result'] = (float)$m[1];
    }

    // Month: "Місяць для перевірки: 3"
    if (preg_match('/Місяць для перевірки:\s*(\d+)/u', $content, $m)) {
        $data['month'] = (int)$m[1];
    }

    // Letter: "Символ для перевірки: 'а'"
    if (preg_match("/Символ для перевірки:\s*'(.+?)'/u", $content, $m)) {
        $data['letter'] = $m[1];
    }

    // 3-digit number: "Число: 427"
    if (preg_match('/Число:\s*(\d{3})/u', $content, $m)) {
        $data['number'] = (int)$m[1];
    }

    // Digit sum: "Сума цифр: 13"
    if (preg_match('/Сума цифр:\s*(\d+)/u', $content, $m)) {
        $data['digit_sum'] = (int)$m[1];
    }

    // Reversed number: "Зворотне число: 724"
    if (preg_match('/Зворотне число:\s*(\d+)/u', $content, $m)) {
        $data['reversed'] = (int)$m[1];
    }

    // Max number: "Найбільше число: 742"
    if (preg_match('/Найбільше число:\s*(\d+)/u', $content, $m)) {
        $data['max_number'] = (int)$m[1];
    }

    // Table dimensions: "Таблиця: 4 x 5"
    if (preg_match('/Таблиця:\s*(\d+)\s*x\s*(\d+)/u', $content, $m)) {
        $data['table'] = $m[1] . 'x' . $m[2];
    }

    // Squares count: "Квадрати: 6"
    if (preg_match('/Квадрат[иів]*:\s*(\d+)/u', $content, $m)) {
        $data['squares'] = (int)$m[1];
    }

    // Poem (first line after ```)
    if (preg_match('/```\s*\n(.+?)\n/u', $content, $m)) {
        $data['poem_first_line'] = trim($m[1]);
    }

    return $data;
}

function parseGeneric(string $content): array
{
    $data = [];

    // Extract all numbers
    preg_match_all('/\b\d+(?:\.\d+)?\b/', $content, $matches);
    $data['all_numbers'] = $matches[0] ?? [];

    // Extract text between ``` blocks
    preg_match_all('/```\s*\n(.+?)\n```/su', $content, $matches);
    $data['code_blocks'] = $matches[1] ?? [];

    return $data;
}

function checkUniqueness(array $variants, array &$errors, array &$warnings, bool $verbose): void
{
    // Collect all field values
    $fields = [];
    foreach ($variants as $name => $data) {
        foreach ($data as $key => $value) {
            if ($key === 'file' || $key === 'all_numbers' || $key === 'code_blocks') {
                continue;
            }
            $fields[$key][$name] = $value;
        }
    }

    echo "\033[1mUniqueness check:\033[0m\n";

    foreach ($fields as $field => $values) {
        // Find duplicates
        $seen = [];
        $duplicates = [];
        foreach ($values as $variant => $value) {
            $key = is_float($value) ? (string)$value : (string)$value;
            if (isset($seen[$key])) {
                $duplicates[$key][] = $variant;
                if (!isset($duplicates[$key]) || count($duplicates[$key]) === 1) {
                    $duplicates[$key][] = $seen[$key];
                }
            }
            $seen[$key] = $variant;
        }

        // Rebuild duplicates properly
        $dupsClean = [];
        foreach ($values as $variant => $value) {
            $key = (string)$value;
            $dupsClean[$key][] = $variant;
        }
        $dupsClean = array_filter($dupsClean, fn($v) => count($v) > 1);

        $uniqueCount = count(array_unique(array_map('strval', $values)));
        $totalCount = count($values);

        if (empty($dupsClean)) {
            echo "  \033[32m✓\033[0m {$field}: {$uniqueCount}/{$totalCount} unique\n";
            if ($verbose) {
                $sortedValues = $values;
                asort($sortedValues);
                foreach ($sortedValues as $v => $val) {
                    echo "    {$v}: {$val}\n";
                }
            }
        } else {
            // Fields that are allowed to repeat (derived or limited range)
            $warnFields = ['month', 'digit_sum', 'reversed', 'max_number', 'squares'];
            if (in_array($field, $warnFields)) {
                $reason = match ($field) {
                    'month' => 'only 12 months',
                    'digit_sum', 'reversed', 'max_number' => 'derived from number',
                    'squares' => 'limited range',
                };
                $warnings[] = "{$field}: {$uniqueCount}/{$totalCount} unique ({$reason})";
                echo "  \033[33m⚠\033[0m {$field}: {$uniqueCount}/{$totalCount} unique (OK — {$reason})\n";
            } else {
                $errors[] = "{$field}: duplicates found ({$uniqueCount}/{$totalCount} unique)";
                echo "  \033[31m✗\033[0m {$field}: {$uniqueCount}/{$totalCount} unique — DUPLICATES:\n";
                foreach ($dupsClean as $val => $vars) {
                    echo "    \033[31m'{$val}' in: " . implode(', ', $vars) . "\033[0m\n";
                }
            }
        }
    }
    echo "\n";
}

function verifyMath(array $variants, array &$errors): void
{
    echo "\033[1mMath verification (LR1):\033[0m\n";

    foreach ($variants as $name => $data) {
        $issues = [];

        // Currency conversion: amount / rate = result (rounded to 2 decimals)
        if (isset($data['amount'], $data['rate'], $data['conversion_result'])) {
            $expected = round($data['amount'] / $data['rate'], 2);
            if (abs($expected - $data['conversion_result']) > 0.01) {
                $issues[] = "currency: {$data['amount']}/{$data['rate']} = {$expected}, file says {$data['conversion_result']}";
            }
        }

        // 3-digit number operations
        if (isset($data['number'])) {
            $num = $data['number'];
            $d1 = intdiv($num, 100);
            $d2 = intdiv($num, 10) % 10;
            $d3 = $num % 10;

            // Digit sum
            $expectedSum = $d1 + $d2 + $d3;
            if (isset($data['digit_sum']) && $data['digit_sum'] !== $expectedSum) {
                $issues[] = "digit_sum: {$d1}+{$d2}+{$d3} = {$expectedSum}, file says {$data['digit_sum']}";
            }

            // Reversed
            $expectedReversed = $d3 * 100 + $d2 * 10 + $d1;
            if (isset($data['reversed']) && $data['reversed'] !== $expectedReversed) {
                $issues[] = "reversed: expected {$expectedReversed}, file says {$data['reversed']}";
            }

            // Max arrangement
            $digits = [$d1, $d2, $d3];
            rsort($digits);
            $expectedMax = $digits[0] * 100 + $digits[1] * 10 + $digits[2];
            if (isset($data['max_number']) && $data['max_number'] !== $expectedMax) {
                $issues[] = "max_number: expected {$expectedMax}, file says {$data['max_number']}";
            }
        }

        if (empty($issues)) {
            if ($name === 'v1' || $name === 'v15' || $name === 'v30') {
                echo "  \033[32m✓\033[0m {$name}: all math correct\n";
            }
        } else {
            foreach ($issues as $issue) {
                $errors[] = "{$name}: {$issue}";
                echo "  \033[31m✗\033[0m {$name}: {$issue}\n";
            }
        }
    }

    // Summary line
    $totalVariants = count($variants);
    $errorVariants = 0;
    foreach ($variants as $name => $data) {
        // Re-check quickly
        if (isset($data['amount'], $data['rate'], $data['conversion_result'])) {
            $expected = round($data['amount'] / $data['rate'], 2);
            if (abs($expected - $data['conversion_result']) > 0.01) {
                $errorVariants++;
                continue;
            }
        }
        if (isset($data['number'], $data['digit_sum'])) {
            $num = $data['number'];
            $expectedSum = intdiv($num, 100) + intdiv($num, 10) % 10 + $num % 10;
            if ($data['digit_sum'] !== $expectedSum) {
                $errorVariants++;
                continue;
            }
        }
    }
    $passedVariants = $totalVariants - $errorVariants;
    echo "  \033[32m✓\033[0m Math: {$passedVariants}/{$totalVariants} variants correct\n";
    echo "\n";
}
