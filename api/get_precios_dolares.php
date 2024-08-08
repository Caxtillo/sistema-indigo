<?php
// get_precios_dolares.php
include "db.php";

$connection = new mysqli($host, $username, $password, $database);

if ($connection->connect_error) {
    die("Error de conexión: " . $connection->connect_error);
}

$sql = "SELECT id, descripcion, precio FROM servicios"; // Ajusta la consulta según tu estructura de base de datos
$result = $connection->query($sql);

$preciosDolares = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $precioDolar = array(
            "id" => $row["id"],
            "descripcion" => $row["descripcion"],
            "precio" => $row["precio"]
        );
        $preciosDolares[] = $precioDolar;
    }
}

echo json_encode($preciosDolares);

$connection->close();
?>
