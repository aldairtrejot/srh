<?php

namespace App\Http\Controllers\Letter\Other;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConsecutivoC extends Controller
{
    //La función obtien solo los numeros que se ingresan de una concatenacion eje. DSIP/0008/2025 => 8
    function getOnlyNo($documento)
    {
        // Usamos una expresión regular para extraer el número antes de la barra "/"
        if (preg_match('/\D*(\d+)\//', $documento, $matches)) {
            // Si encontramos un número, lo devolvemos
            return (int) $matches[1];
        }

        // Si no se encuentra un número, retornamos null o un valor por defecto
        return null;
    }

    // LA función espera dos parametros para concatenarlos ejemplo param 1 = NNN/009/2025 y p2 = NN/10/2025
    // el resultado seria NN/10/2025, del p2 obtiene solo el consecutivo
    function setNoConsecutivo($param1, $param2)
    {
        // Expresión regular para extraer las letras y los "/"
        preg_match('/^[A-Za-z\/-]+/', $param1, $partes1);
        preg_match('/^[A-Za-z\/-]+/', $param2, $partes2);

        // Expresión regular para obtener el número entre los "/" y antes del año (o guion si lo tiene)
        preg_match('/\/?(\d{5,})\/?/', $param1, $numero1);
        preg_match('/\/?(\d{5,})\/?/', $param2, $numero2);

        // Expresión regular para obtener el año (último número)
        preg_match('/(\d{4})$/', $param1, $anio1);
        preg_match('/(\d{4})$/', $param2, $anio2);

        // Verificamos si el primer parámetro tiene un guion en el número
        if (strpos($param1, '-') !== false) {
            // Si hay guion, concatenar usando el guion
            $resultado = $partes1[0] . '-' . $numero2[1] . '/' . $anio2[1];
        } else {
            // Si no hay guion, concatenar usando la barra
            $resultado = $partes1[0] . '/' . $numero2[1] . '/' . $anio2[1];
        }

        // Reemplazar dobles barras "//" o guiones "--" por uno solo
        $resultado = str_replace('//', '/', $resultado);
        $resultado = str_replace('--', '-', $resultado);

        return $resultado;
    }
}
