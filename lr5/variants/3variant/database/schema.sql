-- Users table (auth module)
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    login VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20) DEFAULT '',
    city VARCHAR(50) DEFAULT '',
    gender VARCHAR(10) DEFAULT '',
    about TEXT DEFAULT '',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Movies table (CRUD module — Кінотеатр)
CREATE TABLE IF NOT EXISTS movies (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(200) NOT NULL,
    director VARCHAR(100) NOT NULL,
    genre VARCHAR(50) DEFAULT '',
    year YEAR NOT NULL,
    duration_min INTEGER NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Seed movies
INSERT INTO movies (title, director, genre, year, duration_min) VALUES
    ('Інтерстеллар', 'Крістофер Нолан', 'Наукова фантастика', 2014, 169),
    ('Початок', 'Крістофер Нолан', 'Трилер', 2010, 148),
    ('Темний лицар', 'Крістофер Нолан', 'Бойовик', 2008, 152),
    ('Побачення наосліп', 'Вілл Сміт', 'Комедія', 2005, 123),
    ('Матриця', 'Брати Вачовські', 'Наукова фантастика', 1999, 136),
    ('Володар кілець: Братство кільця', 'Пітер Джексон', 'Фентезі', 2001, 178),
    ('Шерлок Холмс', 'Гай Річі', 'Пригоди', 2009, 128),
    ('Гаррі Поттер і філософський камінь', 'Кріс Коламбус', 'Фентезі', 2001, 152),
    ('Аватар', 'Джеймс Кемерон', 'Наукова фантастика', 2009, 162),
    ('Титанік', 'Джеймс Кемерон', 'Драма', 1997, 195);
