<div class="col-sm-6 col-md-3 mb-4 stretch-card transparent" data-bs-toggle="tooltip" data-bs-placement="top"
    title="{{ $title }}">
    <a href="{{ $href }}" class="card"
        style="background:rgb(62, 71, 70); color: #ffffff; transition: background-color 0.3s ease;">
        <div class="card-body">
            <p style="color:white" class="mb-4"><strong>{{ $field }}</strong></p>
            <!-- Icono de Font Awesome en lugar del número -->
            <p style="color:white" class="fs-30 mb-3">
                <i class="{{ $icon }}"></i> <!-- Aquí puedes poner el icono de tu elección -->
            </p>
            <p style="color:white">{{ $description }}</p>
        </div>
    </a>
</div>


