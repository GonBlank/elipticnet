document.addEventListener("DOMContentLoaded", function () {
    const loginbtn = document.getElementById('login_btn');
    // Animación de carga del formulario
    function toggleButtonState(isLoading) {
        const textDiv = loginbtn.querySelector('.text');
        const loaderDiv = loginbtn.querySelector('.loader-hourglass');

        if (isLoading) {
            textDiv.classList.remove('show');
            textDiv.classList.add('hide');
            loaderDiv.classList.remove('hide');
            loaderDiv.classList.add('show');
            loginbtn.disabled = true;
        } else {
            textDiv.classList.remove('hide');
            textDiv.classList.add('show');
            loaderDiv.classList.remove('show');
            loaderDiv.classList.add('hide');
            loginbtn.disabled = false;
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
        const email = document.getElementById('user-email');
        const password = document.getElementById('user-password');

        let isValid = true;

        // Validar los datos del formulario

        if (!password || password.value.trim() === '') {
            isValid = false;
            document.getElementById('user-password').classList.add('error');
            document.getElementById('user-password-error').textContent = 'Password is required.';
        } else {
            const passwordLength = password.value.length;
            if (passwordLength < 8 || passwordLength > 20) {
                isValid = false;
                document.getElementById('user-password').classList.add('error');
                document.getElementById('user-password-error').textContent = 'Password must be between 8 and 20 characters.';
            }
        }

        if (!email || email.value.trim() === '') {
            isValid = false;
            document.getElementById('user-email').classList.add('error');
            document.getElementById('user-email-error').textContent = 'Email is required.';
        } else {
            const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailRegex.test(email.value)) {
                isValid = false;
                document.getElementById('user-email').classList.add('error');
                document.getElementById('user-email-error').textContent = 'Invalid email format.';
            }
        }

        return isValid;
    }

    loginbtn.addEventListener('click', function (event) {
        event.preventDefault(); // Previene el comportamiento por defecto del botón

        if (!validateUserData()) {
            return; // No enviar datos si la validación falla
        }

        // Cambiar el estado del botón para mostrar el loader
        toggleButtonState(true);

        // Obtener los valores del formulario
        const email = document.getElementById('user-email');
        const password = document.getElementById('user-password');
        const rememberMeCheckbox = document.getElementById('remember_me');

        // Crear un objeto con los datos del usuario
        const User = {
            email: email.value,
            password: password.value,
            remember_me: rememberMeCheckbox.checked,
        };

        console.log(User)

        // Enviar los datos al backend usando fetch
        fetch('../php/sesion/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(User),
        })
            .then(response => {
                if (!response.ok) {
                    ShowAlert('error', 'Error', `Response error: ${response.status}`, 'error');
                    throw new Error(`[Response error]: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                ShowAlert(data.type, data.title, data.message, data.type, data.link_text, data.link);
                if (!data.error) {
                    email.value = '';
                    password.value = '';
                    window.location.assign('home.php'); // Redirige a home.html
                }

            })
            .catch(error => ShowAlert('error', 'Error', `Fetch error: ${error.message || error}`, 'error'))
            .finally(() => {
                // Restablecer el estado del botón y cerrar el diálogo
                toggleButtonState(false);
            });
    });
});