document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("myForm");
    const descripcionInput = document.getElementById("descripcion");

    if (!form || !descripcionInput) return; // Evita errores si el DOM no tiene esos elementos

    const originalValue = descripcionInput.value.trim().toUpperCase();

    form.addEventListener("submit", function (event) {
        const value = descripcionInput.value.trim();

        if (value === "") {
            alert("El campo 'Descripción' no puede estar vacío.");
            event.preventDefault();
            return;
        }

        if (value.length > 200) {
            alert("El campo 'Descripción' no debe exceder los 200 caracteres.");
            event.preventDefault();
            return;
        }

    });
});
