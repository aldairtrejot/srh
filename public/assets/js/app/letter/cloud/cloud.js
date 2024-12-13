// La funcion retorna los valores para listar los archivos de cloud
function templateCloud(templateData, templateDataNull, data) {
    // Limpiar el contenedor antes de agregar nuevos elementos
    templateData.empty();

    // Verificamos si hay datos en los anexos
    if (data.length !== 0) {
        // Si hay información, ocultamos el mensaje "sin contenido"
        templateDataNull.hide();

        // Iteramos sobre los anexos y creamos los elementos HTML dinámicamente
        data.forEach(function (valueTemplate) {
            // Creamos el HTML para cada anexo utilizando la segunda función
            let fileHTML = generateFileHTML(valueTemplate);

            // Insertamos el HTML generado en el contenedor
            templateData.append(fileHTML);
        });
    } else {
        // Si no hay información, mostramos el mensaje de "sin contenido"
        templateDataNull.show();
    }
}

// La función que genera el HTML para cada archivo de cloud
function generateFileHTML(template) {
    return `
        <div class="custom-file-container">
            <div class="custom-file-icon-container">
                <i style="color:#777777" class="fa fa-file" aria-hidden="true"></i>
                <div class="custom-button-container">
                <!--
                    <button onclick="getInfo('${template.id}')" style="background: #003366" class="custom-button" title="Usuario">
                        <i style="color: white" class="fa fa-user"></i>
                    </button>
                    <button onclick="seeDocument('${template.uid}')" style="background: #1D5B3B" class="custom-button" title="Ver" disabled>
                        <i style="color: white" class="fa fa-eye"></i>
                    </button>-->
                    <button onclick="download('${template.uid}')" style="background: #707070" class="custom-button" title="Descargar">
                        <i style="color: white" class="fa fa-download"></i>
                    </button>
                    <button onclick="deleteDocument('${template.uid}')" style="background: #6A1B3D" class="custom-button" title="Eliminar">
                        <i style="color: white" class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="custom-file-name">
                <p>${template.nombre}</p>
            </div>
        </div>
    `;
}

//La funcion desabilita input tomando como parametro el nombre
function disabledInput(idLabel, idIcon, idValue) {
    $(idLabel).css('color', 'gray');  // Cambiar color del texto a gris
    $(idIcon).css('color', 'gray');   // Cambiar color del ícono a gris
    $(idValue).prop('disabled', true);  // Deshabilitar el input
    $(idLabel).css('cursor', 'not-allowed');
}

//La funcion habilita el campo para que se puedan cargar mas archivos
function enableIput(idLabel, idIcon, idValue) {
    $(idLabel).css('color', 'red');  // Restaurar color del texto
    $(idIcon).css('color', '');      // Restaurar color del ícono
    $(idValue).prop('disabled', false);  // Habilitar el input
    $(idLabel).css('cursor', 'pointer');  // Restaurar el cursor normal
}

function download(uid) {
    // Crear una URL para la descarga
    let url = '/srh/public/cloud/download';

    // Crear un formulario temporal para enviar el UID y activar la descarga
    let form = document.createElement('form');
    form.method = 'POST';
    form.action = url;

    // Añadir un campo oculto para el UID
    let uidField = document.createElement('input');
    uidField.type = 'hidden';
    uidField.name = 'uid';
    uidField.value = uid;
    form.appendChild(uidField);

    // Añadir el token CSRF (si es necesario)
    let tokenField = document.createElement('input');
    tokenField.type = 'hidden';
    tokenField.name = '_token';
    tokenField.value = token;  // Asegúrate de que 'token' esté correctamente definido
    form.appendChild(tokenField);

    // Añadir el formulario al body y enviarlo
    document.body.appendChild(form);
    form.submit();

    // Limpiar el formulario después de enviarlo
    document.body.removeChild(form);
}

//Se utiliza la funcion para descargar archivos de alfresco
function seeDocument(uid) {
    $.ajax({
        url: '/srh/public/cloud/see',  // Ruta del servidor que devuelve la URL del documento
        type: 'POST',
        data: {
            uid: uid,
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            // Verifica que la respuesta contenga una URL válida
            if (response.status && response.url) {
                // Abrir la URL en una nueva pestaña o ventana
                window.open(response.url, '_blank');  // '_blank' abre en una nueva pestaña
            } else {
                console.log("Error: No se pudo obtener la URL del archivo.");
            }
        },
        error: function (xhr, status, error) {
            console.log("Error al hacer la solicitud AJAX: " + error);
        }
    });
}

