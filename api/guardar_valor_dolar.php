<?php
// Recuperar el valor enviado desde el cliente
$data = json_decode(file_get_contents("php://input"));

if (isset($data->dollarValue)) {
    // Aquí debes establecer la conexión a la base de datos
    $servername = "localhost";
    $username = "tu_usuario";
    $password = "tu_contraseña";
    $dbname = "tu_base_de_datos";

    $con = new mysqli($servername, $username, $password, $dbname);

    if ($con->connect_error) {
        die(json_encode(array("success" => false)));
    }

    // Actualizar el valor en la base de datos
    $dollarValue = $con->real_escape_string($data->dollarValue);
    $sql = "UPDATE configuracion SET valor_dolar = '$dollarValue' WHERE id = 1"; // Suponiendo que tu tabla se llama "configuracion" y tiene un campo "valor_dolar"

    if ($con->query($sql)) {
        echo json_encode(array("success" => true));
    } else {
        echo json_encode(array("success" => false));
    }

    $con->close();
} else {
    echo json_encode(array("success" => false));
}
?>
