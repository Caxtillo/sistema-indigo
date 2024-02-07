<?php
include 'db.php';

// Realizar la consulta para obtener los servicios
$query = "SELECT id, descripcion,precio FROM servicios";
$result = mysqli_query($connection, $query);

// Verificar si se obtuvieron resultados
if ($result) {
    $servicios = array();

    // Recorrer los resultados y almacenarlos en un arreglo
    while ($row = mysqli_fetch_assoc($result)) {
        $servicios[] = $row;
    }

    // Devolver los resultados en formato JSON
    echo json_encode($servicios);
} else {
    echo 'Error en la consulta: ' . mysqli_error($connection);
}

// Cerrar la conexión
mysqli_close($connection);
?>
