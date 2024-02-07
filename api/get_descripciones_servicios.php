<?php
// get_descripciones_servicios.php
include "db.php";

$connection = new mysqli($host, $username, $password, $database);

if ($connection->connect_error) {
    die("Error de conexión: " . $connection->connect_error);
}

$sql = "SELECT id, descripcion, precio FROM servicios";
$result = $connection->query($sql);

$servicios = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $servicio = array(
            "id" => $row["id"],
            "descripcion" => $row["descripcion"],
            "precio" => $row["precio"]
        );
        $servicios[] = $servicio;
    }
}

echo json_encode($servicios);

$connection->close();
?>
