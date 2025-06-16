<?php
include '../conexion.php';
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'almacen') {
	header("Location: ../login.php");
	exit();
}

$id_adquisicion = $_GET['id_adquisicion'] ?? null;
$flujo = $_GET['flujo'] ?? null;
$proceso = $_GET['proceso'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['confirm']) && $_POST['confirm'] === 'si') {
		// Avanzar al siguiente proceso (P3)
		$proximo_proceso = 'P3';
		$sql = "UPDATE adquisiciones SET proceso = ?, estado = 'en proceso' WHERE id_adquisicion = ?";
		$stmt = mysqli_prepare($con, $sql);
		mysqli_stmt_bind_param($stmt, 'si', $proximo_proceso, $id_adquisicion);
		mysqli_stmt_execute($stmt);

		header("Location: proceso_adquisicion.php?id_adquisicion=$id_adquisicion&flujo=$flujo&proceso=$proximo_proceso");
		exit();
	} else {
		// Si se elige no continuar
		header("Location: bandeja.php");
		exit();
	}
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<title>Verificar Solicitud</title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">
	<div class="bg-white p-8 rounded shadow-md w-full max-w-md">
		<h2 class="text-xl font-bold text-indigo-700 mb-4">¿Deseas aprobar la solicitud de registro del libro?</h2>
		<p class="text-gray-700 mb-6">Esta acción permitirá continuar con el registro del libro en el sistema.</p>

		<form method="post" class="flex justify-between">
			<button type="submit" name="confirm" value="si"
				class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Sí</button>
			<button type="submit" name="confirm" value="no"
				class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">No</button>
		</form>
	</div>
</body>

</html>