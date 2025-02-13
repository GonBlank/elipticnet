function removeLoadCurtain(){
    const loadCurtain = document.getElementById('load-curtain');

    // Verificar si el elemento existe
    if (loadCurtain) {
        // Si existe, ocultarlo
        loadCurtain.style.display = 'none';
        // Luego eliminarlo del DOM
        loadCurtain.remove();
    }
}