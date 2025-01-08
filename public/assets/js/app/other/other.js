// Función para ocultar el div
function hideDiv(parameter) {
    document.getElementById(parameter).style.display = 'none';
}

// Función para mostrar el div
function showDiv(parameter) {
    document.getElementById(parameter).style.display = 'block';
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