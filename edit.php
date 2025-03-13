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

if (!isset($_SESSION["usuario"])) {
    echo '<div class="alert alert-danger">Debe iniciar sesión.</div>';
    exit();
}

// Obtener el ID del usuario actual
function consultarIdUsu($conectada) {
    if (!isset($_SESSION["usuario"])) return null;
    $usuario = $_SESSION["usuario"];
    $sql = "SELECT id_usu FROM usuario WHERE usuario = ?";
    $stmt = $conectada->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    return ($row = $result->fetch_assoc()) ? $row['id_usu'] : null;
}

$conectada = myConexion();
$id_usu = consultarIdUsu($conectada);

// Verificar si se proporcionó un tipo de registro y un ID
if (!isset($_GET['tipo_comida']) || !isset($_GET['id_usu'])) {
    header("Location: consulta.php");
    exit();
}

$tipo_tabla = $_GET['tipo_comida']; // comida, hipoglucemia o hiperglucemia
$id_registro = $_GET['id_usu']; // ID del registro a editar

// Validar el tipo de tabla
$tablas_permitidas = ['comida', 'hipoglucemia', 'hiperglucemia'];
if (!in_array($tipo_tabla, $tablas_permitidas)) {
    header("Location: consulta.php");
    exit();
}

// Obtener los datos del registro
$sql = "SELECT * FROM $tipo_tabla WHERE id_usu = ? AND tipo_comida = ?";
$stmt = $conectada->prepare($sql);
$stmt->bind_param("is", $id_usu, $id_registro);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: consulta.php");
    exit();
}

$registro = $result->fetch_assoc();

// Procesar el formulario de actualización
$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar y sanitizar los datos de entrada
    $fecha = $_POST['fecha'];
    
    // Validar que los campos requeridos no estén vacíos
    if (empty($fecha)) {
        $mensaje = "La fecha es obligatoria.";
        $tipo_mensaje = "danger";
    } else {
        // Definir la consulta SQL básica - mantenemos el tipo_comida igual
        $sql = "UPDATE $tipo_tabla SET fecha = ?";
        $tipos = "s";
        $params = [$fecha];
        
        // Añadir la hora si existe en el formulario
        if (isset($_POST['hora']) && !empty($_POST['hora'])) {
            $hora = $_POST['hora'];
            $sql .= ", hora = ?";
            $tipos .= "s";
            $params[] = $hora;
        }
        
        // Añadir gl_1h si existe y no está vacío
        if (isset($_POST['gl_1h']) && $_POST['gl_1h'] !== '') {
            $gl_1h = $_POST['gl_1h'];
            $sql .= ", gl_1h = ?";
            $tipos .= "d";
            $params[] = $gl_1h;
        }
        
        // Añadir gl_2h si existe y no está vacío
        if (isset($_POST['gl_2h']) && $_POST['gl_2h'] !== '') {
            $gl_2h = $_POST['gl_2h'];
            $sql .= ", gl_2h = ?";
            $tipos .= "d";
            $params[] = $gl_2h;
        }
        
        // Añadir raciones si existe y no está vacío
        if (isset($_POST['raciones']) && $_POST['raciones'] !== '') {
            $raciones = $_POST['raciones'];
            $sql .= ", raciones = ?";
            $tipos .= "d";
            $params[] = $raciones;
        }
        
        // Añadir insulina si existe y no está vacío
        if (isset($_POST['insulina']) && $_POST['insulina'] !== '') {
            $insulina = $_POST['insulina'];
            $sql .= ", insulina = ?";
            $tipos .= "d";
            $params[] = $insulina;
        }
        
        // Completar la consulta con la condición WHERE
        $sql .= " WHERE id_usu = ? AND tipo_comida = ?";
        $tipos .= "is";
        $params[] = $id_usu;
        $params[] = $id_registro;
        
        // Preparar y ejecutar la consulta
        $stmt = $conectada->prepare($sql);
        $stmt->bind_param($tipos, ...$params);
        
        if ($stmt->execute()) {
            $mensaje = "Registro actualizado correctamente.";
            $tipo_mensaje = "success";
            
            // Redirigir después de un breve retraso
            header("Refresh: 2; URL=consulta.php");
        } else {
            $mensaje = "Error al actualizar el registro: " . $conectada->error;
            $tipo_mensaje = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Registro - Control de Glucemia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --success-color: #4cc9f0;
            --warning-color: #f72585;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }
        
        body {
            background-color: #f5f7ff;
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            padding-top: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .page-header {
            background-color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 8px;
            height: 100%;
            background: var(--primary-color);
        }
        
        h2 {
            color: var(--primary-color);
            margin-bottom: 0;
            font-weight: 600;
            font-size: 1.8rem;
        }
        
        .form-card {
            background-color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
            margin-bottom: 15px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        }
        
        .form-control-plaintext {
            padding: 10px 15px;
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 15px;
            color: #6c757d;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn-outline-secondary {
            color: var(--dark-color);
            border-color: #e0e0e0;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-outline-secondary:hover {
            background-color: #f0f0f0;
            color: var(--dark-color);
            transform: translateY(-2px);
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--secondary-color);
        }
        
        .alert {
            border-radius: 10px;
            border-left: 4px solid;
            padding: 15px 20px;
        }
        
        .alert-success {
            background-color: rgba(76, 201, 240, 0.1);
            border-left-color: var(--success-color);
        }
        
        .alert-danger {
            background-color: rgba(247, 37, 133, 0.1);
            border-left-color: var(--warning-color);
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Estilos para dispositivos móviles */
        @media (max-width: 768px) {
            .container {
                padding: 0 10px;
            }
            
            .page-header {
                padding: 15px;
                margin-bottom: 20px;
            }
            
            h2 {
                font-size: 1.5rem;
            }
            
            .form-card {
                padding: 15px;
            }
        }
        
        /* Estilo para campos numéricos */
        .form-control[type="number"] {
            text-align: right;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <div class="page-header d-flex justify-content-between align-items-center">
        <h2><i class="bi bi-pencil-square me-2"></i>Editar Registro</h2>
        <a href="consulta.php" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <?php if (!empty($mensaje)): ?>
    <div class="alert alert-<?php echo $tipo_mensaje; ?> fade-in">
        <?php echo $mensaje; ?>
    </div>
    <?php endif; ?>

    <div class="form-card fade-in">
        <form method="post" action="">
            <div class="mb-3">
                <label for="tipo_comida" class="form-label">
                    <i class="bi bi-tag me-1"></i>Tipo de Comida
                </label>
                <input type="text" class="form-control-plaintext" id="tipo_comida" value="<?php echo ucfirst(htmlspecialchars($registro['tipo_comida'])); ?>" readonly>
                <!-- Campo oculto para mantener el valor -->
                <input type="hidden" name="tipo_comida" value="<?php echo htmlspecialchars($registro['tipo_comida']); ?>">
            </div>

            <div class="mb-3">
                <label for="fecha" class="form-label">
                    <i class="bi bi-calendar-date me-1"></i>Fecha
                </label>
                <input type="date" name="fecha" id="fecha" class="form-control" value="<?php echo htmlspecialchars($registro['fecha']); ?>" required>
            </div>

            <?php if (isset($registro['hora'])): ?>
            <div class="mb-3">
                <label for="hora" class="form-label">
                    <i class="bi bi-clock me-1"></i>Hora
                </label>
                <input type="time" name="hora" id="hora" class="form-control" value="<?php echo htmlspecialchars($registro['hora']); ?>">
            </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="gl_1h" class="form-label">
                        <i class="bi bi-graph-up me-1"></i>Glucemia 1h
                    </label>
                    <input type="number" step="0.1" name="gl_1h" id="gl_1h" class="form-control" 
                           value="<?php echo isset($registro['gl_1h']) ? htmlspecialchars($registro['gl_1h']) : ''; ?>" 
                           placeholder="Glucemia después de 1h">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="gl_2h" class="form-label">
                        <i class="bi bi-graph-up-arrow me-1"></i>Glucemia 2h
                    </label>
                    <input type="number" step="0.1" name="gl_2h" id="gl_2h" class="form-control" 
                           value="<?php echo isset($registro['gl_2h']) ? htmlspecialchars($registro['gl_2h']) : ''; ?>" 
                           placeholder="Glucemia después de 2h">
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
    <label for="raciones" class="form-label">
        <i class="bi bi-ui-radios-grid me-1"></i>Raciones
    </label>
    <input type="number" step="0.1" name="raciones" id="raciones" class="form-control" 
           value="<?php echo isset($registro['raciones']) ? htmlspecialchars($registro['raciones']) : ''; ?>" 
           placeholder="Número de raciones">
</div>
                
<div class="col-md-6 mb-3">
    <label for="insulina" class="form-label">
        <i class="bi bi-droplet-fill me-1"></i>Insulina
    </label>
    <input type="number" step="0.1" name="insulina" id="insulina" class="form-control" 
           value="<?php echo isset($registro['insulina']) ? htmlspecialchars($registro['insulina']) : ''; ?>" 
           placeholder="Unidades de insulina">
</div>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="consulta.php" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>