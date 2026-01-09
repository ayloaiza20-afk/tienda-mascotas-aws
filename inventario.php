<?php
include("conexion.php");


if (isset($_POST['guardar'])) {
    $tipo = $_POST['tipo_movimiento'];
    $cantidad = $_POST['cantidad'];
    $fecha = $_POST['fecha'];
    $id_producto = $_POST['id_producto'];

    $sql = "INSERT INTO inventario (tipo_movimiento, cantidad, fecha, id_producto)
            VALUES ('$tipo', '$cantidad', '$fecha', '$id_producto')";
    $conn->query($sql);
}


$productos = $conn->query("SELECT * FROM productos ORDER BY nombre");
$inventarios = $conn->query("
    SELECT i.*, p.nombre AS nombre_producto 
    FROM inventario i 
    LEFT JOIN productos p ON i.id_producto = p.id_producto
    ORDER BY i.fecha DESC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Inventario</title>
<link rel="stylesheet" href="../css/estilos.css">

<style>
.contenedor {
    background: white;
    padding: 30px;
    border-radius: 10px;
    max-width: 1000px;
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

a {
    text-decoration: none;
}
</style>
</head>

<body>
<div class="contenedor">
    <h2>Gestión de Inventario</h2>


    <form method="POST">
        <select name="id_producto" required>
            <option value="">Seleccione producto</option>
            <?php while($p = $productos->fetch_assoc()) { ?>
                <option value="<?= $p['id_producto'] ?>"><?= $p['nombre'] ?></option>
            <?php } ?>
        </select>

        <select name="tipo_movimiento" required>
            <option value="">Tipo de movimiento</option>
            <option value="entrada">Entrada</option>
            <option value="salida">Salida</option>
        </select>

        <input type="number" name="cantidad" placeholder="Cantidad" required>

        <input type="date" name="fecha" required>

        <button name="guardar">Registrar Movimiento</button>
    </form>

    <table>
        <tr>
            <th>Producto</th>
            <th>Tipo</th>
            <th>Cantidad</th>
            <th>Fecha</th>
        </tr>
        <?php while($i = $inventarios->fetch_assoc()) { ?>
        <tr>
            <td><?= $i['nombre_producto'] ?></td>
            <td><?= ucfirst($i['tipo_movimiento']) ?></td>
            <td><?= $i['cantidad'] ?></td>
            <td><?= $i['fecha'] ?></td>
        </tr>
        <?php } ?>
    </table>

    <br>
    <a href="/tienda_mascotas/index.html">Volver</a>
</div>
</body>
</html>
