<?php
require 'conexion.php'; // Asegúrate de que la conexión a la base de datos esté correctamente configurada

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $dpi = $_POST['dpi'];
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $nim = $_POST['nim'];
    $giro_negocio = $_POST['giro_negocio'];
    $tarjeta_tasa_municipal = $_POST['tarjeta_tasa_municipal'];
    $tarjeta_arbitrio = $_POST['tarjeta_arbitrio'];
    $valor_tasa_municipal = $_POST['valor_tasa_municipal'];
    $valor_arbitrio = $_POST['valor_arbitrio'];
    $numero_local = $_POST['numero_local'];
    $bloque = $_POST['bloque'];

    // Insertar los datos en la tabla 'tarjeta' (corregido)
    $sql = "INSERT INTO tarjeta (dpi, nombres, apellidos, nim, giro_negocio, tarjeta_tasa_municipal, tarjeta_arbitrio, valor_tasa_municipal, valor_arbitrio, numero_local, bloque)
            VALUES (:dpi, :nombres, :apellidos, :nim, :giro_negocio, :tarjeta_tasa_municipal, :tarjeta_arbitrio, :valor_tasa_municipal, :valor_arbitrio, :numero_local, :bloque)";

    // Preparar la consulta
    $stmt = $conexion->prepare($sql);

    // Vincular los parámetros
    $stmt->bindValue(':dpi', $dpi, PDO::PARAM_STR);
    $stmt->bindValue(':nombres', $nombres, PDO::PARAM_STR);
    $stmt->bindValue(':apellidos', $apellidos, PDO::PARAM_STR);
    $stmt->bindValue(':nim', $nim, PDO::PARAM_STR);
    $stmt->bindValue(':giro_negocio', $giro_negocio, PDO::PARAM_STR);
    $stmt->bindValue(':tarjeta_tasa_municipal', $tarjeta_tasa_municipal, PDO::PARAM_STR);
    $stmt->bindValue(':tarjeta_arbitrio', $tarjeta_arbitrio, PDO::PARAM_STR);
    $stmt->bindValue(':valor_tasa_municipal', $valor_tasa_municipal, PDO::PARAM_STR);
    $stmt->bindValue(':valor_arbitrio', $valor_arbitrio, PDO::PARAM_STR);
    $stmt->bindValue(':numero_local', $numero_local, PDO::PARAM_STR);
    $stmt->bindValue(':bloque', $bloque, PDO::PARAM_STR);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Si la inserción es exitosa, redirigir o mostrar un mensaje de éxito
        echo "<script>alert('Tarjeta registrada correctamente'); window.location.href='menu.php';</script>";
    } else {
        // Si hay un error, mostrar un mensaje
        echo "<script>alert('Error al registrar la tarjeta. Inténtelo de nuevo.'); window.location.href='index_tarjeta.php';</script>";
    }

    // Cerrar la conexión
    $stmt = null;
    $conexion = null;
}
?>
