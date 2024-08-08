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
            <h1 class="m-0">Listado de descuentos</h1>
          </div><!-- /.col -->
          
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

  <!-- /.content-wrapper -->

<?php
$action = $_GET['action'] ?? 'list';
$table = "descuentosempleado";

if ($action === 'add') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fields = generateFormFieldsForTable($table, true);
        $fieldNames = implode(', ', array_keys($fields));
        $fieldValues = "'" . implode("', '", $_POST) . "'";

        $query = "INSERT INTO $table ($fieldNames) VALUES ($fieldValues)";
        mysqli_query($connection, $query);

        header("Location: crud_descuentos.php?action=list&table=$table");
        exit();
    }

    $formTitle = 'Agregar ' . ucfirst($table);
    $formFields = generateFormFields($table, true);
    $submitButtonLabel = 'Agregar';

    echo '<div class="container contenido">';
    echo '<h1 class="text-center mb-4">' . $formTitle . '</h1>';
    echo '<form method="POST">';
    echo $formFields;
    echo '<button type="submit" class="btn btn-primary">' . $submitButtonLabel . '</button>';
    echo '</form>';
    echo '</div>';
} elseif ($action === 'edit') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'];
        $fields = generateFormFieldsForTable($table, false);

        $updates = [];
        foreach ($fields as $field) {
            $updates[] = "$field = '" . $_POST[$field] . "'";
        }
        $updates = implode(', ', $updates);

        $query = "UPDATE $table SET $updates WHERE id=$id";
        mysqli_query($connection, $query);

        header("Location: crud_descuentos.php?action=list&table=$table");
        exit();
    }

    $id = $_GET['id'];
    $editQuery = "SELECT * FROM $table WHERE id=$id";
    $editResult = mysqli_query($connection, $editQuery);
    $editRow = mysqli_fetch_assoc($editResult);

    $formTitle = 'Editar ' . ucfirst($table);
    $formFields = generateFormFields($table, false, $editRow);
    $submitButtonLabel = 'Guardar cambios';

    echo '<div class="container contenido">';
    echo '<h1 class="text-center mb-4">' . $formTitle . '</h1>';
    echo '<form method="POST">';
    echo $formFields;
    echo '<button type="submit" class="btn btn-primary">' . $submitButtonLabel . '</button>';
    echo '</form>';
    echo '</div>';
} elseif ($action === 'delete') {
    $id = $_GET['id'];

    $query = "DELETE FROM $table WHERE id=$id";
    mysqli_query($connection, $query);

    header("Location: crud_descuentos.php?action=list&table=$table");
    exit();
} else {
    // Listar registros de la tabla de descuentos
    $query = "SELECT * FROM $table"; // Aquí debes especificar el nombre de la tabla correctamente
    $result = mysqli_query($connection, $query);
    
    // Generar la lista de registros
    $tableHeaders = generateTableHeadersForTable($table);
    $tableContent = generateTableContent($result, $table);
    
    echo '<div class="container contenido">';
    echo '<h1 class="text-center mb-4">Lista de ' . ucfirst($table) . '</h1>';
    echo '<a href="crud_descuentos.php?action=add&table=' . $table . '" class="btn btn-primary mb-3">Agregar ' . ucfirst($table) . '</a>';
    echo '<div class="table-responsive">';
    echo '<table class="table table-bordered full-width">';
    echo '<thead><tr>' . generateTableHeadersForTable($table) . '</tr></thead>';
    echo '<tbody>' . $tableContent . '</tbody>';
    echo '</table>';
    echo '</div></div>';
}

function generateFormFields($table, $isAdding, $editRow = null) {
    global $connection;
    $fields = '';

    $query = "DESCRIBE $table";
    $result = mysqli_query($connection, $query);

    while ($row = mysqli_fetch_assoc($result)) {
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

function generateTableContent($result, $table) {
    $tableContent = '';

    while ($row = mysqli_fetch_assoc($result)) {
        $tableContent .= '<tr>';

        foreach ($row as $fieldValue) {
            $tableContent .= '<td>' . $fieldValue . '</td>';
        }

        $tableContent .= '<td>
            <a href="crud_descuentos.php?action=edit&table=' . $table . '&id=' . $row['id'] . '" class="btn btn-sm btn-primary">Editar</a>
            <a href="crud_descuentos.php?action=delete&table=' . $table . '&id=' . $row['id'] . '" class="btn btn-sm btn-danger" onclick="return confirmDelete()">Eliminar</a>
        </td>';

        $tableContent .= '</tr>';
    }

    return $tableContent;
}
ob_end_flush();
?>
