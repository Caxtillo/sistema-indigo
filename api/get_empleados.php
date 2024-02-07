<?php
include 'db.php';

// Realizar la consulta para obtener los nombres de los clientes
$query = "SELECT id, nombre FROM empleados";
$result = mysqli_query($connection, $query);

// Verificar si se obtuvieron resultados
if ($result) {
    $empleados = array();

    // Recorrer los resultados y almacenarlos en un arreglo
    while ($row = mysqli_fetch_assoc($result)) {
        $empleados[] = $row;
    }

    // Devolver los resultados en formato JSON
    echo json_encode($empleados);
} else {
    echo 'Error en la consulta: ' . mysqli_error($connection);
}

// Cerrar la conexión
mysqli_close($connection);
?>
