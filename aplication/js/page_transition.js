  document.body.addEventListener('click', function (event) {
    // Verifica si el elemento clicado es un enlace con la clase 'transition-link'
    if (event.target.classList.contains('transition-link')) {
      event.preventDefault();
      const href = event.target.href;

      document.body.classList.add('fade-out');

      setTimeout(function () {
        window.location.href = href;
      }, 300); // Debe coincidir con la duración de la transición en el CSS
    }
  });
