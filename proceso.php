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

// Obtener todos los procesos del flujo
$sql_flujo = "SELECT * FROM flujo_proceso WHERE flujo='$flujo' ORDER BY proceso";
$result_flujo = mysqli_query($con, $sql_flujo);
$procesos_flujo = [];
while ($fila = mysqli_fetch_assoc($result_flujo)) {
    $procesos_flujo[] = $fila;
}

// Funciones para obtener proceso anterior y siguiente
function obtenerSiguienteProceso($con, $flujo, $proceso) {
    $sql = "SELECT siguiente FROM flujo_proceso WHERE flujo='$flujo' AND proceso='$proceso'";
    $res = mysqli_query($con, $sql);
    if ($res && mysqli_num_rows($res) > 0) {
        $fila = mysqli_fetch_assoc($res);
        return $fila['siguiente'] ?: null;
    }
    return null;
}

function obtenerAnteriorProceso($procesos_flujo, $proceso) {
    foreach ($procesos_flujo as $i => $proc) {
        if ($proc['proceso'] === $proceso && $i > 0) {
            return $procesos_flujo[$i - 1]['proceso'];
        }
    }
    return null;
}

$procesoAnterior = obtenerAnteriorProceso($procesos_flujo, $proceso);
$procesoSiguiente = obtenerSiguienteProceso($con, $flujo, $proceso);

// Procesar retroceso de proceso
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['retroceder_proceso'])) {
    $proceso_anterior = mysqli_real_escape_string($con, $_POST['proceso_anterior']);
    $sql = "UPDATE reservas SET proceso='$proceso_anterior' 
            WHERE id_reserva=$id_reserva AND flujo='$flujo'";
    if (mysqli_query($con, $sql)) {
        header("Location: proceso.php?id_reserva=$id_reserva&flujo=$flujo&proceso=$proceso_anterior");
        exit();
    } else {
        die("Error al retroceder de proceso: " . mysqli_error($con));
    }
}

// Procesar actualización de estado
if (isset($_POST['actualizar_estado'])) {
    $nuevo_estado = mysqli_real_escape_string($con, $_POST['estado']);
    $sql = "UPDATE reservas SET estado='$nuevo_estado' WHERE id_reserva=$id_reserva";
    mysqli_query($con, $sql);

    if ($nuevo_estado == 'confirmada') {
        $siguiente = obtenerSiguienteProceso($con, $flujo, $proceso);
        if ($siguiente) {
            $sql = "UPDATE reservas SET proceso='$siguiente', fecha_fin=NOW()
                    WHERE id_reserva=$id_reserva AND flujo='$flujo' AND proceso='$proceso'";
            mysqli_query($con, $sql);

            header("Location: proceso.php?id_reserva=$id_reserva&flujo=$flujo&proceso=$siguiente");
            exit();
        }
    }

    header("Location: proceso.php?id_reserva=$id_reserva&flujo=$flujo&proceso=$proceso");
    exit();
}

// Obtener información de la reserva actual
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

		<!-- Flujo de Proceso -->
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

		<!-- Botones Anterior y Siguiente -->
		<div class="flex justify-between items-center mb-8">
			<?php if ($procesoAnterior): ?>
			<form method="POST" onsubmit="return confirmarRetroceso();">
				<input type="hidden" name="retroceder_proceso" value="1">
				<input type="hidden" name="proceso_anterior" value="<?php echo $procesoAnterior; ?>">
				<button type="submit" class="text-white bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded">
					← Paso anterior
				</button>
			</form>
			<?php else: ?>
			<div></div>
			<?php endif; ?>

			<?php if ($procesoSiguiente): ?>
			<?php
				// Define las rutas personalizadas para procesos específicos
				$rutas_especiales = [
					
					'P3' => 'verificar_disponibilidad.php',
					'P4' => 'confirmar_reserva.php',
					'P5' => 'retirar_libro.php'
				];

				// Verifica si el proceso siguiente tiene una ruta especial
				if (array_key_exists($procesoSiguiente, $rutas_especiales)) {
					$ruta = $rutas_especiales[$procesoSiguiente];
					$url_siguiente = "$ruta?id_reserva=$id_reserva&flujo=$flujo&proceso=$procesoSiguiente";
				} else {
					$url_siguiente = "proceso.php?id_reserva=$id_reserva&flujo=$flujo&proceso=$procesoSiguiente";
				}
			?>
			<a href="<?php echo $url_siguiente; ?>"
				class="text-white bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded">
				Paso siguiente →
			</a>
			<?php endif; ?>

		</div>
		<a href="bandeja.php" class="text-white bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded"> volver a bandeja
		</a>
		<!-- Detalles del libro -->
		<div class="bg-white rounded-lg shadow-md p-6">
			<h2 class="text-xl font-semibold mb-4 text-indigo-600">Información del Libro</h2>
			<p><strong>Título:</strong> <?php echo htmlspecialchars($reserva['titulo']); ?></p>
			<p><strong>Autor:</strong> <?php echo htmlspecialchars($reserva['autor']); ?></p>
			<p><strong>Estado de la Reserva:</strong> <?php echo htmlspecialchars($reserva['estado']); ?></p>
		</div>

		<!-- Formulario para actualizar estado -->
		<form method="POST" class="mt-6 bg-white p-6 rounded-lg shadow-md">
			<label for="estado" class="block font-medium text-gray-700 mb-2">Actualizar Estado:</label>
			<select name="estado" id="estado" class="border border-gray-300 rounded px-4 py-2 w-full mb-4">
				<option value="pendiente" <?php if ($reserva['estado'] == 'pendiente') echo 'selected'; ?>>Pendiente
				</option>
				<option value="confirmada" <?php if ($reserva['estado'] == 'confirmada') echo 'selected'; ?>>Confirmada
				</option>
				<option value="cancelada" <?php if ($reserva['estado'] == 'cancelada') echo 'selected'; ?>>Cancelada
				</option>
			</select>
			<button type="submit" name="actualizar_estado"
				class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
				Guardar Cambios
			</button>
		</form>
	</div>

	<script>
	function confirmarRetroceso() {
		return confirm(
			"¿Estás seguro de volver al paso anterior? Se actualizará el estado del proceso en la base de datos.");
	}
	</script>
</body>

</html>