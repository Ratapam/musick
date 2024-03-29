<?php
require_once("../../src/AutentificacionConBaseDeDatos.php");

procesaRegistrate();

// Comprueba se el correo tiene '@' y '.'
function correoValido($correo) {
	if (strpos($correo, "@") && strpos($correo, ".")) {
		return true;
	}
}

// Procesa si hay que guardar datos del procesa
function procesaRegistrate() {
	$errores =[];
	$nick = "";
    $correo = "";
    $correo2 = "";
	$mdb = new AutentificacionConBaseDeDatos();
	// Se guardan los datos válidos para mostrarlos dentro del formulario si ya 
	// se han escrito antes y había alguno que no era válido.
    if (isset($_POST['enviar'])) {		
        if ($mdb -> nickDisponible($_POST["nick"])) {
			$nick = $_POST["nick"];		
        } else {
			$errores[]="Nick no disponible";			
		}		
        if (correoValido($_POST["correo"])) {
			$correo = $_POST["correo"];			
			//Else de comprobacion de correo disponible
		} else {
			$errores[] = "El correo no es válido";		
		}		
		if ($_POST["correo"] != $_POST["correo2"]) {
		
			$errores[] = "Los correos no coinciden";
		}
		//Aqui coprobamos que las contraseñas coinciden por que no funcionan bien los required
		if (isset($_POST['contrasena']) && isset($_POST['repetir_contrasena'])) {
			if ($_POST['contrasena'] != $_POST['repetir_contrasena']) {						
				$errores[] = "Las contraseñas no coinciden";
			}
		}
		if (count($errores)== 0) {
			require_once("../../resources/generarToken.php");
			$token = generateToken();
			$contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
			$correo2 = $_POST["correo2"];
			$mdb -> guardarUsuarioNC($nick, $contrasena, $correo);
			$id_usuarioNC = $mdb -> saberIdusuarioNC($correo);
			$mdb -> insertToken($id_usuarioNC, "nuevoUsuario", $token);
			header('Location: confirmarToken.php?id_usuarioNC='.$id_usuarioNC.'&token='.$token.'');
			die();
		} else {
			mostrarHtml($nick, $correo, $correo2, $errores);
		}        
	} else {
		mostrarHtml($nick, $correo, $correo2, $errores);
	}	
}

// Muestra la pagina html
function mostrarHtml(string $nick, string $correo, string $correo2, $errores) {
	$autofocus1 = "autocus";
	$autofocus2 = "";
	$autofocus3 = "";
	$autofocus4 = "";
	if ($nick == "") {
		$autofocus1 = "autofocus";
	} elseif ($correo == "") {
		$autofocus2 = "autofocus";
	} elseif ($correo2 == "") {
		$autofocus3 = "autofocus";
	} else {
		$autofocus4 = "autofocus";
	}

	?>
	<!DOCTYPE html>	
	<html lang="es">
	<head>
	<meta charset="UTF-8">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="../css/registrate.css">
	<title>Registrate</title>
	</head>
	<body>
    <div class="contenedor">
		<div class="tarjeta">
			<div class="cabecera">
				<h3>Regístrate</h3>
			</div>
				<form action="registrate.php" method="POST" class="formulario">
					<div class="grupo">
						<img src="../img/user.png" class="icono">
						<input type="text" class="input" placeholder="nick" name="nick" value="<?=$nick?>" required <?=$autofocus1?>>
					</div>
					<div class="grupo">
                        <img src="../img/correo.png" class="icono">
						<input type="text" class="input" placeholder="correo" name="correo" value="<?=$correo?>" required <?=$autofocus2?>>
					</div>
					<div class="grupo">
                        <img src="../img/correo.png" class="icono">
						<input type="text" class="input" placeholder="repite el correo" name="correo2" value="<?=$correo2?>" required <?=$autofocus3?>>
					</div>
					<div class="grupo">
                        <img src="../img/llave.png" class="icono">
						<input type="password" class="input" placeholder="contraseña" name="contrasena" required <?=$autofocus4?>>
					</div>
					<div class="grupo">
                        <img src="../img/llave.png" class="icono">
						<input type="password" class="input" placeholder="repetir contraseña" name="repetir_contrasena" required>
					</div>
					<?php 
						if(count($errores)>0){ 
							echo '<div class="grupo errores">';
						 	foreach($errores as $clave => $value){echo $value."<br>";}
							echo '</div>';
						}						
					?>
					<div class="grupo contenedor_boton">
						<input type="submit" name="enviar" value="Registro" class="boton">
					</div>
				</form>
		</div>
	</body>
	</html>
		
	<?php
}

?>