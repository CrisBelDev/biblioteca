<?php
// Conexión a la base de datos
$con = mysqli_connect("localhost", "root", "", "biblioteca");
if (!$con) {
    die("Error en la conexión: " . mysqli_connect_error());
}

$mensaje = '';
$reserva_exitosa = false;

// Procesar formulario de registro de flujo y proceso
if (isset($_POST['registrar_proceso'])) {
    $flujo = mysqli_real_escape_string($con, $_POST['flujo']);
    $proceso = mysqli_real_escape_string($con, $_POST['proceso']);
    $siguiente = mysqli_real_escape_string($con, $_POST['siguiente']);
    $pantalla = mysqli_real_escape_string($con, $_POST['pantalla']);
    $rol = mysqli_real_escape_string($con, $_POST['rol']);

    if ($flujo && $proceso && $pantalla && $rol) {
        $sql = "INSERT INTO flujo_proceso (flujo, proceso, siguiente, pantalla, rol)
                VALUES ('$flujo', '$proceso', '$siguiente', '$pantalla', '$rol')";
        if (mysqli_query($con, $sql)) {
            $mensaje = "Proceso registrado correctamente.";
        } else {
            $mensaje = "Error al registrar proceso: " . mysqli_error($con);
        }
    } else {
        $mensaje = "Por favor, completa todos los campos obligatorios.";
    }
}

// Procesar formulario de nueva reserva
if (isset($_POST['nueva_reserva'])) {
    $usuario = mysqli_real_escape_string($con, $_POST['usuario']);
    $id_libro = (int)$_POST['id_libro'];

    // Obtener primer proceso del flujo F1
    $sql = "SELECT proceso FROM flujo_proceso WHERE flujo='F1' ORDER BY proceso ASC LIMIT 1";
    $result = mysqli_query($con, $sql);
    $proceso = mysqli_fetch_assoc($result)['proceso'];

    $sql = "INSERT INTO reservas (usuario, flujo, proceso, id_libro, fecha_inicio, estado)
            VALUES ('$usuario', 'F1', 'P3', $id_libro, NOW(), 'pendiente')";

    if (mysqli_query($con, $sql)) {
        $id_reserva = mysqli_insert_id($con);
        $mensaje = "Reserva iniciada correctamente. ID: $id_reserva";
        $reserva_exitosa = true;
    } else {
        $mensaje = "Error al crear reserva: " . mysqli_error($con);
    }
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Sistema de Reservas - Biblioteca</title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">

	<?php if ($reserva_exitosa): ?>
	<div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-70 z-50">
		<div class="bg-white p-8 rounded-lg shadow-xl text-center max-w-md mx-auto animate-pulse">
			<h2 class="text-3xl font-extrabold text-indigo-700 mb-4">¡Solicitud en proceso!</h2>
			<p class="text-gray-800 text-lg mb-3">Espere al agente de biblioteca para recibir su libro.</p>
			<p class="text-sm text-gray-500">Será redirigido automáticamente en 5 segundos...</p>
		</div>
	</div>
	<script>
	setTimeout(() => {
		window.location.href = "index.php";
	}, 5000);
	</script>
	<?php endif; ?>

	<div class="container mx-auto px-4 py-8">
		<header class="mb-8">
			<h1 class="text-3xl font-bold text-indigo-700">Sistema de Reservas de Libros</h1>
			<p class="text-gray-600">Biblioteca Municipal</p>
		</header>

		<?php if ($mensaje): ?>
		<a href="confirmar_reserva.php?id_reserva=<?php echo isset($id_reserva) ? $id_reserva : ''; ?>"
			class="inline-block mb-4 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
			Confirmar Reserva
		</a>
		<div
			class="mb-6 p-4 rounded-md <?php echo strpos($mensaje, 'Error') === false ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
			<?php echo htmlspecialchars($mensaje); ?>
		</div>
		<?php endif; ?>

		<div class="grid md:grid-cols-2 gap-8">
			<!-- Formulario para nueva reserva -->
			<div class="bg-white p-6 rounded-lg shadow-md">
				<h2 class="text-xl font-semibold mb-4 text-indigo-600 border-b pb-2">Nueva Reserva</h2>
				<form method="post" action="">
					<div class="mb-4">
						<label for="usuario" class="block text-gray-700 mb-2">Usuario</label>
						<input type="text" id="usuario" name="usuario" required
							class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
					</div>

					<div class="mb-4">
						<label for="id_libro" class="block text-gray-700 mb-2">Libro</label>
						<select id="id_libro" name="id_libro" required
							class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
							<option value="">Seleccione un libro</option>
							<?php
                            $con = mysqli_connect("localhost", "root", "", "biblioteca");
                            $sql = "SELECT * FROM libros";
                            $result = mysqli_query($con, $sql);
                            while ($libro = mysqli_fetch_assoc($result)) {
                                echo "<option value='{$libro['id_libro']}'>{$libro['titulo']} - {$libro['autor']}</option>";
                            }
                            mysqli_close($con);
                            ?>
						</select>
					</div>

					<button type="submit" name="nueva_reserva"
						class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
						Iniciar Reserva
					</button>
				</form>
			</div>

			<!-- Formulario para registrar procesos (admin) -->
			<div class="bg-white p-6 rounded-lg shadow-md">
				<h2 class="text-xl font-semibold mb-4 text-indigo-600 border-b pb-2">Registrar Proceso (Admin)</h2>
				<form method="post" action="">
					<div class="mb-4">
						<label for="flujo" class="block text-gray-700 mb-2">Flujo (ej. F1)</label>
						<input type="text" id="flujo" name="flujo" maxlength="3" required
							class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
					</div>

					<div class="mb-4">
						<label for="proceso" class="block text-gray-700 mb-2">Proceso (ej. P1)</label>
						<input type="text" id="proceso" name="proceso" maxlength="3" required
							class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
					</div>

					<div class="mb-4">
						<label for="siguiente" class="block text-gray-700 mb-2">Siguiente Proceso</label>
						<input type="text" id="siguiente" name="siguiente" maxlength="3"
							class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
					</div>

					<div class="mb-4">
						<label for="pantalla" class="block text-gray-700 mb-2">Pantalla</label>
						<input type="text" id="pantalla" name="pantalla" maxlength="30" required
							class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
					</div>

					<div class="mb-4">
						<label for="rol" class="block text-gray-700 mb-2">Rol</label>
						<select id="rol" name="rol" required
							class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
							<option value="">Seleccione un rol</option>
							<option value="usuario">Usuario</option>
							<option value="bibliotecario">Bibliotecario</option>
						</select>
					</div>

					<button type="submit" name="registrar_proceso"
						class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
						Registrar Proceso
					</button>
				</form>
			</div>
		</div>

		<div class="mt-8">
			<a href="bandeja.php"
				class="inline-block bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors">
				Ver todas las reservas
			</a>
		</div>
	</div>
</body>

</html>