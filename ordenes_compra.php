<?php
include("conexion.php");

/* GUARDAR ORDEN */
if (isset($_POST['guardar'])) {

    $id_proveedor = $_POST['id_proveedor'];
    $fecha = date("Y-m-d");
    $estado = "Pendiente";
    $total = 0;

    $conn->query("
        INSERT INTO orden_compra (fecha, estado, total, id_proveedor)
        VALUES ('$fecha', '$estado', 0, '$id_proveedor')
    ");

    $id_orden = $conn->insert_id;

    if (isset($_POST['producto']) && is_array($_POST['producto'])) {

        foreach ($_POST['producto'] as $id_producto => $cantidad) {

            if ($cantidad > 0) {

                $precio = $conn->query(
                    "SELECT precio_compra FROM productos WHERE id_producto = $id_producto"
                )->fetch_assoc()['precio_compra'];

                $subtotal = $precio * $cantidad;
                $total += $subtotal;

                $conn->query("
                    INSERT INTO detalle_orden_compra
                    (cantidad, costo_unitario, id_orden, id_producto)
                    VALUES
                    ('$cantidad','$precio','$id_orden','$id_producto')
                ");

                $conn->query("
                    UPDATE productos
                    SET stock_actual = stock_actual + $cantidad
                    WHERE id_producto = $id_producto
                ");

                $conn->query("
                    INSERT INTO inventario (tipo_movimiento, cantidad, fecha, id_producto)
                    VALUES ('entrada','$cantidad','$fecha','$id_producto')
                ");
            }
        }

        $conn->query("UPDATE orden_compra SET total='$total' WHERE id_orden=$id_orden");

    } else {
        echo "<script>alert('Debe agregar al menos un producto');</script>";
    }
}

/* CONSULTAS */
$proveedores = $conn->query("SELECT * FROM proveedores WHERE estado = 1");
$productos = $conn->query("SELECT * FROM productos ORDER BY nombre");
$ordenes = $conn->query("
    SELECT o.*, p.nombre AS proveedor
    FROM orden_compra o
    JOIN proveedores p ON o.id_proveedor = p.id_proveedor
    ORDER BY o.fecha DESC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Órdenes de Compra</title>
<link rel="stylesheet" href="../css/estilos.css">

<style>
.contenedor {
    background: white;
    padding: 30px;
    border-radius: 10px;
    max-width: 1200px;
    margin: 30px auto;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
th, td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
    text-align: center;
}
th {
    background: #2563eb;
    color: white;
}
input, select {
    width: 100%;
    padding: 8px;
    margin-bottom: 10px;
}
button {
    background: #2563eb;
    color: white;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
}
</style>
</head>

<body>

<div class="contenedor">
<h2>Órdenes de Compra</h2>

<form method="POST">

<select name="id_proveedor" required>
    <option value="">Seleccione proveedor</option>
    <?php while ($p = $proveedores->fetch_assoc()) { ?>
        <option value="<?= $p['id_proveedor'] ?>"><?= $p['nombre'] ?></option>
    <?php } ?>
</select>

<h4>Productos</h4>

<table>
<tr>
    <th>Producto</th>
    <th>Cantidad</th>
</tr>

<?php while ($prod = $productos->fetch_assoc()) { ?>
<tr>
    <td><?= $prod['nombre'] ?></td>
    <td>
        <input type="number" min="0" name="producto[<?= $prod['id_producto'] ?>]">
    </td>
</tr>
<?php } ?>
</table>

<br>
<button name="guardar">Guardar Orden</button>
</form>

<h3>Historial de Órdenes</h3>

<table>
<tr>
    <th>Fecha</th>
    <th>Proveedor</th>
    <th>Total</th>
    <th>Estado</th>
</tr>

<?php while ($o = $ordenes->fetch_assoc()) { ?>
<tr>
    <td><?= $o['fecha'] ?></td>
    <td><?= $o['proveedor'] ?></td>
    <td>$<?= $o['total'] ?></td>
    <td><?= $o['estado'] ?></td>
</tr>
<?php } ?>
</table>

<br>
<a href="/tienda_mascotas/index.html">Volver</a>
</div>

</body>
</html>
