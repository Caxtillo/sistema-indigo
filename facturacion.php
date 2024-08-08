<?php
include 'header.php';
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Listado de facturas</h1>
          </div><!-- /.col -->
          
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    
  <!-- /.content-wrapper -->
  
    <h1 class="text-center mb-4">Crear Factura</h1>

    <section class="content">
        <div class="container-fluid">
            <form id="factura-form" method="post" action="facturacion.php">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <input type="hidden" id="cliente" name="cliente" class="form-control" value="Cliente"required>
                                </div>
                                <div class="form-group">
                                    <label for="empleados">Empleado:</label>
                                    <select id="empleados" name="empleados" class="form-control" required>
                                        <option value="" disabled selected>Selecciona un empleado</option>
                                        <!-- Opciones cargadas desde la base de datos -->
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                </div>
                                <div class="form-group">
                                    <label for="fecha">Fecha:</label>
                                    <input type="date" datatype="date" id="fecha" name="fecha" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h3>Detalle de Servicios/Artículos</h3>
                        <div class="table-responsive">
                            <table id="detalle" class="table table-striped">
                                <thead>
                                </thead>
                                <tbody>
                                <!--Aqui se genera la tabla-->
                                </tbody>
                            </table>
                        </div>
                        <div class="total">
                            <p class=""><strong>Total Divisa:</strong> <span id="total-divisa">0.00</span></p>
                            <p class=""><strong>Total No Divisa:</strong> <span id="total-no-divisa">0.00</span></p>
                        </div>
                        <input type="hidden" id="datos-json" name="datos-json">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <button type="button" id="agregar-fila" class="btn btn-primary btn-block">Agregar Servicio/Artículo</button>
                            </div>
                <div class="col-md-6">
                    <button type="submit" id="crear-factura" class="btn btn-success btn-block">Crear Factura</button>
                </div>
            </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
    

    <script>
document.addEventListener('DOMContentLoaded', function () {
    const detalleTable = document.getElementById('detalle');
    const agregarFilaButton = document.getElementById('agregar-fila');
    const selectEmpleados = document.getElementById('empleados');
    const facturaForm = document.getElementById('factura-form');

    agregarFilaButton.addEventListener('click', agregarFila);

    function agregarFila() {
        // Realizar una petición AJAX para obtener el contenido de detalles_factura.php
        fetch('detalles_factura.php')
            .then(response => response.text())
            .then(data => {
                // Crear una nueva fila
                const newRow = document.createElement('tr');
                newRow.classList.add('detalle-row');

                // Crear una nueva celda para el número de ítem
                const numberCell = document.createElement('td');
                numberCell.classList.add('numero-item');
                numberCell.textContent = detalleTable.querySelectorAll('tbody tr').length + 1;
                newRow.appendChild(numberCell);

                // Crear una nueva celda para el contenido de detalles_factura.php
                const newCell = document.createElement('td');
                newCell.innerHTML = data;
                newRow.appendChild(newCell);

                // Agregar la nueva fila a la tabla
                detalleTable.querySelector('tbody').appendChild(newRow);

                // Recalcular los números de ítem después de agregar la fila
                recalcularNumerosItem();

                // Posicionar el cursor en el input precio de la nueva fila
                const nuevoPrecioInput = newRow.querySelector('.precio');
                nuevoPrecioInput.focus();

                // Scroll hasta el final de la página
                window.scrollTo(0, document.body.scrollHeight);
            })
            .catch(error => {
                console.error('Error al cargar detalles_factura.php:', error);
            });
    }

    function recalcularNumerosItem() {
        // Obtener todas las filas en la tabla
        const detalleRows = detalleTable.querySelectorAll('.detalle-row');

        // Iterar sobre todas las filas y actualizar los números de ítem
        detalleRows.forEach((row, index) => {
            row.querySelector('.numero-item').textContent = index + 1;
        });
    }

    detalleTable.addEventListener('input', function (event) {
        const targetClassList = event.target.classList;

        if (targetClassList.contains('precio') || targetClassList.contains('cantidad')) {
            calcularTotales();
        }
    });

    detalleTable.addEventListener('click', function (event) {
        if (event.target.classList.contains('eliminar-fila')) {
            const row = event.target.closest('.detalle-row');
            row.remove();
            calcularTotales();
        }
    });

    detalleTable.addEventListener('keypress', function (event) {
        if (event.key === 'Enter') {
            agregarFila();
            event.preventDefault(); // Prevenir el comportamiento por defecto del Enter
        }
    });

    function calcularTotales() {
        let totalDivisa = 0;
        let totalNoDivisa = 0;

        const detalleRows = detalleTable.querySelectorAll('.detalle-row');
        detalleRows.forEach(row => {
            const precio = parseFloat(row.querySelector('.precio').value);
            const cantidad = parseFloat(row.querySelector('.cantidad').value);
            const formaPago = row.querySelector('.forma_pago').value;
            let subtotal = precio * cantidad;

            // Mostrar el símbolo correspondiente según la forma de pago
            if (formaPago === 'divisa') {
                row.querySelector('.subtotal-amount').textContent = '$' + subtotal.toFixed(2);
                totalDivisa += subtotal;
            } else {
                row.querySelector('.subtotal-amount').textContent = 'Bs. ' + subtotal.toFixed(2);
                totalNoDivisa += subtotal;
            }
        });

        // Mostrar los totales por separado
        document.getElementById('total-divisa').textContent = '$' + totalDivisa.toFixed(2);
        document.getElementById('total-no-divisa').textContent = 'Bs. ' + totalNoDivisa.toFixed(2);

        // Scroll hasta el final de la página
        window.scrollTo(0, document.body.scrollHeight);
    }

    facturaForm.addEventListener('keypress', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Prevenir el comportamiento por defecto del Enter
        }
    });

    fetch('get_empleados.php')
        .then(response => response.json())
        .then(data => {
            data.forEach(empleado => {
                const option = document.createElement('option');
                option.value = empleado.id;
                option.textContent = empleado.nombre;
                selectEmpleados.appendChild(option);
            });
        })
        .catch(error => console.error('Error:', error));
});

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
<section class="content"><div class="container-fluid"><div class="card"><div class="card-body"><div>
        <h2>Detalles de la factura</h2>
        <p data-field="EMPLEADO">Empleado: #${data.invoice.EMPLEADO_ID} / ${data.invoice.NOMBRE_EMPLEADO}</p>
        <p>Factura: <!-- <p data-field="FACTURA"> --> ${invoiceId}</p>
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
                <td contenteditable="true" data-field="TIPO">${product.TIPO}</td>
                <td contenteditable="true" data-field="FORMA_PAGO">${product.FORMA_PAGO}</td>
                <td contenteditable="true" data-field="DESCRIPCION">${product.DESCRIPCION}</td>
                <td contenteditable="true" data-field="PRECIO">${product.PRECIO}</td>
                <td contenteditable="true" data-field="CANTIDAD">${product.CANTIDAD}</td>
                <td data-field="SUBTOTAL">${product.SUBTOTAL}</td>
                
                </tr>
            `).join('')}
        </table>

        <br><p>Total de la factura: ${calculateTotal(data.products)}</p>
        <div class="text-center">
        <!-- <button class="btn btn-success" onclick="saveChanges()">Guardar Cambios</button> -->
        </div>
    </div></div></div>
    
    
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

    function enableEdit(element) {
    // Al hacer doble clic, habilitar la edición del campo
    element.setAttribute('contenteditable', 'true');
}
function getValue(selector) {
    const element = document.querySelector(selector);
    return element ? element.textContent : null;
}
function saveChanges() {
    // Recopilar los datos editados
    const editedData = {
    TIPO: getValue('[data-field="TIPO"]'),
    FORMA_PAGO: getValue('[data-field="FORMA_PAGO"]'),
    DESCRIPCION: getValue('[data-field="DESCRIPCION"]'),
    PRECIO: getValue('[data-field="PRECIO"]'),
    CANTIDAD: getValue('[data-field="CANTIDAD"]'),
    FACTURA: getValue('[data-field="FACTURA"]'),  // Agrega el campo FACTURA aquí
    FECHA: obtenerFechaHoy(),
    EMPLEADO: getValue('[data-field="EMPLEADO"]'),
    // Agrega más campos según sea necesario
};

function obtenerFechaHoy() {
    const hoy = new Date();
    const year = hoy.getFullYear();
    const month = ('0' + (hoy.getMonth() + 1)).slice(-2);  // Añade cero al mes si es menor a 10
    const day = ('0' + hoy.getDate()).slice(-2);  // Añade cero al día si es menor a 10

    return `${year}-${month}-${day}`;
}

    console.log('Datos editados:', editedData);  // Agregamos esta línea para depurar

    // Realizar la solicitud AJAX para guardar los cambios
    fetch('guardar_cambios.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ editedData }),
    })
    .then(response => {
        console.log('Respuesta del servidor:', response);  // Agregamos esta línea para depurar

        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }

        return response.json();
    })
    .then(data => {
        // Manejar la respuesta del servidor, por ejemplo, mostrar un mensaje de éxito
        console.log('Cambios guardados exitosamente:', data);
    })
    .catch(error => {
        // Manejar errores de la solicitud
        console.error('Error al guardar cambios:', error);
    });
}


    function editValue(field) {
        const valueContainer = document.querySelector(`[data-field="${field}"]`);
        const originalValue = valueContainer.textContent;

        // Crear un campo de entrada para editar el valor
        const inputField = document.createElement('input');
        inputField.type = 'text';
        inputField.value = originalValue;

        // Reemplazar el contenido actual con el campo de entrada
        valueContainer.innerHTML = '';
        valueContainer.appendChild(inputField);

        // Agregar un botón para confirmar la edición
        const confirmButton = document.createElement('button');
        confirmButton.textContent = 'Confirmar';
        confirmButton.addEventListener('click', () => {
            // Guardar el nuevo valor en la base de datos o donde sea necesario
            const newValue = inputField.value;

            // Actualizar la vista con el nuevo valor
            valueContainer.innerHTML = newValue;
        });
        valueContainer.appendChild(confirmButton);
    };
</script>

<?php
include 'db.php';

$connection = new mysqli($host, $username, $password, $database);

if ($connection->connect_error) {
    die("Error de conexión: " . $connection->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cliente = mysqli_real_escape_string($connection, $_POST["cliente"]);
    $empleado = mysqli_real_escape_string($connection, $_POST["empleados"]);
    $fecha = date("Y-m-d", strtotime($_POST["fecha"]));

    $sql = "INSERT INTO invoice (CLIENTE, EMPLEADO_ID, FECHA) 
            VALUES ('{$cliente}', '{$empleado}', '{$fecha}')";

    if ($connection->query($sql)) {
        $invoice_id = $connection->insert_id;

        if (isset($_POST["tipo"]) && isset($_POST["descripcion"]) && isset($_POST["precio"]) && isset($_POST["cantidad"]) && isset($_POST["forma_pago"])) {
            $tipos = $_POST["tipo"];
            $descripciones = $_POST["descripcion"];
            $precios = $_POST["precio"];
            $cantidades = $_POST["cantidad"];
            $formas_pago = $_POST["forma_pago"];
            $subtotales = array();
        
            for ($i = 0; $i < count($tipos); $i++) {
                $tipo = mysqli_real_escape_string($connection, $tipos[$i]); // Escapar solo esta cadena
                $descripcion = mysqli_real_escape_string($connection, $descripciones[$i]); // Escapar solo esta cadena
                $precio = floatval($precios[$i]); // Convertir a float sin escapar
                $cantidad = intval($cantidades[$i]); // Convertir a int sin escapar
                $forma_pago_detalle = mysqli_real_escape_string($connection, $formas_pago[$i]); // Escapar solo esta cadena
                $subtotal = $precio * $cantidad;
                $subtotales[] = $subtotal;
        
                $sql2 = "INSERT INTO invoice_products (INVOICE_ID, TIPO, DESCRIPCION, PRECIO, CANTIDAD, SUBTOTAL, FORMA_PAGO) 
                         VALUES ('{$invoice_id}', '{$tipo}', '{$descripcion}', '{$precio}', '{$cantidad}', '{$subtotal}', '{$forma_pago_detalle}')";
        
                $connection->query($sql2);
            }
        
            $total = array_sum($subtotales);
        
            echo "<div class='container-fluid'><div class='alert alert-success'>Factura creada exitosamente. 
            <a href='#' onclick='showInvoiceDetails(\"{$invoice_id}\")'>Haz clic aquí</a> para ver la factura.</div>

            ";
            

        } else {
            echo "<div class='alert alert-danger'>Error al procesar los detalles de servicios/artículos.</div>";
        }
    }
    $connection->close();
}
?>
<div id="invoiceDetails"></div>
<?php
include 'footer.php';
?>