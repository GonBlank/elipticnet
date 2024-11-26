function fetchTransports() {
    fetch('../php/get_transport_by_owner.php', {
        method: 'POST', // El método de la solicitud
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            // Asegurarse de que los datos contienen transportes
            if (data.transports && data.transports.length > 0) {
                let transportContainer = document.querySelector('.alert-transports');

                // Limpiar solo los transportes, no el botón
                let existingTransports = transportContainer.querySelectorAll('.transport');
                existingTransports.forEach(transport => transport.remove()); // Elimina solo los transportes previos

                // Obtener los parámetros de la URL
                const urlParams = new URLSearchParams(window.location.search);
                
                // Recorrer cada transporte y crear la estructura HTML correspondiente
                data.transports.forEach(transport => {
                    let transportDiv = document.createElement('div');
                    transportDiv.classList.add('transport');

                    // Crear la estructura para cada tipo de transporte
                    let checkboxWrapper = document.createElement('div');
                    checkboxWrapper.classList.add('checkbox-wrapper-13');
                    let checkboxInput = document.createElement('input');
                    checkboxInput.id = transport.type + '_transport';
                    checkboxInput.name = transport.type + '_transport';
                    checkboxInput.type = 'checkbox';

                    // Verificar si el parámetro para este transporte existe en la URL y si está marcado
                    const transportStatus = urlParams.get(transport.type + '_transport');
                    if (transportStatus === 'true') {
                        checkboxInput.checked = true;
                    } else {
                        checkboxInput.checked = false;
                    }

                    let label = document.createElement('label');
                    label.setAttribute('for', transport.type + '_transport');
                    label.textContent = transport.type.charAt(0).toUpperCase() + transport.type.slice(1); // Capitalizar el tipo

                    checkboxWrapper.appendChild(checkboxInput);
                    checkboxWrapper.appendChild(label);

                    // Crear el enlace con el transport_id
                    let transportLink = document.createElement('a');
                    transportLink.classList.add('transition-link');
                    transportLink.href = '#'; // Aquí puedes agregar la URL que necesites
                    transportLink.innerHTML = ` 
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-wrench" viewBox="0 0 16 16">
                        <path
                            d="M.102 2.223A3.004 3.004 0 0 0 3.78 5.897l6.341 6.252A3.003 3.003 0 0 0 13 16a3 3 0 1 0-.851-5.878L5.897 3.781A3.004 3.004 0 0 0 2.223.1l2.141 2.142L4 4l-1.757.364zm13.37 9.019.528.026.287.445.445.287.026.529L15 13l-.242.471-.026.529-.445.287-.287.445-.529.026L13 15l-.471-.242-.529-.026-.287-.445-.445-.287-.026-.529L11 13l.242-.471.026-.529.445-.287.287-.445.529-.026L13 11z" />
                    </svg>
                    ${transport.transport_id}
                `;

                    // Agregar todo al contenedor de transportes
                    transportDiv.appendChild(checkboxWrapper);
                    transportDiv.appendChild(transportLink);
                    // Insertar el nuevo transporte antes del botón "ADD TRANSPORT"
                    transportContainer.insertBefore(transportDiv, transportContainer.querySelector('.add-transport'));
                });
            } else {
                console.log('No transports found.');
            }
        })
        .catch(error => console.error('Error fetching transports:', error));
}

// Llamar la función al cargar la página
window.onload = fetchTransports;
