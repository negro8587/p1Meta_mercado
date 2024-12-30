<?php
// Conexión a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'meta_mercado_talpetate');

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Comprobar si la clave 'accion' está definida en el array $_POST
    if (isset($_POST['accion'])) {
        // Agregar tipo de arrendamiento
        if ($_POST['accion'] == 'agregar_tipo_arrendamiento' && !empty($_POST['tipo_arrendamiento'])) {
            $tipo_arrendamiento = $_POST['tipo_arrendamiento'];

            $query = "INSERT INTO tipo_arrendamiento (tipo_arrendamiento) VALUES (?)";
            $stmt = $conexion->prepare($query);
            if ($stmt === false) {
                die("Error en la preparación de la consulta: " . $conexion->error);
            }
            $stmt->bind_param('s', $tipo_arrendamiento);
            if ($stmt->execute()) {
                echo "<script>alert('Tipo de Arrendamiento agregado exitosamente'); window.location.href = 'agregar_registros.php';</script>";
            } else {
                echo "Error al agregar tipo de arrendamiento: " . $stmt->error;
            }
            $stmt->close();
        }
        
        // Agregar giro de negocio
        if ($_POST['accion'] == 'agregar_giro_negocio' && !empty($_POST['giro_negocio'])) {
            $giro_negocio = $_POST['giro_negocio'];

            $query = "INSERT INTO giro_negocio (giro_negocio) VALUES (?)";
            $stmt = $conexion->prepare($query);
            if ($stmt === false) {
                die("Error en la preparación de la consulta: " . $conexion->error);
            }
            $stmt->bind_param('s', $giro_negocio);
            if ($stmt->execute()) {
                echo "<script>alert('Giro de Negocio agregado exitosamente'); window.location.href = 'agregar_registros.php';</script>";
            } else {
                echo "Error al agregar giro de negocio: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        echo "No se ha recibido la acción en el formulario.";
    }
}

$conexion->close();
?>
