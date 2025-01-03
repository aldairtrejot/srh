<style>
    /* Contenedor del input */
    .custom-input-container {
        margin-bottom: 20px;
        /* Espaciado entre los campos */
    }

    /* Estilo para el label */
    .custom-input-label {
        width: 95%;
        font-size: 16px;
        /* Tamaño del texto del label */
        color: black;
        /* Color del texto del label */
        font-weight: ;
        text-align: left;
        /* Estilo de texto en negritas */
        margin-bottom: 5px;
        /* Espacio entre el label y el input */
        display: block;
        /* Asegura que el label esté arriba del input */
    }

    /* Estilo para el input */
    .custom-input-field {
        width: 95%;
        /* El input ocupará el 100% del contenedor */
        padding: 10px;
        /* Espaciado interno del input */
        border: 1px solid #ccc;
        /* Borde gris claro */
        border-radius: 2px;
        /* Bordes redondeados */
        font-size: 14px;
        /* Tamaño de fuente para el texto del input */
    }

    .custom-input-field:focus {
        border-color: rgb(121, 122, 122);
        /* Cambia el color del borde al hacer focus */
        outline: none;
        /* Elimina el contorno predeterminado del navegador */
    }
</style>

<div id="_modalChangePassword" class="modal-backdrop" style="display: none;">
    <div class="modal-content" style="width: 430px; height: 380px;">
        <h3
            style="font-weight: bold; color: #10312b; text-align: left; display: flex; align-items: center; margin-bottom: 20px;">
            <i class="fas fa-lock" style="font-size: 20px; color: #10312b; margin-right: 10px;"></i>
            Modificar contraseña
        </h3>

        <form id="myFormPassword" action="{{ route('inside.save') }}" method="POST">
            <div class="custom-input-container">
                <label class="custom-input-label" for="customTextInput">Contraseña anterior</label>
                <input type="password" id="oldPassword" class="custom-input-field">
            </div>

            <div class="custom-input-container">
                <label class="custom-input-label" for="customTextInput">Nueva contraseña</label>
                <input type="password" id="newPassword" name="newPassword" class="custom-input-field">
            </div>

            <div class="custom-input-container">
                <label class="custom-input-label" for="customTextInput">Confirmar contraseña</label>
                <input type="password" id="confirmPassword" class="custom-input-field">
            </div>

            <div class="modal-buttons">
                <button type="button" id="_cancelAction">Cancelar</button>
                <button onclick="validatePassword()" type="submit" style="font-weight: bold; color: #10312b;"
                    id="_confirmAction">Confirmar</button>
            </div>
        </form>
    </div>
</div>