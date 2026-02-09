document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const messageDiv = document.getElementById('message');

    loginForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'php/index.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    messageDiv.textContent = 'Login exitoso!';
                    messageDiv.style.color = 'green';
                    // Redirige a otra página o maneja el login exitoso
                    window.location.href = './home.php';
                } else {
                    messageDiv.textContent = 'Nombre de usuario o contraseña inválidos';
                    messageDiv.style.color = 'red';
                }
            } else {
                messageDiv.textContent = 'Error en el servidor';
                messageDiv.style.color = 'red';
            }
        };

        xhr.send(`username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`);
    });
});
