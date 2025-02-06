<div id="{{ $idModal }}" class="modal-backdrop" style="display: none;">
    <div class="modal-content">
        <h2>{{ $tittle }}</h2>

        <!-- VALUE HIDDEN -->
        <input type="hidden" id="{{ $idInput }}" name="{{ $idInput }}" value="{{ $valueInput }}" />

        <div class="modal-buttons">
            <button id="{{ $idCancel }}">Cancelar</button>
            <button onclick="{{ $functionConfirm }}" style="color: #10312b" id="{{ $idConfirm }}">Confirmar</button>
        </div>
    </div>
</div>