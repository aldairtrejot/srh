<x-app-layout>

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Sistema Integral para Recursos Humanos</h3>
                            <h6 class="font-weight-normal mb-0">¡HOLA {{ Auth::user()->name }}!</h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 grid-margin transparent">

                    <div class="row">

                        <!-- item menu users-->
                        <x-menu-button-dash class="card card-administration" title="Usuarios del sistema"
                            field="ADMINISTRACIÓN" href="{{ route('user') }}" value="4006"
                            description="Total de usuarios" />

                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>