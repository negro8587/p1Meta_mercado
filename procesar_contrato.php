<?php
session_start();
// Habilitar la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
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
         echo "<script type='text/javascript'>
                    alert('Registro guardado exitosamente');
                      window.location.href='menu.php';
                  </script>";

    } catch (PDOException $e) {
            echo "<script type='text/javascript'>
                        alert('Error al registrar el contrato: " . $e->getMessage() . "');
                         window.location.href='menu.php';
                      </script>";
    }
}
?>