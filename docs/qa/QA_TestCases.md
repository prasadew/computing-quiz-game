# Detailed Test Cases

See test IDs and steps below. These map to the automated tests in `tests/test_api.py` and the manual UI flows.

AUTH-01: Register new user
- Preconditions: DB is available
- Steps: POST form to `/register.php` with name/email/password/confirm_password
- Expected: `auth_token` cookie set, redirect to `game.php`

SESSION-01: Initialize session
- Preconditions: Auth cookie present
- Steps: POST `/api/init_session.php` JSON { session_id, difficulty }
- Expected: success true, `game_sessions` and `lifelines` rows created

Q-01: Get question
- Preconditions: session initialized
- Steps: GET `/api/get_question.php?difficulty=Easy&session_id=...`
- Expected: success true with question object, `session_questions` entry inserted

ANS-01: Submit correct answer
- Steps: POST `/api/submit_answer.php` JSON with session_id, question_id, selected_option (correct), time_taken
- Expected: success true, is_correct true, points_earned = base + bonus

LIF-01: Use lifeline
- Steps: POST `/api/use_lifeline.php` JSON { session_id, lifeline_type }
- Expected: success true and remaining_lifelines reflect decrement (current behavior)

AWD-01: Award banana lifeline
- Steps: POST `/api/award_lifeline.php` JSON { session_id }
- Expected: success true up to 2 times; 3rd attempt returns success false

SCORE-01: Save score
- Steps: POST `/api/save_score.php` JSON { session_id, score, difficulty, questions_answered }
- Expected: success true, new_total_score present, session marked inactive
