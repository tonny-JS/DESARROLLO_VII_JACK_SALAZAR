<?php
// gestionar_membresias.php
include 'funciones_gimnasio.php';

// Array asociativo con 5 tipos de membresía y sus precios base
$membresias = [
    'basica' => 80,
    'premium' => 120,
    'vip' => 180,
    'familiar' => 250,
    'corporativa' => 300
];

// Array de miembros con tipo de membresía y antigüedad en años
$miembros = [
    'Juan Pérez' => ['tipo' => 'premium', 'antiguedad' => 1],
    'Ana García' => ['tipo' => 'basica', 'antiguedad' => 2],
    'Carlos López' => ['tipo' => 'vip', 'antiguedad' => 3],
    'María Rodríguez' => ['tipo' => 'familiar', 'antiguedad' => 5],
    'Luis Martínez' => ['tipo' => 'corporativa', 'antiguedad' => 1]
];

// Procesar cada miembro y calcular cuotas
$resultados = [];
foreach ($miembros as $nombre => $info) {
    $tipo = $info['tipo'];
    $antiguedad = $info['antiguedad'];
    $precioBase = $membresias[$tipo];
    $porcentajeDescuento = calcularDescuento($antiguedad); // ← Ya definida
    $montoDescuento = $precioBase * $porcentajeDescuento;
    $seguroMedico = calcular_seguro_medico($precioBase);
    $cuotaFinal = calcular_cuota_final($precioBase, $porcentajeDescuento * 100, $seguroMedico);

    $resultados[$nombre] = [
        'tipo' => $tipo,
        'antiguedad' => $antiguedad,
        'precio_base' => $precioBase,
        'descuento_porcentaje' => $porcentajeDescuento * 100,
        'descuento_monto' => $montoDescuento,
        'seguro_medico' => $seguroMedico,
        'cuota_final' => $cuotaFinal
    ];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Membresías - Gimnasio</title>
    <style>
        body { font-family: Arial; background-color: #f4f4f4; margin: 20px; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { text-align: center; color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .miembro { border: 1px solid #ddd; margin: 15px 0; padding: 15px; border-radius: 5px; background: #f9f9f9; }
        .info { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; }
        .info-item { background: white; padding: 8px; border-left: 3px solid #007bff; border-radius: 3px; }
        .cuota-final { background: #28a745; color: white; padding: 10px; text-align: center; font-weight: bold; border-radius: 5px; }
        .descuento { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Resumen de Membresías del Gimnasio</h1>
        <?php foreach ($resultados as $nombre => $datos): ?>
            <div class="miembro">
                <h3><?php echo htmlspecialchars($nombre); ?></h3>
                <div class="info">
                    <div class="info-item"><strong>Tipo:</strong> <?php echo ucfirst($datos['tipo']); ?></div>
                    <div class="info-item"><strong>Antigüedad:</strong> <?php echo $datos['antiguedad']; ?> año<?php echo $datos['antiguedad'] != 1 ? 's' : ''; ?></div>
                    <div class="info-item"><strong>Cuota Base:</strong> $<?php echo number_format($datos['precio_base'], 2); ?></div>
                    <div class="info-item"><strong>Descuento:</strong> <span class="descuento"><?php echo $datos['descuento_porcentaje']; ?>% (-$<?php echo number_format($datos['descuento_monto'], 2); ?>)</span></div>
                    <div class="info-item"><strong>Seguro Médico:</strong> $<?php echo number_format($datos['seguro_medico'], 2); ?></div>
                </div>
                <div class="cuota-final">Cuota Final: $<?php echo number_format($datos['cuota_final'], 2); ?></div>
            </div>
        <?php endforeach; ?>
        <div style="margin-top: 30px; background: #e9ecef; padding: 15px; border-radius: 5px;">
            <h3>Información sobre Descuentos:</h3>
            <ul>
                <li><strong>5+ años:</strong> 20% de descuento</li>
                <li><strong>3-4 años:</strong> 15% de descuento</li>
                <li><strong>1-2 años:</strong> 10% de descuento</li>
                <li><strong>Menos de 1 año:</strong> Sin descuento</li>
            </ul>
        </div>
    </div>
</body>
</html>
