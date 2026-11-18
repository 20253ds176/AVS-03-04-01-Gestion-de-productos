<?php
require_once 'config.php';

//total de productos
$resultado = $conexion->query("SELECT COUNT(*) AS total FROM productos");
$totalProductos = $resultado->fetch_assoc()['total'];
//total de categorías
$resultadoCategorias = $conexion->query("SELECT COUNT(*) AS total FROM categorias");
$totalCategorias = $resultadoCategorias->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de productos</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

    <header class="encabezado">
        <div class="contenedor">
            <h1>Gestión de productos</h1>
            <p>Sistema de administración de inventario</p>
        </div>
    </header>

    <main class="contenedor">
        <section class="bienvenida">
            <h2>Panel principal</h2>
            <p>Bienvenido al sistema de gestión de productos.</p>
        </section>
        <section class="estadisticas">
            <div class="tarjeta">
                <h3>Total de productos</h3>
                <p class="numero"><?php echo $totalProductos; ?></p>
            </div>
            <div class="tarjeta">
                <h3>Categorías registradas</h3>
                <p class="numero"><?php echo $totalCategorias; ?></p>
            </div>
        </section>
        <section class="acciones">
            <h2>Administración</h2>
            <div class="menu">
                <a href="create.php" class="boton">+ Registrar producto</a>
                <a href="read.php" class="boton">Consultar productos</a>
            </div>
        </section>
    </main>

    <footer>
        <p>AVS.03.04.01 - Karen Salgado Castro</p>
    </footer>
</body>
</html>