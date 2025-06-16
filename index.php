<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'bibliotecario') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<title>Dashboard Administrador</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body class="bg-gray-100">

	<div class="flex h-screen">

		<!-- Sidebar -->
		<aside class="w-64 bg-indigo-800 text-white flex flex-col p-4">
			<h2 class="text-2xl font-bold mb-6 text-center">Admin Panel</h2>
			<nav class="flex-1 space-y-4">
				<a href="dashboard_admin.php"
					class="block px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700">Inicio</a>
				<a href="flujo3/bandeja_admin.php" class="block px-4 py-2 hover:bg-indigo-700 rounded">Bandeja
					adquisiciones</a>
				<a href="bandeja.php" class="block px-4 py-2 hover:bg-indigo-700 rounded">Bandeja reservas</a>
				<a href="gestionar_libros.php" class="block px-4 py-2 hover:bg-indigo-700 rounded">Gestionar libros</a>
			</nav>
			<form action="logout.php" method="POST" class="mt-auto">
				<button type="submit" class="w-full mt-4 bg-red-600 hover:bg-red-700 py-2 rounded">Cerrar
					sesión</button>
			</form>
		</aside>

		<!-- Main Content -->
		<main class="flex-1 p-6 overflow-y-auto">
			<h1 class="text-3xl font-bold text-gray-800 mb-6">Bienvenido, Bibliotecario</h1>

			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

				<!-- Card 1 -->
				<a href="flujo3/bandeja_admin.php" class="bg-white rounded-lg shadow p-5 hover:shadow-lg transition">
					<div class="flex items-center space-x-4">
						<i class="ph ph-books text-indigo-600 text-4xl"></i>
						<div>
							<h2 class="text-lg font-bold text-gray-800">Adquisiciones</h2>
							<p class="text-sm text-gray-600">Gestiona solicitudes de nuevos libros.</p>
						</div>
					</div>
				</a>

				<!-- Card 2 -->
				<a href="bandeja.php" class="bg-white rounded-lg shadow p-5 hover:shadow-lg transition">
					<div class="flex items-center space-x-4">
						<i class="ph ph-calendar-check text-green-600 text-4xl"></i>
						<div>
							<h2 class="text-lg font-bold text-gray-800">Reservas</h2>
							<p class="text-sm text-gray-600">Consulta y aprueba reservas de libros.</p>
						</div>
					</div>
				</a>

				<!-- Card 3 -->
				<a href="gestionar_libros.php" class="bg-white rounded-lg shadow p-5 hover:shadow-lg transition">
					<div class="flex items-center space-x-4">
						<i class="ph ph-book-open-text text-yellow-500 text-4xl"></i>
						<div>
							<h2 class="text-lg font-bold text-gray-800">Libros</h2>
							<p class="text-sm text-gray-600">Agregar, editar o eliminar libros del catálogo.</p>
						</div>
					</div>
				</a>

				<!-- Puedes agregar más cartillas aquí -->
			</div>
		</main>
	</div>

</body>

</html>