<?php
include("conexion.php");

if (isset($_POST['guardar'])) {
    $id = $_POST['id_cliente'];
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $correo_electronico = $_POST['correo_electronico'];
    $tipo_mascota = $_POST['tipo_mascota'];
    $puntos = $_POST['puntos_fidelidad'];

    if ($id == "") {
        $sql = "INSERT INTO clientes
        (nombre, telefono, correo_electronico, tipo_mascota, puntos_fidelidad, estado)
        VALUES
        ('$nombre','$telefono','$correo_electronico','$tipo_mascota','$puntos',1)";
    } else {
        $sql = "UPDATE clientes SET
        nombre='$nombre',
        telefono='$telefono',
        correo_electronico='$correo_electronico',
        tipo_mascota='$tipo_mascota',
        puntos_fidelidad='$puntos'
        WHERE id_cliente=$id";
    }
    $conn->query($sql);
}

if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];

    $conn->query("UPDATE clientes SET estado = 0 WHERE id_cliente = $id");

    header("Location: clientes.php");
    exit;
}


$editar = null;
if (isset($_GET['editar'])) {
    $editar = $conn->query(
        "SELECT * FROM clientes WHERE id_cliente=".$_GET['editar']
    )->fetch_assoc();
}

/* CONSULTA (SOLO ACTIVOS) */
$clientes = $conn->query("SELECT * FROM clientes WHERE estado = 1 ORDER BY nombre");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Clientes</title>
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
<h2>Gestión de Clientes</h2>

<form method="POST">
    <input type="hidden" name="id_cliente" value="<?= $editar['id_cliente'] ?? '' ?>">

    <input type="text" name="nombre" placeholder="Nombre" required
           value="<?= $editar['nombre'] ?? '' ?>">

    <input type="text" name="telefono" placeholder="Teléfono" required
           value="<?= $editar['telefono'] ?? '' ?>">

    <input type="text" name="correo_electronico" placeholder="Correo electronico"
           value="<?= $editar['correo_electronico'] ?? '' ?>">

    <input type="text" name="tipo_mascota" placeholder="Tipo de mascota"
           value="<?= $editar['tipo_mascota'] ?? '' ?>">

    <input type="number" name="puntos_fidelidad" placeholder="Puntos fidelidad"
           value="<?= $editar['puntos_fidelidad'] ?? 0 ?>">

    <button name="guardar">
        <?= $editar ? 'Actualizar' : 'Guardar' ?>
    </button>
</form>

<table>
<tr>
    <th>Nombre</th>
    <th>Teléfono</th>
    <th>Correo electronico</th>
    <th>Mascota</th>
    <th>Puntos</th>
    <th>Acciones</th>
</tr>

<?php while ($c = $clientes->fetch_assoc()) { ?>
<tr>
    <td><?= $c['nombre'] ?></td>
    <td><?= $c['telefono'] ?></td>
    <td><?= $c['correo_electronico'] ?></td>
    <td><?= $c['tipo_mascota'] ?></td>
    <td><?= $c['puntos_fidelidad'] ?></td>
    <td>
        <a href="?editar=<?= $c['id_cliente'] ?>">Editar</a> |
        <a href="?eliminar=<?= $c['id_cliente'] ?>"
           onclick="return confirm('¿Desactivar cliente?')"
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
