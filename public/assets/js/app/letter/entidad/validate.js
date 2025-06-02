document.getElementById("myForm").addEventListener("submit", function (event) {
    // Validar descripción
    if (
        isFieldEmpty($('#descripcion').val(), 'Descripción') ||
        isExceedingLength($('#descripcion').val(), 'Descripción', 200)
    ) {
        event.preventDefault();
        return;
    }

    // Validar clave
    if (
        isFieldEmpty($('#clave').val(), 'Clave') ||
        isExceedingLength($('#clave').val(), 'Clave', 50)
    ) {
        event.preventDefault();
        return;
    }
});
