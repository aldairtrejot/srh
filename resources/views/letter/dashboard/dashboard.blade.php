<!-- TEMPLATE APP-->
<?php include(resource_path('views/config.php')); ?>
<x-template-app.app-layout>

    <!-- View->modal -->
    @include('letter.dashboard.modal')

    <div class="main-panel">
        <div class="content-wrapper">
            <!-- TITTLE -->
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row align-items-center">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Gestión de control</h3>
                            <h5 class="font-weight-normal mb-0">Dashboard</h5>
                        </div>
                        <div class="col-12 col-xl-4 text-xl-right">
                            <button onclick="openModal();" type="button" class="btn btn-link" id="reporteBtn">
                                <span class="font-weight-bold" style="color: #10312b;">Informe</span>
                                <i class="ti-layout" style="color: #10312b;"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>



            <script src="{{ asset('assets/js/app/letter/dashboard/report.js') }}"></script>
            <script src="{{ asset('assets/js/app/letter/dashboard/validate.js') }}"></script>
            <!--
            <script src="{{ asset('assets/js/app/letter/dashboard/dashboard.js') }}"></script>
-->
        </div>
    </div>
</x-template-app.app-layout>