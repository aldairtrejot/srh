// Función para ocultar el div con animación
function hideDiv(parameter) {
    let element = document.getElementById(parameter);
    
    // Cambiar el estilo de opacidad para la animación
    element.style.transition = "opacity 0.5s ease-in-out";  // Definimos la transición de opacidad
    element.style.opacity = "0";  // Hacemos que el elemento se desvanezca
    
    // Esperar a que termine la transición antes de cambiar el display a 'none'
    setTimeout(function() {
        element.style.display = "none"; // Después de la animación, ocultamos el div
    }, 500);  // El tiempo debe coincidir con la duración de la transición
}

// Función para mostrar el div con animación
function showDiv(parameter) {
    let element = document.getElementById(parameter);
    
    // Asegúrate de que el div esté visible y con la animación de opacidad
    element.style.display = "block"; // Cambiar display a 'block' para que sea visible
    element.style.transition = "opacity 0.5s ease-in-out";  // Definir la transición de opacidad
    element.style.opacity = "0";  // Establecemos la opacidad inicial a 0 para comenzar desde invisible
    
    // Forzar un reflujo para aplicar la animación
    void element.offsetWidth; // Este truco hace que el navegador recalcule el estilo antes de comenzar la animación
    
    // Finalmente, hacemos que el div se desvanezca al 100% de opacidad
    element.style.opacity = "1";
}


// Función para mostrar el spinner con animación
function showSpinner() {
    const spinner = document.getElementById('spinner');
    spinner.style.display = 'flex';  // Mostrar el spinner
    setTimeout(() => {
        spinner.classList.add('show');  // Agregar clase para animación
    }, 10); // Para aplicar la transición correctamente
}

// Función para ocultar el spinner con animación
function hideSpinner() {
    const spinner = document.getElementById('spinner');
    spinner.classList.remove('show');  // Eliminar la clase para que comience la transición
    setTimeout(() => {
        spinner.style.display = 'none';  // Ocultar después de la animación
    }, 300);  // Tiempo para esperar que la animación termine
}

// Muestra un tooltip. esperando como parametroa el id del valor value y la leyenta text
function tooltip(value, text) {
    tippy(value, {
        content: text,
        theme: 'dark',  // Color del fondo del tooltip (puedes cambiar el tema o personalizarlo)
        placement: 'top',  // Posición del tooltip (puede ser 'top', 'bottom', 'left', 'right')
        animation: 'fade',  // Animación del tooltip
        arrow: true,  // Muestra una flecha para señalar al checkbox
    });
}
