<?php
include "db.php";
include 'check_session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Verificar si se envió un nuevo valor del dólar
  if (isset($_POST['dollarValue']) && is_numeric($_POST['dollarValue'])) {
      $newDollarValue = $_POST['dollarValue'];

      // Actualizar el valor del dólar en la base de datos
      $sqlUpdate = "UPDATE configuracion SET valor_dolar = $newDollarValue WHERE id = 1";
      $result = $connection->query($sqlUpdate);

      if ($result) {
          // Redirigir a la misma página para actualizar el valor mostrado
          header("Location: index.php");

          exit(); // Asegúrate de salir después de la redirección
      }
  }
}


// Consulta SQL para obtener el valor del dólar en bolívares
$sql = "SELECT valor_dolar FROM configuracion WHERE id = 1";
$result = $connection->query($sql);

$dollarValue = "";
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $dollarValue = $row['valor_dolar'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sistema | Indigo</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="./output.css">
  <link rel="stylesheet" href="dist/css/adminlte.css">
  
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- barra superior -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
  </nav>
  <!-- manu lateral -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!--Logo -->
    <a href="index.php" class="brand-link">
      <center><img src="indigo2.png" alt="Indigo" width="200px" ></center>
      <span class="brand-text font-weight-light"></span>
    </a>
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block ">Administrador</a>
        </div>
      </div>
      <!-- SidebarSearch Form -->
      <form method="post" class="dollar-form">

      <div class="form-inline">
        <div class="input-group">
          <input class="form-control form-control-sidebar" type="text" placeholder="Search" aria-label="Search" style="font-size:1.2em" name="dollarValue" value="<?php echo $dollarValue; ?>">
          <div class="input-group-append">
          </div>
        </div>
        <button type="submit" class="form-control form-control-sidebar btn btn-sidebar" style="width:50%">
        <p> Guardar</p>
        </button>
        <button id="actualizarDolarBtn" type="button" class="form-control form-control-sidebar nav-icon btn btn-sidebar" style="width:50%">
        <p> Actualizar</p>
        </button>
      </div>
      </form>

      <!-- Sidebar Menu -->
      <div class="sidebar">
    <!-- Enlaces de navegación -->
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
        <li class="nav-item">
            <a href="index.php" class="nav-link">
                <i class="nav-icon fas fa-home"></i>
                <p>Inicio</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="facturacion.php" class="nav-link">
                <i class="nav-icon fas fa-file-invoice"></i>
                <p>Facturación</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="crud_factura.php?action=list&table=invoice" class="nav-link">
                <i class="nav-icon fas fa-receipt"></i>
                <p>Facturas</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="crud.php?action=list&table=servicios" class="nav-link">
                <i class="nav-icon fas fa-tools"></i>
                <p>Servicios</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="crud.php?action=list&table=productos" class="nav-link">
                <i class="nav-icon fas fa-boxes"></i>
                <p>Inventario</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="crud.php?action=list&table=empleados" class="nav-link">
                <i class="nav-icon fas fa-users"></i>
                <p>Empleados</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="crud.php?action=list&table=clientes" class="nav-link">
                <i class="nav-icon fas fa-user"></i>
                <p>Clientes</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="crud.php?action=list&table=descuentosempleado" class="nav-link">
                <i class="nav-icon fas fa-percent"></i>
                <p>Descuentos</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="reporte.php?action=list&table=reportes" class="nav-link">
                <i class="nav-icon fas fa-chart-bar"></i>
                <p>Reportes</p>
            </a>
        </li>
        <li class="nav-item"></li>
            <a class="nav-link"><iframe width="219" height="302" src="http://calculator-1.com/outdoor/?f=343a40&r=c2c7d0" scrolling="no" frameborder="0"></iframe><br /><a href="https://calculator-1.com/"></a>
            </a>
        </li>
    </ul>

</div>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>



  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
    <div class="p-3">
      <h5>Title</h5>
      <p>Sidebar content</p>
    </div>
  </aside>
<!-- /.control-sidebar -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('actualizarDolarBtn').addEventListener('click', function() {
        // Envía una solicitud AJAX al archivo PHP para actualizar el valor del dólar
        fetch('updateUsd.php')
            .then(response => {
                if (response.ok) {
                    // Redirige al usuario a index.php después de la actualización
                    window.location.href = 'index.php';
                } else {
                    console.error('Error al actualizar el valor del dólar:', response.statusText);
                }
            })
            .catch(error => {
                console.error('Error al actualizar el valor del dólar:', error);
            });
    });
});
</script>

<?php
include 'footer.php';
?>