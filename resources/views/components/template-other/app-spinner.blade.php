<style>
    /* Estilo para el spinner */
    .spinner {
        display: none;
        /* Inicialmente oculto */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        /* Fondo mucho más oscuro */
        text-align: center;
        z-index: 1000;
        /* Esto puede ser ajustado si tu menú tiene un z-index mayor */
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        opacity: 0;
        transform: scale(0.5);
        /* Inicialmente en tamaño pequeño */
        transition: opacity 0.3s ease, transform 0.3s ease;
        pointer-events: none;
        /* No interactúa con el contenido cuando está oculto */
    }

    .spinner.show {
        opacity: 1;
        transform: scale(1);
        /* Tamaño normal */
        pointer-events: all;
        /* Permitir interacción cuando está visible */
    }

    .spinner .circle {
        border: 5px solid rgba(255, 255, 255, 0.3);
        border-top: 5px solid #ffffff;
        /* Cambiado a blanco */
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
    }

    .spinner p {
        color: #ffffff;
        /* Cambiado a blanco */
        font-size: 18px;
        margin-top: 15px;
    }

    /* Animación para el giro */
    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

<div id="spinner" class="spinner">
    <div class="circle"></div>
    <p>Cargando...</p>
</div>