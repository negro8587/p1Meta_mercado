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
    // Obtener los datos enviados desde el formulario
     $id_mercado = $_POST['id_mercado'] ?? null;
      $nombres = $_POST['nombres'] ?? null;
      $apellidos = $_POST['apellidos'] ?? null;
     $dpi = $_POST['dpi'] ?? null;
    $nim = $_POST['nim'] ?? null;
    $giro_negocio = $_POST['giro_negocio'] ?? null;
    $tarjeta_tasa_municipal = $_POST['tarjeta_tasa_municipal'] ?? null;
    $tarjeta_arbitrio = $_POST['tarjeta_arbitrio'] ?? null;
    $valor_tasa_municipal = $_POST['valor_tasa_municipal'] ?? null;
    $valor_arbitrio = $_POST['valor_arbitrio'] ?? null;
    $numero_local = $_POST['numero_local'] ?? null;
    $bloque = $_POST['bloque'] ?? null;

    try {
        // Consulta SQL para insertar los datos
       $sql = "INSERT INTO tarjeta (id_mercado, nim, giro_negocio, tarjeta_tasa_municipal, tarjeta_arbitrio, valor_tasa_municipal, valor_arbitrio, numero_local, bloque, nombres, apellidos, dpi) 
                VALUES (:id_mercado, :nim, :giro_negocio, :tarjeta_tasa_municipal, :tarjeta_arbitrio, :valor_tasa_municipal, :valor_arbitrio, :numero_local, :bloque, :nombres, :apellidos, :dpi)";

        $stmt = $conexion->prepare($sql);
         $stmt->bindParam(':id_mercado', $id_mercado, PDO::PARAM_INT);
        $stmt->bindParam(':nim', $nim, PDO::PARAM_STR);
        $stmt->bindParam(':giro_negocio', $giro_negocio, PDO::PARAM_STR);
        $stmt->bindParam(':tarjeta_tasa_municipal', $tarjeta_tasa_municipal, PDO::PARAM_STR);
        $stmt->bindParam(':tarjeta_arbitrio', $tarjeta_arbitrio, PDO::PARAM_STR);
        $stmt->bindParam(':valor_tasa_municipal', $valor_tasa_municipal, PDO::PARAM_STR);
        $stmt->bindParam(':valor_arbitrio', $valor_arbitrio, PDO::PARAM_STR);
        $stmt->bindParam(':numero_local', $numero_local, PDO::PARAM_STR);
         $stmt->bindParam(':bloque', $bloque, PDO::PARAM_STR);
         $stmt->bindParam(':nombres', $nombres, PDO::PARAM_STR);
         $stmt->bindParam(':apellidos', $apellidos, PDO::PARAM_STR);
          $stmt->bindParam(':dpi', $dpi, PDO::PARAM_STR);

        $stmt->execute();
         $message = 'Registro guardado exitosamente';

    } catch (PDOException $e) {
         $message = 'Error al registrar la tarjeta: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro de Tarjeta</title>
    <style>
        /* Estilos similares a los de tu formulario anterior */
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
        function buscarTarjeta() {
            const dpi = document.getElementById('dpi').value;

            if (!dpi) {
                alert('Por favor, ingrese el DPI antes de buscar.');
                return;
            }

            fetch(`../buscar_contrato.php?dpi=${dpi}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Asignar los valores solo si no están vacíos o indefinidos
                        document.getElementById('nombres').value = data.nombres || '';
                        document.getElementById('apellidos').value = data.apellidos || '';
                        document.getElementById('nim').value = data.nim || '';
                        document.getElementById('giro_negocio').value = data.giro_negocio || '';
                        document.getElementById('tarjeta_tasa_municipal').value = data.tarjeta_tasa_municipal || '';
                        document.getElementById('tarjeta_arbitrio').value = data.tarjeta_arbitrio || '';
                        document.getElementById('valor_tasa_municipal').value = data.valor_tasa_municipal || '';
                        document.getElementById('valor_arbitrio').value = data.valor_arbitrio || '';
                        document.getElementById('numero_local').value = data.numero_local || '';
                        document.getElementById('bloque').value = data.bloque || '';
                    } else {
                        alert(data.message || 'No se encontró ningún registro con el DPI ingresado.');
                        // Limpiar los campos en caso de error
                        document.getElementById('nombres').value = '';
                        document.getElementById('apellidos').value = '';
                        document.getElementById('nim').value = '';
                        document.getElementById('giro_negocio').value = '';
                        document.getElementById('tarjeta_tasa_municipal').value = '';
                        document.getElementById('tarjeta_arbitrio').value = '';
                        document.getElementById('valor_tasa_municipal').value = '';
                        document.getElementById('valor_arbitrio').value = '';
                        document.getElementById('numero_local').value = '';
                        document.getElementById('bloque').value = '';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ocurrió un error al buscar la tarjeta: '+ error.message);
                });
        }
         function validarFormulario(event) {

        }
    </script>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="../logo_municipalidad_coban.png" alt="Logo Municipalidad de Cobán">
        </div>
       <form action="" method="post" onsubmit="validarFormulario(event)">
            <h1>Formulario de Registro de Tarjeta</h1>
             <h2 class="subtitle">Ingresando datos al mercado: <?php echo $nombre_mercado; ?></h2>
              <!-- Agregamos un campo oculto para el ID del mercado-->
             <input type="hidden" name="id_mercado" value="<?php echo $id_mercado; ?>">
            <label for="dpi">DPI:</label>
            <div style="display: flex; gap: 8px;">
                <input type="text" id="dpi" name="dpi" required>
                <button type="button" onclick="buscarTarjeta()">Buscar</button>
            </div>

            <label for="nombres">Nombres:</label>
            <input type="text" id="nombres" name="nombres" class="disabled-field" readonly>

            <label for="apellidos">Apellidos:</label>
            <input type="text" id="apellidos" name="apellidos" class="disabled-field" readonly>

            <label for="nim">NIM:</label>
            <input type="text" id="nim" name="nim" required>

            <label for="giro_negocio">Giro de Negocio:</label>
            <input type="text" id="giro_negocio" name="giro_negocio" required class="disabled-field" readonly>

            <label for="tarjeta_tasa_municipal">Tarjeta Tasa Municipal:</label>
            <input type="text" id="tarjeta_tasa_municipal" name="tarjeta_tasa_municipal" required>

            <label for="tarjeta_arbitrio">Tarjeta Arbitrio:</label>
            <input type="text" id="tarjeta_arbitrio" name="tarjeta_arbitrio" required>

            <label for="valor_tasa_municipal">Valor Tasa Municipal:</label>
            <input type="number" id="valor_tasa_municipal" name="valor_tasa_municipal" step="0.01" max="999" required>

            <label for="valor_arbitrio">Valor Arbitrio:</label>
            <input type="text" id="valor_arbitrio" name="valor_arbitrio" required>

            <label for="numero_local">Número de Local:</label>
            <input type="text" id="numero_local" name="numero_local" required class="disabled-field" readonly>

            <label for="bloque">Bloque:</label>
            <input type="text" id="bloque" name="bloque" required class="disabled-field" readonly>
 <?php if (isset($message)): ?>
            <div class="error"><?= $message ?></div>
            <?php endif; ?>
            <div class="buttons">
                <button type="submit" class="register-button">Registrar Tarjeta</button>
                 <a href="../menu.php" class="menu-button">Regresar al Menú</a>
            </div>
        </form>
    </div>
</body>
</html>