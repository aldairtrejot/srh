<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'], // Permite todas las rutas API y Sanctum
    'allowed_methods' => ['*'], // Permite todos los métodos (GET, POST, etc.)
    'allowed_origins' => ['*'], // Permite cualquier origen (En producción, cambia '*' por tu dominio)
    'allowed_headers' => ['*'], // Permite todos los encabezados
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // Necesario si usas cookies de autenticación
];