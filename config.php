
<?php
//conexión a la base de datos

$servidor = "localhost";
$usuario = "root";
$password = "";
$base_datos = "AVS_03_04_01";

try {
    $conexion = new mysqli(
        $servidor,
        $usuario,
        $password,
        $base_datos
    );

    // configuración para caracteres especiales como ñ y tildes
    $conexion->set_charset("utf8mb4");

} catch (mysqli_sql_exception $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>
