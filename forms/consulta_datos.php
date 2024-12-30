<?php
// Configuración de la conexión a la base de datos
$host = "localhost";
$dbname = "meta_mercado_talpetate";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$resultados = [];
$mensaje = '';

// Función para obtener los meses entre la fecha de inicio de contrato y la fecha actual
function obtenerMesesContrato($fechaInicio) {
        $fechaInicioObj = new DateTime($fechaInicio);
        $fechaActualObj = new DateTime();
         $fechaActualObj->modify('last day of this month');
        $intervalo = $fechaInicioObj->diff($fechaActualObj);
        $meses = ($intervalo->y * 12) + $intervalo->m +1;
        $fechas = [];
      for ($i = 0; $i < $meses; $i++) {
             $fecha = clone $fechaInicioObj;
           $fecha->modify("+$i month");
            $fechas[]= $fecha->format('Y-m-01');
        }

    return $fechas;
}

// Procesar la búsqueda
if (isset($_POST['buscar'])) {
    $dpi = trim($_POST['dpi']);
    if (!empty($dpi)) {
        try {
            $sql = "
                SELECT 
                    CONCAT(a.nombres, ' ', a.apellidos) AS nombre_completo, 
                    a.dpi, 
                    c.numero_contrato, 
                    CONCAT(c.numero_local, ' - ', c.bloque) AS local_bloque,
                    c.area, 
                    DATE_FORMAT(c.fecha_inicio, '%Y-%m-%d') AS fecha_inicio, 
                    DATE_FORMAT(c.fecha_fin, '%d-%m-%y') AS fecha_fin, 
                    c.giro_negocio
                FROM 
                    arrendatarios a
                INNER JOIN 
                    contrato c ON a.dpi = c.dpi
                 WHERE 
                    a.dpi = :dpi
            ";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':dpi', $dpi, PDO::PARAM_STR);
            $stmt->execute();
            $resultados_contrato = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultados_contrato) {
                 $sql_pagos = "SELECT
                                mes_pago,
                                monto_tasa_municipal,
                                monto_arbitrio,
                                DATE_FORMAT(fecha_pago, '%d/%m/%y') AS fecha_pago,
                                estado_pago
                              FROM 
                                  registro_pagos_arrendamiento 
                              WHERE 
                                 dpi_arrendatario = :dpi
                              ORDER BY mes_pago";
                $stmt_pagos = $pdo->prepare($sql_pagos);
                $stmt_pagos->bindParam(':dpi', $dpi, PDO::PARAM_STR);
                $stmt_pagos->execute();
                $resultados_pagos = $stmt_pagos->fetchAll(PDO::FETCH_ASSOC);


                 $meses = obtenerMesesContrato($resultados_contrato['fecha_inicio']);
                 $meses_pagados = [];
                    foreach($resultados_pagos as $pago){
                       $meses_pagados[$pago['mes_pago']] = $pago;
                    }

                $resultados = ['contrato' => $resultados_contrato, 'meses' => $meses, 'pagos' => $meses_pagados ];
             } else {
                $mensaje = "No se encontraron registros para el DPI ingresado.";
            }
        } catch(PDOException $e) {
            $mensaje = "Error en la búsqueda: " . $e->getMessage();
        }
    } else {
        $mensaje = "Por favor, ingresa un DPI válido.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Datos</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
     <link rel="stylesheet" href="../css/style.css">
    <style>
         body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-family: 'Arial', sans-serif;
             display: flex;
            flex-direction: column;
            min-height: 100vh; /* Asegura que el contenido cubra toda la pantalla */
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
            flex-direction: column;
             justify-content: center;
        }
        .search-button {
            width: 200px;
            height: 50px;
             background-color: #4ade80;
             transition: background-color 0.3s ease;
            color: white;
             font-size: 16px;
            border: none;
             border-radius: 10px;
             cursor: pointer;
         }

        .search-button:hover {
            background-color: #22c55e;
        }

        .result-card {
              background: rgba(255, 255, 255, 0.95);
             border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
             padding: 30px;
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

        th, td {
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
         .logo-container img{
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
        <h1 class="text-3xl font-bold text-blue-600 mb-6 text-center">Consulta de Datos</h1>

        <form method="POST" action="" class="bg-white p-6 rounded-lg shadow-md flex justify-center items-center space-x-4">
            <div class="flex flex-col mr-4">
               <label for="dpi" class="block text-lg font-semibold mb-2">Buscar por DPI:</label>
                <input 
                     type="text" 
                    id="dpi" 
                   name="dpi" 
                    class="w-72 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" 
                    placeholder="Ingresa el DPI" 
                    required
                >
            </div>
            <button type="submit" name="buscar" class="search-button">
                 Buscar
            </button>
        </form>

       <?php if (isset($mensaje) && !empty($mensaje)): ?>
         <div class="mt-4 text-red-500 text-center">
            <?= htmlspecialchars($mensaje) ?>
         </div>
      <?php endif; ?>

        <?php if (!empty($resultados)): ?>
              <div class="result-card">
                   <h2 class="text-2xl font-bold mb-4 text-green-600">Estado de Cuenta</h2>
                    
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th class="relative" data-resizable="true">
                                      Nombre Completo
                                      <div class="resize-handle"></div>
                                    </th>
                                     <th class="relative" data-resizable="true">
                                        DPI
                                       <div class="resize-handle"></div>
                                   </th>
                                   <th class="relative" data-resizable="true">
                                        Contrato
                                       <div class="resize-handle"></div>
                                     </th>
                                     <th class="relative" data-resizable="true">
                                      Local - Bloque
                                         <div class="resize-handle"></div>
                                   </th>
                                   <th class="relative" data-resizable="true">
                                        Giro de Negocio
                                        <div class="resize-handle"></div>
                                   </th>
                                     <th class="relative" data-resizable="true">
                                      Inicio Contrato
                                         <div class="resize-handle"></div>
                                   </th>
                                   <th class="relative" data-resizable="true">
                                        Fin Contrato
                                         <div class="resize-handle"></div>
                                     </th>
                                </tr>
                            </thead>
                           <tbody>
                              <tr>
                                   <td><?= htmlspecialchars($resultados['contrato']['nombre_completo']) ?></td>
                                   <td><?= htmlspecialchars($resultados['contrato']['dpi']) ?></td>
                                   <td><?= htmlspecialchars($resultados['contrato']['numero_contrato']) ?></td>
                                    <td><?= htmlspecialchars($resultados['contrato']['local_bloque']) ?></td>
                                      <td><?= htmlspecialchars($resultados['contrato']['giro_negocio']) ?></td>
                                     <td><?= htmlspecialchars($resultados['contrato']['fecha_inicio']) ?></td>
                                      <td><?= htmlspecialchars($resultados['contrato']['fecha_fin']) ?></td>
                                </tr>
                            </tbody>
                         </table>
                   </div>
                     <div class="mt-4">
                         <h3 class="text-xl font-semibold">Pagos Mensuales</h3>
                         <div class="table-container">
                            <table>
                                 <thead>
                                   <tr>
                                        <th class="relative" data-resizable="true">
                                           Mes
                                           <div class="resize-handle"></div>
                                        </th>
                                        <th class="relative" data-resizable="true">
                                             Tasa Municipal
                                            <div class="resize-handle"></div>
                                        </th>
                                        <th class="relative" data-resizable="true">
                                             Arbitrios
                                            <div class="resize-handle"></div>
                                       </th>
                                       <th class="relative" data-resizable="true">
                                             Fecha Pago
                                            <div class="resize-handle"></div>
                                       </th>
                                       <th class="relative" data-resizable="true">
                                             Estado Pago
                                          <div class="resize-handle"></div>
                                        </th>
                                  </tr>
                             </thead>
                               <tbody>
                                   <?php
                                     foreach ($resultados['meses'] as $mes) {
                                         $fecha = $mes;
                                         if(isset($resultados['pagos'][$mes]))
                                          {
                                            $pago = $resultados['pagos'][$mes];
                                        ?>
                                              <tr>
                                                  <td><?= htmlspecialchars(date('M-Y', strtotime($pago['mes_pago']))) ?></td>
                                                  <td><?= htmlspecialchars($pago['monto_tasa_municipal']) ?></td>
                                                 <td><?= htmlspecialchars($pago['monto_arbitrio']) ?></td>
                                                  <td><?= htmlspecialchars($pago['fecha_pago']) ?></td>
                                                   <td><?= htmlspecialchars($pago['estado_pago']) ?></td>
                                            </tr>
                                   <?php

                                     }
                                       else
                                         {
                                           ?>
                                             <tr>
                                                 <td><?= htmlspecialchars(date('M-Y', strtotime($fecha))) ?></td>
                                                 <td></td>
                                                  <td></td>
                                                   <td></td>
                                                 <td class="text-red-500">Pendiente</td>
                                          </tr>
                                     <?php
                                      }
                                   }
                              ?>
                             </tbody>
                             </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
             <a href="../menu.php" class="back-button">Regresar al Menú</a>
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