<x-template-app.app-layout>
    <?php include(resource_path('views/config.php')); ?>
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
                        @if($adminMatch)
                            <x-template-button-dash title="Usuarios del sistema" field="ADMINISTRACIÓN"
                                href="{{ route('user.list') }}" icon="fas fa-cogs" description="Administración" />
                        @endif
                        <!-- item menu users-->
                        @if($letterMatch)
                            <x-template-button-dash title="Gestión de control" field="GESTIÓN DE CONTROL"
                                href="{{ route('letter.list') }}" icon="fa fa-archive" description="Correspondencia" />
                        @endif
                    </div>

                    <!-- CORRESPONDENCIA-->
                    <div class="row">
                        <!-- item menu administracion-->

                        @if($letterMatch)
                            <x-template-button-dash class="card card-correspondencia" title="Correspondencia"
                                field="GESTIÓN DE CONTROL" href="{{ route('letter.list') }}" value=""
                                description="Correspondencia" />
                        @endif

                        <!-- item menu administracion-->
                        @if($letterMatch)
                            <x-template-button-dash class="card card-correspondencia" title="Expedientes"
                                field="GESTIÓN DE CONTROL" href="{{ route(name: 'file.list') }}" value=""
                                description="Expedientes" />

                        @endif

                        <!-- item menu administracion-->
                        @if($letterMatch)
                            <x-template-button-dash class="card card-correspondencia" title="Circulares"
                                field="GESTIÓN DE CONTROL" href="{{ route('round.list') }}" value=""
                                description="Circulares" />
                        @endif

                    </div>

                    <!-- CORRESPONDENCIA-->
                    <div class="row">
                        <!-- item menu Interno-->
                        @if($letterMatch)
                            <x-template-button-dash class="card card-correspondencia" title="Interno"
                                field="GESTIÓN DE CONTROL" href="{{ route('inside.list') }}" value=""
                                description="Interno" />
                        @endif

                        <!-- item menu oficios-->
                        @if($letterMatch)
                            <x-template-button-dash class="card card-correspondencia" title="Oficios"
                                field="GESTIÓN DE CONTROL" href="{{ route('office.list') }}" value=""
                                description="Oficios" />
                        @endif
                    </div>
                       <!-- CURSOS-->
                       <div class="row">
                        <!-- item menu administracion-->

                        @if($coursesMatch)
                            <x-template-button-dash class="card card-cursos" title="cursos"
                                field="CURSOS" href="{{ route('courses.list') }}" value="0"
                                description="Cursos" />
                        @endif


                </div>
            </div>
        </div>
</x-template-app.app-layout>