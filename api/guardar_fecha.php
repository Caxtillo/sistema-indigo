<?php
include 'db.php';

// Obtener las fechas enviadas desde el formulario
$fechaInicio = $_POST['fechaInicio'];
$fechaFin = $_POST['fechaFin'];

// Consulta SQL para actualizar las fechas en la tabla de configuración
$query = "UPDATE fechas SET fechaInicio = '$fechaInicio', fechaFin = '$fechaFin' WHERE id = 1";

// Ejecutar la consulta
if (mysqli_query($connection, $query)) {
    echo "Fechas actualizadas correctamente.";
} else {
    echo "Error al actualizar las fechas: " . mysqli_error($connection);
}

// Cerrar conexión
mysqli_close($connection);
?>