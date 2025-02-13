// Capturamos todos los enlaces con la clase 'transition_link'
document.querySelectorAll('a.transition_link').forEach(function(link) {
  link.addEventListener('click', function(event) {
      event.preventDefault();  // Evita que el enlace se abra inmediatamente
      
      const targetUrl = this.href;  // Obtiene la URL del enlace
      
      // Añadimos la clase 'fade-out' al body para el efecto de transición
      document.body.classList.add('fade-out');
      
      // Esperamos a que termine la animación antes de redirigir
      setTimeout(function() {
          window.location.href = targetUrl;  // Redirige a la nueva página
      }, 300);  // Duración de la animación (en ms)
  });
});

// Evento pageshow para restaurar la opacidad al navegar hacia atrás
window.addEventListener('pageshow', function(event) {
  if (event.persisted) {  // Si la página fue restaurada desde el caché
      document.body.classList.remove('fade-out');
      document.body.style.opacity = 1;
  }
});

// Esperamos a que el contenido de la nueva página se cargue
window.addEventListener('load', function() {
  document.body.classList.remove('fade-out');
  document.body.classList.add('fade-in');
  setTimeout(function() {
      document.body.style.opacity = 1;
  }, 1);  // Asegura que la transición sea visible
});