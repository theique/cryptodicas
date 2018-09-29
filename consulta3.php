<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"><meta name="viewport" content="width=device-width"><title>consulta.php</title>
<link rel="stylesheet" type="text/css" href="viewsource.css">
</head>

<body id="viewsource" class="highlight" style="-moz-tab-size: 4" contextmenu="actions">

<style type="text/css">
body {background-color: #fff; color: #222; font-family: sans-serif;}
pre {margin: 0; font-family: monospace;}
a:link {color: #009; text-decoration: none; background-color: #fff;}
a:hover {text-decoration: underline;}
table {border-collapse: collapse; border: 0; width: 934px; box-shadow: 1px 2px 3px #ccc;}
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


session_start();
session_checker();

echo "<table><tr><td>";

echo "Olá ". $_SESSION['nome'] ." ". $_SESSION['sobrenome'] .", seus endereços cadastrados estão abaixo: ";

echo "</td></tr></table><br>";

$sql_busca_enderecos_usuario = "SELECT * FROM monk_add WHERE usuario_id='{$_SESSION['usuario_id']}'";
$result = $con->query($sql_busca_enderecos_usuario);

$total = 0;
$total_geral = 0;

if ($result->num_rows > 0) 
{
    while($row = $result->fetch_assoc()) 
	{
		
		$url_saldo = "https://www.coinexplorer.net/api/v1/MONK/address/balance?address=".$row["endereco"];
		$response = file_get_contents($url_saldo);
		$sucesso = substr($response,17,4);
		$endereco = substr($response,48,34);
		$saldo = str_replace('"',"",substr($response,86,18));

		while ( $sucesso != "true")
		{
			//echo "tentando novamente com explorer...";
			$response = file_get_contents($url_saldo);
			$sucesso = substr($response,17,4);
			$endereco = substr($response,48,34);
			$saldo = str_replace('"',"",substr($response,86,12));
		}

		$url_masternode = "https://www.coinexplorer.net/api/v1/MONK/masternode?address=".$row["endereco"];
		$mn_response = file_get_contents($url_masternode);
		$mn_sucesso = substr($mn_response,17,4);
		$mn_status = substr($mn_response,222,7);
		$mn_ativo = substr($mn_response,254,4);
		
		$i = 0;
		while ( $mn_sucesso != "true")
		{
			$i = $i + 1;
			if ( $i > 5 )
			{	
				//Endereço não é Masternode
				break;
			}
			//echo "tentando novamente com explorer...";
			$mn_response = file_get_contents($url_masternode);
			$mn_sucesso = substr($mn_response,17,4);
			$mn_status = substr($mn_response,222,7);
			$mn_ativo = str_replace('"',"",substr($mn_response,254,4));
			//echo "<br>MN tentando sucesso..." . $i;
		}
		
		
		$valor_percent = $saldo*$row["percent"]/100;
		
		echo "<table><tr><td><h1>" . $row["nome"]. '</h1></td><td colspan="3">' . $row["endereco"]. "</td></tr>";
		
		if ( $i > 3 )
		{	
			echo '<tr><td colspan="2">Endereço não é Masternode</td></tr>';
		}
		else
		{
			echo "<tr><td><h2>Masternode Status:</h2></td><td>" . $mn_status . "</td><td><h2>Masternode Ativo:</h2></td><td>" . $mn_ativo . "</td></tr>";
		}
		echo '<tr><td><h2>Saldo do Endereço:</h2></td><td colspan="3">' . round($saldo,2) . " MONK</td></tr><tr>";
		echo '<td><h2>Minha Parte:</h2></td><td colspan="3">' . round($valor_percent,2) . " MONK (" . round($row["percent"],2) . "%)</td></tr></table> <br>";
		$total = $total + $valor_percent;
		$total_geral = $total_geral + $saldo;
	}
	echo "<table><tr><td><h1>Total Geral:</h1></td><td>" . round($total_geral,2) . " MONK</td></tr>";
	echo "<tr><td><h1>Meu Total:</h1></td><td>" . round($total,2) . " MONK</td></tr></table>";
} 
else 
{
    echo "<table><tr><td><h1> Ainda não há endereços cadastrados para usuário id " . $_SESSION['usuario_id'] . "</h1></td></tr></table>";
}
	

?>

</body>
</html>