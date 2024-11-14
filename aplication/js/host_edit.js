const urlParams = new URLSearchParams(window.location.search);
const hostId = urlParams.get('id');

if (!hostId || hostId === null || !Number.isInteger(Number(hostId))) {
    window.location.replace('../public/home.php');
}



document.getElementById('host-name').value = urlParams.get('name');
document.getElementById('host-ip').value = urlParams.get('ip');
document.getElementById('host-description').value = urlParams.get('description');

document.getElementById("email_transport").checked = (urlParams.get('email_transport') === 'true');
document.getElementById("telegram_transport").checked = (urlParams.get('telegram_transport') === 'true');

//---------------------------------------------------------------------------

document.addEventListener("DOMContentLoaded", function () {
    const editHostButton = document.getElementById('update_agent');

    function toggleButtonState(isLoading) {
        const textDiv = editHostButton.querySelector('.text');
        const loaderDiv = editHostButton.querySelector('.loader-hourglass');

        if (isLoading) {
            textDiv.classList.remove('show');
            textDiv.classList.add('hide');
            loaderDiv.classList.remove('hide');
            loaderDiv.classList.add('show');
            editHostButton.disabled = true;
        } else {
            textDiv.classList.remove('hide');
            textDiv.classList.add('show');
            loaderDiv.classList.remove('show');
            loaderDiv.classList.add('hide');
            editHostButton.disabled = false;
        }
    }

    function validateHostData() {
        const inputs = document.querySelectorAll('.input');
        inputs.forEach(input => {
            input.classList.remove('error');
            const errorMessage = document.getElementById(`${input.id}-error`);
            if (errorMessage) errorMessage.textContent = '';
        });

        const hostName = document.getElementById('host-name').value;
        const hostIp = document.getElementById('host-ip').value;

        let isValid = true;

        if (!hostName) {
            isValid = false;
            document.getElementById('host-name').classList.add('error');
            document.getElementById('host-name-error').textContent = 'Host name is required.';
        }

        if (!hostIp) {
            isValid = false;
            document.getElementById('host-ip').classList.add('error');
            document.getElementById('host-ip-error').textContent = 'IP is required.';
        } else {
            const ipv4Pattern = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
            const ipv6Pattern = /^(?:[0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}|(?:[0-9a-fA-F]{1,4}:){1,7}:|(?:[0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|(?:[0-9a-fA-F]{1,4}:){1,5}(?::[0-9a-fA-F]{1,4}){1,2}|(?:[0-9a-fA-F]{1,4}:){1,4}(?::[0-9a-fA-F]{1,4}){1,3}|(?:[0-9a-fA-F]{1,4}:){1,3}(?::[0-9a-fA-F]{1,4}){1,4}|(?:[0-9a-fA-F]{1,4}:){1,2}(?::[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:(?::[0-9a-fA-F]{1,4}){1,6}|::(?:[0-9a-fA-F]{1,4}){1,7}|::(?:[0-9a-fA-F]{1,4}){1,6}:(?:[0-9a-fA-F]{1,4}){1,6}|(?:[0-9a-fA-F]{1,4}:){1,7}:|(?:[0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}$/;

            if (!ipv4Pattern.test(hostIp) && !ipv6Pattern.test(hostIp)) {
                isValid = false;
                document.getElementById('host-ip').classList.add('error');
                document.getElementById('host-ip-error').textContent = 'Invalid IP address format.';
            }
        }

        return isValid;
    }

    editHostButton.addEventListener('click', function (event) {
        event.preventDefault(); // Previene el comportamiento por defecto del botón

        if (!validateHostData()) {
            return; // No enviar datos si la validación falla
        }

        toggleButtonState(true); // Mostrar el loader

        // Obtener los valores del formulario
        const hostName = document.getElementById('host-name').value;
        //const hostIp = document.getElementById('host-ip').value;
        const description = document.getElementById('host-description').value;
        //const selectedRadio = document.querySelector('input[name="radio-check"]:checked');


        // Seleccionar todos los checkboxes de tipo 'transport' en el formulario
        let transportCheckboxes = document.querySelectorAll("input[name$='_transport']"); // Selecciona todos los input con nombre que termine en '_transport'

        // Crear un objeto para almacenar los transportes seleccionados
        const transports = {};

        // Recorrer todos los checkboxes y almacenar si están seleccionados
        transportCheckboxes.forEach(checkbox => {
            transports[checkbox.name] = checkbox.checked; // Usamos el 'name' del checkbox como clave, el valor es si está marcado
        });


        // Crear el objeto con los datos del host actualizado
        const updatedHost = {
            name: hostName,
            description: description,
            transports: transports // El objeto con todos los transportes seleccionados
        };

        fetch(`../php/update_host_by_id.php?id=${hostId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(updatedHost),
            
        })
            .then(response => {
                // Leer la respuesta como texto para ver qué devuelve el servidor
                return response.text().then(text => {

                    return JSON.parse(text); // Intentar convertirla a JSON
                });
            })
            .then(data => {
                if (data.id) {
                    // Capturar el ID y el mensaje de respuesta
                    ShowAlert('success', 'Success', 'Host created successfully', 'success');
                    
                    
                    // Redireccionar a home.html
                    setTimeout(() => {
                        window.location.href = 'home.php';
                    }, 2000); //delay
                    
                } else if (data.error) {
                    ShowAlert(data.type, data.title, data.message, data.type);
                }
            })

            .catch(error => ShowAlert('error', 'Error', `Error: ${error.message}`, 'error'))
            .finally(() => {
                toggleButtonState(false);
            });


    });
});
