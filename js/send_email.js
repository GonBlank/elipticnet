document.addEventListener("DOMContentLoaded", function () {
    const sendEmailBtn = document.getElementById('sendEmailBtn');

    // Animación de carga del formulario
    function toggleButtonState(isLoading) {
        const textDiv = sendEmailBtn.querySelector('.textButton');
        const loaderDiv = sendEmailBtn.querySelector('.loaderButton');

        if (isLoading) {
            textDiv.classList.remove('show');
            textDiv.classList.add('hide');
            loaderDiv.classList.remove('hide');
            loaderDiv.classList.add('show');
            sendEmailBtn.disabled = true;
        } else {
            textDiv.classList.remove('hide');
            textDiv.classList.add('show');
            loaderDiv.classList.remove('show');
            loaderDiv.classList.add('hide');
            sendEmailBtn.disabled = false;
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

    sendEmailBtn.addEventListener('click', function (event) {
        event.preventDefault(); // Previene el comportamiento por defecto del botón

        if (!validateUserData()) {
            return; // No enviar datos si la validación falla
        }

        // Cambiar el estado del botón para mostrar el loader
        toggleButtonState(true);

        // Obtener los valores del formulario
        const email = document.getElementById('user-email');
        const language = (navigator.language || navigator.userLanguage).split('-')[0];
        const data = {
            email: email.value,  // Ajusta los datos según tu caso
            language: language,
        };
        
        fetch('../php/send_email.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'  // Especifica el tipo de contenido como JSON
            },
            body: JSON.stringify(data)  // Convierte los datos a JSON
        })
            .then(response => response.json())
            .then(data => {
                ShowAlert(data.type, data.title, data.message, data.type, data.link_text, data.link);
                if (!data.error) {
                    email.value = '';
                }
            })
            .catch(error => ShowAlert('error', 'Error', `Error: ${error.message}`, 'error'))
            .finally(() => {
                toggleButtonState(false);
            });
        


    });
});