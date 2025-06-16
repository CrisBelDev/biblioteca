<?php
session_start();
include 'conexion.php';

// Validar sesión
if (!isset($_SESSION['id_usuario'])) {
    $_SESSION['mensaje'] = "Debes iniciar sesión para realizar una reserva.";
    header("Location: login.php");
    exit();
}

// Obtener ID del libro
$id_libro = isset($_GET['id_libro']) ? (int)$_GET['id_libro'] : 0;

// Si aún no hay confirmación, mostrar el formulario
if (!isset($_POST['confirmar'])) {
    // Validar que haya un libro seleccionado
    if ($id_libro <= 0) {
        $_SESSION['mensaje'] = "Error: No se ha seleccionado un libro válido.";
        header("Location: reservas_usuario.php?flujo=F2&proceso=P1");
        exit();
    }

    // Mostrar HTML de confirmación
    echo '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Confirmar Reserva</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100 flex items-center justify-center h-screen">
        <div class="bg-white shadow-lg rounded-xl p-8 max-w-md text-center">
            <h1 class="text-2xl font-bold mb-4">¿Deseas confirmar la reserva del libro?</h1>
            <form method="post">
                <input type="hidden" name="id_libro" value="' . $id_libro . '">
                <button type="submit" name="confirmar" value="si" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded mr-2">
                    Sí, confirmar
                </button>
                <button type="submit" name="confirmar" value="no" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                    No, cancelar
                </button>
            </form>
        </div>
    </body>
    </html>
    ';
    exit();
}

// Si se confirmó o canceló
$confirmar = $_POST['confirmar'];
$id_libro = (int)$_POST['id_libro'];

if ($confirmar === "si") {
    $sql = "INSERT INTO reservas (id_usuario, flujo, proceso, id_libro, fecha_inicio, estado)
            VALUES (?, 'F1', 'P2', ?, NOW(), 'pendiente')";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $_SESSION['id_usuario'], $id_libro);

    if (mysqli_stmt_execute($stmt)) {
        $id_reserva = mysqli_insert_id($con);
        $_SESSION['mensaje'] = "✅ Reserva iniciada correctamente. ID: $id_reserva";
    } else {
        $_SESSION['mensaje'] = "❌ Error al crear reserva: " . mysqli_error($con);
    }
} else {
    $_SESSION['mensaje'] = "❌ Reserva cancelada por el usuario.";
}

header("Location: reservas_usuario.php?flujo=F1&proceso=P1");
exit();
?>