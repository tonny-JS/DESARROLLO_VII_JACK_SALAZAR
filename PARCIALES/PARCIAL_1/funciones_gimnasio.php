<?php
// funciones_gimnasio.php

// 1. Calcular porcentaje de promoción
function calcular_promocion($antiguedad_meses) {
    if ($antiguedad_meses < 3) {
        return 0; // Sin promoción
    } elseif ($antiguedad_meses >= 3 && $antiguedad_meses <= 12) {
        return 8; // 8% descuento
    } elseif ($antiguedad_meses >= 13 && $antiguedad_meses <= 24) {
        return 12; // 12% descuento
    } else {
        return 20; // Más de 24 meses → 20% descuento
    }
}

// 2. Calcular seguro médico (5% de la cuota base)
function calcular_seguro_medico($cuota_base) {
    return $cuota_base * 0.05;
}

// 3. Calcular cuota final
function calcular_cuota_final($cuota_base, $descuento_porcentaje, $seguro_medico) {
    $descuento = $cuota_base * ($descuento_porcentaje / 100);
    $cuota_final = $cuota_base - $descuento + $seguro_medico;
    return $cuota_final;
}
?>
