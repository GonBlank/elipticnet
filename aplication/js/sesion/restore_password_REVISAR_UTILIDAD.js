function getUrlParameter(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
}

document.addEventListener("DOMContentLoaded", function () {
    const updatePassBtn = document.getElementById('update_btn');

    function validateUserData() {
        // Limpiar errores anteriores
        const inputs = document.querySelectorAll('.input');
        inputs.forEach(input => {
            input.classList.remove('error');
            const errorMessage = document.getElementById(`${input.id}-error`);
            if (errorMessage) errorMessage.textContent = '';
        });

        // Obtener los valores del formulario
        const newPassword = document.getElementById('new-password');
        const newPasswordRepeat = document.getElementById('repeat-new-password');

        let isValid = true;


        // Validar new password
        if (!newPassword || newPassword.value.trim() === '') {
            isValid = false;
            newPassword.classList.add('error');
            document.getElementById('new-password-error').textContent = 'Password is required.';
        } else if (newPassword.value.length < 8 || newPassword.value.length > 20) {
            isValid = false;
            newPassword.classList.add('error');
            document.getElementById('new-password-error').textContent = 'Password must be between 8 and 20 characters.';
        }

        // Validar repeat new password
        if (!newPasswordRepeat || newPasswordRepeat.value.trim() === '') {
            isValid = false;
            newPasswordRepeat.classList.add('error');
            document.getElementById('repeat-new-password-error').textContent = 'Password is required.';
        } else if (newPasswordRepeat.value !== newPassword.value) {
            isValid = false;
            newPasswordRepeat.classList.add('error');
            document.getElementById('new-password-error').textContent = 'The passwords do not match.';
        }

        return isValid;
    }


    updatePassBtn.addEventListener('click', function (event) {
        event.preventDefault(); // Previene el comportamiento por defecto del botón

        if (!validateUserData()) {
            return; // No enviar datos si la validación falla
        }

        // Cambiar el estado del botón para mostrar el loader
        toggleButtonState("update_btn",true);

        // Obtener los valores del formulario
        const newPassword = document.getElementById('new-password');
        const newPasswordRepeat = document.getElementById('repeat-new-password');
        const validationHash = getUrlParameter('validation_hash');

        const password_vector = {
            new_password: newPassword.value,
            repeat_new_password: newPasswordRepeat.value,
            validation_hash: validationHash,
        };

        console.log(password_vector);

        // Enviar los datos al backend usando fetch
        fetch('../php/sesion/restore_password.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(password_vector),
        })
            .then(response => response.json())
            .then(data => {
                ShowAlert(data.type, data.title, data.message, data.type, data.link_text, data.link);
                if (!data.error){
                    setTimeout(() => {
                        window.location.href = "login.php";
                    }, 4000); // 4000 ms = 4 segundos
                }
            })
            .catch(error => ShowAlert('error', 'Error', `Error: ${error.message}`, 'error'))//
            .finally(() => {
                // Restablecer el estado del botón y cerrar el diálogo
                toggleButtonState("update_btn",false);
            });
    });
});