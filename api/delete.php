<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "DELETE FROM Empleados WHERE id=$id";
    mysqli_query($connection, $query);
}

header('Location: index.php');
include 'layout.php';
?>
