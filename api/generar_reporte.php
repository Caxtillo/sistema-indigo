<?php
include "db.php";

// Verificar la conexión
if ($connection->connect_error) {
    die("Conexión fallida: " . $connection->connect_error);
}

$fechaInicio = $_POST['fechaInicio'];
$fechaFin = $_POST['fechaFin'];

// Consulta SQL
$sql = "
SELECT
        e.nombre AS empleado,
        ip.FORMA_PAGO,
        ROUND(SUM(CASE WHEN ip.FORMA_PAGO = 'efectivo' AND ip.TIPO = 'servicio' THEN ip.SUBTOTAL ELSE 0 END), 2) AS efectivo,
        ROUND(SUM(CASE WHEN ip.FORMA_PAGO = 'transferencia' AND ip.TIPO = 'servicio' THEN ip.SUBTOTAL ELSE 0 END), 2) AS transferencia,
        ROUND(SUM(CASE WHEN ip.FORMA_PAGO = 'divisa' AND ip.TIPO = 'servicio' THEN ip.SUBTOTAL ELSE 0 END), 2) AS divisa,
        ROUND(SUM(CASE WHEN ip.FORMA_PAGO = 'efectivo' AND ip.TIPO = 'servicio' THEN ip.SUBTOTAL ELSE 0 END) * e.porcentaje, 2) AS porcentaje_efectivo,
        ROUND(SUM(CASE WHEN ip.FORMA_PAGO = 'transferencia' AND ip.TIPO = 'servicio' THEN ip.SUBTOTAL ELSE 0 END) * e.porcentaje, 2) AS porcentaje_transferencia,
        ROUND(
            SUM(CASE WHEN ip.FORMA_PAGO = 'divisa' AND ip.TIPO = 'servicio' THEN ip.SUBTOTAL ELSE 0 END) * e.porcentaje
            + SUM(CASE WHEN ip.TIPO = 'propina' AND ip.FORMA_PAGO = 'divisa' THEN ip.SUBTOTAL ELSE 0 END)
            , 2) AS porcentaje_divisa,
        COALESCE(SUM(CASE WHEN ip.TIPO = 'descuento' AND ip.FORMA_PAGO != 'divisa' THEN ip.SUBTOTAL ELSE 0 END), 0) AS descuentos,
        COALESCE(SUM(CASE WHEN ip.TIPO = 'descuento' AND ip.FORMA_PAGO = 'divisa' THEN ip.SUBTOTAL ELSE 0 END), 0) AS descuentos_divisa,
        COALESCE(SUM(CASE WHEN ip.TIPO = 'propina' AND ip.FORMA_PAGO != 'divisa' THEN ip.SUBTOTAL ELSE 0 END), 0) AS propinas,
        COALESCE(SUM(CASE WHEN ip.TIPO = 'propina' AND ip.FORMA_PAGO = 'divisa' THEN ip.SUBTOTAL ELSE 0 END), 0) AS propinas_divisa,
        ROUND(
            (COALESCE(SUM(CASE WHEN ip.FORMA_PAGO = 'transferencia' AND ip.TIPO = 'servicio' THEN ip.SUBTOTAL ELSE 0 END) * e.porcentaje, 0)
            - (COALESCE(SUM(CASE WHEN ip.TIPO = 'descuento' AND ip.FORMA_PAGO != 'divisa' THEN ip.SUBTOTAL ELSE 0 END), 0) ))
            + COALESCE(SUM(CASE WHEN ip.TIPO = 'propina' AND ip.FORMA_PAGO != 'divisa' THEN ip.SUBTOTAL ELSE 0 END), 0)
            , 2) AS total
    FROM empleados e
    JOIN invoice i ON e.id = i.EMPLEADO_ID
    JOIN invoice_products ip ON i.INVOICE_ID = ip.INVOICE_ID
    CROSS JOIN configuracion
    WHERE i.FECHA BETWEEN '$fechaInicio' AND '$fechaFin'
    GROUP BY e.id, ip.FORMA_PAGO;
";

$result = $connection->query($sql);

// Crear un arreglo asociativo para almacenar los datos por empleado y método de pago
$reporte = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $empleado = $row['empleado'];
        $efectivo = $row['efectivo'];
        $transferencia = $row['transferencia'];
        $divisa = $row['divisa'];
        $porcentaje_efectivo = $row['porcentaje_efectivo'];
        $porcentaje_transferencia = $row['porcentaje_transferencia'];
        $porcentaje_divisa = $row['porcentaje_divisa'];
        $descuentos = $row['descuentos'];
        $descuentos_divisa = $row['descuentos_divisa'];
        $propinas = $row['propinas'];
        $propinas_divisa = $row['propinas_divisa'];
        $total = $row['total'];

        // Restar los descuentos en divisa al porcentaje de pago en divisa
        $porcentaje_divisa -= $descuentos_divisa;

        if (!isset($reporte[$empleado])) {
            $reporte[$empleado] = array(
                'efectivo' => 0.00,
                'transferencia' => 0.00,
                'divisa' => 0.00,
                'porcentaje_efectivo' => 0.00,
                'porcentaje_transferencia' => 0.00,
                'porcentaje_divisa' => 0.00,
                'descuentos' => 0.00,
                'descuentos_divisa' => 0.00,
                'propinas' => 0.00,
                'propinas_divisa' => 0.00,
                'total' => 0.00,
            );
        }

        $reporte[$empleado]['efectivo'] += $efectivo;
        $reporte[$empleado]['transferencia'] += $transferencia;
        $reporte[$empleado]['divisa'] += $divisa;
        $reporte[$empleado]['porcentaje_efectivo'] += $porcentaje_efectivo;
        $reporte[$empleado]['porcentaje_transferencia'] += $porcentaje_transferencia;
        $reporte[$empleado]['porcentaje_divisa'] += $porcentaje_divisa;
        $reporte[$empleado]['descuentos'] += $descuentos;
        $reporte[$empleado]['descuentos_divisa'] += $descuentos_divisa;
        $reporte[$empleado]['propinas'] += $propinas;
        $reporte[$empleado]['propinas_divisa'] += $propinas_divisa;
        $reporte[$empleado]['total'] += $total;
    }

    // Consultar nombres de empleados y mostrar el reporte
    foreach ($reporte as $empleado => $datos) {
        echo '
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h2 class="mb-0">' . $empleado . '</h2>
                    </div>
                    <div class="card-body">';
        
        // Tabla 1: Efectivo, Transferencia, Divisa
        echo '<div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 33%;">Efectivo</th>
                            <th style="width: 33%;">Transferencia</th>
                            <th style="width: 33%;">Divisa</th>
                        </tr>
                    </thead>
                    <tbody>';
        echo '<tr>';
        echo '<td>BS. ' . number_format($datos['efectivo'], 2) . '</td>';
        echo '<td>BS. ' . number_format($datos['transferencia'], 2) . '</td>';
        echo '<td>$ ' . number_format($datos['divisa'], 2) . '</td>';
        echo '</tr>';
        echo '</tbody>
                </table>
            </div>';
        
        // Tabla 2: Porcentajes
        echo '<div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 33%;">%Pago Efectivo</th>
                            <th style="width: 33%;">%Pago Transferencia</th>
                            <th style="width: 33%;">%Pago Divisa</th>
                        </tr>
                    </thead>
                    <tbody>';
        echo '<tr>';
        echo '<td>BS. ' . number_format($datos['porcentaje_efectivo'], 2) . '</td>';
        echo '<td>BS. ' . number_format($datos['porcentaje_transferencia'], 2) . '</td>';
        echo '<td>$ ' . number_format($datos['porcentaje_divisa'], 2) . '</td>';
        echo '</tr>';
        echo '</tbody>
                </table>
            </div>';
        
        // Tabla 3: Descuentos y Total

        // Consulta para obtener el valor del dólar
        $sql_dolar = "SELECT valor_dolar FROM configuracion WHERE id = 1";
        $result_dolar = $connection->query($sql_dolar);
        $row_dolar = $result_dolar->fetch_assoc();
        $valor_dolar = $row_dolar['valor_dolar'];
        $totalDescuentos = $datos['descuentos'] + $valor_dolar;
        $totalTrans = $datos['total'] - $valor_dolar;

        echo '<div class="table-responsive">';
        echo '<table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 33%;">Prop. / Prop. $</th>
                        <th style="width: 33%;">Desc. / Desc. $</th>
                        <th style="color: green;">Total a transferir</th>
                    </tr>
                </thead>
                <tbody>';
        echo '<tr>';
        echo '<td>BS. ' . number_format($datos['propinas'], 2) .' / $ ' . number_format($datos['propinas_divisa'], 2) . '</td>';
        echo '<td>BS. ' . number_format($totalDescuentos, 2) . ' / $ ' . number_format($datos['descuentos_divisa'], 2) . '</td>';
        echo '<td style="color: green;">BS. ' . number_format($totalTrans, 2) . '</td>';
        echo '</tr>';
        echo '</tbody>
            </table></div>'; // Cerrar table-responsive
   
        // Tabla de Desglose de Descuentos
echo '<div class="table-responsive">';
echo '<h3>Desglose de Descuentos:</h3>';
echo '<table class="table table-bordered">
        <thead>
            <tr>
                <th>Concepto</th>
                <th>Monto</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>';

// Consulta para obtener los descuentos aplicados al empleado
$sql_descuentos = "SELECT ip.descripcion AS concepto, ip.SUBTOTAL AS monto, i.FECHA AS fecha, ip.FORMA_PAGO AS forma_pago
                    FROM invoice_products ip
                    JOIN invoice i ON ip.INVOICE_ID = i.INVOICE_ID
                    JOIN empleados e ON i.EMPLEADO_ID = e.id
                    WHERE e.nombre = '$empleado' 
                    AND (ip.TIPO = 'descuento' OR (ip.TIPO = 'internet' AND ip.descripcion = 'Internet'))
                    AND i.FECHA BETWEEN '$fechaInicio' AND '$fechaFin'";

$result_descuentos = $connection->query($sql_descuentos);

echo '<tr>
        <td>Internet</td>
        <td>BS. ' . $valor_dolar . '</td>
        <td>Fijo</td>
      </tr>';

if ($result_descuentos->num_rows > 0) {
    while ($row_descuento = $result_descuentos->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row_descuento['concepto'] . '</td>';

        // Verificar la forma de pago y mostrar el símbolo correspondiente
        if ($row_descuento['forma_pago'] == 'divisa') {
            echo '<td>$ ' . number_format($row_descuento['monto'], 2) . '</td>';
            // Restar el descuento en divisa al porcentaje de pago en divisa
            $porcentaje_divisa -= floatval($row_descuento['monto']);
        } else {
            echo '<td>BS. ' . number_format($row_descuento['monto'], 2) . '</td>';
        }

        echo '<td>' . $row_descuento['fecha'] . '</td>';
        echo '</tr>';
    }
}

echo '</tbody>
      </table></div>'; // Cerrar table-responsive

echo    '</div>'; // Cerrar card-body
echo '</div>'; // Cerrar card

echo '</div></div>'; // Cerrar col, row, container-fluid
    }
}

$sql = "SELECT
    ROUND(SUM(CASE WHEN ip.FORMA_PAGO = 'efectivo' AND ip.TIPO = 'servicio' THEN ip.SUBTOTAL * 0.4 ELSE 0 END), 2) AS ganancias_efectivo,
    ROUND(SUM(CASE WHEN ip.FORMA_PAGO = 'transferencia' AND ip.TIPO = 'servicio' THEN ip.SUBTOTAL * 0.4 ELSE 0 END), 2) AS ganancias_transferencia,
    ROUND(SUM(CASE WHEN ip.FORMA_PAGO = 'divisa' AND ip.TIPO = 'servicio' THEN ip.SUBTOTAL * 0.4 ELSE 0 END), 2) AS ganancias_divisa
FROM empleados e
JOIN invoice i ON e.id = i.EMPLEADO_ID
JOIN invoice_products ip ON i.INVOICE_ID = ip.INVOICE_ID
WHERE i.FECHA BETWEEN '$fechaInicio' AND '$fechaFin'";

$result = $connection->query($sql);

// Inicializar variables para almacenar las ganancias
$ganancias_efectivo = 0.00;
$ganancias_transferencia = 0.00;
$ganancias_divisa = 0.00;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $ganancias_efectivo = $row['ganancias_efectivo'];
    $ganancias_transferencia = $row['ganancias_transferencia'];
    $ganancias_divisa = $row['ganancias_divisa'];
}
$connection->close();
?>
                  
<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-header">
                <h2 class='mb-0'>INDIGO</h2>
            </div>  
        <div class="card-body">
        <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="width: 33%;">Efectivo</th>
                    <th style="width: 33%;">Transferencia</th>
                    <th style="width: 33%;">Divisa</th>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td>BS. <?php echo number_format($ganancias_efectivo, 2); ?></td>
                <td>BS. <?php echo number_format($ganancias_transferencia, 2); ?></td>
                <td>$ <?php echo number_format($ganancias_divisa, 2); ?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
