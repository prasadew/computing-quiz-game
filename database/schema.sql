-- Database Schema for History of Computing Quiz Game
-- MySQL Version for WAMP/PHPMyAdmin

-- Create Database
CREATE DATABASE IF NOT EXISTS ComputingQuizGame;
USE ComputingQuizGame;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    total_score INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Questions Table
CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_text TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_option CHAR(1) NOT NULL CHECK (correct_option IN ('A', 'B', 'C', 'D')),
    difficulty VARCHAR(20) NOT NULL CHECK (difficulty IN ('Easy', 'Medium', 'Hard')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Scores Table
CREATE TABLE IF NOT EXISTS scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    score INT NOT NULL,
    difficulty VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Lifelines Table (Session-based)
CREATE TABLE IF NOT EXISTS lifelines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_id VARCHAR(100) NOT NULL,
    add_time_remaining INT DEFAULT 3,
    fifty_fifty_remaining INT DEFAULT 3,
    skip_remaining INT DEFAULT 3,
    banana_used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Game Sessions Table
CREATE TABLE IF NOT EXISTS game_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_id VARCHAR(100) UNIQUE NOT NULL,
    difficulty VARCHAR(20) NOT NULL,
    current_score INT DEFAULT 0,
    questions_answered INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ended_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Session Questions Table (Track asked questions per session)
CREATE TABLE IF NOT EXISTS session_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(100) NOT NULL,
    question_id INT NOT NULL,
    asked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Session Answers Table (Track all answers in a session)
CREATE TABLE IF NOT EXISTS session_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(100) NOT NULL,
    question_id INT NOT NULL,
    selected_option CHAR(1) NOT NULL,
    is_correct TINYINT(1) NOT NULL,
    points_earned INT DEFAULT 0,
    time_taken INT DEFAULT 0,
    answered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Sample Questions for Easy Difficulty
INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_option, difficulty) VALUES
('Who is considered the father of modern computing?', 'Steve Jobs', 'Alan Turing', 'Bill Gates', 'Mark Zuckerberg', 'B', 'Easy'),
('What does CPU stand for?', 'Central Processing Unit', 'Computer Personal Unit', 'Central Program Utility', 'Common Processing Unit', 'A', 'Easy'),
('In what year was the first iPhone released?', '2005', '2007', '2009', '2011', 'B', 'Easy'),
('What does RAM stand for?', 'Random Access Memory', 'Rapid Access Memory', 'Read Access Memory', 'Real Application Memory', 'A', 'Easy'),
('Which company created the Windows operating system?', 'Apple', 'IBM', 'Microsoft', 'Google', 'C', 'Easy'),
('What does USB stand for?', 'Universal Serial Bus', 'United System Bus', 'Universal System Block', 'Unified Serial Block', 'A', 'Easy'),
('Who co-founded Apple Computer with Steve Jobs?', 'Steve Wozniak', 'Bill Gates', 'Larry Page', 'Paul Allen', 'A', 'Easy'),
('What is the name of the first electronic computer?', 'UNIVAC', 'ENIAC', 'EDVAC', 'EDSAC', 'B', 'Easy'),
('Which programming language is known as the mother of all languages?', 'Java', 'Python', 'C', 'FORTRAN', 'C', 'Easy'),
('What does HTML stand for?', 'HyperText Markup Language', 'HighTech Modern Language', 'HyperTransfer Markup Language', 'HomeText Making Language', 'A', 'Easy');

-- Insert Sample Questions for Medium Difficulty
INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_option, difficulty) VALUES
('What was the name of the first web browser?', 'Mosaic', 'WorldWideWeb', 'Netscape', 'Internet Explorer', 'B', 'Medium'),
('Who invented the World Wide Web?', 'Tim Berners-Lee', 'Vint Cerf', 'Marc Andreessen', 'Larry Page', 'A', 'Medium'),
('What year was the transistor invented?', '1945', '1947', '1950', '1952', 'B', 'Medium'),
('Which machine is considered the first mechanical computer?', 'Analytical Engine', 'Difference Engine', 'ENIAC', 'UNIVAC', 'B', 'Medium'),
('What does ARPANET stand for?', 'Advanced Research Projects Agency Network', 'American Research Projects Agency Network', 'Automated Research Projects Agency Network', 'Applied Research Projects Agency Network', 'A', 'Medium'),
('In which decade was the mouse invented?', '1950s', '1960s', '1970s', '1980s', 'B', 'Medium'),
('Who developed the first compiler?', 'Grace Hopper', 'Ada Lovelace', 'Alan Turing', 'John von Neumann', 'A', 'Medium'),
('What was the name of IBM\'s chess-playing computer?', 'Watson', 'Deep Blue', 'AlphaGo', 'HAL 9000', 'B', 'Medium'),
('Which company developed the first commercially successful personal computer?', 'Apple', 'IBM', 'Commodore', 'Tandy', 'C', 'Medium'),
('What does ASCII stand for?', 'American Standard Code for Information Interchange', 'Automated Standard Code for Information Interchange', 'American System Code for Information Interchange', 'Advanced Standard Code for Information Interchange', 'A', 'Medium');

-- Insert Sample Questions for Hard Difficulty
INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_option, difficulty) VALUES
('What was the name of the first algorithm written for a computer?', 'Babbage Algorithm', 'Lovelace Algorithm', 'Turing Algorithm', 'Von Neumann Algorithm', 'B', 'Hard'),
('In what year was the integrated circuit invented?', '1956', '1958', '1960', '1962', 'B', 'Hard'),
('Who proposed the concept of a universal computing machine?', 'Charles Babbage', 'Alan Turing', 'John von Neumann', 'Claude Shannon', 'B', 'Hard'),
('What was the name of the first stored-program computer?', 'ENIAC', 'EDVAC', 'Manchester Baby', 'UNIVAC', 'C', 'Hard'),
('Which mathematician is credited with developing Boolean algebra?', 'George Boole', 'John Venn', 'Augustus De Morgan', 'Claude Shannon', 'A', 'Hard'),
('What was the code name for the British computer used to break Enigma codes?', 'Colossus', 'Bombe', 'Ultra', 'Magic', 'A', 'Hard'),
('In what year was the first email sent?', '1969', '1971', '1973', '1975', 'B', 'Hard'),
('Who developed the concept of hypertext?', 'Ted Nelson', 'Tim Berners-Lee', 'Douglas Engelbart', 'Vannevar Bush', 'A', 'Hard'),
('What was the first computer virus called?', 'Creeper', 'Morris Worm', 'Brain', 'Elk Cloner', 'A', 'Hard'),
('Which programming language was developed by Bjarne Stroustrup?', 'Java', 'C++', 'Python', 'C#', 'B', 'Hard');

-- Create Indexes for Performance
CREATE INDEX idx_questions_difficulty ON questions(difficulty);
CREATE INDEX idx_scores_user_id ON scores(user_id);
CREATE INDEX idx_scores_created_at ON scores(created_at);
CREATE INDEX idx_game_sessions_user ON game_sessions(user_id);
CREATE INDEX idx_game_sessions_active ON game_sessions(is_active);
CREATE INDEX idx_session_questions_session ON session_questions(session_id);

-- Create View for Leaderboard
CREATE OR REPLACE VIEW leaderboard_view AS
SELECT 
    u.id,
    u.name,
    u.email,
    u.total_score,
    COUNT(s.id) as games_played,
    MAX(s.score) as highest_score
FROM users u
LEFT JOIN scores s ON u.id = s.user_id
GROUP BY u.id, u.name, u.email, u.total_score
ORDER BY u.total_score DESC
LIMIT 10;