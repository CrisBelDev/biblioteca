<?php
//creamos la conexión a la base de datos
$con = mysqli_connect("localhost", "root", "", "biblioteca");
if (!$con) {
	die("Error en la conexión: " . mysqli_connect_error());
}	