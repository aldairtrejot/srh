
//Scrip que se ejecuta con el formulario, para funciones u herramientas extras
//Ejecucion cuando carga el formulario
var token = $('meta[name="csrf-token"]').attr('content'); //Token for form

$(document).ready(function () {
    $('select').selectpicker(); //Iniciar los select
    //checkboxState();
    setData(); //Establecer las variables de informacion general
    getRole(); //Obtener y definir los roles para no tener los input
    setCheckboxArea();
});

//La funcion activa o desactiva el valor de un checkbox de area
function setCheckboxArea() {
    if (!$('#es_por_area').val()) {
        console.log('falso');
    }
}


//La funcion desabilita los campos dependiendo del rol de usuario
function getRole() {
    let bool_user_role = $('#bool_user_role').val(); //Se obtienen los roles de usuario
    let new_variable = (bool_user_role && bool_user_role.trim() !== '') ? true : false; //Se validan para obtener una variable boolean
    if (!new_variable) { //Condicion para inabilitar las opciones
        $('#num_documento').prop('disabled', true);
        $('#fecha_inicio').prop('disabled', true);
        $('#fecha_fin').prop('disabled', true);
        $('#asunto').prop('disabled', true);

        $('#id_cat_area').prop('disabled', true); //Desabilitar selecct
        $('#id_usuario_area').prop('disabled', true);
        $('#id_usuario_enlace').prop('disabled', true);
        $('#id_cat_remitente').prop('disabled', true);

        $('#id_cat_area').selectpicker('refresh'); //Refresh de select 
        $('#id_usuario_area').selectpicker('refresh');
        $('#id_usuario_enlace').selectpicker('refresh');
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

//La funcion obtiene el año
function getData() {

    let id_cat_anio = $('#id_cat_anio').val();//Obtener elemento

    $.ajax({
        url: '/srh/public/year/getYear',
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

