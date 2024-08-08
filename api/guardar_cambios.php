<?php

include 'db.php';

$connection = new mysqli($host, $username, $password, $database);

if ($connection->connect_error) {
    die("Error de conexión: " . $connection->connect_error);
}

$response = array();  // Crear un arreglo para la respuesta

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos necesarios para la actualización
    $cliente = isset($_POST["cliente"]) ? mysqli_real_escape_string($connection, $_POST["cliente"]) : "";
    $empleado = isset($_POST["empleados"]) ? mysqli_real_escape_string($connection, $_POST["empleados"]) : "";
    $fecha = isset($_POST["fecha"]) ? date("Y-m-d", strtotime($_POST["fecha"])) : "";
    $factura = isset($_POST["FACTURA"]) ? mysqli_real_escape_string($connection, $_POST["FACTURA"]) : "";  // Agrega esta línea

    // Realizar el update en la tabla 'invoice'
    $sql = "UPDATE invoice SET CLIENTE = '{$cliente}', EMPLEADO_ID = '{$empleado}', FECHA = '{$fecha}' WHERE INVOICE_ID = '{$factura}'";

    if ($connection->query($sql)) {
        // Actualizar los detalles de los productos
        if (isset($_POST["TIPO"]) && isset($_POST["DESCRIPCION"]) && isset($_POST["PRECIO"]) && isset($_POST["CANTIDAD"]) && isset($_POST["FORMA_PAGO"])) {
            $tipos = $_POST["TIPO"];
            $descripciones = $_POST["DESCRIPCION"];
            $precios = $_POST["PRECIO"];
            $cantidades = $_POST["CANTIDAD"];
            $formas_pago = $_POST["FORMA_PAGO"];

            for ($i = 0; $i < count($tipos); $i++) {
                $tipo = mysqli_real_escape_string($connection, $tipos[$i]);
                $descripcion = mysqli_real_escape_string($connection, $descripciones[$i]);
                $precio = floatval($precios[$i]);
                $cantidad = intval($cantidades[$i]);
                $forma_pago_detalle = mysqli_real_escape_string($connection, $formas_pago[$i]);
                $subtotal = $precio * $cantidad;

                // Realizar el update en la tabla 'invoice_products'
                $sql2 = "UPDATE invoice_products SET 
                             TIPO = '{$tipo}', 
                             DESCRIPCION = '{$descripcion}', 
                             PRECIO = '{$precio}', 
                             CANTIDAD = '{$cantidad}', 
                             FORMA_PAGO = '{$forma_pago_detalle}' 
                         WHERE INVOICE_ID = '{$factura}'";

                $connection->query($sql2);
            }

            $response['success'] = true;
            $response['message'] = 'Cambios guardados exitosamente.';
        } else {
            $response['success'] = false;
            $response['message'] = 'Error al procesar los detalles de servicios/artículos.';
        }
    } else {
        $response['success'] = false;
        $response['message'] = 'Error al actualizar la factura: ' . $connection->error;
    }

    // Enviar una respuesta JSON válida
    header('Content-Type: application/json');
    echo json_encode($response);

    $connection->close();
}
?>
