// Renderiza la tabla con los resultados
function renderTable(response) {
    const tbody = $('#template-table tbody');
    tbody.empty();

    if (response.value && response.value.length > 0) {
        response.value.forEach((object) => {
            const finalUrl = `${URL_DEFAULT}/coursesauditoria/edit/${object.id_cat_auditoria}`;
            const rowHTML = `
                <tr>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-transparent dropdown-toggle-split icon-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background: transparent;" data-toggle="tooltip" title="Menú">
                                <i class="fas fa-ellipsis-h" style="color: #9F2241; font-size: 2rem;"></i>
                            </button>
                            <div class="dropdown-menu">
                                <h6 class="dropdown-header">Acciones</h6>
                                <a class="dropdown-item" href="${finalUrl}">
                                    <span style="background:#1D5B3B" class="icon-container-template">
                                        <div style="text-align: center;">
                                            <i class="fa fa-pencil item-icon-menu"></i>
                                        </div>
                                    </span>
                                    Modificar
                                </a>
                                <a class="dropdown-item" href="#" onclick="confirmDelete(${object.id_cat_auditoria})">
                                    <span style="background:#6A1B3D" class="icon-container-template">
                                        <div style="text-align: center;">
                                            <i class="fa fa-trash item-icon-menu"></i>
                                        </div>
                                    </span>
                                    Eliminar
                                </a>
                            </div>
                        </div>
                    </td>
                    <td>${object.descripcion}</td>
                    <td>${object.estatus ? 'ACTIVO' : 'INACTIVO'}</td>
                </tr>
            `;
            tbody.append(rowHTML);
        });
        emptyContent = false;
    } else {
        tbody.html('<tr><td colspan="8" class="text-center">No se encontraron resultados</td></tr>');
        emptyContent = true;
    }
}