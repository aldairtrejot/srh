
//Scrip que se ejecuta con el formulario, para funciones u herramientas extras
//Ejecucion cuando carga el formulario
var token = $('meta[name="csrf-token"]').attr('content'); //Token for form

$(document).ready(function () {
    $('select').selectpicker(); //Iniciar los select
    //checkboxState();
    setData(); //Establecer las variables de informacion general
    getRole(); //Obtener y definir los roles para no tener los input
});

//La funcion desabilita los campos dependiendo del rol de usuario
function getRole() {
    let bool_user_role = $('#bool_user_role').val(); //Se obtienen los roles de usuario
    let new_variable = (bool_user_role && bool_user_role.trim() !== '') ? true : false; //Se validan para obtener una variable boolean
    if (!new_variable) { //Condicion para inabilitar las opciones
        $('#num_documento').prop('disabled', true);
        $('#fecha_inicio').prop('disabled', true);
        $('#fecha_fin').prop('disabled', true);
        $('#num_flojas').prop('disabled', true);
        $('#num_tomos').prop('disabled', true);
        $('#lugar').prop('disabled', true);
        $('#asunto').prop('disabled', true);

        $('#id_cat_area').prop('disabled', true); //Desabilitar selecct
        $('#id_usuario_area').prop('disabled', true);
        $('#id_usuario_enlace').prop('disabled', true);
        $('#id_cat_unidad').prop('disabled', true);
        $('#id_cat_coordinacion').prop('disabled', true);
        $('#id_cat_tramite').prop('disabled', true);
        $('#id_cat_clave').prop('disabled', true);
        $('#id_cat_remitente').prop('disabled', true);

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
        url: '/srh/public/letter/collection/dataClave',
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

/*
//La funcion obtiene el estatus del checkbox para marcar o desmarcar la casilla
function checkboxState() {
    let nameCheckbox = document.getElementById('rfc_remitente_bool').value; //se obitnee el valor del checkbox
    let state = true; //se inicializa el status en false
    nameCheckbox != 'true' ? state = false : state; //Validacion si el chechkbox es verdadero el status se queda en verdadero
    document.getElementById("rfc_remitente_bool").checked = state; //Se marca o desmarca el checkbox dependiendo del valor
    state ? showDiv('is_inputRemitente') : hideDiv('is_inputRemitente');// se muestra u oculta el contenido dependiendo del check
}

//Se oculta/muestra el div dependiendo si se selecciona el check o no
document.getElementById('rfc_remitente_bool').addEventListener('change', function () {

    if (this.checked) { //esta marcado el check
        showDiv('is_inputRemitente'); //Mostrar elementos ocultos
        cleanSelect('#id_cat_remitente'); //Se limpia el select
    } else {
        hideDiv('is_inputRemitente');//Ocultar elementos
        $('#rfc_remitente_aux').val('');
    }
});
*/
