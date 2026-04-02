CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_sessions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    token CHAR(64) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    CONSTRAINT fk_user_sessions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE muscle_groups (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL UNIQUE
);

INSERT INTO muscle_groups (name) VALUES
('Borst'),
('Rug'),
('Schouders'),
('Biceps'),
('Triceps'),
('Quadriceps'),
('Hamstrings'),
('Bilspieren'),
('Kuiten'),
('Core');

CREATE TABLE exercises (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    name VARCHAR(120) NOT NULL,
    description TEXT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    sets_count INT UNSIGNED NOT NULL,
    start_reps INT UNSIGNED NOT NULL,
    start_weight_kg DECIMAL(5,2) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_exercises_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE exercise_target_muscles (
    exercise_id INT UNSIGNED NOT NULL,
    muscle_group_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (exercise_id, muscle_group_id),
    CONSTRAINT fk_etm_exercise FOREIGN KEY (exercise_id) REFERENCES exercises(id) ON DELETE CASCADE,
    CONSTRAINT fk_etm_muscle FOREIGN KEY (muscle_group_id) REFERENCES muscle_groups(id) ON DELETE CASCADE
);
