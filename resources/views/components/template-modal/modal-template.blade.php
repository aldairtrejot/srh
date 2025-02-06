<style>
    /* Nueva clase para el contenido del modal */
    .custom-modal-content {
        background-color: white;
        /* Fondo blanco */
        border-radius: 0 !important;
        /* Sin bordes en el modal */
        display: flex;
        flex-direction: column;
        padding: 0px;
    }

    /* Nueva clase para la franja verde del título */
    .custom-modal-header {
        background-color: #10312b !important;
        /* Fondo verde */
        color: white;
        padding: 10px 0 !important;
        /* Espaciado vertical sin márgenes horizontales */
        margin: 0 !important;
        /* Eliminar márgenes para que ocupe todo el ancho */
        text-align: center;
        width: 100% !important;
        /* Asegura que el encabezado ocupe todo el ancho */
        box-sizing: border-box !important;
        /* Asegura que el padding no afecte el ancho total */
    }

    /* Nueva clase para el contenido del modal */
    .custom-modal-body {
        padding: 20px !important;
        /* Asegura el padding deseado */
        flex-grow: 1;
        overflow-y: auto;
        /* Permite desplazamiento si el contenido es largo */
    }

    /* Nueva clase para los botones */
    .custom-modal-buttons {
        display: flex;
        justify-content: flex-end;
        padding: 10px;
    }

    .custom-modal-buttons button {
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 5px;
        cursor: pointer;
        margin-left: 10px;
        /* Espacio entre los botones */
    }
</style>


<div id="{{ $idModal }}" class="modal-backdrop" style="display: none;">
    <div class="modal-content custom-modal-content" style="width: 500px; height: 300px; overflow-y: auto;">
        <div class="modal-header custom-modal-header">
            <h2>{{ $tittle }}</h2>
        </div>

        <div class="modal-body custom-modal-body">
            {{ $slot }}
        </div>

        <div class="modal-buttons custom-modal-buttons">
            <button id="{{ $idCancel }}">Cancelar</button>
            <button onclick="{{ $functionConfirm }}" style="color: #10312b" id="{{ $idConfirm }}">Confirmar</button>
        </div>
    </div>
</div>