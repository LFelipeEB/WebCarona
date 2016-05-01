<?php
require_once '../Configure/Connection.class.php';
require_once '../Usuario/User.class.php';
require_once '../Carona/Carona.class.php';

/**
 * A classe Reserva é responsavel por manipular a tabela reserva e Gerenciar tudo em que é relacionado a Reservas
 *
 * @author lfelipeeb
 * @version 1
 * @access public
 * @namespace Reserva
 */
class Reserva {
	private $idReserva, $situacao, $obs, $carona, $user;
	/*
	 * ------------------ CONSTRUCT ---------------------
	 */
	
	/**
	 * Construtor da classe Reserva
	 *
	 * @param User $user
	 *        	Id do Usuario que publicou a Carona
	 * @param Carona $carona
	 *        	Id da Carona em que reservou a vaga
	 * @param unknown $idReserva
	 *        	Id da Reserva
	 * @param unknown $situacao
	 *        	Situação da Carona (Aprovado, aguardando Resposta, Reprovado)
	 * @param unknown $obs
	 *        	Observações descrita na reserva
	 *        	
	 * @author Luiz Felipe Evaristo
	 * @version 1
	 * @access public
	 * @category Carona
	 * @namespace Carona
	 */
	public function __construct(User $user, Carona $carona, $idReserva, $situacao, $obs) {
		$this->user = $user;
		$this->carona = $carona;
		$this->idReserva = $idReserva;
		$this->situacao = $situacao;
		$this->obs = $obs;
	}
	
	/*
	 * ------------------ PRIVATE FUNCTIONS ----------------------------
	 */
	
	/**
	 * Função responsavel por verificar se existe vagas na carona solicitada.
	 * 
	 * @param unknown $idCarona
	 * @return boolean | NULL
	 * 
	 * @author Luiz Felipe Evaristo
	 * @version 1
	 * @access public
	 * @category Carona
	 * @namespace Carona
	 */
	private static function isVaga($idCarona) {
		$con = Connection::open ( 'mysql' );
		
		$bd = $con->prepare ( 'SELECT VAGAS FROM CARONA WHERE ID_CARONA = ?' );
		$bd->bindParam ( 1, $idCarona );
		
		if ($bd->execute ()) {
			$resutl = $bd->fetch ( PDO::FETCH_OBJ );
			if ($resutl->VAGAS > 0) {
				return true;
			} else { //IF VAGAS > 0
				return false;
			}
		} else {// IF BD EXECUTE
			return NULL;
		}
	}
	
	/**
	 * Metodo responsavel por fazer o update na tabela da carona.
	 * @param unknown $idCarona
	 * 
	 * @author Luiz Felipe Evaristo
	 * @version 1
	 * @access public
	 * @category Carona
	 * @namespace Carona
	 
	 */
	private static function updateCarona($idCarona) {
		$con = Connection::open ( 'mysql' );
		
		$bd = $con->prepare ( 'SELECT VAGAS FROM CARONA WHERE ID_CARONA = ?' );
		$bd->bindParam ( 1, $idCarona );
		
		if ($bd->execute ()) {
			$resutl = $bd->fetch ( PDO::FETCH_OBJ );
			$vagas = $resutl->VAGAS;
			if ($vagas == 1) {
				$bd2 = $con->prepare ( 'UPDATE CARONA SET VAGAS = 0 , DISPONIVEL = 0 WHERE ID_CARONA = ?' );
				$bd2->bindParam ( 1, $idCarona );
				if ($bd2->execute ()) {
					return true;
				} else {// IF BD2 EXECUTE
					return null;
				}
			} else {// IF VAGAS == 1
				$vagas = $vagas - 1;
				$bd2 = $con->prepare ( 'UPDATE CARONA SET VAGAS =  ? WHERE ID_CARONA = ?' );
				$bd2->bindParam ( 1, $vagas );
				$bd2->bindParam ( 2, $idCarona );
				if ($bd2->execute ()) {
					return true;
				} else {//IF BD2 EXecute
					return null;
				}
			}
		}else{//IF BD EXECUTE
			return NULL;
		}
	}
	
	/*
	 * ------------------  STATIC FUNCTIONS -------------------
	 */
	
	public static function getReservaFromId($idReserva) {
		$con = Connection::open ( 'mysql' );
		
		$bd = $con->prepare ( 'SELECT * FROM RESERVA WHERE ID_RESERVA=?' );
		$bd->bindParam(1, $idReserva);
		if($bd->execute()){
			if($bd->rowCount() > 0){
				$result = $bd->fetch(PDO::FETCH_OBJ);
				return new Reserva(User::getUserFromId($result->ID_USUARIO), 
						Carona::getCaronaFromId($result->ID_CARONA), 
						$result->ID_RESERVA, $result->SITUACAO, $result->OBS);
			}else{//IF RowCount
				return NULL;
			}
		}else{//IF BD Execute
			return null;
		}
	}
	
	/**
	 * Função responsvael por registrar a carona no banco.
	 * Esta função verifica se existe vagas e se existe resgrista a carona, e faz o update no banco.
	 * 
	 * @param unknown $idUser		ID do Usuario que solicitou a reserva
	 * @param unknown $idCarona		ID da carona que é solicitada a Reserva
	 * @param unknown $obs			Observação da reserva.
	 * @return Reserva | NULL
	 * 
	 * @author Luiz Felipe Evaristo
	 * @version 1
	 * @access public
	 * @category Carona
	 * @namespace Carona
	 
	 */
	public static function registraReserva($idUser, $idCarona, $obs) {
		$con = Connection::open ( 'mysql' );
		
		if (Reserva::isVaga ( $idCarona )) {
			$bd = $con->prepare ( 'INSERT INTO RESERVA(ID_USUARIO, ID_CARONA, SITUACAO, OBS) VALUES (?,?,0,?)' );
			$bd->bindParam ( 1, $idUser );
			$bd->bindParam ( 2, $idCarona );
			$bd->bindParam ( 3, $obs );
			
			if ($bd->execute ()) {
				$bd2 = $con->prepare ( 'SELECT * FROM RESERVA WHERE ID_USUARIO=? AND ID_CARONA=? AND SITUACAO=0 AND OBS=?' );
				$bd2->bindParam ( 1, $idUser );
				$bd2->bindParam ( 2, $idCarona );
				$bd2->bindParam ( 3, $obs );
				if ($bd2->execute ()) {
					if ($bd2->rowCount () > 0) {
						Reserva::updateCarona ( $idCarona );
						$result = $bd2->fetch ( PDO::FETCH_OBJ );
						return new Reserva ( User::getUserFromId ( $result->ID_USUARIO ), Carona::getCaronaFromId ( $result->ID_CARONA ), $result->ID_RESERVA, $result->SITUACAO, $result->OBS );
					} else { // IF RowCount
						return NULL;
					}
				} else {
					return NULL;
				}
			} else { // IF EXECUTE
				return NULL;
			}
		} else {
			return NULL;
		}
	}
	
	/*
	 * ---------------------- PUBLIC FUNCTIONS ----------------
	 */
	
	/*
	 * -------- Override Functions AND GETs ---------------
	 */
	function __toString() {
		return "
		ID da reserva: <b>{$this->idReserva}</b>
		<br><br>Nome da Carona: <b>{$this->carona->getNomeCarona()}</b>
		<br>ID da Carona: <b>{$this->carona->getIdCarona()}</b>
		<br><br>Quem Reservou: <b>{$this->user->getNome()}</b>
		<br>Foto: <b>{$this->user->getLinkFoto()}</b>
		";
	}
	public function getIdUser() {
		return $this->idUser;
	}
	public function getIdCarona() {
		return $this->idCarona;
	}
	public function getIdReserva() {
		return $this->idReserva;
	}
	public function getSituacao() {
		return $this->situacao;
	}
	public function getObs() {
		return $this->obs;
	}
}