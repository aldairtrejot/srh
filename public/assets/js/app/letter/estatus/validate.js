document.getElementById("myForm").addEventListener("submit", function (event) {
    // Validar descripción
    if (
        isFieldEmpty($('#descripcion').val(), 'Descripción') ||
        isExceedingLength($('#descripcion').val(), 'Descripción', 200)
    ) {
        event.preventDefault();
        return;
    }
});