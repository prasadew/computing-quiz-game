-- Database Schema for History of Computing Quiz Game
-- Create Database
CREATE DATABASE ComputingQuizGame;
GO

USE ComputingQuizGame;
GO

-- Users Table
CREATE TABLE users (
    id INT IDENTITY(1,1) PRIMARY KEY,
    name NVARCHAR(100) NOT NULL,
    email NVARCHAR(255) UNIQUE NOT NULL,
    password_hash NVARCHAR(255) NOT NULL,
    total_score INT DEFAULT 0,
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME DEFAULT GETDATE()
);
GO

-- Questions Table
CREATE TABLE questions (
    id INT IDENTITY(1,1) PRIMARY KEY,
    question_text NVARCHAR(MAX) NOT NULL,
    option_a NVARCHAR(255) NOT NULL,
    option_b NVARCHAR(255) NOT NULL,
    option_c NVARCHAR(255) NOT NULL,
    option_d NVARCHAR(255) NOT NULL,
    correct_option CHAR(1) NOT NULL CHECK (correct_option IN ('A', 'B', 'C', 'D')),
    difficulty NVARCHAR(20) NOT NULL CHECK (difficulty IN ('Easy', 'Medium', 'Hard')),
    created_at DATETIME DEFAULT GETDATE()
);
GO

-- Scores Table
CREATE TABLE scores (
    id INT IDENTITY(1,1) PRIMARY KEY,
    user_id INT NOT NULL,
    score INT NOT NULL,
    difficulty NVARCHAR(20),
    created_at DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
GO

-- Lifelines Table (Session-based)
CREATE TABLE lifelines (
    id INT IDENTITY(1,1) PRIMARY KEY,
    user_id INT NOT NULL,
    session_id NVARCHAR(100) NOT NULL,
    add_time_remaining INT DEFAULT 3,
    fifty_fifty_remaining INT DEFAULT 3,
    skip_remaining INT DEFAULT 3,
    banana_used BIT DEFAULT 0,
    created_at DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
GO

-- Game Sessions Table
CREATE TABLE game_sessions (
    id INT IDENTITY(1,1) PRIMARY KEY,
    user_id INT NOT NULL,
    session_id NVARCHAR(100) UNIQUE NOT NULL,
    difficulty NVARCHAR(20) NOT NULL,
    current_score INT DEFAULT 0,
    questions_answered INT DEFAULT 0,
    is_active BIT DEFAULT 1,
    started_at DATETIME DEFAULT GETDATE(),
    ended_at DATETIME NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
GO

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
('What was the name of IBM''s chess-playing computer?', 'Watson', 'Deep Blue', 'AlphaGo', 'HAL 9000', 'B', 'Medium'),
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

GO

-- Create Indexes for Performance
CREATE INDEX idx_questions_difficulty ON questions(difficulty);
CREATE INDEX idx_scores_user_id ON scores(user_id);
CREATE INDEX idx_scores_created_at ON scores(created_at DESC);
CREATE INDEX idx_game_sessions_user ON game_sessions(user_id);
CREATE INDEX idx_game_sessions_active ON game_sessions(is_active);
GO

-- Create View for Leaderboard
CREATE VIEW leaderboard_view AS
SELECT TOP 10
    u.id,
    u.name,
    u.email,
    u.total_score,
    COUNT(s.id) as games_played,
    MAX(s.score) as highest_score
FROM users u
LEFT JOIN scores s ON u.id = s.user_id
GROUP BY u.id, u.name, u.email, u.total_score
ORDER BY u.total_score DESC;
GO

-- Stored Procedure to Update Total Score
CREATE PROCEDURE UpdateUserTotalScore
    @user_id INT,
    @new_score INT
AS
BEGIN
    UPDATE users
    SET total_score = total_score + @new_score,
        updated_at = GETDATE()
    WHERE id = @user_id;
END;
GO