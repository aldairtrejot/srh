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
                <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
                    <i class="fa fa-cog menu-icon"></i>
                    <span class="menu-title">Administraci&oacuten</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-basic">
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
                        <li class="nav-item"><a class="nav-link" href="#">Administración</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('letter.list') }}">Correspondencia</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Expedientes</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('round.list') }}">Ciculares</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('inside.list') }}">Interno</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('office.list') }}">Oficios</a></li>
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