document.getElementById("myForm").addEventListener("submit", function (event) {
    let fecha_inicio = document.getElementById('fecha_inicio').value;
    let fecha_fin = document.getElementById('fecha_fin').value;

    if (
        isFieldEmpty($('#id_cat_dependencia').val(), 'Dependencia General') ||
        isFieldEmpty($('#id_cat_dependencia_area').val(), 'Dependencia Especifica') 
    
    ) {
        event.preventDefault();  // Evita el envío del formulario
        return false;  // Detener la ejecución aquí
    }
});