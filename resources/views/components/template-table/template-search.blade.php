<!-- CODIGO DE INPUT BUSQUEDA-->
{{-- 
<div class="input-group-prepend">
    <span style="background:#10312B" class="input-group-text" id="basic-addon1">
        <i class="fas fa-search"></i>
    </span>
</div>
 --}}

<style>
    /* Clases específicas para el input de búsqueda */
    .search-input-custom {
        border-radius: 15px !important;
        border: 2px solid #d1d5db !important;
        transition: all 0.3s ease !important;
        padding: 10px 20px !important;
    }

    /* Efecto cuando el input está en foco (click) */
    .search-input-custom:focus {
        border-color: #6b7280 !important;
        outline: none !important;
    }

    /* Efecto cuando se está escribiendo */
    .search-input-custom:not(:placeholder-shown) {
        border-color: #4b5563 !important;
    }

    /* Efecto hover */
    .search-input-custom:hover {
        border-color: #9ca3af !important;
    }
</style>

<input onkeyup="searchValue();" type="text" class="form-control search-input-custom" placeholder="Buscar ..."
    id="searchValue">
