INSERT INTO users (name, email, gender, register_date, occupation)
VALUES ('Логунов Илья Сергеевич', 'logunov@gmail.com', 'male', date('now'), 'student');

INSERT INTO users (name, email, gender, register_date, occupation)
VALUES ('Кочнев Артем Алексеевич', 'kochnev@gmail.com', 'male', date('now'), 'student');

INSERT INTO users (name, email, gender, register_date, occupation)
VALUES ('Колыганов Александр Павлович', 'kolganov@gmail.com', 'male', date('now'), 'student');

INSERT INTO users (name, email, gender, register_date, occupation)
VALUES ('Казейкин Иван Иванович', 'kazeikin@gmail.com', 'male', date('now'), 'student');

INSERT INTO users (name, email, gender, register_date, occupation)
VALUES ('Макарова Юлия Сергеевна', 'makarova@gmail.com', 'female', date('now'), 'student');

INSERT INTO movies (title, year)
VALUES ('Мастер 2025', 2025);

INSERT INTO movie_genres (movie_id, genre_id)
SELECT m.id, g.id 
FROM movies m, genres g 
WHERE m.title = 'Мастер 2025' AND g.name = 'Action';

INSERT INTO movies (title, year)
VALUES ('Август 2025', 2025);

INSERT INTO movie_genres (movie_id, genre_id)
SELECT m.id, g.id 
FROM movies m, genres g 
WHERE m.title = 'Август 2025' AND g.name = 'Comedy';

INSERT INTO movies (title, year)
VALUES ('Алиса в Стране чудес 2025', 2025);

INSERT INTO movie_genres (movie_id, genre_id)
SELECT m.id, g.id 
FROM movies m, genres g 
WHERE m.title = 'Алиса в Стране чудес 2025' AND g.name = 'Sci-Fi';

INSERT INTO ratings (user_id, movie_id, rating, timestamp)
SELECT 
    (SELECT id FROM users WHERE email = 'logunov@gmail.com'),
    (SELECT id FROM movies WHERE title = 'Мастер 2025'),
    4.0,
    strftime('%s', 'now');

INSERT INTO ratings (user_id, movie_id, rating, timestamp)
SELECT 
    (SELECT id FROM users WHERE email = 'logunov@gmail.com'),
    (SELECT id FROM movies WHERE title = 'Август 2025'),
    4.5,
    strftime('%s', 'now');

INSERT INTO ratings (user_id, movie_id, rating, timestamp)
SELECT 
    (SELECT id FROM users WHERE email = 'logunov@gmail.com'),
    (SELECT id FROM movies WHERE title = 'Алиса в Стране чудес 2025'),
    5.0,
    strftime('%s', 'now');