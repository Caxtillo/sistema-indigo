<?php
include "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si se envió un nuevo valor del dólar
    if (isset($_POST['dollarValue']) && is_numeric($_POST['dollarValue'])) {
        $newDollarValue = $_POST['dollarValue'];

        // Actualizar el valor del dólar en la base de datos
        $sqlUpdate = "UPDATE configuracion SET valor_dolar = $newDollarValue WHERE id = 1";
        $result = $connection->query($sqlUpdate);

        if ($result) {
            // Redirigir a la misma página para actualizar el valor mostrado
            header("Location: index.php");

            exit();
        }
    }
}

// Consulta SQL para obtener el valor del dólar en bolívares
$sql = "SELECT valor_dolar FROM configuracion WHERE id = 1";
$result = $connection->query($sql);

$dollarValue = "";
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $dollarValue = $row['valor_dolar'];
}
?>

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
        <img src="indigo.png" alt="Logo del Salón de Belleza">
    </header>
    <script>
    const dollarValue = <?php echo $dollarValue; ?>;
    </script>
    <div class="menu-toggle" id="mobile-menu"><nav>
    <ul class="mobile-nav">
            <li><a href="index.php">Inicio</a></li>
            <li><a href="facturacion.php">Facturación</a></li>
            <li><a href="crud_factura.php?action=list&table=invoice">Facturas</a></li>
            <li><a href="crud.php?action=list&table=servicios">Servicios</a></li>
            <li><a href="crud.php?action=list&table=productos">Inventario</a></li>
            <li><a href="crud.php?action=list&table=empleados">Empleados</a></li>
            <li><a href="crud.php?action=list&table=clientes">Clientes</a></li>
            <li><a href="crud.php?action=list&table=descuentosempleado">Descuentos</a></li>
            <li><a href="reporte.php?action=list&table=reportes">Reportes</a></li>
            <!-- Agregar otros enlaces -->
            <li>
                <form method="post" class="dollar-form">
                    <input type="text" class="dollar-input" name="dollarValue" value="<?php echo $dollarValue; ?>">
                    <button type="submit" class="dollar-button">Guardar</button>
                </form>
            </li>
        </ul>
    </nav>
    </div>
    <!-- Resto del contenido de la página -->
    
</body>
</html>
