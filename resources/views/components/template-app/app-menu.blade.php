<?php include(resource_path('views/config.php')); ?>
<nav class="sidebar sidebar-offcanvas" id="sidebar" style="background:#777777">
    <ul class="nav">
        <!-- Item de inicio -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="icon-grid menu-icon"></i>
                <span class="menu-title">Inicio</span>
            </a>
        </li>

        <!-- Item Administracion -->
        @if($adminMatch)
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#ui-basic-admin" aria-expanded="false" aria-controls="ui-basic-admin">
                    <i class="fa fa-cog menu-icon"></i>
                    <span class="menu-title">Administración</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-basic-admin">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link" href="{{ route('user.list') }}">Usuarios</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Roles</a></li>
                    </ul>
                </div>
            </li>
        @endif

        <!-- Item Correspondencia -->
        @if($letterMatch)
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="collapse" href="#ui-basic_corres" aria-expanded="false"
                            aria-controls="ui-basic">
                            <i class="fa fa-file-text menu-icon"></i>
                            <span class="menu-title">Correspondencia</span>
                            <i class="menu-arrow"></i>
                        </a>
                        <div class="collapse" id="ui-basic_corres">
                            <ul class="nav flex-column sub-menu">
                                <!--
                                    <li class="nav-item"><a class="nav-link" href="#">Administración</a></li>
            -->
                                <li class="nav-item"><a class="nav-link" href="{{ route('letter.list') }}">Correspondencia</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route( 'file.list') }}">Expedientes</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('round.list') }}">Circulares</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('inside.list') }}">Interno</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('office.list') }}">Oficios</a></li>
                            </ul>
                        </div>
                    </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#ui-basic-corres" aria-expanded="false" aria-controls="ui-basic-corres">
                    <i class="fa fa-file-text menu-icon"></i> 
                    <span class="menu-title">Correspondencia</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-basic-corres">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link" href="#">Administración</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('letter.list') }}">Correspondencia</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Expedientes</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Circulares</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Interno</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Oficios</a></li>
                    </ul>
                </div>
            </li>
        @endif

        <!-- Item Cursos -->
        @if($coursesMatch)
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#ui-basic-courses" aria-expanded="false" aria-controls="ui-basic-courses">
                <i class="fa fa-desktop menu-icon"></i><!-- Icono cambiado a computadora -->
                    <span class="menu-title">Cursos</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-basic-courses">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link" href="{{ route('coursesauditoria.list') }}">Auditoria</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('courses.list') }}">Beneficio</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('coursescategoria.list') }}">Categoría</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('coursescoordinacion.list') }}">Coordinación</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('coursesestatuto.list') }}">Estatuto Orgánico</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('coursesmodalidad.list') }}">Modalidad</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('coursesnombreacc.list') }}">Nombre Acción</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('coursesorganizacion.list') }}">Organización</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('coursesprograma.list') }}">P. Institucional</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('coursestipoac.list') }}">Tipo Acción</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('coursestipocur.list') }}">Tipo Cursos</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('tableinstructor.list') }}">Instructores</a></li>
                        
                    </ul>
                </div>
            </li>
        @endif

        <!-- Item Acerca de -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('about') }}">
                <i class="icon-paper menu-icon"></i>
                <span class="menu-title">Acerca de</span>
            </a>
        </li>
    </ul>
</nav>