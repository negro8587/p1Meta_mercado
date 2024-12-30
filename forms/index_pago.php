<?php
session_start();
    if (!isset($_SESSION['id_mercado'])) {
        header("Location: ../index.php");
        exit();
    }
 $id_mercado = $_SESSION['id_mercado'];

// archivo de conexión
require '../conexion.php';

  // Obtener el nombre del mercado
  try {
      $stmt = $conexion->prepare("SELECT nombre_mercado FROM mercado WHERE id_mercado = :id_mercado");
      $stmt->bindParam(':id_mercado', $id_mercado, PDO::PARAM_INT);
      $stmt->execute();
        $mercado = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$mercado) {
            $nombre_mercado = 'Mercado no encontrado';
        } else {
            $nombre_mercado = $mercado['nombre_mercado'];
        }
    } catch (PDOException $e) {
       $nombre_mercado = "Error al obtener el nombre del mercado: " . $e->getMessage();
    }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos enviados desde el formulario
     $id_mercado = $_POST['id_mercado'] ?? null;
     $dpi_arrendatario = $_POST['dpi'] ?? null;
     $mes_pago = $_POST['mes_pago'] ?? null;
    $fecha_pago = $_POST['fecha_pago'] ?? null;
    $monto_tasa_municipal = $_POST['monto_pago_tasa_municipal'] ?? null;
     $monto_arbitrio = $_POST['monto_pago_arbitrio'] ?? null;
    $estado_pago = $_POST['estado_pago'] ?? null;
     $observaciones = $_POST['observaciones'] ?? null;

     try {
           // Consulta para verificar si el pago ya existe
         $sql_verificar = "SELECT COUNT(*) FROM registro_pagos_arrendamiento WHERE dpi_arrendatario = :dpi_arrendatario AND mes_pago = :mes_pago";
         $stmt_verificar = $conexion->prepare($sql_verificar);
         $stmt_verificar->bindParam(':dpi_arrendatario', $dpi_arrendatario, PDO::PARAM_STR);
          // Formatear la fecha para la base de datos y guardarla con el formato YYYY-MM-01
            $mes_pago_formateado = date('Y-m-01', strtotime($mes_pago));
         $stmt_verificar->bindParam(':mes_pago', $mes_pago_formateado, PDO::PARAM_STR);
         $stmt_verificar->execute();
         $pago_existente = $stmt_verificar->fetchColumn();

          if ($pago_existente > 0) {
               echo "<script type='text/javascript'>
                      alert('Ya existe un pago registrado para este mes y arrendatario.');
                    </script>";
           }
        else{
             // Consulta SQL para insertar los datos
            $sql = "INSERT INTO registro_pagos_arrendamiento (id_mercado, dpi_arrendatario, mes_pago, fecha_pago, monto_tasa_municipal, monto_arbitrio, estado_pago, observaciones) 
                      VALUES (:id_mercado, :dpi_arrendatario, :mes_pago, :fecha_pago, :monto_tasa_municipal, :monto_arbitrio, :estado_pago, :observaciones)";

            // Prepara la consulta
            $stmt = $conexion->prepare($sql);

            // Vincula los parámetros
             $stmt->bindParam(':id_mercado', $id_mercado, PDO::PARAM_INT);
             $stmt->bindParam(':dpi_arrendatario', $dpi_arrendatario, PDO::PARAM_STR);
              $stmt->bindParam(':mes_pago', $mes_pago_formateado, PDO::PARAM_STR);
             $stmt->bindParam(':fecha_pago', $fecha_pago, PDO::PARAM_STR);
            $stmt->bindParam(':monto_tasa_municipal', $monto_tasa_municipal, PDO::PARAM_STR);
             $stmt->bindParam(':monto_arbitrio', $monto_arbitrio, PDO::PARAM_STR);
             $stmt->bindParam(':estado_pago', $estado_pago, PDO::PARAM_STR);
            $stmt->bindParam(':observaciones', $observaciones, PDO::PARAM_STR);

            // Ejecuta la consulta
            $stmt->execute();
             echo "<script type='text/javascript'>
                        alert('Registro guardado exitosamente');
                    </script>";
        }
        }
        catch (PDOException $e) {
           echo "<script type='text/javascript'>
                        alert('Error al registrar el pago: " . $e->getMessage() . "');
                 </script>";
        }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Pago de Arrendamiento</title>
     <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Estilos para el formulario */
        body {
             font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #e3fdfd 0%, #ffffff 100%);
            margin: 0;
             padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
             height: 100vh;
        }
         .menu-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
             padding: 30px; /* Menos padding para mejor aspecto */
            text-align: center;
             max-width: 550px; /* Ancho máximo para la tarjeta */
              margin: auto; /* Centrar la tarjeta */
        }
         h1 {
            text-align: center;
             margin-bottom: 20px;
             font-size: 2.2rem;
             font-weight: bold;
             color: #2d7e41;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input, select, button, textarea {
            width: 100%;
            padding: 8px;
             margin-top: 5px;
            margin-bottom: 15px;
            border-radius: 4px;
             border: 2px solid #4ade80;
              background-color: white;
              transition: all 0.3s ease;
            font-size: 1rem;
               cursor: pointer;
             appearance: none;
            -webkit-appearance: none;
             -moz-appearance: none;
         }
           input:focus, select:focus, textarea:focus{
             outline: none;
             border-color:#22c55e;
        }

        button, .back-button {
            background-color: #4ade80;
            color: white;
            cursor: pointer;
             text-align: center;
            text-decoration: none;
            display: inline-block;
            padding: 12px 24px;
            border-radius: 10px;
            transition: background-color 0.3s ease;
             border: none;
             font-weight: bold;
        }
        button:hover, .back-button:hover {
            background-color: #22c55e;
        }
        .result {
            margin-top: 20px;
             background-color: #e9f7e9;
             padding: 10px;
            border-radius: 4px;
             display: none;
        }
        .back-button {
            margin-bottom: 20px;
             display: block;
            text-align: center;
             text-decoration: none;
        }
        .logo-container img{
            width: 180px;
            height: auto;
             margin-bottom: 20px;
        }
    </style>
    <script>
        // Función para buscar los datos del DPI
         function buscarDpi() {
            var dpi = document.getElementById('dpi').value;
             if (!dpi) {
                alert('Por favor, ingrese el DPI antes de buscar.');
                return;
            }

            fetch(`../buscar_tarjeta.php?dpi=${dpi}`)
                 .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor: ' + response.status);
                    }
                     return response.json();
                   })
                 .then(data => {
                    if (data.success) {
                           // Mostrar los resultados
                        document.getElementById('result').style.display = 'block';
                            document.getElementById('nombres').textContent = data.nombres || '';
                            document.getElementById('numero_local').textContent = data.numero_local || '';
                            document.getElementById('bloque').textContent = data.bloque || '';
                            document.getElementById('giro_negocio').textContent = data.giro_negocio || '';
                             document.getElementById('tarjeta_tasa_municipal').textContent = data.tarjeta_tasa_municipal || '';
                             document.getElementById('tarjeta_arbitrio').textContent = data.tarjeta_arbitrio || '';
                           // Habilitar el formulario para registrar el pago
                           document.getElementById('form-pago').style.display = 'block';
                           // Agregar el dpi al form de registro de pagos
                            document.getElementById('form-pago').dpi.value = dpi;
                              // asignar valores a los campos de pago con la información de la tabla
                             document.getElementById('monto_pago_tasa_municipal').value = data.valor_tasa_municipal;
                            document.getElementById('monto_pago_arbitrio').value = data.valor_arbitrio;
                      }
                       else
                           alert(data.message || 'No se encontró ningún registro con el DPI ingresado.');
                    })
                 .catch(error => {
                    console.error('Error:', error);
                    alert('Ocurrió un error al buscar la información del arrendatario: ' + error.message);
                });
            }
           function validarFormulario(event) {

           }

    </script>
</head>
 <body class="min-h-screen flex items-center justify-center p-4">
     <div class="w-full max-w-md">
      <div class="menu-card">
            <!-- Logo -->
            <div class="logo-container mb-6">
                <img src="../images/logo muni 2024.png" alt="Logo Municipalidad 2024" class="w-32 mx-auto">
            </div>
        <!-- Botón para regresar al menú -->
            <a href="../menu.php" class="back-button">Regresar al Menú</a>
          <!-- Título -->
          <h1>Registro de Pago de Arrendamiento</h1>

        <!-- Formulario para buscar el DPI -->
        <form id="formulario-pago">
            <label for="dpi">DPI del Arrendatario:</label>
           <input type="text" id="dpi" name="dpi" required placeholder="Ingrese el DPI">
             <button type="button" onclick="buscarDpi()">Buscar DPI</button>
         </form>

        <!-- Mostrar los datos del arrendatario si se encuentra el DPI -->
        <div id="result" class="result">
           <h3>Datos del Arrendatario</h3>
            <p><strong>Nombre:</strong> <span id="nombres"></span></p>
              <p><strong>Local:</strong> <span id="numero_local"></span></p>
            <p><strong>Bloque:</strong> <span id="bloque"></span></p>
             <p><strong>Giro de Negocio:</strong> <span id="giro_negocio"></span></p>
            <p><strong>Tasa Municipal:</strong> <span id="tarjeta_tasa_municipal"></span></p>
            <p><strong>Arbitrio:</strong> <span id="tarjeta_arbitrio"></span></p>
        </div>

       <!-- Formulario para registrar el pago -->
        <form  method="post" onsubmit="validarFormulario(event)" id="form-pago" style="display:none;">
          <!-- Agregamos un campo oculto para el ID del mercado-->
            <input type="hidden" name="id_mercado" value="<?php echo $id_mercado; ?>">
            <label for="mes_pago">Mes de Pago:</label>
             <input type="month" id="mes_pago" name="mes_pago" required>

             <label for="fecha_pago">Fecha de Pago:</label>
             <input type="date" id="fecha_pago" name="fecha_pago" required>

           <label for="monto_pago_tasa_municipal">Monto Tasa Municipal:</label>
            <input type="number" step="1" id="monto_pago_tasa_municipal" name="monto_pago_tasa_municipal" required readonly>

            <label for="monto_pago_arbitrio">Monto Arbitrio:</label>
            <input type="number" step="1" id="monto_pago_arbitrio" name="monto_pago_arbitrio" required readonly>

            <label for="estado_pago">Estado del Pago:</label>
             <select id="estado_pago" name="estado_pago" required>
                <option value="pendiente">Pendiente</option>
                <option value="completado">Completado</option>
                <option value="cancelado">Cancelado</option>
             </select>

            <label for="observaciones">Observaciones:</label>
             <textarea id="observaciones" name="observaciones" rows="4" placeholder="Ingrese observaciones (opcional)"></textarea>
           <!-- campos hidden para la información del arrendatario -->
             <input type="hidden" id="nombres" name="nombres" >
              <input type="hidden" id="apellidos" name="apellidos" >
           <input type="hidden" id="dpi" name="dpi" >
        
            <button type="submit" class="register-button">Registrar Pago</button>
        </form>
           <!-- Pie de página -->
          <div class="mt-8 text-sm text-gray-500">
                © 2024 Municipalidad. Todos los derechos reservados.
           </div>
       </div>
    </div>
</body>
</html>