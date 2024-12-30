<?php
// Conexion a la base de datos
require 'conexion.php';

if (isset($_GET['dpi'])) {
    $dpi = $_GET['dpi'];

    // Consulta para buscar el contrato por DPI
    $query_contrato = "SELECT idcontrato, nombres, apellidos, numero_local, bloque FROM contrato WHERE dpi = :dpi";
    $stmt = $conexion->prepare($query_contrato);
    $stmt->bindParam(':dpi', $dpi);
    $stmt->execute();
    
    $contrato = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($contrato) {
        // Si se encuentra el contrato, devolver los datos
        echo json_encode([
            'success' => true,
            'nombres' => $contrato['nombres'],
            'apellidos' => $contrato['apellidos'],
            'numero_local' => $contrato['numero_local'],
            'bloque' => $contrato['bloque']
        ]);
    } else {
        // Si no se encuentra el contrato, devolver un error
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>
