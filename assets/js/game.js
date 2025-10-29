// Sample hardcoded questions
const sampleQuestions = {
    Easy: [
        {
            question: "Who is known as the father of modern computer science?",
            options: ["Alan Turing", "Bill Gates", "Steve Jobs", "Mark Zuckerberg"],
            correct_answer: 0
        },
        {
            question: "What does CPU stand for?",
            options: ["Central Processing Unit", "Central Program Utility", "Computer Personal Unit", "Central Process Utility"],
            correct_answer: 0
        },
        {
            question: "In which decade was the first email sent?",
            options: ["1970s", "1960s", "1980s", "1990s"],
            correct_answer: 0
        }
    ],
    Medium: [
        {
            question: "Which programming language was created by James Gosling?",
            options: ["Java", "Python", "C++", "JavaScript"],
            correct_answer: 0
        },
        {
            question: "What was the name of the first computer mouse?",
            options: ["XY Position Indicator", "Pointing Device", "Track Ball", "Mouse 1.0"],
            correct_answer: 0
        },
        {
            question: "Which company developed the first commercial GUI?",
            options: ["Xerox", "Apple", "Microsoft", "IBM"],
            correct_answer: 0
        }
    ],
    Hard: [
        {
            question: "What was the name of the first computer virus?",
            options: ["Creeper", "ILOVEYOU", "Morris Worm", "Melissa"],
            correct_answer: 0
        },
        {
            question: "What was the clock speed of the Intel 8086 processor?",
            options: ["4.77 MHz", "8 MHz", "1 MHz", "2.5 MHz"],
            correct_answer: 0
        },
        {
            question: "Who invented the first compiler?",
            options: ["Grace Hopper", "Ada Lovelace", "Donald Knuth", "John von Neumann"],
            correct_answer: 0
        }
    ]
};

// Game state
let currentQuestions = [];
let currentQuestionIndex = 0;
let score = 0;
let timer;
let timeLeft;
let difficulty = '';
let lifelines = {
    addTime: 3,
    fiftyFifty: 3,
    skip: 3
};

// Start game with selected difficulty
function startGame(selectedDifficulty) {
    difficulty = selectedDifficulty;
    difficultyScreen.classList.add('hidden');
    gameScreen.classList.remove('hidden');
    
    // Set time per question based on difficulty
    switch(difficulty) {
        case 'Easy':
            timeLeft = 30;
            break;
        case 'Medium':
            timeLeft = 20;
            break;
        case 'Hard':
            timeLeft = 15;
            break;
    }
    
    // Use sample questions
    currentQuestions = sampleQuestions[difficulty];
    displayQuestion();
    startTimer();
}

// Display current question
function displayQuestion() {
    if (currentQuestionIndex >= currentQuestions.length) {
        endGame();
        return;
    }
    
    const question = currentQuestions[currentQuestionIndex];
    questionText.textContent = question.question;
    
    optionsGrid.innerHTML = '';
    question.options.forEach((option, index) => {
        const button = document.createElement('button');
        button.className = 'option-btn';
        button.textContent = option;
        button.onclick = () => checkAnswer(index);
        optionsGrid.appendChild(button);
    });
}

// Timer function
function startTimer() {
    clearInterval(timer);
    timerElement.textContent = timeLeft;
    
    timer = setInterval(() => {
        timeLeft--;
        timerElement.textContent = timeLeft;
        
        if (timeLeft <= 5) {
            tickSound.play();
        }
        
        if (timeLeft <= 0) {
            clearInterval(timer);
            wrongAnswer();
        }
    }, 1000);
}

// Check answer
function checkAnswer(selectedIndex) {
    clearInterval(timer);
    const question = currentQuestions[currentQuestionIndex];
    
    if (selectedIndex === question.correct_answer) {
        correctAnswer();
    } else {
        wrongAnswer();
    }
}

// Correct answer handler
function correctAnswer() {
    correctSound.play();
    
    // Calculate bonus points based on time left
    let bonus = 0;
    if (timeLeft >= Math.floor(getMaxTime() * 0.75)) {
        bonus = 10;
    } else if (timeLeft >= Math.floor(getMaxTime() * 0.5)) {
        bonus = 5;
    }
    
    // Base points based on difficulty
    let points = difficulty === 'Easy' ? 10 : (difficulty === 'Medium' ? 20 : 30);
    points += bonus;
    
    score += points;
    currentScoreElement.textContent = score;
    document.getElementById('totalScore').textContent = score;
    
    nextQuestion();
}

// Wrong answer handler
function wrongAnswer() {
    wrongSound.play();
    nextQuestion();
}

// Get maximum time based on difficulty
function getMaxTime() {
    switch(difficulty) {
        case 'Easy':
            return 30;
        case 'Medium':
            return 20;
        case 'Hard':
            return 15;
    }
}

// Use lifeline
function useLifeline(type) {
    if (lifelines[type] <= 0) return;
    
    lifelines[type]--;
    document.getElementById(`${type}Count`).textContent = lifelines[type];
    
    switch(type) {
        case 'addTime':
            timeLeft += 10;
            timerElement.textContent = timeLeft;
            break;
            
        case 'fiftyFifty':
            const question = currentQuestions[currentQuestionIndex];
            const correctAnswer = question.correct_answer;
            const buttons = optionsGrid.children;
            
            // Count how many wrong options we've hidden
            let hiddenCount = 0;
            for (let i = 0; i < buttons.length; i++) {
                if (i !== correctAnswer && hiddenCount < 2) {
                    buttons[i].style.visibility = 'hidden';
                    hiddenCount++;
                }
            }
            break;
            
        case 'skip':
            nextQuestion();
            break;
    }
}

// Move to next question
function nextQuestion() {
    currentQuestionIndex++;
    
    if (currentQuestionIndex < currentQuestions.length) {
        timeLeft = getMaxTime();
        displayQuestion();
        startTimer();
    } else {
        endGame();
    }
}

// End game
function endGame() {
    gameOverSound.play();
    clearInterval(timer);
    
    finalScoreElement.textContent = score;
    questionsAnsweredElement.textContent = currentQuestionIndex;
    
    // Show banana game option if all lifelines are used
    const bananaGameOption = document.getElementById('bananaGameOption');
    if (Object.values(lifelines).every(count => count === 0)) {
        bananaGameOption.classList.remove('hidden');
    }
    
    gameOverModal.style.display = 'flex';
    
    // Log score for demo
    console.log('Game Over! Final Score:', score);
}