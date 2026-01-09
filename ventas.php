<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

if (isset($_POST['agregar_carrito'])) {
    list($id_producto, $precio_venta) = explode("|", $_POST['id_producto']);
    $cantidad = (int)$_POST['cantidad'];

    $stock_disponible = $conn->query("SELECT stock_actual, nombre FROM productos WHERE id_producto = $id_producto")->fetch_assoc();
    if($cantidad > $stock_disponible['stock_actual']){
        $error = "No hay suficiente stock de {$stock_disponible['nombre']}.";
    } else {
        $existe = false;
        foreach($_SESSION['carrito'] as &$item) {
            if($item['id_producto'] == $id_producto) {
                $item['cantidad'] += $cantidad;
                $existe = true;
                break;
            }
        }
        if(!$existe){
            $_SESSION['carrito'][] = [
                'id_producto' => $id_producto,
                'nombre' => $stock_disponible['nombre'],
                'precio_venta' => $precio_venta,
                'cantidad' => $cantidad
            ];
        }
    }
}

if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    foreach($_SESSION['carrito'] as $key => $item) {
        if($item['id_producto'] == $id) {
            unset($_SESSION['carrito'][$key]);
            $_SESSION['carrito'] = array_values($_SESSION['carrito']);
            break;
        }
    }
}

$venta_detalle = null;
if (isset($_POST['guardar_venta']) && !empty($_SESSION['carrito'])) {
    $fecha = date('Y-m-d');
    $subtotal = 0;

    foreach($_SESSION['carrito'] as $item) {
        $subtotal += $item['precio_venta'] * $item['cantidad'];
    }

    $impuestos = $subtotal * 0.16;
    $descuento = $_POST['descuento'] ?? 0;
    $total = $subtotal + $impuestos - $descuento;

    $conn->query("INSERT INTO ventas (fecha, subtotal, impuestos, descuento, total, id_cliente) 
                  VALUES ('$fecha', '$subtotal', '$impuestos', '$descuento', '$total', NULL)");
    $id_venta = $conn->insert_id;

    foreach($_SESSION['carrito'] as $item) {
        $conn->query("INSERT INTO detalle_venta (cantidad, precio_unitario, total, id_venta, id_producto)
                      VALUES ({$item['cantidad']}, {$item['precio_venta']}, {$item['cantidad']}*{$item['precio_venta']}, $id_venta, {$item['id_producto']})");

        $conn->query("INSERT INTO inventario (tipo_movimiento, cantidad, fecha, id_producto) 
                      VALUES ('salida', {$item['cantidad']}, '$fecha', {$item['id_producto']})");

        $conn->query("UPDATE productos SET stock_actual = stock_actual - {$item['cantidad']} WHERE id_producto = {$item['id_producto']}");
    }

    $venta_detalle = $_SESSION['carrito'];
    $venta_totales = [
        'subtotal' => $subtotal,
        'impuestos' => $impuestos,
        'total' => $total
    ];

    $_SESSION['carrito'] = [];
}

$productos = $conn->query("SELECT * FROM productos ORDER BY nombre");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ventas - Punto de Venta</title>
<link rel="stylesheet" href="../css/estilos.css">
<style>
.contenedor { background: white; padding: 30px; border-radius: 10px; max-width: 1200px; margin: 30px auto; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: center; }
th { background: #2563eb; color: white; }
input, select { width: 100%; padding: 8px; margin-bottom: 10px; }
button { background: #2563eb; color: white; border: none; padding: 10px 20px; cursor: pointer; }
a { text-decoration: none; }
h3 { margin-top: 30px; }
</style>
</head>

<body>
<div class="contenedor">
    <h2>Punto de Venta</h2>

    <?php 
    if(!empty($error)) echo "<p style='color:red;'>$error</p>";
    ?>

    <form method="POST">
        <?php $productos = $conn->query("SELECT * FROM productos ORDER BY nombre"); ?>
        <select name="id_producto" required>
            <option value="">Seleccione producto</option>
            <?php while($p = $productos->fetch_assoc()) { ?>
                <option value="<?= $p['id_producto'] ?>|<?= $p['precio_venta'] ?>">
                    <?= $p['nombre'] ?> - $<?= $p['precio_venta'] ?> (Stock: <?= $p['stock_actual'] ?>)
                </option>
            <?php } ?>
        </select>
        <input type="number" name="cantidad" placeholder="Cantidad" min="1" required>
        <button name="agregar_carrito">Agregar al carrito</button>
    </form>

    <div class="carrito-container">
        <h3>Carrito de Compras</h3>
        <?php if(!empty($_SESSION['carrito'])) { ?>
        <table>
            <tr>
                <th>Producto</th>
                <th>Precio Unitario</th>
                <th>Cantidad</th>
                <th>Total</th>
                <th>Acciones</th>
            </tr>
            <?php 
            $subtotal = 0;
            foreach($_SESSION['carrito'] as $item) { 
                $total_item = $item['precio_venta'] * $item['cantidad'];
                $subtotal += $total_item;
            ?>
            <tr>
                <td><?= $item['nombre'] ?></td>
                <td>$<?= $item['precio_venta'] ?></td>
                <td><?= $item['cantidad'] ?></td>
                <td>$<?= $total_item ?></td>
                <td>
                    <a href="?eliminar=<?= $item['id_producto'] ?>" style="color:red;">Eliminar</a>
                </td>
            </tr>
            <?php } ?>
        </table>

        <p>Subtotal: $<?= $subtotal ?></p>
        <p>Impuestos (16%): $<?= $subtotal*0.16 ?></p>
        <p>Total: $<?= $subtotal*1.16 ?></p>

        <form method="POST">
            <button name="guardar_venta">Registrar Venta</button>
        </form>
        <?php } else { ?>
            <p>No hay productos en el carrito</p>
        <?php } ?>
    </div>

    <?php if($venta_detalle) { ?>
        <h3>Detalles de la Venta</h3>
        <table>
            <tr>
                <th>Producto</th>
                <th>Precio Unitario</th>
                <th>Cantidad</th>
                <th>Total</th>
            </tr>
            <?php foreach($venta_detalle as $item) { 
                $total_item = $item['precio_venta'] * $item['cantidad'];
            ?>
            <tr>
                <td><?= $item['nombre'] ?></td>
                <td>$<?= $item['precio_venta'] ?></td>
                <td><?= $item['cantidad'] ?></td>
                <td>$<?= $total_item ?></td>
            </tr>
            <?php } ?>
        </table>
        <p>Subtotal: $<?= $venta_totales['subtotal'] ?></p>
        <p>Impuestos: $<?= $venta_totales['impuestos'] ?></p>
        <p><strong>Total: $<?= $venta_totales['total'] ?></strong></p>
    <?php } ?>

    <br>
    <a href="/tienda_mascotas/index.html">Volver</a>
</div>
</body>
</html>
