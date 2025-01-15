// Escuchar el clic sobre el botón 'delete' para mostrar el modal
document.body.addEventListener('click', function (event) {
    // Verificar si el clic fue en un botón con la clase 'delete'
    if (event.target && event.target.classList.contains('delete')) {
        // Obtener el ID del botón (u otro atributo si lo necesitas)
        var transportId = event.target.getAttribute('data-id');
        // Mostrar el modal
        const dialogDelete = document.getElementById('delete_transport');
        dialogDelete.showModal();
        // Almacenar el ID en el modal para usarlo más tarde
        dialogDelete.setAttribute('data-id', transportId);
    }
});

// Escuchar el clic sobre el botón 'delete-btn' dentro del modal
document.body.addEventListener('click', function (event) {
    // Verificar si el clic fue en el botón 'delete-btn' dentro del modal
    if (event.target && event.target.id === 'delete-btn') {
        // Obtener referencias
        const deleteDialog = document.getElementById('delete_transport');
        const deleteBtn = document.getElementById('delete-btn');
        const transportId = deleteDialog.getAttribute('data-id');

        // Validar si hay un ID de transporte
        if (!transportId) {
            console.error('Error: No se encontró el ID del transporte.');
            alert('Error: No se pudo identificar el transporte a eliminar.');
            return;
        }

        // Función para cambiar el estado del botón
        function toggleButtonState(isLoading) {
            const textDiv = deleteBtn.querySelector('.text');
            const loaderDiv = deleteBtn.querySelector('.loader-hourglass');

            if (isLoading) {
                textDiv.classList.remove('show');
                textDiv.classList.add('hide');
                loaderDiv.classList.remove('hide');
                loaderDiv.classList.add('show');
                deleteBtn.disabled = true;
            } else {
                textDiv.classList.remove('hide');
                textDiv.classList.add('show');
                loaderDiv.classList.remove('show');
                loaderDiv.classList.add('hide');
                deleteBtn.disabled = false;
            }
        }

        // Cambiar el estado del botón a "cargando"
        toggleButtonState(true);

        // Realizar la solicitud de eliminación
        fetch(`../php/API/transport_delete.php?transportId=${transportId}`, {
            method: 'GET',
        })
            .then(response => {
                if (!response.ok) {
                    ShowAlert('error', 'Error', `Response error: ${response.status}`, 'error');
                    throw new Error(`[Response error]: ${response.status}`);
                }
                return response.json();
            })
            .then((data) => {
                // Cerrar el modal
                deleteDialog.close();
                ShowAlert(data.type, data.title, data.message, data.type, data.link_text, data.link);
                if (!data.error) {
                    setTimeout(function () {
                        location.reload();
                    }, 3900);
                }

            })
            .catch(error => ShowAlert('error', 'Error', `Fetch error: ${error.message || error}`, 'error'))
            .finally(() => {
                // Restaurar el estado del botón
                toggleButtonState(false);
            });
    }
});

