//La funcion limpia un select volviendo a su estado normaL
function cleanSelect(value) {
    $(value).val('');
    $(value).selectpicker('refresh');
    $('.selectpicker').selectpicker();
}


//La funcion limpia un select volviendo a su estado normaL
function cleanSelectMoreSelect(value) {
    $(value).val('');
    $(value).empty();//limpiar catalogo
    $(value).append('<option value="">SELECCIONE</option>');// Agregar una opción por defecto
    $(value).selectpicker('refresh');
    $('.selectpicker').selectpicker();
}

//la funcion itera un catalogo de select
function allTextForeachSelect(value, name) {
    $(name).empty();//limpiar catalogo
    $(name).append('<option value="">TODOS</option>');// Agregar una opción por defecto
    $.each(value, function (index, item) { // Iterar sobre las opciones recibidas y agregarlas al select
        $(name).append('<option value="' + item.id + '">' + item.descripcion + '</option>');
    });
    $(name).selectpicker('refresh');
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

//la funcion itera un catalogo de select sin la propiedad SELECCIONE
function foreachSelectNull(value, name) {
    $(name).empty();//limpiar catalogo
    $.each(value, function (index, item) { // Iterar sobre las opciones recibidas y agregarlas al select
        $(name).append('<option value="' + item.id + '">' + item.descripcion + '</option>');
    });
    $(name).selectpicker('refresh');
}

function foreachSelectByDefault(valueDefaultArray, valueAll, id_select) {
    let valueDefault = valueDefaultArray[0]; // Inicio de array

    console.log(valueDefault);
    console.log(valueAll);


    $(id_select).empty();//limpiar catalogo
    $(id_select).append('<option value="' + valueDefault.id + '">' + valueDefault.descripcion + '</option>');// Agregar una opción por defecto
    $.each(value, function (index, valueAll) { // Iterar sobre las opciones recibidas y agregarlas al select
        $(id_select).append('<option value="' + valueAll.id + '">' + valueAll.descripcion + '</option>');
    });
    $(id_select).selectpicker('refresh');
}