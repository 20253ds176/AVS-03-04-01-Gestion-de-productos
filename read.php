<?php
require_once 'config.php';

$sql = "SELECT p.*, c.descripcion AS categoria
        FROM productos p
        INNER JOIN categorias c
        ON p.categoria_id = c.id_categoria
        ORDER BY p.id_producto DESC";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar productos</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

<header class="encabezado">
    <div class="contenedor">
        <h1>Productos registrados</h1>
        <p>Consulta y administra tu inventario</p>
    </div>
</header>

<main class="contenedor">
    <section class="bienvenida">
        <h2>Listado de productos</h2>
        <a href="create.php" class="boton">Agregar producto</a>
        <a href="index.php" class="boton">Regresar</a>
    </section>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Fotografía</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Categoría</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
        <?php if ($resultado->num_rows > 0): ?>
            <?php while ($producto = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $producto['id_producto']; ?></td>
                    <td>
                        <?php
                        $foto = 'uploads/' . basename($producto['foto_p']);
                        if (!empty($producto['foto_p']) && file_exists($foto)):?>
                            <img src="<?php echo htmlspecialchars($foto); ?>"
                                 class="foto-producto"
                                 alt="Fotografía del producto">
                        <?php else: ?>
                            Sin fotografía
                        <?php endif; ?></td>
                    <td>
                        <?php echo htmlspecialchars($producto['nombre']); ?></td>
                    <td>
                        $<?php echo number_format($producto['precio'], 2); ?></td>
                    <td><?php echo $producto['stock']; ?></td>
                    <td>
                        <?php echo htmlspecialchars($producto['categoria']); ?></td>
                    <td>
                        <a href="update.php?id=<?php echo $producto['id_producto']; ?>">
                            Editar</a>
                        <a href="delete.php?id=<?php echo $producto['id_producto']; ?>">
                            Eliminar</a>
                    </td>
                </tr>

            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">No hay productos registrados.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</main>
</body>
</html>