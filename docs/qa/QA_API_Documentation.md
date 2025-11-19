# API Documentation — Computing Quiz Game

All endpoints expect authentication via cookie `auth_token` or Authorization Bearer header.

Base path: `/api/`

1) POST `/api/init_session.php`
- Payload: { session_id: string, difficulty: string }
- Success: { success: true, message: 'Session initialized successfully' }
- Errors: missing params, DB error

2) GET `/api/get_question.php`?difficulty=&session_id=
- Response: { success:true, question: { id, question_text, option_a.., correct_option, difficulty } }
- Errors: missing params, no more questions available

3) POST `/api/submit_answer.php`
- Payload: { session_id, question_id, selected_option, time_taken }
- Response: { success:true, is_correct: bool, correct_option: 'A'|'B'|'C'|'D', points_earned: int }
- Points: Easy 10, Medium 20, Hard 30; time bonus <=5s +10, <=10s +5

4) POST `/api/save_score.php`
- Payload: { session_id, score, difficulty, questions_answered }
- Response: { success:true, message:'Score saved successfully', new_total_score: int }

5) GET `/api/get_lifelines.php`?session_id=
- Response: { success:true, lifelines: { add_time_remaining, fifty_fifty_remaining, skip_remaining, banana_used } }

6) POST `/api/use_lifeline.php`
- Payload: { session_id, lifeline_type: 'addTime'|'fiftyFifty'|'skip' }
- Response: current behavior returns success true with remaining_lifelines; recommend returning error when count is 0.

7) POST `/api/award_lifeline.php`
- Payload: { session_id }
- Response: success true (awards +1 skip) up to 2 times; afterwards success false with message.

Notes: APIs return JSON `success` field and generally use HTTP 200 for errors; recommended improvement: use appropriate HTTP status codes (401, 400, 500) along with consistent JSON.
