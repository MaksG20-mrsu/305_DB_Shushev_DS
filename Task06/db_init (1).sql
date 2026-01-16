DROP TABLE IF EXISTS work_records;
DROP TABLE IF EXISTS appointment_services;
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS schedules;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS employees;

CREATE TABLE employees (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    position TEXT NOT NULL,
    hire_date TEXT NOT NULL CHECK(hire_date = strftime('%Y-%m-%d', hire_date)),
    dismissal_date TEXT CHECK(dismissal_date = strftime('%Y-%m-%d', dismissal_date)),
    salary_percentage REAL NOT NULL CHECK(salary_percentage BETWEEN 0 AND 100),
    status TEXT NOT NULL DEFAULT 'active' CHECK(status IN ('active', 'fired')),
    phone TEXT,
    email TEXT
);

CREATE TABLE services (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE,
    duration_minutes INTEGER NOT NULL CHECK(duration_minutes > 0),
    price REAL NOT NULL CHECK(price >= 0)
);

CREATE TABLE schedules (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    employee_id INTEGER NOT NULL,
    day_of_week INTEGER NOT NULL CHECK(day_of_week BETWEEN 1 AND 7),
    start_time TEXT NOT NULL CHECK(start_time = strftime('%H:%M', start_time)),
    end_time TEXT NOT NULL CHECK(end_time = strftime('%H:%M', end_time)),
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

CREATE TABLE appointments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    employee_id INTEGER NOT NULL,
    client_name TEXT NOT NULL,
    client_phone TEXT,
    appointment_date TEXT NOT NULL CHECK(appointment_date = strftime('%Y-%m-%d', appointment_date)),
    appointment_time TEXT NOT NULL CHECK(appointment_time = strftime('%H:%M', appointment_time)),
    status TEXT NOT NULL DEFAULT 'scheduled' CHECK(status IN ('scheduled', 'completed', 'cancelled')),
    total_price REAL NOT NULL DEFAULT 0 CHECK(total_price >= 0),
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE NO ACTION
);

CREATE TABLE appointment_services (
    appointment_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    PRIMARY KEY (appointment_id, service_id),
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT
);

CREATE TABLE work_records (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    appointment_id INTEGER NOT NULL,
    employee_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    work_date TEXT NOT NULL CHECK(work_date = strftime('%Y-%m-%d', work_date)),
    work_time TEXT NOT NULL CHECK(work_time = strftime('%H:%M', work_time)),
    revenue REAL NOT NULL CHECK(revenue >= 0),
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE NO ACTION,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE NO ACTION,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT
);

CREATE INDEX idx_employees_status ON employees(status);
CREATE INDEX idx_employees_name ON employees(name);
CREATE INDEX idx_employees_hire_dismiss ON employees(hire_date, dismissal_date);

CREATE INDEX idx_services_name ON services(name);

CREATE INDEX idx_schedules_employee_id ON schedules(employee_id);

CREATE INDEX idx_appointments_employee_id ON appointments(employee_id);
CREATE INDEX idx_appointments_date ON appointments(appointment_date);
CREATE INDEX idx_appointments_status ON appointments(status);

CREATE INDEX idx_work_records_employee_id ON work_records(employee_id);
CREATE INDEX idx_work_records_date ON work_records(work_date);


INSERT INTO employees (id, name, position, hire_date, dismissal_date, salary_percentage, status, phone, email) VALUES
(1, 'Григорьев Артём Денисович', 'Мастер', '2025-02-10', NULL, 25.5, 'active', '+7-921-102-34-56', 'grigoryev@sto-auto.ru'),
(2, 'Фёдоров Максим Валерьевич', 'Мастер', '2025-04-05', NULL, 30.0, 'active', '+7-921-213-45-67', 'fedorov@sto-auto.ru'),
(3, 'Егоров Константин Сергеевич', 'Мастер', '2024-10-01', '2025-09-30', 28.0, 'fired', '+7-921-324-56-78', 'egorov@sto-auto.ru'),
(4, 'Волков Дмитрий Ильич', 'Мастер', '2025-06-12', NULL, 27.5, 'active', '+7-921-435-67-89', 'volkov@sto-auto.ru'),
(5, 'Морозова Анна Юрьевна', 'Мастер', '2024-09-18', NULL, 26.0, 'active', '+7-921-546-78-90', 'morozova@sto-auto.ru');

INSERT INTO services (id, name, duration_minutes, price) VALUES
(1, 'Замена масла', 30, 1500.00),
(2, 'Замена тормозных колодок', 60, 3500.00),
(3, 'Диагностика двигателя', 45, 2500.00),
(4, 'Замена фильтров', 40, 2000.00),
(5, 'Шиномонтаж', 20, 1200.00),
(6, 'Развал-схождение', 90, 4000.00),
(7, 'Ремонт подвески', 120, 8000.00),
(8, 'Замена аккумулятора', 25, 3000.00),
(9, 'Промывка системы охлаждения', 60, 2800.00),
(10, 'Замена свечей зажигания', 35, 1800.00);

INSERT INTO schedules (employee_id, day_of_week, start_time, end_time) VALUES
(1, 1, '08:30', '17:30'),
(1, 2, '08:30', '17:30'),
(1, 3, '08:30', '17:30'),
(1, 4, '08:30', '17:30'),
(1, 5, '08:30', '17:30'),
(2, 2, '09:30', '18:30'),
(2, 3, '09:30', '18:30'),
(2, 4, '09:30', '18:30'),
(2, 5, '09:30', '18:30'),
(2, 6, '09:30', '18:30'),
(4, 1, '08:00', '17:00'),
(4, 2, '08:00', '17:00'),
(4, 3, '08:00', '17:00'),
(4, 4, '08:00', '17:00'),
(4, 5, '08:00', '17:00'),
(5, 1, '10:00', '19:00'),
(5, 2, '10:00', '19:00'),
(5, 3, '10:00', '19:00'),
(5, 4, '10:00', '19:00'),
(5, 5, '10:00', '19:00'),
(5, 6, '10:00', '15:00');

INSERT INTO appointments (id, employee_id, client_name, client_phone, appointment_date, appointment_time, status, total_price) VALUES
(1, 1, 'Савельев Илья Романович', '+7-931-112-22-33', '2025-12-01', '09:00', 'completed', 4000.00),
(2, 1, 'Кузнецова Екатерина Андреевна', '+7-931-223-33-44', '2025-12-01', '13:30', 'completed', 5500.00),
(3, 2, 'Беляев Никита Олегович', '+7-931-334-44-55', '2025-12-02', '10:00', 'completed', 3000.00),
(4, 2, 'Фролова Дарья Павловна', '+7-931-445-55-66', '2025-12-02', '16:00', 'scheduled', 8000.00),
(5, 4, 'Тихонов Арсений Викторович', '+7-931-556-66-77', '2025-12-03', '08:30', 'completed', 4300.00),
(6, 5, 'Горбачёва Полина Степановна', '+7-931-667-77-88', '2025-12-03', '11:00', 'scheduled', 2500.00),
(7, 1, 'Щукин Михаил Григорьевич', '+7-931-778-88-99', '2025-12-04', '09:00', 'scheduled', 6800.00),
(8, 2, 'Романова Алина Вадимовна', '+7-931-889-99-00', '2025-12-04', '14:00', 'scheduled', 1500.00);

INSERT INTO appointment_services (appointment_id, service_id) VALUES
(1, 6),
(2, 2), 
(2, 4),
(3, 8),
(4, 7),
(5, 1), 
(5, 4),
(6, 3),
(7, 2), 
(7, 4),
(8, 1);

INSERT INTO work_records (id, appointment_id, employee_id, service_id, work_date, work_time, revenue) VALUES
(1, 1, 1, 6, '2025-12-01', '09:00', 4000.00),
(2, 2, 1, 2, '2025-12-01', '13:30', 3500.00),
(3, 2, 1, 4, '2025-12-01', '14:45', 2000.00),
(4, 3, 2, 8, '2025-12-02', '10:00', 3000.00),
(5, 5, 4, 1, '2025-12-03', '08:30', 1500.00),
(6, 5, 4, 4, '2025-12-03', '09:15', 2000.00),
(7, 5, 4, 10, '2025-12-03', '10:00', 800.00);