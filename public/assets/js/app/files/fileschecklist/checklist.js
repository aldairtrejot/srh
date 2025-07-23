document.addEventListener('DOMContentLoaded', () => {
    // Listener para checkboxes individuales (guardar al cambiar)
    document.querySelectorAll('.check-doc').forEach(checkbox => {
        checkbox.addEventListener('change', async function () {
            const idDocumento = this.dataset.idDocumento;
            const idGestion = this.dataset.idGestion;
            const estatus = this.checked ? 1 : 0;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch(`${BASE_URL}/fileschecklist/guardar-check`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        id_cat_documento: idDocumento,
                        id_tbl_gestion_documentos: idGestion,
                        estatus: estatus
                    })
                });

                const result = await response.json();
                if (result.success) {
                    mostrarAlertaExito(); // ✅ Mostramos burbuja
                } else {
                    console.error('No se pudo guardar el estatus del documento.');
                }
            } catch (e) {
                console.error('Error en la petición:', e);
            }
        });
    });
});

/**
 * ✅ Función para mostrar alerta tipo burbuja
 */
function mostrarAlertaExito() {
    const alerta = document.getElementById('alerta-exito');
    if (!alerta) return;

    alerta.style.display = 'block';
    setTimeout(() => {
        alerta.style.display = 'none';
    }, 1000); // Oculta la alerta después de 1 segundos
}




