<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Formulario Cadastro</title>
<style type="text/css">
<!--
body {
	background-color: #CCCCCC;
}
-->
</style></head>

<body>
Formulario de Cadastro <br /><br />
<form name="cadastro" method="post" action="cadastra.php">
Nome<br /> 
<input name="nome" type="text" id="nome" value="<?php echo $_POST['nome']; ?>" /><br />
<br />
Sobrenome<br /> 
<input name="sobrenome" type="text" id="sobrenome" value="<?php echo $_POST['sobrenome']; ?>" /><br />
<br />
Email<br /> 
<input name="email" type="text" id="email" value="<?php echo $_POST['email']; ?>" /><br />
<br />
Nome de Usu&aacute;rio<br /> 
<input name="usuario" type="text" id="usuario" value="<?php echo $_POST['usuario']; ?>" /><br />
<br />
+ informacoes  sobre voce<br />
<textarea name="info" id="info"><?php echo $_POST['info']; ?></textarea>
<br />
<br />
<input type="submit" name="Submit" value="Enviar" />

</form>
</body>
</html>
