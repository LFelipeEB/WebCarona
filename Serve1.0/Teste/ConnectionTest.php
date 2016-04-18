<?php
require_once '../Configure/Connection.class.php';

$con = Connection::open('mysql');

if($con){
	echo "Sucesso conexão estabelecida";
}else{
	echo "Algo esta errado !";
}