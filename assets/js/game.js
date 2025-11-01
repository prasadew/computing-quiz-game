// Game Logic - assets/js/game.js

// Game State
let gameState = {
    difficulty: '',
    sessionId: '',
    currentQuestion: null,
    currentScore: 0,
    questionsAnswered: 0,
    timeRemaining: 20,
    timerInterval: null,
    lifelines: {
        addTime: 3,
        fiftyFifty: 3,
        skip: 3
    },
    bananaGameUsed: false,
    isAnswering: false
};

// Initialize game
function startGame(difficulty) {
    console.log('startGame called with difficulty:', difficulty);
    
    // Ensure screens exist
    const difficultyScreen = document.getElementById('difficultyScreen');
    const gameScreen = document.getElementById('gameScreen');
    
    if (!difficultyScreen || !gameScreen) {
        console.error('Required screens not found:', { 
            difficultyScreen: !!difficultyScreen, 
            gameScreen: !!gameScreen 
        });
        return;
    }

    // Initialize game state
    gameState.difficulty = difficulty;
    gameState.sessionId = generateSessionId();
    gameState.currentScore = 0;
    gameState.questionsAnswered = 0;
    gameState.bananaGameUsed = false;
    
    // Set time based on difficulty
    switch(difficulty) {
        case 'Easy':
            gameState.timeRemaining = 30;
            break;
        case 'Medium':
            gameState.timeRemaining = 20;
            break;
        case 'Hard':
            gameState.timeRemaining = 15;
            break;
    }

    // Hide difficulty screen, show game screen
    document.getElementById('difficultyScreen').classList.add('hidden');
    document.getElementById('gameScreen').classList.remove('hidden');

    // Initialize session on server
    initializeSession();

    // Load first question
    loadQuestion();
}

// Generate unique session ID
function generateSessionId() {
    return 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
}

// Initialize game session on server
async function initializeSession() {
    try {
        const response = await fetch('api/init_session.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                session_id: gameState.sessionId,
                difficulty: gameState.difficulty
            })
        });

        const data = await response.json();
        if (!data.success) {
            console.error('Failed to initialize session:', data.message);
        }
    } catch (error) {
        console.error('Error initializing session:', error);
    }
}

// Load question from API
async function loadQuestion() {
    try {
        console.log('Loading question for:', {
            difficulty: gameState.difficulty,
            sessionId: gameState.sessionId
        });

        const response = await fetch(`api/get_question.php?difficulty=${gameState.difficulty}&session_id=${gameState.sessionId}`);
        console.log('API Response status:', response.status);
        
        const data = await response.json();
        console.log('Question API response:', data);

        if (!data.success) {
            console.error('Failed to load question:', data.message);
            endGame(data.message || 'No more questions available!');
            return;
        }

        if (!data.question || !data.question.question_text) {
            console.error('Invalid question data received:', data);
            endGame('Error: Invalid question data');
            return;
        }

        gameState.currentQuestion = data.question;
        console.log('Displaying question:', data.question.question_text);
        displayQuestion(data.question);
        startTimer();
    } catch (error) {
        console.error('Error loading question:', error);
        endGame('Error loading question: ' + error.message);
    }
}

// Display question on screen
function displayQuestion(question) {
    document.getElementById('questionText').textContent = question.question_text;

    const optionsGrid = document.getElementById('optionsGrid');
    optionsGrid.innerHTML = '';

    const options = [
        { label: 'A', text: question.option_a },
        { label: 'B', text: question.option_b },
        { label: 'C', text: question.option_c },
        { label: 'D', text: question.option_d }
    ];

    options.forEach(option => {
        const button = document.createElement('button');
        button.className = 'option-btn';
        button.setAttribute('data-option', option.label);
        button.innerHTML = `<strong>${option.label}:</strong> ${option.text}`;
        button.onclick = () => selectAnswer(option.label);
        optionsGrid.appendChild(button);
    });

    gameState.isAnswering = false;
}

// Start countdown timer
function startTimer() {
    // Reset timer based on difficulty
    let baseTime;
    switch(gameState.difficulty) {
        case 'Easy':
            baseTime = 30;
            break;
        case 'Medium':
            baseTime = 20;
            break;
        case 'Hard':
            baseTime = 15;
            break;
    }
    
    gameState.timeRemaining = baseTime;
    updateTimerDisplay();

    clearInterval(gameState.timerInterval);
    gameState.timerInterval = setInterval(() => {
        gameState.timeRemaining--;
        updateTimerDisplay();

        // Play tick sound when time is low
        if (gameState.timeRemaining <= 5 && gameState.timeRemaining > 0) {
            playSound('tick');
        }

        if (gameState.timeRemaining <= 0) {
            clearInterval(gameState.timerInterval);
            timeUp();
        }
    }, 1000);
}

// Update timer display
function updateTimerDisplay() {
    const timerElement = document.getElementById('timer');
    timerElement.textContent = gameState.timeRemaining;

    if (gameState.timeRemaining <= 5) {
        timerElement.classList.add('warning');
    } else {
        timerElement.classList.remove('warning');
    }
}

// Handle answer selection
async function selectAnswer(selectedOption) {
    if (gameState.isAnswering) return;
    gameState.isAnswering = true;

    clearInterval(gameState.timerInterval);

    try {
        const response = await fetch('api/submit_answer.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                session_id: gameState.sessionId,
                question_id: gameState.currentQuestion.id,
                selected_option: selectedOption,
                time_taken: getTimeTaken()
            })
        });

        const data = await response.json();

        // Visual feedback
        const buttons = document.querySelectorAll('.option-btn');
        buttons.forEach(btn => {
            const option = btn.getAttribute('data-option');
            if (option === data.correct_option) {
                btn.classList.add('correct');
            }
            if (option === selectedOption && !data.is_correct) {
                btn.classList.add('wrong');
            }
            btn.disabled = true;
        });

        if (data.is_correct) {
            playSound('correct');
            gameState.currentScore += data.points_earned;
            document.getElementById('currentScore').textContent = gameState.currentScore;
        } else {
            playSound('wrong');
        }

        gameState.questionsAnswered++;

        // Wait before loading next question
        setTimeout(() => {
            if (data.is_correct) {
                loadQuestion();
            } else {
                endGame('Wrong answer!');
            }
        }, 2000);

    } catch (error) {
        console.error('Error submitting answer:', error);
        endGame('Error submitting answer');
    }
}

// Get time taken to answer
function getTimeTaken() {
    let baseTime;
    switch(gameState.difficulty) {
        case 'Easy': baseTime = 30; break;
        case 'Medium': baseTime = 20; break;
        case 'Hard': baseTime = 15; break;
    }
    return baseTime - gameState.timeRemaining;
}

// Handle time up
function timeUp() {
    if (gameState.isAnswering) return;

    playSound('wrong');
    endGame('Time\'s up!');
}

// Use lifeline
async function useLifeline(type) {
    if (gameState.lifelines[type] <= 0) return;
    if (gameState.isAnswering) return;

    gameState.lifelines[type]--;
    document.getElementById(`${type}Count`).textContent = gameState.lifelines[type];

    if (gameState.lifelines[type] === 0) {
        document.getElementById(`${type}Btn`).disabled = true;
    }

    switch(type) {
        case 'addTime':
            gameState.timeRemaining += 10;
            updateTimerDisplay();
            break;

        case 'fiftyFifty':
            fiftyFiftyLifeline();
            break;

        case 'skip':
            clearInterval(gameState.timerInterval);
            gameState.questionsAnswered++;
            loadQuestion();
            break;
    }

    // Update lifeline on server
    await updateLifelineOnServer(type);
}

// 50/50 Lifeline - Remove 2 wrong answers
function fiftyFiftyLifeline() {
    const correctOption = gameState.currentQuestion.correct_option;
    const buttons = Array.from(document.querySelectorAll('.option-btn'));
    
    const wrongButtons = buttons.filter(btn => 
        btn.getAttribute('data-option') !== correctOption
    );

    // Randomly select 2 wrong answers to remove
    const toRemove = wrongButtons.sort(() => 0.5 - Math.random()).slice(0, 2);
    toRemove.forEach(btn => {
        btn.style.opacity = '0.3';
        btn.style.pointerEvents = 'none';
        btn.disabled = true;
    });
}

// Update lifeline count on server
async function updateLifelineOnServer(type) {
    try {
        await fetch('api/use_lifeline.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                session_id: gameState.sessionId,
                lifeline_type: type
            })
        });
    } catch (error) {
        console.error('Error updating lifeline:', error);
    }
}

// End game
async function endGame(reason) {
    clearInterval(gameState.timerInterval);

    // Save final score
    await saveFinalScore();

    // Update modal
    document.getElementById('gameOverTitle').innerHTML = reason.includes('Wrong') || reason.includes('Time') 
        ? '😢 Game Over!' 
        : '🎉 Congratulations!';
    document.getElementById('finalScore').textContent = gameState.currentScore;
    document.getElementById('questionsAnswered').textContent = gameState.questionsAnswered;

    // Show banana game option if all lifelines used and not used before
    const allLifelinesUsed = gameState.lifelines.addTime === 0 && 
                             gameState.lifelines.fiftyFifty === 0 && 
                             gameState.lifelines.skip === 0;
    
    if (allLifelinesUsed && !gameState.bananaGameUsed) {
        document.getElementById('bananaGameOption').classList.remove('hidden');
    }

    // Show modal
    document.getElementById('gameOverModal').classList.add('active');
    playSound('gameOver');
}

// Save final score to database
async function saveFinalScore() {
    try {
        const response = await fetch('api/save_score.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                session_id: gameState.sessionId,
                score: gameState.currentScore,
                difficulty: gameState.difficulty,
                questions_answered: gameState.questionsAnswered
            })
        });

        const data = await response.json();
        if (data.success) {
            // Update total score display
            document.getElementById('totalScore').textContent = data.new_total_score;
        }
    } catch (error) {
        console.error('Error saving score:', error);
    }
}

// Play Banana Game
function playBananaGame() {
    window.location.href = 'banana-game.php?session_id=' + gameState.sessionId;
}

// Play sound effect
function playSound(type) {
    const sound = document.getElementById(type + 'Sound');
    if (sound) {
        sound.currentTime = 0;
        sound.play().catch(err => console.log('Audio play failed:', err));
    }
}

// Update score display
function updateScore() {
    document.getElementById('currentScore').textContent = gameState.currentScore;
}

// Attach click handlers to difficulty cards as a fallback/robust handler
// Initialize difficulty card handlers
function initializeDifficultyCards() {
    try {
        // Clear any existing listeners first
        const cards = document.querySelectorAll('.difficulty-card');
        cards.forEach(card => {
            const newCard = card.cloneNode(true);
            card.parentNode.replaceChild(newCard, card);
        });

        // Reattach click handlers
        document.querySelectorAll('.difficulty-card').forEach(card => {
            const clickHandler = (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                // Get difficulty from class
                let diff = '';
                if (card.classList.contains('easy')) diff = 'Easy';
                else if (card.classList.contains('medium')) diff = 'Medium';
                else if (card.classList.contains('hard')) diff = 'Hard';

                console.log('Difficulty card clicked, starting game with:', diff);
                
                // Start the game
                startGame(diff);
                
                // Double-check visibility
                setTimeout(() => {
                    const diffScreen = document.getElementById('difficultyScreen');
                    const gameScreen = document.getElementById('gameScreen');
                    console.log('Screen visibility check:', {
                        'difficultyScreen.hidden': diffScreen.classList.contains('hidden'),
                        'gameScreen.hidden': gameScreen.classList.contains('hidden'),
                        'difficultyScreen.style.display': window.getComputedStyle(diffScreen).display,
                        'gameScreen.style.display': window.getComputedStyle(gameScreen).display
                    });
                }, 100);
            };

            // Add the click handler
            card.addEventListener('click', clickHandler);
            card.style.cursor = 'pointer';
            
            // Make the entire card and its contents clickable
            card.style.pointerEvents = 'auto';
            Array.from(card.children).forEach(child => {
                child.style.pointerEvents = 'auto';
            });
        });
        
        console.log('Difficulty listeners initialized:', document.querySelectorAll('.difficulty-card').length);
    } catch (err) {
        console.error('Error initializing difficulty cards:', err);
    }
}

// Initialize on DOMContentLoaded and after any dynamic updates
document.addEventListener('DOMContentLoaded', initializeDifficultyCards);

// Global debug click logger to help identify overlay or blocking elements
// (temporary - remove after debugging)
document.addEventListener('click', (e) => {
    try {
        console.log('Global click event target:', e.target);
        const topEl = document.elementFromPoint(e.clientX, e.clientY);
        console.log('Top element at click position (elementFromPoint):', topEl);

        // If the clicked element is not a difficulty-card or a child, log ancestors
        const difficultyCard = e.target.closest ? e.target.closest('.difficulty-card') : null;
        if (!difficultyCard) {
            console.warn('Clicked target is NOT inside a .difficulty-card');
        } else {
            console.log('Clicked inside difficulty-card:', difficultyCard.className);
        }
    } catch (err) {
        console.error('Error in global click logger:', err);
    }
});

document.addEventListener('pointerdown', (e) => {
    // also log pointerdown which fires earlier than click
    try {
        console.log('Pointerdown at', e.clientX, e.clientY, 'target:', e.target);
    } catch (err) {
        console.error('Error logging pointerdown:', err);
    }
});