<!-- Modal -->
<div class="modal fade" id="modalReturnado" tabindex="-1" aria-labelledby="modalReturnadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-info text-white">
                <h5 class="modal-title" id="modalReturnadoLabel">Asignar Subárea</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Tabs -->
                <ul class="nav nav-tabs" id="tabsSubarea" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="area-tab" data-toggle="tab" href="#areaContent" role="tab">Área asignada</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="subarea-tab" data-toggle="tab" href="#subareaContent" role="tab">Subáreas disponibles</a>
                    </li>
                </ul>

                <!-- Content -->
                <div class="tab-content mt-3">
                    <div class="tab-pane fade show active" id="areaContent" role="tabpanel">
                        <p><strong>Área actual:</strong> <span id="areaActual"></span></p>
                    </div>
                    <div class="tab-pane fade" id="subareaContent" role="tabpanel">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Subárea</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaSubareas">
                                <!-- Aquí se insertan dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
