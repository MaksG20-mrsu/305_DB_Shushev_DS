<?php

require_once __DIR__ . '/db.php';

$pdo = require __DIR__ . '/db.php';

$currentYear = (int) date('Y');

$stmt = $pdo->prepare("SELECT DISTINCT number FROM groups WHERE graduation_year >= :currentYear ORDER BY number");
$stmt->execute(['currentYear' => $currentYear]);
$groupNumbers = $stmt->fetchAll(PDO::FETCH_COLUMN);

$selectedGroup = $_GET['group'] ?? null;
if ($selectedGroup !== null && !in_array($selectedGroup, $groupNumbers)) {
    http_response_code(400);
    die('Неверный номер группы.');
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

if ($selectedGroup !== null) {
    $sql .= " AND g.number = :groupNumber";
    $params['groupNumber'] = $selectedGroup;
}

$sql .= " ORDER BY g.number, s.full_name";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список студентов</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 1rem; }
        th, td { border: 1px solid #999; padding: 0.5rem; text-align: left; }
        th { background-color: #f0f0f0; }
        form { margin: 1rem 0; }
    </style>
</head>
<body>

<h1>Список студентов действующих групп</h1>

<form method="GET">
    <label for="group">Фильтр по группе:</label>
    <select name="group" id="group">
        <option value="">Все группы</option>
        <?php foreach ($groupNumbers as $num): ?>
            <option value="<?= htmlspecialchars($num) ?>" <?= $selectedGroup === $num ? 'selected' : '' ?>>
                <?= htmlspecialchars($num) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Показать</button>
</form>

<?php if (!empty($students)): ?>
    <table>
        <thead>
            <tr>
                <th>Группа</th>
                <th>Направление подготовки</th>
                <th>ФИО</th>
                <th>Пол</th>
                <th>Дата рождения</th>
                <th>Студ. билет</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['group_number']) ?></td>
                    <td><?= htmlspecialchars($s['program']) ?></td>
                    <td><?= htmlspecialchars($s['full_name']) ?></td>
                    <td><?= htmlspecialchars($s['gender']) ?></td>
                    <td><?= htmlspecialchars($s['birth_date']) ?></td>
                    <td><?= htmlspecialchars($s['student_card_number']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Нет студентов.</p>
<?php endif; ?>

</body>
</html>