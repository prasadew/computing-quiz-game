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
    // track how many times banana has been used (server-side counter reflected here)
    bananaUses: 0,
    isAnswering: false
};

// When true, the final score has not yet been saved to the server and
// the session remains open so the player can attempt the banana game.
gameState.pendingSave = false;

// Fetch current lifeline status from server
async function fetchLifelines() {
    try {
        const response = await fetch(`api/get_lifelines.php?session_id=${gameState.sessionId}`, {
            credentials: 'same-origin'
        });
        const data = await response.json();
        
        if (data.success) {
            gameState.lifelines = {
                addTime: data.lifelines.add_time_remaining,
                fiftyFifty: data.lifelines.fifty_fifty_remaining,
                skip: data.lifelines.skip_remaining
            };
            // banana usage counter from server (may be 0,1 or 2)
            gameState.bananaUses = parseInt(data.lifelines.banana_used || 0, 10);
            
            // Update UI
            document.getElementById('addTimeCount').textContent = data.lifelines.add_time_remaining;
            document.getElementById('fiftyFiftyCount').textContent = data.lifelines.fifty_fifty_remaining;
            document.getElementById('skipCount').textContent = data.lifelines.skip_remaining;
            
            // Update button states
            document.getElementById('addTimeBtn').disabled = data.lifelines.add_time_remaining === 0;
            document.getElementById('fiftyFiftyBtn').disabled = data.lifelines.fifty_fifty_remaining === 0;
            document.getElementById('skipBtn').disabled = data.lifelines.skip_remaining === 0;
        }
    } catch (error) {
        console.error('Error fetching lifelines:', error);
    }
}

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
    // bananaUses is tracked from server; no local boolean needed here
    
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
            credentials: 'same-origin',
            body: JSON.stringify({
                session_id: gameState.sessionId,
                difficulty: gameState.difficulty
            })
        });

        const data = await response.json();
        if (!data.success) {
            console.error('Failed to initialize session:', data.message);
        } else {
            // Fetch lifelines for the newly created session so UI reflects banana usage and counts
            try {
                await fetchLifelines();
            } catch (err) {
                console.warn('Could not fetch lifelines after session init:', err);
            }
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

        // Alternate between local DB and API
        const useApi = (gameState.questionsAnswered % 2 === 1); // alternate every other question
        const endpoint = useApi
            ? `api/fetch_api_questions.php?difficulty=${gameState.difficulty}`
            : `api/get_question.php?difficulty=${gameState.difficulty}&session_id=${gameState.sessionId}`;

        const response = await fetch(endpoint, {
            credentials: 'same-origin'
        });
        console.log('API Response status:', response.status);

        const data = await response.json();
        console.log('Question API response:', data);

        if (!data.success) {
            console.error('Failed to load question:', data.message);
            endGame(data.message || 'No more questions available!');
            return;
        }

        // Handle both local and API formats
        let questionObj = {};
        if (data.question.question_text) {
            // Local DB format
            questionObj = data.question;
        } else if (data.question.question) {
            // API format
            // Map choices to option_a, option_b, ...
            const shuffledChoices = [...data.question.choices];
            // Shuffle choices for randomness
            for (let i = shuffledChoices.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [shuffledChoices[i], shuffledChoices[j]] = [shuffledChoices[j], shuffledChoices[i]];
            }
            questionObj = {
                id: data.question.id,
                question_text: data.question.question,
                option_a: shuffledChoices[0],
                option_b: shuffledChoices[1],
                option_c: shuffledChoices[2],
                option_d: shuffledChoices[3],
                correct_answer: data.question.correct_answer,
                correct_option: ['A','B','C','D'][shuffledChoices.indexOf(data.question.correct_answer)]
            };
        } else {
            console.error('Invalid question data received:', data);
            endGame('Error: Invalid question data');
            return;
        }

        gameState.currentQuestion = questionObj;
        console.log('Displaying question:', questionObj.question_text);
        displayQuestion(questionObj);
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
        // Prepare payload
        const payload = {
            session_id: gameState.sessionId,
            question_id: gameState.currentQuestion.id,
            selected_option: selectedOption,
            time_taken: getTimeTaken()
        };
        // If API question, send correct_option and difficulty for backend validation
        if (String(gameState.currentQuestion.id).startsWith('api_')) {
            payload.correct_option = gameState.currentQuestion.correct_option;
            payload.difficulty = gameState.currentQuestion.difficulty || gameState.difficulty;
        }
        const response = await fetch('api/submit_answer.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify(payload)
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
            credentials: 'same-origin',
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

    // Check if returning from successful banana game
    const bananaGameSuccess = sessionStorage.getItem('bananaGameSuccess');
    if (bananaGameSuccess) {
        sessionStorage.removeItem('bananaGameSuccess');
        // Reset game state for continuation: fetch lifelines and consume awarded skip if present
        await fetchLifelines();
        if (gameState.lifelines.skip > 0) {
            await useLifeline('skip');
        } else {
            loadQuestion();
        }
        return;
    }

    // Defer saving final score so player can play Banana Game and resume
    // The score will be saved only when the player chooses to finish (Play Again / View Leaderboard)
    gameState.pendingSave = true;

    // Update modal
    document.getElementById('gameOverTitle').innerHTML = reason.includes('Wrong') || reason.includes('Time') 
        ? '😢 Game Over!' 
        : '🎉 Congratulations!';
    document.getElementById('finalScore').textContent = gameState.currentScore;
    document.getElementById('questionsAnswered').textContent = gameState.questionsAnswered;

    // Only show banana game option if NOT a congratulations ending
    const isCongrats = document.getElementById('gameOverTitle').innerHTML.includes('Congratulations');
    if (!isCongrats && gameState.bananaUses < 2) {
        document.getElementById('bananaGameOption').classList.remove('hidden');
    } else {
        document.getElementById('bananaGameOption').classList.add('hidden');
    }

    // Show modal
    document.getElementById('gameOverModal').classList.add('active');
    playSound('gameOver');

    // Attach handlers to modal action buttons so score is saved before navigation
    try {
        const playAgainBtn = Array.from(document.querySelectorAll('button')).find(b => b.textContent && b.textContent.includes('Play Again'));
        const leaderboardBtn = Array.from(document.querySelectorAll('button')).find(b => b.textContent && b.textContent.includes('View Leaderboard'));

        if (playAgainBtn) {
            // Remove existing inline handler if any and attach our handler
            playAgainBtn.onclick = async (e) => {
                // Save score, then reload to start a fresh game
                if (gameState.pendingSave) {
                    await saveFinalScore();
                    gameState.pendingSave = false;
                }
                location.reload();
            };
        }

        if (leaderboardBtn) {
            leaderboardBtn.onclick = async (e) => {
                if (gameState.pendingSave) {
                    await saveFinalScore();
                    gameState.pendingSave = false;
                }
                window.location.href = 'leaderboard.php';
            };
        }
    } catch (err) {
        console.error('Error attaching gameOver modal handlers:', err);
    }
}

// Save final score to database
async function saveFinalScore() {
    try {
        const response = await fetch('api/save_score.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin',
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
    // Preserve minimal game state so we can resume after the banana game
    try {
        sessionStorage.setItem('bananaPreviousSessionId', gameState.sessionId);
        sessionStorage.setItem('bananaPreviousDifficulty', gameState.difficulty);
        sessionStorage.setItem('bananaPrevScore', String(gameState.currentScore));
        sessionStorage.setItem('bananaPrevQuestionsAnswered', String(gameState.questionsAnswered));
    } catch (err) {
        console.warn('Could not persist banana previous state:', err);
    }

    // bananaUses is controlled by server; no local boolean required
    window.location.href = 'banana-game.php?session_id=' + gameState.sessionId;
}

// On page load, check if we returned from the banana game successfully and resume
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const success = sessionStorage.getItem('bananaGameSuccess');
        if (!success) return;

        // Clear the success flag so subsequent loads don't auto-resume
        sessionStorage.removeItem('bananaGameSuccess');

        const prevSessionId = sessionStorage.getItem('bananaPreviousSessionId');
        const prevDifficulty = sessionStorage.getItem('bananaPreviousDifficulty');
        const prevScore = parseInt(sessionStorage.getItem('bananaPrevScore') || '0', 10);
        const prevQuestionsAnswered = parseInt(sessionStorage.getItem('bananaPrevQuestionsAnswered') || '0', 10);

        // Remove persisted previous state after reading
        sessionStorage.removeItem('bananaPreviousSessionId');
        sessionStorage.removeItem('bananaPreviousDifficulty');
        sessionStorage.removeItem('bananaPrevScore');
        sessionStorage.removeItem('bananaPrevQuestionsAnswered');

        if (!prevSessionId || !prevDifficulty) {
            console.warn('Banana game returned but previous session info missing.');
            return;
        }

        // Restore minimal game state
        gameState.sessionId = prevSessionId;
        gameState.difficulty = prevDifficulty;
        gameState.currentScore = isNaN(prevScore) ? 0 : prevScore;
        gameState.questionsAnswered = isNaN(prevQuestionsAnswered) ? 0 : prevQuestionsAnswered;

        // Update UI to show the game screen and scores
        const difficultyScreen = document.getElementById('difficultyScreen');
        const gameScreen = document.getElementById('gameScreen');
        if (difficultyScreen && gameScreen) {
            difficultyScreen.classList.add('hidden');
            gameScreen.classList.remove('hidden');
        }

        updateScore();

        // Fetch lifelines from server. If the banana game awarded a skip, consume it
        // immediately so banana acts as a skip and the player continues the quiz.
        await fetchLifelines();
        if (gameState.lifelines.skip > 0) {
            // consume the awarded skip and load the next question
            await useLifeline('skip');
        } else {
            // fallback: just load the next question
            loadQuestion();
        }
    } catch (err) {
        console.error('Error while resuming after banana game:', err);
    }
});

// Restore lifeline after banana game success
function restoreLifeline() {
    // Find first empty lifeline to restore
    if (gameState.lifelines.addTime === 0) {
        gameState.lifelines.addTime = 1;
        document.getElementById('addTimeBtn').disabled = false;
        document.getElementById('addTimeCount').textContent = '1';
    } else if (gameState.lifelines.fiftyFifty === 0) {
        gameState.lifelines.fiftyFifty = 1;
        document.getElementById('fiftyFiftyBtn').disabled = false;
        document.getElementById('fiftyFiftyCount').textContent = '1';
    } else if (gameState.lifelines.skip === 0) {
        gameState.lifelines.skip = 1;
        document.getElementById('skipBtn').disabled = false;
        document.getElementById('skipCount').textContent = '1';
    }
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