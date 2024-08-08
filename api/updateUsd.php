<?php
// Obtener el valor del dólar desde un servicio en línea
$response = file_get_contents('https://api.exchangerate-api.com/v4/latest/USD');
$data = json_decode($response, true);
$usd_rate = $data['rates']['VES']; // Suponiendo que 'VES' es el código ISO para el bolívar venezolano

include 'db.php';

// Verificar la conexión
if ($connection->connect_error) {
    die("Error de conexión: " . $connection->connect_error);
}

// Insertar el valor del dólar en la base de datos
$insert_query = "UPDATE configuracion SET valor_dolar = $usd_rate WHERE id = 1";
if ($connection->query($insert_query) === TRUE) {
    header("Location: index.php");
} else {
    echo "Error al insertar el valor del dólar en la base de datos: " . $connection->error;
}

// Cerrar la conexión a la base de datos
$connection->close();
?>
