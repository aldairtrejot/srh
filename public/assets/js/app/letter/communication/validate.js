// Validacion de fórmulario
document.getElementById("myForm").addEventListener("submit", function (event) {
    if (//Validacion de campos requeridos y max caracteres
        isFieldEmpty($('#asunto').val(), 'Asunto') ||
        isFieldEmpty($('#observaciones').val(), 'Observaciones') ||
        isFieldEmpty($('#id_cat_tema').val(), 'Tema') ||
        isFieldEmpty($('#id_cat_entidad').val(), 'Lugar') ||
        isFieldEmpty($('#id_cat_area_interno').val(), 'Área / Zona') ||
        isFieldEmpty($('#id_cat_solicitante').val(), 'Solicitante') ||
        isFieldEmpty($('#id_cat_destinatario').val(), 'Destinatario') ||
        isFieldEmpty($('#cargo_destinatario').val(), 'Cargo destinatario') ||
        isExceedingLength($('#asunto').val(), 'Asunto', 300) ||
        isExceedingLength($('#observaciones').val(), 'Observaciones', 150) ||
        isExceedingLength($('#cargo_destinatario').val(), 'Asunto', 150)) {
        event.preventDefault();  // Evita el envío del formulario
        return;  // Detener la ejecución aquí
    }
});


