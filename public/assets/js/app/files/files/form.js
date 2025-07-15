let iterator = 1;
let emptyContent = false;

function validarcurp() {
    const valor = document.getElementById("datos").value.trim();
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    if (!valor) {
        alert('Por favor ingresa un dato para buscar.');
        return;
    }

    document.getElementById("contenidoTabla").innerHTML = `
        <tr>
            <td colspan="9" class="text-center py-4">
                <i class="fas fa-spinner fa-spin fa-2x text-secondary"></i>
            </td>
        </tr>
    `;

    fetch(rutaBuscarEmpleado, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({ datos: valor, page: iterator })
    })
    .then(response => response.json())
    .then(data => {
        const tbody = document.getElementById("contenidoTabla");
        tbody.innerHTML = '';

        if (data && data.length > 0) {
            emptyContent = false;

            data.forEach(item => {
                const finalUrl = `/srh/public/fileschecklist/view/${item.id}`; // Ruta personalizada por ID

                tbody.innerHTML += `
                    <tr>
                        <td>
                            <div class="button-container" style="display: flex; gap: 2px;">
                                <a href="${finalUrl}" style="background: #10312b; padding: 8px 12px;" class="custom-button custom-button-x" title="Checklist">
                                    <i style="color: white; font-size: 15px" class="fa-solid fa-file-zipper"></i>
                                </a>
                            </div>
                        </td>
                        <td>${item.rfc ?? '-'}</td>
                        <td>${item.curp ?? '-'}</td>
                        <td>${item.nombre ?? '-'}</td>
                        <td>${item.primer_apellido ?? '-'}</td>
                        <td>${item.segundo_apellido ?? '-'}</td>
                        <td>${item.ultima_fecha_movimiento ?? '-'}</td>
                        <td>${item.nombre_movimiento ?? '-'}</td>
                    </tr>
                `;
            });
        } else {
            emptyContent = true;
            tbody.innerHTML = `<tr><td colspan="9" class="text-center">No se encontraron resultados.</td></tr>`;
        }

        setValue();
    })
    .catch(error => {
        console.error('Error en la búsqueda:', error);
    });
}

function searchInit() {
    validarcurp();
}

function paginatorMax1() {
    iterator = emptyContent ? iterator : iterator += 1;
    setValue();
    searchInit();
}

function paginatorMax5() {
    iterator = emptyContent ? iterator : iterator += 5;
    setValue();
    searchInit();
}

function paginatorMin5() {
    let iteratorAux = iterator;
    iterator = (iteratorAux -= 5) > 0 ? (iterator -= 5) : 1;
    setValue();
    searchInit();
}

function paginatorMin1() {
    let iteratorAux = iterator;
    iterator = (iteratorAux -= 1) > 0 ? (iterator -= 1) : 1;
    setValue();
    searchInit();
}

function setValue() {
    document.getElementById("is_iterator").innerText = iterator;
    document.getElementById("is_iteratorMin").innerText = iterator - 1;
    document.getElementById("is_iteratorMax").innerText = iterator + 1;
}




