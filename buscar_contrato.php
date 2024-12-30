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
            // Consulta SQL para obtener datos de la tabla contrato
             $sql_contrato = "SELECT nombres, apellidos, giro_negocio, numero_local, bloque FROM contrato WHERE dpi = :dpi";
             $stmt_contrato = $conexion->prepare($sql_contrato);
            $stmt_contrato->bindParam(':dpi', $dpi, PDO::PARAM_STR);
            $stmt_contrato->execute();
            $result_contrato = $stmt_contrato->fetch(PDO::FETCH_ASSOC);


            if ($result_contrato && is_array($result_contrato)) {
              $response =  json_encode([
                        'success' => true,
                        'nombres' => $result_contrato['nombres'],
                        'apellidos' => $result_contrato['apellidos'],
                         'giro_negocio' => $result_contrato['giro_negocio'],
                         'numero_local' => $result_contrato['numero_local'],
                           'bloque' => $result_contrato['bloque'],
                    ]);
                  if (json_last_error() !== JSON_ERROR_NONE) {
                      echo json_encode(['success' => false, 'message' => 'Error al codificar la respuesta a JSON: ' . json_last_error_msg(), 'dpi' => $dpi, 'result_contrato'=> var_export($result_contrato,true)]);
                    }
                   else
                     echo $response;

            } else {
                 echo json_encode(['success' => false, 'message' => 'No se encontró un contrato con ese DPI', 'dpi' => $dpi]);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error al buscar el contrato: ' . $e->getMessage(), 'dpi' => $dpi]);
        }
    } else {
          echo json_encode(['success' => false, 'message' => 'DPI no proporcionado']);
    }
?>