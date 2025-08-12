function generateReportX() {

    showSpinner(); // Mostrar spinner

    $.ajax({
        url: URL_DEFAULT.concat('/guest/generate'),
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        xhrFields: {
            responseType: 'blob'
        },
        success: function (response, status, xhr) {
            const filename = "DATA_GC_GUEST_SIRH.xlsx";
            const blob = new Blob([response], { type: xhr.getResponseHeader('Content-Type') });
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            hideSpinner();
            notyfEM.success("El archivo Excel se ha descargado correctamente.");
        },
        error: function (xhr, status, error) {
            console.log(xhr);
            hideSpinner();
            notyfEM.error("Ocurrió un problema al generar el archivo.");
        }
    });
}