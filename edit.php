<?php
include 'db.php';

$contenido = '';

if ($_SERVER['REQUEST_METHOD'] === 'get') {
    $id = $_GET['id'];
    $nombre = $_GET['nombre'];
    $especialidad = $_GET['especialidad'];

    $query = "UPDATE Empleados SET nombre='$nombre', especialidad='$especialidad' WHERE id=$id";
    mysqli_query($connection, $query);

    header('Location: index.php');
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM Empleados WHERE id=$id";
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_assoc($result);

    $contenido = '
    <div class="container contenido">
    <h2 class="mb-4">Editar Empleado</h2>
    <form method="POST">
        <input type="hidden" name="id" value="' . $row['id'] . '">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" class="form-control" name="nombre" value="' . $row['nombre'] . '" required>
        </div>
        <div class="form-group">
            <label for="especialidad">Especialidad:</label>
            <input type="text" class="form-control" name="especialidad" value="' . $row['especialidad'] . '" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </form>
    <br>
    <button class="btn btn-secondary" onclick="window.history.back();">Volver</button>
</div>
';

} else {
    header('Location: index.php');
}

include 'layout.php';
?>
