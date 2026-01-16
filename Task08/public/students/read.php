<?php
require_once __DIR__ . '/../../src/db.php';
$pdo = require __DIR__ . '/../../src/db.php';

$group = $_GET['group'] ?? null;
$groups = $pdo->query("SELECT number FROM groups ORDER BY number")->fetchAll(PDO::FETCH_COLUMN);

$sql = "
    SELECT s.id, s.full_name, s.gender, s.student_card_number, g.number AS group_number
    FROM students s
    JOIN groups g ON s.group_id = g.id
    WHERE 1=1
";
$params = [];
if ($group) {
    $sql .= " AND g.number = ?";
    $params[] = $group;
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
</head>
<body>
    <h1>Список студентов</h1>

    <form method="GET">
        <label>Группа:
            <select name="group">
                <option value="">Все</option>
                <?php foreach ($groups as $num): ?>
                    <option value="<?= htmlspecialchars($num) ?>" <?= $group === $num ? 'selected' : '' ?>>
                        <?= htmlspecialchars($num) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Показать</button>
    </form>

    <table border="1" style="margin: 1rem 0; width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Группа</th>
                <th>ФИО</th>
                <th>Пол</th>
                <th>Студ. билет</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['group_number']) ?></td>
                    <td><?= htmlspecialchars($s['full_name']) ?></td>
                    <td><?= htmlspecialchars($s['gender']) ?></td>
                    <td><?= htmlspecialchars($s['student_card_number']) ?></td>
                    <td>
                        <a href="../exams/read.php?student_id=<?= $s['id'] ?>">Результаты экзаменов</a> |
                        <a href="update.php?id=<?= $s['id'] ?>">Редактировать</a> |
                        <a href="delete.php?id=<?= $s['id'] ?>" onclick="return confirm('Удалить студента?')">Удалить</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p><a href="create.php">Добавить студента</a></p>
</body>
</html>