
//Scrip que se ejecuta con el formulario, para funciones u herramientas extras
//Ejecucion cuando carga el formulario
var token = $('meta[name="csrf-token"]').attr('content'); //Token for form

$(document).ready(function () {
    $('#num_documento_area').prop('disabled', true); //Desabilitar input
    $('select').selectpicker(); //Iniciar los select
    //checkboxState();
    setData(); //Establecer las variables de informacion general
    //getRole(); //Obtener y definir los roles para no tener los input
    setCheckboxArea();
});

//La funcion activa o desactiva el valor de un checkbox de area
function setCheckboxArea() {
    if ($('#es_por_area').val()) { //valor del  la variable check true
        $('#idcheckboxTemplate').prop('checked', true); //Activar el checkk
        showDiv('mostrar_ocultar_no_area'); //Mostrar contenido
        $('#num_correspondencia').val('');// Limpiar input
        $('#num_correspondencia').prop('disabled', true); // desabilitar no de documento por area
    } else { //Valor de la variable check falso
        hideDiv('mostrar_ocultar_no_area'); //Ocultar contenido
        cleanSelect('#id_cat_area_documento'); // Limpiar select
        $('#num_documento_area').val('');// Limpiar input
        $('#num_correspondencia').prop('disabled', false); // desabilitar no de documento por area
    }
    //getRole(); //Validacion por roles
}

//Codigo para la ejecucion de un checkbox
$('#idcheckboxTemplate').change(function () {
    let bool = false; // Inicio de variable de checkbox de area
    bool = $(this).prop('checked') ? true : ''; //Se valida si el checkbox es verdadero o falso para asignarle ese valor a la variable
    $('#es_por_area').val(bool); //Se asigna el valor
    setCheckboxArea();//Se ejecuta la funcon
});

//La funcion desabilita los campos dependiendo del rol de usuario
function getRole() {
    let bool_user_role = $('#bool_user_role').val(); //Se obtienen los roles de usuario
    let new_variable = (bool_user_role && bool_user_role.trim() !== '') ? true : false; //Se validan para obtener una variable boolean
    if (!new_variable) { //Condicion para inabilitar las opciones
        $('#num_documento').prop('disabled', true);
        $('#num_correspondencia').prop('disabled', true);
        $('#fecha_inicio').prop('disabled', true);
        $('#fecha_fin').prop('disabled', true);
        $('#asunto').prop('disabled', true);
        $('#idcheckboxTemplate').prop('disabled', true);

        $('#id_cat_area_documento').prop('disabled', true);
        $('#id_cat_area_documento').selectpicker('refresh');
    }
}

//la funcion obtiene los datos al iniciar el formulario, como fecha inicio, año etc
function setData() {
    let fecha_captura = $('#fecha_captura').val();//fecha de captura
    $('#_labFechaCaptura').text(fecha_captura); //establecer los varoles

    let num_turno_sistema = $('#num_turno_sistema').val();//fecha de captura
    $('#_labNoCorrespondencia').text(num_turno_sistema); //establecer los varoles

    getData();//Se hace busqueda de la informacion
}

//La funcion obtiene el año
function getData() {

    let id_cat_anio = $('#id_cat_anio').val();//Obtener elemento

    $.ajax({
        url: URL_DEFAULT.concat('/year/getYear'),
        type: 'POST',
        data: {
            id_cat_anio: id_cat_anio,
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            let item = response.nameYear;
            $('#_labAño').text(item.name); // establecer los valores
        },
    });
}

