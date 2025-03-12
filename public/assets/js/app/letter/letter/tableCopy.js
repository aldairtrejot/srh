// Ejecuta la consulta para la tabla de com copia a de turnos

var token = $('meta[name="csrf-token"]').attr('content'); //Token for form

function searchInitToCopy(idLetter) {
    $.ajax({
        url: URL_DEFAULT.concat('/letter/tableCopy'),
        type: 'POST',
        data: {
            id: idLetter,
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            const tbody = $('#template-table-copy tbody');
            tbody.empty(); // Limpiar la tabla

            if (response.value && response.value.length > 0) {
                response.value.forEach(function (objectCy) {

                    // Generar el HTML con template literals
                    const rowHTML = `
                    <tr>
                        <td class="button-column">
                            <button onclick="openModalDelete('${objectCy.id}')" style="background: #6A1B3D" class="custom-button" title="Eliminar">
                                <i style="color: white; font-size: 15px" class="fa fa-trash"></i>
                            </button>
                        </td>
                        <td>${objectCy.area}</td>
                        <td>${objectCy.tramite}</td>
                        <td>${objectCy.clave}</td>
                        <td>${objectCy.usuario}</td>
                        <td>${objectCy.enlace}</td>
                    </tr>
                `;
                    tbody.append(rowHTML);
                });
                emptyContent = false;
                //talldropdown(response.value.length, 1); // Scroll en dropw
            } else {
                tbody.html('<tr><td colspan="8" class="text-center">No se encontraron resultados</td></tr>');
                emptyContent = true;
                setValue();
            }
        },
    });
}
