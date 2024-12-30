<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include('conexion.php');  // Incluye el archivo de conexión

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si los datos fueron recibidos
    $dpi_arrendatario = $_POST['dpi_arrendatario'];
    $mes_pago = $_POST['mes_pago'];
    $fecha_pago = $_POST['fecha_pago'];
    $monto_pago_tasa_municipal = $_POST['monto_pago_tasa_municipal'];
    $monto_pago_arbitrio = $_POST['monto_pago_arbitrio'];
    $estado_pago = $_POST['estado_pago'];
    $observaciones = $_POST['observaciones'];

    // Depurar los datos recibidos
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    // Ahora, realiza la inserción en la base de datos
    try {
        $stmt = $conexion->prepare("INSERT INTO registro_pagos_arrendamiento (dpi_arrendatario, mes_pago, fecha_pago, monto_tasa_municipal, monto_arbitrio, estado_pago, observaciones) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$dpi_arrendatario, $mes_pago, $fecha_pago, $monto_pago_tasa_municipal, $monto_pago_arbitrio, $estado_pago, $observaciones]);

        echo "Pago registrado correctamente!";
    } catch (PDOException $e) {
        echo "Error al registrar el pago: " . $e->getMessage();
    }
} else {
    echo "No se recibieron datos";
}
?>
