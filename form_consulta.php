<?php

session_start();

include "functions.php";

session_checker();

echo " ". $_SESSION['nome'] ." ". $_SESSION['sobrenome'] ." <br />
	<br /><br />";

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Formulario Consulta MONK</title>
<style type="text/css">
<!--
body {
	background-color: #CCCCCC;
}
-->
</style></head>



<body>
Consulta endereços MONK <br /><br />
<form name="consulta_addys" method="post" action="consulta.php">
Opcional<br /> 
<input name="opcional" type="text" id="opcional" value="<?php echo $_POST['opcional']; ?>" /><br />
<br />
<br />
<br />
<input type="submit" name="Submit" value="Enviar" />

</form>
</body>
</html>
