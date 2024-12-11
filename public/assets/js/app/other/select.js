//La funcion limpia un select volviendo a su estado normaL
function cleanSelect(value) {
    $(value).val('');
    $(value).selectpicker('refresh');
    $('.selectpicker').selectpicker();
}

//la funcion itera un catalogo de select
function foreachSelect(value, name) {
    $(name).empty();//limpiar catalogo
    $(name).append('<option value="">SELECCIONE</option>');// Agregar una opción por defecto
    $.each(value, function (index, item) { // Iterar sobre las opciones recibidas y agregarlas al select
        $(name).append('<option value="' + item.id + '">' + item.descripcion + '</option>');
    });
    $(name).selectpicker('refresh');
}