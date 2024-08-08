<?php
ob_start();
include 'db.php';
include 'header.php';

$invoiceId = $_GET['id'] ?? 0;

// Realiza la consulta para obtener los detalles de la factura según el $invoiceId
$query = "SELECT * FROM invoice WHERE INVOICE_ID = $invoiceId";
$result = mysqli_query($connection, $query);
$invoice = mysqli_fetch_assoc($result);

// Realiza la consulta para obtener los productos de la factura
$query = "SELECT * FROM invoice_products WHERE INVOICE_ID = $invoiceId";
$result = mysqli_query($connection, $query);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Comienzo de la estructura HTML para impresión
?>
<!-- Contenido de la factura -->
<div class="content-wrapper">
    <h2>Detalles de la Factura</h2>
    <p>Cliente: <?php echo $invoice['CLIENTE']; ?></p>
    <p>Fecha: <?php echo $invoice['FECHA']; ?></p>

    <!-- Lista de productos -->
    <h3>Productos:</h3>
    <table>
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo $product['DESCRIPCION']; ?></td>
                    <td><?php echo $product['PRECIO']; ?></td>
                    <td><?php echo $product['CANTIDAD']; ?></td>
                    <td><?php echo $product['SUBTOTAL']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Puedes agregar más detalles según tus necesidades -->
</div>

</body>
</html>

<?php

ob_end_flush();
?>
