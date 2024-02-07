<?php
include 'db.php';

$contenido = '
<div class="container contenido">
    <h2 class="mb-4">Agregar Empleado</h2>
    <form method="POST">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" class="form-control" name="nombre" required>
        </div>
        <div class="form-group">
            <label for="especialidad">Especialidad:</label>
            <input type="text" class="form-control" name="especialidad" required>
        </div>
        <button type="submit" class="btn btn-primary">Agregar</button>
    </form>
    <br>
    <button class="btn btn-secondary" onclick="window.history.back();">Volver</button>
</div>
';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $especialidad = $_POST['especialidad'];

    $query = "INSERT INTO Empleados (nombre, especialidad) VALUES ('$nombre', '$especialidad')";
    mysqli_query($connection, $query);

    header('Location: index.php');
}

include 'layout.php';
?>
