# 🖥️ Computing Quiz Game

> A feature-rich, interactive web-based quiz application testing knowledge on computing history, technology pioneers, and fundamental computer concepts with engaging gameplay mechanics, strategic challenges, and comprehensive security.

<div align="center">

[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=for-the-badge&logo=php)](https://www.php.net)
[![MySQL Version](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?style=for-the-badge&logo=mysql)](https://www.mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)
[![Security](https://img.shields.io/badge/Security-Enhanced-brightgreen?style=for-the-badge)](README.md#-security-architecture)
[![Build Status](https://img.shields.io/badge/Build-Passing-brightgreen?style=for-the-badge)](.)
[![Status](https://img.shields.io/badge/Status-Production%20Ready-blue?style=for-the-badge)](.)

</div>

---

## 📋 Table of Contents

- [Overview](#-overview)
- [Key Features](#-key-features)
- [Technical Stack](#-technical-stack)
- [Database Architecture](#-database-architecture)
- [Installation Guide](#-installation-guide)
- [API Endpoints](#-api-endpoints)
- [Game Mechanics](#-game-mechanics)
- [Security](#-security-architecture)
- [Project Structure](#-project-structure)
- [Contributing](#-contributing)
- [License](#-license)
- [Acknowledgments](#-acknowledgments)

---

## 🎮 Overview

Computing Quiz Game is a sophisticated, full-featured quiz platform designed to test and expand knowledge about computing history, technology evolution, and computer science fundamentals. Built with modern web technologies, it combines engaging gameplay mechanics with enterprise-grade security and performance optimization.

**Perfect for:**
- Computer science enthusiasts
- Educational assessments
- Competitive gameplay
- Skill verification and progression tracking

---

## ✨ Key Features

### 🎯 Core Gameplay

#### **Three-Tier Difficulty System**

<div align="center">

| Level | Time | Points | Target |
|:---:|:---:|:---:|:---|
| 🟢 **Easy** | 30 sec | 10 base | Beginners & Learning |
| 🟡 **Medium** | 20 sec | 20 base | Balanced Challenge |
| 🔴 **Hard** | 15 sec | 30 base | Seasoned Players |

</div>

- **Easy Mode** (30 sec per question)
  - 10 base points per correct answer
  - Ideal for beginners and learning
- **Medium Mode** (20 sec per question)
  - 20 base points per correct answer
  - Balanced challenge and reward
- **Hard Mode** (15 sec per question)
  - 30 base points per correct answer
  - For seasoned players

#### **Advanced Lifeline System**
Strategic aids to help overcome challenging questions:
- ⏰ **Add Time** - Gain 10 additional seconds on current question (3x per session)
- ↔️ **50/50** - Eliminate two incorrect answers, narrowing choices (3x per session)
- ⏭️ **Skip Question** - Move to the next question without penalty (3x per session)

#### **Signature Feature: Banana Pattern Game™**
An innovative puzzle-solving mechanic that awards players:
- 🍌 **Available when:** All lifelines are exhausted
- 🧩 **Challenge:** Solve a pattern recognition puzzle
- 🎁 **Reward:** Restore one lifeline to continue gameplay
- 🎮 **Strategy:** One-time use per game session, encouraging creative recovery

### 👤 User Management & Authentication

#### **Registration & Login System**
- ✅ Secure account creation with email uniqueness validation
- 🔐 Password strength requirements (minimum 6 characters)
- 🔑 Persistent session management with JWT tokens
- 💾 Remember-me functionality via secure cookies

#### **Two-Factor Authentication (2FA)**
Enterprise-grade account security:
- 📱 **TOTP Support** - Compatible with Google Authenticator, Microsoft Authenticator, Authy
- 📲 **QR Code Setup** - Quick, secure enrollment
- 🔄 **Backup Codes** - Recovery access if authenticator is unavailable
- 🎛️ **Flexible Enablement** - Users can enable/disable 2FA anytime

#### **User Profiles**
- 👤 Personalized dashboard with progress tracking
- 📈 Total score accumulation across sessions
- 📊 Game history and performance analytics
- ⚙️ Settings panel for account management

### 🏆 Competitive Features

#### **Real-Time Leaderboard**
- 🥇 Top 10 global rankings by total score
- 🎮 Games played counter
- ⭐ Personal best score tracking
- 📍 Current session ranking position
- 🔄 Updated dynamically as scores change

#### **Dynamic Scoring System**
Points earned based on:
- 🎯 Question difficulty level (Easy: 10, Medium: 20, Hard: 30)
- ⚡ Response speed (bonus: +5-10 for quick answers under 10 seconds)
- ✔️ Answer accuracy
- 🎯 Lifeline usage strategy

### 🎨 User Experience

#### **Modern Dark Theme**
- 🌙 Eye-friendly dark color scheme
- ✨ Smooth CSS animations and transitions
- 🎬 Real-time visual feedback for actions
- 📱 Responsive design for desktop and tablets
- 🎭 Engaging typography with custom fonts (Bangers for headings)

#### **Interactive Gameplay**
- ⏱️ Live countdown timer with visual indicators
- 🔊 Sound effects for correct/incorrect answers
- 📊 Progress visualization
- 🎯 Question counter and session statistics
- 🎪 Smooth page transitions

#### **Question Library**
Comprehensive quiz content covering:
- 👨‍💻 Computing pioneers and historical milestones
- 💡 Technology fundamentals and concepts
- 🔤 Programming languages and paradigms
- 🏗️ Computer architecture and hardware
- 🌐 Internet, networking, and protocols
- 💾 Operating systems and software engineering
- 📚 And many more categories...

#### **Dual Question Source System**
The application supports questions from two sources:
- 💾 **Local Database** - Custom questions stored in MySQL
- 🌐 **Open Trivia Database (OpenTDB)** - External API with 50+ questions per fetch
  - 🎯 Category: 18 (Computer Science & Technology)
  - 🔄 Automatic formatting to match local question structure
  - 🎚️ Dynamically selected by difficulty level

---

## 🛠️ Technical Stack

### **Frontend Architecture**

<div align="center">

| Technology | Purpose | Features |
|:---:|:---:|:---|
| ![HTML5](https://img.shields.io/badge/HTML5-E34C26?style=for-the-badge&logo=html5&logoColor=white) | Document structure | Semantic markup, accessibility |
| ![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white) | Styling & layout | Custom animations, dark theme, responsive grid |
| ![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black) | Client-side logic | DOM manipulation, event handling, real-time updates |

</div>

**Frontend Capabilities:**
- ✨ Event-driven architecture for responsive UI
- 🔄 AJAX/Fetch API for seamless server communication
- 💾 LocalStorage for client-side state management
- ⏱️ Timer management and countdown logic
- 🔊 Sound playback and multimedia handling

### **Backend Architecture**

<div align="center">

| Component | Technology | Purpose |
|:---:|:---:|:---|
| **Server** | ![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white) | Server-side logic and routing |
| **Database** | ![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white) | Data persistence and queries |
| **API** | ![REST API](https://img.shields.io/badge/REST-API-009688?style=for-the-badge&logo=api&logoColor=white) | Client-server communication |
| **Authentication** | ![JWT](https://img.shields.io/badge/JWT-000000?style=for-the-badge&logo=json-web-tokens&logoColor=white) | Secure user verification |
| **Security** | ![PDO](https://img.shields.io/badge/PDO-336791?style=for-the-badge&logo=php&logoColor=white) | SQL injection prevention |

</div>

**Backend Features:**
- 🔌 RESTful API design with consistent response formats
- 🔐 JWT (JSON Web Token) authentication for stateless sessions
- 📊 Session management for real-time game state
- 🗄️ PDO database abstraction layer for MySQL compatibility
- 🛡️ Prepared statements preventing SQL injection
- 📝 Error handling and logging infrastructure
- 🌐 External API integration (Open Trivia Database)

### **External Data Sources**

#### **Open Trivia Database (OpenTDB) Integration**

<div align="center">

![OpenTDB](https://img.shields.io/badge/OpenTDB-API-FF6B6B?style=for-the-badge&logo=api&logoColor=white)
![REST](https://img.shields.io/badge/REST-JSON-4ECDC4?style=for-the-badge&logo=json&logoColor=white)

</div>

- 🌐 **API Endpoint:** `https://opentdb.com/api.php`
- 🎯 **Category:** Computer Science & Technology (ID: 18)
- 📋 **Question Type:** Multiple choice
- 📦 **Fetch Strategy:** Retrieve up to 50 questions per API call
- 🔄 **Response Format:** Automatic conversion to local database format
- 🎚️ **Difficulty Filtering:** Questions filtered by requested difficulty level
- 📍 **Implementation:** `/api/fetch_api_questions.php`
- ⚡ **Features:**
  - Automatic HTML entity decoding
  - Random question selection from API batch
  - Fallback error handling if API unavailable
  - Seamless integration with local database questions

---

## 🎨 Technology Showcase

<div align="center">

### **Complete Technology Stack Visualization**

```
┌─────────────────────────────────────────────────────────────┐
│              COMPUTING QUIZ GAME ARCHITECTURE                │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌─────────────────────┐        ┌──────────────────────┐   │
│  │   FRONTEND LAYER    │        │   BACKEND LAYER      │   │
│  ├─────────────────────┤        ├──────────────────────┤   │
│  │ 🌐 HTML5            │        │ 🔌 PHP 7.4+          │   │
│  │ 🎨 CSS3             │        │ 🗄️  MySQL 5.7+       │   │
│  │ ⚙️  JavaScript       │        │ 🔐 JWT Auth          │   │
│  │                     │        │ 🛡️  PDO/Prepared     │   │
│  │ Features:           │        │ 🚀 RESTful API       │   │
│  │ • Animations        │        │                      │   │
│  │ • Real-time Timer   │        │ Features:            │   │
│  │ • Responsive UI     │        │ • Session Mgmt       │   │
│  │ • Sound Effects     │        │ • Security Layer     │   │
│  │ • LocalStorage      │        │ • Error Handling     │   │
│  └─────────────────────┘        └──────────────────────┘   │
│           ↓                                ↓                 │
│         AJAX/Fetch                  External API            │
│           ↓                                ↓                 │
│  ┌────────────────────────────────────────────────────┐    │
│  │    OPEN TRIVIA DATABASE (OpenTDB) Integration      │    │
│  │  📚 50+ Computer Science Questions • Auto Format  │    │
│  └────────────────────────────────────────────────────┘    │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

</div>

---

---

## 📊 Database Architecture

### **Entity Relationship Overview**

```
users (1) ──── (N) game_sessions
  │
  ├──── (N) scores
  └──── (N) lifelines

game_sessions (1) ──── (N) session_questions
                  └──── (N) session_answers

questions (1) ──── (N) session_questions
             └──── (N) session_answers
```

### **Core Tables**

#### **users**
```sql
- id (PRIMARY KEY)
- name, email (UNIQUE), password_hash
- total_score (cumulative points)
- two_fa_enabled, two_fa_secret, two_fa_backup_codes
- created_at, updated_at (timestamps)
```

#### **game_sessions**
```sql
- id (PRIMARY KEY), session_id (UNIQUE)
- user_id (FOREIGN KEY → users)
- difficulty (Easy/Medium/Hard)
- current_score, questions_answered
- is_active (boolean game state)
- started_at, ended_at (timestamps)
```

#### **questions**
```sql
- id (PRIMARY KEY)
- question_text (the quiz question)
- option_a, option_b, option_c, option_d
- correct_option (ENUM: A/B/C/D)
- difficulty (Easy/Medium/Hard)
- created_at (timestamp)
```

#### **lifelines**
```sql
- id (PRIMARY KEY)
- user_id, session_id (FOREIGN KEYS)
- add_time_remaining (count: 0-3)
- fifty_fifty_remaining (count: 0-3)
- skip_remaining (count: 0-3)
- banana_used (boolean: 0-1)
- created_at (timestamp)
```

#### **scores**
```sql
- id (PRIMARY KEY)
- user_id (FOREIGN KEY → users)
- score (points earned)
- difficulty (Easy/Medium/Hard)
- created_at (timestamp)
```

#### **session_questions & session_answers**
- Track question progression within sessions
- Log user responses and correctness
- Calculate time taken per question
- Enable replay and analytics

---

## 🚀 Installation Guide

<div align="center">

![Easy Setup](https://img.shields.io/badge/Setup-Easy-green?style=for-the-badge)
![Time](https://img.shields.io/badge/Time-~10%20mins-blue?style=for-the-badge)
![Difficulty](https://img.shields.io/badge/Difficulty-Beginner-brightgreen?style=for-the-badge)

</div>

### **Prerequisites**

Ensure your system has:
- **PHP 7.4 or higher** with MySQL extension
- **MySQL 5.7 or higher** database server
- **Web Server** (Apache with mod_rewrite, Nginx, etc.)
- **WAMP/LAMP/LEMP Stack** (recommended for all-in-one setup)

### **Step 1: Clone or Download Project**

```bash
# Clone the repository
git clone <repository-url>
cd computing-quiz-game

# Or extract the project zip file
unzip computing-quiz-game.zip
cd computing-quiz-game
```

### **Step 2: Database Setup**

```bash
# 1. Open MySQL command line or phpMyAdmin
mysql -u root -p

# 2. Create database
CREATE DATABASE ComputingQuizGame;
USE ComputingQuizGame;

# 3. Import schema
mysql -u root -p ComputingQuizGame < database/schema.sql
```

### **Step 3: Configure Database Connection**

```bash
# Edit the database configuration file
# File: config/database.php

# Update these credentials:
private $host = "localhost";              // MySQL host
private $database = "ComputingQuizGame";  // Database name
private $username = "root";               // MySQL user
private $password = "";                   // MySQL password
```

### **Step 4: Configure Web Server**

#### **For Apache:**
```apache
# .htaccess (included) should enable mod_rewrite
# Set DocumentRoot to the project folder
# Enable Apache rewrite module: a2enmod rewrite
```

#### **For Nginx:**
```nginx
server {
    listen 80;
    server_name localhost;
    root /path/to/computing-quiz-game;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
```

### **Step 5: Verify Installation**

```bash
# 1. Start your web server
# 2. Navigate to: http://localhost/computing-quiz-game
# 3. Create a test account
# 4. Play a test game
```

### **Step 6: Optional - Add Sample Questions**

```php
// Use the API or admin panel to add questions
// Or import a question CSV file
```

### **Step 7: External API Configuration**

The application includes optional integration with **Open Trivia Database (OpenTDB)**:

```php
// Configuration in: /api/fetch_api_questions.php
$url = 'https://opentdb.com/api.php?amount=50&category=18&type=multiple';

// Parameters:
// amount=50       - Fetch 50 questions per request
// category=18     - Computer Science & Technology category
// type=multiple   - Multiple choice questions only

// No API key required for OpenTDB (free public API)
// Rate limiting: ~5 requests per second recommended
```

**Features:**
- ✅ No authentication needed
- ✅ Automatically formatted for compatibility
- ✅ Fallback to local database if unavailable
- ✅ Difficulty-based filtering
- ✅ Questions decoded and sanitized

<div align="center">

### 🎉 **You're all set! Start playing the Computing Quiz Game!**

</div>

---

## 📡 API Endpoints

### **Base URL:** `http://localhost/computing-quiz-game/api/`

### **Game Session Management**

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/init_session.php` | POST | Initialize new game session with difficulty |
| `/get_question.php` | GET | Fetch current question for session |
| `/submit_answer.php` | POST | Submit answer and calculate points |
| `/save_score.php` | POST | Save final score to database |
| `/fetch_api_questions.php` | GET | Fetch questions from Open Trivia DB API |

### **Lifeline System**

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/get_lifelines.php` | GET | Fetch remaining lifelines for session |
| `/use_lifeline.php` | POST | Use specified lifeline type |
| `/award_lifeline.php` | POST | Award lifeline from banana game |

### **Authentication (via `/auth/`)**

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/register.php` | POST | Create new user account |
| `/login.php` | POST | Authenticate and receive JWT token |
| `/logout.php` | GET | Destroy session and token |
| `/verify-2fa.php` | POST | Verify 2FA code |

### **User Data**

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/pages/game.php` | GET | Main game dashboard |
| `/pages/leaderboard.php` | GET | Global rankings |
| `/pages/settings.php` | GET/POST | User settings and 2FA management |

### **Example API Request**

```javascript
// Initialize game session
fetch('/api/init_session.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + authToken
    },
    body: JSON.stringify({
        difficulty: 'Medium'
    })
})
.then(response => response.json())
.then(data => {
    console.log('Session started:', data.sessionId);
});

// Submit answer
fetch('/api/submit_answer.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        sessionId: sessionId,
        questionId: questionId,
        selectedOption: 'B',
        timeTaken: 12
    })
})
.then(response => response.json())
.then(data => {
    console.log('Points earned:', data.pointsEarned);
});
```

### **External Question Source (Open Trivia Database)**

#### **Fetch External Questions**

```javascript
// Fetch from Open Trivia DB API
fetch('/api/fetch_api_questions.php?difficulty=Medium', {
    method: 'GET',
    headers: {'Content-Type': 'application/json'}
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('Question:', data.question);
        // Returns:
        // {
        //   id: 'api_<hash>',
        //   question: 'What is the binary representation of 10?',
        //   choices: ['1010', '1000', '1100', '0101'],
        //   correct_answer: '1010',
        //   difficulty: 'Medium',
        //   source: 'api'
        // }
    }
});
```

#### **How External Questions Work**

1. **Question Fetching**
   - Sends request to Open Trivia Database API
   - Fetches 50 questions per batch request
   - Filters by requested difficulty (Easy/Medium/Hard)
   - Selects random question from batch

2. **Question Formatting**
   - Converts API format to local database format
   - Decodes HTML entities from API response
   - Assigns unique ID: `api_<md5_hash_of_question>`
   - Marks source as 'api' for tracking

3. **Answer Processing**
   - Questions with `api_` prefix are recognized as external
   - Answers validated against API question data
   - Points calculated same as local questions
   - Results tracked in game session

4. **Fallback Handling**
   - If external API unavailable, falls back to local database
   - Error handling with JSON response
   - Graceful degradation of service

---

## 🎯 Game Mechanics

### **Question Flow**

```
1. Select Difficulty
   ↓
2. Lifelines Initialized (3 each of: Add Time, 50/50, Skip)
   ↓
3. Fetch Question from Database
   ↓
4. Display Question with 4 Options & Timer
   ↓
5. Player Selects Option or Uses Lifeline
   ↓
6. Evaluate Answer
   ↓
7. Award/Deny Points
   ↓
8. Move to Next Question or Game Over
```

### **Scoring Calculation**

```
Base Points = Difficulty Level
  Easy:   10 points
  Medium: 20 points
  Hard:   30 points

Time Bonus = 5-10 points (if answered in <10 seconds)

Total Points = Base Points + Time Bonus (only if correct)

Running Total = Sum of all question points
```

### **Game Over Conditions**

❌ **Wrong Answer** - Immediate game over
⏱️ **Time Expired** - Question timeout triggers game over
✅ **All Questions Complete** - Successfully finish all questions

### **Lifeline Mechanics**

**Add Time**
- Adds 10 seconds to current question timer
- Consumed immediately upon use
- Cannot be used if time already expired

**50/50**
- Removes 2 incorrect options
- Displays remaining 2 options
- Significantly improves odds

**Skip Question**
- Moves to next question immediately
- No points awarded for skipped question
- Does not trigger game over

**Banana Game Recovery**
- Available when all 9 lifelines consumed
- Solve pattern puzzle (integration with Marc Conrad's game)
- Successfully solving restores 1 lifeline
- One-time use per session

---

### **Question Sources & Data Flow**

#### **Dual Question System**

The game engine supports two question sources seamlessly:

**Local Database Questions**
- Stored in MySQL `questions` table
- Manually curated content
- Full admin control
- Persistent across sessions
- Custom questions specific to your institution/context

**External API Questions (Open Trivia Database)**
- Real-time fetching from OpenTDB API
- 50+ technology questions per fetch
- Automatic category filtering (Computer Science)
- Difficulty-based selection
- Fresh content variety
- No storage overhead

#### **Question Selection Logic**

```
1. Player initiates game session
   ↓
2. System checks available question sources
   ↓
3. Gets difficulty preference (Easy/Medium/Hard)
   ↓
4. Either:
   a) Fetches from local database, OR
   b) Calls OpenTDB API (/api/fetch_api_questions.php)
   ↓
5. Question formatted with unique ID:
   - Local: numeric ID (e.g., "42")
   - External: api_<hash> (e.g., "api_a3f2b1c9")
   ↓
6. Display question to player
   ↓
7. Player submits answer
   ↓
8. Answer validation checks question source:
   - api_ prefix → validate against API data
   - numeric → validate against database
   ↓
9. Points awarded (same calculation for both sources)
   ↓
10. Result recorded in game session
```

#### **API Response Structure Comparison**

**Local Database Question:**
```json
{
  "id": 42,
  "question_text": "Who invented the first computer?",
  "option_a": "Charles Babbage",
  "option_b": "Alan Turing",
  "option_c": "Ada Lovelace",
  "option_d": "Grace Hopper",
  "correct_option": "A",
  "difficulty": "Easy"
}
```

**External API Question (converted format):**
```json
{
  "id": "api_7d8e3f2a",
  "question": "What does CPU stand for?",
  "choices": ["Central Processing Unit", "Computer Personal Unit", "Central Program Utility", "CPU Program Unit"],
  "correct_answer": "Central Processing Unit",
  "difficulty": "Easy",
  "source": "api"
}
```

---

## 🔒 Security Architecture

### **Authentication & Authorization**

<div align="center">

![Encryption](https://img.shields.io/badge/Encryption-256bit-FF6B6B?style=for-the-badge)
![HTTPS](https://img.shields.io/badge/HTTPS-Secure-4ECDC4?style=for-the-badge)
![2FA](https://img.shields.io/badge/2FA-Enabled-95E1D3?style=for-the-badge)

</div>

#### **JWT Token System**
```php
// Token issued on login
Header: Authorization: Bearer <JWT_TOKEN>
Payload: { user_id, email, issued_at, expires_at }
Signature: HMAC-SHA256 encrypted with secret key
```

#### **Password Security**
- 🔐 **Algorithm:** bcrypt (PHP's password_hash())
- 🔢 **Cost Factor:** 10 (adjustable for performance)
- 🧂 **Salt:** Automatically generated per password
- ✓ **Verification:** password_verify() function

#### **Session Management**
- 🔒 Secure PHP sessions with secure cookie flags
- 🚫 HttpOnly flag prevents JavaScript access
- 🔐 Secure flag ensures HTTPS transmission
- 🎯 SameSite attribute prevents CSRF attacks

### **Data Protection**

#### **SQL Injection Prevention**
```php
// ❌ Vulnerable
$query = "SELECT * FROM users WHERE email = '$email'";

// ✅ Secure (using prepared statements)
$query = "SELECT * FROM users WHERE email = ?";
$stmt = $database->executeQuery($query, [$email]);
```

#### **XSS (Cross-Site Scripting) Prevention**
```php
// Input sanitization
htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8')

// Output encoding
echo htmlentities($dynamic_content);
```

#### **CSRF (Cross-Site Request Forgery) Protection**
- 🛡️ Token generation and validation
- 📍 Same-origin policy enforcement
- 🔄 SameSite cookie attributes

### **Input Validation**

- ✉️ Email format validation with filter_var()
- 🔐 Password strength checking (minimum 6 chars)
- 📋 Option selection verification (A/B/C/D only)
- 🔢 Numeric input type casting
- 📏 String length limits

### **Database Security**

- 🌍 UTF-8 character encoding
- 🔓 Row-level access control
- 🔗 Foreign key constraints
- ✔️ Data integrity checks
- 📅 Automatic timestamp tracking

### **2FA Security**

- 📱 TOTP (Time-based One-Time Password) RFC 6238 compliant
- ⏱️ 30-second time window with 1-window tolerance
- 🔢 6-digit codes preventing brute force
- 🔄 Backup codes for account recovery
- 📲 QR code generation for authenticator apps

### **Deployment Security Checklist**

- [ ] Set `display_errors = Off` in production
- [ ] Use HTTPS/SSL certificates
- [ ] Enable PHP security headers
- [ ] Configure firewall rules
- [ ] Regular security updates
- [ ] Database backups
- [ ] Access logging
- [ ] Rate limiting on authentication endpoints

---

## 📁 Project Structure

```
computing-quiz-game/
├── 📄 index.php                    # Landing page & welcome screen
├── 📄 README.md                    # This file
│
├── 📂 api/                         # RESTful API endpoints
│   ├── init_session.php            # Create new game session
│   ├── get_question.php            # Fetch current question
│   ├── submit_answer.php           # Process answer submission
│   ├── save_score.php              # Persist final score
│   ├── get_lifelines.php           # Check lifeline status
│   ├── use_lifeline.php            # Consume a lifeline
│   ├── award_lifeline.php          # Grant lifeline from banana game
│   ├── fetch_api_questions.php     # Fetch from Open Trivia DB API
│   ├── game/                       # Game-specific endpoints
│   └── lifelines/                  # Lifeline operation handlers
│
├── 📂 auth/                        # Authentication & authorization
│   ├── register.php                # User registration form & logic
│   ├── login.php                   # User login form & logic
│   ├── logout.php                  # Session termination
│   ├── setup-2fa.php               # 2FA enrollment
│   ├── verify-2fa.php              # 2FA code verification
│   ├── migrate-2fa.php             # 2FA settings migration
│   └── debug-2fa.php               # 2FA debugging utilities
│
├── 📂 pages/                       # User-facing pages
│   ├── game.php                    # Main quiz interface
│   ├── banana-game.php             # Pattern puzzle game
│   ├── leaderboard.php             # Global rankings display
│   └── settings.php                # User account settings
│
├── 📂 includes/                    # Reusable PHP classes & functions
│   ├── auth.php                    # Auth class (login/register/jwt)
│   ├── jwt.php                     # JWT token handling
│   ├── two-factor-auth.php         # 2FA/TOTP implementation
│   └── banana-game-access.php      # Banana game integration
│
├── 📂 config/                      # Configuration files
│   └── database.php                # Database connection class
│
├── 📂 database/                    # Database schemas & migrations
│   └── schema.sql                  # Complete database schema
│
├── 📂 assets/                      # Frontend resources
│   ├── css/
│   │   ├── style.css               # Main stylesheet
│   │   └── dark-theme.css          # Dark theme styling
│   ├── js/
│   │   ├── game.js                 # Game logic & UI interactions
│   │   ├── lifeline.js             # Lifeline mechanics
│   │   ├── timer.js                # Countdown timer module
│   │   └── sounds.js               # Audio playback management
│   ├── sounds/                     # Audio files (mp3/wav)
│   └── images/                     # Image assets
│
└── 📂 setup/                       # Installation & setup guides
    └── setup-guide.php             # 2FA setup documentation
```

---

## 🤝 Contributing

We welcome contributions! Here's how to get involved:

### **Reporting Issues**
- 🔍 Check existing issues first
- 📝 Provide detailed reproduction steps
- ⚠️ Include error messages and logs
- 📸 Screenshot for UI-related issues

### **Suggesting Features**
- 💡 Describe the feature clearly
- 🎯 Explain the use case
- 🔧 Suggest implementation approach
- 🔄 Consider backward compatibility

### **Submitting Code**
1. 🍴 Fork the repository
2. 🌱 Create a feature branch (`git checkout -b feature/amazing-feature`)
3. ✏️ Make your changes with clear commits
4. 🧪 Test thoroughly (functionality & security)
5. 📤 Submit a pull request with detailed description

### **Areas for Contribution**

<div align="center">

| Area | Icon | Description |
|:---:|:---:|:---|
| Questions | ✏️ | Add new quiz questions |
| Design | 🎨 | UI/UX improvements |
| Bugs | 🐛 | Bug fixes and issue resolution |
| Docs | 📖 | Documentation improvements |
| I18n | 🌐 | Internationalization (i18n) |
| Performance | ⚡ | Performance optimization |
| Security | 🔐 | Security enhancements |
| Tests | 🧪 | Unit tests and coverage |

</div>

---

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

**Quick Summary:**
- ✅ Use for commercial & personal projects
- ✅ Modify and distribute
- ✅ Include in proprietary software
- ⚠️ Include license notice
- ⚠️ No warranty provided

---

## 👥 Author & Contributors

### **Primary Developer**

<div align="center">
  <a href="https://github.com/prasadew" target="_blank">
    <img src="https://github.com/prasadew.png?size=150" alt="Prasasthi Dewasurendra" width="150" height="150" style="border-radius: 50%; border: 4px solid #00d4ff; box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);" />
  </a>
  <h3><a href="https://github.com/prasadew" style="text-decoration: none; color: #00d4ff;">Prasasthi Dewasurendra</a></h3>
  <p><strong>Full Stack Developer | Project Architect</strong></p>
  <p>Complete project development, architecture design, implementation, and maintenance</p>
  <br/>
  <a href="https://github.com/prasadew?tab=repositories" style="display: inline-block; padding: 10px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 5px; margin: 5px;">View GitHub Profile</a>
</div>

---

### **Community & Contributors**

We welcome contributions from the open source community! If you've contributed to this project, please open an issue or PR to be added here.

---

## 🙏 Acknowledgments

<div align="center">

### **Special Thanks To**

</div>

- 🎮 **[Marc Conrad](https://marcconrad.com)** - Brilliant pattern puzzle game integration (Banana Game™)
- 📚 **Open Source Community** - For excellent PHP, MySQL, and JavaScript libraries
- 🌐 **[OpenTDB](https://opentdb.com)** - Free and open-source trivia database API
- 💡 **Computing History Resources** - For comprehensive question content and historical accuracy
- 👥 **All Contributors** - For bug reports, suggestions, and code improvements
- 🎯 **Stack Overflow & GitHub Communities** - For guidance and best practices

---

## 📞 Support & Contact

For questions, issues, or suggestions:
- 🐛 Open an issue on the [GitHub repository](https://github.com/prasadew/computing-quiz-game/issues)
- 💬 Contact the development team
- 🔗 Visit the [author's GitHub profile](https://github.com/prasadew)

---

## 🎓 Learning Resources

Interested in understanding the codebase?
- Start with `index.php` for entry point
- Review `config/database.php` for database patterns
- Study `includes/auth.php` for authentication flow
- Explore `assets/js/game.js` for game logic
- Check `api/` directory for API design patterns

---

<div align="center">
<br/>

**Made with ❤️ by [Prasasthi Dewasurendra](https://github.com/prasadew)**

<a href="https://github.com/prasadew/computing-quiz-game" style="display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 10px 5px;">⭐ Star This Repo</a>
<a href="https://github.com/prasadew/computing-quiz-game/fork" style="display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 10px 5px;">🍴 Fork This Repo</a>

<br/><br/>

![Last Updated](https://img.shields.io/badge/Last%20Updated-December%202025-blue?style=flat-square)
![Version](https://img.shields.io/badge/Version-2.0.0-green?style=flat-square)
![Status](https://img.shields.io/badge/Status-Production%20Ready%20✅-brightgreen?style=flat-square)

</div>

---

**Last Updated:** December 2025  
**Version:** 2.0.0  
**Status:** Production Ready ✅
