<?php
include("conexion.php");

if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];

    $conn->query("INSERT INTO categorias (nombre_categoria, descripcion)
                  VALUES ('$nombre', '$descripcion')");
}

if (isset($_GET['eliminar'])) {
    $conn->query("DELETE FROM categorias WHERE id_categoria=".$_GET['eliminar']);
}

$editar = null;
if (isset($_GET['editar'])) {
    $editar = $conn->query("SELECT * FROM categorias WHERE id_categoria=".$_GET['editar'])->fetch_assoc();
}

if (isset($_POST['actualizar'])) {
    $id = $_POST['id_categoria'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];

    $conn->query("UPDATE categorias 
                  SET nombre_categoria='$nombre', descripcion='$descripcion'
                  WHERE id_categoria=$id");
}

$categorias = $conn->query("SELECT * FROM categorias");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Categorías</title>
<link rel="stylesheet" href="../css/estilos.css">

<style>
.contenedor {
    background: white;
    padding: 30px;
    border-radius: 10px;
    max-width: 900px;
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
input, textarea {
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
    font-weight: bold;
}
</style>
</head>

<body>

<div class="contenedor">
    <h2>Gestión de Categorías</h2>

    <form method="POST">
        <input type="hidden" name="id_categoria" value="<?= $editar['id_categoria'] ?? '' ?>">

        <input type="text" name="nombre" placeholder="Nombre de la categoría" required
               value="<?= $editar['nombre_categoria'] ?? '' ?>">

        <textarea name="descripcion" placeholder="Descripción"><?= $editar['descripcion'] ?? '' ?></textarea>

        <?php if ($editar) { ?>
            <button name="actualizar">Actualizar</button>
        <?php } else { ?>
            <button name="guardar">Guardar</button>
        <?php } ?>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Categoría</th>
            <th>Acciones</th>
        </tr>

        <?php while ($row = $categorias->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id_categoria'] ?></td>
            <td><?= $row['nombre_categoria'] ?></td>
            <td>
                <a href="?editar=<?= $row['id_categoria'] ?>">Editar</a> |
                <a href="?eliminar=<?= $row['id_categoria'] ?>" style="color:red;">Eliminar</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <br>
    <a href="/tienda_mascotas/index.html">Volver</a>
</div>

</body>
</html>
