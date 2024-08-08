<?php
include 'db.php';
include 'header.php';

  $sql = "SELECT valor_dolar FROM configuracion WHERE id = 1";
  $result = $connection->query($sql);

  $dollarValue = "";
  if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $dollarValue = $row['valor_dolar'];
  }

    // Verificar si se han enviado fechas
    // Obtener las fechas enviadas desde el formulario
    // Consulta SQL para obtener las fechas de la tabla "fechas" con id=1
    $fechaQuery = "SELECT fechaInicio, fechaFin FROM fechas WHERE id = 1";
    $fechaResult = mysqli_query($connection, $fechaQuery);

    if ($fechaResult) {
        $fechas = mysqli_fetch_assoc($fechaResult);
        $fechaInicioTabla = $fechas['fechaInicio'];
        $fechaFinTabla = $fechas['fechaFin'];
    } else {
        // Manejo de errores si la consulta de fechas falla
        $cantidadRegistros = "Error al obtener las fechas de la tabla";
    }

    // Consulta SQL para contar registros entre las fechas de la tabla "fechas"
    // Consulta SQL para contar registros entre las fechas de la tabla "fechas"
    $query = "SELECT COUNT(*) AS cantidad_registros
    FROM invoice_products AS ip
    INNER JOIN invoice AS i ON ip.invoice_id = i.invoice_id
    WHERE i.fecha BETWEEN '$fechaInicioTabla' AND '$fechaFinTabla';";


    $result = mysqli_query($connection, $query);

    // Verificar si la consulta fue exitosa
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $cantidadRegistros = $row['cantidad_registros'];
    } else {
        // Manejo de errores si la consulta falla
        $cantidadRegistros = "Error en la consulta";
    }

    $sql = "SELECT
    ROUND(SUM(CASE WHEN ip.FORMA_PAGO = 'efectivo' AND ip.TIPO = 'servicio' THEN ip.SUBTOTAL ELSE 0 END), 2) AS ganancias_efectivo,
    ROUND(SUM(CASE WHEN ip.FORMA_PAGO = 'transferencia' AND ip.TIPO = 'servicio' THEN ip.SUBTOTAL ELSE 0 END), 2) AS ganancias_transferencia,
    ROUND(SUM(CASE WHEN ip.FORMA_PAGO = 'divisa' AND ip.TIPO = 'servicio' THEN ip.SUBTOTAL ELSE 0 END), 2) AS ganancias_divisa
    FROM empleados e
    JOIN invoice i ON e.id = i.EMPLEADO_ID
    JOIN invoice_products ip ON i.INVOICE_ID = ip.INVOICE_ID
    WHERE i.FECHA BETWEEN '$fechaInicioTabla' AND '$fechaFinTabla';";

    $result = $connection->query($sql);

    // Inicializar variables para almacenar las ganancias
    $ganancias_efectivo = 0.00;
    $ganancias_transferencia = 0.00;
    $ganancias_divisa = 0.00;

    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
  
      // Suma de las ganancias en bolívares
      $gananciasBolivares = $row['ganancias_efectivo'] + $row['ganancias_transferencia'];
  
      // Asignación de las ganancias en divisa
      $ganancias_divisa = $row['ganancias_divisa'];
      
      $total_facturado=$gananciasBolivares+$ganancias_divisa*$dollarValue;
  }
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="text-4xl">Starter Page</h1>
          </div><!-- /.col -->
          
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

  <!-- /.content-wrapper -->
            
<section class="content">
      <div class="container-fluid">
      <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-6">
                  <label for="fechaInicio">Fecha de Inicio:</label>
                  <input class="form-control" type="date" name="fechaInicio" id="fechaInicio" value="<?php echo $fechaInicioTabla; ?>">
                </div>
                <div class="col-lg-6">
                  <label for="fechaFin">Fecha de Fin:</label>
                  <input class="form-control" type="date" name="fechaFin" id="fechaFin" value="<?php echo $fechaFinTabla; ?>">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-lg-12">
                    <button class="btn btn-primary btn-block" id="guardarFecha">Guardar fecha</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-lg-12">
            <div id="resultadoReporte"></div>
        </div>
    </div>
        <div class="row">
            
          <div class="col-6 col-md-3">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3 style="text-align: center;"><?php echo $cantidadRegistros; ?></h3>
                <p style="text-align: center;">Servicios realizados</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-6 col-md-3">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3 style="text-align: center;">Bs. <?php echo $gananciasBolivares; ?></h3>
                <p style="text-align: center;">Ganancias en Bolivares.</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-6 col-md-3">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3 style="text-align: center;">$ <?php echo $ganancias_divisa; ?></h3>
                <p style="text-align: center;">Ganancias en Divisas</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-6 col-md-3">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3 style="text-align: center;">Bs. <?php echo $total_facturado; ?></h3>
                <p style="text-align: center;">Total</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->
        <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <section class="col-6 connectedSortable">
            <!-- Custom tabs (Charts with tabs)-->
            <div class="card">
              <div class="card-header">
                <h3 style="text-align: center;" class="card-title">
                  <i class="fas fa-chart-pie mr-1"></i>
                  Facturación por empleado
                </h3>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content p-0">
                  <!-- Morris chart - Sales -->
                  <div class="chart tab-pane active" id="revenue-chart">
                      <canvas id="revenue-chart-canvas" height="100%" style="height: 5vw;"></canvas>
                   </div>
                </div>
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
          </section>
          <section class="col-6 connectedSortable">
            <!-- Custom tabs (Charts with tabs)-->
            <div class="card">
              <div class="card-header">
                <h3 style="text-align: center;" class="card-title">
                  <i class="fas fa-chart-pie mr-1"></i>
                  Facturación por dia
                </h3>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content p-0">
                  <!-- Morris chart - Sales -->
                  <div class="chart tab-pane active" id="revenue-chart">
                      <canvas id="ganancias-chart" height="100%" style="height: 5vw;"></canvas>
                   </div>
                </div>
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
          </section>
    <!-- /.content -->
  </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
        $("#guardarFecha").click(function() {
            var fechaInicio = $("#fechaInicio").val(); // Obtener el valor de fechaInicio
            var fechaFin = $("#fechaFin").val(); // Obtener el valor de fechaFin
            
            // Realizar una solicitud AJAX para actualizar las fechas en la base de datos
            $.ajax({
                url: "guardar_fecha.php", // Archivo PHP que maneja la actualización de fechas
                type: "POST",
                data: { fechaInicio: fechaInicio, fechaFin: fechaFin },
                success: function(response) {
                    alert("Fechas guardadas correctamente.");
                },
                error: function() {
                    alert("Error al guardar las fechas.");
                }
            });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Envía una solicitud AJAX para obtener los datos del servidor
        fetch('obtener_datos_grafico.php')
            .then(response => response.json())
            .then(data => {
                // Procesa los datos recibidos para crear el gráfico de barras de ganancias por empleado
                const labelsEmpleado = data.ganancias_por_empleado.map(item => item.nombre_empleado);
                const valuesEmpleado = data.ganancias_por_empleado.map(item => item.total_bolivares);

                // Crea el gráfico de barras de ganancias por empleado
                const ctxEmpleado = document.getElementById('revenue-chart-canvas').getContext('2d');
                const barChartEmpleado = new Chart(ctxEmpleado, {
                    type: 'bar',
                    data: {
                        labels: labelsEmpleado,
                        datasets: [{
                            label: 'Total de Bolívares Facturados por Empleado',
                            data: valuesEmpleado,
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Procesa los datos recibidos para crear el gráfico de barras de ganancias por día de la semana
                const diasSemana = data.ganancias_por_dia.map(item => item.Dia_Semana);
                const ganancias = data.ganancias_por_dia.map(item => item.Ganancia_Bolivares);

                // Crea el gráfico de barras de ganancias por día de la semana
                const ctxGanancias = document.getElementById('ganancias-chart').getContext('2d');
                const barChartGanancias = new Chart(ctxGanancias, {
                    type: 'bar',
                    data: {
                        labels: diasSemana,
                        datasets: [{
                            label: 'Facturación por Día de la Semana (Bolívares)',
                            data: ganancias,
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error al obtener los datos del servidor:', error));
    });
</script>
