import { toggleButtonState } from '../functions/toggleButtonState.js';

document.addEventListener("DOMContentLoaded", function () {
    const recoveryBtn = document.getElementById('recoveryBtn');
    function validateUserData() {
        // Limpiar errores anteriores
        const inputs = document.querySelectorAll('.input');
        inputs.forEach(input => {
            input.classList.remove('error');
            const errorMessage = document.getElementById(`${input.id}Error`);
            if (errorMessage) errorMessage.textContent = '';
        });
        // Obtener los valores del formulario
        const email = document.getElementById('recoveryEmail');
        let isValid = true;
        if (!email || email.value.trim() === '') {
            isValid = false;
            document.getElementById('recoveryEmail').classList.add('error');
            document.getElementById('recoveryEmailError').textContent = 'Email is required.';
        } else {
            const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailRegex.test(email.value)) {
                isValid = false;
                document.getElementById('recoveryEmail').classList.add('error');
                document.getElementById('recoveryEmailError').textContent = 'Invalid email format.';
            }
        }
        return isValid;
    }

    recoveryBtn.addEventListener('click', function (event) {
        event.preventDefault();

        if (!validateUserData()) {
            return;
        }

        toggleButtonState('recoveryBtn', true);
        const email = document.getElementById('recoveryEmail').value;
        const formData = new FormData();
        formData.append('email', email);

        fetch('../php/sesion/send_validation_code_restore_password.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                ShowAlert(data.type, data.title, data.message, data.type, data.link_text, data.link);
            })
            .catch(error => ShowAlert('error', 'Error', `Error: ${error.message}`, 'error'))//
            .finally(() => {
                toggleButtonState('recoveryBtn', false);
                const dialog = document.getElementById('forgotPassword');
                dialog.close();
                document.getElementById('recoveryEmail').value = '';
            });
    });
});