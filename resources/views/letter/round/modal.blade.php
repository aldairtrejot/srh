<x-template-modal.modal-template 
    tittle="Generar informe"
    idModal="modalReport"
    idCancel="cancel_copy"
    idConfirm="confir_copy"
    functionConfirm="validateDate();"
    width="1200px"
    height="300px">

    <div class="row">
        <!-- Área -->
        <div class="col-12 col-md-6">
            <div class="custom-col">
                <label for="id_cat_area_informe" class="label-time">Área</label>
                <select 
                    class="form-control selectpicker" 
                    id="id_cat_area_informe" 
                    data-live-search="true" 
                    data-dropup-auto="false">
                    <option value="">-- Todas las áreas --</option>
                </select>
            </div>
        </div>

        <!-- Estatus -->
        <div class="col-12 col-md-3">
            <div class="custom-col">
                <label for="id_cat_status_informe" class="label-time">Estatus</label>
                <select 
                    class="form-control selectpicker" 
                    id="id_cat_status_informe" 
                    data-live-search="true" 
                    data-dropup-auto="false">
                    <option value="">-- Todos los estatus --</option>
                </select>
            </div>
        </div>

        <!-- Año -->
        <div class="col-12 col-md-3">
            <div class="custom-col">
                <label for="id_cat_date_informe" class="label-time">Año</label>
                <select 
                    class="form-control selectpicker" 
                    id="id_cat_date_informe" 
                    data-live-search="true" 
                    data-dropup-auto="false">
                    <option value="">-- Todos los años --</option>
                </select>
            </div>
        </div>
    </div>
</x-template-modal.modal-template>

<style>
.label-time {
    display: block;
    text-align: left;
    font-size: 14px;
    color: #333;
    margin-bottom: 5px;
}
</style>
