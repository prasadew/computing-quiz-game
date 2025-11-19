# Bug Reports (discovered via code review)

Bug-001 — Race condition: immediate Game Over on first start
- Description: Client didn't await session initialization before requesting first question. Fixed in `assets/js/game.js` by awaiting `initializeSession()`.
- Severity: High
- Recommendation: Keep fix; also add server-side guard in `get_question.php`.

Bug-002 — use_lifeline returns success even when count = 0
- Description: `api/use_lifeline.php` does not return error when lifeline unavailable; it returns success true but counts unchanged.
- Severity: Medium
- Recommendation: Update API to check affected rows and return error if lifeline count is insufficient.

Bug-003 — Duplicate lifelines if init called twice
- Description: `lifelines.session_id` not unique; duplicate rows possible.
- Severity: Low
- Recommendation: Add unique constraint on `session_id` or check before insert.

Bug-004 — Hard-coded JWT secret in source
- Description: `includes/jwt.php` contains secret in code; security risk.
- Severity: Medium
- Recommendation: Read secret from env/config and prevent committing secrets.

Bug-005 — Empty JS modules and debug logs
- Description: `assets/js/lifeline.js` and `assets/js/timer.js` are empty; `game.js` contains debug logging.
- Severity: Low
- Recommendation: Clean up debug logs and remove or implement empty modules.
