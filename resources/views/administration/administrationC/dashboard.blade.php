<x-template-app.app-layout>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Control de gestión</h3>
                            <h6 class="font-weight-normal mb-0">Catálogos</h6>
                            <br>
                            <br>
                            <h6 class="font-weight-bold mb-0">Circulares Externas</h6>
                            <div class="row">
                                <div class="col-lg-4 col-sm-4 col-md-4 col-xl-4 col-xs-12">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h4 class="card-title">Dependencia General</h4>
                                            <a href="{{ route('dependencia.list') }}" class="btn-icon" title="Ir a Catálogo Dependencia">
                                                <i class="fa fa-connectdevelop fa-3x"></i>
                                            </a>
                                            <p class="card-text mt-2">Correspondencia</p>
                                        </div>
                                    </div>
                                </div>
                            <div class="col-lg-4 col-sm-4 col-md-4 col-xl-4 col-xs-12">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h4 class="card-title">Dependencia Específica</h4>
                                        <a href="{{ route('dependenciarea.list') }}" class="btn-icon" title="Ir a Catálogo Dependencia Área">
                                            <i class="fa fa-braille fa-3x"></i>
                                        </a>
                                        <p class="card-text mt-2">Correspondencia</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-4 col-md-4 col-xl-4 col-xs-12">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h4 class="card-title">Dependencias por Área</h4>
                                        <a href="{{ route('reldependenciarea.list') }}" class="btn-icon" title="Ir a Catálogo Dependencia Área">
                                            <i class="fa fa-area-chart fa-3x"></i>
                                        </a>
                                        <p class="card-text mt-2">Correspondencia</p>
                                    </div>
                                </div>
                            </div>
                            <!--<div class="col-lg-4 col-sm-4 col-md-4 col-xl-4 col-xs-12">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h4 class="card-title">Catálogo Área</h4>
                                        <a href="{{ route('administration.list') }}" class="btn-icon" title="Ir a Catálogo Área">
                                            <i class="fa fa-area-chart fa-3x"></i>
                                        </a>
                                        <p class="card-text mt-2">Correspondencia</p>
                                    </div>
                                </div>
                            </div>-->
                        </div>
                            
                            
                                
                                <style>
                                    .btn-icon {
                                        display: inline-block;
                                        padding: 20px;
                                        border-radius: 50%;
                                        background-color: #f4f4f4;
                                        color: #1D5B3B;
                                        box-shadow: 0 0 10px rgba(0,0,0,0.1);
                                        text-decoration: none;
                                        transition: all 0.3s ease-in-out;
                                        transform: scale(1);
                                    }
                                
                                    .btn-icon:hover {
                                        background-color: #e1f3ea;
                                        color: #0f3e2a;
                                        box-shadow: 0 0 15px rgba(29, 91, 59, 0.5);
                                        transform: scale(1.2);
                                    }
                                
                                    .btn-icon:active {
                                        transform: scale(0.95);
                                    }
                                
                                    .btn-icon i {
                                        transition: transform 0.3s ease-in-out;
                                    }
                                
                                    .btn-icon:hover i {
                                        transform: rotate(5deg);
                                    }
                                </style>
                                
                            </div>       
                        </div>
</x-template-app.app-layout>