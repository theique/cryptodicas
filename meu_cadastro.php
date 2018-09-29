<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"><meta name="viewport" content="width=device-width"><title>CRYPTODICAS - Meu Cadastro</title>
<link rel="stylesheet" type="text/css" href="viewsource.css">
</head>

<body id="viewsource" class="highlight" style="-moz-tab-size: 4" contextmenu="actions">

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

include "config.php";
include "functions.php";
include "apijson.php";

session_start();
session_checker();

echo "<table><tr><td><center>";
echo "Olá ". $_SESSION['nome'] ." ". $_SESSION['sobrenome'] .", atualize seu cadastro abaixo: ";
echo "</center></td></tr></table><br>";


// inicializa variaveis
$usuarioErr = $nomeErr = $sobrenomeErr = $emailErr = $infoErr = $celularErr = "";
$usuario = $nome = $sobrenome = $email = $info = $celular = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["nome"])) {
    $nameErr = "Nome não pode estar vazio";
  } else {
    $name = test_input($_POST["nome"]);
    if (!preg_match("/^[a-zA-Z ]*$/",$name)) {
      $nameErr = "Apenas letras e espaços permitidos para Nome"; 
    }
  }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["sobrenome"])) {
    $nameErr = "SobreNome não pode estar vazio";
  } else {
    $name = test_input($_POST["sobrenome"]);
    if (!preg_match("/^[a-zA-Z ]*$/",$name)) {
      $nameErr = "Apenas letras e espaços permitidos para SobreNome"; 
    }
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["celular"])) {
    $nameErr = "Telefone Celular não pode estar vazio";
  } else {
    $name = test_input($_POST["celular"]);
  }
  
  if (empty($_POST["email"])) {
    $emailErr = "Email não pode estar vazio";
  } else {
    $email = test_input($_POST["email"]);
    // checa email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $emailErr = "E-mail inválido"; 
    }
  }
 
  if (empty($_POST["info"])) {
    $info = "";
  } else {
    $info = test_input($_POST["info"]);
  }

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>

<p><span class="error">* campo obrigatório</span></p>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
  Nome: <input type="text" name="Nome" value="<?php echo $nome;?>">
  <span class="error">* <?php echo $nomeErr;?></span>
  <br><br>
  Sobre Nome: <input type="text" name="Sobre Nome" value="<?php echo $sobrenome;?>">
  <span class="error">* <?php echo $sobrenomeErr;?></span>
  <br><br>
  E-mail: <input type="text" name="email" value="<?php echo $email;?>">
  <span class="error">* <?php echo $emailErr;?></span>
  <br><br>
  Telefone Celular: <input type="text" name="Celular" value="<?php echo $celular;?>">
  <span class="error"><?php echo $celularErr;?></span>
  <br><br>
  Info: <textarea name="Info" rows="5" cols="40"><?php echo $info;?></textarea>
  <br><br>
  
  <input type="submit" name="submit" value="Submit">  
</form>


<?php
echo "<h2>Informado:</h2>";
echo $nome;
echo "<br>";
echo $email;
echo "<br>";
echo $website;
echo "<br>";
echo $comment;
echo "<br>";
echo $gender;
?>

</body>
</html>