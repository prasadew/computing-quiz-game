# Computing Quiz Game 🎮

A dynamic web-based quiz game focused on the history of computing and technology. Test your knowledge with adaptive difficulty levels, lifelines, and interactive gameplay.

## 🌟 Features

### User Management
- Secure user registration and login system
- JWT-based authentication
- Personalized user profiles
- Total score tracking and progression

### Game Mechanics
- Three difficulty levels:
  - Easy (30 seconds per question)
  - Medium (20 seconds per question)
  - Hard (15 seconds per question)
- Dynamic scoring system based on:
  - Question difficulty
  - Response time
  - Accuracy

### Lifeline System
- Three types of lifelines:
  - ⏰ Add Time (10 seconds)
  - ↔️ 50/50 (removes two wrong options)
  - ⏭️ Skip Question
- Bonus Feature: Banana Pattern Game
  - Available when all lifelines are used
  - Solve a pattern puzzle to restore one lifeline
  - One-time use per game session

### Question Categories
- Computing pioneers and history
- Technology fundamentals
- Programming languages
- Computer architecture
- Internet and networking
- And more...

### Interactive Features
- Real-time timer
- Sound effects and visual feedback
- Dynamic difficulty progression
- Leaderboard system

## 🛠️ Technical Stack

### Frontend
- HTML5
- CSS3 (Custom dark theme)
- JavaScript (Vanilla)
- Features:
  - Responsive design
  - CSS animations
  - Dynamic DOM manipulation
  - Event-driven architecture

### Backend
- PHP 7.4+
- MySQL Database
- Features:
  - RESTful API endpoints
  - JWT authentication
  - PDO database abstraction
  - Prepared statements for security

### Database Structure
- Users table (authentication & scores)
- Questions table (quiz content)
- Game Sessions table (active games)
- Lifelines table (player aids)
- Scores table (history tracking)
- Session Questions table (progress tracking)
- Session Answers table (response logging)

### Security Features
- Password hashing (bcrypt)
- JWT token authentication
- SQL injection prevention
- XSS protection
- CSRF protection
- Input validation
- Secure session management

## 🚀 Setup Instructions

1. Prerequisites:
   ```
   - PHP 7.4 or higher
   - MySQL 5.7 or higher
   - Web server (Apache/Nginx)
   ```

2. Database Setup:
   ```sql
   - Create MySQL database
   - Import schema.sql
   ```

3. Configuration:
   ```
   - Copy config/database.example.php to config/database.php
   - Update database credentials
   ```

4. Web Server:
   ```
   - Set document root to project folder
   - Ensure PHP has MySQL extension enabled
   ```

5. First Run:
   ```
   - Navigate to index.php
   - Create an account
   - Start playing!
   ```

## 🎯 Game Flow

1. **Registration/Login**
   - Create account or login
   - View total score and ranking

2. **Game Start**
   - Select difficulty level
   - Initialize game session
   - Start with 3 lifelines each

3. **Gameplay**
   - Answer questions within time limit
   - Use lifelines strategically
   - Earn points based on speed and accuracy

4. **Scoring System**
   - Easy: 10 points base
   - Medium: 20 points base
   - Hard: 30 points base
   - Time bonus: +5-10 points for quick answers

5. **Game Over Conditions**
   - Wrong answer
   - Time expired
   - All questions completed

6. **Banana Game Feature**
   - Available after using all lifelines
   - Solve pattern puzzle
   - Restore one lifeline
   - Continue game

## 🔒 Security Considerations

- Passwords are securely hashed
- Session management is secure
- SQL injection protected
- XSS attacks prevented
- CSRF protection implemented
- Input validation on all forms
- Secure cookie handling

## 🤝 Contributing

Feel free to:
- Report bugs
- Suggest features
- Submit pull requests
- Add new questions
- Improve documentation

## 📝 License



## 👥 Authors

- Prasasthi Dewasurendra - Initial work and development

## 🙏 Acknowledgments

- [Marc Conrad](https://marcconrad.com) - Banana Pattern Game integration
- Computing history resources and question sources
- Open source community and contributors