<?php
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if(isset($data['cliente']) && isset($data['empleado']) && isset($data['formaPago']) && isset($data['detalles']) && isset($data['total'])) {
    $cliente = $data['cliente'];
    $empleado = $data['empleado'];
    $formaPago = $data['formaPago'];
    $detalles = $data['detalles'];
    $total = $data['total'];

    // Insertar los datos en la tabla de facturas
    $query = "INSERT INTO facturas (cliente, empleado, forma_pago) VALUES ('$cliente', '$empleado', '$formaPago')";
    if (mysqli_real_query($connection, $query)) {
        // ... Inserción de detalles ...
        // Devolver respuesta exitosa en formato JSON
        header("Content-Type: application/json");
        echo json_encode(array("success" => true));
    } else {
        // Error en la inserción de la factura
        header("Content-Type: application/json");
        echo json_encode(array("success" => false, "sql_query" => $query));
    }
} else {
    // Devolver respuesta de error en formato JSON
    header("Content-Type: application/json");
    echo json_encode(array("success" => false, "message" => "Faltan datos"));
}
?>
