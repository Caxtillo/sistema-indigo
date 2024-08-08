<?php
include 'db.php';

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? 0;

if ($action === 'get_details' && $id > 0) {
    $query = "SELECT * FROM invoice WHERE INVOICE_ID = $id";
    $result = mysqli_query($connection, $query);
    $invoiceDetails = mysqli_fetch_assoc($result);
    
    // Obtener el nombre del empleado asociado al ID
    $empleadoId = $invoiceDetails['EMPLEADO_ID'];
    $query = "SELECT nombre FROM empleados WHERE id = $empleadoId";
    $result = mysqli_query($connection, $query);
    $empleadoDetails = mysqli_fetch_assoc($result);
    
    // Agregar el nombre del empleado a los detalles de la factura
    $invoiceDetails['NOMBRE_EMPLEADO'] = $empleadoDetails['nombre'];
    
    $query = "SELECT * FROM invoice_products WHERE INVOICE_ID = $id";
    $result = mysqli_query($connection, $query);
    $products = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    
    $details = array(
        'invoice' => $invoiceDetails,
        'products' => $products
    );
    
    // Devolver detalles en formato JSON
    header('Content-Type: application/json');
    echo json_encode($details);
    exit;
}
?>
