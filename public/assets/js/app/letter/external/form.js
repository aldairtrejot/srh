
//Scrip que se ejecuta con el formulario, para funciones u herramientas extras
//Ejecucion cuando carga el formulario
var token = $('meta[name="csrf-token"]').attr('content'); //Token for form

$(document).ready(function () {
    $('select').selectpicker(); //Iniciar los select
    setData(); //Establecer las variables de informacion general
});

function setData() {
    $('#_labFechaCaptura').text($('#fecha_captura').val()); //establecer los varoles
    $('#_labAño').text($('#anio').val()); //establecer los varoles
    $('#_labNoCorrespondencia').text($('#num_turno_sistema').val()); //establecer los varoles
}
