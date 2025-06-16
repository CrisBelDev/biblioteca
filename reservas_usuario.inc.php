<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8" />
	<title>Reservas - Usuario</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

	<div class="container mx-auto px-4 py-8">
		<header class="mb-8">
			<h1 class="text-3xl font-bold text-indigo-700">Bienvenido, <?php echo htmlspecialchars($nombre_usuario); ?>
			</h1>
			<?php if ($flujo && $proceso): ?>
			<p class="text-gray-600">Flujo: <?php echo htmlspecialchars($flujo); ?>, Proceso:
				<?php echo htmlspecialchars($proceso); ?></p>
			<?php endif; ?>
			<p class="text-gray-600">Aquí puedes hacer una nueva reserva o ver tu historial.</p>
		</header>

		<!-- Mostrar mensaje si existe -->
		<?php if ($mensaje): ?>
		<div
			class="mb-6 p-4 rounded-md <?php echo strpos($mensaje, 'Error') === false ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
			<?php echo htmlspecialchars($mensaje); ?>
		</div>
		<?php endif; ?>

		<div class="grid md:grid-cols-2 gap-8">
			<!-- Formulario de nueva reserva -->
			<div class="bg-white p-6 rounded-lg shadow-md">
				<h2 class="text-xl font-semibold mb-4 text-indigo-600 border-b pb-2">Nueva Reserva</h2>
				<form method="post"
					action="controlador.php?flujo=<?php echo urlencode($flujo); ?>&proceso=<?php echo urlencode($proceso); ?>">
					<div class="mb-4">
						<label for="id_libro" class="block text-gray-700 mb-2">Libro</label>
						<select id="id_libro" name="id_libro" required
							class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
							<option value="">Seleccione un libro</option>
							<?php
                                $sql = "SELECT * FROM libros";
                                $result = mysqli_query($con, $sql);
                                while ($libro = mysqli_fetch_assoc($result)) {
                                    echo "<option value='{$libro['id_libro']}'>{$libro['titulo']} - {$libro['autor']}</option>";
                                }
                            ?>
						</select>
					</div>
					<button type="submit"
						class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
						Registrar Reserva
					</button>
				</form>
			</div>
			<!-- boton de enlace a listar_reservas_usuario.php -->
			<div class="bg-white p-6 rounded-lg shadow-md">
				<h2 class="text-xl font-semibold mb-4 text-indigo-600 border-b pb-2">Historial de Reservas</h2>
				<p class="text-gray-700 mb-4">Ver todas tus reservas pasadas y actuales.</p>
				<a href="listar_reservas_usuario.php"
					class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
					Siguiente -->: Listar Reservas
				</a>

			</div>

			<div class="mt-8">
				<a href="logout.php"
					class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors">
					Cerrar Sesión
				</a>
			</div>
		</div>

		<?php mysqli_close($con); ?>
</body>

</html>