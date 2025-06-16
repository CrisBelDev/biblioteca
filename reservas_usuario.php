<?php
session_start();
include 'conexion.php';

// Obtenemos el flujo y proceso de la URL (por GET)
$flujo = isset($_GET['flujo']) ? $_GET['flujo'] : '';
$proceso = isset($_GET['proceso']) ? $_GET['proceso'] : '';
$pantalla = '';

// Validamos que el usuario tenga el rol 'usuario'
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'usuario') {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$nombre_usuario = $_SESSION['nombre'];

// Conexión a la base de datos
if (!$con) {
    die("Error en la conexión: " . mysqli_connect_error());
}

// Consulta para verificar flujo y proceso
$consulta = "SELECT * FROM flujo_proceso WHERE flujo = '$flujo' AND proceso = '$proceso' ";
$resultado = mysqli_query($con, $consulta);

if ($resultado) {
    if (mysqli_num_rows($resultado) == 0) {
        // Si no existe el flujo o proceso, redirigimos al login
        $_SESSION['mensaje'] = "Flujo o proceso no válido.";
        header("Location: login.php");
        exit;
    }

    $fila = mysqli_fetch_assoc($resultado);

    if ($fila['flujo'] == 'F1' && $fila['proceso'] == 'P1') {
        // Si el flujo es F1 y el proceso es P1, usamos su pantalla
        $pantalla = $fila['pantalla'];
    }else{
		echo "Flujo o proceso no válido.";
		exit;
	}
} else {
    die("Error en la consulta: " . mysqli_error($con));
}

// Verificamos si hay un mensaje en la sesión
$mensaje = '';
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}
?>



<!-- Incluir la pantalla correspondiente -->
<?php if (!empty($pantalla)) include $pantalla . '.inc.php'; ?>