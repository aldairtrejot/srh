// CARGA MASIVA DE ALUMNOS
$(document).on('submit', '#massUploadForm', function (e) {
    e.preventDefault();
    console.log('✅ JS de carga masiva activo');

    let formData = new FormData(this);

    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $('#massUploadResults').hide();
            $('#resultBody').empty();
            $('#loadingIndicator').fadeIn();
        },
        success: function (response) {
            setTimeout(() => {
                $('#loadingIndicator').fadeOut();

                if (response && Array.isArray(response.data) && response.data.length > 0) {
                    $('#massUploadResults').fadeIn();
                    response.data.forEach(function (row, index) {
                        $('#resultBody').append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${row.curp || ''}</td>
                                <td>${row.rfc || ''}</td>
                                <td>${row.observacion || ''}</td>
                            </tr>
                        `);
                    });
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Carga completada!',
                        text: 'Archivo procesado correctamente. No se detectaron observaciones.'
                    });
                }
            }, 600);
        },
        error: function (xhr) {
            $('#loadingIndicator').fadeOut();
            let mensaje = 'Ocurrió un error al procesar el archivo.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                mensaje = xhr.responseJSON.message;
            }

            Swal.fire({
                icon: 'error',
                title: 'Error en la carga',
                text: mensaje
            });
        }
    });
});
