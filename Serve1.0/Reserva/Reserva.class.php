<?php

/**
 * A classe Reserva é responsavel por manipular a tabela reserva e Gerenciar tudo em que é relacionado a Reservas
 * 
 * @author lfelipeeb
 * @version 1
 * @access public 
 * @namespace Reserva
 */
class Reserva{

	private $idUser,$idCarona, $idReserva, $situacao, $obs;

	/*
	 * ------------------    CONSTRUCT    ---------------------
	 */
	
	/**
	 * Construtor da classe Reserva
	 * @param unknown $idUser		Id do Usuario que publicou a Carona
	 * @param unknown $idCarona		Id da Carona em que reservou a vaga
	 * @param unknown $idReserva	Id da Reserva
	 * @param unknown $situacao		Situação da Carona (Aprovado, aguardando Resposta, Reprovado)
	 * @param unknown $obs			Observações descrita na reserva
	 * 
	 * @author Luiz Felipe Evaristo
	 * @version 1
	 * @access public
	 * @category Carona
	 * @namespace Carona
	 */
	public function __construct($idUser,$idCarona, $idReserva, $situacao, $obs){
		$this->idUser = $idUser;
		$this->idCarona = $idCarona;
		$this->idReserva = $idReserva;
		$this->situacao = $situacao;
		$this->obs = $obs;
	}
	
	/*
	 * ------------------   PRIVATE FUNCTIONS ----------------------------
	 */
	
	
	/*
	 * ------------------ STATIC FUNCTIONS -------------------
	 */
	public static function getReservaFromId($idReserva){
		$con = Connection::open('mysql');
		
		$bd = $con->prepare('');
		
	}
	
	public static function registraReserva($idUser,$idCarona, $situacao, $obs){
		$con = Connection::open('mysql');
		$bd = $con->prepare('INSERT INTO RESERVA(ID_USUARIO, ID_CARONA, SITUACAO, OBS) VALUES (?,?,?,?)');
		$bd->bindParam(1, $idUser);
		$bd->bindParam(2, $idCarona);
		$bd->bindParam(3, $situacao);
		$bd->bindParam(4, $obs);
		
		if($bd->execute()){
			$bd2 = $con->prepare('SELECT * FROM RESERVA WHERE ID_USUARIO=? AND ID_CARONA=? AND SITUACAO=? AND OBS=?');
			$bd2->bindParam(1, $idUser);
			$bd2->bindParam(2, $idCarona);
			$bd2->bindParam(3, $situacao);
			$bd2->bindParam(4, $obs);
			if($bd2->execute()){
				if($bd2->rowCount() > 0){
					$result = $bd2->fetch(PDO::FETCH_OBJ);
					return new Reserva($result->ID_USUARIO, $result->ID_CARONA, $result->ID_RESERVA, $result->SITUAÇÂO, $result->OBS);
				}else{// IF RowCount
					return NULL;
				}
			}else{
				return NULL;
			}
		}else{ //IF EXECUTE
			return NULL;
		}
	}

	/*
	 * ---------------------- PUBLIC FUNCTIONS ----------------
	 */
	
	
	/*
	 *  -------- Override Functions AND GETs ---------------
	 */
	
	function __toString(){
		$u = User::getUserFromId($this->idUser);
		$c = Carona::getCaronaFromId($this->idCarona);
		return "
		ID da reserva: <b>{$this->idCarona}</b>
		<br><br>Nome da Carona: <b>{$c->getNomeCarona()}</b>
		<br>ID da Carona: <b>{$this->idCarona}</b>
		<br><br>Quem Reservou: <b>{$u->getNome()}</b>
		<br>Foto: <b>{$u->getLinkFoto()}</b>
		";
	}
	
	public function getIdUser(){return $this->idUser;}

	public function  getIdCarona(){ return $this->idCarona;}
	
	public function getIdReserva(){return $this->idReserva ;}

	public function getSituacao(){return $this->situacao ;}

	public function getObs(){return $this->obs ;}
	
}