<!-- DESIGN -->
<style>
    #reporteBtn {
        margin-left: 10px;
        border: 2px solid gray;
        border-radius: 50px;
        padding: 5px 15px;
        font-size: 14px;
        color: gray;
        background-color: white;
        transition: transform 0.2s ease;
    }
</style>

<!-- TITTLE -->
<div class="row">
    <div class="col-md-12 grid-margin">
        <div class="row align-items-center">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <h3 class="font-weight-bold">Control de gestión</h3>
                <h5 class="font-weight-normal mb-0">Dashboard</h5>
            </div>
            <!-- Contenedor para los selects y el botón Informe -->
            <div class="col-12 col-xl-4 text-xl-right">
                <div class="d-flex-custom align-items-center justify-content-end">
                    <!-- Select Año -->
                    <div class="select-container-custom mb-2">
                        <label for="selectAño" class="select-label-custom">Año</label>
                        <select id="selectAño" class="select-dashboard-custom">
                        </select>
                    </div>
                    <!-- Select Mes -->
                    <div class="select-container-custom mb-2">
                        <label for="selectMes" class="select-label-custom">Mes</label>
                        <select id="selectMes" class="select-dashboard-custom">
                        </select>
                    </div>
                    <!-- Select Área -->
                    <div class="select-container-custom mb-2">
                        <label for="selectArea" class="select-label-custom">Área</label>
                        <select id="selectArea" class="select-dashboard-custom">
                        </select>
                    </div>
                    <!-- Botón Informe -->
                    <div class="select-container-custom mb-2">
                        <label class="select-label-custom">Reporte</label>
                        <button onclick="openModal();" type="button" class="btn btn-link btn-dashboard-custom"
                            id="reporteBtn">
                            <span class="font-weight-bold">Informe</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>