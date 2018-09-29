<?php

session_start();  // Inicia a session

include "config.php";

$usuario = $_POST['usuario'];
$senha = $_POST['senha'];

if((!$usuario) || (!$senha)){

	echo "Por favor, todos campos devem ser preenchidos! <br /><br />";
	include "formulario_login.html";

}
else{

	$senha = md5($senha);

	$sql = mysqli_query($con,"SELECT * FROM usuarios WHERE usuario='{$usuario}' AND senha='{$senha}' AND ativado='1'");
	$login_check = mysqli_num_rows($sql);

	if($login_check > 0){

		while($row = mysqli_fetch_array($sql)){

			foreach( $row AS $key => $val ){

				$$key = stripslashes( $val );

			}

			$_SESSION['usuario_id'] = $usuario_id;
			$_SESSION['nome'] = $nome;
			$_SESSION['sobrenome'] = $sobrenome;
			$_SESSION['email'] = $email;
			$_SESSION['nivel_usuario'] = $nivel_usuario;
		
			mysqli_query($con,"UPDATE usuarios SET data_ultimo_login = now() WHERE usuario_id ='{$usuario_id}'");

			header("Location: area_restrita.php");

		}

	}
	else{

		echo "Voce nao pode logar-se! Este usuario e/ou senha nao sao validos!<br />
			Por favor tente novamente!<br />";

		include "formulario_login.html";

	}
}

?>
