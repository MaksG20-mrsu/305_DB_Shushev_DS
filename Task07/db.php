<?php
$databaseFile = __DIR__ . '/university.db';
$pdo = new PDO('sqlite:' . $databaseFile, null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
$pdo->exec('PRAGMA foreign_keys = ON;');
$exists = $pdo->query("SELECT 1 FROM sqlite_master WHERE type='table' AND name='groups'")->fetch();
if (!$exists) {
    $sql = "
        PRAGMA foreign_keys = ON;

        DROP TABLE IF EXISTS students;
        DROP TABLE IF EXISTS groups;

        CREATE TABLE groups (
            id INTEGER PRIMARY KEY,
            number TEXT NOT NULL,
            program TEXT NOT NULL,
            graduation_year INTEGER NOT NULL
        );

        CREATE TABLE students (
            id INTEGER PRIMARY KEY,
            full_name TEXT NOT NULL,
            gender TEXT NOT NULL CHECK (gender IN ('М', 'Ж')),
            birth_date TEXT NOT NULL,
            student_card_number TEXT NOT NULL,
            group_id INTEGER NOT NULL,
            FOREIGN KEY (group_id) REFERENCES groups(id)
        );

        INSERT INTO groups (number, program, graduation_year) VALUES
        ('1', 'Программная инженерия', 2025),
        ('2', 'Программная инженерия', 2026);

        INSERT INTO students (full_name, gender, birth_date, student_card_number, group_id) VALUES
        ('Зубков Роман Сергеевич', 'М', '2005-09-20', '31', 1),
        ('Иванов Максим Александрович', 'М', '2005-11-09', '12', 1),
        ('Ивенин Артём Андреевич', 'М', '2005-12-25', '67', 1),
        ('Казейкин Иван Иванович', 'М', '2006-03-12', '90', 2),
        ('Колыганов Александр Павлович', 'М', '2005-04-18', '34', 2),
        ('Кочнев Артем Алексеевич', 'М', '2005-05-29', '89', 1),
        ('Логунов Илья Сергеевич', 'М', '2005-02-01', '56', 1),
        ('Макарова Юлия Сергеевна', 'Ж', '2005-05-07', '23', 1),
        ('Маклаков Сергей Александрович', 'М', '2005-03-13', '11', 2),
        ('Маскинскова Наталья Сергеевна', 'Ж', '2005-10-28', '61', 1),
        ('Мукасеев Дмитрий Александрович', 'М', '2005-11-19', '55', 1),
        ('Наумкин Владислав Валерьевич', 'М', '2005-12-12', '67', 1),
        ('Паркаев Василий Александрович', 'М', '2005-10-16', '19', 2),
        ('Полковников Дмитрий Александрович', 'М', '2006-01-10', '27', 2),
        ('Пузаков Дмитрий Александрович', 'М', '2005-07-20', '20', 2),
        ('Пшеницына Полина Алексеевна', 'Ж', '2005-09-23', '63', 2),
        ('Пяткин Игорь Алексеевич', 'М', '2005-02-09', '33', 2),
        ('Рыбаков Евгений Геннадьевич', 'М', '2005-03-03', '37', 1),
        ('Рыжкин Владислав Дмитриевич', 'М', '2005-08-07', '83', 2),
        ('Рябченко Александра Станиславовна', 'Ж', '2005-09-30', '15', 1),
        ('Снегирев Данил Александрович', 'М', '2005-11-26', '99', 2),
        ('Тульсков Илья Андреевич', 'М', '2005-10-20', '48', 2),
        ('Фирстов Артём Александрович', 'М', '2005-12-10', '49', 2),
        ('Четайкин Владислав Александрович', 'М', '2005-08-26', '51', 2),
        ('Шарунов Максим Игоревич', 'М', '2005-04-24', '21', 2),
        ('Шушев Денис Сергеевич', 'М', '2005-08-15', '78', 1);
    ";
    $queries = explode(';', $sql);
    foreach ($queries as $query) {
        $query = trim($query);
        $query = preg_replace('/--.*$/', '', $query); // удаляем комментарии
        $query = trim($query);
        if ($query !== '') {
            $pdo->exec($query);
        }
    }
}

return $pdo;