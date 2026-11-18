<?php
require_once 'config.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = trim($_POST["nombre"]);
    $precio = $_POST["precio"];
    $stock = $_POST["stock"];
    $categoria = $_POST["categoria_id"];

    if (
        !empty($nombre) &&
        is_numeric($precio) && $precio >= 0 &&
        filter_var($stock, FILTER_VALIDATE_INT) !== false && $stock >= 0 &&
        filter_var($categoria, FILTER_VALIDATE_INT)
    ) { if (isset($_FILES["foto_p"]) &&
            $_FILES["foto_p"]["error"] == 0) {

            $foto = $_FILES["foto_p"];
            $imagen = getimagesize($foto["tmp_name"]);

            if ($imagen && $foto["size"] <= 5 * 1024 * 1024) {

                $extensiones = [
                    IMAGETYPE_JPEG => "jpg",
                    IMAGETYPE_PNG => "png",
                    IMAGETYPE_GIF => "gif"
                ];

                $tipo = $imagen[2];

                if (isset($extensiones[$tipo])) {

                    $nombreFoto = uniqid("producto_") . "." . $extensiones[$tipo];
                    $ruta = "uploads/" . $nombreFoto;

                    if (move_uploaded_file($foto["tmp_name"], $ruta)) {

                        $sql = "INSERT INTO productos
                        (nombre, precio, stock, foto_p, categoria_id)
                        VALUES (?, ?, ?, ?, ?)";

                        $stmt = $conexion->prepare($sql);
                        $stmt->bind_param(
                            "sdisi",
                            $nombre,
                            $precio,
                            $stock,
                            $nombreFoto,
                            $categoria);

                        if ($stmt->execute()) {
                            $mensaje = "Producto registrado correctamente.";
                        } else {
                            unlink($ruta);
                            $mensaje = "Error al registrar el producto.";}

                        $stmt->close();

                    } else {
                        $mensaje = "No se pudo guardar la fotografía.";}

                } else {
                    $mensaje = "Formato de imagen no permitido.";}

            } else {
                $mensaje = "La imagen no es válida o supera los 5 MB.";}

        } else {
            $mensaje = "Debes seleccionar una fotografía.";}

    } else {
        $mensaje = "Verifica los datos del formulario.";}
}

$categorias = $conexion->query("SELECT * FROM categorias ORDER BY descripcion");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar producto</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
<header class="encabezado">
    <div class="contenedor">
        <h1>Registrar producto</h1>
        <p>Agregar un nuevo producto al inventario</p>
    </div>
</header>

<main class="contenedor">
    <section class="bienvenida">
        <h2>Datos del producto</h2>
        <?php if ($mensaje != ""): ?>
            <p><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <p>Nombre:</p>
            <input type="text" name="nombre" maxlength="30" required>
            <p>Precio:</p>
            <input type="number" name="precio" step="0.01" min="0" required>
            <p>Stock:</p>
            <input type="number" name="stock" min="0" required>
            <p>Categoría:</p>
            <select name="categoria_id" required>
                <option value="">Selecciona una categoría</option>
                <?php while ($cat = $categorias->fetch_assoc()): ?>
                    <option value="<?php echo $cat['id_categoria']; ?>">
                        <?php echo htmlspecialchars($cat['descripcion']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <p>Fotografía:</p>
            <input type="file" name="foto_p"
                   accept="image/jpeg,image/png,image/gif" required>
            <br><br>
            <button type="submit" class="boton">
                Guardar producto
            </button>
            <a href="index.php" class="boton">Regresar</a>
        </form>
    </section>
</main>
</body>
</html>
