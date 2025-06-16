<?php
// Conexión a la base de datos
$con = mysqli_connect("localhost", "root", "", "biblioteca");

// Verificar conexión
if (!$con) {
    die("Error en la conexión: " . mysqli_connect_error());
}

// Procesar formulario
$mensaje = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    $sql = "INSERT INTO usuarios (nombre, apellidos, correo, contrasena)
            VALUES ('$nombre', '$apellidos', '$correo', '$contrasena')";

    if (mysqli_query($con, $sql)) {
        $mensaje = "✅ Usuario registrado correctamente.";
    } else {
        $mensaje = "❌ Error: " . mysqli_error($con);
    }
}
?>
<!DOCTYPE html>
<html lang="es" class="dark">

<head>
	<meta charset="UTF-8">
	<title>Registro de Usuario</title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center">
	<div class="w-full max-w-md bg-gray-800 p-6 rounded-lg shadow-lg">
		<h2 class="text-2xl font-bold mb-6 text-center">Registro de Usuario</h2>

		<?php if (!empty($mensaje)): ?>
		<div class="mb-4 text-center text-sm p-3 rounded 
                        <?= str_contains($mensaje, '✅') ? 'bg-green-600' : 'bg-red-600' ?>">
			<?php echo $mensaje; ?>
		</div>
		<?php endif; ?>

		<form method="POST" class="space-y-4">
			<div>
				<label class="block mb-1" for="nombre">Nombre</label>
				<input type="text" id="nombre" name="nombre" required
					class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
			</div>

			<div>
				<label class="block mb-1" for="apellidos">Apellidos</label>
				<input type="text" id="apellidos" name="apellidos" required
					class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
			</div>

			<div>
				<label class="block mb-1" for="correo">Correo</label>
				<input type="email" id="correo" name="correo" required
					class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
			</div>

			<div>
				<label class="block mb-1" for="contrasena">Contraseña</label>
				<input type="text" id="contrasena" name="contrasena" required
					class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
			</div>

			<button type="submit"
				class="w-full bg-blue-600 hover:bg-blue-700 py-2 rounded text-white font-semibold transition duration-200">
				Registrar
			</button>
		</form>
	</div>
</body>

</html>