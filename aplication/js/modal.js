document.addEventListener('click', (event) => {
    // Abrir el modal al hacer clic en un botón con la clase 'openModal'
    const openModalButton = event.target.closest('.openModal');
    if (openModalButton) {
        const modalId = openModalButton.getAttribute('data-modal');
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.showModal();  // Solo abre el modal, no intenta enfocar ningún campo
            modal.querySelector('input').blur();
        }
        return; // Previene la ejecución del resto del código si ya se ha manejado el clic
    }
    // Cerrar el modal al hacer clic en un botón con la clase 'close'
    if (event.target.closest('.close-btn')) {
        const dialog = event.target.closest('dialog');
        if (dialog) {
            dialog.close();
        }
    }
    // Cerrar el modal al hacer clic fuera de él
    if (event.target.nodeName === 'DIALOG' && !event.target.querySelector(':hover')) {
        event.target.close();
    }
});//