document.addEventListener("DOMContentLoaded", function () {
    const resendBtn = document.getElementById('resend_btn');

    // Animación de carga del formulario
    function toggleButtonState(isLoading) {
        const textDiv = resendBtn.querySelector('.text');
        const loaderDiv = resendBtn.querySelector('.loader-hourglass');

        if (isLoading) {
            textDiv.classList.remove('show');
            textDiv.classList.add('hide');
            loaderDiv.classList.remove('hide');
            loaderDiv.classList.add('show');
            resendBtn.disabled = true;
        } else {
            textDiv.classList.remove('hide');
            textDiv.classList.add('show');
            loaderDiv.classList.remove('show');
            loaderDiv.classList.add('hide');
            resendBtn.disabled = false;
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

        let isValid = true;

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

    resendBtn.addEventListener('click', function (event) {
        event.preventDefault(); // Previene el comportamiento por defecto del botón

        if (!validateUserData()) {
            return; // No enviar datos si la validación falla
        }

        // Cambiar el estado del botón para mostrar el loader
        toggleButtonState(true);

        // Obtener los valores del formulario
        const email = document.getElementById('user-email').value;

        const formData = new FormData();
        formData.append('email', email);

        // Enviar los datos al backend usando fetch
        fetch('../php/sesion/resend_validation_code.php', {
            method: 'POST',
            body: formData
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
            })
            .catch(error => ShowAlert('error', 'Error', `Fetch error: ${error.message || error}`, 'error'))
            .finally(() => {
                // Restablecer el estado del botón y cerrar el diálogo
                toggleButtonState(false);
            });
    });
});