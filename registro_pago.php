<?php
// Configuración de la conexión a la base de datos
$host = "localhost";
$user = "root";
$password = "";
$database = "meta_mercado_talpetate";

// Crear la conexión
$conn = new mysqli($host, $user, $password, $database);

// Verificar si hay errores en la conexión
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

// Función para verificar si existe un pago para el mes y DPI específico
function verificarPagoExistente($conn, $dpi_arrendatario, $mes_pago) {
    $query = "SELECT * FROM registro_pagos_arrendamiento 
              WHERE dpi_arrendatario = ? 
              AND DATE_FORMAT(mes_pago, '%Y-%m') = DATE_FORMAT(?, '%Y-%m')";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ss", $dpi_arrendatario, $mes_pago);
        $stmt->execute();
        $result = $stmt->get_result();
        $existe = $result->num_rows > 0;
        $stmt->close();
        return $existe;
    }
    return false;
}

// Si se envió el formulario para registrar el pago
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dpi_arrendatario = $_POST['dpi_arrendatario'];
    $mes_pago = $_POST['mes_pago'] . "-01"; // Convertir a formato YYYY-MM-DD
    
    // Verificar si ya existe un pago para este mes y DPI
    if (verificarPagoExistente($conn, $dpi_arrendatario, $mes_pago)) {
        echo "<script>
                alert('¡Ya existe un pago registrado para este DPI y mes!');
                history.back();
              </script>";
        exit;
    }

    $fecha_pago = $_POST['fecha_pago'];
    $monto_tasa_municipal = $_POST['monto_tasa_municipal'];
    $monto_arbitrio = $_POST['monto_arbitrio'];
    $estado_pago = $_POST['estado_pago'];
    $observaciones = $_POST['observaciones'];

    // Consulta para insertar el registro de pago
    $query = "INSERT INTO registro_pagos_arrendamiento 
              (dpi_arrendatario, mes_pago, fecha_pago, monto_tasa_municipal, 
               monto_arbitrio, estado_pago, observaciones)
              VALUES (?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("sssddss", 
            $dpi_arrendatario, 
            $mes_pago, 
            $fecha_pago, 
            $monto_tasa_municipal, 
            $monto_arbitrio, 
            $estado_pago, 
            $observaciones
        );

        if ($stmt->execute()) {
            echo "<script>
                    alert('Pago registrado con éxito.');
                    window.location.href = 'registro_pago.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Error al registrar el pago: " . $stmt->error . "');
                    history.back();
                  </script>";
        }

        $stmt->close();
    } else {
        echo "<script>
                alert('Error al preparar la consulta: " . $conn->error . "');
                history.back();
              </script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro de Pago</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to bottom, #4facfe, #00f2fe);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 700px;
            margin: 20px;
            border: 1px solid #e1e1e1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .logo img {
            width: 80px;
            height: auto;
        }

        h1 {
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;
            color: #4A90E2;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            background-color: #f9f9f9;
        }

        input[type="month"], input[type="date"] {
            width: auto;
            display: inline-block;
        }

        button {
            background-color: #4A90E2;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-right: 15px;
        }

        button:hover {
            background-color: #357ABD;
        }

        .regresar-btn {
            background-color: #f36f6f;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .regresar-btn:hover {
            background-color: #e03d3d;
        }

        .result {
            margin-top: 20px;
            padding: 10px;
            background-color: #f2f2f2;
            border-radius: 8px;
            display: none;
        }

        .result p {
            margin: 5px 0;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="logo">
            <img src="logo_municipalidad_coban.png" alt="Logo Municipalidad de Cobán">
        </div>
        <h1>Formulario de Registro de Pago</h1>
        <form id="formulario-pago" action="registro_pago.php" method="POST">
            <label for="dpi_arrendatario">DPI del Arrendatario:</label>
            <input type="text" id="dpi_arrendatario" name="dpi_arrendatario" required placeholder="Ingrese el DPI">
            <button type="button" onclick="buscarDpi()">Buscar DPI</button>

            <div id="result" class="result">
                <h3>Datos del Arrendatario</h3>
                <p><strong>Nombre:</strong> <span id="nombre_arrendatario"></span></p>
                <p><strong>Apellidos:</strong> <span id="apellidos_arrendatario"></span></p>
                <p><strong>Número Local:</strong> <span id="numero_local_arrendatario"></span></p>
                <p><strong>Bloque:</strong> <span id="bloque_arrendatario"></span></p>
            </div>

            <label for="mes_pago">Mes de Pago:</label>
            <input type="month" id="mes_pago" name="mes_pago" required>

            <label for="fecha_pago">Fecha de Pago:</label>
            <input type="date" id="fecha_pago" name="fecha_pago" required>

            <label for="monto_tasa_municipal">Monto Tasa Municipal:</label>
            <input type="number" id="monto_tasa_municipal" name="monto_tasa_municipal" required step="0.01">

            <label for="monto_arbitrio">Monto Arbitrio:</label>
            <input type="number" id="monto_arbitrio" name="monto_arbitrio" required step="0.01">

            <label for="estado_pago">Estado del Pago:</label>
            <select id="estado_pago" name="estado_pago" required>
                <option value="pendiente">Pendiente</option>
                <option value="completado">Completado</option>
                <option value="cancelado">Cancelado</option>
            </select>

            <label for="observaciones">Observaciones:</label>
            <textarea id="observaciones" name="observaciones" rows="4"></textarea>

            <button type="submit">Registrar Pago</button>
            <button type="button" class="regresar-btn" onclick="window.location.href = 'menu.php';">Regresar al Menú</button>
        </form>
    </div>

    <script>
        function buscarDpi() {
            var dpi = document.getElementById('dpi_arrendatario').value;
            if (dpi) {
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'buscar_tarjeta.php?dpi=' + dpi, true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            document.getElementById('result').style.display = 'block';
                            document.getElementById('nombre_arrendatario').textContent = response.nombres;
                            document.getElementById('apellidos_arrendatario').textContent = response.apellidos;
                            document.getElementById('numero_local_arrendatario').textContent = response.numero_local;
                            document.getElementById('bloque_arrendatario').textContent = response.bloque;
                            document.getElementById('monto_tasa_municipal').value = response.valor_tasa_municipal;
                            document.getElementById('monto_arbitrio').value = response.valor_arbitrio;
                        } else {
                            alert('No se encontraron datos para este DPI.');
                        }
                    } else {
                        alert('Error al consultar los datos.');
                    }
                };
                xhr.send();
            }
        }
    </script>

</body>
</html>
