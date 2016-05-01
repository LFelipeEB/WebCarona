<?php
require_once '../Carona/Carona.class.php';

$opc = $_GET['opc'];

switch ($opc){
	
	case 1:
		//Estou inserido porem dentro do Insert eu faço a busca de 1 carona
		$c = Carona::insertCarona(1, '2', '10:01:00', '2016-04-29', '1', 'Carona Teste', 'Largo Dom João', 'Campus JK', 'Passo pelo Amendoim');
		echo $c.'<br><br>Carona adicionada com sucesso';
		break;
	
	case 2:
		//busco todas e printo como um array de JSON
		echo json_encode(Carona::getAllCarona());
		break;
}
