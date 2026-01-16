<?php
require_once __DIR__ . '/../../src/db.php';
$pdo = require __DIR__ . '/../../src/db.php';

$id = $_GET['id'] ?? null;
if ($id) {
    $pdo->prepare("DELETE FROM students WHERE id = ?")->execute([$id]);
}
header('Location: read.php');
exit;