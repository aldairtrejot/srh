document.getElementById("myForm").addEventListener("submit", function (event) {
    let fecha_inicio = document.getElementById('fecha_inicio').value;
    let fecha_fin = document.getElementById('fecha_fin').value;

    if (
        isFieldEmpty($('#programa_proyecto').val(), 'Nombre Curso') ||
        isFieldEmpty($('#fecha_inicio').val(), 'Fecha de inicio') ||
        isFieldEmpty($('#fecha_fin').val(), 'Fecha fin') ||
        isFieldEmpty($('#horas').val(), 'Horas') ||
        isPositiveIntegerUpTo100($('#horas').val(), 'Horas') ||
        isFieldEmpty($('#costo').val(), 'Costo') ||
        isPositiveDecimalEntero($('#costo').val(), 'Costo') || 
        isFieldEmpty($('#iva').val(), 'Iva') ||
        isPositiveIntegerUpTo100($('#iva').val(), 'Iva') ||
        isFieldEmpty($('#id_tbl_instructores').val(), 'Instructor') ||
        isFieldEmpty($('#id_cat_tipo_cursos').val(), 'Tipo Curso') ||
        isFieldEmpty($('#id_cat_coordinacion').val(), 'Coordinacion') ||
        isFieldEmpty($('#id_cat_beneficio').val(), 'Beneficio') ||
        isFieldEmpty($('#id_cat_organizacion').val(), 'Organizacion') ||
        isFieldEmpty($('#id_cat_tipo_accion').val(), 'Tipo Accion') ||
        isFieldEmpty($('#id_cat_modalidad').val(), 'Modalidad') ||
        isFieldEmpty($('#id_cat_categoria').val(), 'Categoria') ||
        isFieldEmpty($('#id_cat_nombre_accion').val(), 'Nombre Accion') ||
        isFieldEmpty($('#id_cat_programa_institucional').val(), 'Programa Institucional') ||
        isFieldEmpty($('#id_cat_estatuto_organico').val(), 'Estatuto Organico')      
    ) {
        event.preventDefault();  // Evita el envío del formulario
        return false;  // Detener la ejecución aquí
    }
});

function isPositiveDecimalEntero(value, fieldName) {
    let regex = /^(0|[1-9]\d*)(\.\d+)?$/;  // Expresión regular para enteros y decimales positivos
    if (!regex.test(value)) {
        notyfEM.error('Campo '+fieldName+' no valido' );
        return true;
    }
    return false;
}
function isPositiveInteger(value, fieldName) {
    let regex = /^(0|[1-9]\d*)$/;  // Expresión regular para enteros positivos, incluyendo el 0
    if (!regex.test(value)) {
        notyfEM.error('Campo ' + fieldName + ' no válido');
        return true;
    }
    return false;
}
function isPositiveIntegerUpTo100(value, fieldName) {
    let regex = /^(0|[1-9]\d{0,2})$/;  // Expresión regular para enteros positivos entre 0 y 100
    if (!regex.test(value) || parseInt(value) > 100) {
        notyfEM.error('Campo ' + fieldName + ' no válido');
        return true;
    }
    return false;
}








 