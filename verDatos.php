<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos Estadísticos - Control Glucosa</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6366f1;
            --secondary-color: #22c55e;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #3b82f6;
            --light-color: #f8fafc;
            --dark-color: #1e293b;
            --bg-gradient: linear-gradient(135deg, #f0f4ff 0%, #e2efff 100%);
            --card-gradient: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
            --primary-gradient: linear-gradient(45deg, #6366f1 0%, #818cf8 100%);
            --secondary-gradient: linear-gradient(45deg, #22c55e 0%, #4ade80 100%);
            --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            --border-radius: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--bg-gradient);
            color: var(--dark-color);
            line-height: 1.6;
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            padding-bottom: 80px;
            position: relative;
        }

        .container {
            max-width: 1280px;
            margin: auto;
            padding: 20px;
        }

        /* Estilo de encabezado con gráfico decorativo */
        .page-header {
            background: var(--primary-gradient);
            color: white;
            border-radius: var(--border-radius);
            margin-bottom: 40px;
            padding: 30px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 300px;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='rgba(255, 255, 255, 0.1)' fill-opacity='1' d='M0,224L48,218.7C96,213,192,203,288,181.3C384,160,480,128,576,138.7C672,149,768,203,864,224C960,245,1056,235,1152,202.7C1248,171,1344,117,1392,90.7L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E");
            background-size: cover;
            opacity: 0.8;
        }

        .page-header h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0;
            position: relative;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .page-header h1 i {
            font-size: 2rem;
        }

        .page-header p {
            margin: 10px 0 0;
            font-weight: 300;
            max-width: 60%;
            position: relative;
        }

        /* Barra de navegación moderna y elevada */
        .navbar {
            background: white;
            box-shadow: var(--shadow);
            border-radius: var(--border-radius);
            margin-bottom: 30px;
            padding: 15px 25px;
            position: relative;
        }
        
        .navbar-brand {
            color: var(--primary-color) !important;
            font-weight: 700;
            font-size: 1.5rem;
            font-family: 'Montserrat', sans-serif;
            letter-spacing: -0.5px;
        }
        
        .nav-link {
            font-weight: 500;
            transition: all 0.3s ease;
            border-radius: 8px;
            padding: 8px 16px !important;
            margin: 0 5px;
            position: relative;
        }
        
        .nav-link:hover {
            background-color: rgba(99, 102, 241, 0.08);
        }
        
        .nav-link.active {
            color: white !important;
            background: var(--primary-gradient);
        }
        
        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 5px;
            height: 5px;
            background-color: var(--primary-color);
            border-radius: 50%;
        }

        /* Tarjetas de estadísticas visualmente atractivas */
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--card-gradient);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 25px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            border-top: 4px solid var(--primary-color);
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.15);
        }

        .stat-card:nth-child(2) {
            border-top-color: var(--secondary-color);
        }

        .stat-card:nth-child(3) {
            border-top-color: var(--warning-color);
        }

        .stat-card .stat-icon {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 1.5rem;
            color: rgba(99, 102, 241, 0.15);
            z-index: 0;
        }

        .stat-card:nth-child(2) .stat-icon {
            color: rgba(34, 197, 94, 0.15);
        }

        .stat-card:nth-child(3) .stat-icon {
            color: rgba(245, 158, 11, 0.15);
        }

        .stat-card .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
            background: var(--primary-gradient);
            /* -webkit-background-clip: text; */
            -webkit-text-fill-color: transparent;
            position: relative;
            z-index: 1;
        }

        .stat-card:nth-child(2) .stat-value {
            background: var(--secondary-gradient);
            /* -webkit-background-clip: text; */
            -webkit-text-fill-color: transparent;
        }

        .stat-card:nth-child(3) .stat-value {
            background: linear-gradient(45deg, #f59e0b 0%, #fbbf24 100%);
            /* -webkit-background-clip: text; */
            -webkit-text-fill-color: transparent;
        }

        .stat-card .stat-label {
            font-size: 1rem;
            color: #64748b;
            font-weight: 500;
            position: relative;
            z-index: 1;
        }

        .stat-card .stat-change {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 10px;
            position: relative;
            z-index: 1;
        }

        .stat-card .stat-change.positive {
            background-color: rgba(34, 197, 94, 0.1);
            color: #22c55e;
        }

        .stat-card .stat-change.negative {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .stat-card .stat-change i {
            margin-right: 5px;
        }

        /* Tarjetas de gráficos con efectos visuales mejorados */
        .charts-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .chart-card {
            background: var(--card-gradient);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 25px;
            transition: all 0.3s ease;
            position: relative;
            min-height: 400px;
            display: flex;
            flex-direction: column;
        }

        .chart-card::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: var(--primary-gradient);
            border-radius: 0 0 var(--border-radius) var(--border-radius);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .chart-card:hover::before {
            opacity: 1;
        }

        .chart-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.15);
        }

        .chart-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px dashed rgba(99, 102, 241, 0.2);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chart-title i {
            color: var(--primary-color);
            font-size: 1.2rem;
        }

        /* Botones de acción visualmente atractivos */
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 40px;
        }

        .btn-action {
            padding: 15px 30px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            font-family: 'Montserrat', sans-serif;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 8px 20px -5px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn-action::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
            z-index: -1;
            transition: all 0.3s ease;
        }

        .btn-action:hover::before {
            transform: translateY(-100%);
        }

        .btn-action::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 10%;
            width: 80%;
            height: 10px;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            filter: blur(5px);
            z-index: -2;
            transition: all 0.3s ease;
        }

        .btn-action:hover::after {
            width: 90%;
            left: 5%;
        }

        .btn-action i {
            font-size: 1.2rem;
        }

        .btn-add { 
            background: var(--secondary-gradient);
            color: white; 
        }
        
        .btn-logout { 
            background: linear-gradient(45deg, #1e293b 0%, #334155 100%);
            color: white; 
        }
        
        .btn-action:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 15px 25px -5px rgba(0, 0, 0, 0.3);
        }

        /* Pie de página con diseño moderno */
        footer {
            text-align: center;
            padding: 25px 20px;
            background: white;
            box-shadow: var(--shadow);
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            z-index: 10;
        }

        footer p {
            margin: 0;
            color: #64748b;
        }

        footer strong {
            color: var(--primary-color);
        }

        /* Filtros y controles */
        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .date-filters {
            display: flex;
            gap: 10px;
        }

        .date-filter {
            padding: 8px 16px;
            border: none;
            border-radius: 20px;
            background: white;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .date-filter.active {
            background: var(--primary-gradient);
            color: white;
        }

        .export-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 20px;
            background: white;
            color: var(--primary-color);
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .export-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* Adaptación para móviles */
        @media (max-width: 768px) {
            .charts-row {
                grid-template-columns: 1fr;
            }
            
            .page-header p {
                max-width: 100%;
            }
            
            .page-header::before {
                width: 100%;
                opacity: 0.2;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn-action {
                width: 100%;
                justify-content: center;
            }
            
            .stat-card .stat-value {
                font-size: 2rem;
            }
            
            .controls {
                flex-direction: column;
                align-items: stretch;
            }
            
            .date-filters {
                justify-content: center;
            }
        }

        /* Animaciones */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .pulse {
            animation: pulse 2s infinite;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Navbar visualmente mejorado -->
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <i class="fas fa-heartbeat me-2"></i>Control Glucosa
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.html">
                                <i class="fas fa-home me-1"></i> Inicio
                            </a>
                        </li>
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

        <!-- Encabezado visual -->
        <div class="page-header">
            <h1><i class="fas fa-chart-line"></i> Datos Estadísticos</h1>
            <p>Visualiza todos tus datos de control de glucosa en un solo lugar para tomar decisiones informadas sobre tu salud.</p>
        </div>

        <!-- Controles y filtros -->
        <div class="controls">
           <!--  <div class="date-filters">
                <button class="date-filter active">7 días</button>
                <button class="date-filter">30 días</button>
                <button class="date-filter">90 días</button>
                <button class="date-filter">Todo</button>
            </div> -->
            <!-- <button class="export-btn">
                <i class="fas fa-download"></i> Exportar datos
            </button> -->
        </div>

        <!-- Tarjetas de estadísticas con efectos visuales -->
        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-utensils fa-3x"></i>
                </div>
                <h2 class="stat-value">158</h2>
                <p class="stat-label">Registros alimenticios</p>
                <span class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> 12% vs mes anterior
                </span>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle fa-3x"></i>
                </div>
                <h2 class="stat-value">82%</h2>
                <p class="stat-label">En rango objetivo</p>
                <span class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> 5% vs mes anterior
                </span>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle fa-3x"></i>
                </div>
                <h2 class="stat-value">8</h2>
                <p class="stat-label">Alertas este mes</p>
                <span class="stat-change negative">
                    <i class="fas fa-arrow-down"></i> 25% vs mes anterior
                </span>
            </div>
        </div>

        <!-- Gráficos con diseño mejorado -->
        <div class="charts-row">
            <div class="chart-card">
                <div class="chart-title">
                    <i class="fas fa-pizza-slice"></i> Consumo de Comida
                </div>
                <div id="chart_div" style="flex: 1; height: 300px;"></div>
            </div>
            <div class="chart-card">
                <div class="chart-title">
                    <i class="fas fa-arrow-down"></i> Hipoglucemia
                </div>
                <div id="hipo_div" style="flex: 1; height: 300px;"></div>
            </div>
            <div class="chart-card">
                <div class="chart-title">
                    <i class="fas fa-arrow-up"></i> Hiperglucemia
                </div>
                <div id="hiper_div" style="flex: 1; height: 300px;"></div>
            </div>
        </div>

        <!-- Botones de acción con efectos visuales -->
        <div class="action-buttons">
            <button class="btn-action btn-add" onclick="location.href='consulta.php'">
                <i class="fas fa-search"></i> Consultar más Datos
            </button>
            <button class="btn-action btn-logout" onclick="location.href='index.html'">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </button>
        </div>
    </div>

    <!-- Pie de página mejorado -->
    <footer>
        <p>&copy; 2025 <strong>Control Glucosa</strong> • Todos los derechos reservados</p>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        google.charts.load('current', {'packages':['corechart', 'bar'], 'language': 'es'});
        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts() {
            // Simulación de datos hasta que la API esté disponible
            const comidaData = [
                ['Tipo', 'Cantidad'],
                ['Desayuno', 28],
                ['Almuerzo', 33],
                ['Merienda', 18],
                ['Cena', 30],
                ['Snacks', 15]
            ];
            
            const hipoData = [
                ['Día', 'Eventos'],
                ['Lun', 2],
                ['Mar', 1],
                ['Mié', 0],
                ['Jue', 3],
                ['Vie', 1],
                ['Sáb', 0],
                ['Dom', 1]
            ];
            
            const hiperData = [
                ['Día', 'Eventos'],
                ['Lun', 1],
                ['Mar', 2],
                ['Mié', 3],
                ['Jue', 1],
                ['Vie', 0],
                ['Sáb', 1],
                ['Dom', 0]
            ];
            
            // Configuración visual mejorada para los gráficos
            drawPieChart('chart_div', comidaData);
            drawColumnChart('hipo_div', hipoData, '#6366f1');
            drawColumnChart('hiper_div', hiperData, '#ef4444');
            
            // Intento de cargar datos reales
            fetch('datos.php')
                .then(response => response.json())
                .then(data => {
                    drawPieChart('chart_div', data.comida);
                    drawColumnChart('hipo_div', data.hipoglucemia, '#6366f1');
                    drawColumnChart('hiper_div', data.hiperglucemia, '#ef4444');
                })
                .catch(error => console.log('Usando datos de ejemplo'));
        }

        function drawPieChart(elementId, chartData) {
            var dataTable = google.visualization.arrayToDataTable(chartData);
            
            var options = { 
                pieHole: 0.4,
                width: '100%',
                height: 300,
                legend: { position: 'bottom' },
                chartArea: { width: '90%', height: '75%' },
                colors: ['#6366f1', '#22c55e', '#f59e0b', '#ef4444', '#a855f7'],
                fontName: 'Poppins',
                backgroundColor: { fill:'transparent' },
                pieSliceTextStyle: {
                    color: 'white',
                    fontName: 'Poppins',
                    fontSize: 14
                },
                animation: {
                    startup: true,
                    duration: 1000,
                    easing: 'out',
                },
                is3D: false,
                slices: {
                    0: { offset: 0.05 }
                }
            };
            
            var chart = new google.visualization.PieChart(document.getElementById(elementId));
            chart.draw(dataTable, options);
        }

        function drawColumnChart(elementId, chartData, color) {
            var dataTable = google.visualization.arrayToDataTable(chartData);
            
            var options = { 
                width: '100%',
                height: 300,
                legend: { position: 'none' },
                chartArea: { width: '85%', height: '75%' },
                colors: [color],
                fontName: 'Poppins',
                backgroundColor: { fill:'transparent' },
                animation: {
                    startup: true,
                    duration: 1000,
                    easing: 'out',
                },
                bar: { groupWidth: '70%' },
                vAxis: { 
                    minValue: 0,
                    gridlines: {
                        color: '#f1f5f9',
                        count: 4
                    }
                },
                hAxis: {
                    gridlines: {
                        color: 'transparent'
                    }
                }
            };
            
            var chart = new google.visualization.ColumnChart(document.getElementById(elementId));
            chart.draw(dataTable, options);
        }

        // Interactividad para los filtros
        document.querySelectorAll('.date-filter').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.date-filter').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
                // Aquí se podría añadir la lógica para cambiar los datos según el filtro
            });
        });
    </script>
</body>
</html>