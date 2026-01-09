<?php
// crear_usuario.php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre   = $_POST['nombre'];
    $usuario  = $_POST['usuario'];
    $password = $_POST['password'];
    $rol      = $_POST['rol'];

    // VERIFICAR SI EL USUARIO YA EXISTE
    $check = $conn->query("SELECT id_usuario FROM usuarios WHERE usuario = '$usuario'");
    if ($check->num_rows > 0) {
        echo "<script>
            alert('El usuario ya existe, elige otro.');
            window.location.href='registro.html';
        </script>";
        exit;
    }

    // ENCRIPTAR CONTRASEÑA
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // INSERTAR USUARIO
    $sql = "INSERT INTO usuarios (nombre, usuario, password, rol)
            VALUES ('$nombre', '$usuario', '$password_hash', '$rol')";

    if ($conn->query($sql)) {
        echo "<script>
            alert('Usuario registrado correctamente');
            window.location.href='login.html';
        </script>";
    } else {
        echo "<script>
            alert('Error al registrar usuario');
            window.location.href='registro.html';
        </script>";
    }
}
?>
