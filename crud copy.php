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
            <h1 class="m-0">Listado</h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

  <!-- /.content-wrapper -->

<?php
$action = $_GET['action'] ?? 'list'; // Obtener la acción de la URL
$table = $_GET['table'] ?? ''; // Obtener el nombre de la tabla de la URL

if ($action === 'add') {
    // Lógica para agregar un registro en la tabla
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtener los nombres de los campos y los valores del formulario
        $fields = generateFormFieldsForTable($table, true);
        $fieldNames = implode(', ', array_keys($fields));
        $fieldValues = "'" . implode("', '", $_POST) . "'";

        // Crear y ejecutar la consulta SQL de inserción
        $query = "INSERT INTO $table ($fieldNames) VALUES ($fieldValues)";
        mysqli_query($connection, $query);

        // Redirigir a la página de lista después de agregar
        header("Location: crud.php?action=list&table=$table");
    }

    // Generar el formulario de agregar
    $formTitle = 'Agregar ' . ucfirst($table);
    $formFields = generateFormFields($table, true);
    $submitButtonLabel = 'Agregar';

    // Mostrar el formulario de agregar
    echo '<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">' . $formTitle . '</h3>
                    </div>
                    <form method="POST">
                        <div class="card-body">';
    echo $formFields;
    echo '
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">' . $submitButtonLabel . '</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </section>';

} elseif ($action === 'edit') {
    // Lógica para editar un registro en la tabla
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtener el ID y los campos a actualizar
        $id = $_POST['id'];
        $fields = generateFormFieldsForTable($table, false);

        // Generar la parte SET de la consulta SQL
        $updates = [];
        foreach ($fields as $field) {
            $updates[] = "$field = '" . $_POST[$field] . "'";
        }
        $updates = implode(', ', $updates);

        // Crear y ejecutar la consulta SQL de actualización
        $query = "UPDATE $table SET $updates WHERE id=$id";
        mysqli_query($connection, $query);

        // Redirigir a la página de lista después de editar
        header("Location: crud.php?action=list&table=$table");
    }

    // Obtener el ID del registro a editar
    $id = $_GET['id'];

    // Obtener la información del registro a editar
    $editQuery = "SELECT * FROM $table WHERE id=$id";
    $editResult = mysqli_query($connection, $editQuery);
    $editRow = mysqli_fetch_assoc($editResult);

    // Generar el formulario de edición
    $formTitle = 'Editar ' . ucfirst($table);
    $formFields = generateFormFields($table, false, $editRow);
    $submitButtonLabel = 'Guardar cambios';

    // Mostrar el formulario de edición
    echo '<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Edición de registro</h3>
                    </div>
                    <form>
                        <div class="card-body">';
    echo $formFields;
    echo '
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">' . $submitButtonLabel . '</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </section>';

} elseif ($action === 'delete') {
    // Lógica para borrar un registro en la tabla
    $id = $_GET['id'];

    // Crear y ejecutar la consulta SQL de eliminación
    $query = "DELETE FROM $table WHERE id=$id";
    mysqli_query($connection, $query);

    // Redirigir a la página de lista después de eliminar
    header("Location: crud.php?action=list&table=$table");
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
                    <a href="crud.php?action=add&table=' . $table .'"class="btn btn-primary">Agregar Registro</a>
                        <div class="card">
                             <div class="card-header">
                            <h3 class="card-title">';
    echo $table;
    echo '</h3> </div>';
    echo '<div class="card-body" id="invoiceDetails">
            <table id="example2" class="table table-bordered table-hover">
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

        // Pasar el tipo de campo 'date' cuando sea necesario
        $fieldType = $row['Type'];
        if (strpos($fieldType, 'date') !== false) {
            $fieldType = 'date';
        } else {
            $fieldType = 'text';
        }

        $fields .= generateFormField($row['Field'], $fieldValue, $fieldType);
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
function generateFormField($fieldName, $fieldValue, $fieldType) {
    if ($fieldType === 'date') {
        return '<div class="form-group">
                    <label for="' . $fieldName . '">' . ucwords(str_replace('_', ' ', $fieldName)) . ':</label>
                    <input type="date" class="form-control" name="' . $fieldName . '" value="' . $fieldValue . '" required>
                </div>';
    } else {
        return '<div class="form-group">
                    <label for="' . $fieldName . '">' . ucwords(str_replace('_', ' ', $fieldName)) . ':</label>
                    <input type="text" class="form-control" name="' . $fieldName . '" value="' . $fieldValue . '" required>
                </div>';
    }
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
        <a href="crud.php?action=edit&table=' . $table . '&id=' . $row['id'] . '" class="btn btn-sm btn-primary">Editar</a>
        <a href="crud.php?action=delete&table=' . $table . '&id=' . $row['id'] . '" class="btn btn-sm btn-danger" onclick="return confirmDelete()">Eliminar</a>
    </td>';
    }

    return $tableContent;
}


ob_end_flush();

?>
