<?php

/**
 * OPC 1 = Inserir User Teste
 * OPC 2 = Buscar por ID tem que passar o ID por get tbm.
 * OPC 3 = Efetuar login, passar login e senha por GET
 */
require_once '../Usuario/User.class.php';

$opc = $_GET['opc'];

switch ($opc){
	case 1:
		$u = User::insertUser('Teste', 'senha', 'email.1@partiufacu.com', 'curso', '38997332701', 'Endereco', 'linkFoto', 'Facebook');
		echo $u;
		break;
		
	case 2:
		$id = $_GET['id'];
		$u = User::getUserFromId($id);
		echo $u;
		break;
	
	case 3:
		$email = $_GET['email'];
		$id = User::verificaEmail($email);
		if($id){
			$senha = $_GET['senha'];
			$u = User::verificaUser($email, $senha);
			if($u){echo $u;}
			else{echo "senha incorreta";}
		}else{
			echo 'Email não encontrado';
		}
		break;
}