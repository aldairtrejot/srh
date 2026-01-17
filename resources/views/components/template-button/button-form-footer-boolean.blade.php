<!-- TEMPLATE BUTTON FOOTER -->
<div class="form-group mt-4">
    <div class="d-flex justify-content-end">
        <a class="btn btn-secondary" href=" {{ $routeBack }} "
            style="font-size: 1rem; padding: 10px 20px; margin-left: 10px; background-color: 	#6c757d; color: white; border: none;">
            <i class="fas fa-times"></i> Cancelar
        </a>
        @if ($status)
            <button type="submit" class="btn"
                style="font-size: 1rem; padding: 10px 20px; margin-left: 10px; background-color: #10312B; color: white; border: none;">
                <i class="fas fa-save"></i> Guardar
            </button>
        @endif
    </div>
</div>