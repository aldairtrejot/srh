window.addEventListener('DOMContentLoaded', () => {
    if (typeof empleadoData !== 'undefined') {
        document.getElementById("file_nombre").innerText = empleadoData.nombre ?? '-';
        document.getElementById("file_primer_apellido").innerText = empleadoData.primer_apellido ?? '-';
        document.getElementById("file_segundo_apellido").innerText = empleadoData.segundo_apellido ?? '-';
        document.getElementById("file_rfc").innerText = empleadoData.rfc ?? '-';
        document.getElementById("file_curp").innerText = empleadoData.curp ?? '-';
        document.getElementById("file_unidad").innerText = empleadoData.nombre_unidad ?? '-';
        document.getElementById("file_coordinacion").innerText = empleadoData.nombre_coordinacion ?? '-';
    } else {
        console.warn("empleadoData no está definido.");
    }
});


