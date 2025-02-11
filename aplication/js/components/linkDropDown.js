document.addEventListener("DOMContentLoaded", function () {
    const dropdown_button = document.getElementById("dropdown_button");
    const dropdown_menu = document.getElementById("dropdown_menu");

    // Función para alternar el estado del dropdown
    function toggleDropdown() {
        if (dropdown_menu.classList.contains("collapsed")) {
            dropdown_menu.classList.remove("collapsed");
        } else {
            dropdown_menu.classList.add("collapsed");
        }
    }

    // Detectar clic en el botón para abrir/cerrar el dropdown
    dropdown_button.addEventListener("click", function (e) {
        e.stopPropagation(); // Evitar que el clic se propague y cierre el dropdown
        toggleDropdown();
    });

    // Detectar clic fuera del dropdown para colapsarlo
    document.addEventListener("click", function (e) {
        if (!dropdown_menu.classList.contains("collapsed") && !dropdown_menu.contains(e.target)) {
            dropdown_menu.classList.add("collapsed");
        }
    });
});