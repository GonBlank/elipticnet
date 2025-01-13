import { toggleButtonState } from '../functions/toggleButtonState.js';

document.addEventListener("DOMContentLoaded", function () {
    const signUpbtn = document.getElementById('signUpBtn');

    function validateUserData() {
        // Limpiar errores anteriores
        const inputs = document.querySelectorAll('.input');
        inputs.forEach(input => {
            input.classList.remove('error');
            const errorMessage = document.getElementById(`${input.id}Error`);
            if (errorMessage) errorMessage.textContent = '';
        });

        // Obtener los valores del formulario
        const username = document.getElementById('registerUsername');
        const email = document.getElementById('registerUserEmail');
        const password = document.getElementById('registerUserPassword');

        let isValid = true;

        // Validar los datos del formulario
        if (!username || username.value.trim() === '') {
            isValid = false;
            document.getElementById('registerUsername').classList.add('error');
            document.getElementById('registerUsernameError').textContent = 'Username is required.';
        } else {
            const usernameLength = username.value.length;
            if (usernameLength < 3 || usernameLength > 15) {
                isValid = false;
                document.getElementById('registerUsername').classList.add('error');
                document.getElementById('registerUsernameError').textContent = 'Username must be between 3 and 15 characters.';
            }
        }

        if (!password || password.value.trim() === '') {
            isValid = false;
            document.getElementById('registerUserPassword').classList.add('error');
            document.getElementById('registerUserPasswordError').textContent = 'Password is required.';
        } else {
            const passwordLength = password.value.length;
            if (passwordLength < 8 || passwordLength > 20) {
                isValid = false;
                document.getElementById('registerUserPassword').classList.add('error');
                document.getElementById('registerUserPasswordError').textContent = 'Password must be between 8 and 20 characters.';
            }
        }

        if (!email || email.value.trim() === '') {
            isValid = false;
            document.getElementById('registerUserEmail').classList.add('error');
            document.getElementById('registerUserEmailError').textContent = 'Email is required.';
        } else {
            const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailRegex.test(email.value)) {
                isValid = false;
                document.getElementById('registerUserEmail').classList.add('error');
                document.getElementById('registerUserEmailError').textContent = 'Invalid email format.';
            }
        }
        return isValid;
    }

    signUpbtn.addEventListener('click', function (event) {
        event.preventDefault();

        if (!validateUserData()) {
            return;
        }

        toggleButtonState('signUpBtn', true);

        const username = document.getElementById('registerUsername');
        const email = document.getElementById('registerUserEmail');
        const password = document.getElementById('registerUserPassword');

        const newUser = {
            username: username.value,
            email: email.value,
            password: password.value,
            timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone,
        };
        fetch('../php/sesion/signup.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(newUser),
        })
            .then(response => response.json())
            .then(data => {
                ShowAlert(data.type, data.title, data.message, data.type);
                if (!data.error) {
                    username.value = '';
                    email.value = '';
                    password.value = '';
                }
            })
            .catch(error => ShowAlert('error', 'Error', `Error: ${error.message}`, 'error'))//
            .finally(() => {
                // Restablecer el estado del botón
                toggleButtonState('signUpBtn', false);

                // Cerrar el diálogo
                const dialog = document.getElementById('dialogSignUp');
                dialog.close(); // Cerrar el diálogo
            });
    });
});