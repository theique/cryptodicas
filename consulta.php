<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"><meta name="cryptodicas" content="width=device-width"><title>CRYPTODICAS - Consulta MONKs</title>
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

echo "<table><tr><td><center>";
echo "Olá ". $_SESSION['nome'] ." ". $_SESSION['sobrenome'] .", seus endereços cadastrados estão abaixo: ";
echo "</center></td></tr></table><br>";

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
	
	$fonte_dos_dados = "API";
}
else
{
	$fonte_dos_dados = "DATABASE";
}

//Busca todos endereços do usuario logado
$sql_busca_enderecos_usuario = "SELECT * FROM monk_add WHERE usuario_id='{$_SESSION['usuario_id']}'";
$result = $con->query($sql_busca_enderecos_usuario);

$total = 0;
$total_geral = 0;

//Caso tenha endereços cadastrados entra
if ($result->num_rows > 0) 
{
    //Vai fazer um a um para todos os endereços cadastrados
	while($row_end = $result->fetch_assoc()) 
	{
		//Busca saldo no blockchain usando a api da coinexplorer.net
		$url_saldo = "https://www.coinexplorer.net/api/v1/MONK/address/balance?address=".$row_end["endereco"];
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
				//echo "ERRO ao buscar saldo do endereço: ".$row_end["endereco"];
				break;
			}
			//echo "tentando novamente com explorer...";
			$response = file_get_contents($url_saldo);
			$sucesso = substr($response,17,4);
			$endereco = substr($response,48,34);
			$saldo = str_replace('"',"",substr($response,86,12));
		}

		//Caso o Endereço seja Mastermone busca o Status do MN
		if ( $row_end["MN"] == "1" )
		{
			//Busca status do Masternode usando API do coinexplorer.net
			$url_masternode = "https://www.coinexplorer.net/api/v1/MONK/masternode?address=".$row_end["endereco"];
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
					echo "ERRO ao buscar status do endereço cadastrado como MASTERNODE: ".$row_end["endereco"];
					break;
				}
				$mn_response = file_get_contents($url_masternode);
				$mn_sucesso = substr($mn_response,17,4);
				$mn_status = substr($mn_response,222,7);
				$mn_ativo = str_replace('"',"",substr($mn_response,254,4));
			}
		}
		
		//Calcula o Saldo correspondente ao valor % cadastrado para o endereço
		$valor_percent = $saldo*$row_end["percent"]/100;

		//ENDEREÇOS UM A UM
		//Montando HTML de saída
		echo "<table><tr><td>" . $row_end["nome"]. '</td><td colspan="3"><a href="https://www.coinexplorer.net/MONK/address/' . $row_end["endereco"]. '" target="_blank">' . $row_end["endereco"] . '</a></td></tr>';
		if ( $row_end["MN"] == "0" )
		{	
			//ENDEREÇOS UM A UM
			//Se NAO é masternode
			echo "<tr><td>Saldo Atual</td><td>Sua Parte</td></tr>";
			echo "<tr><td>" . round($saldo,1) . " MONK</td><td>" . round($valor_percent,1) . " MONK (" . round($row_end["percent"],2) . "%)</td></tr></table> ";
		}
		else
		{
			//ENDEREÇOS UM A UM
			//Se é masternode
			echo "<tr><td>Status do MN</td><td>MN Ativo</td><td>Saldo Atual</td><td>Sua Parte</td></tr>";
			echo "<tr><td>" . $mn_status . "</td><td>" . $mn_ativo . "</td><td>" . round($saldo,1) . " MONK</td><td>" . round($valor_percent,1) . " MONK (" . round($row_end["percent"],2) . "%)</td></tr></table> ";
		}
		
		echo "<br>";
		
		//Soma os valores Total e % do endereço para ao final termos os Totais Gerais
		$total = $total + $valor_percent;
		$total_geral = $total_geral + $saldo;
	}
	
	//SOMATÓRIOS DOS ENDEREÇOS
	//Exibe tabela final com os valores Toais Gerais de todos endereços cadastrados para o usuário logado
	if ( $fonte_dos_dados == "API" )
	{
		echo "<table><tr><td>Total Geral</td><td>" . round($total_geral,1) . " MONK</td><td>R$ " . round($total_geral*$search_coins_brl['data']['MONK']['quote']['BRL']["price"],1) . "</td><td>" . round($total_geral*$search_coins_btc['data']['MONK']['quote']['BTC']["price"],8) . "BTC</td><td> U$" . round($total_geral*$search_coins_usd['data']['MONK']['quote']['USD']["price"],1) . "</td></tr>";
		echo "<tr><td>Seu Total</td><td>" . round($total,1) . " MONK</td><td>R$ " . round($total*$search_coins_brl['data']['MONK']['quote']['BRL']["price"],1) . "</td><td>" . round($total*$search_coins_btc['data']['MONK']['quote']['BTC']["price"],8) . "BTC</td><td> U$" . round($total*$search_coins_usd['data']['MONK']['quote']['USD']["price"],1) . "</td></tr></table>";
	}
	else
	{
		echo "<table><tr><td>Total Geral</td><td>" . round($total_geral,1) . " MONK</td><td> R$ " . round($total_geral*$row["brl_price"],1) . "</td><td>" . round($total_geral*$row["btc_price"],8) . "BTC</td><td> U$" . round($total_geral*$row["usd_price"],1) . "</td></tr>";
		echo "<tr><td>Seu Total</td><td>" . round($total,1) . " MONK</td><td> R$ " . round($total*$row["brl_price"],1) . "</td><td>" . round($total*$row["btc_price"],8) . "BTC</td><td> U$" . round($total*$row["usd_price"],1) . "</td></tr></table>";
	}
} 
else 
{
    echo "<table><tr><td><h1> Ainda não há endereços cadastrados para usuário id " . $_SESSION['usuario_id'] . "</h1></td></tr></table>";
}

echo "<br>";

//DADOS MONKEY FINAL
//Imprime as tabelas finais com os dados da MONK atualizados
if ( $fonte_dos_dados == "API" )
{
	//DADOS MONKEY FINAL
	//Tabela com dados buscados na API
	echo "<table><tr><td> Preço de 1 MONK </td><td> R$ " . round($search_coins_brl['data']['MONK']['quote']['BRL']["price"],2) . "</td><td>" . round($search_coins_btc['data']['MONK']['quote']['BTC']["price"],8) . "BTC</td><td> U$" . round($search_coins_usd['data']['MONK']['quote']['USD']["price"],2) . "</td></tr>";
	echo "<tr><td> Volume em 24h </td><td> R$ " . round($search_coins_brl['data']['MONK']['quote']['BRL']["volume_24h"],0) . "</td><td>" . round($search_coins_btc['data']['MONK']['quote']['BTC']["volume_24h"],1) . "BTC</td><td> U$" . round($search_coins_usd['data']['MONK']['quote']['USD']["volume_24h"],0) . "</td></tr>";
	echo "<tr><td> Variação de 1h </td><td> " . round($search_coins_brl['data']['MONK']['quote']['BRL']["percent_change_1h"],1) . "%</td><td>" . round($search_coins_btc['data']['MONK']['quote']['BTC']["percent_change_1h"],1) . "%</td><td>" . round($search_coins_usd['data']['MONK']['quote']['USD']["percent_change_1h"],1) . "%</td></tr>";
	echo "<tr><td> Variação de 24h </td><td> " . round($search_coins_brl['data']['MONK']['quote']['BRL']["percent_change_24h"],1) . "%</td><td>" . round($search_coins_btc['data']['MONK']['quote']['BTC']["percent_change_24h"],1) . "%</td><td>" . round($search_coins_usd['data']['MONK']['quote']['USD']["percent_change_24h"],1) . "%</td></tr>";
	echo "<tr><td> Variação de 1 Semana </td><td> " . round($search_coins_brl['data']['MONK']['quote']['BRL']["percent_change_7d"],1) . "%</td><td>" . round($search_coins_btc['data']['MONK']['quote']['BTC']["percent_change_7d"],1) . "%</td><td>" . round($search_coins_usd['data']['MONK']['quote']['USD']["percent_change_7d"],1) . "%</td></tr>";
	echo "<tr><td> Capital do Mercado MONK</td><td> R$ " . round($search_coins_brl['data']['MONK']['quote']['BRL']["market_cap"],2) . "</td><td>" . round($search_coins_btc['data']['MONK']['quote']['BTC']["market_cap"],0) . "BTC</td><td> U$" . round($search_coins_usd['data']['MONK']['quote']['USD']["market_cap"],0) . "</td></tr>";
	echo "<tr><td colspan=4> Dados atualizados do site www.coinmarketcap.com nesse exato momento. </td></tr></table>";

	//Imprime os Vetores Obtidos via API devidamente estruturados
	//echo '<br><pre>';
	//print_r($search_coins_brl);
	//print_r($search_coins_btc);
	//print_r($search_coins_usd);
	//echo '</pre>';
}
else
{
	//DADOS MONKEY FINAL
	//Tabela com dados buscados no Banco de Dados Local
	echo "<table><tr><td> Preço de 1 MONK </td><td> R$ " . $row["brl_price"] . "</td><td>" . $row["btc_price"] . "BTC</td><td> U$" . $row["usd_price"] . "</td></tr>";
	echo "<tr><td> Volume em 24h </td><td> R$ " . $row["brl_volume_24h"] . "</td><td>" . $row["btc_volume_24h"] . "BTC</td><td> U$" . $row["usd_volume_24h"] . "</td></tr>";
	echo "<tr><td> Variação de 1h </td><td> " . $row["brl_percent_change_1h"] . "%</td><td>" . $row["btc_percent_change_1h"] . "%</td><td>" . $row["usd_percent_change_1h"] . "%</td></tr>";
	echo "<tr><td> Variação de 24h </td><td> " . $row["brl_percent_change_24h"] . "%</td><td>" . $row["btc_percent_change_24h"] . "%</td><td>" . $row["usd_percent_change_24h"] . "%</td></tr>";
	echo "<tr><td> Variação de 1 Semana </td><td> " . $row["brl_percent_change_7d"] . "%</td><td>" . $row["btc_percent_change_7d"] . "%</td><td>" . $row["usd_percent_change_7d"] . "%</td></tr>";
	echo "<tr><td> Capital do Mercado MONK</td><td> R$ " . $row["brl_market_cap"] . "</td><td>" . $row["btc_market_cap"] . "BTC</td><td> U$" . $row["usd_market_cap"] . "</td></tr>";
	echo "<tr><td colspan=4> Dados atualizados do site www.coinmarketcap.com de " . $diferenca . " minutos atrás </td></tr></table>";
}

//echo '<pre>';
//print_r($search_coins);
//echo '</pre>';

//RODAPÉ
echo "<br><table><tr><td><center> www.cryptodicas.com - by Marcio Campos - TMJ Macacada!</center></td></tr><tr><td><center>theique@gmail.com</center></td></tr></table>";

//Fecha conexão com Banco de Dados
$con->close();
?>
</center>
</body>
</html>