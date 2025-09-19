-- أنشئ قاعدة البيانات (عدّل الاسم إن أردت)
CREATE DATABASE IF NOT EXISTS med_quiz
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE med_quiz;

-- جدول المستخدمين الإداريين
CREATE TABLE IF NOT EXISTS admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- جدول المواد
CREATE TABLE IF NOT EXISTS courses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  year TINYINT NOT NULL,      -- 1..6
  semester TINYINT NOT NULL,  -- 1 أو 2
  name VARCHAR(100) NOT NULL,
  UNIQUE KEY uniq_course (year, semester, name)
);

-- جدول الأسئلة
CREATE TABLE IF NOT EXISTS questions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  course_id INT NOT NULL,
  question_text TEXT NOT NULL,
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- جدول الخيارات
CREATE TABLE IF NOT EXISTS options (
  id INT AUTO_INCREMENT PRIMARY KEY,
  question_id INT NOT NULL,
  option_text VARCHAR(255) NOT NULL,
  is_correct BOOLEAN NOT NULL DEFAULT 0,
  FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);

-- (اختياري) عين عيّنة مادة وسؤال تجريبي
INSERT INTO courses (year, semester, name) VALUES (1, 1, 'Anatomy')
  ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO questions (course_id, question_text)
SELECT id, 'What is the largest organ in the human body?'
FROM courses WHERE year=1 AND semester=1 AND name='Anatomy'
  AND NOT EXISTS (
    SELECT 1 FROM questions q
    JOIN courses c ON c.id=q.course_id
    WHERE c.year=1 AND c.semester=1 AND c.name='Anatomy'
  );

INSERT INTO options (question_id, option_text, is_correct)
SELECT q.id, o.txt, o.ok
FROM questions q
JOIN courses c ON c.id=q.course_id AND c.year=1 AND c.semester=1 AND c.name='Anatomy'
JOIN (
  SELECT 'Skin' AS txt, 1 AS ok
  UNION ALL SELECT 'Liver', 0
  UNION ALL SELECT 'Lung', 0
  UNION ALL SELECT 'Heart', 0
) o
WHERE NOT EXISTS (SELECT 1 FROM options oo WHERE oo.question_id=q.id);