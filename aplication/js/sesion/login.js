import { toggleButtonState } from '../functions/toggleButtonState.js';

document.addEventListener("DOMContentLoaded", function () {
    const loginBtn = document.getElementById('loginBtn');

    function validateUserData() {
        // Limpiar errores anteriores
        const inputs = document.querySelectorAll('.input');
        inputs.forEach(input => {
            input.classList.remove('error');
            const errorMessage = document.getElementById(`${input.id}-error`);
            if (errorMessage) errorMessage.textContent = '';
        });

        const email = document.getElementById('userEmail');
        const password = document.getElementById('userPassword');

        let isValid = true;

        if (!password || password.value.trim() === '') {
            isValid = false;
            document.getElementById('userPassword').classList.add('error');
            document.getElementById('userPasswordError').textContent = 'Password is required.';
        } else {
            const passwordLength = password.value.length;
            if (passwordLength < 8 || passwordLength > 20) {
                isValid = false;
                document.getElementById('userPassword').classList.add('error');
                document.getElementById('userPasswordError').textContent = 'Password must be between 8 and 20 characters.';
            }
        }

        if (!email || email.value.trim() === '') {
            isValid = false;
            document.getElementById('userEmail').classList.add('error');
            document.getElementById('userEmailError').textContent = 'Email is required.';
        } else {
            const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailRegex.test(email.value)) {
                isValid = false;
                document.getElementById('userEmail').classList.add('error');
                document.getElementById('userEmailError').textContent = 'Invalid email format.';
            }
        }

        return isValid;
    }

    loginBtn.addEventListener('click', function (event) {
        event.preventDefault();

        if (!validateUserData()) {
            return;
        }

        toggleButtonState('loginBtn', true);

        const email = document.getElementById('userEmail');
        const password = document.getElementById('userPassword');
        const rememberMeCheckbox = document.getElementById('rememberMe');

        // Crear un objeto con los datos del usuario
        const User = {
            email: email.value,
            password: password.value,
            rememberMe: rememberMeCheckbox.checked,
        };

        fetch('../php/sesion/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(User),
        })
            .then(response => response.json())
            .then(data => {
                ShowAlert(data.type, data.title, data.message, data.type, data.link_text, data.link);
                if (!data.error) {
                    email.value = '';
                    password.value = '';
                    window.location.assign('home.php'); // Redirige a home.html
                }
            })
            .catch(error => ShowAlert('error', 'Error', `Error: ${error.message}`, 'error'))//
            .finally(() => {
                // Restablecer el estado del botón y cerrar el diálogo
                toggleButtonState('loginBtn', false);
            });
    });
});