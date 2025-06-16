<?php
session_start();

// Conexión a la base de datos
$con = mysqli_connect("localhost", "root", "", "biblioteca");
if (!$con) {
    die("Error en la conexión: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    // Consulta para verificar usuario
    $sql = "SELECT * FROM usuarios WHERE correo = '$correo' AND contrasena = '$contrasena'";
    $resultado = mysqli_query($con, $sql);

    if ($fila = mysqli_fetch_assoc($resultado)) {
        // Usuario encontrado, guardar rol en sesión y redirigir
        $_SESSION['rol'] = $fila['rol'];
        $_SESSION['nombre'] = $fila['nombre']; // opcional
		$_SESSION['id_usuario'] = $fila['id']; // opcional

		if ($fila['rol'] === 'usuario') {
			header('Location: reservas_usuario.php?flujo=F1&proceso=P1');
		} else if ($fila['rol'] === 'bibliotecario') {
			header('Location: bandeja.php');
		} else if ($fila['rol'] === 'almacen') {
			header('Location: flujo3/bandeja.php');
		}
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es" class="dark">

<head>
	<meta charset="UTF-8">
	<title>Login Bibliotecario</title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center">
	<div class="w-full max-w-sm p-6 bg-gray-800 rounded-xl shadow-lg">
		<h2 class="text-2xl font-bold text-center mb-6">Login Bibliotecario</h2>

		<?php if (!empty($error)): ?>
		<div class="bg-red-500 text-white text-sm p-3 rounded mb-4 text-center">
			<?php echo $error; ?>
		</div>
		<?php endif; ?>

		<form method="post" class="space-y-4">
			<div>
				<label for="correo" class="block mb-1">Correo</label>
				<input type="text" id="correo" name="correo" required
					class="w-full px-3 py-2 bg-gray-700 text-white border border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
			</div>

			<div>
				<label for="contrasena" class="block mb-1">Contraseña</label>
				<input type="password" id="contrasena" name="contrasena" required
					class="w-full px-3 py-2 bg-gray-700 text-white border border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
			</div>

			<button type="submit"
				class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 rounded text-white font-semibold transition duration-200">
				Ingresar
			</button>

			no tienes cuenta? <a href="registro_usuario.php" class="text-blue-400 hover:text-blue-300">Regístrate
				aquí</a>
		</form>
	</div>
</body>

</html>