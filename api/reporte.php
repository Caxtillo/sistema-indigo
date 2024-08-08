<?php
include 'header.php';
include 'db.php';

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
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Generador de reportes</h1>
                </div><!-- /.col -->

            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Indique la fecha de incio y fin del reporte</h3>
                    </div>
                    <div class="card-body">
                        <!-- Agrega los campos de fecha -->
                        <div class="form-group">
                            <label for="fechaInicio">Fecha de Inicio:</label>
                            <input class="form-control" type="date" name="fechaInicio" id="fechaInicio" value="<?php echo $fechaInicioTabla; ?>">
                        </div>
                        <div class="form-group">
                            <label for="fechaFin">Fecha de Fin:</label>
                            <input class="form-control" type="date" name="fechaFin" id="fechaFin" value="<?php echo $fechaFinTabla; ?>">
                        </div>
                        <button class="btn btn-primary" id="generarReporte">Generar Reporte</button>
                    </div>
                </div>
                <div id="resultadoReporte"></div>
            </div>
        </div>
    </div>
</section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/adminlte@3.1.0/dist/js/adminlte.min.js"></script>

<script>
$(document).ready(function() {
    $("#generarReporte").click(function() {
        var fechaInicio = $("#fechaInicio").val();
        var fechaFin = $("#fechaFin").val();

        $.ajax({
            url: "generar_reporte.php",
            type: "POST",
            data: { fechaInicio: fechaInicio, fechaFin: fechaFin },
            success: function(response) {
                $("#resultadoReporte").html(response);
            },
            error: function() {
                $("#resultadoReporte").html("<p>Error al generar el reporte.</p>");
            }
        });
    });
});
</script>
</div>

