<div id="{{ $idModal }}" class="modal-backdrop" style="display: none;">
    <div class="modal-content custom-modal-content"
        style="width: {{ $width }}; height: {{ $height }}; overflow-y: auto;">
        <div class="modal-header custom-modal-header">
            <h2 style="color:black; font-family: 'Poppins', sans-serif; font-size: 1.5rem; font-weight: bold;">
                {{ $tittle }}</h2>
        </div>

        <div class="modal-body custom-modal-body">
            {{ $slot }}
        </div>

        <div class="modal-buttons custom-modal-buttons">
            <button style="font-weight: bold;" id="{{ $idCancel }}">Cancelar</button>
            <button style="font-weight: bold;color: #10312b" onclick="{{ $functionConfirm }}"
                id="{{ $idConfirm }}">Confirmar</button>
        </div>
    </div>
</div>