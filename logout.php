<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"><meta name="cryptodicas" content="width=device-width"><title>CRYPTODICAS - Logout</title>
</head>
<body>
<center>
<style type="text/css">
body {background-color: #fff; color: #222; font-family: sans-serif;}
pre {margin: 0; font-family: monospace;}
a:link {color: #009; text-decoration: none; background-color: #fff;}
a:hover {text-decoration: underline;}
table {border-collapse: collapse; border: 0; width: 400px; box-shadow: 1px 2px 3px #ccc;}
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

.buttonazul {
    background-color: #303F9F;
    border: none;
	width: 330px;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
}

.buttonvermelho {
    background-color: #EF9A9A;
    border: none;
	width: 330px;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
}

</style>

<?php
session_start();
if(!isset($_REQUEST['logmeout']))
{
	echo "<table><td><center>Voce realmente deseja sair da area restrita?</center></td></table>";
	echo '<br><br><a href="logout.php?logmeout" class=buttonazul>Sim</a> <br><br> <a href="javascript:history.go(-1)" class=buttonvermelho>N&atilde;o</a>';
}
else
{
	session_destroy();

		echo "<strong>Voce nao esta mais logado!</strong><br /><br />";
		echo "<strong>Login:</strong><br /><br />";

		include "index.html";
}
//RODAPÉ
echo "<br><table><tr><td><center> www.cryptodicas.com - by Marcio Campos - TMJ Macacada!</center></td></tr><tr><td><center>theique@gmail.com</center></td></tr></table>";
?>
</center>
</body>
</html>