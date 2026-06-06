DROP DATABASE IF EXISTS health_tracker;
CREATE DATABASE health_tracker CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS ai_diagnosis_logs;
DROP TABLE IF EXISTS meal_records;
DROP TABLE IF EXISTS sleep_records;
DROP TABLE IF EXISTS exercise_records;
DROP TABLE IF EXISTS health_records;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE health_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    weight FLOAT NOT NULL,
    heart_rate INT NOT NULL,
    systolic INT, -- 収縮期（上の血圧）
    diastolic INT, -- 拡張期（下の血圧）
    recorded_at DATE UNIQUE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE exercise_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    exercise_date DATE NOT NULL,
    exercise_type VARCHAR(100) NOT NULL,
    duration_minutes INT NOT NULL,
    calories_burned INT,
    distance_km DECIMAL(6, 2),
    memo TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_exercise_records_user_date (user_id, exercise_date),
    CONSTRAINT fk_exercise_records_user
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE sleep_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    sleep_date DATE NOT NULL,
    bedtime DATETIME NOT NULL,
    wake_time DATETIME NOT NULL,
    sleep_duration_minutes INT NOT NULL,
    sleep_quality TINYINT,
    memo TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_sleep_records_user_date (user_id, sleep_date),
    CONSTRAINT fk_sleep_records_user
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE CASCADE,
    CONSTRAINT chk_sleep_records_quality
        CHECK (sleep_quality IS NULL OR sleep_quality BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE ai_diagnosis_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    diagnosis_type VARCHAR(50) NOT NULL, -- 'health' など
    result TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ai_diagnosis_logs_user (user_id, created_at),
    CONSTRAINT fk_ai_diagnosis_logs_user
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE meal_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    meal_date DATE NOT NULL,
    meal_type ENUM('breakfast', 'lunch', 'dinner', 'snack') NOT NULL,
    food_name VARCHAR(255) NOT NULL,
    calories INT,
    protein_g DECIMAL(5, 1),
    fat_g DECIMAL(5, 1),
    carbohydrate_g DECIMAL(5, 1),
    memo TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_meal_records_user_date (user_id, meal_date),
    CONSTRAINT fk_meal_records_user
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
