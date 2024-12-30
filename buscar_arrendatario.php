<?php
    // Habilitar la visualización de errores
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require 'conexion.php'; // Asegura que la ruta a conexion.php es correcta

    header('Content-Type: application/json');

    if (isset($_GET['dpi'])) {
       $dpi = $_GET['dpi'];
        try {
           $sql = "SELECT nombres, apellidos FROM arrendatarios WHERE dpi = :dpi";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':dpi', $dpi, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                 echo json_encode(['success' => true, 'nombres' => $result['nombres'], 'apellidos' => $result['apellidos']]);
            } else {
                 echo json_encode(['success' => false, 'message' => 'No se encontró un arrendatario con ese DPI']);
            }
        } catch (PDOException $e) {
           echo json_encode(['success' => false, 'message' => 'Error al buscar el arrendatario: ' . $e->getMessage()]);
        }
    } else {
         echo json_encode(['success' => false, 'message' => 'DPI no proporcionado']);
    }
?>