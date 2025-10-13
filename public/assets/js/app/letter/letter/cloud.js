//Scrip que se ejecuta con el formulario, para funciones u herramientas extras
//Ejecucion cuando carga el formulario
var token = $('meta[name="csrf-token"]').attr('content'); //Token for form
var id = $('#id').val();//Obtener elemento  (id_tbl_correspondencia)
var id_cat_area = $('#id_cat_area').val(); //Se obtiene el id de la area
var id_cat_entrada = $('#id_cat_entrada').val(); //Se obtiene el id de la area
var id_cat_tipo_oficio = $('#id_cat_tipo_oficio').val(); //Se obtiene el id de la area
var es_oficio = 1; //Identifica si es oficio
var es_anexo = 0;//Identifca si es un anexo

//Inicio de variables
$(document).ready(function () {
    getDataCloud();
    getDataDocument();
    getReplySummary(); // <-- llena el panel "Documento de Respuesta"

    $(window).click(function (event) {//El evento se utiliza para oculatar el modal de eliminar cuando se da click en cualquier parte distinta
        if ($(event.target).is('#modalBackdrop')) {
            $('#modalBackdrop').fadeOut(); // Ocultar la ventana modal
        }
    });
});

function getRole() {
    let bool_user_role = $('#bool_user_role').val(); //Se obtienen los roles de usuario
    let new_variable = (bool_user_role && bool_user_role.trim() !== '') ? true : false; //Se validan para obtener una variable boolean
    if (!new_variable) { //Condicion para inabilitar las opciones
        disabledInput('#label_oficio_entrada', '#icon_oficio_entrada', '#file_oficio_entrada');
        disabledInput('#label_anexo_entrada', '#icon_anexo_entrada', '#file_anexo_entrada');
    } else {
        enableIput('#label_oficio_entrada', '#icon_oficio_entrada', '#file_oficio_entrada');
        enableIput('#label_anexo_entrada', '#icon_anexo_entrada', '#file_anexo_entrada');
    }
}
//La funcion lista los documentos que existen en el cloud
function getDataDocument() {

    let bool_user_role = $('#bool_user_role').val(); //Se obtienen los roles de usuario
    let new_variable = (bool_user_role && bool_user_role.trim() !== '') ? true : false; //Se validan para obtener una variable boolean

    let container_anexo_entrada_vacio = $('#container_anexo_entrada_vacio');
    let container_anexo_entrada = $('#container_anexo_entrada');
    let container_oficio_entrada_vacio = $('#container_oficio_entrada_vacio');
    let container_oficio_entrada = $('#container_oficio_entrada');

    $.ajax({
        url: URL_DEFAULT.concat('/letter/cloud/anexos'),
        type: 'POST',
        data: {
            id_tbl_oficio: id, // <- tu endpoint actual lo espera así
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            let anexosEntrada = response.anexosEntrada;  // Suponiendo que la respuesta tiene una propiedad 'value' con los datos
            let oficosEntrada = response.oficosEntrada;

            //Habilita o desabilita los botones de agregar
            response.resultOficioEntrada || !new_variable ? disabledInput('#label_oficio_entrada', '#icon_oficio_entrada', '#file_oficio_entrada') : enableIput('#label_oficio_entrada', '#icon_oficio_entrada', '#file_oficio_entrada');
            response.resultAnexosEntrada || !new_variable ? disabledInput('#label_anexo_entrada', '#icon_anexo_entrada', '#file_anexo_entrada') : enableIput('#label_anexo_entrada', '#icon_anexo_entrada', '#file_anexo_entrada');

            templateCloud(new_variable, container_anexo_entrada, container_anexo_entrada_vacio, anexosEntrada); //Listamos la informacion
            templateCloud(new_variable, container_oficio_entrada, container_oficio_entrada_vacio, oficosEntrada); //Listamos la informacion
        },
    });
}

//La funcion obtiene los datos del encabezado de cloud
function getDataCloud() {
    $.ajax({
        url: URL_DEFAULT.concat('/letter/cloud/data'),
        type: 'POST',
        data: {
            id: id,
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            let item = response.value; //Obtenemos la consulta
            $('#_noOficio').text(item.num_turno_sistema); // establecer los valores
            $('#_noCorrespondencia').text(item.num_documento); // establecer los valores
            $('#_noAnio').text(item.anio); // establecer los valores
            $('#_fechaInicio').text(item.fecha_inicio); // establecer los valores
            $('#_fechaFin').text(item.fecha_fin); // establecer los valores
        },
    });
}

// ========= NUEVO: Lado derecho (solo lectura) =========
function getReplySummary() {
    $.ajax({
        url: URL_DEFAULT.concat('/letter/cloud/reply'),
        type: 'POST',
        data: {
            id: id,      // id_tbl_correspondencia
            _token: token
        },
        success: function (r) {
            // Texto
            $('#resp_asunto').text(r.asunto || '—');
            $('#resp_observaciones').text(r.observaciones || '—');

            // Oficio de salida
            const ofiVacio = $('#container_oficio_salida_vacio');
            const ofiCont  = $('#container_oficio_salida');
            ofiCont.empty();
            if (Array.isArray(r.oficiosSalida) && r.oficiosSalida.length > 0) {
                ofiVacio.hide();
                r.oficiosSalida.forEach(function (v) {
                    // generateFileHTML(boolPermitirEliminar, template)
                    ofiCont.append(generateFileHTML(false, v)); // false => sin botón de eliminar
                });
            } else {
                ofiVacio.show();
            }

            // Anexos de salida
            const aneVacio = $('#container_anexo_salida_vacio');
            const aneCont  = $('#container_anexo_salida');
            aneCont.empty();
            if (Array.isArray(r.anexosSalida) && r.anexosSalida.length > 0) {
                aneVacio.hide();
                r.anexosSalida.forEach(function (v) {
                    aneCont.append(generateFileHTML(false, v));
                });
            } else {
                aneVacio.show();
            }
        }
    });
}


//La funcion sube el archivo que el usuario esta seleccionando
document.getElementById('file_oficio_entrada').addEventListener('change', function (event) {
    if (event.target.files.length > 0) {
        sendFile(event.target.files[0], id_cat_entrada, es_oficio); // Pasa el archivo real a la función
    }
});

//La funcion sube el archivo que el usuario esta seleccionando
document.getElementById('file_anexo_entrada').addEventListener('change', function (event) {
    if (event.target.files.length > 0) {
        sendFile(event.target.files[0], id_cat_entrada, es_anexo); // Pasa el archivo real a la función
    }
});


function sendFile(file, id_entrada_salida, esOficio) {
    if (file) {
        showSpinner();// Inicio de spinner
        let data = new FormData();// Crear el objeto FormData
        data.append('file', file);
        data.append('id_cat_tipo_oficio', id_cat_tipo_oficio);
        data.append('id_cat_area', id_cat_area);
        data.append('id', id);
        data.append('id_entrada_salida', id_entrada_salida);
        data.append('esOficio', esOficio);
        $.ajax({
            url: URL_DEFAULT.concat("/letter/cloud/upload"),
            type: 'POST',
            data: data, // Enviar directamente el FormData
            processData: false,  // No procesar los datos
            contentType: false,  // El navegador define el boundary
            headers: {
                'X-CSRF-TOKEN': token
            },
            success: function (response) {
                hideSpinner(); // Se oculta el spinner
                if (response.status) { //Validacion si es que los cambios se han agregado correctamente
                    notyfEM.success("Documento agregado correctamente.");
                } else {
                    notyfEM.error(response.messages);
                }
                getDataDocument(); //Lista de nuevo el directorio (entrada)
                // getReplySummary(); // <- normalmente no cambia la respuesta, por eso lo dejo comentado
            },
        });
    }
}

//La funcion elimina un documento
function deleteDocument(uid) {
    $('#modalBackdrop').fadeIn();//Iniciar ventana modal

    $('#cancelBtn').click(function () { //Se pulsa el boton de cancelar
        $('#modalBackdrop').fadeOut(); // Cerrar la ventana modal
    });

    $('#confirmBtn').click(function () {///Se da click al boton de confirmar y se ejecuta el evento de eliminacion
        deleteDocumenServer(uid);
        $('#modalBackdrop').fadeOut(); // Cerrar modal después de confirmar
    });
}

//La funcion elimina oficios del repositorio, solo de la base
function deleteDocumenServer(uid) {
    $.ajax({
        url: URL_DEFAULT.concat('/letter/cloud/delete'),
        type: 'POST',
        data: {
            uid: uid,
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            if (response.messages) {
                notyfEM.success("El archivo se eliminó correctamente.");
            } else {
                notyfEM.error("Algo inesperado ocurrió al realizar la acción.");
            }
            getDataDocument(); //Lista de nuevo e directorio
            // getReplySummary(); // <- si algún día permites borrar en el panel derecho, descomenta
        },
    });
}

