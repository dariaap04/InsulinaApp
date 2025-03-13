<?php
session_start();
require_once "conexion.php";

// Verificar que el usuario esté autenticado
if (!isset($_SESSION["usuario"])) {
    echo json_encode(["error" => "Debe iniciar sesión."]);
    exit();
}

// Verificar que se reciban los parámetros necesarios
if (!isset($_POST['id']) || !isset($_POST['tabla'])) {
    echo json_encode(["error" => "Faltan parámetros necesarios."]);
    exit();
}

$id = $_POST['id'];
$tabla = $_POST['tabla'];

// Validar la tabla para evitar inyección SQL
$tablas_permitidas = ["comida", "hipoglucemia", "hiperglucemia"];
if (!in_array($tabla, $tablas_permitidas)) {
    echo json_encode(["error" => "Tabla no válida."]);
    exit();
}

// Conexión a la base de datos
$conexion = new mysqli($servername, $username, $passworddb, $dbname);
if ($conexion->connect_error) {
    echo json_encode(["error" => "Error de conexión: " . $conexion->connect_error]);
    exit();
}

// Obtener ID del usuario actual
$usuario = $_SESSION["usuario"];
$sql_usuario = "SELECT id_usu FROM usuario WHERE usuario = ?";
$stmt_usuario = $conexion->prepare($sql_usuario);
$stmt_usuario->bind_param("s", $usuario);
$stmt_usuario->execute();
$resultado_usuario = $stmt_usuario->get_result();

if ($resultado_usuario->num_rows === 0) {
    echo json_encode(["error" => "Usuario no encontrado."]);
    $conexion->close();
    exit();
}

$row_usuario = $resultado_usuario->fetch_assoc();
$id_usu = $row_usuario['id_usu'];

// Preparar y ejecutar la consulta de eliminación
// Importante: verificamos que el registro pertenezca al usuario actual por seguridad
$sql = "DELETE FROM $tabla WHERE tipo_comida = ? AND id_usu = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("si", $id, $id_usu);
$resultado = $stmt->execute();

if ($resultado) {
    // Registrar la acción en un log (opcional)
    $fecha_actual = date('Y-m-d H:i:s');
    $sql_log = "INSERT INTO log_actividad (id_usu, accion, tabla, fecha) VALUES (?, ?, ?, ?)";
    $accion = "Eliminación de registro tipo_comida: $id";
    
    if ($stmt_log = $conexion->prepare($sql_log)) {
        $stmt_log->bind_param("isss", $id_usu, $accion, $tabla, $fecha_actual);
        $stmt_log->execute();
    }
    
    echo json_encode(["success" => "Registro eliminado correctamente."]);
} else {
    echo json_encode(["error" => "Error al eliminar el registro: " . $conexion->error]);
}

$conexion->close();
?>