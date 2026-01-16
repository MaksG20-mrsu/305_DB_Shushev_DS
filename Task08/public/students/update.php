<?php
require_once __DIR__ . '/../../src/db.php';
$pdo = require __DIR__ . '/../../src/db.php';

$id = $_GET['id'] ?? null;
if (!$id) die('Нет id');

if ($_POST) {
    $stmt = $pdo->prepare("
        UPDATE students 
        SET full_name = ?, gender = ?, birth_date = ?, student_card_number = ?, 
            group_id = (SELECT id FROM groups WHERE number = ?)
        WHERE id = ?
    ");
    $stmt->execute([
        $_POST['full_name'],
        $_POST['gender'],
        $_POST['birth_date'],
        $_POST['student_card_number'],
        $_POST['group_number'],
        $id
    ]);
    header('Location: read.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    die('Студент не найден');
}

$stmt2 = $pdo->prepare("SELECT number FROM groups ORDER BY number");
$stmt2->execute();
$groups = $stmt2->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Редактировать студента</title>
</head>
<body>
    <h1>Редактировать студента</h1>
    <form method="POST">
        <p>
            <label>ФИО:<br>
                <input type="text" name="full_name" value="<?= htmlspecialchars($student['full_name']) ?>" required>
            </label>
        </p>
        <p>
            <label>Пол:<br>
                <label><input type="radio" name="gender" value="М" <?= $student['gender'] === 'М' ? 'checked' : '' ?> required> М</label>
                <label><input type="radio" name="gender" value="Ж" <?= $student['gender'] === 'Ж' ? 'checked' : '' ?>> Ж</label>
            </label>
        </p>
        <p>
            <label>Дата рождения:<br>
                <input type="date" name="birth_date" value="<?= $student['birth_date'] ?>" required>
            </label>
        </p>
        <p>
            <label>Номер студ. билета:<br>
                <input type="text" name="student_card_number" value="<?= htmlspecialchars($student['student_card_number']) ?>" required>
            </label>
        </p>
        <p>
            <label>Группа:<br>
                <select name="group_number" required>
                    <?php foreach ($groups as $num): ?>
                        <?php
                        $currentGroupId = $student['group_id'];
                        $stmt3 = $pdo->prepare("SELECT id FROM groups WHERE number = ?");
                        $stmt3->execute([$num]);
                        $group = $stmt3->fetch();
                        $selected = ($group && $currentGroupId == $group['id']) ? 'selected' : '';
                        ?>
                        <option value="<?= htmlspecialchars($num) ?>" <?= $selected ?>>
                            <?= htmlspecialchars($num) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
        </p>
        <button type="submit">Сохранить</button>
        <a href="read.php">Отмена</a>
    </form>
</body>
</html>