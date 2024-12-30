<?php
// Habilitar la visualización de errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Archivo de conexión
require 'conexion.php';

// Obtener id del mercado por la variable GET
$id_mercado = isset($_GET['id_mercado']) ? $_GET['id_mercado'] : null;

// Obtener el nombre del mercado
$nombre_mercado = "";
if ($id_mercado) {
    try {
        $stmt = $conexion->prepare("SELECT nombre_mercado FROM mercado WHERE id_mercado = :id_mercado");
        $stmt->bindParam(':id_mercado', $id_mercado, PDO::PARAM_INT);
        $stmt->execute();
        $mercado = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($mercado) {
            $nombre_mercado = $mercado['nombre_mercado'];
        }
    } catch (PDOException $e) {
        $nombre_mercado = "Error al obtener el nombre del mercado: " . $e->getMessage();
    }
}

$mensaje = '';

// Función para obtener las columnas de una tabla
function getTableColumns($conexion, $table)
{
    $stmt = $conexion->prepare("DESCRIBE $table");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
}

// Función para ejecutar la consulta y obtener los datos
function fetchData($conexion, $table, $filters)
{
    $sql = "SELECT * FROM $table WHERE 1=1";
    $params = [];
    if (isset($filters['mercado']) && $filters['mercado'] != "") {
        $sql .= " AND id_mercado = :mercado";
        $params[":mercado"] = $filters['mercado'];
    }
    foreach ($filters as $columna => $valor) {
        if (is_numeric($valor)) {
            // Filtros numéricos
            $minimo_key = "min_" . $columna;
            $maximo_key = "max_" . $columna;

            if (isset($_GET[$minimo_key]) && $_GET[$minimo_key] !== "") {
                $sql .= " AND $columna >= :$minimo_key";
                $params[":$minimo_key"] = $_GET[$minimo_key];
            }
            if (isset($_GET[$maximo_key]) && $_GET[$maximo_key] !== "") {
                $sql .= " AND $columna <= :$maximo_key";
                $params[":$maximo_key"] = $_GET[$maximo_key];
            }
        } else {
            // Filtros de texto
            if (isset($_GET[$columna]) && $_GET[$columna] !== "") {
                $sql .= " AND $columna LIKE :$columna";
                $params[":$columna"] = "%" . $_GET[$columna] . "%";
            }
        }
    }
    $stmt = $conexion->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener un resumen de datos (ej: conteo de registros)
function getSummaryData($conexion, $table, $filters)
{
    $sql = "SELECT COUNT(*) AS total FROM $table WHERE 1=1";
    $params = [];
    if (isset($filters['mercado']) && $filters['mercado'] != "") {
        $sql .= " AND id_mercado = :mercado";
        $params[":mercado"] = $filters['mercado'];
    }
    foreach ($filters as $columna => $valor) {
        if (is_numeric($valor)) {
            // Filtros numéricos
            $minimo_key = "min_" . $columna;
            $maximo_key = "max_" . $columna;

            if (isset($_GET[$minimo_key]) && $_GET[$minimo_key] !== "") {
                $sql .= " AND $columna >= :$minimo_key";
                $params[":$minimo_key"] = $_GET[$minimo_key];
            }
            if (isset($_GET[$maximo_key]) && $_GET[$maximo_key] !== "") {
                $sql .= " AND $columna <= :$maximo_key";
                $params[":$maximo_key"] = $_GET[$maximo_key];
            }
        } else {
            // Filtros de texto
            if (isset($_GET[$columna]) && $_GET[$columna] !== "") {
                $sql .= " AND $columna LIKE :$columna";
                $params[":$columna"] = "%" . $_GET[$columna] . "%";
            }
        }
    }
    $stmt = $conexion->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

// Tablas disponibles
$tables = ['contrato', 'registro_pagos_arrendamiento', 'tarjeta', 'arrendatarios'];

// Obtener la tabla seleccionada (por defecto: contrato)
$selected_table = isset($_GET['table']) ? $_GET['table'] : 'contrato';

// Validar la tabla seleccionada
if (!in_array($selected_table, $tables)) {
    $selected_table = 'contrato';
    $mensaje = "<p style='color:red;'>Tabla no valida, se mostrarán los resultados de la tabla contrato</p>";
}

// Obtener las columnas de la tabla seleccionada
$columns = getTableColumns($conexion, $selected_table);

// Inicializar el array de filtros
$filters = [];
if (isset($_GET['id_mercado'])) {
    $filters['mercado'] = $_GET['id_mercado'];
}
foreach ($columns as $columna) {
    // Crear input para filtros de texto y rangos numéricos
    if (isset($_GET[$columna])) {
        if (is_numeric($_GET[$columna])) {
            $minimo_key = "min_" . $columna;
            $maximo_key = "max_" . $columna;

            if (isset($_GET[$minimo_key]) && $_GET[$minimo_key] !== "") $filters[$minimo_key] = $_GET[$minimo_key];
            if (isset($_GET[$maximo_key]) && $_GET[$maximo_key] !== "") $filters[$maximo_key] = $_GET[$maximo_key];
        } else {
             if ($_GET[$columna] !== "") {
                $filters[$columna] = $_GET[$columna];
            }
        }
    }
}

// Obtener los datos filtrados
$data = fetchData($conexion, $selected_table, $filters);

// Obtener datos de resumen
$summary = getSummaryData($conexion, $selected_table, $filters);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            /* Asegura que el contenido cubra toda la pantalla */
        }

        .menu-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            padding: 30px;
            text-align: center;
            max-width: 1000px;
            width: 100%;
            margin: auto;
            display: flex;
            /* Habilitar flexbox */
            flex-direction: column;
            justify-content: center;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 2.2rem;
            font-weight: bold;
            color: #2d7e41;
        }

        .select-container {
            position: relative;
            display: inline-block;
            width: 100%;
            margin-bottom: 20px;
        }

        .select-container select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            padding: 12px 24px;
            border: 2px solid #4ade80;
            border-radius: 10px;
            background-color: white;
            font-size: 1.1rem;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
        }

        .select-container:after {
            content: '\25BC';
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            pointer-events: none;
            color: #4ade80;
            font-size: 1.2em;
            transition: all 0.3s ease;
        }

        .select-container:hover::after {
            color: #22c55e;
        }

        .select-container select:focus {
            outline: none;
            border-color: #22c55e;
        }

        .search-button {
            display: inline-block;
            background-color: #4ade80;
            transition: background-color 0.3s ease;
            color: white;
            padding: 12px 24px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
            width: 100%;
        }

        .search-button:hover {
            background-color: #22c55e;
        }

        .dashboard-card {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
        }

        .table-container {
            overflow-x: auto;
            margin-top: 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 16px;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
            position: relative;
        }

        tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .resize-handle {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            cursor: col-resize;
            background: rgba(0, 0, 0, 0.1);
        }

        .resize-handle:hover {
            background: rgba(0, 0, 0, 0.2);
        }

        .logo-container img {
            width: 180px;
            height: auto;
            margin-bottom: 20px;
        }

        .back-button {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #4ade80;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .back-button:hover {
            background-color: #22c55e;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col items-center justify-center p-4">
    <div class="w-full max-w-6xl">
        <div class="menu-card">
            <!-- Logo -->
            <div class="logo-container mb-6">
                <img src="../images/logo muni 2024.png" alt="Logo Municipalidad 2024" class="w-32 mx-auto">
            </div>
            <h1 class="text-3xl font-bold text-blue-600 mb-6 text-center">Dashboard de Contratos</h1>
            <form method="GET" action="" class="space-y-4">
                <div class="select-container">
                    <select name="id_mercado" onchange="this.form.submit()">
                        <option value="" disabled selected hidden>Selecciona un mercado</option>
                        <?php
                        try {
                            $stmt = $conexion->query("SELECT id_mercado, nombre_mercado FROM mercado");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='{$row['id_mercado']}'";
                                if (isset($_GET['id_mercado']) && $_GET['id_mercado'] == $row['id_mercado']) {
                                    echo " selected";
                                }
                                echo  ">{$row['nombre_mercado']}</option>";
                            }
                        } catch (PDOException $e) {
                            echo "Error al obtener la información de los mercados: " . $e->getMessage();
                        }
                        ?>
                    </select>
                </div>
            </form>

            <?php if (isset($mensaje) && !empty($mensaje)) : ?>
                <div class="mt-4 text-red-500 text-center">
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($data)) : ?>
                <div class="dashboard-card">
                    <h2 class="text-2xl font-bold mb-4 text-green-600">
                        Información de la tabla <?= htmlspecialchars($selected_table) ?> para el mercado <?= htmlspecialchars($nombre_mercado) ?>
                    </h2>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <?php foreach ($columns as $columna) : ?>
                                        <th class="relative" data-resizable="true">
                                            <?= $columna ?>
                                            <div class="resize-handle"></div>
                                            <?php if (is_numeric($data[0][$columna] ?? "")) : ?>
                                                <br>
                                                <input type="number" placeholder="min" name="min_<?= $columna ?>" value="<?= $_GET["min_" . $columna] ?? "" ?>" oninput="this.form.submit()">
                                                <input type="number" placeholder="max" name="max_<?= $columna ?>" value="<?= $_GET["max_" . $columna] ?? "" ?>" oninput="this.form.submit()">
                                            <?php else : ?>
                                                <br>
                                                <input type="text" placeholder="Buscar..." name="<?= $columna ?>" value="<?= $_GET[$columna] ?? "" ?>" oninput="this.form.submit()">
                                            <?php endif; ?>
                                        </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $row) : ?>
                                    <tr>
                                        <?php foreach ($columns as $columna) : ?>
                                            <td><?= htmlspecialchars($row[$columna] ?? '') ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="summary-card">
                        <p>Total de Registros: <?= $summary ?></p>
                    </div>
                </div>
            <?php endif; ?>
              <!-- Ruta relativa al menú -->
            <a href="menu.php" class="back-button">Regresar al Menú</a>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tables = document.querySelectorAll('table');

            tables.forEach(table => {
                const headers = table.querySelectorAll('th[data-resizable="true"]');

                headers.forEach(header => {
                    const resizer = header.querySelector('.resize-handle');
                    let startX, startWidth;

                    resizer.addEventListener('mousedown', function(e) {
                        startX = e.pageX;
                        startWidth = header.offsetWidth;

                        document.addEventListener('mousemove', onMouseMove);
                        document.addEventListener('mouseup', onMouseUp);
                        header.classList.add('resizing');
                    });

                    function onMouseMove(e) {
                        const width = startWidth + (e.pageX - startX);
                        if (width >= 100) {
                            header.style.width = width + 'px';
                            const index = Array.from(header.parentElement.children).indexOf(header);
                            const rows = table.querySelectorAll('tr');

                            rows.forEach(row => {
                                const cell = row.children[index];
                                if (cell) {
                                    cell.style.width = width + 'px';
                                }
                            });
                        }
                    }

                    function onMouseUp() {
                        document.removeEventListener('mousemove', onMouseMove);
                        document.removeEventListener('mouseup', onMouseUp);
                        header.classList.remove('resizing');
                    }
                });
            });
        });
    </script>
</body>

</html>