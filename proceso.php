<?php
// Conexión a la base de datos
$con = mysqli_connect("localhost", "root", "", "biblioteca");
if (!$con) {
    die("Error en la conexión: " . mysqli_connect_error());
}
session_start();

// Obtener parámetros
$id_reserva = (int)$_GET['id_reserva'];
$flujo = mysqli_real_escape_string($con, $_GET['flujo']);
$proceso = mysqli_real_escape_string($con, $_GET['proceso']);

// Obtener información de la reserva
$sql = "SELECT r.*, l.titulo, l.autor, fp.pantalla, fp.rol, fp.siguiente
        FROM reservas r
        LEFT JOIN libros l ON r.id_libro = l.id_libro
        LEFT JOIN flujo_proceso fp ON r.flujo = fp.flujo AND r.proceso = fp.proceso
        WHERE r.id_reserva = $id_reserva AND r.flujo = '$flujo' AND r.proceso = '$proceso'";

$resultado = mysqli_query($con, $sql);
$reserva = mysqli_fetch_assoc($resultado);

if (!$reserva) {
    die("Reserva no encontrada.");
}

// Obtener todos los procesos del flujo para mostrar el progreso
$sql_flujo = "SELECT * FROM flujo_proceso WHERE flujo='$flujo' ORDER BY proceso";
$result_flujo = mysqli_query($con, $sql_flujo);
$procesos_flujo = [];
while ($fila = mysqli_fetch_assoc($result_flujo)) {
    $procesos_flujo[] = $fila;
}

// Función para obtener el siguiente proceso
function obtenerSiguienteProceso($con, $flujo, $proceso) {
    $sql = "SELECT siguiente FROM flujo_proceso WHERE flujo='$flujo' AND proceso='$proceso'";
    $res = mysqli_query($con, $sql);
    if ($res && mysqli_num_rows($res) > 0) {
        $fila = mysqli_fetch_assoc($res);
        return $fila['siguiente'] ?: null;
    }
    return null;
}

// Procesar actualización de estado
if (isset($_POST['actualizar_estado'])) {
    $nuevo_estado = mysqli_real_escape_string($con, $_POST['estado']);
    $sql = "UPDATE reservas SET estado='$nuevo_estado' WHERE id_reserva=$id_reserva";
    mysqli_query($con, $sql);
    
    // Si se confirma, avanzar al siguiente proceso si existe
    if ($nuevo_estado == 'confirmada') {
        $siguiente = obtenerSiguienteProceso($con, $flujo, $proceso);
        if ($siguiente) {
            $sql = "UPDATE reservas SET proceso='$siguiente', fecha_fin=NOW() 
                    WHERE id_reserva=$id_reserva AND flujo='$flujo' AND proceso='$proceso'";
            mysqli_query($con, $sql);
            
            // Redirigir al siguiente proceso
            header("Location: proceso.php?id_reserva=$id_reserva&flujo=$flujo&proceso=$siguiente");
            exit();
        }
    }
    
    // Recargar la página para ver los cambios
    header("Location: proceso.php?id_reserva=$id_reserva&flujo=$flujo&proceso=$proceso");
    exit();
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Proceso de Reserva - Biblioteca</title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
	<div class="container mx-auto px-4 py-8">
		<header class="mb-8">
			<h1 class="text-3xl font-bold text-indigo-700">Detalle de Reserva #<?php echo $id_reserva; ?></h1>
			<p class="text-gray-600">Proceso: <?php echo htmlspecialchars("$flujo-$proceso"); ?></p>
		</header>

		<!-- Visualización del flujo completo -->
		<div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
			<div class="p-6">
				<h2 class="text-xl font-semibold mb-4 text-indigo-600">Progreso de la Reserva</h2>
				<div class="flex items-center justify-between">
					<?php foreach ($procesos_flujo as $index => $proc): ?>
					<div class="flex flex-col items-center">
						<div
							class="w-12 h-12 rounded-full flex items-center justify-center 
                                <?php echo $proc['proceso'] == $proceso ? 'bg-indigo-600 text-white' : 
									($index < array_search($proceso, array_column($procesos_flujo, 'proceso')) ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700'); ?>">
							<?php echo substr($proc['proceso'], 1); ?>
						</div>
						<span
							class="mt-2 text-sm text-center <?php echo $proc['proceso'] == $proceso ? 'font-bold text-indigo-600' : ''; ?>">
							<?php echo $proc['pantalla']; ?>
						</span>
					</div>
					<?php if ($index < count($procesos_flujo) - 1): ?>
					<div
						class="flex-1 h-1 mx-2 <?php echo $index < array_search($proceso, array_column($procesos_flujo, 'proceso')) ? 'bg-green-500' : 'bg-gray-200'; ?>">
					</div>
					<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<!-- Resto del código permanece igual -->
		<div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
			<div class="p-6">
				<div class="grid md:grid-cols-2 gap-6">
					<div>
						<h2 class="text-xl font-semibold mb-4 text-indigo-600">Información del Libro</h2>
						<div class="space-y-2">
							<p><span class="font-semibold">Título:</span>
								<?php echo htmlspecialchars($reserva['titulo']); ?></p>
							<p><span class="font-semibold">Autor:</span>
								<?php echo htmlspecialchars($reserva['autor']); ?></p>
						</div>
					</div>

					<div>
						<h2 class="text-xl font-semibold mb-4 text-indigo-600">Detalles de la Reserva</h2>
						<div class="space-y-2">
							<p><span class="font-semibold">Usuario:</span>
								<?php echo htmlspecialchars($reserva['usuario']); ?></p>
							<p><span class="font-semibold">Estado:</span>
								<?php 
                                $estadoColor = '';
                                switch ($reserva['estado']) {
                                    case 'pendiente': $estadoColor = 'bg-yellow-100 text-yellow-800'; break;
                                    case 'confirmada': $estadoColor = 'bg-blue-100 text-blue-800'; break;
                                    case 'completada': $estadoColor = 'bg-green-100 text-green-800'; break;
                                    case 'cancelada': $estadoColor = 'bg-red-100 text-red-800'; break;
                                }
                                echo "<span class='px-2 py-1 rounded-full text-sm font-semibold $estadoColor'>" . htmlspecialchars($reserva['estado']) . "</span>";
                                ?>
							</p>
							<p><span class="font-semibold">Iniciada:</span>
								<?php echo htmlspecialchars($reserva['fecha_inicio']); ?></p>
							<?php if ($reserva['fecha_fin']): ?>
							<p><span class="font-semibold">Finalizada:</span>
								<?php echo htmlspecialchars($reserva['fecha_fin']); ?></p>
							<?php endif; ?>
						</div>
					</div>
				</div>

				<div class="mt-6">
					<h2 class="text-xl font-semibold mb-4 text-indigo-600">Proceso Actual</h2>
					<div class="space-y-2">
						<p><span class="font-semibold">Pantalla:</span>
							<?php echo htmlspecialchars($reserva['pantalla']); ?></p>
						<p><span class="font-semibold">Rol responsable:</span>
							<?php echo htmlspecialchars($reserva['rol']); ?></p>
						<p><span class="font-semibold">Siguiente proceso:</span>
							<?php echo htmlspecialchars($reserva['siguiente'] ?: 'Ninguno'); ?></p>
					</div>
				</div>
			</div>
		</div>

		<!-- Botón para confirmar reserva solo si hay sesión de tipo rol -->
		<?php if (!empty($_SESSION['rol'])): ?>
		<div class="bg-white rounded-lg shadow-md p-6 mb-6">
			<a href="verificar_disponibilidad.php?id_reserva=<?php echo $id_reserva; ?>"
				class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors inline-block">
				verificar disponibilidad
			</a>
		</div>
		<?php endif; ?>

		<?php if (!empty($_SESSION['rol'])): ?>
		<div class="bg-white rounded-lg shadow-md p-6 mb-6">
			<a href="confirmar_reserva.php?id_reserva=<?php echo $id_reserva; ?>"
				class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors inline-block">
				confirmar reserva
			</a>
		</div>
		<?php endif; ?>

		<?php if (!empty($_SESSION['rol'])): ?>
		<div class="bg-white rounded-lg shadow-md p-6 mb-6">
			<a href="retirar_libro.php?id_reserva=<?php echo $id_reserva; ?>"
				class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors inline-block">
				retirar libro
			</a>
		</div>
		<?php endif; ?>

		<!-- Formulario para actualizar estado -->
		<div class="bg-white rounded-lg shadow-md p-6">
			<h2 class="text-xl font-semibold mb-4 text-indigo-600">Actualizar Estado</h2>
			<form method="post" action="">
				<div class="mb-4">
					<label for="estado" class="block text-gray-700 mb-2">Nuevo estado</label>
					<select id="estado" name="estado" required
						class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
						<option value="pendiente" <?php echo $reserva['estado'] == 'pendiente' ? 'selected' : ''; ?>>
							Pendiente</option>
						<option value="confirmada" <?php echo $reserva['estado'] == 'confirmada' ? 'selected' : ''; ?>>
							Confirmada</option>
						<option value="completada" <?php echo $reserva['estado'] == 'completada' ? 'selected' : ''; ?>>
							Completada</option>
						<option value="cancelada" <?php echo $reserva['estado'] == 'cancelada' ? 'selected' : ''; ?>>
							Cancelada</option>
					</select>
				</div>

				<button type="submit" name="actualizar_estado"
					class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
					Actualizar Estado
				</button>
			</form>
		</div>

		<div class="mt-4">
			<a href="bandeja.php" class="text-indigo-600 hover:text-indigo-900">← Volver a la bandeja</a>
		</div>
	</div>
</body>

</html>