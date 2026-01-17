
//Scrip que se ejecuta con el formulario, para funciones u herramientas extras
//Ejecucion cuando carga el formulario
var token = $('meta[name="csrf-token"]').attr('content'); //Token for form

// Carga de formulario inicial
$(document).ready(function () {
    $('select').selectpicker(); //Iniciar los select
    setData(); // Carga de encabezados de formulario
});

// La función establece los datos en el encabezado del formulario
function setData() {
    $('#_labFechaCaptura').text($('#fecha_asignacion').val()); // Asignar valores a labelput
    $('#_labNoOficio').text($('#consecutivo').val()); // Asignar valores a label
}

