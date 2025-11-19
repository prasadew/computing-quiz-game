# LinkedIn Posts — QA Portfolio (10 posts)

1) Completed an end-to-end QA package for an open-source PHP/MySQL quiz game covering auth, game sessions, lifelines, scoring, and a banana mini-game. Delivered automated tests, API docs, and bug reports. #QA #APITesting

2) Designed stateful integration tests that replicate a player session (register -> start session -> fetch questions -> submit answers -> save score). This catches both logic and UX races. #TestDesign

3) Built a PyTest + requests suite that uses the public registration flow to obtain auth tokens—this keeps tests aligned with real app behavior. #Automation

4) Discovered a start-up race where the client fetched questions before the session was ready. Fixed the client to await session init; added tests to prevent regressions. #Debugging

5) Found inconsistent lifeline handling where the server returned success even when lifelines were exhausted. Reported and recommended server-side checks. #DefectAnalysis

6) Emphasized security best practice: move hard-coded JWT secret to environment configuration and avoid committing secrets. #Security

7) Mapped all requirements to test cases in an RTM and provided a test plan for manual and automated testing. #Process

8) Delivered QA documentation (API docs, detailed test cases, bug reports) as part of the repo for maintainers and future contributors. #Documentation

9) Suggested next steps: CI integration to run pytest on pushes, HTML UI smoke tests using Playwright, and DB constraints to harden data integrity. #CI #E2ETesting

10) Lesson: small server-side validations and clear API error codes vastly improve UX and make automation more reliable—tiny changes yield big gains. #QualityAssurance
