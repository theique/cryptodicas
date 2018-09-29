<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"><meta name="viewport" content="width=device-width"><title>CRYPTODICAS - TESTE</title>
<link rel="stylesheet" type="text/css" href="viewsource.css">
</head>

<body id="viewsource" class="highlight" style="-moz-tab-size: 4" contextmenu="actions">
<center>
<style type="text/css">
body {background-color: #fff; color: #222; font-family: sans-serif;}
pre {margin: 0; font-family: monospace;}
a:link {color: #009; text-decoration: none; background-color: #fff;}
a:hover {text-decoration: underline;}
table {border-collapse: collapse; border: 0; width: 450px; box-shadow: 1px 2px 3px #ccc;}
.center {text-align: center;}
.center table {margin: 1em auto; text-align: left;}
.center th {text-align: center !important;}
td, th {border: 1px solid #666; font-size: 75%; vertical-align: baseline; padding: 4px 5px;}
h1 {font-size: 150%;}
h2 {font-size: 125%;}
.p {text-align: left;}
.e {background-color: #ccf; width: 300px; font-weight: bold;}
.h {background-color: #99c; font-weight: bold;}
.v {background-color: #ddd; max-width: 300px; overflow-x: auto; word-wrap: break-word;}
.v i {color: #999;}
img {float: right; border: 0;}
hr {width: 934px; background-color: #ccc; border: 0; height: 1px;}
</style>

<?php
include "config.php";
include "functions.php";
include "apijson.php";

session_start();
session_checker();

echo "<table><tr><td>";
echo "Olá ". $_SESSION['nome'] ." ". $_SESSION['sobrenome'] ." ";
echo "</td></tr></table><br>";

//Busca grupos do usuario logado
$sql_consulta_grupos = "SELECT id FROM grupo WHERE id_usuario = '{$_SESSION['usuario_id']}' ";
$result = $con->query($sql_consulta_grupos);

//Caso tenha grupos cadastrados entra
if ($result->num_rows > 0) 
{
	//Vai fazer um a um para cada grupo cadastrados
	while($row_grupo = $result->fetch_assoc()) 
	{
		//Busca Usuarios do grupo
		$sql_consulta_usuarios_do_grupo = "SELECT * FROM `grupo` WHERE `id` = " . $row_grupo["id"];
		$result_u_g = $con->query($sql_consulta_usuarios_do_grupo);

		echo "<br><table><tr><td colspan=4>Participantes do grupo " . $row_grupo["id"] . "</td></tr>";
		echo "<tr><td>Nome</td><td>Sobrenome</td><td>email</td><td>%</td></tr>";
		//Vai fazer um a um para cada usuario do grupo
		while($row_usuario_do_grupo = $result_u_g->fetch_assoc()) 
		{
			//Busca dados do usuario
			$sql_consulta_usuario = "SELECT * FROM `usuarios` WHERE `usuario_id` = " . $row_usuario_do_grupo["id_usuario"];
			//echo "SQL: " . $sql_consulta_usuario . " <BR> ";
			$result_c_u = $con->query($sql_consulta_usuario);
			if(!$result_c_u)
			{
				echo "Error : " . mysqli_error($con);
				echo "<br> Error number: " . mysqli_errno($con);
			}

			//echo $result;
			$row_usuario = $result_c_u->fetch_assoc();
			
			//echo $row_usuario;
			echo "<tr><td>" . $row_usuario["nome"] . "</td><td>"  . $row_usuario["sobrenome"] . "</td><td>" . $row_usuario["email"] . "</td><td>" . round($row_usuario_do_grupo["percentual"],1) . "</td></tr>";
		}
		echo "</table><br>";
		
		//Busca Endereços do grupo
		$sql_consulta_enderecos = "SELECT `endereco` FROM `monk_add` WHERE `grupo` = " . $row_grupo["id"] . " group by endereco";
		$result = $con->query($sql_consulta_enderecos);
		if(!$result)
		{
			echo "Error : " . mysqli_error($con);
			echo "<br> Error number: " . mysqli_errno($con);
		}
		
		$i=0;
		echo "<br><table><tr><td colspan=2> Endereços do grupo " . $row_grupo["id"] . "</td></tr>";
		//Vai fazer um a um para cada endereço do grupo
		while($row_endereco_do_grupo = $result->fetch_assoc()) 
		{
			$i++;
			echo "<tr><td>" . $i . "</td><td>" . $row_endereco_do_grupo["endereco"] . "</td></tr>";
		}
		echo "</table><br>";

	}
}
else 
{
    echo "<table><tr><td><h1> Ainda não há grupo cadastrado para usuário " . $_SESSION['nome'] . "</h1></td></tr></table>";
}
	
echo "<br><table><tr><td> www.cryptodicas.com - by Marcio Campos - TMJ Macacada!</td></tr><tr><td>theique@gmail.com</td></tr></table>";

$con->close();
?>
</center>
</body>
</html>