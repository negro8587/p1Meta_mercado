<?php
session_start(); // Inicia la sesión para guardar la elección del mercado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["mercado"])) {
    $_SESSION['id_mercado'] = $_POST["mercado"];
}
//Redireccionar al index.php si no hay id_mercado
if (!isset($_SESSION['id_mercado'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Municipal</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .menu-button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border: 2px solid rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .menu-button:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.63);
        }
        .icon {
            font-size: 1.5rem;
        }
        .return-button {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #4ade80;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            border: none;
            cursor: pointer;
        }
        .return-button:hover {
            background-color: #22c55e;
        }
         .reporte-button {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
             background-color: #6686C9;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            border: none;
            cursor: pointer;
        }
         .reporte-button:hover {
             background-color: #4F70B2;
         }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
<div class="w-full max-w-md">
    <div class="menu-card">
        <!-- Logo -->
        <div class="logo-container mb-6">
            <img src="./images/logo muni 2024.png" alt="Logo Municipalidad 2024" class="w-32 mx-auto">
        </div>

        <!-- Título -->
        <?php
        include 'conexion.php';
        $id_mercado = $_SESSION['id_mercado'];
        try{
            $stmt = $conexion->prepare("SELECT nombre_mercado FROM mercado WHERE id_mercado = :id_mercado");
            $stmt->bindParam(':id_mercado', $id_mercado, PDO::PARAM_INT);
            $stmt->execute();
            $mercado = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($mercado) {
                echo "<h1 class='text-3xl font-extrabold text-green-700 mb-8'>{$mercado['nombre_mercado']}</h1>";
            } else {
                echo "<h1 class='text-3xl font-extrabold text-red-700 mb-8'>Error: Mercado no encontrado</h1>";
            }
        } catch (PDOException $e) {
            echo "Error al cargar el nombre del mercado: " . $e->getMessage();
        }
        ?>

        <!-- Botones del menú -->
        <div class="grid grid-cols-1 gap-4">
            <a href="forms/index_arrendatario.php" class="menu-button bg-green-500 hover:bg-green-600 text-white py-3 px-6 rounded-lg text-lg font-semibold">
                <span class="icon">👤</span> Registrar Usuario
            </a>
            <a href="forms/index_contrato.php" class="menu-button bg-blue-500 hover:bg-blue-600 text-white py-3 px-6 rounded-lg text-lg font-semibold">
                <span class="icon">📄</span> Formulario de Contrato
            </a>
            <a href="forms/index_tarjeta.php" class="menu-button bg-yellow-500 hover:bg-yellow-600 text-white py-3 px-6 rounded-lg text-lg font-semibold">
                <span class="icon">💳</span> Formulario de Tarjeta
            </a>
            <a href="forms/agregar_registros.php" class="menu-button bg-purple-500 hover:bg-purple-600 text-white py-3 px-6 rounded-lg text-lg font-semibold">
                <span class="icon">➕</span> Agregar Registros
            </a>
            <a href="forms/consulta_datos.php" class="menu-button bg-indigo-500 hover:bg-indigo-600 text-white py-3 px-6 rounded-lg text-lg font-semibold">
                <span class="icon">🔍</span> Buscar Registros
            </a>
            <a href="forms/index_pago.php" class="menu-button bg-pink-500 hover:bg-pink-600 text-white py-3 px-6 rounded-lg text-lg font-semibold">
                <span class="icon">💰</span> Pago Arrendamiento
            </a>
        </div>
        <!-- Enlace para regresar a index.php -->
        <a href="indice.php" class="return-button">Regresar a Selección de Mercado</a>
         <!-- Enlace para el reporte -->
        <a href="reporte.php" class="reporte-button">Ir al Reporte</a>

        <!-- Pie de página -->
        <div class="mt-8 text-sm text-gray-500">
            © 2024 Municipalidad. Todos los derechos reservados.
        </div>
    </div>
</div>
</body>
</html>