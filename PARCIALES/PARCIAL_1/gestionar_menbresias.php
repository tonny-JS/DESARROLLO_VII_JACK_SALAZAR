<?php
// gestionar_menbresias.php
// Cuotas base según tipo de membresía
$cuotas_base = [
    'premium' => 120,
    'básica' => 80,
    'corporativo' => 300,
    'vip' => 180,
    'familiar' => 250
];
// Datos de miembros (nombre, tipo de membresía, antigüedad en meses)
$miembros = 
["juan Pérez" => ['tipo'=>'premium','antiguedad'=> 15] ]
+
["Ana Garcia" => ['tipo'=>'básica','antiguedad'=> 2] ]
+
["Luis Martínez" => ['tipo'=>'corporativo','antiguedad'=> 18] ]
+
["Carlos López" => ['tipo'=>'vip','antiguedad'=> 30] ]
+
["María Rodriguez" => ['tipo'=>'familiar','antiguedad'=> 8] ]
;
echo"cuota de cada miembro";
foreach ($cuotas_base as $tipo => $cuota) {
    echo "</br>Tipo: $tipo - Cuota Base: $$cuota</br>";
};

include 'funciones_gimnasio.php';
echo "<h1>Gestión de Membresías del Gimnasio</h1>";
foreach ($miembros as $nombre => $detalles) {
    $tipo = $detalles['tipo'];
    $antiguedad = $detalles['antiguedad'];
    $cuota_base = $cuotas_base[$tipo];

    // Calcular promoción, seguro médico y cuota final
    $descuento_porcentaje = calcular_promocion($antiguedad);
    $seguro_medico = calcular_seguro_medico($cuota_base);
    $cuota_final = calcular_cuota_final($cuota_base, $descuento_porcentaje, $seguro_medico);

    // Mostrar detalles
    echo "<h2>$nombre</h2>";
    echo "Tipo de Membresía: $tipo<br>";
    echo "Antigüedad: $antiguedad meses<br>";
    echo "Cuota Base: $" . number_format($cuota_base, 2) . "<br>";
    echo "Descuento por Promoción: $descuento_porcentaje%<br>";
    echo "Seguro Médico: $" . number_format($seguro_medico, 2) . "<br>";
    echo "<strong>Cuota Final a Pagar: $" . number_format($cuota_final, 2) . "</strong><br><br>";
}   
?>
