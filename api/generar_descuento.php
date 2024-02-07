<?php
// Conexión a la base de datos y otras configuraciones

$empleado = $_POST['empleado'];
$fechaInicio = $_POST['fechaInicio'];
$fechaFin = $_POST['fechaFin'];

// Consulta SQL para obtener los descuentos del empleado en el rango de fechas
$sqlDescuentos = "SELECT concepto, monto FROM descuentos WHERE empleado = :empleado AND fecha BETWEEN :fechaInicio AND :fechaFin";

// Preparar la consulta
$stmt = $pdo->prepare($sqlDescuentos);
$stmt->bindParam(':empleado', $empleado, PDO::PARAM_INT);
$stmt->bindParam(':fechaInicio', $fechaInicio, PDO::PARAM_STR);
$stmt->bindParam(':fechaFin', $fechaFin, PDO::PARAM_STR);

// Ejecutar la consulta
$stmt->execute();

// Obtener los resultados
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mostrar los resultados en una lista
if ($resultados) {
    echo '<h2>Descuentos</h2>';
    echo '<ul>';
    foreach ($resultados as $descuento) {
        echo '<li>' . $descuento['concepto'] . ': $' . $descuento['monto'] . '</li>';
    }
    echo '</ul>';
}
?>
