<?php
// Conexión a la base de datos
$con = mysqli_connect("localhost", "root", "", "biblioteca");
if (!$con) {
    die("Error en la conexión: " . mysqli_connect_error());
}

session_start();

// Validar sesión y parámetros
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_reserva = (int)$_GET['id_reserva'];
$flujo = mysqli_real_escape_string($con, $_GET['flujo']);
$proceso = mysqli_real_escape_string($con, $_GET['proceso']);

// Obtener todos los procesos del flujo
$sql_flujo = "SELECT * FROM flujo_proceso WHERE flujo='$flujo' ORDER BY proceso";
$result_flujo = mysqli_query($con, $sql_flujo);
$procesos_flujo = [];
while ($fila = mysqli_fetch_assoc($result_flujo)) {
    $procesos_flujo[] = $fila;
}

// Función para obtener el proceso anterior
function obtenerAnteriorProceso($procesos_flujo, $procesoActual) {
    foreach ($procesos_flujo as $i => $proc) {
        if ($proc['proceso'] === $procesoActual && $i > 0) {
            return $procesos_flujo[$i - 1]['proceso'];
        }
    }
    return null;
}

$procesoAnterior = obtenerAnteriorProceso($procesos_flujo, $proceso);

// Procesar retroceso
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['retroceder_proceso'])) {
    if ($_SESSION['rol'] !== 'usuario') {
        die("No tienes permiso para retroceder el proceso.");
    }

    $proceso_anterior = mysqli_real_escape_string($con, $_POST['proceso_anterior']);

    if ($proceso_anterior === $procesos_flujo[0]['proceso']) {
        $sql = "DELETE FROM reservas WHERE id_reserva=$id_reserva";
        if (mysqli_query($con, $sql)) {
            header("Location: reservas_usuario.php?flujo=$flujo&proceso={$procesos_flujo[0]['proceso']}");
            exit();
        } else {
            echo "Error al eliminar reserva: " . mysqli_error($con);
        }
    } else {
        header("Location: reservas_usuario.php?id_reserva=$id_reserva&flujo=$flujo&proceso=$proceso_anterior");
        exit();
    }
}

// Actualizar estado
if (isset($_POST['actualizar_estado'])) {
    $nuevo_estado = mysqli_real_escape_string($con, $_POST['estado']);
    $sql = "UPDATE reservas SET estado='$nuevo_estado' WHERE id_reserva=$id_reserva";
    mysqli_query($con, $sql);
    header("Location: reservas_usuario.php?id_reserva=$id_reserva&flujo=$flujo&proceso=$proceso");
    exit();
}

// Obtener info de reserva
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

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<title>Proceso de Reserva</title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
	<div class="container mx-auto px-4 py-8">
		<h1 class="text-3xl font-bold text-indigo-700 mb-4">Reserva #<?php echo $id_reserva; ?></h1>
		<p class="text-gray-600 mb-6">Proceso actual: <?php echo "$flujo - $proceso"; ?> (Rol:
			<?php echo $_SESSION['rol']; ?>)</p>

		<!-- Progreso del flujo -->
		<div class="bg-white shadow rounded p-6 mb-6">
			<h2 class="text-xl font-semibold text-indigo-600 mb-4">Progreso</h2>
			<div class="flex items-center justify-between">
				<?php foreach ($procesos_flujo as $index => $proc): ?>
				<div class="flex flex-col items-center">
					<div
						class="w-10 h-10 flex items-center justify-center rounded-full <?php echo $proc['proceso'] == $proceso ? 'bg-indigo-600 text-white' : ($index < array_search($proceso, array_column($procesos_flujo, 'proceso')) ? 'bg-green-500 text-white' : 'bg-gray-300 text-black'); ?>">
						<?php echo substr($proc['proceso'], 1); ?>
					</div>
					<span class="text-sm mt-1 text-center"><?php echo $proc['pantalla']; ?></span>
				</div>
				<?php if ($index < count($procesos_flujo) - 1): ?>
				<div
					class="flex-1 h-1 mx-2 <?php echo $index < array_search($proceso, array_column($procesos_flujo, 'proceso')) ? 'bg-green-500' : 'bg-gray-200'; ?>">
				</div>
				<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- Botón retroceder -->
		<?php if ($procesoAnterior): ?>
		<form method="POST" onsubmit="return confirmarRetroceso();" class="mb-6">
			<input type="hidden" name="retroceder_proceso" value="1">
			<input type="hidden" name="proceso_anterior" value="<?php echo $procesoAnterior; ?>">
			<button class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
				← Paso anterior
			</button>
		</form>
		<?php endif; ?>

		<a href="listar_reservas_usuario.php" class="text-white bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded">
			Volver a bandeja
		</a>

		<!-- Info libro -->
		<div class="mt-6 bg-white p-6 rounded shadow">
			<h2 class="text-lg font-semibold text-indigo-600 mb-2">Libro reservado</h2>
			<p><strong>Título:</strong> <?php echo htmlspecialchars($reserva['titulo']); ?></p>
			<p><strong>Autor:</strong> <?php echo htmlspecialchars($reserva['autor']); ?></p>
			<p><strong>Estado:</strong> <?php echo htmlspecialchars($reserva['estado']); ?></p>
		</div>

		<!-- Formulario para actualizar estado -->
		<form method="POST" class="mt-6 bg-white p-6 rounded shadow">
			<label class="block text-gray-700 mb-2">Actualizar Estado:</label>
			<select name="estado" class="border border-gray-300 rounded px-4 py-2 w-full mb-4">
				<option value="pendiente" <?php if ($reserva['estado'] == 'pendiente') echo 'selected'; ?>>Pendiente
				</option>
				<option value="confirmada" <?php if ($reserva['estado'] == 'confirmada') echo 'selected'; ?>>Confirmada
				</option>
				<option value="cancelada" <?php if ($reserva['estado'] == 'cancelada') echo 'selected'; ?>>Cancelada
				</option>
			</select>
			<button type="submit" name="actualizar_estado"
				class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
				Guardar cambios
			</button>
		</form>
	</div>

	<script>
	function confirmarRetroceso() {
		return confirm("¿Seguro que deseas volver al paso anterior? Si es el primer paso, se eliminará la reserva.");
	}
	</script>
</body>

</html>