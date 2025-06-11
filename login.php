<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    if ($usuario === 'bibliotecario' && $contrasena === '12345678') {
        $_SESSION['rol'] = 'bibliotecario';
        header('Location: bandeja.php');
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<title>Login Bibliotecario</title>
</head>

<body>
	<h2>Login Bibliotecario</h2>
	<?php if (!empty($error)): ?>
	<p style="color:red;"><?php echo $error; ?></p>
	<?php endif; ?>
	<form method="post">
		<label for="usuario">Usuario:</label>
		<input type="text" id="usuario" name="usuario" required>
		<br>
		<label for="contrasena">Contraseña:</label>
		<input type="password" id="contrasena" name="contrasena" required>
		<br>
		<button type="submit">Ingresar</button>
	</form>
</body>

</html>