<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"><meta name="viewport" content="width=device-width"><title>CRYPTODICAS - Consulta MONKs</title>
<link rel="stylesheet" type="text/css" href="viewsource.css">
</head>

<body id="viewsource" class="highlight" style="-moz-tab-size: 4" contextmenu="actions">

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

//Busca Informações atualizadas da MONK usando API Profissional do coinmarketcap.com
$class = new APIJSON("https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest?symbol=MONK&convert=BRL");
$call= $class->call(array());
$search_coins= json_decode($call,true);

session_start();
session_checker();

echo "<table><tr><td>";
echo "Olá ". $_SESSION['nome'] ." ". $_SESSION['sobrenome'] .", seus endereços cadastrados estão abaixo: ";
echo "</td></tr></table><br>";

//Busca todos endereços do usuario logado
$sql_busca_enderecos_usuario = "SELECT * FROM monk_add WHERE usuario_id='{$_SESSION['usuario_id']}'";
$result = $con->query($sql_busca_enderecos_usuario);
$con->close();

$total = 0;
$total_geral = 0;

//Caso tenha endereços cadastrados entra
if ($result->num_rows > 0) 
{
    //Vai fazer um a um para todos os endereços cadastrados
	while($row = $result->fetch_assoc()) 
	{
		//Busca saldo no blockchain usando a api da coinexplorer.net
		$url_saldo = "https://www.coinexplorer.net/api/v1/MONK/address/balance?address=".$row["endereco"];
		$response = file_get_contents($url_saldo);
		$sucesso = substr($response,17,4);
		$endereco = substr($response,48,34);
		$saldo = str_replace('"',"",substr($response,86,18));

		//Só entra nesse loop caso a consulta da API não retorne com sucesso
		//O loop tenta novamente até conseguir sucesso ou após 5 tentativas
		//Após 5 tentativas o erro será exibido
		$i = 0;
		while ( $sucesso != "true")
		{
			$i = $i + 1;
			if ( $i > 5 )
			{	
				echo "ERRO ao buscar saldo do endereço: ".$row["endereco"];
				break;
			}
			//echo "tentando novamente com explorer...";
			$response = file_get_contents($url_saldo);
			$sucesso = substr($response,17,4);
			$endereco = substr($response,48,34);
			$saldo = str_replace('"',"",substr($response,86,12));
		}

		//Caso o Endereço seja Mastermone busca o Status do MN
		if ( $row["MN"] == "1" )
		{
			//Busca status do Masternode usando API do coinexplorer.net
			$url_masternode = "https://www.coinexplorer.net/api/v1/MONK/masternode?address=".$row["endereco"];
			$mn_response = file_get_contents($url_masternode);
			$mn_sucesso = substr($mn_response,17,4);
			$mn_status = substr($mn_response,222,7);
			$mn_ativo = substr($mn_response,254,4);
		
			//Só entra nesse loop caso a consulta da API não retorne com sucesso
			//O loop tenta novamente até conseguir sucesso ou após 5 tentativas
			//Após 5 tentativas o erro será exibido
			$i = 0;
			while ( $mn_sucesso != "true")
			{
				$i = $i + 1;
				if ( $i > 5 )
				{	
					echo "ERRO ao buscar status do endereço cadastrado como MASTERNODE: ".$row["endereco"];
					break;
				}
				$mn_response = file_get_contents($url_masternode);
				$mn_sucesso = substr($mn_response,17,4);
				$mn_status = substr($mn_response,222,7);
				$mn_ativo = str_replace('"',"",substr($mn_response,254,4));
			}
		}
		
		//Calcula o Saldo correspondente ao valor % cadastrado para o endereço
		$valor_percent = $saldo*$row["percent"]/100;

		// Montando HTML de saída
		echo "<table><tr><td>" . $row["nome"]. '</td><td colspan="3">' . $row["endereco"]. "</td></tr>";
		
		//Se NAO é masternode
		if ( $row["MN"] == "0" )
		{	
			echo "<tr><td>Saldo do Endereço</td><td>Minha Parte</h2></td></tr>";
			echo "<tr><td>" . round($saldo,2) . " MONK</td><td>" . round($valor_percent,2) . " MONK (" . round($row["percent"],2) . "%)</td></tr></table> <br>";
		}
		else
		{
			//Se é masternode
			echo "<tr><td>Masternode Status</td><td>Masternode Ativo</td><td>Saldo do Endereço</td><td>Minha Parte</td></tr>";
			echo "<tr><td>" . $mn_status . "</td><td>" . $mn_ativo . "</td><td>" . round($saldo,2) . " MONK</td><td>" . round($valor_percent,2) . " MONK (" . round($row["percent"],2) . "%)</td></tr></table> <br>";
		}

		//Soma os valores Total e % do endereço para ao final termos os Totais Gerais
		$total = $total + $valor_percent;
		$total_geral = $total_geral + $saldo;
	}
	
	//Exibe tabela final com os valores Toais Gerais de todos endereços cadastrados para o usuário logado
	echo "<table><tr><td>Total Geral</td><td>" . round($total_geral,2) . " MONK</td><td>R$ " . round($total_geral*$search_coins['data']['MONK']['quote']['BRL']["price"],2) . "</td></tr>";
	echo "<tr><td>Meu Total</td><td>" . round($total,2) . " MONK</td><td>R$ " . round($total*$search_coins['data']['MONK']['quote']['BRL']["price"],2) . "</td></tr></table>";
} 
else 
{
    echo "<table><tr><td><h1> Ainda não há endereços cadastrados para usuário id " . $_SESSION['usuario_id'] . "</h1></td></tr></table>";
}

//echo "<br> name: " . $search_coins['data']['MONK']['name'];
//echo "<br> symbol: " . $search_coins['data']['MONK']["symbol"];

echo "<br><table><tr><td> Preço de 1 MONK </td><td> R$ " . round($search_coins['data']['MONK']['quote']['BRL']["price"],2) . "</td></tr>";
echo "<tr><td> Volume em 24h </td><td> R$ " . round($search_coins['data']['MONK']['quote']['BRL']["volume_24h"],2) . "</td></tr>";
echo "<tr><td> Variação de 1h </td><td> " . round($search_coins['data']['MONK']['quote']['BRL']["percent_change_1h"],1) . "%</td></tr>";
echo "<tr><td> Variação de 24h </td><td> " . round($search_coins['data']['MONK']['quote']['BRL']["percent_change_24h"],1) . "%</td></tr>";
echo "<tr><td> Variação de 1 Semana </td><td> " . round($search_coins['data']['MONK']['quote']['BRL']["percent_change_7d"],1) . "%</td></tr>";
echo "<tr><td> Total do Mercado </td><td> R$ " . round($search_coins['data']['MONK']['quote']['BRL']["market_cap"],2) . "</td></tr>";
echo '<tr><td colspan="2"> Pessoal, para exibir reais (R$) utilizei as informações do coinmarketcap obtidas online.<br> Deve servir apenas para termos uma noção do preço.</td></tr></table>';

//echo $call;
//echo '<pre>';
//print_r($search_coins);
//echo '</pre>';

echo "<br><table><tr><td> www.cryptodicas.com - by Marcio Campos - TMJ Macacada!</td></tr><tr><td>theique@gmail.com</td></tr></table>";


	

?>

</body>
</html>