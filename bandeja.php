<?php
// Conexión a la base de datos

$con = mysqli_connect("localhost", "root", "", "biblioteca");
if (!$con) {
    die("Error en la conexión: " . mysqli_connect_error());
}
session_start();

// Consulta reservas
$sql = "SELECT r.id_reserva, r.usuario, r.flujo, r.proceso, l.titulo, r.fecha_inicio, r.fecha_fin, r.estado 
        FROM reservas r
        LEFT JOIN libros l ON r.id_libro = l.id_libro
        ORDER BY r.estado, r.fecha_inicio DESC";

$resultado = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Bandeja de Reservas - Biblioteca</title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
	<div class="container mx-auto px-4 py-8">
		<header class="mb-8">
			<h1 class="text-3xl font-bold text-indigo-700">Bandeja de Reservas</h1>
			<p class="text-gray-600">


				<?php if (!empty($_SESSION['rol'])): ?>
				Bienvenido, <?php echo htmlspecialchars($_SESSION['rol']); ?>

				<?php endif; ?>
			</p>

			<p class="text-gray-600">Estado de todas las reservas</p>
		</header>

		<div class="bg-white rounded-lg shadow-md overflow-hidden">
			<table class="min-w-full divide-y divide-gray-200">
				<thead class="bg-gray-50">
					<tr>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID
						</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							Usuario</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Libro
						</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							Progreso</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							Estado</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha
							Inicio</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							Acción</th>
					</tr>
				</thead>
				<tbody class="bg-white divide-y divide-gray-200">
					<?php
                    if ($resultado && mysqli_num_rows($resultado) > 0) {
                        while ($fila = mysqli_fetch_assoc($resultado)) {
                            $estadoColor = '';
                            switch ($fila['estado']) {
                                case 'pendiente': $estadoColor = 'bg-yellow-100 text-yellow-800'; break;
                                case 'confirmada': $estadoColor = 'bg-blue-100 text-blue-800'; break;
                                case 'completada': $estadoColor = 'bg-green-100 text-green-800'; break;
                                case 'cancelada': $estadoColor = 'bg-red-100 text-red-800'; break;
                            }
                            
                            echo "<tr class='hover:bg-gray-50'>";
                            echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($fila['id_reserva']) . "</td>";
                            echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($fila['usuario']) . "</td>";
                            echo "<td class='px-6 py-4'>" . htmlspecialchars($fila['titulo'] ?? 'N/A') . "</td>";
                            
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
                            
                            echo "<td class='px-6 py-4 whitespace-nowrap'><span class='px-2 py-1 rounded-full text-xs font-semibold $estadoColor'>" . htmlspecialchars($fila['estado']) . "</span></td>";
                            echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($fila['fecha_inicio']) . "</td>";
                            echo "<td class='px-6 py-4 whitespace-nowrap'><a href='proceso.php?id_reserva=" . $fila['id_reserva'] . "&flujo=" . urlencode($fila['flujo']) . "&proceso=" . urlencode($fila['proceso']) . "' class='text-indigo-600 hover:text-indigo-900'>Ver</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='px-6 py-4 text-center text-gray-500'>No hay reservas registradas</td></tr>";
                    }
                    ?>
				</tbody>
			</table>
		</div>

		<div class="mt-4">
			<a href="index.php" class="text-indigo-600 hover:text-indigo-900">← Volver al inicio</a>
		</div>
	</div>
</body>

</html>

<?php
mysqli_close($con);
?>