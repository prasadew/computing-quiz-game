import time
import pytest


def test_init_session_and_get_question(http, new_user):
    base = new_user['base_url']
    session_id = f"session_test_{int(time.time())}"
    difficulty = 'Easy'

    # initialize session
    r = http.post(f'{base}/api/init_session.php', json={
        'session_id': session_id,
        'difficulty': difficulty
    })
    assert r.status_code == 200
    data = r.json()
    assert data.get('success') is True

    # fetch lifelines
    r = http.get(f'{base}/api/get_lifelines.php', params={'session_id': session_id})
    assert r.status_code == 200
    data = r.json()
    assert data.get('success') is True

    # fetch question
    r = http.get(f'{base}/api/get_question.php', params={'difficulty': difficulty, 'session_id': session_id})
    assert r.status_code == 200
    data = r.json()
    assert data.get('success') is True
    q = data.get('question')
    assert q and 'id' in q and 'question_text' in q


def test_submit_answer_flow(http, new_user):
    base = new_user['base_url']
    session_id = f"session_test_{int(time.time())}"
    difficulty = 'Easy'

    http.post(f'{base}/api/init_session.php', json={'session_id': session_id, 'difficulty': difficulty})
    r = http.get(f'{base}/api/get_question.php', params={'difficulty': difficulty, 'session_id': session_id})
    data = r.json()
    assert data['success'] is True
    q = data['question']

    # submit correct answer
    correct = q['correct_option']
    r = http.post(f'{base}/api/submit_answer.php', json={
        'session_id': session_id,
        'question_id': q['id'],
        'selected_option': correct,
        'time_taken': 2
    })
    resp = r.json()
    assert resp['success'] is True
    assert resp['is_correct'] is True
    assert resp['points_earned'] > 0

    # get another question and submit incorrect
    r = http.get(f'{base}/api/get_question.php', params={'difficulty': difficulty, 'session_id': session_id})
    data2 = r.json()
    if not data2['success']:
        pytest.skip('No more questions available for test')
    q2 = data2['question']
    wrong = next(o for o in ['A', 'B', 'C', 'D'] if o != q2['correct_option'])
    r = http.post(f'{base}/api/submit_answer.php', json={
        'session_id': session_id,
        'question_id': q2['id'],
        'selected_option': wrong,
        'time_taken': 4
    })
    resp2 = r.json()
    assert resp2['success'] is True
    assert resp2['is_correct'] is False


def test_lifeline_and_banana_award(http, new_user):
    base = new_user['base_url']
    session_id = f"session_test_{int(time.time())}"
    http.post(f'{base}/api/init_session.php', json={'session_id': session_id, 'difficulty': 'Easy'})

    # use a lifeline
    r = http.post(f'{base}/api/use_lifeline.php', json={'session_id': session_id, 'lifeline_type': 'addTime'})
    assert r.status_code == 200
    data = r.json()
    assert data.get('success') is True

    # award banana twice then fail third
    for i in range(2):
        r = http.post(f'{base}/api/award_lifeline.php', json={'session_id': session_id})
        j = r.json()
        assert j.get('success') is True

    r = http.post(f'{base}/api/award_lifeline.php', json={'session_id': session_id})
    assert r.json().get('success') is False


def test_save_score_and_leaderboard(http, new_user):
    base = new_user['base_url']
    session_id = f"session_test_{int(time.time())}"
    http.post(f'{base}/api/init_session.php', json={'session_id': session_id, 'difficulty': 'Easy'})
    r = http.post(f'{base}/api/save_score.php', json={'session_id': session_id, 'score': 42, 'difficulty': 'Easy', 'questions_answered': 4})
    assert r.status_code == 200
    data = r.json()
    assert data.get('success') is True
    assert 'new_total_score' in data

    # leaderboard page renders
    lr = http.get(f'{base}/leaderboard.php')
    assert lr.status_code == 200
    assert 'Leaderboard' in lr.text
