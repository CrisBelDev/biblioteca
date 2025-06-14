<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<title>Dashboard Biblioteca</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- TailwindCSS CDN -->
	<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">
	<!-- Sidebar -->
	<div class="flex min-h-screen">
		<aside class="w-64 bg-blue-800 text-white flex flex-col py-8 px-6">
			<div class="mb-10">
				<h2 class="text-2xl font-bold text-center">Admin Biblioteca</h2>
			</div>
			<nav class="flex flex-col gap-4">
				<a href="dashboard.php"
					class="bg-blue-700 rounded px-4 py-2 font-semibold hover:bg-blue-600 transition">Dashboard</a>
				<a href="bandeja.php" class="rounded px-4 py-2 hover:bg-blue-600 transition">Bandeja de Libros</a>
				<a href="registrar_libro.php" class="rounded px-4 py-2 hover:bg-blue-600 transition">Registrar Libro</a>
			</nav>
			<div class="mt-auto pt-10 border-t border-blue-700">
				<button class="w-full bg-red-500 hover:bg-red-600 text-white py-2 rounded transition">Cerrar
					sesión</button>
			</div>
		</aside>
		<!-- Main Content -->
		<main class="flex-1 flex flex-col items-center justify-center p-10">
			<div class="bg-white shadow-lg rounded-lg p-10 w-full max-w-2xl">
				<h1 class="text-3xl font-bold mb-8 text-blue-700 text-center">Bienvenido al Dashboard</h1>
				<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
					<a href="bandeja.php"
						class="flex flex-col items-center bg-blue-100 hover:bg-blue-200 p-6 rounded-lg shadow transition">
						<svg class="w-12 h-12 text-blue-500 mb-2" fill="none" stroke="currentColor" stroke-width="2"
							viewBox="0 0 24 24">
							<path d="M12 20h9"></path>
							<path d="M12 4v16"></path>
							<path d="M4 4h16v16H4z"></path>
						</svg>
						<span class="font-semibold text-blue-700">Bandeja de Libros</span>
					</a>
					<a href="registrar_libro.php"
						class="flex flex-col items-center bg-green-100 hover:bg-green-200 p-6 rounded-lg shadow transition">
						<svg class="w-12 h-12 text-green-500 mb-2" fill="none" stroke="currentColor" stroke-width="2"
							viewBox="0 0 24 24">
							<path d="M12 4v16m8-8H4"></path>
						</svg>
						<span class="font-semibold text-green-700">Registrar Nuevo Libro</span>
					</a>
				</div>
			</div>
		</main>
	</div>
</body>

</html>