<?php
ob_start(); 
include 'db.php';
include 'header.php';
?>

<div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><?php echo ucfirst($_GET['table'] ?? 'Listado'); ?></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active"><?php echo ucfirst($_GET['table'] ?? 'Listado'); ?></li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Gestión de <?php echo ucfirst($_GET['table'] ?? 'registros'); ?></h3>
                <div class="card-tools">
                  <a href="crud.php?action=add&table=<?php echo $_GET['table']; ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Agregar Registro
                  </a>
                </div>
              </div>
              <div class="card-body">
                <?php
                $action = $_GET['action'] ?? 'list';
                $table = $_GET['table'] ?? '';

                if ($action === 'add') {
                    displayForm($table, true);
                } elseif ($action === 'edit') {
                    displayForm($table, false);
                } elseif ($action === 'delete') {
                    deleteRecord($table);
                } else {
                    displayList($table);
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
</div>

<?php
function displayForm($table, $isAdding) {
    global $connection;
    $id = $_GET['id'] ?? null;
    $formTitle = ($isAdding ? 'Agregar' : 'Editar') . ' ' . ucfirst($table);
    $submitButtonLabel = $isAdding ? 'Agregar' : 'Guardar cambios';

    if (!$isAdding && $id) {
        $editQuery = "SELECT * FROM $table WHERE id=$id";
        $editResult = mysqli_query($connection, $editQuery);
        $editRow = mysqli_fetch_assoc($editResult);
    }

    echo "<h2>$formTitle</h2>";
    echo '<form method="POST" class="needs-validation" novalidate>';
    
    $fields = generateFormFieldsForTable($table, $isAdding);
    foreach ($fields as $fieldName => $fieldType) {
        $fieldValue = $isAdding ? '' : ($editRow[$fieldName] ?? '');
        echo generateFormField($fieldName, $fieldValue, $fieldType);
    }

    echo '<div class="form-group">
            <button type="submit" class="btn btn-primary">' . $submitButtonLabel . '</button>
            <a href="crud.php?action=list&table=' . $table . '" class="btn btn-secondary">Cancelar</a>
          </div>';
    echo '</form>';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        processFormSubmission($table, $isAdding, $id);
    }
}

function displayList($table) {
    global $connection;
    $query = "SELECT * FROM $table";
    $result = mysqli_query($connection, $query);
    
    echo '<table class="table table-bordered table-hover">
            <thead>
              <tr>' . generateTableHeadersForTable($table) . '</tr>
            </thead>
            <tbody>' . generateTableContent($result, $table) . '</tbody>
          </table>';
}

function generateFormFieldsForTable($table, $isAdding) {
    global $connection;
    $fields = array();

    $query = "DESCRIBE $table";
    $result = mysqli_query($connection, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['Field'] === 'id') continue;
        $fields[$row['Field']] = $row['Type'];
    }

    return $fields;
}

function generateFormField($fieldName, $fieldValue, $fieldType) {
    $label = ucwords(str_replace('_', ' ', $fieldName));
    $inputType = (strpos($fieldType, 'date') !== false) ? 'date' : 'text';
    
    return '<div class="form-group">
              <label for="' . $fieldName . '">' . $label . ':</label>
              <input type="' . $inputType . '" class="form-control" id="' . $fieldName . '" name="' . $fieldName . '" value="' . htmlspecialchars($fieldValue) . '" required>
              <div class="invalid-feedback">Por favor, ingrese ' . strtolower($label) . '.</div>
            </div>';
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
    $content = '';
    while ($row = mysqli_fetch_assoc($result)) {
        $content .= '<tr>';
        foreach ($row as $fieldValue) {
            $content .= '<td>' . htmlspecialchars($fieldValue) . '</td>';
        }
        $content .= '<td>
                      <a href="crud.php?action=edit&table=' . $table . '&id=' . $row['id'] . '" class="btn btn-sm btn-info"><i class="fas fa-edit"></i> Editar</a>
                      <a href="crud.php?action=delete&table=' . $table . '&id=' . $row['id'] . '" class="btn btn-sm btn-danger" onclick="return confirm(\'¿Está seguro de que desea eliminar este registro?\')"><i class="fas fa-trash"></i> Eliminar</a>
                    </td>';
        $content .= '</tr>';
    }
    return $content;
}

function processFormSubmission($table, $isAdding, $id = null) {
    global $connection;
    $fields = generateFormFieldsForTable($table, $isAdding);
    $fieldNames = implode(', ', array_keys($fields));
    $fieldValues = "'" . implode("', '", array_map([$connection, 'real_escape_string'], $_POST)) . "'";

    if ($isAdding) {
        $query = "INSERT INTO $table ($fieldNames) VALUES ($fieldValues)";
    } else {
        $updates = [];
        foreach ($fields as $field => $type) {
            $updates[] = "$field = '" . $connection->real_escape_string($_POST[$field]) . "'";
        }
        $updates = implode(', ', $updates);
        $query = "UPDATE $table SET $updates WHERE id=$id";
    }

    if (mysqli_query($connection, $query)) {
        header("Location: crud.php?action=list&table=$table");
        exit();
    } else {
        echo "Error: " . mysqli_error($connection);
    }
}

function deleteRecord($table) {
    global $connection;
    $id = $_GET['id'];
    $query = "DELETE FROM $table WHERE id=$id";
    if (mysqli_query($connection, $query)) {
        header("Location: crud.php?action=list&table=$table");
        exit();
    } else {
        echo "Error al eliminar el registro: " . mysqli_error($connection);
    }
}

ob_end_flush();
?>

<script>
(function() {
  'use strict';
  window.addEventListener('load', function() {
    var forms = document.getElementsByClassName('needs-validation');
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false);
})();
</script>