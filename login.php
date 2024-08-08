<?php
session_start();

// Si ya hay una sesión activa, redirigir al panel de control
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include 'db.php';

if ($connection->connect_error) {
    die("Conexión fallida: " . $connection->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = $_POST["usuario"];
    $contrasena = $_POST["contrasena"];

    $sql = "SELECT id, name FROM users WHERE name=? AND password=?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("ss", $usuario, $contrasena);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header("Location: index.php");
        exit();
    } else {
        $error_message = "Usuario o contraseña incorrectos.";
    }
}

$connection->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Iniciar Sesión</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            text-align: center;
            margin-top: 100px;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
        }
        .logo {
            margin-bottom: 20px;
        }
        .login-box {
            width: 300px;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            width: 100%;
            background-color: #007BFF;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="logo">
        <img src="indigo.png" alt="Logo" width="250">
    </div>
    <div class="login-box">
        <h2>Iniciar Sesión</h2>
        <?php
        if (isset($error_message)) {
            echo "<p style='color: red;'>$error_message</p>";
        }
        ?>
        <form method="post" action="">
            <input type="text" name="usuario" placeholder="Usuario" required><br>
            <input type="password" name="contrasena" placeholder="Contraseña" required><br>
            <input type="submit" value="Ingresar">
        </form>
    </div>
</div>

</body>
</html>