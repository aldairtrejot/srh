//Scrip que se ejecuta con el formulario, para funciones u herramientas extras
//Ejecucion cuando carga el formulario
var token = $('meta[name="csrf-token"]').attr('content'); //Token for form
var id = $('#id').val();//Obtener elemento  (id_tbl_correspondencia)
var id_cat_area = $('#id_cat_area').val(); //Se obtiene el id de la area
var id_cat_entrada = $('#id_cat_entrada').val(); //Se obtiene el id de la area
var id_cat_tipo_oficio = $('#id_cat_tipo_oficio').val(); //Se obtiene el id de la area
var es_oficio = 1; //Identifica si es oficio
var es_anexo = 0;//Identifca si es un anexo

// ===== NUEVO: datos para anexos de respuesta (lado derecho) =====
var replyOficioId      = null;  // id_tbl_oficio (respuesta)
var replyAnexosCount   = 0;     // cuantos anexos de salida hay
var MAX_ANEXOS_SALIDA  = 3;     // se sobreescribe con lo que mande el back

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

//La funcion lista los documentos que existen en el cloud (ENTRADA)
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
            let anexosEntrada = response.anexosEntrada;
            let oficosEntrada = response.oficosEntrada;

            //Habilita o desabilita los botones de agregar (ENTRADA)
            response.resultOficioEntrada || !new_variable
                ? disabledInput('#label_oficio_entrada', '#icon_oficio_entrada', '#file_oficio_entrada')
                : enableIput('#label_oficio_entrada', '#icon_oficio_entrada', '#file_oficio_entrada');

            response.resultAnexosEntrada || !new_variable
                ? disabledInput('#label_anexo_entrada', '#icon_anexo_entrada', '#file_anexo_entrada')
                : enableIput('#label_anexo_entrada', '#icon_anexo_entrada', '#file_anexo_entrada');

            templateCloud(new_variable, container_anexo_entrada, container_anexo_entrada_vacio, anexosEntrada);
            templateCloud(new_variable, container_oficio_entrada, container_oficio_entrada_vacio, oficosEntrada);
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
            $('#_noOficio').text(item.num_turno_sistema);
            $('#_noCorrespondencia').text(item.num_documento);
            $('#_noAnio').text(item.anio);
            $('#_fechaInicio').text(item.fecha_inicio);
            $('#_fechaFin').text(item.fecha_fin);
        },
    });
}

// ========= Lado derecho: Documento de respuesta (solo lectura + subir anexos) =========
function getReplySummary() {
    $.ajax({
        url: URL_DEFAULT.concat('/letter/cloud/reply'),
        type: 'POST',
        data: {
            id: id,      // id_tbl_correspondencia
            _token: token
        },
        success: function (r) {
            console.log(r);

            // Guardar info global
            replyOficioId     = r.id_oficio || null;
            MAX_ANEXOS_SALIDA = r.max_anexos_salida || 3;
            replyAnexosCount  = Array.isArray(r.anexosSalida) ? r.anexosSalida.length : 0;

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
                    ofiCont.append(generateFileHTML(false, v)); // sin eliminar
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
                    aneCont.append(generateFileHTML(false, v)); // sin eliminar
                });
            } else {
                aneVacio.show();
            }

            // Habilitar / deshabilitar botón "Cargar" de anexos de respuesta
            toggleReplyAnexoUpload();
        }
    });
}

// ====== EVENTOS DE SUBIDA (ENTRADA) ======

//La funcion sube el archivo que el usuario esta seleccionando (OFICIO ENTRADA)
document.getElementById('file_oficio_entrada').addEventListener('change', function (event) {
    if (event.target.files.length > 0) {
        sendFile(event.target.files[0], id_cat_entrada, es_oficio);
    }
});

//La funcion sube el archivo que el usuario esta seleccionando (ANEXO ENTRADA)
document.getElementById('file_anexo_entrada').addEventListener('change', function (event) {
    if (event.target.files.length > 0) {
        sendFile(event.target.files[0], id_cat_entrada, es_anexo);
    }
});

// ====== EVENTO DE SUBIDA (ANEXO SALIDA / RESPUESTA) ======
var fileAnexoSalidaEl = document.getElementById('file_anexo_salida');
if (fileAnexoSalidaEl) {
    fileAnexoSalidaEl.addEventListener('change', function (event) {
        var files = event.target.files || [];
        if (!files.length) return;
        sendReplyAnexo(files[0]);
        event.target.value = ''; // limpiar input
    });
}

// ====== FUNCIÓN PARA SUBIR ANEXO DE RESPUESTA ======
function sendReplyAnexo(file) {
    if (!file) return;

    if (!replyOficioId) {
        if (typeof notyfEM !== 'undefined' && notyfEM.error) {
            notyfEM.error('No hay oficio de respuesta para adjuntar.');
        } else {
            alert('No hay oficio de respuesta para adjuntar.');
        }
        $('#file_anexo_salida').val('');
        return;
    }

    var remaining = MAX_ANEXOS_SALIDA - replyAnexosCount;
    if (remaining <= 0) {
        if (typeof notyfEM !== 'undefined' && notyfEM.error) {
            notyfEM.error('Solo se permiten hasta ' + MAX_ANEXOS_SALIDA + ' anexos de respuesta.');
        } else {
            alert('Solo se permiten hasta ' + MAX_ANEXOS_SALIDA + ' anexos de respuesta.');
        }
        $('#file_anexo_salida').val('');
        return;
    }

    showSpinner();

    var data = new FormData();
    data.append('file', file);
    data.append('id_tbl_oficio', replyOficioId);
    data.append('id_tbl_correspondencia', id);
    data.append('id_cat_area', id_cat_area);
    data.append('id_cat_entrada', id_cat_entrada);
    data.append('id_cat_tipo_oficio', id_cat_tipo_oficio);

    $.ajax({
        url: URL_DEFAULT.concat('/letter/cloud/reply/upload-anexo'),
        type: 'POST',
        data: data,
        processData: false,
        contentType: false,
        headers: { 'X-CSRF-TOKEN': token },
        success: function (resp) {
            hideSpinner();
            $('#file_anexo_salida').val('');

            if (resp.status) {
                if (typeof notyfEM !== 'undefined' && notyfEM.success) {
                    notyfEM.success(resp.message || 'Anexo agregado correctamente.');
                } else {
                    alert('Anexo agregado correctamente.');
                }
                // Recargar panel derecho para actualizar conteo y lista
                getReplySummary();
            } else {
                if (typeof notyfEM !== 'undefined' && notyfEM.error) {
                    notyfEM.error(resp.message || 'No fue posible subir el anexo.');
                } else {
                    alert(resp.message || 'No fue posible subir el anexo.');
                }
            }
        },
        error: function () {
            hideSpinner();
            $('#file_anexo_salida').val('');
            if (typeof notyfEM !== 'undefined' && notyfEM.error) {
                notyfEM.error('Error al subir el anexo.');
            } else {
                alert('Error al subir el anexo.');
            }
        }
    });
}

// ====== Habilitar / deshabilitar botón "Cargar" anexos de respuesta ======
function toggleReplyAnexoUpload() {
    var bool_user_role = $('#bool_user_role').val();
    var hasRole        = !!(bool_user_role && bool_user_role.trim() !== '');
    var remaining      = MAX_ANEXOS_SALIDA - replyAnexosCount;

    if (!hasRole || !replyOficioId || remaining <= 0) {
        disabledInput('#label_anexo_salida', '#icon_anexo_salida', '#file_anexo_salida');
    } else {
        enableIput('#label_anexo_salida', '#icon_anexo_salida', '#file_anexo_salida');
        $('#file_anexo_salida').attr('data-remaining', remaining);
    }
}

// ====== SUBIDA DE ARCHIVOS (ENTRADA) ======
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
        },
    });
}

