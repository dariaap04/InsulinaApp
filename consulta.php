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

function consultarRegistros($conectada, $id_usu, $tipo_comida, $fecha_comida, $tabla) {
    $sql = "SELECT * FROM $tabla WHERE id_usu = ?";
    $params = [$id_usu];
    $types = "i";
    
    if (!empty($tipo_comida)) {
        $sql .= " AND tipo_comida = ?";
        $params[] = $tipo_comida;
        $types .= "s";
    }
    
    if (!empty($fecha_comida)) {
        $sql .= " AND fecha = ?";
        $params[] = $fecha_comida;
        $types .= "s";
    }
    
    $stmt = $conectada->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->get_result();
}

function mostrarTabla($result, $tipo) {
    if ($result->num_rows > 0) {
        echo "<div class='table-responsive'>
              <table class='table table-hover table-bordered custom-table'><thead><tr>
                <th>Tipo</th><th>Fecha</th>";
        
        // Verificar qué columnas tenemos en este tipo de registro
        $primera_fila = $result->fetch_assoc();
        $result->data_seek(0); // Volver al principio del resultado
        
        // Columnas comunes para todos los tipos
        if (isset($primera_fila['hora'])) {
            echo "<th>Hora</th>";
        }
        
        // Columnas específicas según el tipo
        if ($tipo === 'comida') {
            // Para comidas, mostrar glucemias, raciones e insulina
            echo "<th>Glucemia 1h</th><th>Glucemia 2h</th><th>Raciones</th><th>Insulina</th>";
        } else if ($tipo === 'hipoglucemia') {
            // Para hipoglucemia, mostrar glucosa
            echo "<th>Glucosa</th>";
        } else if ($tipo === 'hiperglucemia') {
            // Para hiperglucemia, mostrar glucosa y corrección
            echo "<th>Glucosa</th><th>Corrección</th>";
        }
        
        echo "<th class='text-center'>Acciones</th></tr></thead><tbody>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td><span class='badge bg-primary'>{$row['tipo_comida']}</span></td>
                    <td>" . date('d/m/Y', strtotime($row['fecha'])) . "</td>";
            
            // Mostrar hora si existe
            if (isset($row['hora'])) {
                echo "<td>{$row['hora']}</td>";
            }
            
            // Mostrar campos específicos según el tipo
            if ($tipo === 'comida') {
                // Glucemia 1h
                echo "<td>" . (isset($row['gl_1h']) ? $row['gl_1h'] : '-') . "</td>";
                // Glucemia 2h
                echo "<td>" . (isset($row['gl_2h']) ? $row['gl_2h'] : '-') . "</td>";
                // Raciones
                echo "<td>" . (isset($row['raciones']) ? $row['raciones'] : '-') . "</td>";
                // Insulina
                echo "<td>" . (isset($row['insulina']) ? $row['insulina'] : '-') . "</td>";
            } else if ($tipo === 'hipoglucemia') {
                // Glucosa
                echo "<td>" . (isset($row['glucosa']) ? $row['glucosa'] : '-') . "</td>";
            } else if ($tipo === 'hiperglucemia') {
                // Glucosa
                echo "<td>" . (isset($row['glucosa']) ? $row['glucosa'] : '-') . "</td>";
                // Corrección
                echo "<td>" . (isset($row['correccion']) ? $row['correccion'] : '-') . "</td>";
            }
            
            echo "<td class='text-center'>
                    <div class='btn-group' role='group'>
                       <a href='edit.php?tipo_comida={$tipo}&id_usu={$row['tipo_comida']}' class='btn btn-warning btn-sm rounded-pill me-2' title='Editar'>
                            <i class='bi bi-pencil-fill'></i> Editar
                        </a>
                        <button class='btn btn-danger btn-sm rounded-pill eliminar' data-tabla='{$tipo}' data-id='{$row['tipo_comida']}' title='Eliminar'>
                            <i class='bi bi-trash-fill'></i> Eliminar
                        </button>
                    </div>
                  </td>
                </tr>";
        }
        echo "</tbody></table></div>";
        echo "<div class='text-end mb-4'><span class='badge bg-light text-dark'>Total: {$result->num_rows} registros</span></div>";
    } else {
        echo "<div class='alert alert-warning'>
                <i class='bi bi-exclamation-triangle-fill me-2'></i>No hay registros disponibles para {$tipo}.
              </div>";
    }
}

$conectada = myConexion();

$id_usu = consultarIdUsu($conectada);
$tipo_comida = isset($_POST['tipo_comida']) ? $_POST['tipo_comida'] : '';
$fecha_comida = isset($_POST['fecha_comida']) ? $_POST['fecha_comida'] : '';

$comidas = consultarRegistros($conectada, $id_usu, $tipo_comida, $fecha_comida, "comida");
$hipoglucemia = consultarRegistros($conectada, $id_usu, $tipo_comida, $fecha_comida, "hipoglucemia");
$hiperglucemia = consultarRegistros($conectada, $id_usu, $tipo_comida, $fecha_comida, "hiperglucemia");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Glucemia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root {
            --primary-color: #6a11cb; /* Morado moderno */
            --secondary-color: #2575fc; /* Azul vibrante */
            --accent-color: #ff6f61; /* Coral */
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --gradient-primary: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        body {
            background-color: var(--light-color);
            font-family: 'Poppins', sans-serif;
            color: var(--dark-color);
            padding-top: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .page-header {
            background: var(--gradient-primary);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .page-header h2 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }

        .page-header a {
            color: white;
            border: 1px solid white;
            border-radius: 8px;
            padding: 8px 16px;
            text-decoration: none;
            transition: all 0.3s;
        }

        .page-header a:hover {
            background: white;
            color: var(--primary-color);
        }

        .section-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .section-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        h4 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 20px;
            position: relative;
            padding-left: 15px;
        }

        h4::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 6px;
            height: 18px;
            background: var(--accent-color);
            border-radius: 3px;
        }

        .custom-table {
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 0;
            box-shadow: none;
        }

        .custom-table thead {
            background: var(--gradient-primary);
            color: white;
        }

        .custom-table th {
            font-weight: 600;
            border-bottom: none;
        }

        .custom-table tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }

        .badge {
            padding: 6px 10px;
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        .badge.bg-primary {
            background: var(--gradient-primary) !important;
        }

        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-outline-secondary {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-outline-secondary:hover {
            background: #f0f0f0;
            transform: translateY(-2px);
        }

        .btn-warning {
            background: #ffd166;
            border: none;
            color: var(--dark-color);
        }

        .btn-danger {
            background: var(--accent-color);
            border: none;
        }

        .alert {
            border-radius: 10px;
            border-left: 4px solid;
            padding: 15px 20px;
        }

        .alert-warning {
            background-color: rgba(255, 209, 102, 0.2);
            border-left-color: #ffd166;
            color: #856404;
        }

        /* Animaciones */
        .fade-in {
            opacity: 0;
            animation: fadeIn 0.5s ease-in forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Estilos para dispositivos móviles */
        @media (max-width: 768px) {
            .page-header h2 {
                font-size: 1.5rem;
            }

            .section-card {
                padding: 15px;
            }

            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <div class="page-header d-flex justify-content-between align-items-center">
        <h2><i class="bi bi-clipboard2-pulse me-2"></i>Sistema de Control de Glucemia</h2>
        <a href="index.html" class="btn btn-outline-light">
            <i class="bi bi-house-door-fill me-1"></i> Inicio
        </a>
        <div>
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="consulta.php">
                                <i class="fas fa-table me-1"></i> Consulta
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="aniadir.html">
                                <i class="fas fa-plus-circle me-1"></i> Añadir
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="verDatos.php">
                                <i class="fas fa-chart-bar me-1"></i> Estadísticas
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        </div>
    </div>
   

    <div class="filtro-form fade-in">
        <form method="post" class="mb-3">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="tipo_comida" class="form-label fw-semibold"><i class="bi bi-filter me-1"></i>Tipo de Comida:</label>
                    <select name="tipo_comida" class="form-select">
                        <option value="">Todos los tipos</option>
                        <option value="desayuno" <?php echo ($tipo_comida == 'desayuno') ? 'selected' : ''; ?>>Desayuno</option>
                        <option value="almuerzo" <?php echo ($tipo_comida == 'almuerzo') ? 'selected' : ''; ?>>Almuerzo</option>
                        <option value="cena" <?php echo ($tipo_comida == 'cena') ? 'selected' : ''; ?>>Cena</option>
                        <option value="merienda" <?php echo ($tipo_comida == 'merienda') ? 'selected' : ''; ?>>Merienda</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="fecha_comida" class="form-label fw-semibold"><i class="bi bi-calendar-event me-1"></i>Fecha:</label>
                    <input type="date" name="fecha_comida" class="form-control" value="<?php echo htmlspecialchars($fecha_comida); ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search me-1"></i>Filtrar
                    </button>
                    <a href="consulta.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="section-card fade-in">
        <h4>Registros de Comidas</h4>
        <?php mostrarTabla($comidas, "comida"); ?>
    </div>
    
    <div class="section-card fade-in">
        <h4>Registros de Hipoglucemia</h4>
        <?php mostrarTabla($hipoglucemia, "hipoglucemia"); ?>
    </div>
    
    <div class="section-card fade-in">
        <h4>Registros de Hiperglucemia</h4>
        <?php mostrarTabla($hiperglucemia, "hiperglucemia"); ?>
    </div>
    
    <footer class="text-center text-muted my-4">
        <small>&copy; <?php echo date('Y'); ?> Sistema de Control de Glucemia</small>
    </footer>
</div>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar este registro? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast para notificaciones -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="liveToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="bi bi-check-circle-fill text-success me-2"></i>
            <strong class="me-auto">Notificación</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Registro eliminado correctamente.
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Variables para almacenar los datos del registro a eliminar
    let deleteId, deleteTabla;
    
    // Modal de confirmación para eliminar
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    
    // Toast para notificaciones
    const toastLiveExample = document.getElementById('liveToast');
    
    $(document).on("click", ".eliminar", function() {
        deleteId = $(this).data("id");
        deleteTabla = $(this).data("tabla");
        deleteModal.show();
    });
    
    document.getElementById('confirmDelete').addEventListener('click', function() {
        $.post("eliminar_registro.php", { id: deleteId, tabla: deleteTabla }, function(response) {
            const data = JSON.parse(response);
            
            if (data.success) {
                // Ocultar el modal
                deleteModal.hide();
                
                // Mostrar notificación
                const toast = new bootstrap.Toast(toastLiveExample);
                toast.show();
                
                // Recargar la página después de un breve retraso
                setTimeout(function() {
                    location.reload();
                }, 1500);
            } else {
                alert("Error: " + data.error);
            }
        });
    });
    
    // Añadir animaciones a los elementos de la página
    document.addEventListener('DOMContentLoaded', function() {
        const sections = document.querySelectorAll('.fade-in');
        sections.forEach((section, index) => {
            setTimeout(() => {
                section.style.opacity = '1';
            }, 100 * index);
        });
    });
</script>
</body>
</html>