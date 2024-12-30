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
    $nombre_mercado = "Error al obtener el nombre del mercado";
  }

// Consultar los giros de negocio desde la base de datos
$query_giro = "SELECT giro_negocio FROM giro_negocio";
$result_giro = $conexion->query($query_giro);

// Consultar los tipos de arrendamiento desde la base de datos
$query_tipo_arrendamiento = "SELECT tipo_arrendamiento FROM tipo_arrendamiento";
$result_tipo_arrendamiento = $conexion->query($query_tipo_arrendamiento);

// Verifica si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos enviados desde el formulario
    $id_mercado = $_POST['id_mercado'] ?? null;
    $numero_contrato = $_POST['numero_contrato'] ?? null;
    $tipo_arrendamiento = $_POST['tipo_arrendamiento'] ?? null;
    $giro_negocio = $_POST['giro_negocio'] ?? null;
    $numero_local = $_POST['numero_local'] ?? null;
    $bloque = $_POST['bloque'] ?? null;
    $medida_norte = $_POST['medida_norte'] ?? null;
    $medida_sur = $_POST['medida_sur'] ?? null;
    $medida_este = $_POST['medida_este'] ?? null;
    $medida_oeste = $_POST['medida_oeste'] ?? null;
    $area = $_POST['area'] ?? null;
    $fecha_inicio = $_POST['fecha_inicio'] ?? null;
    $fecha_fin = $_POST['fecha_fin'] ?? null;
    $dpi = $_POST['dpi'] ?? null;
    $nombres = $_POST['nombres'] ?? null;
    $apellidos = $_POST['apellidos'] ?? null;

    try {
        // Consulta SQL para insertar los datos
        $sql = "INSERT INTO contrato (id_mercado, numero_contrato, tipo_arrendamiento, giro_negocio, numero_local, bloque, medida_norte, medida_sur, medida_este, medida_oeste, area, fecha_inicio, fecha_fin, nombres, apellidos, dpi) 
                VALUES (:id_mercado, :numero_contrato, :tipo_arrendamiento, :giro_negocio, :numero_local, :bloque, :medida_norte, :medida_sur, :medida_este, :medida_oeste, :area, :fecha_inicio, :fecha_fin, :nombres, :apellidos, :dpi)";

        $stmt = $conexion->prepare($sql);

        $stmt->bindParam(':id_mercado', $id_mercado, PDO::PARAM_INT);
        $stmt->bindParam(':numero_contrato', $numero_contrato, PDO::PARAM_STR);
        $stmt->bindParam(':tipo_arrendamiento', $tipo_arrendamiento, PDO::PARAM_STR);
        $stmt->bindParam(':giro_negocio', $giro_negocio, PDO::PARAM_STR);
        $stmt->bindParam(':numero_local', $numero_local, PDO::PARAM_STR);
        $stmt->bindParam(':bloque', $bloque, PDO::PARAM_STR);
        $stmt->bindParam(':medida_norte', $medida_norte, PDO::PARAM_INT);
        $stmt->bindParam(':medida_sur', $medida_sur, PDO::PARAM_INT);
        $stmt->bindParam(':medida_este', $medida_este, PDO::PARAM_INT);
        $stmt->bindParam(':medida_oeste', $medida_oeste, PDO::PARAM_INT);
        $stmt->bindParam(':area', $area, PDO::PARAM_INT);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_fin', $fecha_fin, PDO::PARAM_STR);
        $stmt->bindParam(':nombres', $nombres, PDO::PARAM_STR);
        $stmt->bindParam(':apellidos', $apellidos, PDO::PARAM_STR);
        $stmt->bindParam(':dpi', $dpi, PDO::PARAM_STR);


        $stmt->execute();

        // Mostrar mensaje de registro exitoso y redirigir
        echo "<script type='text/javascript'>
                        alert('Registro guardado exitosamente');
                        window.location.href='../menu.php';
                   </script>";

    } catch (PDOException $e) {
          echo "<script type='text/javascript'>
                      alert('Error al registrar el contrato: " . $e->getMessage() . "');
                 </script>";
    }
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro de Contrato</title>
     <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #4facfe, #00f2fe);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            display: flex;
            align-items: flex-start;
            background: #fff;
            padding: 45px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            max-width: 700px;
            width: 100%;
        }
        .logo {
            margin-right: 10px;
        }
        .logo img {
            width: 80px;
            height: auto;
             opacity: 0.7;
        }
        form {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 16px;
        }
        h1 {
            grid-column: span 2;
            color: #333;
            font-size: 20px;
            text-align: center;
        }
       .subtitle{
             grid-column: span 2;
            color: #333;
            font-size: 16px;
            text-align: center;
              margin-bottom:15px;

         }
        label {
            font-weight: bold;
            text-align: left;
            font-size: 14px;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 2px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        button {
            padding: 6px 10px;
            font-size: 12px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
        }
        .buttons {
            grid-column: span 2;
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        .register-button {
            background-color: #5cb85c;
            color: white;
        }
        .register-button:hover {
            background-color: #4cae4c;
        }
        .menu-button {
            background-color: #f0ad4e;
            color: white;
        }
        .menu-button:hover {
            background-color: #ec971f;
        }

        /* Estilo para los campos deshabilitados */
        .disabled-field {
            background-color: #f0f0f0;
            cursor: not-allowed;
        }
         .error {
            color: red;
            font-size: 14px;
            text-align: center;
            margin-top: 5px;
            margin-bottom: 5px;
        }
    </style>
    <script>
        function buscarArrendatario() {
            const dpi = document.getElementById('dpi').value;

            if (!dpi) {
                alert('Por favor, ingrese el DPI antes de buscar.');
                return;
            }

            fetch(`../buscar_arrendatario.php?dpi=${dpi}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor: ' + response.status);
                    }
                     return response.json();
                    })
                .then(data => {
                    if (data.success) {
                        // Asignar los valores solo si no están vacíos o indefinidos
                        document.getElementById('nombres').value = data.nombres || '';
                        document.getElementById('apellidos').value = data.apellidos || '';
                    } else {
                        alert(data.message || 'No se encontró ningún registro con el DPI ingresado.');
                        // Limpiar los campos en caso de error
                        document.getElementById('nombres').value = '';
                        document.getElementById('apellidos').value = '';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ocurrió un error al buscar el contrato: ' + error.message);
                });
        }
         function validarFormulario(event) {
            const numeroContratoInput = document.getElementById('numero_contrato');
            const numeroLocalInput = document.getElementById('numero_local');
            const bloqueInput = document.getElementById('bloque');
            const contratoRegex = /^\d{3}-\d{4}$/; // Regex para ###-yyyy
           const numeroLocalRegex = /^\d{3}$/;
             const bloqueRegex = /^\d{2}$/;
            let errores = '';

            if (!contratoRegex.test(numeroContratoInput.value)) {
                 errores += 'El número de contrato debe tener el formato ###-yyyy\n';
                 numeroContratoInput.classList.add('error-input');
                }
             else
                numeroContratoInput.classList.remove('error-input');

            if (!numeroLocalRegex.test(numeroLocalInput.value)) {
                  errores += 'El número de local debe tener el formato ###\n';
                  numeroLocalInput.classList.add('error-input');
            }
              else
                numeroLocalInput.classList.remove('error-input');

            if (!bloqueRegex.test(bloqueInput.value)) {
                errores +=  'El bloque debe tener el formato ##\n';
               bloqueInput.classList.add('error-input');

            }
            else
                bloqueInput.classList.remove('error-input');
            if (errores) {
                 alert(errores);
                  event.preventDefault();
                return;
              }
             return true; // Permite que el formulario se envíe
        }


    </script>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="../logo_municipalidad_coban.png" alt="Logo Municipalidad de Cobán">
        </div>
        <form  method="post" onsubmit="validarFormulario(event)">
            <h1>Formulario de Registro de Contrato</h1>
             <h2 class="subtitle">Ingresando datos al mercado: <?php echo $nombre_mercado; ?></h2>
             <input type="hidden" name="id_mercado" value="<?php echo $id_mercado; ?>">

            <label for="dpi">DPI:</label>
            <div style="display: flex; gap: 8px;">
                <input type="text" id="dpi" name="dpi" required>
                <button type="button" onclick="buscarArrendatario()">Buscar</button>
            </div>

            <label for="nombres">Nombres:</label>
            <input type="text" id="nombres" name="nombres" class="disabled-field" readonly>

            <label for="apellidos">Apellidos:</label>
            <input type="text" id="apellidos" name="apellidos" class="disabled-field" readonly>

            <label for="numero_contrato">Número de Contrato:</label>
            <input type="text" id="numero_contrato" name="numero_contrato" required >

            <label for="tipo_arrendamiento">Tipo de Arrendamiento:</label>
            <select id="tipo_arrendamiento" name="tipo_arrendamiento" required>
                <option value="">Seleccionar...</option>
                <?php 
                if ($result_tipo_arrendamiento->rowCount() > 0) {
                    while ($row = $result_tipo_arrendamiento->fetch(PDO::FETCH_ASSOC)) { ?>
                        <option value="<?php echo $row['tipo_arrendamiento']; ?>"><?php echo $row['tipo_arrendamiento']; ?></option>
                    <?php }
                } else { ?>
                    <option value="">No hay tipos de arrendamiento disponibles</option>
                <?php } ?>
            </select>

            <label for="giro_negocio">Giro de Negocio:</label>
            <select id="giro_negocio" name="giro_negocio" required>
                <option value="">Seleccionar...</option>
                <?php 
                if ($result_giro->rowCount() > 0) {
                    while ($row = $result_giro->fetch(PDO::FETCH_ASSOC)) { ?>
                        <option value="<?php echo $row['giro_negocio']; ?>"><?php echo $row['giro_negocio']; ?></option>
                    <?php }
                } else { ?>
                    <option value="">No hay giros de negocio disponibles</option>
                <?php } ?>
            </select>

            <label for="numero_local">Número de Local:</label>
            <input type="text" id="numero_local" name="numero_local" required >

            <label for="bloque">Bloque:</label>
             <input type="text" id="bloque" name="bloque" required >

            <label for="medida_norte">Medida Norte:</label>
            <input type="number" id="medida_norte" name="medida_norte">

            <label for="medida_sur">Medida Sur:</label>
            <input type="number" id="medida_sur" name="medida_sur">

            <label for="medida_este">Medida Este:</label>
            <input type="number" id="medida_este" name="medida_este">

            <label for="medida_oeste">Medida Oeste:</label>
            <input type="number" id="medida_oeste" name="medida_oeste">

            <!-- Nuevos campos agregados -->
            <label for="area">Área:</label>
            <input type="number" id="area" name="area" required>

            <label for="fecha_inicio">Fecha Inicio:</label>
            <input type="date" id="fecha_inicio" name="fecha_inicio" required>

            <label for="fecha_fin">Fecha Fin:</label>
            <input type="date" id="fecha_fin" name="fecha_fin" required>

            <div class="buttons">
                <button type="submit" class="register-button">Registrar</button>
                 <a href="../menu.php" class="menu-button">Menú</a>
            </div>
        </form>
    </div>
</body>
</html>