<?php
require_once 'config.php';

$id = $_GET['id'] ?? '';

if (!filter_var($id, FILTER_VALIDATE_INT)) {
    die("Producto no válido.");}

$stmt = $conexion->prepare(
    "SELECT * FROM productos WHERE id_producto = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$producto = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$producto) {
    die("Producto no encontrado.");}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $stmt = $conexion->prepare(
        "DELETE FROM productos WHERE id_producto = ?"
    );
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $foto = 'uploads/' . basename($producto['foto_p']);

        if (!empty($producto['foto_p']) && file_exists($foto)) {
            unlink($foto);}
        header("Location: read.php?eliminado=1");
        exit;

    } else {
        $mensaje = "No se pudo eliminar el producto.";}

    $stmt->close();}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar producto</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
<header class="encabezado">
    <div class="contenedor">
        <h1>Eliminar producto</h1>
        <p>Confirmación de eliminación</p>
    </div>
</header>
<main class="contenedor">

    <section class="bienvenida">
        <h2>¿Deseas eliminar este producto?</h2>
        <p>
            <strong>Producto:</strong>
            <?php echo htmlspecialchars($producto['nombre']); ?></p>
        <p>
            <strong>Precio:</strong>
            $<?php echo number_format($producto['precio'], 2); ?></p>
        <p>
            <strong>Stock:</strong>
            <?php echo $producto['stock']; ?></p>
        <?php if ($mensaje != ""): ?>
            <p><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>
        <form method="POST">
            <button type="submit" class="boton">
                Confirmar eliminación
            </button>
            <a href="read.php" class="boton">Cancelar</a>
        </form>
    </section>
</main>
</body>
</html>