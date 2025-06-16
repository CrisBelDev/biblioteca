<?php
include '../conexion.php';
session_start();

// Validación de sesión
if (!isset($_SESSION['rol'])) {
    header("Location: login.php");
    exit();
}

$rol_usuario = $_SESSION['rol'];

// Obtener variables
$id_adquisicion = $_GET['id_adquisicion'] ?? null;
$flujo = $_GET['flujo'] ?? null;
$proceso_actual = $_GET['proceso'] ?? null;

// Si el proceso es P2 y la pantalla es vericar_solicitar_reg_libro, redirigir a su pantalla especial
if ($proceso_actual === 'P2') {
    header("Location: vericar_solicitar_reg_libro.php?id_adquisicion=$id_adquisicion&flujo=$flujo&proceso=$proceso_actual");
    exit();
}


if (!$id_adquisicion || !$flujo || !$proceso_actual) {
    echo "Faltan parámetros.";
    exit();
}

// Obtener adquisición actual
$sql = "SELECT a.*, l.titulo AS libro, u.nombre AS usuario
        FROM adquisiciones a
        LEFT JOIN libros l ON a.id_libro = l.id_libro
        LEFT JOIN usuarios u ON a.id_usuario = u.id
        WHERE a.id_adquisicion = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id_adquisicion);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$fila = mysqli_fetch_assoc($res);

if (!$fila) {
    echo "No se encontró la adquisición.";
    exit();
}

// Obtener flujo
$sql_proc = "SELECT * FROM flujo_proceso WHERE flujo = ? ORDER BY proceso ASC";
$stmt_proc = mysqli_prepare($con, $sql_proc);
mysqli_stmt_bind_param($stmt_proc, 's', $flujo);
mysqli_stmt_execute($stmt_proc);
$res_proc = mysqli_stmt_get_result($stmt_proc);

$procesos = [];
while ($p = mysqli_fetch_assoc($res_proc)) {
    $procesos[] = $p;
}

// Obtener índice actual
$index_actual = array_search($proceso_actual, array_column($procesos, 'proceso'));
$proc_actual = $procesos[$index_actual];
$hay_siguiente = isset($procesos[$index_actual + 1]);
$pantalla = $proc_actual['pantalla'];
$rol_requerido = $proc_actual['rol'];

// Validar rol
$rol_permitido = $rol_usuario === $rol_requerido;

// Manejo del POST (avance de proceso)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $rol_permitido) {
    if ($hay_siguiente) {
        $proximo_proceso = $procesos[$index_actual + 1]['proceso'];
        $sql_upd = "UPDATE adquisiciones SET proceso = ?, estado = 'en proceso' WHERE id_adquisicion = ?";
        $stmt_upd = mysqli_prepare($con, $sql_upd);
        mysqli_stmt_bind_param($stmt_upd, 'si', $proximo_proceso, $id_adquisicion);
        mysqli_stmt_execute($stmt_upd);

        header("Location: proceso_adquisicion.php?id_adquisicion=$id_adquisicion&flujo=$flujo&proceso=$proximo_proceso");
        exit();
    } else {
        // Si es el último proceso, marcar como completado
        $sql_complete = "UPDATE adquisiciones SET estado = 'completada' WHERE id_adquisicion = ?";
        $stmt_complete = mysqli_prepare($con, $sql_complete);
        mysqli_stmt_bind_param($stmt_complete, 'i', $id_adquisicion);
        mysqli_stmt_execute($stmt_complete);

		if ($rol_requerido === 'bibliotecario') {
        	echo "<script>alert('Proceso completado.'); window.location='bandeja_admin.php';</script>";
        	exit();
		} else {
			echo "<script>alert('Proceso completado.'); window.location='bandeja.php';</script>";
			exit();
		}
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<title>Proceso de Adquisición</title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
	<div class="max-w-2xl mx-auto p-6 mt-10 bg-white rounded shadow">
		<h1 class="text-2xl font-bold text-indigo-700 mb-4">Proceso de Adquisición</h1>

		<div class="space-y-2 text-sm text-gray-700">
			<p><strong>ID Adquisición:</strong> <?= $id_adquisicion ?></p>
			<p><strong>Libro:</strong> <?= htmlspecialchars($fila['libro']) ?></p>
			<p><strong>Usuario solicitante:</strong> <?= htmlspecialchars($fila['usuario']) ?></p>
			<p><strong>Estado:</strong> <?= htmlspecialchars($fila['estado']) ?></p>
			<p><strong>Flujo:</strong> <?= htmlspecialchars($flujo) ?></p>
			<p><strong>Proceso:</strong> <?= htmlspecialchars($proceso_actual) ?> (Pantalla:
				<code><?= $pantalla ?></code>)
			</p>
			<p><strong>Rol necesario:</strong> <?= $rol_requerido ?> | <strong>Tu rol:</strong> <?= $rol_usuario ?></p>
		</div>

		<?php if (!$rol_permitido): ?>
		<div class="mt-4 p-4 bg-yellow-100 text-yellow-800 rounded">
			⚠️ No tienes permiso para continuar este proceso.
		</div>
		<?php else: ?>
		<form method="post" class="mt-6">
			<button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">
				<?= $hay_siguiente ? "Siguiente paso →" : "Finalizar proceso" ?>
			</button>
		</form>
		<?php endif; ?>

		<div class="mt-6">
			<?php if ($rol_usuario === 'bibliotecario'): ?>
			<a href="../bandeja.php" class="text-indigo-600 hover:underline">← Volver a la bandeja</a>
			<?php else: $rol_usuario === 'almacen'?>
			<a href="bandeja.php" class="text-indigo-600 hover:underline">← Volver a la bandeja</a>
			<?php endif; ?>
		</div>
	</div>
</body>

</html>