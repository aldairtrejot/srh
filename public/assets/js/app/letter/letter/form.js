
//Scrip que se ejecuta con el formulario, para funciones u herramientas extras
//Ejecucion cuando carga el formulario
var token = $('meta[name="csrf-token"]').attr('content'); //Token for form

$(document).ready(function () {
    $('select').selectpicker(); //Iniciar los select
    //checkboxState();
    setData(); //Establecer las variables de informacion general
    getRole(); //Obtener y definir los roles para no tener los input
    setCheckboxArea();
    setCheckbox(); // Inicio de status de checkox

    tooltip('#id_checkbox_Template_tooltip_fisico', 'Marcar si el documento es físico'); // Tooltip
    tooltip('#id_checkbox_Template_tooltip', 'Añadir un remitente no registrado'); // Tooltip
    tooltip('#mas_remitentes', 'Añadir dos o más remitentes'); // Tooltip
});

//La funcion activa o desactiva el valor de un checkbox de area
function setCheckboxArea() {
    if ($('#rfc_remitente_bool').val()) { //valor del  la variable check true
        $('#idcheckboxTemplate').prop('checked', true); //Activar el checkk
        cleanSelect('#id_cat_remitente'); //Se limpia el select
        $('#id_cat_remitente').prop('disabled', true); // desabilitar no de documento por area
        $('#id_cat_remitente').selectpicker('refresh');
        showDiv('mostrar_ocultar_template'); //Mostrar contenido
    } else { //Valor de la variable check falso
        $('#remitente_nombre').val('');// Limpiar input
        $('#remitente_apellido_paterno').val('');// Limpiar input
        $('#remitente_apellido_materno').val('');// Limpiar input
        $('#remitente_rfc').val('');// Limpiar input
        $('#id_cat_remitente').prop('disabled', false); // desabilitar no de documento por area
        $('#id_cat_remitente').selectpicker('refresh');
        hideDiv('mostrar_ocultar_template'); //Ocultar contenido
    }
    getRole();
}

// La función de define el status de los checkbox, con el fin de marcalos y obtener el status de sus variables
function setCheckbox() {
    // Declaracion de variables
    let es_doc_fisico = $('#es_doc_fisico').val();
    let son_mas_remitentes = $('#son_mas_remitentes').val();

    // Dependiendo del valor marca o desmarca el checbox
    es_doc_fisico ? $('#es_doc_fisico_box').prop('checked', true) : $('#es_doc_fisico_box').prop('checked', false);
    son_mas_remitentes ? $('#son_mas_remitentes_box').prop('checked', true) : $('#son_mas_remitentes_box').prop('checked', false);

    // Oculta o muestra el contenido dependiendo del la seleccion de remitentes
    setValueOfMoreRem();

}

// Oculta o muestra el contenido dependiendo del la seleccion de remitentes
function setValueOfMoreRem() {
    let son_mas_remitentes = $('#son_mas_remitentes').val();

    if (son_mas_remitentes) {
        hideDiv('_hidden_select');
        hideDiv('mostrar_ocultar_template');
        showDiv('mostrar_ocultar_mas_remitentes');
        cleanSelect('#id_cat_remitente'); //Se limpia el select
    } else {
        showDiv('_hidden_select');
        hideDiv('mostrar_ocultar_template');
        hideDiv('mostrar_ocultar_mas_remitentes');
        $('#remitente').val('');
    }
}

//Codigo para la ejecucion de un checkbox
$('#es_doc_fisico_box').change(function () {
    let bool = false; // Inicio de variable de checkbox de area
    bool = $(this).prop('checked') ? true : ''; //Se valida si el checkbox es verdadero o falso para asignarle ese valor a la variable
    $('#es_doc_fisico').val(bool); //Se asigna el valor
});

//Codigo para la ejecucion de un checkbox
$('#son_mas_remitentes_box').change(function () {
    let bool = false; // Inicio de variable de checkbox de area
    bool = $(this).prop('checked') ? true : ''; //Se valida si el checkbox es verdadero o falso para asignarle ese valor a la variable
    $('#son_mas_remitentes').val(bool); //Se asigna el valor
    setCheckbox();
});

//Codigo para la ejecucion de un checkbox
$('#idcheckboxTemplate').change(function () {
    let bool = false; // Inicio de variable de checkbox de area
    bool = $(this).prop('checked') ? true : ''; //Se valida si el checkbox es verdadero o falso para asignarle ese valor a la variable
    $('#rfc_remitente_bool').val(bool); //Se asigna el valor
    setCheckboxArea();//Se ejecuta la funcon
});

//La funcion desabilita los campos dependiendo del rol de usuario
function getRole() {
    let bool_user_role = $('#bool_user_role').val(); //Se obtienen los roles de usuario
    let new_variable = (bool_user_role && bool_user_role.trim() !== '') ? true : false; //Se validan para obtener una variable boolean
    if (!new_variable) { //Condicion para inabilitar las opciones
        validateEstatus();

        $('#num_documento').prop('disabled', true);
        $('#num_copias').prop('disabled', true);
        $('#fecha_inicio').prop('disabled', true);
        $('#fecha_fin').prop('disabled', true);
        $('#num_flojas').prop('disabled', true);
        $('#num_tomos').prop('disabled', true);
        $('#asunto').prop('disabled', true);
        $('#remitente_nombre').prop('disabled', true);
        $('#remitente_apellido_paterno').prop('disabled', true);
        $('#remitente_apellido_materno').prop('disabled', true);
        $('#remitente_rfc').prop('disabled', true);
        $('#horas_respuesta').prop('disabled', true);
        $('#puesto_remitente').prop('disabled', true);
        $('#id_cat_remitente').prop('disabled', true);
        $('#folio_gestion').prop('disabled', true);
        $('#remitente').prop('disabled', true);
        $('#fecha_documento').prop('disabled', true);

        $('#idcheckboxTemplate').prop('disabled', true);
        $('#es_doc_fisico_box').prop('disabled', true);
        $('#son_mas_remitentes_box').prop('disabled', true);

        $('#id_cat_area').prop('disabled', true); //Desabilitar selecct
        $('#id_usuario_area').prop('disabled', true);
        $('#id_usuario_enlace').prop('disabled', true);
        $('#id_cat_unidad').prop('disabled', true);
        $('#id_cat_coordinacion').prop('disabled', true);
        $('#id_cat_tramite').prop('disabled', true);
        $('#id_cat_clave').prop('disabled', true);
        $('#id_cat_remitente').prop('disabled', true);
        $('#id_cat_entidad').prop('disabled', true);

        $('#id_cat_entidad').selectpicker('refresh');
        $('#id_cat_area').selectpicker('refresh'); //Refresh de select 
        $('#id_usuario_area').selectpicker('refresh');
        $('#id_usuario_enlace').selectpicker('refresh');
        $('#id_cat_unidad').selectpicker('refresh');
        $('#id_cat_coordinacion').selectpicker('refresh');
        $('#id_cat_tramite').selectpicker('refresh');
        $('#id_cat_clave').selectpicker('refresh');
        $('#id_cat_remitente').selectpicker('refresh');
    }
}

//valida si el estatus es vencido o cancelado se desabilite para que el enlace no pueda cambiar el estatus
function validateEstatus() {
    // Se eliminan las opciones
    if ($('#id_cat_estatus').val() == 2 || $('#id_cat_estatus').val() == 5) {
        $('#id_cat_estatus').prop('disabled', true); //Desabilitar selecct
        $('#id_cat_estatus').selectpicker('refresh'); //Refresh de select 
    } else {
        //$('#id_cat_estatus option').eq(1).remove(); // Elimina la opción 2
        $('#id_cat_estatus option[value="2"]').remove();
        $('#id_cat_estatus option[value="5"]').remove();
        $('#id_cat_estatus').selectpicker('refresh');
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

//La funcion obtiene el año, clave, codigo y redaccion
function getData() {

    let id_cat_anio = $('#id_cat_anio').val();//Obtener elemento
    let id_cat_clave = $('#id_cat_clave_aux').val();//Obtener elemento

    $.ajax({
        url: URL_DEFAULT.concat('/letter/collection/dataClave'),
        type: 'POST',
        data: {
            id_cat_anio: id_cat_anio,
            id_cat_clave: id_cat_clave,
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            let item = response.nameYear;
            let itemClave = response.dataClave;

            $('#_labAño').text(item.name); // establecer los valores
            $('#_labClave').text(itemClave._labClave);
            $('#_labClaveCodigo').text(itemClave._labClaveCodigo);
            $('#_labClaveRedaccion').text(itemClave._labClaveRedaccion);
        },
    });
}


