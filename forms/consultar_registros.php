<?php
// Conexión a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'meta_mercado_talpetate');

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Consultar los registros de tipo_arrendamiento
$query_arrendamiento = "SELECT * FROM tipo_arrendamiento";
$result_arrendamiento = $conexion->query($query_arrendamiento);

// Consultar los registros de giro_negocio
$query_giro = "SELECT * FROM giro_negocio";
$result_giro = $conexion->query($query_giro);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Registros</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gray-100 p-4">

    <div class="w-full max-w-md bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-center mb-6">Consultar Registros</h2>

        <h3 class="text-xl mb-4">Tipos de Arrendamiento</h3>
        <table class="w-full table-auto border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 border">ID</th>
                    <th class="p-2 border">Tipo de Arrendamiento</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_arrendamiento->fetch_assoc()): ?>
                    <tr>
                        <td class="p-2 border"><?php echo $row['id']; ?></td>
                        <td class="p-2 border"><?php echo $row['tipo_arrendamiento']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h3 class="text-xl mb-4 mt-6">Giros de Negocio</h3>
        <table class="w-full table-auto border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 border">ID</th>
                    <th class="p-2 border">Giro de Negocio</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_giro->fetch_assoc()): ?>
                    <tr>
                        <td class="p-2 border"><?php echo $row['id']; ?></td>
                        <td class="p-2 border"><?php echo $row['giro_negocio']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>
</html>

<?php
$conexion->close();
?>
