<?php
ob_start();
include 'db.php';
include 'header.php';
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">

          </div><!-- /.col -->
          
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

  <!-- /.content-wrapper -->
<?php

$action = $_GET['action'] ?? 'list'; // Obtener la acción de la URL
$table = $_GET['table'] ?? ''; // Obtener el nombre de la tabla de la URL

if ($action === 'delete') {
    // Lógica para borrar un registro en la tabla
    $id = $_GET['id'];

    // Crear y ejecutar la consulta SQL de eliminación
    $query = "DELETE FROM invoice_products WHERE invoice_id=$id";
    mysqli_query($connection, $query);
    $query = "DELETE FROM invoice WHERE invoice_id=$id";
    mysqli_query($connection, $query);

    // Redirigir a la página de lista después de eliminar
    header("Location: crud_factura.php?action=list&table=$table");
} else {
    // Listar registros de la tabla
    $query = "SELECT * FROM $table";
    $result = mysqli_query($connection, $query);
    
    // Generar la lista de registros
    $tableHeaders = generateTableHeadersForTable($table);
    $tableContent = generateTableContent($result, $table);
    
    echo '<section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                             <div class="card-header">
                            <h1 >Listado de facturas</h1>
            </div>';
    echo '<div class="card-body table-responsive" id="invoiceDetails">
            <table id="example2" class="jsgrid-grid-header jsgrid-header-scrollbar">
            <thead>';
    echo '<tr>' . generateTableHeadersForTable($table) . '</tr></thead>';
    echo '<tbody>' . $tableContent . '</tbody></tr>
            </table>
        </div>

                        </div>
                    </div>

                </div>

            </div>

        </section>';

}

// Función para generar los campos del formulario según la tabla
function generateFormFields($table, $isAdding, $editRow = null) {
    global $connection;
    $fields = '';

    $query = "DESCRIBE $table";
    $result = mysqli_query($connection, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        // Omitir el campo 'id' en el formulario
        if ($row['Field'] === 'id') {
            continue;
        }

        if ($isAdding) {
            $fieldValue = '';
        } elseif ($editRow !== null) {
            $fieldValue = $editRow[$row['Field']];
        }

        $fields .= generateFormField($row['Field'], $fieldValue);
    }

    return $fields;
}
function generateFormFieldsForTable($table, $isAdding) {
    global $connection;
    $fields = array();

    $query = "DESCRIBE $table";
    $result = mysqli_query($connection, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        // Omitir el campo 'id' en el formulario
        if ($row['Field'] === 'id') {
            continue;
        }

        if ($isAdding) {
            $fields[$row['Field']] = '';
        }
    }

    return $fields;
}
// Función para generar un campo de formulario
function generateFormField($fieldName, $fieldValue) {
    return '<div class="form-group">
                <label for="' . $fieldName . '">' . ucwords(str_replace('_', ' ', $fieldName)) . ':</label>
                <input type="text" class="form-control" name="' . $fieldName . '" value="' . $fieldValue . '" required>
            </div>';
}

// Función para generar los encabezados de la tabla según la tabla
function generateTableHeadersForTable($table) {
    global $connection;
    $headers = '';

    $query = "DESCRIBE $table";
    $result = mysqli_query($connection, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $headers .= '<th>' . ucwords(str_replace('_', ' ', $row['Field'])) . '</th>';
    }

    $headers .= '<th>Acciones</th>';

    return $headers;
}


// Función para generar el contenido de la tabla
function generateTableContent($result, $table) {
    $tableContent = '';

    while ($row = mysqli_fetch_assoc($result)) {
        $tableContent .= '<tr>';
        
        foreach ($row as $fieldValue) {
            $tableContent .= '<td>' . $fieldValue . '</td>';
        }
        
        $tableContent .= '<td>
        <a href="#" class="btn btn-sm btn-primary" onclick="showInvoiceDetails(' . $row['INVOICE_ID'] . ')">Detalles</a>
        <a href="crud_factura.php?action=delete&table=' . $table . '&id=' . $row['INVOICE_ID'] . '" class="btn btn-sm btn-danger" onclick="return confirmDelete()">Eliminar</a>
    </td>';
    }

    return $tableContent;
}
ob_end_flush();
?>
<style>
    #invoiceDetails {
        margin-top: 0px;
    }

    #invoiceDetails table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #ccc;
    }

    #invoiceDetails th, #invoiceDetails td {
        padding: 8px;
        border: 1px solid #ccc;
    }

    #invoiceDetails th {
        background-color: #f7f7f7;
        font-weight: bold;
    }
</style>
<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
function reload() {
    location.reload();
}
function showInvoiceDetails(invoiceId) {
    const detailsContainer = document.getElementById('invoiceDetails');

    // Aquí debes reemplazar 'ruta_hacia_archivo_php' con la ruta real hacia tu archivo PHP que obtiene los detalles de la factura
    const url = 'generar_detalles.php?action=get_details&id=' + invoiceId;

    // Realizar una solicitud AJAX para obtener los detalles de la factura
    fetch(url)
        .then(response => response.json())
        .then(data => {
            // Crear el contenido para mostrar los detalles de la factura
            const detailsContent = `
    <div>
        <h2>Detalles de la factura</h2>
        <p>Empleado: ${data.invoice.NOMBRE_EMPLEADO}</p>
        <p>Método de Pago: ${data.invoice.FORMA_PAGO}</p>
        <table>
            <tr>
                <th>Tipo de Producto</th>
                <th>Forma de pago</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
            </tr>
            ${data.products.map(product => `
                <tr>
                    <td>${product.TIPO}</td>
                    <td>${product.FORMA_PAGO}</td>
                    <td>${product.DESCRIPCION}</td>
                    <td>${product.PRECIO}</td>
                    <td>${product.CANTIDAD}</td>
                    <td>${product.SUBTOTAL}</td>
                </tr>
            `).join('')}
        </table>
        
        <br><p>Total de la factura: ${calculateTotal(data.products)}</p>
        <div class="text-center">
            <button class="btn btn-secondary" onclick="reload()">
                <i class="fas fa-arrow-left"></i> Volver
            </button>
        </div>
    </div>
    
    
`;

function calculateTotal(products) {
    let total = 0;
    for (const product of products) {
        total += parseFloat(product.SUBTOTAL);
    }
    return total.toFixed(2);
}
            detailsContainer.innerHTML = detailsContent;
        })
        .catch(error => {
            console.error('Error al obtener los detalles de la factura:', error);
        });
}
</script>
