document.getElementById("myForm").addEventListener("submit", function (event) {

    if (
        isFieldEmpty($('#descripcion').val(), 'Descripcion') ||
        isExceedingLength($('#descripcion').val(), 'Descripcion', 200) ||
        isFieldEmpty($('#nombre').val(), 'Nombre') ||
        isExceedingLength($('#nombre').val(), 'Nombre', 200) 
    ) {
        event.preventDefault();  // Evita el envío del formulario
        return;  // Detener la ejecución aquí
    }
});