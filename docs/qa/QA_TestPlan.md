# Test Plan — Computing Quiz Game

Objective
- Validate the functional correctness, API contracts, and critical user flows of the Computing Quiz Game as implemented in the repository.

Scope (in-scope)
- API endpoints under `/api/` (init_session, get_question, submit_answer, save_score, get_lifelines, use_lifeline, award_lifeline)
- User flows: register, login, start game, play question(s), use lifelines, banana mini-game handoff, save score, leaderboard
- DB interactions for game_sessions, lifelines, session_questions, session_answers, scores

Out-of-scope
- Visual/UI styling tests, audio playback tests, performance/load tests

Approach
- Automated API tests (PyTest + requests)
- Manual exploratory UI tests for end-to-end gameplay
- Security checks for JWT behavior (tampering, missing, expired)

Test environment
- Windows with WAMP (Apache + PHP + MySQL) hosting the project at `http://localhost/computing-quiz-game`
- MySQL database `ComputingQuizGame` created and `database/schema.sql` imported
- Python 3.10+ with dependencies from `requirements-dev.txt`

Risks & Mitigation
- Race condition between client session init and question fetch — mitigation: await init before question (client fix applied)
- Hard-coded JWT secret — recommend moving to env/config

Entry criteria
- Project served by WAMP and reachable, DB imported
- Python dependencies installed

Exit criteria
- All automated tests executed; critical failures logged and triaged
- Test summary delivered

Deliverables
- Automated test suite in `tests/`
- QA documentation in `docs/qa/`
- Bug reports and recommendations
