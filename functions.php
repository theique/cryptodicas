<?php

function session_checker(){

	if(!isset($_SESSION['usuario_id'])){

		header ("Location:index.html");

		exit(); 
	}
}

function verifica_email($EMAIL){

    list($User, $Domain) = explode("@", $EMAIL);
    $result = @checkdnsrr($Domain, 'MX');

    return($result);

}

?>