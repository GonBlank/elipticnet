document.addEventListener('DOMContentLoaded', function () {
    const dropdowns = document.querySelectorAll('.custom-dropdown');

    dropdowns.forEach(dropdown => {
        const dropdownButton = dropdown.querySelector('.dropdown-button');
        const dropdownList = dropdown.querySelector('.dropdown-list');
        const dropdownItems = dropdown.querySelectorAll('.dropdown-item');

        // Mostrar/ocultar lista
        dropdownButton.addEventListener('click', function () {
            closeAllDropdowns();
            dropdownList.classList.toggle('show');
        });

        // Seleccionar opción
        dropdownItems.forEach(item => {
            item.addEventListener('click', function () {
                dropdownButton.textContent = item.textContent;
                dropdownList.classList.remove('show');
            });
        });
    });

    // Ocultar todos los dropdowns cuando se haga clic fuera
    document.addEventListener('click', function (e) {
        dropdowns.forEach(dropdown => {
            const dropdownList = dropdown.querySelector('.dropdown-list');
            const dropdownButton = dropdown.querySelector('.dropdown-button');
            if (!dropdown.contains(e.target)) {
                dropdownList.classList.remove('show');
            }
        });
    });

    function closeAllDropdowns() {
        dropdowns.forEach(dropdown => {
            const dropdownList = dropdown.querySelector('.dropdown-list');
            dropdownList.classList.remove('show');
        });
    }
});
