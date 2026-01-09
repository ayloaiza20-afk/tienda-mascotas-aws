<?php
include("conexion.php");

/* GUARDAR / ACTUALIZAR */
if (isset($_POST['guardar'])) {
    $id = $_POST['id_proveedor'];
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $correo = $_POST['correo'];

    if ($id == "") {
        $sql = "INSERT INTO proveedores
        (nombre, telefono, direccion, correo, estado)
        VALUES
        ('$nombre','$telefono','$direccion','$correo',1)";
    } else {
        $sql = "UPDATE proveedores SET
        nombre='$nombre',
        telefono='$telefono',
        direccion='$direccion',
        correo='$correo'
        WHERE id_proveedor=$id";
    }
    $conn->query($sql);
}

/* ELIMINAR (BORRADO LÓGICO) */
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];

    $conn->query("UPDATE proveedores SET estado = 0 WHERE id_proveedor = $id");

    header("Location: proveedores.php");
    exit;
}

/* EDITAR */
$editar = null;
if (isset($_GET['editar'])) {
    $editar = $conn->query(
        "SELECT * FROM proveedores WHERE id_proveedor=".$_GET['editar']
    )->fetch_assoc();
}

/* CONSULTA (SOLO ACTIVOS) */
$proveedores = $conn->query("SELECT * FROM proveedores WHERE estado = 1 ORDER BY nombre");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Proveedores</title>
<link rel="stylesheet" href="../css/estilos.css">

<style>
.contenedor {
    background: white;
    padding: 30px;
    border-radius: 10px;
    max-width: 1100px;
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
input {
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
<h2>Gestión de Proveedores</h2>

<form method="POST">
    <input type="hidden" name="id_proveedor" value="<?= $editar['id_proveedor'] ?? '' ?>">

    <input type="text" name="nombre" placeholder="Nombre" required
           value="<?= $editar['nombre'] ?? '' ?>">

    <input type="text" name="telefono" placeholder="Teléfono"
           value="<?= $editar['telefono'] ?? '' ?>">

    <input type="text" name="direccion" placeholder="Dirección"
           value="<?= $editar['direccion'] ?? '' ?>">

    <input type="email" name="correo" placeholder="Correo"
           value="<?= $editar['correo'] ?? '' ?>">

    <button name="guardar">
        <?= $editar ? 'Actualizar' : 'Guardar' ?>
    </button>
</form>

<table>
<tr>
    <th>Nombre</th>
    <th>Teléfono</th>
    <th>Dirección</th>
    <th>Correo</th>
    <th>Acciones</th>
</tr>

<?php while ($p = $proveedores->fetch_assoc()) { ?>
<tr>
    <td><?= $p['nombre'] ?></td>
    <td><?= $p['telefono'] ?></td>
    <td><?= $p['direccion'] ?></td>
    <td><?= $p['correo'] ?></td>
    <td>
        <a href="?editar=<?= $p['id_proveedor'] ?>">Editar</a> |
        <a href="?eliminar=<?= $p['id_proveedor'] ?>"
           onclick="return confirm('¿Desactivar proveedor?')"
           style="color:red;">Eliminar</a>
    </td>
</tr>
<?php } ?>
</table>

<br>
<a href="/tienda_mascotas/index.html">Volver</a>
</div>

</body>
</html>
