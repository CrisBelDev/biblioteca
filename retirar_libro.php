<?php
session_start();
// Verificar rol
if ($_SESSION['rol'] != 'bibliotecario') {
    header("Location: bandeja.php");
    exit();
}

// Conexión a la base de datos
$con = mysqli_connect("localhost", "root", "", "biblioteca");
if (!$con) {
    die("Error en la conexión: " . mysqli_connect_error());
}

// Obtener parámetros
$id_reserva = (int)$_GET['id_reserva'];

// Obtener información de la reserva
$sql = "SELECT r.*, l.titulo, l.autor 
        FROM reservas r
        JOIN libros l ON r.id_libro = l.id_libro
        WHERE r.id_reserva = $id_reserva";
$resultado = mysqli_query($con, $sql);
$reserva = mysqli_fetch_assoc($resultado);

// Procesar retiro del libro
if (isset($_POST['retirar'])) {
    // Actualizar estado de la reserva y marcar libro como no disponible
    $sql = "UPDATE reservas SET estado = 'completada', fecha_fin = NOW(), proceso = 'P5' WHERE id_reserva = $id_reserva";
    mysqli_query($con, $sql);
    
    $sql = "UPDATE libros SET disponible = 0 WHERE id_libro = {$reserva['id_libro']}";
    mysqli_query($con, $sql);
    
    // Redirigir a la bandeja
    header("Location: bandeja.php");
    exit();
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Retirar Libro - Biblioteca</title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
	<div class="container mx-auto px-4 py-8">
		<header class="mb-8">
			<h1 class="text-3xl font-bold text-indigo-700">Retirar Libro</h1>
			<p class="text-gray-600">Reserva #<?php echo $id_reserva; ?></p>
		</header>

		<div class="bg-white rounded-lg shadow-md p-6 mb-8">
			<h2 class="text-xl font-semibold mb-4 text-indigo-600">Detalles del Retiro</h2>
			<div class="space-y-2">
				<p><span class="font-semibold">Libro:</span> <?php echo htmlspecialchars($reserva['titulo']); ?></p>
				<p><span class="font-semibold">Autor:</span> <?php echo htmlspecialchars($reserva['autor']); ?></p>
				<p><span class="font-semibold">Usuario:</span> <?php echo htmlspecialchars($reserva['id_usuario']); ?>
				</p>
				<p><span class="font-semibold">Estado:</span>
					<span
						class="<?php echo $reserva['estado'] == 'completada' ? 'text-green-600' : 'text-blue-600'; ?>">
						<?php echo htmlspecialchars($reserva['estado']); ?>
					</span>
				</p>
			</div>
		</div>

		<div class="bg-white rounded-lg shadow-md p-6">
			<h2 class="text-xl font-semibold mb-4 text-indigo-600">Proceso de Retiro</h2>
			<p class="mb-4">Por favor confirma que el usuario ha retirado el libro físicamente.</p>

			<form method="post" action="">
				<button type="submit" name="retirar"
					class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
					Confirmar Retiro
				</button>
			</form>
		</div>

		<div class="mt-4">
			<a href="bandeja.php" class="text-indigo-600 hover:text-indigo-900">← Volver a la bandeja</a>
		</div>
	</div>
</body>

</html>