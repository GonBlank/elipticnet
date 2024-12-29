document.addEventListener("DOMContentLoaded", function () {
    const signUpbtn = document.getElementById('sign_up_btn');

    // Animación de carga del formulario
    function toggleButtonState(isLoading) {
        const textDiv = signUpbtn.querySelector('.text');
        const loaderDiv = signUpbtn.querySelector('.loader-hourglass');

        if (isLoading) {
            textDiv.classList.remove('show');
            textDiv.classList.add('hide');
            loaderDiv.classList.remove('hide');
            loaderDiv.classList.add('show');
            signUpbtn.disabled = true;
        } else {
            textDiv.classList.remove('hide');
            textDiv.classList.add('show');
            loaderDiv.classList.remove('show');
            loaderDiv.classList.add('hide');
            signUpbtn.disabled = false;
        }
    }

    function validateUserData() {
        // Limpiar errores anteriores
        const inputs = document.querySelectorAll('.input');
        inputs.forEach(input => {
            input.classList.remove('error');
            const errorMessage = document.getElementById(`${input.id}-error`);
            if (errorMessage) errorMessage.textContent = '';
        });

        // Obtener los valores del formulario
        const username = document.getElementById('register-username');
        const email = document.getElementById('register-user-email');
        const password = document.getElementById('register-user-password');

        let isValid = true;

        // Validar los datos del formulario
        if (!username || username.value.trim() === '') {
            isValid = false;
            document.getElementById('register-username').classList.add('error');
            document.getElementById('register-username-error').textContent = 'Username is required.';
        } else {
            const usernameLength = username.value.length;
            if (usernameLength < 3 || usernameLength > 15) {
                isValid = false;
                document.getElementById('register-username').classList.add('error');
                document.getElementById('register-username-error').textContent = 'Username must be between 3 and 15 characters.';
            }
        }

        if (!password || password.value.trim() === '') {
            isValid = false;
            document.getElementById('register-user-password').classList.add('error');
            document.getElementById('register-user-password-error').textContent = 'Password is required.';
        } else {
            const passwordLength = password.value.length;
            if (passwordLength < 8 || passwordLength > 20) {
                isValid = false;
                document.getElementById('register-user-password').classList.add('error');
                document.getElementById('register-user-password-error').textContent = 'Password must be between 8 and 20 characters.';
            }
        }

        if (!email || email.value.trim() === '') {
            isValid = false;
            document.getElementById('register-user-email').classList.add('error');
            document.getElementById('register-user-email-error').textContent = 'Email is required.';
        } else {
            const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailRegex.test(email.value)) {
                isValid = false;
                document.getElementById('register-user-email').classList.add('error');
                document.getElementById('register-user-email-error').textContent = 'Invalid email format.';
            }
        }

        return isValid;
    }

    signUpbtn.addEventListener('click', function (event) {
        event.preventDefault(); // Previene el comportamiento por defecto del botón

        if (!validateUserData()) {
            return; // No enviar datos si la validación falla
        }

        // Cambiar el estado del botón para mostrar el loader
        toggleButtonState(true);

        // Obtener los valores del formulario
        const username = document.getElementById('register-username');
        const email = document.getElementById('register-user-email');
        const password = document.getElementById('register-user-password');

        // Crear un objeto con los datos del nuevo host
        // Crear un objeto con los datos del nuevo usuario
        const newUser = {
            username: username.value,
            email: email.value,
            password: password.value,
        };

        // Enviar los datos al backend usando fetch
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
                toggleButtonState(false);

                // Cerrar el diálogo
                const dialog = document.getElementById('sign-up');
                dialog.close(); // Cerrar el diálogo
            });
    });
});