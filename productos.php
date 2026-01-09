<?php
include("conexion.php");

/* GUARDAR / ACTUALIZAR PRODUCTO */
if (isset($_POST['guardar'])) {
    $id = $_POST['id_producto'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $marca = $_POST['marca'];
    $precio_compra = $_POST['precio_compra'];
    $precio_venta = $_POST['precio_venta'];
    $stock = $_POST['stock_actual'];
    $stock_minimo = $_POST['stock_minimo'];
    $fecha_vencimiento = $_POST['fecha_vencimiento'];
    $id_categoria = $_POST['id_categoria'];

    if ($id == "") {
        $sql = "INSERT INTO productos
        (nombre, descripcion, marca, precio_compra, precio_venta, stock_actual, stock_minimo, fecha_vencimiento, id_categoria)
        VALUES
        ('$nombre','$descripcion','$marca','$precio_compra','$precio_venta','$stock','$stock_minimo','$fecha_vencimiento','$id_categoria')";
    } else {
        $sql = "UPDATE productos SET
        nombre='$nombre',
        descripcion='$descripcion',
        marca='$marca',
        precio_compra='$precio_compra',
        precio_venta='$precio_venta',
        stock_actual='$stock',
        stock_minimo='$stock_minimo',
        fecha_vencimiento='$fecha_vencimiento',
        id_categoria='$id_categoria'
        WHERE id_producto=$id";
    }
    $conn->query($sql);
}

/* ELIMINAR (CORREGIDO FK) */
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];

    /* eliminar registros relacionados */
    $conn->query("DELETE FROM detalle_orden_compra WHERE id_producto = $id");
    $conn->query("DELETE FROM detalle_venta WHERE id_producto = $id");
    $conn->query("DELETE FROM inventario WHERE id_producto = $id");

    /* eliminar producto */
    $conn->query("DELETE FROM productos WHERE id_producto = $id");

    header("Location: productos.php");
    exit;
}

/* EDITAR */
$editar = null;
if (isset($_GET['editar'])) {
    $editar = $conn->query("SELECT * FROM productos WHERE id_producto=".$_GET['editar'])->fetch_assoc();
}

/* CONSULTAS */
$productos = $conn->query("
SELECT p.*, c.nombre_categoria
FROM productos p
LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
ORDER BY p.nombre
");

$categorias = $conn->query("SELECT * FROM categorias");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Productos</title>
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
input, textarea, select {
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
.alerta {
    background: #fee2e2;
}
</style>
</head>

<body>

<div class="contenedor">
    <h2>Gestión de Productos</h2>

    <!-- FORMULARIO -->
    <form method="POST">
        <input type="hidden" name="id_producto" value="<?= $editar['id_producto'] ?? '' ?>">

        <input type="text" name="nombre" placeholder="Nombre del producto" required
               value="<?= $editar['nombre'] ?? '' ?>">

        <textarea name="descripcion" placeholder="Descripción"><?= $editar['descripcion'] ?? '' ?></textarea>

        <input type="text" name="marca" placeholder="Marca"
               value="<?= $editar['marca'] ?? '' ?>">

        <input type="number" step="0.01" name="precio_compra" placeholder="Precio compra" required
               value="<?= $editar['precio_compra'] ?? '' ?>">

        <input type="number" step="0.01" name="precio_venta" placeholder="Precio venta" required
               value="<?= $editar['precio_venta'] ?? '' ?>">

        <input type="number" name="stock_actual" placeholder="Stock actual" required
               value="<?= $editar['stock_actual'] ?? '' ?>">

        <input type="number" name="stock_minimo" placeholder="Stock mínimo" required
               value="<?= $editar['stock_minimo'] ?? '' ?>">

        <input type="date" name="fecha_vencimiento"
               value="<?= $editar['fecha_vencimiento'] ?? '' ?>">

        <select name="id_categoria">
            <option value="">Seleccione categoría</option>
            <?php while($c = $categorias->fetch_assoc()) { ?>
                <option value="<?= $c['id_categoria'] ?>"
                <?= ($editar && $editar['id_categoria'] == $c['id_categoria']) ? 'selected' : '' ?>>
                    <?= $c['nombre_categoria'] ?>
                </option>
            <?php } ?>
        </select>

        <button name="guardar">
            <?= $editar ? 'Actualizar' : 'Guardar' ?>
        </button>
    </form>

    <!-- TABLA -->
    <table>
        <tr>
            <th>Producto</th>
            <th>Categoría</th>
            <th>Precio Venta</th>
            <th>Stock</th>
            <th>Vence</th>
            <th>Acciones</th>
        </tr>

        <?php while ($p = $productos->fetch_assoc()) {
            $alerta = ($p['stock_actual'] <= $p['stock_minimo']) ? 'alerta' : '';
        ?>
        <tr class="<?= $alerta ?>">
            <td><?= $p['nombre'] ?></td>
            <td><?= $p['nombre_categoria'] ?? 'Sin categoría' ?></td>
            <td>$<?= $p['precio_venta'] ?></td>
            <td><?= $p['stock_actual'] ?></td>
            <td><?= $p['fecha_vencimiento'] ?></td>
            <td>
                <a href="?editar=<?= $p['id_producto'] ?>">Editar</a> |
                <a href="?eliminar=<?= $p['id_producto'] ?>"
                   onclick="return confirm('¿Eliminar este producto?')"
                   style="color:red;">Eliminar</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <p style="margin-top:10px; color:red;">
        Productos en rojo = stock bajo
    </p>

    <a href="/tienda_mascotas/index.html">Volver</a>
</div>

</body>
</html>
