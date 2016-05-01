<?php
require_once '../Reserva/Reserva.class.php';

$opc = $_GET ['opc'];

switch ($opc) {
	case 1 :
		$r = Reserva::registraReserva ( 1, 1, "Espero no Posto do Largo" );
		echo $r;
		break;
	
	case 2 :
		$r = Reserva::getReservaFromId(15);
		echo $r;
		break;
}