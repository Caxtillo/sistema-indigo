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
        <img src="logo.png" alt="Logo">
    </div>
    <div class="login-box">
        <h2>Iniciar Sesión</h2>
        <form method="post" action="">
            <input type="text" name="usuario" placeholder="Usuario" required><br>
            <input type="password" name="contrasena" placeholder="Contraseña" required><br>
            <input type="submit" value="Ingresar">
        </form>
        <?php
        $server = "localhost";
        $usuario_db = "root";
        $contrasena_db = "";
        $nombre_db = "sistema";

        $conn = new mysqli($server, $usuario_db, $contrasena_db, $nombre_db);

        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $usuario = $_POST["usuario"];
            $contrasena = $_POST["contrasena"];

            $sql = "SELECT * FROM usuarios WHERE usuario='$usuario' AND contrasena='$contrasena'";
            $resultado = $conn->query($sql);

            if ($resultado->num_rows > 0) {
                echo "<p>Inicio de sesión exitoso. ¡Bienvenido!</p>";
            } else {
                echo "<p>Usuario o contraseña incorrectos.</p>";
            }
        }

        $conn->close();
        ?>
    </div>
</div>

</body>
</html>
