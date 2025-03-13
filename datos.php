<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

require_once "conexion.php";
$conexion = new mysqli ($servername, $username, $passworddb, $dbname);

if ($conexion->connect_error) {
    die(json_encode(["error" => "Error de conexión: " . $con->connect_error]));
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Consulta a la base de datos de comida
$sql = "SELECT tipo_comida, raciones FROM comida";
$result = $conexion->query($sql);

$dataComida = [];
$dataComida[] = ['Topping', 'Slices'];

while ($row = $result->fetch_assoc()) {
    $dataComida[] = [$row['tipo_comida'], (int)$row['raciones']];
}

// Consulta a la base de datos de hipoglucemia
$sqlHipo = "SELECT glucosa, hora, tipo_comida, fecha FROM hipoglucemia";
$resultHipo = $conexion->query($sqlHipo);

$dataHipo = [];
$dataHipo[] = ['Hora', 'Glucosa'];

while ($row = $resultHipo->fetch_assoc()) {
    $dataHipo[] = [(string)$row['hora'], (int)$row['glucosa']];
}

// Consulta a la base de datos de hiperglucemia
$sqlHiper = "SELECT glucosa, hora, correccion, tipo_comida, fecha FROM hiperglucemia";
$resultHiper = $conexion->query($sqlHiper);

$dataHiper = [];
$dataHiper[] = ['Hora', 'Glucosa'];

while ($row = $resultHiper->fetch_assoc()) {
    $dataHiper[] = [(string)$row['hora'], (int)$row['glucosa']];
}

// Enviar datos en formato JSON
echo json_encode([
    'comida' => $dataComida,
    'hipoglucemia' => $dataHipo,
    'hiperglucemia' => $dataHiper
]);
?>