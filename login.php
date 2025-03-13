<?php
session_start();

function myConexion() {
    require_once "conexion.php";
    $conexion = new mysqli($servername, $username, $passworddb, $dbname);
    if ($conexion->connect_error) {
        die("Error de conexion" . $conexion->connect_error);
    }
    return $conexion;
}

function validarLogueo($conectada) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST["usuario"]) && !empty($_POST["contra"])) {
            $usuario = $_POST["usuario"];
            $contra = $_POST["contra"];

            $stmt = $conectada->prepare("SELECT * FROM usuario WHERE usuario = ?");
            $stmt->bind_param("s", $usuario);
            $stmt->execute();
            $result = $stmt->get_result();
            

            if ($result->num_rows > 0) {
                $usuario_data = $result->fetch_assoc();
                if (password_verify($contra,$usuario_data['contra'])) {
                    echo "Contraseña correcta";
                    $_SESSION["usuario"] = $usuario;
                    header("Location: consulta.php");
                } else {
                    echo "Contraseña incorrecta";
                }
            } else {
                echo "Usuario no encontrado";
            }

            $stmt->close();
        }
    }
}

$conectada = myConexion();
validarLogueo($conectada);
?>