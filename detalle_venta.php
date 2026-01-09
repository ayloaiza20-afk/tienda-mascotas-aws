<?php
include("conexion.php");

$ventas = $conn->query("
    SELECT v.id_venta, v.fecha, v.total, c.nombre AS cliente
    FROM ventas v
    JOIN clientes c ON v.id_cliente = c.id_cliente
    ORDER BY v.fecha DESC
");

$detalle = null;
$idVenta = null;

if (isset($_GET['id'])) {
    $idVenta = $_GET['id'];
    $detalle = $conn->query("
        SELECT d.cantidad, d.precio_unitario, d.total, p.nombre
        FROM detalle_venta d
        JOIN productos p ON d.id_producto = p.id_producto
        WHERE d.id_venta = $idVenta
    ");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Detalle de Ventas</title>
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
.btn-pdf {
    display: inline-block;
    background: #2563eb;
    color: white;
    padding: 10px 15px;
    border-radius: 5px;
    text-decoration: none;
    margin-bottom: 15px;
}
.btn-pdf:hover {
    background: #1e40af;
}
a {
    color: #2563eb;
    text-decoration: none;
    font-weight: bold;
}
</style>
</head>

<body>

<div class="contenedor">
    <h2>Historial de Ventas</h2>

    <table>
        <tr>
            <th>ID Venta</th>
            <th>Fecha</th>
            <th>Cliente</th>
            <th>Total</th>
            <th>Acción</th>
        </tr>

        <?php while ($v = $ventas->fetch_assoc()) { ?>
        <tr>
            <td><?= $v['id_venta'] ?></td>
            <td><?= $v['fecha'] ?></td>
            <td><?= $v['cliente'] ?></td>
            <td>$<?= number_format($v['total'], 2) ?></td>
            <td>
                <a href="?id=<?= $v['id_venta'] ?>">Ver detalle</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <?php if ($detalle) { ?>
    <div class="detalle">
        <h3>🧾 Detalle de la Venta #<?= $idVenta ?></h3>


        <a class="btn-pdf" href="factura_pdf.php?id=<?= $idVenta ?>" target="_blank">
            🖨 Generar Ticket PDF
        </a>

        <table>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Total</th>
            </tr>

            <?php while ($d = $detalle->fetch_assoc()) { ?>
            <tr>
                <td><?= $d['nombre'] ?></td>
                <td><?= $d['cantidad'] ?></td>
                <td>$<?= number_format($d['precio_unitario'], 2) ?></td>
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
