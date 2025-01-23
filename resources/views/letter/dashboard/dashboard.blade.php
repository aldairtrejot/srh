<!-- TEMPLATE APP-->
<?php include(resource_path('views/config.php')); ?>
<x-template-app.app-layout>

    <div class="main-panel">
        <div class="content-wrapper">


            <style>
                /* Aseguramos que el botón sea completamente transparente y sin borde */
                #reporteBtn {
                    background: transparent;
                    border: none;
                    padding: 0;
                    text-decoration: none;
                    /* Elimina el subrayado al pasar el cursor */
                    cursor: pointer;
                    /* Cambia el cursor a puntero */
                }

                /* Aseguramos que el texto y el ícono estén alineados correctamente */
                #reporteBtn .ti-layout {
                    margin-left: 5px;
                    /* Ajuste para separar el ícono del texto */
                }

                /* Efecto de hover: agrandar texto y ícono */
                #reporteBtn:hover {
                    transform: scale(1.1);
                    /* Hace que todo el botón (texto + ícono) crezca un poco */
                    transition: transform 0.3s ease;
                    /* Animación suave */
                }

                /* Asegura que no haya subrayado */
                #reporteBtn:hover {
                    text-decoration: none;
                }
            </style>

            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row align-items-center">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Gestión de control</h3>
                            <h5 class="font-weight-normal mb-0">Dashboard</h5>
                        </div>
                        <div class="col-12 col-xl-4 text-xl-right">
                            <button type="button" class="btn btn-link" id="reporteBtn">
                                <span class="font-weight-bold" style="color: #10312b;">Reporte</span>
                                <i class="ti-layout" style="color: #10312b;"></i>
                            </button>
                        </div>
                    </div>
                </div> 
            </div>

            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card position-relative">
                        <div class="card-body">
                            <div id="detailedReports"
                                class="carousel slide detailed-report-carousel position-static pt-2"
                                data-ride="carousel">
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <div class="row">
                                            <div class="col-md-12 col-xl-3 d-flex flex-column justify-content-start">
                                                <div class="ml-xl-4 mt-3">
                                                    <p class="card-title">Correspondencia</p>
                                                    <h1 class="text-primary">12</h1>
                                                    <h3 class="font-weight-500 mb-xl-4 text-primary">Total</h3>
                                                    <p class="mb-2 mb-xl-0">The total number of sessions
                                                        within the date range. It is the period time a user
                                                        is actively engaged with your website, page or app,
                                                        etc</p>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xl-9">
                                                <div class="row">
                                                    <div class="col-md-6 border-right">
                                                        <div class="table-responsive mb-3 mb-md-0 mt-3">
                                                            <table class="table table-borderless report-table">
                                                                <tr>
                                                                    <td class="text-muted">Illinois</td>
                                                                    <td class="w-100 px-0">
                                                                        <div class="progress progress-md mx-4">
                                                                            <div class="progress-bar bg-primary"
                                                                                role="progressbar" style="width: 100%"
                                                                                aria-valuenow="70" aria-valuemin="0"
                                                                                aria-valuemax="100"></div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <h5 class="font-weight-bold mb-0">
                                                                            713</h5>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-muted">Washington</td>
                                                                    <td class="w-100 px-0">
                                                                        <div class="progress progress-md mx-4">
                                                                            <div class="progress-bar bg-warning"
                                                                                role="progressbar" style="width: 30%"
                                                                                aria-valuenow="30" aria-valuemin="0"
                                                                                aria-valuemax="100"></div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <h5 class="font-weight-bold mb-0">
                                                                            583</h5>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-muted">Mississippi</td>
                                                                    <td class="w-100 px-0">
                                                                        <div class="progress progress-md mx-4">
                                                                            <div class="progress-bar bg-danger"
                                                                                role="progressbar" style="width: 95%"
                                                                                aria-valuenow="95" aria-valuemin="0"
                                                                                aria-valuemax="100"></div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <h5 class="font-weight-bold mb-0">
                                                                            924</h5>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-muted">California</td>
                                                                    <td class="w-100 px-0">
                                                                        <div class="progress progress-md mx-4">
                                                                            <div class="progress-bar bg-info"
                                                                                role="progressbar" style="width: 60%"
                                                                                aria-valuenow="60" aria-valuemin="0"
                                                                                aria-valuemax="100"></div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <h5 class="font-weight-bold mb-0">
                                                                            664</h5>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-muted">Maryland</td>
                                                                    <td class="w-100 px-0">
                                                                        <div class="progress progress-md mx-4">
                                                                            <div class="progress-bar bg-primary"
                                                                                role="progressbar" style="width: 40%"
                                                                                aria-valuenow="40" aria-valuemin="0"
                                                                                aria-valuemax="100"></div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <h5 class="font-weight-bold mb-0">
                                                                            560</h5>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-muted">Alaska</td>
                                                                    <td class="w-100 px-0">
                                                                        <div class="progress progress-md mx-4">
                                                                            <div class="progress-bar bg-danger"
                                                                                role="progressbar" style="width: 75%"
                                                                                aria-valuenow="75" aria-valuemin="0"
                                                                                aria-valuemax="100"></div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <h5 class="font-weight-bold mb-0">
                                                                            793</h5>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mt-3">
                                                        <canvas id="north-america-chart"></canvas>
                                                        <div id="north-america-legend"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="carousel-item">
                                        <div class="row">
                                            <div class="col-md-12 col-xl-3 d-flex flex-column justify-content-start">
                                                <div class="ml-xl-4 mt-3">
                                                    <p class="card-title">Detailed Reports</p>
                                                    <h1 class="text-primary">$34040</h1>
                                                    <h3 class="font-weight-500 mb-xl-4 text-primary">North
                                                        America</h3>
                                                    <p class="mb-2 mb-xl-0">The total number of sessions
                                                        within the date range. It is the period time a user
                                                        is actively engaged with your website, page or app,
                                                        etc</p>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xl-9">
                                                <div class="row">
                                                    <div class="col-md-6 border-right">
                                                        <div class="table-responsive mb-3 mb-md-0 mt-3">
                                                            <table class="table table-borderless report-table">
                                                                <tr>
                                                                    <td class="text-muted">Illinois</td>
                                                                    <td class="w-100 px-0">
                                                                        <div class="progress progress-md mx-4">
                                                                            <div class="progress-bar bg-primary"
                                                                                role="progressbar" style="width: 70%"
                                                                                aria-valuenow="70" aria-valuemin="0"
                                                                                aria-valuemax="100"></div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <h5 class="font-weight-bold mb-0">
                                                                            713</h5>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-muted">Washington</td>
                                                                    <td class="w-100 px-0">
                                                                        <div class="progress progress-md mx-4">
                                                                            <div class="progress-bar bg-warning"
                                                                                role="progressbar" style="width: 30%"
                                                                                aria-valuenow="30" aria-valuemin="0"
                                                                                aria-valuemax="100"></div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <h5 class="font-weight-bold mb-0">
                                                                            583</h5>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-muted">Mississippi</td>
                                                                    <td class="w-100 px-0">
                                                                        <div class="progress progress-md mx-4">
                                                                            <div class="progress-bar bg-danger"
                                                                                role="progressbar" style="width: 95%"
                                                                                aria-valuenow="95" aria-valuemin="0"
                                                                                aria-valuemax="100"></div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <h5 class="font-weight-bold mb-0">
                                                                            924</h5>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-muted">California</td>
                                                                    <td class="w-100 px-0">
                                                                        <div class="progress progress-md mx-4">
                                                                            <div class="progress-bar bg-info"
                                                                                role="progressbar" style="width: 60%"
                                                                                aria-valuenow="60" aria-valuemin="0"
                                                                                aria-valuemax="100"></div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <h5 class="font-weight-bold mb-0">
                                                                            664</h5>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-muted">Maryland</td>
                                                                    <td class="w-100 px-0">
                                                                        <div class="progress progress-md mx-4">
                                                                            <div class="progress-bar bg-primary"
                                                                                role="progressbar" style="width: 40%"
                                                                                aria-valuenow="40" aria-valuemin="0"
                                                                                aria-valuemax="100"></div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <h5 class="font-weight-bold mb-0">
                                                                            560</h5>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-muted">Alaska</td>
                                                                    <td class="w-100 px-0">
                                                                        <div class="progress progress-md mx-4">
                                                                            <div class="progress-bar bg-danger"
                                                                                role="progressbar" style="width: 75%"
                                                                                aria-valuenow="75" aria-valuemin="0"
                                                                                aria-valuemax="100"></div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <h5 class="font-weight-bold mb-0">
                                                                            793</h5>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mt-3">
                                                        <canvas id="south-america-chart"></canvas>
                                                        <div id="south-america-legend"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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




        </div>
    </div>
</x-template-app.app-layout>