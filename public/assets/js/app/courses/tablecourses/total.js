document.addEventListener('DOMContentLoaded', function() {
    // Obtén los elementos del formulario
    const costoInput = document.getElementById('costo');
    const ivaInput = document.getElementById('iva');
    const costoTotalInput = document.getElementById('costo_total');

    // Función para calcular el Costo Total
    function calcularCostoTotal() {
        const costo = parseFloat(costoInput.value) || 0;  // Si no es un número, asigna 0
        const iva = parseFloat(ivaInput.value) || 0;      // Si no es un número, asigna 0

        // Calcula el costo total (costo + iva)
        const costoTotal = costo + (costo * iva / 100);

        // Asigna el valor calculado al campo "Costo Total"
        costoTotalInput.value = costoTotal.toFixed(2); // Redondea a dos decimales
    }

    // Escucha los cambios en los campos de Costo e Iva
    costoInput.addEventListener('input', calcularCostoTotal);
    ivaInput.addEventListener('input', calcularCostoTotal);

    costoTotalInput.disabled = true; 
});
