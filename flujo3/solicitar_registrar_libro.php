<?php
include '../conexion.php';
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'almacen') {
	header("Location: login.php");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$titulo = $_POST['titulo'];
	$autor = $_POST['autor'];
	$id_usuario = $_SESSION['id_usuario'];

	// Insertar el libro con stock 0 y disponible 0 (aún no disponible)
	$query_libro = "INSERT INTO libros (titulo, autor, disponible, stock) VALUES (?, ?, 0, 0)";
	$stmt = mysqli_prepare($con, $query_libro);
	mysqli_stmt_bind_param($stmt, "ss", $titulo, $autor);
	mysqli_stmt_execute($stmt);
	$id_libro = mysqli_insert_id($con);
	mysqli_stmt_close($stmt);

	// Insertar la solicitud en adquisiciones (flujo F3, proceso P1)
	$query_adq = "INSERT INTO adquisiciones (id_libro, id_usuario, flujo, proceso, estado) VALUES (?, ?, 'F3', 'P1', 'pendiente')";
	$stmt2 = mysqli_prepare($con, $query_adq);
	mysqli_stmt_bind_param($stmt2, "ii", $id_libro, $id_usuario);
	mysqli_stmt_execute($stmt2);
	mysqli_stmt_close($stmt2);

	$_SESSION['mensaje'] = "Solicitud de adquisición registrada correctamente.";
	header("Location: bandeja.php");
	exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<title>Solicitar Registro de Libro</title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
	<div class="container mx-auto px-4 py-8">
		<h1 class="text-2xl font-bold text-indigo-700 mb-6">Solicitar Registro de Libro</h1>

		<form method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-4">
			<div>
				<label for="titulo" class="block text-sm font-medium text-gray-700">Título del libro</label>
				<input type="text" name="titulo" id="titulo" required
					class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
			</div>

			<div>
				<label for="autor" class="block text-sm font-medium text-gray-700">Autor</label>
				<input type="text" name="autor" id="autor" required
					class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
			</div>

			<div class="flex justify-end">
				<button type="submit"
					class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Registrar
					solicitud</button>
			</div>
		</form>

		<div class="mt-4">
			<a href="bandeja.php" class="text-indigo-600 hover:text-indigo-900">← Volver a la bandeja</a>
		</div>
	</div>
</body>

</html>