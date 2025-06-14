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
$sql = "SELECT r.*, l.titulo, l.autor, l.disponible, fp.siguiente 
        FROM reservas r
        JOIN libros l ON r.id_libro = l.id_libro
        JOIN flujo_proceso fp ON r.flujo = fp.flujo AND r.proceso = fp.proceso
        WHERE r.id_reserva = $id_reserva";
$resultado = mysqli_query($con, $sql);
$reserva = mysqli_fetch_assoc($resultado);

// Procesar verificación de disponibilidad
if (isset($_POST['verificar'])) {
    $disponible = (int)$_POST['disponible'];
    $siguiente = $reserva['siguiente'];
    
    // Actualizar disponibilidad del libro
    $sql = "UPDATE libros SET disponible = $disponible WHERE id_libro = {$reserva['id_libro']}";
    mysqli_query($con, $sql);
    
    // Actualizar estado y proceso de la reserva
    $estado = $disponible ? 'confirmada' : 'cancelada';
    $sql = "UPDATE reservas SET estado = '$estado', proceso = 'P3', fecha_fin = NOW() 
            WHERE id_reserva = $id_reserva";
    mysqli_query($con, $sql);
    
    // Redirigir al siguiente proceso
    header("Location: proceso.php?id_reserva=$id_reserva&flujo={$reserva['flujo']}&proceso=P3");
    exit();
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Verificar Disponibilidad - Biblioteca</title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
	<div class="container mx-auto px-4 py-8">
		<header class="mb-8">
			<h1 class="text-3xl font-bold text-indigo-700">Verificar Disponibilidad</h1>
			<p class="text-gray-600">Reserva #<?php echo $id_reserva; ?></p>
		</header>

		<div class="bg-white rounded-lg shadow-md p-6 mb-8">
			<h2 class="text-xl font-semibold mb-4 text-indigo-600">Información del Libro</h2>
			<div class="space-y-2">
				<p><span class="font-semibold">Título:</span> <?php echo htmlspecialchars($reserva['titulo']); ?></p>
				<p><span class="font-semibold">Autor:</span> <?php echo htmlspecialchars($reserva['autor']); ?></p>
				<p><span class="font-semibold">Disponibilidad actual:</span>
					<span class="<?php echo $reserva['disponible'] ? 'text-green-600' : 'text-red-600'; ?>">
						<?php echo $reserva['disponible'] ? 'Disponible' : 'No disponible'; ?>
					</span>
				</p>
			</div>
		</div>

		<div class="bg-white rounded-lg shadow-md p-6">
			<h2 class="text-xl font-semibold mb-4 text-indigo-600">Verificar Disponibilidad</h2>
			<form method="post" action="">
				<div class="mb-4">
					<label class="block text-gray-700 mb-2">¿El libro está disponible?</label>
					<div class="flex items-center space-x-4">
						<label class="inline-flex items-center">
							<input type="radio" name="disponible" value="1" class="form-radio text-indigo-600"
								<?php echo $reserva['disponible'] ? 'checked' : ''; ?>>
							<span class="ml-2">Sí</span>
						</label>
						<label class="inline-flex items-center">
							<input type="radio" name="disponible" value="0" class="form-radio text-indigo-600"
								<?php echo !$reserva['disponible'] ? 'checked' : ''; ?>>
							<span class="ml-2">No</span>
						</label>
					</div>
				</div>

				<button type="submit" name="verificar"
					class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
					Verificar y Continuar
				</button>
			</form>
		</div>

		<div class="mt-4">
			<a href="bandeja.php" class="text-indigo-600 hover:text-indigo-900">← Volver a la bandeja</a>
		</div>
	</div>
</body>

</html>