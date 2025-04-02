<x-template-app.app-layout>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Sistema Integral prueba</h3>
                            <h6 class="font-weight-normal mb-0">VERSION</h6>

                            <div class="row">
                                <div class="col-lg-4 col-sm-4 col-md-4 col-xl-4 col-xs-12">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h4 class="card-title">Catálogo Área</h4>
                                            
                                            <div class="card-image">
                                                <a href="{{ route('administration.list') }}" class="text-decoration-none" title="Ir a Catálogo Área" style="color: #1D5B3B;">
                                                    <i class="fa fa-area-chart fa-5x"></i>
                                                </a>
                                            </div>
                                            
                                            <p class="card-text mt-2">Correspondencia</p>
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