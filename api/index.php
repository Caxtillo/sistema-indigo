<?php
include 'db.php';
include 'header.php';

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



?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Starter Page</h1>
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
                  <input class="form-control" type="date" name="fechaInicio" id="fechaInicio">
                </div>
                <div class="col-lg-6">
                  <label for="fechaFin">Fecha de Fin:</label>
                  <input class="form-control" type="date" name="fechaFin" id="fechaFin">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-lg-12">
                    <button class="btn btn-primary btn-block" id="generarReporte">Generar Reporte</button>
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
                <h3><?php echo $cantidadRegistros; ?></h3>
                <p>Servicios realizados</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-6 col-md-3">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3>53</h3>
                <p>Ganancias en Bolivares.</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-6 col-md-3">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>44</h3>
                <p>Ganancias en Divisas</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-6 col-md-3">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>65</h3>
                <p>Total Facturado</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->
        <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <section class="col-12 connectedSortable">
            <!-- Custom tabs (Charts with tabs)-->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-chart-pie mr-1"></i>
                  Sales
                </h3>
                <div class="card-tools">
                  <ul class="nav nav-pills ml-auto">
                    <li class="nav-item">
                      <a class="nav-link active" href="#revenue-chart" data-toggle="tab">Area</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="#sales-chart" data-toggle="tab">Donut</a>
                    </li>
                  </ul>
                </div>
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
    <!-- /.content -->
  </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $("#generarReporte").click(function() {
            var fechaInicio = $("#fechaInicio").val(); // Obtener el valor de fechaInicio
            var fechaFin = $("#fechaFin").val(); // Obtener el valor de fechaFin
            
            // Realizar una solicitud AJAX a la misma página (index.php)
            $.ajax({
                url: "index.php", // Cambia "index.php" si es necesario
                type: "POST",
                data: { fechaInicio: fechaInicio, fechaFin: fechaFin },
                success: function(response) {
                    $("#resultadoReporte").html(response); // Actualizar el contenido en "#resultadoReporte"
                },
                error: function() {
                    alert("Error al generar el reporte.");
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
                    // Procesa los datos recibidos para crear el gráfico de barras
                    const labels = data.map(item => item.nombre_empleado);
                    const values = data.map(item => item.total_bolivares);

                    // Crea el gráfico de barras
                    const ctx = document.getElementById('revenue-chart-canvas').getContext('2d');
                    const barChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Total de Bolívares Generados por Empleado',
                                data: values,
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
<?php
include 'footer.php';
?>
