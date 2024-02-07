<?php
// Conexión a la base de datos
include 'db.php';

// Verificar conexión
if ($connection->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consulta SQL para obtener los datos
$sql = "SELECT
            e.nombre AS nombre_empleado,
            SUM(ip.SUBTOTAL) AS total_bolivares
        FROM
            invoice i
        JOIN
            invoice_products ip ON i.INVOICE_ID = ip.INVOICE_ID
        JOIN
            empleados e ON i.EMPLEADO_ID = e.id
        WHERE
            ip.TIPO = 'servicio'
        GROUP BY
            e.nombre
        ORDER BY
            total_bolivares DESC";

$result = $connection->query($sql);

// Crear un array para almacenar los resultados
$data = array();

if ($result->num_rows > 0) {
    // Obtener los datos de cada fila y almacenarlos en el array
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Devolver los datos como JSON
echo json_encode($data);

// Cerrar conexión
$connection->close();
?>
