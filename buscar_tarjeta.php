<?php
    // Habilitar la visualización de errores
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require 'conexion.php';

    header('Content-Type: application/json');

    if (isset($_GET['dpi'])) {
        $dpi = $_GET['dpi'];
        try {
            // Consulta SQL para obtener datos de la tabla tarjeta
           $sql = "SELECT nombres, apellidos, nim, giro_negocio, tarjeta_tasa_municipal, tarjeta_arbitrio, valor_tasa_municipal, 
                     valor_arbitrio, numero_local, bloque FROM tarjeta WHERE dpi = :dpi";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':dpi', $dpi, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                // Devuelve los datos en formato JSON
                 $response = json_encode([
                    'success' => true,
                      'nombres' => $result['nombres'],
                    'apellidos' => $result['apellidos'],
                     'nim' => $result['nim'],
                    'giro_negocio' => $result['giro_negocio'],
                     'tarjeta_tasa_municipal' => $result['tarjeta_tasa_municipal'],
                   'tarjeta_arbitrio' => $result['tarjeta_arbitrio'],
                   'valor_tasa_municipal' => $result['valor_tasa_municipal'],
                     'valor_arbitrio' => $result['valor_arbitrio'],
                     'numero_local' => $result['numero_local'],
                     'bloque' => $result['bloque']
                ]);
                if (json_last_error() !== JSON_ERROR_NONE) {
                      echo json_encode(['success' => false, 'message' => 'Error al codificar la respuesta a JSON: ' . json_last_error_msg(), 'dpi' => $dpi]);
                    }
                  else
                     echo $response;
            } else {
                // Si no se encuentra ningún registro con el DPI, devuelve un mensaje de error
                echo json_encode(['success' => false, 'message' => 'No se encontró ninguna tarjeta con ese DPI', 'dpi'=>$dpi]);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error al buscar la tarjeta: ' . $e->getMessage(),'dpi'=>$dpi]);
        }
    } else {
        // Si no se ha enviado el DPI, devuelve un error
        echo json_encode(['success' => false, 'message' => 'DPI no proporcionado']);
    }
?>