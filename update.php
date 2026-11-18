<?php
require_once 'config.php';

$id = $_GET['id'] ?? '';

if (!filter_var($id, FILTER_VALIDATE_INT)) {
    die("Producto no válido.");}

$stmt = $conexion->prepare("SELECT * FROM productos WHERE id_producto = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$producto = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$producto) {
    die("Producto no encontrado.");}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = trim($_POST["nombre"]);
    $precio = $_POST["precio"];
    $stock = $_POST["stock"];
    $categoria = $_POST["categoria_id"];
    $foto = $producto['foto_p'];

    if (
        $nombre != "" &&
        is_numeric($precio) && $precio >= 0 &&
        filter_var($stock, FILTER_VALIDATE_INT) !== false && $stock >= 0 &&
        filter_var($categoria, FILTER_VALIDATE_INT)
    ) {

        if (!empty($_FILES['foto_p']['name'])) {
            $imagen = getimagesize($_FILES['foto_p']['tmp_name']);
            $tipos = [
                IMAGETYPE_JPEG => 'jpg',
                IMAGETYPE_PNG => 'png',
                IMAGETYPE_GIF => 'gif'];
            if (!$imagen || !isset($tipos[$imagen[2]]) ||
                $_FILES['foto_p']['size'] > 5 * 1024 * 1024) {
                $mensaje = "La fotografía no es válida o supera los 5 MB.";
            } else {
                $nuevoNombre = uniqid('producto_') . '.' . $tipos[$imagen[2]];

                if (move_uploaded_file(
                    $_FILES['foto_p']['tmp_name'],
                    'uploads/' . $nuevoNombre
                )) {
                    $foto = $nuevoNombre;
                } else {
                    $mensaje = "No se pudo guardar la fotografía.";
                }}
        }

        if ($mensaje == "") {

            $sql = "UPDATE productos
                    SET nombre=?, precio=?, stock=?,
                    foto_p=?, categoria_id=?
                    WHERE id_producto=?";

            $stmt = $conexion->prepare($sql);
            $stmt->bind_param(
                "sdisii",
                $nombre,
                $precio,
                $stock,
                $foto,
                $categoria,
                $id);

            if ($stmt->execute()) {
                if ($foto != $producto['foto_p'] && !empty($producto['foto_p'])) {
                    $anterior = 'uploads/' . basename($producto['foto_p']);
                    if (file_exists($anterior)) {
                        unlink($anterior);}}

                header("Location: read.php?actualizado=1");
                exit;
            } else {
                $mensaje = "Error al actualizar el producto.";}

            $stmt->close();}

    } else {
        $mensaje = "Verifica los datos ingresados.";    }}

// Obtener categorías
$categorias = $conexion->query("SELECT * FROM categorias ORDER BY descripcion");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar producto</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

<header class="encabezado">
    <div class="contenedor">
        <h1>Actualizar producto</h1>
        <p>Modificar información del inventario</p>
    </div>
</header>

<main class="contenedor">
    <section class="bienvenida">
        <h2>Editar información</h2>
        <?php if ($mensaje != ""): ?>
            <p><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <p>Nombre:</p>
            <input type="text" name="nombre" maxlength="30"
                value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
            <p>Precio:</p>
            <input type="number" name="precio" step="0.01" min="0"
                value="<?php echo $producto['precio']; ?>" required>
            <p>Stock:</p>
            <input type="number" name="stock" min="0"
                value="<?php echo $producto['stock']; ?>" required>
            <p>Categoría:</p>
            <select name="categoria_id" required>
                <?php while ($cat = $categorias->fetch_assoc()): ?>
                    <option value="<?php echo $cat['id_categoria']; ?>"
                        <?php echo ($cat['id_categoria'] == $producto['categoria_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['descripcion']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <p>Fotografía actual:</p>
            <?php if (!empty($producto['foto_p'])): ?>
                <img src="uploads/<?php echo htmlspecialchars(basename($producto['foto_p'])); ?>"
                     class="foto-producto" alt="Fotografía actual">
            <?php endif; ?>
            <p>Nueva fotografía (opcional):</p>
            <input type="file" name="foto_p"
                accept="image/jpeg,image/png,image/gif">
            <br><br>
            <button type="submit" class="boton">
                Guardar cambios</button>
            <a href="read.php" class="boton">Cancelar</a>
        </form>
    </section>
</main>
</body>
</html>
