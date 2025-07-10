function validarcurp() {
    const valor = document.getElementById("datos").value;
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    if (!valor.trim()) {
        alert('Por favor ingresa un dato para buscar.');
        return;
    }

    // Loader inicial
    document.getElementById("contenidoTabla").innerHTML = `
        <tr><td colspan="8" class="text-center">Buscando...</td></tr>
    `;

    fetch(rutaBuscarEmpleado, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({ datos: valor })
    })
    .then(response => response.json())
    .then(data => {
        const tbody = document.getElementById("contenidoTabla");
        tbody.innerHTML = '';

        if (data && data.length > 0) {
    data.forEach(item => {
        tbody.innerHTML += `
            <tr>
                <td><i class="fas fa-eye"></i></td>
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
    tbody.innerHTML = `<tr><td colspan="8" class="text-center">No se encontraron resultados.</td></tr>`;
}

    })
    .catch(error => {
        console.error('Error en la búsqueda:', error);
    });
}


