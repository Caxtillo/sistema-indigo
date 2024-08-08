<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Salón de Belleza</title>
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">

</head>
<body>
    <header>
        <h1>Sistema de Salón de Belleza</h1>
    </header>
    
    <nav>
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="crud.php?action=list&table=servicios">Servicios</a></li>
            <li><a href="crud.php?action=list&table=empleados">Empleados</a></li>
            <li><a href="#">Clientes</a></li>
            <li><a href="#">Descuentos</a></li>
            <li><a href="#">Reportes</a></li>
        </ul>
    </nav>
    
    <div class="contenido">
        <?php echo $contenido; ?>
    </div>
    
    <footer>
        <p>&copy; 2023 Salón de Belleza XYZ</p>
    </footer>
</body>
</html>
