<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Registros</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100 p-4">

    <div class="w-full max-w-2xl bg-white p-12 rounded-lg shadow-lg relative">
        <h2 class="text-3xl font-bold text-center mb-8">Agregar Tipo de Arrendamiento y Giro de Negocio</h2>

        <!-- Formulario para agregar tipo de arrendamiento -->
        <form action="procesar_registros.php" method="POST">
            <div class="mb-6">
                <label for="tipo_arrendamiento" class="block text-gray-700 text-lg">Tipo de Arrendamiento</label>
                <input type="text" name="tipo_arrendamiento" id="tipo_arrendamiento" class="w-full p-4 border border-gray-300 rounded-lg" required>
            </div>
            <button type="submit" name="accion" value="agregar_tipo_arrendamiento" class="w-full bg-green-500 text-white p-4 rounded-lg">Agregar Tipo de Arrendamiento</button>
        </form>

        <!-- Formulario para agregar giro de negocio -->
        <form action="procesar_registros.php" method="POST" class="mt-6">
            <div class="mb-6">
                <label for="giro_negocio" class="block text-gray-700 text-lg">Giro de Negocio</label>
                <input type="text" name="giro_negocio" id="giro_negocio" class="w-full p-4 border border-gray-300 rounded-lg" required>
            </div>
            <button type="submit" name="accion" value="agregar_giro_negocio" class="w-full bg-blue-500 text-white p-4 rounded-lg">Agregar Giro de Negocio</button>
        </form>

        <!-- Botón para consultar los registros -->
        <a href="consultar_registros.php" class="w-full bg-yellow-500 text-white p-4 rounded-lg mt-6 block text-center">Consultar Registros</a>

       <!-- Enlace para regresar al menú principal -->
       <a href="menu.php" class="absolute bottom-4 left-4 text-gray-600 text-xs underline">Regresar al Menú Principal</a>
    </div>

</body>
</html>