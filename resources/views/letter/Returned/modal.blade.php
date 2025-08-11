<x-template-modal.modal-template 
    tittle="Asignar Subárea"
    idModal="modalReturnado"
    idCancel="cancel_returnado"
    idConfirm="confirm_returnado"
    functionConfirm="return false;"
    width="1200px"
    height="auto">

    {{-- Fila 1: bloque de Área + ayuda --}}
    <div class="row">
        <!-- Área -->
        <div class="col-12 col-md-6">
            <div class="custom-col">
                <label class="label-time d-block mb-2">Área</label>
                <div class="border rounded p-2">
                    <strong>Área actual:</strong>
                    <span id="areaActual" class="ml-1">—</span>
                </div>
            </div>
        </div>

        <!-- Estado / Mensaje -->
        <div class="col-12 col-md-6">
            <div class="custom-col">
                <label class="label-time d-block mb-2">Estado</label>
                <div class="border rounded p-2 text-muted" id="returnadoEstado">
                    Selecciona una subárea de la tabla para asignarla.
                </div>
            </div>
        </div>
    </div>

    {{-- Fila 2: tabla de subáreas --}}
    <div class="row mt-3">
        <div class="col-12">
            <div class="custom-col">
                <label class="label-time d-block mb-2">Subáreas disponibles</label>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Subárea</th>
                                <th class="text-right" style="width:160px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaSubareas">
                            {{-- Se llena dinámicamente desde el JS (openReturnModal) --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</x-template-modal.modal-template>
