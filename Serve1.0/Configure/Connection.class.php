<?php

final class Connection{
	
	/**
	 * Construtor privado garante que nenhum objeto do tipo Connection seja instanciado;
	 */
	private function __construct() {
	}
	
	/**
	 * <b>A função Open deve ser chamada para obter uma conexão com o banco de dados do aquivo <u>.ini</u></b>
	 * @param  nome do tipo de banco a ser criado.
	 * @author  Luiz Felipe Evaristo
	 * @version  1.0
	 * @name  open
	 * @namespace Configure
	 * @category  Connection BD
	 * @since  07/04/2016
	 */
	public static function open($banco) {
		switch (strtoupper($banco)) {
			case 'MYSQL' :
				if (file_exists ( 'mysql.ini' )) {
					$db = parse_ini_file ( mysql . ini );
				}
				break;
		}
		
		$user = isset ( $db ['user'] ) ? $db ['user'] : NULL;
		$pass = isset ( $db ['pass'] ) ? $db ['pass'] : NULL;
		$name = isset ( $db ['name'] ) ? $db ['name'] : NULL;
		$host = isset ( $db ['host'] ) ? $db ['host'] : NULL;
		
		$con = new PDO("mysql:host={$host}; dbname={$name};charset=utf8", $user, $pass);
		
		$con->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		
		return $con;
	}
}