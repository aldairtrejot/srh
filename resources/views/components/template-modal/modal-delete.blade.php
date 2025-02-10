<div id="{{ $tittleModal }}" class="modal-backdrop" style="display: none;">
    <div class="modal-content">
        <h2>¿Deseas eliminar este elemento?</h2>

        <!-- VALUE HIDDEN -->
        <input type="hidden" id="{{ $idInput }}" name="{{ $idInput }}" value="{{ $valueInput }}" />

        <div class="modal-buttons">
            <button style="font-weight: bold;" id="{{ $cancelModal }}">Cancelar</button>
            <button style="font-weight: bold; color:red" onclick="{{ $functionConfirm }}" id="{{ $confirmButton }}">Eliminar</button>
        </div>
    </div>
</div>