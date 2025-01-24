<!-- Panel de carousel -->
<div class="{{ $isClass }}"> <!-- Aquí se usa isClass -->
    <div class="row">
        <div class="col-md-12 col-xl-3 d-flex flex-column justify-content-start">
            <div class="ml-xl-4 mt-3">
                <p class="card-title">{{ $tittle }}</p>
                <h1 class="text-primary">{{ $value }}</h1>
                <h3 class="font-weight-500 mb-xl-4 text-primary">Total</h3>
                <p class="mb-2 mb-xl-0">{{ $text }}</p>
            </div>
        </div>
        <div class="col-md-12 col-xl-9">
            <div class="row">
                <div class="col-md-6 border-right">
                    <div class="table-responsive mb-3 mb-md-0 mt-3">
                        <table class="table table-borderless report-table">
                            <!-- AQUI VA CONTENIDO HTML DINAMICO -->
                            {{ $slot }}
                            <!-- AQUI VA CONTENIDO HTML DINAMICO -->
                        </table>
                    </div>
                </div>
                <div class="col-md-6 mt-3">
                    <canvas id="{{ $idCanvas }}"></canvas>
                    <div id="{{ $idLength }}"></div>
                </div>
            </div>
        </div>
    </div>
</div>