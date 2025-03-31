<!-- TEMPLATE APP-->
<?php include(resource_path('views/config.php')); ?>
<x-template-app.app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- token html-->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Cursos del Instructor</h3>
                            <h5 class="font-weight-normal mb-0">Listado de cursos asignados</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card custom-card">
                    <div class="card-body">
                                  <!-- Contenedor de Resultados -->
                                  <div class="contenedor">
                                    <div class="item">
                                        <label class="etiqueta">Nombre:</label>
                                        <label class="valor">{{ $instructor->nombre ?? '' }}</label>
                                    </div>
                                    <div class="item">
                                        <label class="etiqueta">Primer Apellido:</label>
                                        <label class="valor">{{ $instructor->primer_apellido ?? '' }}</label>
                                    </div>
                                    <div class="item">
                                        <label class="etiqueta">Segundo Apellido:</label>
                                        <label class="valor">{{ $instructor->segundo_apellido ?? '' }}</label>
                                    </div>
                                    <div class="item">
                                        <label class="etiqueta">RFC:</label>
                                        <label class="valor">{{ $instructor->rfc ?? '' }}</label>
                                    </div>
                                    <div class="item">
                                        <label class="etiqueta">CURP:</label>
                                        <label class="valor">{{ $instructor->curp ?? '' }}</label>
                                    </div>
                                </div>
                                
                <br>
                        <h4 class="card-title">Cursos Asignados</h4>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nombre curso</th>
                                        <th>Beneficio</th>
                                        <th>Tipo curso</th>
                                        <th>Tipo acción</th>
                                        <th>Programa institucional</th>
                                        <th>Costo total</th>
                                        <th>Fecha inicio</th>
                                        <th>Fecha fin</th>
                                        <th>Horas</th>
                                        <th>Instructor</th>
                                        <th>Estatus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($cursos as $curso)
                                        <tr>
                                            <td>{{ $curso->nombre_curso }}</td>
                                            <td>{{ $curso->categoria_beneficio }}</td>
                                            <td>{{ $curso->categoria_tipo_curso }}</td>
                                            <td>{{ $curso->categoria_tipo_accion }}</td>
                                            <td>{{ $curso->categoria_programa_institucional }}</td>
                                            <td>$ {{ $curso->costo_total }}</td>
                                            <td>{{ $curso->fecha_inicio }}</td>
                                            <td>{{ $curso->fecha_fin }}</td>
                                            <td>{{ $curso->horas_curso }}</td>
                                            <td>{{ $curso->nombre_completo }}</td>
                                            <td>{{ $curso->estatus ? 'ACTIVO' : 'INACTIVO' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center">No hay cursos asignados a este instructor.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('tableinstructor.list') }}" class="btn" style="background-color: #1D5B3B; color: white;">
                                <i class="fa fa-arrow-left"></i> Volver al listado de instructores
                            </a>                                                      
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</x-template-app.app-layout>
