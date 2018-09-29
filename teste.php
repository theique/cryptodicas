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
echo "Olá ". $_SESSION['nome'] ." ". $_SESSION['sobrenome'] .", testando novas funcionalidades... ";
echo "</td></tr></table><br>";

//Consulta moeda MONK no Banco Local, id 2230
$sql_consulta_moeda = "SELECT * FROM coin WHERE 1";
$result = $con->query($sql_consulta_moeda);
$row = $result->fetch_assoc();
//Converte Timestamp do banco para comparação
$time = strtotime($row["timestamp"]);
//Pega Timestap atual
$curtime = time();
//Transforma diferença de tempo de segundos para minutos
$diferenca = round(($curtime - $time) / 60,1);
//Checa se dados são atuais
if(($diferenca) > 40) 
{ 
	//Diferença de tempo maior que 40 minutos identificada (Banco de Dados Local atualizado a mais de 40 min)
	//Vamos buscar os dados atualizados na coinmarketcap e atualizar o Banco de Dados Local agora
	//Busca Informações atualizadas da MONK usando API Profissional do coinmarketcap.com - BRL
	$class = new APIJSON("https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest?symbol=MONK&convert=BRL");
	$call= $class->call(array());
	$search_coins_brl= json_decode($call,true);
	//usleep(3000000);
	//BTC
	$class = new APIJSON("https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest?symbol=MONK&convert=BTC");
	$call= $class->call(array());
	$search_coins_btc= json_decode($call,true);
	//usleep(3000000);
	//USD
	$class = new APIJSON("https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest?symbol=MONK&convert=USD");
	$call= $class->call(array());
	$search_coins_usd= json_decode($call,true);

	$sql_atualiza_moeda = "UPDATE coin SET circulating_supply=" . round($search_coins_brl['data']['MONK']['circulating_supply'],0)
	. ",total_supply=" . round($search_coins_brl['data']['MONK']['total_supply'],0)
	. ",max_supply=" . round($search_coins_brl['data']['MONK']['max_supply'],0)
	. ",cmc_rank=" . round($search_coins_brl['data']['MONK']['cmc_rank'],0)
	. ",brl_price=" . round($search_coins_brl['data']['MONK']['quote']['BRL']['price'],2)
	. ",brl_volume_24h=" . round($search_coins_brl['data']['MONK']['quote']['BRL']['volume_24h'],0)
	. ",brl_percent_change_1h=" . round($search_coins_brl['data']['MONK']['quote']['BRL']['percent_change_1h'],1)
	. ",brl_percent_change_24h=" . round($search_coins_brl['data']['MONK']['quote']['BRL']['percent_change_24h'],1)
	. ",brl_percent_change_7d=" . round($search_coins_brl['data']['MONK']['quote']['BRL']['percent_change_7d'],1)
	. ",brl_market_cap=" . round($search_coins_brl['data']['MONK']['quote']['BRL']['market_cap'],0)
	. ",usd_price=" . round($search_coins_usd['data']['MONK']['quote']['USD']['price'],2)
	. ",usd_volume_24h=" . round($search_coins_usd['data']['MONK']['quote']['USD']['volume_24h'],0)
	. ",usd_percent_change_1h=" . round($search_coins_usd['data']['MONK']['quote']['USD']['percent_change_1h'],1)
	. ",usd_percent_change_24h=" . round($search_coins_usd['data']['MONK']['quote']['USD']['percent_change_24h'],1)
	. ",usd_percent_change_7d=" . round($search_coins_usd['data']['MONK']['quote']['USD']['percent_change_7d'],1)
	. ",usd_market_cap=" . round($search_coins_usd['data']['MONK']['quote']['USD']['market_cap'],0)
	. ",btc_price=" . round($search_coins_btc['data']['MONK']['quote']['BTC']['price'],8)
	. ",btc_volume_24h=" . round($search_coins_btc['data']['MONK']['quote']['BTC']['volume_24h'],1)
	. ",btc_percent_change_1h=" . round($search_coins_btc['data']['MONK']['quote']['BTC']['percent_change_1h'],1)
	. ",btc_percent_change_24h=" . round($search_coins_btc['data']['MONK']['quote']['BTC']['percent_change_24h'],1)
	. ",btc_percent_change_7d=" . round($search_coins_btc['data']['MONK']['quote']['BTC']['percent_change_7d'],1)
	. ",btc_market_cap=" . round($search_coins_btc['data']['MONK']['quote']['BTC']['market_cap'],0) . " WHERE 1";

	//echo "<br><br>Abaixo o SQL montado para UPDATE: <br>" . $sql_atualiza_moeda;
	//echo "<br><br> Tentando Rodar o UPDATE...";

	//Tenta Rodar Atualização no banco
	if ($con->query($sql_atualiza_moeda) === TRUE)
	{
		//echo "<br>Rodou! Atualizado com sucesso! Verificar no Banco de dados.";
	}
	else
	{
		echo "<br>Deu erro no UPDATE(con error): " . $con->error;
	}
	
	echo "<br><br>";
	
	//Tabela com dados buscados na API
	echo "<table><tr><td> Preço de 1 MONK </td><td> R$ " . round($search_coins_brl['data']['MONK']['quote']['BRL']["price"],2) . "</td><td>" . round($search_coins_btc['data']['MONK']['quote']['BTC']["price"],8) . "BTC</td><td> U$" . round($search_coins_usd['data']['MONK']['quote']['USD']["price"],2) . "</td></tr>";
	echo "<td> Volume em 24h </td><td> R$ " . round($search_coins_brl['data']['MONK']['quote']['BRL']["volume_24h"],0) . "</td><td>" . round($search_coins_btc['data']['MONK']['quote']['BTC']["volume_24h"],1) . "BTC</td><td> U$" . round($search_coins_usd['data']['MONK']['quote']['USD']["volume_24h"],0) . "</td></tr>";
	echo "<td> Variação de 1h </td><td> " . round($search_coins_brl['data']['MONK']['quote']['BRL']["percent_change_1h"],1) . "%</td><td>" . round($search_coins_btc['data']['MONK']['quote']['BTC']["percent_change_1h"],1) . "%</td><td>" . round($search_coins_usd['data']['MONK']['quote']['USD']["percent_change_1h"],1) . "%</td></tr>";
	echo "<td> Variação de 24h </td><td> " . round($search_coins_brl['data']['MONK']['quote']['BRL']["percent_change_24h"],1) . "%</td><td>" . round($search_coins_btc['data']['MONK']['quote']['BTC']["percent_change_24h"],1) . "%</td><td>" . round($search_coins_usd['data']['MONK']['quote']['USD']["percent_change_24h"],1) . "%</td></tr>";
	echo "<td> Variação de 1 Semana </td><td> " . round($search_coins_brl['data']['MONK']['quote']['BRL']["percent_change_7d"],1) . "%</td><td>" . round($search_coins_btc['data']['MONK']['quote']['BTC']["percent_change_7d"],1) . "%</td><td>" . round($search_coins_usd['data']['MONK']['quote']['USD']["percent_change_7d"],1) . "%</td></tr>";
	echo "<td> Capital do Mercado MONK</td><td> R$ " . round($search_coins_brl['data']['MONK']['quote']['BRL']["market_cap"],2) . "</td><td>" . round($search_coins_btc['data']['MONK']['quote']['BTC']["market_cap"],0) . "BTC</td><td> U$" . round($search_coins_usd['data']['MONK']['quote']['USD']["market_cap"],0) . "</td></tr>";
	echo "<tr><td colspan=4> Dados atualizados do site www.coinmarketcap.com nesse exato momento. </td></tr></table>";
	
	//Imprime os Vetores Obtidos via API devidamente estruturados
	echo '<br><pre>';
	print_r($search_coins_brl);
	print_r($search_coins_btc);
	print_r($search_coins_usd);
	echo '</pre>';

}
else
{
	//echo "Dados de " . $diferenca . " minutos atrás";
	//Tabela com dados buscados no Banco de Dados Local
	echo "<table><tr><td> Preço de 1 MONK </td><td> R$ " . $row["brl_price"] . "</td><td>" . $row["btc_price"] . "BTC</td><td> U$" . $row["usd_price"] . "</td></tr>";
	echo "<tr><td> Volume em 24h </td><td> R$ " . $row["brl_volume_24h"] . "</td><td>" . $row["btc_volume_24h"] . "BTC</td><td> U$" . $row["usd_volume_24h"] . "</td></tr>";
	echo "<tr><td> Variação de 1h </td><td> " . $row["brl_percent_change_1h"] . "%</td><td>" . $row["btc_percent_change_1h"] . "%</td><td>" . $row["usd_percent_change_1h"] . "%</td></tr>";
	echo "<tr><td> Variação de 24h </td><td> " . $row["brl_percent_change_24h"] . "%</td><td>" . $row["btc_percent_change_24h"] . "%</td><td>" . $row["usd_percent_change_24h"] . "%</td></tr>";
	echo "<tr><td> Variação de 1 Semana </td><td> " . $row["brl_percent_change_7d"] . "%</td><td>" . $row["btc_percent_change_7d"] . "%</td><td>" . $row["usd_percent_change_7d"] . "%</td></tr>";
	echo "<tr><td> Capital do Mercado MONK</td><td> R$ " . $row["brl_market_cap"] . "</td><td>" . $row["btc_market_cap"] . "BTC</td><td> U$" . $row["usd_market_cap"] . "</td></tr>";
	echo "<tr><td colspan=4> Dados atualizados do site www.coinmarketcap.com de " . $diferenca . " minutos atrás </td></tr></table>";
}

echo "<br><table><tr><td> www.cryptodicas.com - by Marcio Campos - TMJ Macacada!</td></tr><tr><td>theique@gmail.com</td></tr></table>";



$con->close();
?>
</center>
</body>
</html>