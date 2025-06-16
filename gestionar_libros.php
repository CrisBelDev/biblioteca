<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'bibliotecario') {
    header("Location: login.php");
    exit();
}

$conexion = new mysqli("localhost", "root", "", "biblioteca");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// AGREGAR LIBRO
if (isset($_POST['agregar'])) {
    $titulo = $conexion->real_escape_string($_POST['titulo']);
    $autor = $conexion->real_escape_string($_POST['autor']);
    $conexion->query("INSERT INTO libros (titulo, autor) VALUES ('$titulo', '$autor')");
    header("Location: gestionar_libros.php");
    exit();
}

// ELIMINAR LIBRO
if (isset($_GET['eliminar'])) {
    $id = (int) $_GET['eliminar'];
    $conexion->query("DELETE FROM libros WHERE id_libro = $id");
    header("Location: gestionar_libros.php");
    exit();
}

// EDITAR LIBRO
if (isset($_POST['editar'])) {
    $id = (int) $_POST['id_libro'];
    $titulo = $conexion->real_escape_string($_POST['titulo']);
    $autor = $conexion->real_escape_string($_POST['autor']);
    $conexion->query("UPDATE libros SET titulo='$titulo', autor='$autor' WHERE id_libro=$id");
    header("Location: gestionar_libros.php");
    exit();
}

// OBTENER LIBROS
$resultado = $conexion->query("SELECT * FROM libros ORDER BY id_libro DESC");
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<title>Gestionar Libros</title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6">
	<div class="max-w-5xl mx-auto">
		<h1 class="text-3xl font-bold text-indigo-700 mb-6">📚 Gestión de Libros</h1>

		<!-- Formulario de agregar -->
		<div class="bg-white p-6 rounded shadow mb-6">
			<h2 class="text-xl font-semibold mb-4">Agregar libro</h2>
			<form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
				<input type="text" name="titulo" placeholder="Título" required class="border rounded px-4 py-2">
				<input type="text" name="autor" placeholder="Autor" required class="border rounded px-4 py-2">
				<button type="submit" name="agregar"
					class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
					Agregar
				</button>
			</form>
		</div>

		<!-- Tabla de libros -->
		<div class="bg-white p-6 rounded shadow">
			<h2 class="text-xl font-semibold mb-4">Listado de libros</h2>
			<table class="min-w-full table-auto border">
				<thead>
					<tr class="bg-indigo-100 text-indigo-800">
						<th class="px-4 py-2 border">ID</th>
						<th class="px-4 py-2 border">Título</th>
						<th class="px-4 py-2 border">Autor</th>
						<th class="px-4 py-2 border">Acciones</th>
					</tr>
				</thead>
				<tbody>
					<?php while ($libro = $resultado->fetch_assoc()): ?>
					<tr class="hover:bg-gray-50">
						<td class="px-4 py-2 border"><?php echo $libro['id_libro']; ?></td>
						<td class="px-4 py-2 border"><?php echo htmlspecialchars($libro['titulo']); ?></td>
						<td class="px-4 py-2 border"><?php echo htmlspecialchars($libro['autor']); ?></td>
						<td class="px-4 py-2 border">
							<!-- Editar -->
							<button
								onclick="mostrarEditar(<?php echo $libro['id_libro']; ?>, '<?php echo addslashes($libro['titulo']); ?>', '<?php echo addslashes($libro['autor']); ?>')"
								class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded">
								Editar
							</button>
							<!-- Eliminar -->
							<a href="?eliminar=<?php echo $libro['id_libro']; ?>"
								onclick="return confirm('¿Eliminar este libro?');"
								class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded ml-2">
								Eliminar
							</a>
						</td>
					</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>

	<!-- Modal de edición -->
	<div id="modalEditar" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
		<div class="bg-white p-6 rounded shadow max-w-md w-full">
			<h2 class="text-xl font-semibold mb-4">Editar libro</h2>
			<form method="POST" class="space-y-4">
				<input type="hidden" name="id_libro" id="edit_id">
				<input type="text" name="titulo" id="edit_titulo" required class="w-full border rounded px-4 py-2">
				<input type="text" name="autor" id="edit_autor" required class="w-full border rounded px-4 py-2">
				<div class="flex justify-end gap-2">
					<button type="button" onclick="cerrarModal()"
						class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Cancelar</button>
					<button type="submit" name="editar"
						class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">Guardar</button>
				</div>
			</form>
		</div>
	</div>

	<script>
	function mostrarEditar(id, titulo, autor) {
		document.getElementById('edit_id').value = id;
		document.getElementById('edit_titulo').value = titulo;
		document.getElementById('edit_autor').value = autor;
		document.getElementById('modalEditar').classList.remove('hidden');
	}

	function cerrarModal() {
		document.getElementById('modalEditar').classList.add('hidden');
	}
	</script>
</body>

</html>