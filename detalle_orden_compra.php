<?php
include("conexion.php");

$ordenes = $conn->query("
    SELECT o.id_orden, o.fecha, o.total, o.estado, p.nombre AS proveedor
    FROM orden_compra o
    JOIN proveedores p ON o.id_proveedor = p.id_proveedor
    ORDER BY o.fecha DESC
");

$detalle = null;
if (isset($_GET['id'])) {
    $detalle = $conn->query("
        SELECT d.cantidad, d.costo_unitario, (d.cantidad * d.costo_unitario) AS total, pr.nombre
        FROM detalle_orden_compra d
        JOIN productos pr ON d.id_producto = pr.id_producto
        WHERE d.id_orden = ".$_GET['id']
    );
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Detalle Orden de Compra</title>
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
    padding: 10px;
    border-bottom: 1px solid #ddd;
    text-align: center;
}
th {
    background: #2563eb;
    color: white;
}
.detalle {
    background: #f1f5f9;
    padding: 20px;
    border-radius: 8px;
    margin-top: 30px;
}
.estado {
    font-weight: bold;
}
a {
    color: #2563eb;
    text-decoration: none;
}
</style>
</head>

<body>

<div class="contenedor">
    <h2>Órdenes de Compra</h2>

    <!-- TABLA ORDENES -->
    <table>
        <tr>
            <th>ID Orden</th>
            <th>Fecha</th>
            <th>Proveedor</th>
            <th>Total</th>
            <th>Estado</th>
            <th>Acción</th>
        </tr>

        <?php while ($o = $ordenes->fetch_assoc()) { ?>
        <tr>
            <td><?= $o['id_orden'] ?></td>
            <td><?= $o['fecha'] ?></td>
            <td><?= $o['proveedor'] ?></td>
            <td>$<?= number_format($o['total'], 2) ?></td>
            <td class="estado"><?= $o['estado'] ?></td>
            <td>
                <a href="?id=<?= $o['id_orden'] ?>">Ver detalle</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <?php if ($detalle) { ?>
    <div class="detalle">
        <h3>Detalle de Orden #<?= $_GET['id'] ?></h3>

        <table>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Costo Unitario</th>
                <th>Total</th>
            </tr>

            <?php while ($d = $detalle->fetch_assoc()) { ?>
            <tr>
                <td><?= $d['nombre'] ?></td>
                <td><?= $d['cantidad'] ?></td>
                <td>$<?= number_format($d['costo_unitario'], 2) ?></td>
                <td>$<?= number_format($d['total'], 2) ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
    <?php } ?>

    <br>
    <a href="/tienda_mascotas/index.html">Volver</a>
</div>

</body>
</html>
