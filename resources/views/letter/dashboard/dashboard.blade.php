<!-- TEMPLATE APP-->
<?php include(resource_path('views/config.php')); ?>
<x-template-app.app-layout>

    <!-- View->modal -->
    @include('letter.dashboard.modal')

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


    <div class="main-panel">
        <div class="content-wrapper">
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
                                        <option value="opcion1">Opción 1</option>
                                        <option value="opcion2">Opción 2</option>
                                        <option value="opcion3">Opción 3</option>
                                    </select>
                                </div>
                                <!-- Select Mes -->
                                <div class="select-container-custom mb-2">
                                    <label for="selectMes" class="select-label-custom">Mes</label>
                                    <select id="selectMes" class="select-dashboard-custom">
                                        <option value="opcion1">Opción 1</option>
                                        <option value="opcion2">Opción 2</option>
                                        <option value="opcion3">Opción 3</option>
                                    </select>
                                </div>
                                <!-- Select Área -->
                                <div class="select-container-custom mb-2">
                                    <label for="selectArea" class="select-label-custom">Área</label>
                                    <select id="selectArea" class="select-dashboard-custom">
                                        <option value="opcion1">Opción 1</option>
                                        <option value="opcion2">Opción 2</option>
                                        <option value="opcion3">Opción 3</option>
                                    </select>
                                </div>
                                <!-- Botón Informe -->
                                <div class="select-container-custom mb-2">
                                    <label class="select-label-custom">Reporte</label>
                                    <button onclick="openModal();" type="button"
                                        class="btn btn-link btn-dashboard-custom" id="reporteBtn">
                                        <span class="font-weight-bold">Informe</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CAROUSEL -->
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card position-relative">
                        <div class="card-body">
                            <div id="detailedReports"
                                class="carousel slide detailed-report-carousel position-static pt-2"
                                data-ride="carousel">
                                <div class="carousel-inner">

                                    <x-template-charts.charts-carousel-panel isClass="carousel-item active"
                                        tittle="Correspondencia" value="106"
                                        text="Los estatus de correspondencia se detallan a través de las barras de progreso, que permiten visualizar el avance de cada uno. Además, la gráfica de dona ofrece una representación visual de los registros que has capturado."
                                        idCanvas="north-america-chart" idLength="north-america-legend">

                                        <x-template-charts.charts-carousel-item tittle="Concluido" size="50%"
                                            color="#BC955C" value="10" />

                                        <x-template-charts.charts-carousel-item tittle="Turnado" size="50%"
                                            color="#BC955C" value="10" />

                                        <x-template-charts.charts-carousel-item tittle="En proceso" size="50%"
                                            color="#BC955C" value="10" />

                                        <x-template-charts.charts-carousel-item tittle="Rechazado" size="50%"
                                            color="#BC955C" value="10" />

                                        <x-template-charts.charts-carousel-item tittle="Vencido" size="50%"
                                            color="#BC955C" value="50" />

                                        <x-template-charts.charts-carousel-item tittle="Cancelado" size="50%"
                                            color="#BC955C" value="10" />

                                    </x-template-charts.charts-carousel-panel>

                                </div>
                                <a class="carousel-control-prev" href="#detailedReports" role="button"
                                    data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#detailedReports" role="button"
                                    data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>









            <div class="row">
                <div class="col-md-8 stretch-card grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title mb-0">Últimos remitentes</p>
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th class="pl-0  pb-2 border-bottom">Places</th>
                                            <th class="border-bottom pb-2">Orders</th>
                                            <th class="border-bottom pb-2">Users</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="pl-0">Kentucky</td>
                                            <td>
                                                <p class="mb-0"><span class="font-weight-bold mr-2">65</span>(2.15%)</p>
                                            </td>
                                            <td class="text-muted">65</td>
                                        </tr>
                                        <tr>
                                            <td class="pl-0">Ohio</td>
                                            <td>
                                                <p class="mb-0"><span class="font-weight-bold mr-2">54</span>(3.25%)</p>
                                            </td>
                                            <td class="text-muted">51</td>
                                        </tr>
                                        <tr>
                                            <td class="pl-0">Nevada</td>
                                            <td>
                                                <p class="mb-0"><span class="font-weight-bold mr-2">22</span>(2.22%)</p>
                                            </td>
                                            <td class="text-muted">32</td>
                                        </tr>
                                        <tr>
                                            <td class="pl-0">North Carolina</td>
                                            <td>
                                                <p class="mb-0"><span class="font-weight-bold mr-2">46</span>(3.27%)</p>
                                            </td>
                                            <td class="text-muted">15</td>
                                        </tr>
                                        <tr>
                                            <td class="pl-0">Montana</td>
                                            <td>
                                                <p class="mb-0"><span class="font-weight-bold mr-2">17</span>(1.25%)</p>
                                            </td>
                                            <td class="text-muted">25</td>
                                        </tr>
                                        <tr>
                                            <td class="pl-0">Nevada</td>
                                            <td>
                                                <p class="mb-0"><span class="font-weight-bold mr-2">52</span>(3.11%)</p>
                                            </td>
                                            <td class="text-muted">71</td>
                                        </tr>
                                        <tr>
                                            <td class="pl-0 pb-0">Louisiana</td>
                                            <td class="pb-0">
                                                <p class="mb-0"><span class="font-weight-bold mr-2">25</span>(1.32%)</p>
                                            </td>
                                            <td class="pb-0">14</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 stretch-card grid-margin">
                    <div class="row">
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <p class="card-title">Charts</p>
                                    <div class="charts-data">
                                        <div class="mt-3">
                                            <p class="mb-0">Data 1</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="progress progress-md flex-grow-1 mr-4">
                                                    <div class="progress-bar bg-inf0" role="progressbar"
                                                        style="width: 95%" aria-valuenow="95" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                                <p class="mb-0">5k</p>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <p class="mb-0">Data 2</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="progress progress-md flex-grow-1 mr-4">
                                                    <div class="progress-bar bg-info" role="progressbar"
                                                        style="width: 35%" aria-valuenow="35" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                                <p class="mb-0">1k</p>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <p class="mb-0">Data 3</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="progress progress-md flex-grow-1 mr-4">
                                                    <div class="progress-bar bg-info" role="progressbar"
                                                        style="width: 48%" aria-valuenow="48" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                                <p class="mb-0">992</p>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <p class="mb-0">Data 4</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="progress progress-md flex-grow-1 mr-4">
                                                    <div class="progress-bar bg-info" role="progressbar"
                                                        style="width: 25%" aria-valuenow="25" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                                <p class="mb-0">687</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 stretch-card grid-margin grid-margin-md-0">
                            <div class="card data-icon-card-primary">
                                <div class="card-body">
                                    <p class="card-title text-white">Number of Meetings</p>
                                    <div class="row">
                                        <div class="col-8 text-white">
                                            <h3>34040</h3>
                                            <p class="text-white font-weight-500 mb-0">The total number of sessions
                                                within the date range.It is calculated as the sum . </p>
                                        </div>
                                        <div class="col-4 background-icon">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script src="{{ asset('assets/js/app/letter/dashboard/report.js') }}"></script>
            <script src="{{ asset('assets/js/app/letter/dashboard/validate.js') }}"></script>
            <script src="{{ asset('assets/js/app/letter/dashboard/dashboard.js') }}"></script>

        </div>
    </div>
</x-template-app.app-layout>