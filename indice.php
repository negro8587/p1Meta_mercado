<?php
// Habilitar la visualización de errores
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selección de Mercado</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #e3fdfd 0%, #ffffff 100%);
            font-family: 'Arial', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
              padding: 20px;
        }
        .menu-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
           max-width: 450px;
           width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .logo-container {
            margin-bottom: 20px;
        }
        .logo-container img {
            max-width: 200px;
            height: auto;
             transition: transform 0.3s ease-in-out;
        }
        .logo-container img:hover{
            transform: scale(1.05);
        }
        .title {
             font-size: 2.2rem;
            font-weight: bold;
            color: #2d7e41;
            margin-bottom: 25px;
        }
        .select-container {
            position: relative;
            display: inline-block;
           width: 100%;
             margin-bottom: 20px;
        }
        .select-container select {
             appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
             padding: 14px 24px;
            border: 2px solid #4ade80;
            border-radius: 10px;
             background-color: white;
            font-size: 1.1rem;
             cursor: pointer;
            width: 100%;
             transition: all 0.3s ease;
         }
         .select-container:after {
             content: '\25BC';
            position: absolute;
            top: 50%;
            right: 15px;
             transform: translateY(-50%);
            pointer-events: none;
             color: #4ade80;
              font-size: 1.2em;
              transition: all 0.3s ease;
        }
          .select-container:hover::after {
           color: #22c55e;

        }
         .select-container select:focus{
             outline: none;
             border-color:#22c55e;
         }
         .menu-button {
              display: inline-block;
            background-color: #4ade80;
             transition: background-color 0.3s ease;
            color: white;
            padding: 12px 24px;
            border-radius: 10px;
             text-decoration: none;
            font-weight: bold;
             border: none;
             cursor: pointer;
            font-size: 1.1rem;
              width: 100%;
          }
          .menu-button:hover{
             background-color: #22c55e;
          }
        .footer {
            margin-top: 30px;
            text-align: center;
           font-size: 0.8em;
            color: #71717a;
        }
          .muni-logo{
            max-width: 300px;
            height: auto;
            margin: 0px auto 25px;
            opacity: 0.8;
             transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;

          }
          .muni-logo:hover{
            opacity: 1;
              transform: scale(1.05);
          }
    </style>
</head>
<body>
    <div class="w-full max-w-md">
        <div class="menu-card">
            <!-- Logo Principal -->
            <div class="logo-container">
                <img src="images/logo muni 2024.png" alt="Logo Municipalidad 2024" class="mx-auto">
            </div>
             <h1 class="title">Selecciona un Mercado</h1>
           <form action="menu.php" method="post" class="space-y-4 w-full" id="market-form">
                <div class="select-container">
                     <select name="mercado" id="mercado">
                       <option value="" disabled selected hidden>Selecciona un mercado</option>
                         <?php
                         include 'conexion.php';
                           try {
                            $stmt = $conexion->query("SELECT id_mercado, nombre_mercado FROM mercado");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='{$row['id_mercado']}'>{$row['nombre_mercado']}</option>";
                            }
                           } catch (PDOException $e) {
                             echo "Error al cargar los mercados: " . $e->getMessage();
                          }
                        ?>
                    </select>
               </div>
               <button type="submit" class="menu-button">Continuar</button>
           </form>
           <img src="images/logo_municipalidad_coban.png" alt="Logo Muni Coban" class="muni-logo mx-auto">
           <div class="footer">
                © 2024 Municipalidad. Todos los derechos reservados.
            </div>
        </div>
   </div>
   <script>
        document.getElementById('market-form').addEventListener('submit', function(event) {
            const mercadoSelect = document.getElementById('mercado');
            if (!mercadoSelect.value) {
               event.preventDefault(); // Evita que el formulario se envíe
               alert('Por favor, selecciona un mercado antes de continuar.'); // Alerta al usuario
             }
           });
    </script>
</body>
</html>