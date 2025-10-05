<?php
// 1. Crear un string JSON con datos de una tienda en línea
$jsonDatos = '
{
    "tienda": "ElectroTech",
    "productos": [
        {"id": 1, "nombre": "Laptop Gamer", "precio": 1200, "categorias": ["electrónica", "computadoras"]},
        {"id": 2, "nombre": "Smartphone 5G", "precio": 800, "categorias": ["electrónica", "celulares"]},
        {"id": 3, "nombre": "Auriculares Bluetooth", "precio": 150, "categorias": ["electrónica", "accesorios"]},
        {"id": 4, "nombre": "Smart TV 4K", "precio": 700, "categorias": ["electrónica", "televisores"]},
        {"id": 5, "nombre": "Tablet", "precio": 300, "categorias": ["electrónica", "computadoras"]}
    ],
    "clientes": [
        {"id": 101, "nombre": "Ana López", "email": "ana@example.com"},
        {"id": 102, "nombre": "Carlos Gómez", "email": "carlos@example.com"},
        {"id": 103, "nombre": "María Rodríguez", "email": "maria@example.com"}
    ]
}
';

// 2. Convertir el JSON a un arreglo asociativo de PHP
$tiendaData = json_decode($jsonDatos, true);

// 3. Función para imprimir los productos
function imprimirProductos($productos) {
    foreach ($productos as $producto) {
        echo "{$producto['nombre']} - \${$producto['precio']} - Categorías: " . implode(", ", $producto['categorias']) . "\n";
}
}

echo "Productos de {$tiendaData['tienda']}:\n";
imprimirProductos($tiendaData['productos']);

// 4. Calcular el valor total del inventario
$valorTotal = array_reduce($tiendaData['productos'], function($total, $producto) {
    return $total + $producto['precio'];
}, 0);

echo "\nValor total del inventario: $$valorTotal\n";

// 5. Encontrar el producto más caro
$productoMasCaro = array_reduce($tiendaData['productos'], function($max, $producto) {
    return ($producto['precio'] > $max['precio']) ? $producto : $max;
}, $tiendaData['productos'][0]);
echo "\nProducto más caro: {$productoMasCaro['nombre']} (\${$productoMasCaro['precio']})\n";

// 6. Filtrar productos por categoría
function filtrarPorCategoria($productos, $categoria) {
    return array_filter($productos, function($producto) use ($categoria) {
        return in_array($categoria, $producto['categorias']);
    });
}

$productosDeComputadoras = filtrarPorCategoria($tiendaData['productos'], "computadoras");
echo "\nProductos en la categoría 'computadoras':\n";
imprimirProductos($productosDeComputadoras);

// 7. Agregar un nuevo producto
$nuevoProducto = [
    "id" => 6,
    "nombre" => "Smartwatch",
    "precio" => 250,
    "categorias" => ["electrónica", "accesorios", "wearables"]
];
$tiendaData['productos'][] = $nuevoProducto;

// 8. Convertir el arreglo actualizado de vuelta a JSON
$jsonActualizado = json_encode($tiendaData, JSON_PRETTY_PRINT);
echo "\nDatos actualizados de la tienda (JSON):\n$jsonActualizado\n";

// TAREA: Implementa una función que genere un resumen de ventas
// Datos de productos
$productos = [
    1 => "Laptop Gamer",
    2 => "Smartphone 5G",
    3 => "Auriculares Bluetooth",
    4 => "Smart TV 4K",
    5 => "Tablet",
    6 => "Smartwatch"
];

// Datos de clientes
$clientes = [
    101 => "Ana López",
    102 => "Carlos Gómez",
    103 => "María Rodríguez"
];

// Arreglo de ventas
$ventas = [
    ["producto_id" => 1, "cliente_id" => 101, "cantidad" => 1, "precio_unitario" => 1200,"fecha" => "2024-01-15"],
    ["producto_id" => 2, "cliente_id" => 102, "cantidad" => 2, "precio_unitario" => 800,"fecha" => "2024-01-16"],
    ["producto_id" => 3, "cliente_id" => 103, "cantidad" => 1, "precio_unitario" => 150,"fecha"=> "2024-01-17"],
    ["producto_id" => 1, "cliente_id" => 103, "cantidad" => 1, "precio_unitario" => 1200,"fecha" =>"2024-01-18"],
    ["producto_id" => 5, "cliente_id" => 101, "cantidad" => 3, "precio_unitario" => 300,"fecha" => "2024-01-19"]
];

// Función para generar resumen de ventas
function generarResumenVentas($ventas) {
    $contadorClientes = [];
    $contadorProductos = [];
    $totalVentas = count($ventas);

    foreach ($ventas as $venta) {
        // Contar compras por cliente
        if (!isset($contadorClientes[$venta['cliente_id']])) {
            $contadorClientes[$venta['cliente_id']] = 0;
        }
        $contadorClientes[$venta['cliente_id']] += $venta['cantidad'];

        // Contar ventas por producto
        if (!isset($contadorProductos[$venta['producto_id']])) {
            $contadorProductos[$venta['producto_id']] = 0;
        }
        $contadorProductos[$venta['producto_id']] += $venta['cantidad'];
    }

    // Cliente que más ha comprado
    $clienteMasComprador = array_search(max($contadorClientes), $contadorClientes);

    // Producto más vendido
    $productoMasVendido = array_search(max($contadorProductos), $contadorProductos);

    return [
        'total_ventas' => $totalVentas,
        'cliente_mas_comprador' => $clienteMasComprador,
        'producto_mas_vendido' => $productoMasVendido
    ];
}

// Generar resumen
$resumen = generarResumenVentas($ventas);

// Mostrar resultados con nombres
echo "<table>";
echo "<tr>\nResumen de ventas:\n</tr>";
echo "Total de ventas: {$resumen['total_ventas']}\n";
echo "Producto más vendido: {$productos[$resumen['producto_mas_vendido']]}\n";
echo "Cliente que más ha comprado: {$clientes[$resumen['cliente_mas_comprador']]}\n";
echo "</table>";
?>