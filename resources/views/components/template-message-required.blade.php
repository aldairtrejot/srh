<style>
    .error-message {
        color: red;
        font-family: Arial, sans-serif;
        display: inline-block;
        animation: shake 0.4s ease-in-out;
        animation-iteration-count: 1;
    }

    .error-message i {
        color: red;
    }

    @keyframes shake {
        0% {
            transform: translateX(0);
        }

        20% {
            transform: translateX(-5px);
        }

        40% {
            transform: translateX(5px);
        }

        60% {
            transform: translateX(-5px);
        }

        80% {
            transform: translateX(5px);
        }

        100% {
            transform: translateX(0);
        }
    }
</style>



<small class="error-message" style="color:#7A1B32; font-family: Arial, sans-serif; font-weight: bold;">
    <i class="fas fa-exclamation-circle" style="color:#7A1B32;"></i> {{ $slot }}
</small>
