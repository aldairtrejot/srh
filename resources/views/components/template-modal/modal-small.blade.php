<div id="{{ $idModal }}" class="modal-backdrop" style="display: none;">
    <div class="modal-content">
        <h2>{{ $tittle }}</h2>

        <!-- VALUE HIDDEN -->
        <input type="hidden" id="{{ $idInput }}" name="{{ $idInput }}" value="{{ $valueInput }}" />

        <div class="modal-buttons">
            <button style="font-weight: bold;" id="{{ $idCancel }}">Cancelar</button>
            <button style="font-weight: bold;color: #10312b" onclick="{{ $functionConfirm }}" id="{{ $idConfirm }}">Confirmar</button>
        </div>
    </div>
</div>