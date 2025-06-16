<?php
// Conexión a la base de datos
$con = mysqli_connect("localhost", "root", "", "biblioteca");
if (!$con) {
    die("Error en la conexión: " . mysqli_connect_error());
}

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Consulta solo las reservas del usuario actual
$sql = "SELECT r.id_reserva, u.nombre as unombre, r.flujo, r.proceso, l.titulo, r.fecha_inicio, r.fecha_fin, r.estado 
        FROM reservas r 
        LEFT JOIN usuarios u ON r.id_usuario = u.id
        LEFT JOIN libros l ON r.id_libro = l.id_libro
        WHERE r.id_usuario = $id_usuario
        ORDER BY r.estado, r.fecha_inicio DESC";

$resultado = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<title>Mis Reservas - Biblioteca</title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
	<div class="container mx-auto px-4 py-8">
		<header class="mb-6">
			<h1 class="text-2xl font-bold text-indigo-700">Mis Reservas</h1>
			<p class="text-gray-600">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?>
				(<?php echo htmlspecialchars($_SESSION['rol']); ?>)</p>
		</header>

		<div class="bg-white shadow-md rounded-lg overflow-hidden">
			<table class="min-w-full divide-y divide-gray-200">
				<thead class="bg-gray-50">
					<tr>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Libro</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progreso</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Inicio</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
					</tr>
				</thead>
				<tbody class="bg-white divide-y divide-gray-200">
					<?php
                    if ($resultado && mysqli_num_rows($resultado) > 0) {
                        while ($fila = mysqli_fetch_assoc($resultado)) {
                            $estadoColor = match ($fila['estado']) {
                                'pendiente' => 'bg-yellow-100 text-yellow-800',
                                'confirmada' => 'bg-blue-100 text-blue-800',
                                'completada' => 'bg-green-100 text-green-800',
                                'cancelada' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800'
                            };

                            echo "<tr class='hover:bg-gray-50'>";
                            echo "<td class='px-6 py-4'>" . htmlspecialchars($fila['id_reserva']) . "</td>";
                            echo "<td class='px-6 py-4'>" . htmlspecialchars($fila['titulo'] ?? 'N/A') . "</td>";

                            // Progreso
                            echo "<td class='px-6 py-4'>";
                            echo "<div class='flex items-center'>";

                            $con_flujo = mysqli_connect("localhost", "root", "", "biblioteca");
                            $sql_flujo = "SELECT * FROM flujo_proceso WHERE flujo='{$fila['flujo']}' ORDER BY proceso";
                            $result_flujo = mysqli_query($con_flujo, $sql_flujo);
                            $procesos_flujo = [];
                            while ($proc = mysqli_fetch_assoc($result_flujo)) {
                                $procesos_flujo[] = $proc;
                            }
                            mysqli_close($con_flujo);

                            foreach ($procesos_flujo as $index => $proc) {
                                $active = $proc['proceso'] == $fila['proceso'];
                                $completed = array_search($proc['proceso'], array_column($procesos_flujo, 'proceso')) <
                                             array_search($fila['proceso'], array_column($procesos_flujo, 'proceso'));

                                echo "<div class='flex items-center'>";
                                echo "<div class='w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold " .
                                     ($active ? "bg-indigo-600 text-white" :
                                     ($completed ? "bg-green-500 text-white" : "bg-gray-300 text-gray-800")) . "'>" .
                                     substr($proc['proceso'], 1) . "</div>";
                                if ($index < count($procesos_flujo) - 1) {
                                    echo "<div class='w-4 h-1 mx-1 " .
                                         ($completed ? "bg-green-500" : "bg-gray-300") . "'></div>";
                                }
                                echo "</div>";
                            }

                            echo "</div></td>";

                            echo "<td class='px-6 py-4'><span class='px-2 py-1 rounded-full text-xs font-semibold $estadoColor'>" .
                                 htmlspecialchars($fila['estado']) . "</span></td>";
                            echo "<td class='px-6 py-4'>" . htmlspecialchars($fila['fecha_inicio']) . "</td>";
                            echo "<td class='px-6 py-4'><a class='text-indigo-600 hover:underline' href='proceso_usuario.php?id_reserva={$fila['id_reserva']}&flujo=" . urlencode($fila['flujo']) . "&proceso=" . urlencode($fila['proceso']) . "'>Ver</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='px-6 py-4 text-center text-gray-500'>No tienes reservas registradas</td></tr>";
                    }
                    ?>
				</tbody>
			</table>
		</div>

		<div class="mt-4">
			<a href="reservas_usuario.php?flujo=F1&proceso=P1" class="text-indigo-600 hover:text-indigo-900">← Volver
				al inicio</a>
		</div>
	</div>
</body>

</html>

<?php mysqli_close($con); ?>