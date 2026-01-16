<?php
require_once __DIR__ . '/db.php';

$pdo = require __DIR__ . '/db.php';

$currentYear = (int) date('Y');

$stmt = $pdo->prepare("
    SELECT DISTINCT number FROM groups 
    WHERE graduation_year >= :currentYear 
    ORDER BY number
");
$stmt->execute(['currentYear' => $currentYear]);
$groupNumbers = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($groupNumbers)) {
    echo "Нет действующих групп.\n";
    exit;
}

echo "Доступные номера групп:\n";
foreach ($groupNumbers as $num) {
    echo "- $num\n";
}
echo "\nВведите номер группы (или нажмите Enter для всех): ";

$handle = fopen("php://stdin", "r");
$input = trim(fgets($handle));
fclose($handle);

if ($input !== '') {
    if (!in_array($input, $groupNumbers)) {
        echo "Ошибка: группы с таким номером не существует.\n";
        exit(1);
    }
}

$sql = "
    SELECT 
        g.number AS group_number,
        g.program,
        s.full_name,
        s.gender,
        s.birth_date,
        s.student_card_number
    FROM students s
    JOIN groups g ON s.group_id = g.id
    WHERE g.graduation_year >= :currentYear
";

$params = ['currentYear' => $currentYear];

if ($input !== '') {
    $sql .= " AND g.number = :groupNumber";
    $params['groupNumber'] = $input;
}

$sql .= " ORDER BY g.number, s.full_name";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();

if (empty($students)) {
    echo "Нет студентов.\n";
    exit;
}

if (!function_exists('mb_str_pad')) {
    function mb_str_pad($str, $pad_len, $pad_str = ' ', $pad_type = STR_PAD_RIGHT, $encoding = 'UTF-8') {
        $str_len = mb_strlen($str, $encoding);
        $pad_str_len = mb_strlen($pad_str, $encoding);

        if (!$str_len && ($pad_type == STR_PAD_RIGHT || $pad_type == STR_PAD_LEFT)) {
            $str_len = 1;
        }

        if ($pad_len <= $str_len) {
            return $str;
        }

        $result = '';
        switch ($pad_type) {
            case STR_PAD_RIGHT:
                $result = $str . str_repeat($pad_str, ceil(($pad_len - $str_len) / $pad_str_len));
                break;
            case STR_PAD_LEFT:
                $result = str_repeat($pad_str, ceil(($pad_len - $str_len) / $pad_str_len)) . $str;
                break;
            case STR_PAD_BOTH:
                $left = floor(($pad_len - $str_len) / 2);
                $right = ceil(($pad_len - $str_len) / 2);
                $result = str_repeat($pad_str, $left) . $str . str_repeat($pad_str, $right);
                break;
        }

        return mb_substr($result, 0, $pad_len, $encoding);
    }
}

function drawTable($data) {
    if (empty($data)) {
        echo "Нет данных для отображения.\n";
        return;
    }

    $headers = ['Группа', 'Напр. подготовки', 'ФИО', 'Пол', 'Дата рожд.', 'Студ. билет'];
    $columnCount = count($headers);

    $widths = [];
    for ($i = 0; $i < $columnCount; $i++) {
        $max = mb_strlen($headers[$i], 'UTF-8');
        foreach ($data as $row) {
            $val = '';
            switch ($i) {
                case 0: $val = $row['group_number']; break;
                case 1: $val = $row['program']; break;
                case 2: $val = $row['full_name']; break;
                case 3: $val = $row['gender']; break;
                case 4: $val = $row['birth_date']; break;
                case 5: $val = $row['student_card_number']; break;
            }
            $max = max($max, mb_strlen($val, 'UTF-8'));
        }
        $widths[$i] = min($max, 40);
    }

    $widths[1] = max($widths[1], 22);

    $top = '┌' . implode('┬', array_map(fn($w) => str_repeat('─', $w + 2), $widths)) . '┐';
    $mid = '├' . implode('┼', array_map(fn($w) => str_repeat('─', $w + 2), $widths)) . '┤';
    $bot = '└' . implode('┴', array_map(fn($w) => str_repeat('─', $w + 2), $widths)) . '┘';

    echo $top . "\n";

    echo '│';
    for ($i = 0; $i < $columnCount; $i++) {
        $cell = mb_str_pad($headers[$i], $widths[$i], ' ', STR_PAD_BOTH, 'UTF-8');
        echo " $cell │";
    }
    echo "\n$mid\n";

    foreach ($data as $row) {
        echo '│';
        for ($i = 0; $i < $columnCount; $i++) {
            $val = '';
            switch ($i) {
                case 0: $val = $row['group_number']; break;
                case 1: $val = $row['program']; break;
                case 2: $val = $row['full_name']; break;
                case 3: $val = $row['gender']; break;
                case 4: $val = $row['birth_date']; break;
                case 5: $val = $row['student_card_number']; break;
            }

            if (mb_strlen($val, 'UTF-8') > $widths[$i]) {
                $val = mb_substr($val, 0, $widths[$i] - 3, 'UTF-8') . '...';
            }

            $cell = mb_str_pad($val, $widths[$i], ' ', STR_PAD_RIGHT, 'UTF-8');
            echo " $cell │";
        }
        echo "\n";
    }

    echo $bot . "\n";
}

drawTable($students);