<?php
// gestionar_membresias.php
include 'funciones_gimnasio.php';

// 1. Array asociativo con 5 tipos de membresía y sus precios base
$membresias = [
    'basica' => 80,
    'premium' => 120,
    'vip' => 180,
    'familiar' => 250,
    'corporativa' => 300
];

// 2. Array asociativo con la información de 5 miembros
$miembros = [
    'Juan Pérez' => ['tipo' => 'premium', 'antiguedad' => 1],
    'Ana García' => ['tipo' => 'basica', 'antiguedad' => 2],
    'Carlos López' => ['tipo' => 'vip', 'antiguedad' => 3],
    'María Rodríguez' => ['tipo' => 'familiar', 'antiguedad' => 5],
    'Luis Martínez' => ['tipo' => 'corporativa', 'antiguedad' => 1]
];
// 3. Procesar cada miembro y calcular cuotas
$resultados = [];
foreach ($miembros as $nombre => $info) {
    $tipo = $info['tipo'];
    $antiguedad = $info['antiguedad'];
    $precioBase = $membresias[$tipo];
    
    // Calcular descuento
    $porcentajeDescuento = calcularDescuento($antiguedad);
    $montoDescuento = $precioBase * $porcentajeDescuento;
    
    // Calcular seguro médico
    $seguroMedico = calcularSeguroMedico($tipo);
    
    // Calcular cuota final
    $cuotaFinal = calcularCuotaFinal($info, $membresias);
    
    // Almacenar resultados
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

// 4. Mostrar resumen en formato HTML
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Membresías - Gimnasio</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .miembro {
            border: 1px solid #ddd;
            margin: 15px 0;
            padding: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .miembro h3 {
            color: #007bff;
            margin-top: 0;
        }
        .info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin: 10px 0;
        }
        .info-item {
            background-color: white;
            padding: 8px;
            border-radius: 3px;
            border-left: 3px solid #007bff;
        }
        .info-item strong {
            color: #333;
        }
        .cuota-final {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            font-size: 1.1em;
        }
        .descuento {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Resumen de Membresías del Gimnasio</h1>
        
        <?php foreach ($resultados as $nombre => $datos): ?>
            <div class="miembro">
                <h3><?php echo htmlspecialchars($nombre); ?></h3>
                
                <div class="info">
                    <div class="info-item">
                        <strong>Tipo de Membresía:</strong> 
                        <?php echo ucfirst(htmlspecialchars($datos['tipo'])); ?>
                    </div>
                    
                    <div class="info-item">
                        <strong>Antigüedad:</strong> 
                        <?php echo $datos['antiguedad']; ?> año<?php echo $datos['antiguedad'] != 1 ? 's' : ''; ?>
                    </div>
                    
                    <div class="info-item">
                        <strong>Cuota Base:</strong> 
                        $<?php echo number_format($datos['precio_base'], 2); ?>
                    </div>
                    
                    <div class="info-item">
                        <strong>Descuento:</strong> 
                        <span class="descuento">
                            <?php echo $datos['descuento_porcentaje']; ?>% 
                            (-$<?php echo number_format($datos['descuento_monto'], 2); ?>)
                        </span>
                    </div>
                    
                    <div class="info-item">
                        <strong>Seguro Médico:</strong> 
                        $<?php echo number_format($datos['seguro_medico'], 2); ?>
                    </div>
                </div>
                
                <div class="cuota-final">
                    Cuota Final a Pagar: $<?php echo number_format($datos['cuota_final'], 2); ?>
                </div>
            </div>
        <?php endforeach; ?>
        
        <div style="margin-top: 30px; padding: 15px; background-color: #e9ecef; border-radius: 5px;">
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