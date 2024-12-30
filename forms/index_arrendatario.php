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


// Verifica si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos enviados desde el formulario y convertirlos a mayúsculas
     $id_mercado = $_POST['id_mercado'] ?? null;
    $nombres = strtoupper($_POST['nombres'] ?? null);
    $apellidos = strtoupper($_POST['apellidos'] ?? null);
    $dpi = $_POST['dpi'] ?? null;
    $estado_civil = strtoupper($_POST['estado_civil'] ?? null);
    $direccion = strtoupper($_POST['direccion'] ?? null);
    $telefono = $_POST['telefono'] ?? null;
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
    $beneficiario = strtoupper($_POST['beneficiario'] ?? null);
    $dpi_beneficiario = $_POST['dpi_beneficiario'] ?? null;
    $telefono_beneficiario = $_POST['telefono_beneficiario'] ?? null;
    $nit = strtoupper($_POST['nit'] ?? null);
    $correo_electronico = strtoupper($_POST['correo_electronico'] ?? null);

    // Verificar si el DPI ya existe en la base de datos
    try {
        $sql_check_dpi = "SELECT COUNT(*) FROM arrendatarios WHERE dpi = :dpi";
        $stmt_check_dpi = $conexion->prepare($sql_check_dpi);
        $stmt_check_dpi->bindParam(':dpi', $dpi);
        $stmt_check_dpi->execute();
        $existing_dpi = $stmt_check_dpi->fetchColumn();

        if ($existing_dpi > 0) {
            // El DPI ya existe, solo mostramos el mensaje emergente en JavaScript
            echo "<script type='text/javascript'>
                    alert('El DPI ingresado ya está registrado. Por favor, ingrese otro DPI.');
                  </script>";
        } else {
            // El DPI no existe, proceder con el registro
            try {
                // Consulta SQL para insertar los datos
                $sql = "INSERT INTO arrendatarios 
                        (id_mercado, nombres, apellidos, dpi, estado_civil, direccion, telefono, fecha_nacimiento, beneficiario, dpi_beneficiario, telefono_beneficiario, nit, correo_electronico) 
                        VALUES 
                        (:id_mercado, :nombres, :apellidos, :dpi, :estado_civil, :direccion, :telefono, :fecha_nacimiento, :beneficiario, :dpi_beneficiario, :telefono_beneficiario, :nit, :correo_electronico)";

                // Prepara la consulta
                $stmt = $conexion->prepare($sql);

                // Vincula los parámetros
                $stmt->bindParam(':id_mercado', $id_mercado, PDO::PARAM_INT);
                $stmt->bindParam(':nombres', $nombres);
                $stmt->bindParam(':apellidos', $apellidos);
                $stmt->bindParam(':dpi', $dpi);
                $stmt->bindParam(':estado_civil', $estado_civil);
                $stmt->bindParam(':direccion', $direccion);
                $stmt->bindParam(':telefono', $telefono);
                $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
                $stmt->bindParam(':beneficiario', $beneficiario);
                $stmt->bindParam(':dpi_beneficiario', $dpi_beneficiario);
                $stmt->bindParam(':telefono_beneficiario', $telefono_beneficiario);
                $stmt->bindParam(':nit', $nit);
                $stmt->bindParam(':correo_electronico', $correo_electronico);

                // Ejecuta la consulta
                $stmt->execute();

                // Mostrar mensaje de registro exitoso y redirigir
                echo "<script type='text/javascript'>
                        alert('Registro guardado exitosamente');
                      </script>";
            } catch (PDOException $e) {
                echo "<script type='text/javascript'>
                        alert('Error al registrar los datos: " . $e->getMessage() . "');
                      </script>";
            }
        }
    } catch (PDOException $e) {
        echo "<script type='text/javascript'>
                alert('Error al verificar el DPI: " . $e->getMessage() . "');
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Arrendatarios</title>
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
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            max-width: 800px;
            width: 100%;
        }
        .logo {
            margin-right: 20px;
        }
        .logo img {
            width: 100px;
            height: auto;
        }
        form {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 20px;
        }
        h1 {
            grid-column: span 2;
            color: #333;
            font-size: 18px;
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
        }
        input, select, button {
            width: 100%;
            padding: 6px;
            margin-top: 4px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        .button-container {
            grid-column: span 2;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }
        button {
            background-color: #5cb85c;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 14px;
            padding: 6px;
            width: calc(50% - 5px);  /* Ajusta el ancho para que queden paralelos */
        }
        button:hover {
            background-color: #4cae4c;
        }
        .return-btn {
            background-color: #f0ad4e;
        }
        .return-btn:hover {
            background-color: #ec971f;
        }

        .error {
            color: red;
            font-size: 14px;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="../logo_municipalidad_coban.png" alt="Logo Municipalidad de Cobán">
        </div>
        <form action="" method="post">
           <h1>Registro de Arrendatarios</h1>
             <h2 class="subtitle">Ingresando datos al mercado: <?php echo $nombre_mercado; ?></h2>
            <?php if (isset($message)): ?>
                <div class="error"><?= $message ?></div>
            <?php endif; ?>
             <!-- Agregamos un campo oculto para el ID del mercado-->
            <input type="hidden" name="id_mercado" value="<?php echo $id_mercado; ?>">

            <label for="nombres">Nombres:</label>
            <input type="text" id="nombres" name="nombres" required>

            <label for="apellidos">Apellidos:</label>
            <input type="text" id="apellidos" name="apellidos" required>

            <label for="dpi">DPI:</label>
            <input type="text" id="dpi" name="dpi" required pattern="\d{13}" title="Debe tener 13 dígitos">

            <label for="estado_civil">Estado Civil:</label>
            <select id="estado_civil" name="estado_civil" required>
                <option value="">Seleccione</option>
                <option value="SOLTERO">Soltero</option>
                <option value="CASADO">Casado</option>
                <option value="DIVORCIADO">Divorciado</option>
                <option value="VIUDO">Viudo</option>
            </select>

            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion" required>

            <label for="telefono">Teléfono:</label>
            <input type="tel" id="telefono" name="telefono" required pattern="\d{8}" title="Debe tener 8 dígitos">

            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>

            <label for="beneficiario">Beneficiario:</label>
            <input type="text" id="beneficiario" name="beneficiario">

            <label for="dpi_beneficiario">DPI del Beneficiario:</label>
            <input type="text" id="dpi_beneficiario" name="dpi_beneficiario">

            <label for="telefono_beneficiario">Teléfono del Beneficiario:</label>
            <input type="tel" id="telefono_beneficiario" name="telefono_beneficiario">

            <label for="nit">NIT:</label>
            <input type="text" id="nit" name="nit">

            <label for="correo_electronico">Correo Electrónico:</label>
            <input type="email" id="correo_electronico" name="correo_electronico">

            <div class="button-container">
                <button type="submit">Registrar</button>
                <button type="button" class="return-btn" onclick="window.location.href='../menu.php'">Regresar al Menú</button>
            </div>
        </form>
    </div>
</body>
</html>