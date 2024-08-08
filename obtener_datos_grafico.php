<?php
// Conexión a la base de datos
include 'db.php';

// Verificar conexión
if ($connection->connect_error) {
    die("Error de conexión: " . $connection->connect_error);
}

// Consulta SQL para obtener los datos de ganancias por día de la semana
$sql_ganancias = "SELECT
                    CASE DAYNAME(FECHA)
                        WHEN 'Saturday' THEN 'Sábado'
                        WHEN 'Monday' THEN 'Lunes'
                        WHEN 'Tuesday' THEN 'Martes'
                        WHEN 'Wednesday' THEN 'Miércoles'
                        WHEN 'Thursday' THEN 'Jueves'
                        WHEN 'Friday' THEN 'Viernes'
                    END AS Dia_Semana,
                    SUM(SUBTOTAL) AS Ganancia_Bolivares
                    FROM invoice
                    JOIN invoice_products ON invoice.INVOICE_ID = invoice_products.INVOICE_ID
                    WHERE DAYOFWEEK(FECHA) BETWEEN 2 AND 6 -- De lunes a viernes (1 es domingo)
                    GROUP BY Dia_Semana
                    ORDER BY Dia_Semana ASC;";

// Ejecutar consulta de ganancias por día de la semana
$result_ganancias = $connection->query($sql_ganancias);

// Crear un array para almacenar los resultados de ganancias por día de la semana
$data_ganancias = array();

if ($result_ganancias->num_rows > 0) {
    // Obtener los datos de cada fila y almacenarlos en el array
    while($row_ganancias = $result_ganancias->fetch_assoc()) {
        $data_ganancias[] = $row_ganancias;
    }
}

// Consulta SQL para obtener los datos de ganancias por empleado
$sql_empleados = "SELECT
                        e.nombre AS nombre_empleado,
                        SUM(ip.SUBTOTAL) AS total_bolivares
                        FROM
                        invoice i
                        JOIN
                        invoice_products ip ON i.INVOICE_ID = ip.INVOICE_ID
                        JOIN
                        empleados e ON i.EMPLEADO_ID = e.id
                        JOIN
                        fechas f ON f.id = 1
                        WHERE
                        ip.TIPO = 'servicio'
                        AND i.FECHA BETWEEN f.fechaInicio AND f.fechaFin
                        GROUP BY
                        e.nombre
                        ORDER BY
                        total_bolivares DESC";

// Ejecutar consulta de ganancias por empleado
$result_empleados = $connection->query($sql_empleados);

// Crear un array para almacenar los resultados de ganancias por empleado
$data_empleados = array();

if ($result_empleados->num_rows > 0) {
    // Obtener los datos de cada fila y almacenarlos en el array
    while($row_empleados = $result_empleados->fetch_assoc()) {
        $data_empleados[] = $row_empleados;
    }
}

// Combinar los datos de ganancias por día de la semana y por empleado en un solo array
$data_combined = array(
    "ganancias_por_dia" => $data_ganancias,
    "ganancias_por_empleado" => $data_empleados
);

// Devolver los datos combinados como JSON
echo json_encode($data_combined);

// Cerrar conexión
$connection->close();
?>
