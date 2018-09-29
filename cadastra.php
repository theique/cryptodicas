<?php

include "config.php";
include "functions.php";

$nome = trim($_POST['nome']);
$sobrenome = trim($_POST['sobrenome']);
$email = trim($_POST['email']);
$usuario = trim($_POST['usuario']);
$info = trim($_POST['info']);

if ((!$nome) || (!$sobrenome) || (!$email) || (!$usuario)){

	echo "ERRO: Voce nao enviou as seguintes informacoes requeridas para o cadastro! <br /> <br />";

	if (!$nome){

		echo "Nome e um campo requerido. <br />";

	}

	if (!$sobrenome){

		echo "Sobrenome e um campo requerido. <br />";

	}

	if (!$email){

		echo "Email e um campo requerido.<br />";

	}

	if (!$usuario){

		echo "Nome de Usuario e um campo requerido. <br />";

	}

	echo "<br />Preencha os campos necessarios abaixo: <br /><br />";

	include "formulario_cadastro.php"; 

}
else{

	$sql_email_check = mysqli_query($con,"SELECT COUNT(usuario_id) FROM usuarios WHERE email='{$email}'");
	$sql_usuario_check = mysqli_query($con,"SELECT COUNT(usuario_id) FROM usuarios WHERE usuario='{$usuario}'");

	$eReg = mysqli_fetch_array($sql_email_check);
	$uReg = mysqli_fetch_array($sql_usuario_check);

	$email_check = $eReg[0];
	$usuario_check = $uReg[0];
	
	if (($email_check > 0) || ($usuario_check > 0)){

		echo "<strong>ERRO </strong>- Por favor corrija os seguintes erros abaixo: <br /> <br />";

		if ($email_check > 0){

		    echo "Este email ( <strong>".$email."</strong> ) ja esta sendo utilizado.<br />Por favor utilize outra conta de email! <br />";

		    unset($email);

		}

		if ($usuario_check > 0){

			echo "Este nome de usuario ( <strong>".$usuario."</strong> ) ja esta sendo utilizado.<br />Por favor utilize outro nome de usuario!<br />";

			unset($usuario);

		}

		echo "<br />";
		include "formulario_cadastro.php";

	}
	else
	{

		$email = strtolower(trim($_POST['email']));
		$char = "@";
		$pos = strpos($email, $char);

        if ($pos === false){

			echo "<strong>ERRO:</strong><br />";
			echo "O endereco de email [ <strong><em>".$email."</em></strong> ] que esta tentando utilizar nao e valido.<br />";
			echo "Por favor, utilize um email valido.<br /><br />";
			include "formulario_cadastro.php"; 

        }else{

            $v_mail = verifica_email($email);

            if ($v_mail){

        			function makeRandomPassword(){

					$salt = "abchefghjkmnpqrstuvwxyz0123456789";
					srand((double)microtime()*1000000); 

					$i = 0;
                                        $pass = "";

					while($i <= 7){

						$num = rand() % 33;
						$tmp = substr($salt, $num, 1);
						$pass = $pass . $tmp;
						$i++;

					}

					return $pass;

				}

				$senha_randomica = makeRandomPassword();

				$senha = md5($senha_randomica);
				echo "Senha: " . $senha;

				// Inserindo os dados no banco de dados

				$info = htmlspecialchars($info);

				echo "inserindo no BD...";
				
				$sql = "INSERT INTO `usuarios` (`nome`, `sobrenome`, `email`, `usuario`, `senha`, `info`, `celular`, `data_cadastro`, `ativado`) VALUES ('{$nome}', '{$sobrenome}', '{$email}', '{$usuario}', '{$senha}', '{$info}','', now(), '1')";
						
				
				//Tenta Rodar Atualização no banco
				if ($con->query($sql) === TRUE)
				{
					echo "<br>Rodou! Verificar no Banco de dados.";
				}
				else
				{
					echo "<br>Deu erro (con error): " . $con->error;
				}
				
								
							
				if (!$sql){

					echo "Ocorreu algum erro ao criar sua conta, por favor entre em contato com o Webmaster.";

				}
				else {

					$usuario_id = mysqli_insert_id($con);

					echo "Prezado <strong>$nome $sobrenome</strong>,
			
								<br />
			
								Obrigado pelo seu cadastro 
														
								<br /><br />

								Para confirmar seu cadastro e ativar sua conta, 
								clique no link abaixo ou copie e cole o link na barra de endereco do seu navegador.
						
								<br /><br /> 

								<a href ='http://www.cryptodicas.com/ativar.php?id=$usuario_id&code=$senha'>
								http://www.cryptodicas.com/ativar.php?id=$usuario_id&code=$senha
								</a>

								<br /> <br />

								Apos a ativacao de sua conta, voce podera ter acesso ao conteudo exclusivo, 
								efetuando o login com os dados abaixo:
						
								<br /> <br /> 

								<strong>Usuario</strong>: {$usuario}
						
								<br /> 
						
								<strong>Senha</strong>: {$senha_randomica}
						
								<br /><br /> 

								Obrigado!<br /> <br /> 

								Cryptodicas.com<br /> <br /> <br /> 

								";

					//mail($email, $subject, $mensagem, $headers);

					//echo $email." ) um pedido de confirma&ccedil;&atilde;o de cadastro, 
					//		por favor verifique e sigas as instru&ccedil;&otilde;es!";

				}

            }else{

                echo "<strong>ERRO:</strong><br />";
                echo "O endereco de email [ <strong><em>".$email."</em></strong> ] que esta tentando utilizar nao e valido.<br />";
                echo "Por favor, utilize um email valido.<br /><br />";
				include "formulario_cadastro.php"; 

            }

        }

    }

}

?>
