import os
import time
import random
import pytest
import requests

BASE_URL = os.getenv('CQG_BASE_URL', 'http://localhost/computing-quiz-game')


@pytest.fixture(scope='session')
def http():
    s = requests.Session()
    s.headers.update({'Accept': 'application/json'})
    return s


@pytest.fixture
def new_user(http):
    # Create a unique test user via the public registration form
    t = int(time.time())
    email = f'test_user_{t}_{random.randint(1000,9999)}@example.com'
    password = 'TestPass123!'
    name = f'TestUser{t}'

    resp = http.post(f'{BASE_URL}/register.php', data={
        'name': name,
        'email': email,
        'password': password,
        'confirm_password': password
    }, allow_redirects=True)

    # Ensure the app set the auth cookie
    cookies = http.cookies
    if not any(c.name == 'auth_token' for c in cookies):
        # Fallback: try logging in explicitly (some environments don't set cookie on register POST)
        login_resp = http.post(f'{BASE_URL}/login.php', data={
            'email': email,
            'password': password
        }, allow_redirects=True)
        cookies = http.cookies
        if not any(c.name == 'auth_token' for c in cookies):
            # Provide diagnostic output: include a small snippet of the server response to help debugging
            snippet = (resp.text or '')[:800]
            login_snip = (login_resp.text or '')[:800]
            raise AssertionError(
                "Registration/login did not set auth_token cookie. "
                f"register_status={resp.status_code}, login_status={login_resp.status_code}\n"
                f"register_response_snippet:\n{snippet}\n---\nlogin_response_snippet:\n{login_snip}"
            )

    return {
        'name': name,
        'email': email,
        'password': password,
        'base_url': BASE_URL
    }
