<?php
include '../conexion.php';
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'almacen') {
	header("Location: login.php");
	exit();
}

// Consulta de adquisiciones
$sql = "SELECT a.id_adquisicion, u.nombre AS unombre, l.titulo, a.flujo, a.proceso, a.fecha, a.estado
        FROM adquisiciones a
        LEFT JOIN usuarios u ON a.id_usuario = u.id
        LEFT JOIN libros l ON a.id_libro = l.id_libro
        ORDER BY a.estado, a.fecha DESC";

$resultado = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<title>Bandeja de Adquisiciones</title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
	<div class="container mx-auto px-4 py-8">
		<header class="mb-8">
			<h1 class="text-3xl font-bold text-indigo-700">Bandeja de Adquisiciones</h1>
			<p class="text-gray-600">Bienvenido, <?php echo $_SESSION['nombre']; ?> (<?php echo $_SESSION['rol']; ?>)
			</p>
		</header>
		<a href="solicitar_registrar_libro.php"
			class="inline-block mb-4 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Solicitar
			Registro de Libro</a>

		<a href="../logout.php"
			class="inline-block mb-4 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">cerrar session</a>
		<div class="bg-white rounded-lg shadow-md overflow-hidden">
			<table class="min-w-full divide-y divide-gray-200">
				<thead class="bg-gray-50">
					<tr>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Libro</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progreso</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
					</tr>
				</thead>
				<tbody class="bg-white divide-y divide-gray-200">
					<?php
					if ($resultado && mysqli_num_rows($resultado) > 0) {
						while ($fila = mysqli_fetch_assoc($resultado)) {
							$estadoColor = match ($fila['estado']) {
								'pendiente'   => 'bg-yellow-100 text-yellow-800',
								'en proceso'  => 'bg-blue-100 text-blue-800',
								'completada'  => 'bg-green-100 text-green-800',
								'cancelada'   => 'bg-red-100 text-red-800',
								default       => 'bg-gray-100 text-gray-800',
							};

							echo "<tr class='hover:bg-gray-50'>";
							echo "<td class='px-6 py-4 whitespace-nowrap'>{$fila['id_adquisicion']}</td>";
							echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($fila['unombre']) . "</td>";
							echo "<td class='px-6 py-4'>" . htmlspecialchars($fila['titulo'] ?? 'N/A') . "</td>";

							// PROGRESO (VERTICAL)
// Columna de progreso
                            echo "<td class='px-6 py-4 whitespace-nowrap'>";
                            echo "<div class='flex items-center'>";
                            
                            // Obtener todos los procesos del flujo
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
                                echo "<div class='w-6 h-6 rounded-full flex items-center justify-center text-xs ".
                                     ($active ? "bg-indigo-600 text-white" : 
                                      ($completed ? "bg-green-500 text-white" : "bg-gray-200 text-gray-700"))."'>".
                                     substr($proc['proceso'], 1)."</div>";
                                
                                if ($index < count($procesos_flujo) - 1) {
                                    echo "<div class='w-4 h-1 mx-1 ".
                                         ($completed ? "bg-green-500" : "bg-gray-200")."'></div>";
                                }
                                
                                echo "</div>";
                            }
                            
                            echo "</div>";
                            echo "</td>";


							echo "<td class='px-6 py-4'><span class='px-2 py-1 rounded-full text-xs font-semibold $estadoColor'>" . htmlspecialchars($fila['estado']) . "</span></td>";
							echo "<td class='px-6 py-4'>" . htmlspecialchars($fila['fecha']) . "</td>";
							echo "<td class='px-6 py-4'><a href='proceso_adquisicion.php?id_adquisicion={$fila['id_adquisicion']}&flujo=" . urlencode($fila['flujo']) . "&proceso=" . urlencode($fila['proceso']) . "' class='text-indigo-600 hover:text-indigo-900'>Ver</a></td>";
							echo "</tr>";
						}
					} else {
						echo "<tr><td colspan='7' class='px-6 py-4 text-center text-gray-500'>No hay adquisiciones registradas.</td></tr>";
					}
					?>
				</tbody>
			</table>
		</div>

		<div class="mt-4">

		</div>
	</div>
</body>

</html>

<?php mysqli_close($con); ?>