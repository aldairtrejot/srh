<!-- resources/views/components/error-message.blade.php -->
<style>
    @keyframes shake {
        0% {
            transform: translateX(0);
        }

        20% {
            transform: translateX(-3px);
        }

        40% {
            transform: translateX(3px);
        }

        60% {
            transform: translateX(-3px);
        }

        80% {
            transform: translateX(3px);
        }

        100% {
            transform: translateX(0);
        }
    }

    .shake {
        display: inline-block;
        animation: shake 0.5s ease-in-out 1;
    }
</style>

<small class="shake" style="color:red; font-family: Arial, sans-serif;">
    <i class="fas fa-exclamation-circle" style="color:red;"></i> {{ $slot }}
</small>