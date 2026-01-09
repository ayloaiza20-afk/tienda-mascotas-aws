<?php
session_start();
include("conexion.php");

$usuario = $_POST['usuario'];
$password = $_POST['password'];

$sql = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ?");
$sql->bind_param("s", $usuario);
$sql->execute();
$resultado = $sql->get_result();

if ($resultado->num_rows == 1) {
    $user = $resultado->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        $_SESSION['id_usuario'] = $user['id_usuario'];
        $_SESSION['rol'] = $user['rol'];

        header("Location: index.html");
    } else {
        echo "Contraseña incorrecta";
    }
} else {
    echo "Usuario no encontrado";
}
