<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border: none;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }
        .card-header {
            background-color: #007bff;
            color: white;
            border-radius: 0.5rem 0.5rem 0 0;
        }
        .table {
            margin-bottom: 0;
        }
        .table th {
            background-color: #f1f3f5;
        }
        .total {
            font-weight: bold;
            color: #28a745;
        }
    </style>
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
    
    ?>
    <?php foreach ($reporte as $empleado => $datos): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h2 class="mb-0"><i class="fas fa-user me-2"></i><?php echo $empleado; ?></h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Ventas por Método de Pago</h5>
                                <table class="table table-striped">
                                    <tr>
                                        <th><i class="fas fa-money-bill-wave me-2"></i>Efectivo</th>
                                        <td>BS. <?php echo number_format($datos['efectivo'], 2); ?></td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-exchange-alt me-2"></i>Transferencia</th>
                                        <td>BS. <?php echo number_format($datos['transferencia'], 2); ?></td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-dollar-sign me-2"></i>Divisa</th>
                                        <td>$ <?php echo number_format($datos['divisa'], 2); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Porcentajes de Pago</h5>
                                <table class="table table-striped">
                                    <tr>
                                        <th><i class="fas fa-percentage me-2"></i>Efectivo</th>
                                        <td>BS. <?php echo number_format($datos['porcentaje_efectivo'], 2); ?></td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-percentage me-2"></i>Transferencia</th>
                                        <td>BS. <?php echo number_format($datos['porcentaje_transferencia'], 2); ?></td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-percentage me-2"></i>Divisa</th>
                                        <td>$ <?php echo number_format($datos['porcentaje_divisa'], 2); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                                <?php
                                $sql_dolar = "SELECT valor_dolar FROM configuracion WHERE id = 1";
                                $result_dolar = $connection->query($sql_dolar);
                                $row_dolar = $result_dolar->fetch_assoc();
                                $valor_dolar = $row_dolar['valor_dolar'];
                                $totalDescuentos = $datos['descuentos'] + $valor_dolar;
                                $totalTrans = $datos['total'] - $valor_dolar;
                                ?>

                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Resumen</h5>
                                <table class="table table-striped">
                                    <tr>
                                        <th><i class="fas fa-gift me-2"></i>Propinas</th>
                                        <td>BS. <?php echo number_format($datos['propinas'], 2); ?> / $ <?php echo number_format($datos['propinas_divisa'], 2); ?></td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-minus-circle me-2"></i>Descuentos</th>
                                        <td>BS. <?php echo number_format($datos['descuentos'], 2); ?> / $ <?php echo number_format($datos['descuentos_divisa'], 2); ?></td>
                                    </tr>
                                    <tr class="total">
                                        <th><i class="fas fa-calculator me-2"></i>Total a transferir</th>
                                        <td>BS. <?php echo number_format($datos['total'], 2); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Descuentos</h5>
                                <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Concepto</th>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><tr>
                                    <td>Internet</td>
                                    <td>BS. <?php echo number_format($valor_dolar, 2); ?></td>
                                    <td>Fijo</td>
                                </tr>
                                <?php
                                // Consulta para obtener los descuentos aplicados al empleado
                                $sql_descuentos = "SELECT ip.descripcion AS concepto, ip.SUBTOTAL AS monto, i.FECHA AS fecha, ip.FORMA_PAGO AS forma_pago
                                                    FROM invoice_products ip
                                                    JOIN invoice i ON ip.INVOICE_ID = i.INVOICE_ID
                                                    JOIN empleados e ON i.EMPLEADO_ID = e.id
                                                    WHERE e.nombre = '$empleado' 
                                                    AND (ip.TIPO = 'descuento' OR (ip.TIPO = 'internet' AND ip.descripcion = 'Internet'))
                                                    AND i.FECHA BETWEEN '$fechaInicio' AND '$fechaFin'";

                                $result_descuentos = $connection->query($sql_descuentos);

                                while ($row_descuento = $result_descuentos->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row_descuento['concepto']; ?></td>
                                    <td><?php echo ($row_descuento['forma_pago'] == 'divisa' ? '$ ' : 'BS. ') . number_format($row_descuento['monto'], 2); ?></td>
                                    <td><?php echo $row_descuento['fecha']; ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- INDIGO section -->
        <?php
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

        <div class="card mt-5">
            <div class="card-header">
                <h2 class="mb-0"><i class="fas fa-building me-2"></i>INDIGO</h2>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <div class="mt-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Ganancias por Método de Pago</h5>
                                <table class="table table-striped">
                                    <tr>
                                        <th><i class="fas fa-money-bill-wave me-2"></i>Efectivo</th>
                                        <td>BS. <?php echo number_format($ganancias_efectivo, 2); ?></td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-exchange-alt me-2"></i>Transferencia</th>
                                        <td>BS. <?php echo number_format($ganancias_transferencia, 2); ?></td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-dollar-sign me-2"></i>Divisa</th>
                                        <td>$ <?php echo number_format($ganancias_divisa, 2); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
                                   
<?php

}

