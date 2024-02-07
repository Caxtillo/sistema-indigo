<?php
include 'db.php';

$query = "SELECT * FROM Empleados";
$result = mysqli_query($connection, $query);

$contenido = '
<div class="container contenido">
    <h1 class="text-center mb-4">CRUD de Empleados</h1>
    <a href="create.php" class="btn btn-primary mb-3">Agregar Empleado</a>
    <div class="table-responsive">
        <table class="table table-bordered full-width">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Especialidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
';

while ($row = mysqli_fetch_assoc($result)) {
    $contenido .= "
    <tr>
        <td>{$row['id']}</td>
        <td>{$row['nombre']}</td>
        <td>{$row['especialidad']}</td>
        <td>
            <a href='edit.php?id={$row['id']}' class='btn btn-sm btn-primary'>Editar</a>
            <a href='delete.php?id={$row['id']}' class='btn btn-sm btn-danger'>Eliminar</a>
        </td>
    </tr>
    ";
}

$contenido .= '
            </tbody>
        </table>
    </div>
</div>
';

include 'layout.php';
?>
