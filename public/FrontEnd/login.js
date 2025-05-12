document.getElementById('login-form').addEventListener('submit', async (e) => {
    e.preventDefault();

    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();
    const errorMessage = document.getElementById('error-message');

    try {
        const response = await fetch('http://localhost:8000/api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ username, password })
        });

        if (!response.ok) {
            throw new Error('Nom d’utilisateur ou mot de passe incorrect.');
        }

        const data = await response.json();

        localStorage.setItem('jwt_token', data.token);

        const redirectUrl = localStorage.getItem('redirect_after_login') || 'activeGame.html';
        localStorage.removeItem('redirect_after_login');
        window.location.href = redirectUrl;

    } catch (error) {
        errorMessage.textContent = error.message;
        errorMessage.style.display = 'block';
    }
});