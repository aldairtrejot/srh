<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row" style="background:red">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="{{ route('dashboard') }}">
            <img src="{{ asset('assets/images/imss/logo_imss_blanco.png') }}" class="mr-2" alt="logo"
                style="width: 200px; height: auto;" />
        </a>
        <a class="navbar-brand brand-logo-mini" href="{{ route('dashboard') }}"><img
                src="{{ asset('assets/images/imss/logo_imss_mini_white.png') }}" alt="logo" /></a>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span style="color:white" class="icon-menu"></span>
        </button>
        <ul class="navbar-nav mr-lg-2">
            <li class="nav-item nav-search d-none d-lg-block">
                <H6>
                    </H1>
            </li>
        </ul>
        <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item dropdown">
                <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
                    data-toggle="dropdown">
                    <i style="color:#fff" class="fa fa-bell mx-0"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                    aria-labelledby="notificationDropdown">
                    <p class="mb-0 font-weight-normal float-left dropdown-header">Notificaciones</p>
                    <a class="dropdown-item preview-item">
                        <div class="preview-thumbnail">
                            <div class="preview-icon bg-success">
                                <i class="ti-info-alt mx-0"></i>
                            </div>
                        </div>
                        <div class="preview-item-content">
                            <h6 class="preview-subject font-weight-normal">Estatus</h6>
                            <p class="font-weight-light small-text mb-0 text-muted">
                                @if(session('SESSION_ROLE_USER'))
                                    Activo
                                @else
                                    Sin roles
                                @endif
                            </p>
                        </div>
                    </a>
                </div>
            </li>
            <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                    <i style="color:#fff; font-size: 22px;" class="fa fa-cog mx-0"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                    <a class="dropdown-item" id="changePassword">
                        <i class="ti-lock text-primary"></i>
                        Contraseña
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <a class="dropdown-item" onclick="this.closest('form').submit();">
                            <i class="ti-power-off text-primary"></i>
                            Salir
                        </a>
                    </form>
                </div>
            </li>
        </ul>

    </div>
</nav>