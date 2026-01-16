<?php
require_once __DIR__ . '/../../src/db.php';
$pdo = require __DIR__ . '/../../src/db.php';

if ($_POST) {
    $pdo->prepare("
        INSERT INTO students (full_name, gender, birth_date, student_card_number, group_id)
        VALUES (?, ?, ?, ?, (SELECT id FROM groups WHERE number = ?))
    ")->execute([
        $_POST['full_name'],
        $_POST['gender'],
        $_POST['birth_date'],
        $_POST['student_card_number'],
        $_POST['group_number']
    ]);
    header('Location: read.php');
    exit;
}

$groups = $pdo->query("SELECT number FROM groups ORDER BY number")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Добавить студента</title>
</head>
<body>
    <h1>Добавить студента</h1>
    <form method="POST">
        <p>
            <label>ФИО:<br>
                <input type="text" name="full_name" required>
            </label>
        </p>
        <p>
            <label>Пол:<br>
                <label><input type="radio" name="gender" value="М" required> М</label>
                <label><input type="radio" name="gender" value="Ж"> Ж</label>
            </label>
        </p>
        <p>
            <label>Дата рождения:<br>
                <input type="date" name="birth_date" required>
            </label>
        </p>
        <p>
            <label>Номер студ. билета:<br>
                <input type="text" name="student_card_number" required>
            </label>
        </p>
        <p>
            <label>Группа:<br>
                <select name="group_number" required>
                    <?php foreach ($groups as $num): ?>
                        <option value="<?= htmlspecialchars($num) ?>"><?= htmlspecialchars($num) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </p>
        <button type="submit">Сохранить</button>
        <a href="read.php">Отмена</a>
    </form>
</body>
</html>