<?php
// obtenemos la conexión a la base de datos
session_start();
$con = mysqli_connect("localhost", "root", "", "biblioteca");

// obtenemos el flujo y proceso de la URL, si existen
$flujo = isset($_GET['flujo']) ? $_GET['flujo'] : '';
$proceso = isset($_GET['proceso']) ? $_GET['proceso'] : '';
// buscamos el siguiente proceso en la base de datos en la tabla flujo_proceso
if (!$con) {
	die("Error en la conexión: " . mysqli_connect_error());
}
$sql = "SELECT * FROM flujo_proceso WHERE flujo = '$flujo' AND proceso = '$proceso'";
$resultado = mysqli_query($con, $sql);
if ($fila = mysqli_fetch_assoc($resultado)) {
	$siguiente = $fila['siguiente'];
	//echo "Siguiente proceso: $siguiente<br>";
} else {
	die("Proceso no encontrado.");
}

// ahora definomos las consultas si siguiente es p2 es registrar_reserva.php
if ($siguiente == 'P2') {
	// Aquí puedes definir la lógica para registrar la reserva
	// Por ejemplo, podrías redirigir a un formulario de registro de reserva enviando el dato 
//capturado por el metodo POST de id_libro
	$dato = isset($_POST['id_libro']);
	
	header("Location: registrar_reserva.php?flujo=$flujo&proceso=$siguiente&id_libro=$dato");
	exit();
} elseif ($siguiente === 'P3') {
	// Aquí puedes definir la lógica para confirmar la reserva
	// Por ejemplo, podrías redirigir a una página de confirmación de reserva
	header("Location: confirmar_reserva.php?flujo=$flujo&proceso=$siguiente");
	exit();
} else {
	die("Proceso no válido.");
}