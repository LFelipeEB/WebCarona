<?php

require_once '../Usuario/User.class.php';

class Carona{
	private $idCarona, $idUser, $vagas, $horario, $data, $preco, $disponivel, $nomeCarona, $localSaida, $localChegada, $obs;
	
	/*
	 * ------------------    CONSTRUCT    --------------------- 
	 */	
	/**
	 * Construtor da classe Carona;
	 * 
	 * @param unknown $idCarona  ID da Carona a ser criada
	 * @param unknown $idUser   ID da pessoa que publicou a carona
	 * @param unknown $vagas    Numero de Vagas restante na Carona 
	 * @param unknown $horario  Horario de saida
	 * @param unknown $data     Dia de saida da Carona'
	 * @param unknown $preco    Preço da Carona 
	 * @param unknown $disponivel  Booleano se a carona esta ativa ou não
	 * @param unknown $nomeCarona  Nome da carona (Atribuido por quem ofereceu)
	 * @param unknown $localSaida  Local de saida da Carona 
	 * @param unknown $localChegada Para aonde o carona esta indo
	 * @param unknown $obs     		Observações do Autor
	 * 
	 * @author Luiz Felipe Evaristo
	 * @version 1
	 * @access public
	 * @category Carona
	 * @namespace Carona
	 */
	public function __construct($idCarona, $idUser, $vagas, $horario, $data, $preco, $disponivel, $nomeCarona, $localSaida, $localChegada, $obs ){
		$this->data = $data;
		$this->disponivel = $disponivel;
		$this->horario = $horario;
		$this->idCarona = $idCarona;
		$this->idUser = $idUser;
		$this->localChegada = $localChegada;
		$this->localSaida = $localSaida;
		$this->nomeCarona = $nomeCarona;
		$this->obs = $obs;
		$this->preco = $preco;
		$this->vagas = $vagas;
	}
	
	/*
	 * ------------------   PRIVATE FUNCTIONS ----------------------------
	 */
	

	/*
	 * ------------------ STATIC FUNCTIONS -------------------
	 */
	/**
	 * Insere um carona no banco de dados.
	 * 
	 * @param unknown $idUser		ID do usuario que postou a carona
	 * @param unknown $vagas		Numero de vagas disponivel
	 * @param unknown $horario		Horario de saida
	 * @param unknown $data			Data de saida
	 * @param unknown $preco		Auda na carona
	 * @param unknown $nomeCarona	Nome da Carona
	 * @param unknown $localSaida	Local de aonde vai sair
	 * @param unknown $localChegada	Para oande o carro vai
	 * @param unknown $obs			Observações sobre a carona
	 * 
	 * @return Carona | NULL
	 * @author Luiz Felipe Evaristo
	 * @version 1
	 * @access public static
	 * @category Carona
	 * @namespace Carona
	 */
	public static function insertCarona($idUser, $vagas, $horario, $data, $preco, $nomeCarona, $localSaida, $localChegada, $obs){
		$con = Connection::open('mysql');
		
		$bd = $con->prepare('INSERT INTO CARONA(ID_USUARIO, VAGAS, HORARIO, DATA, PRECO, NOME_CARONA, LOCAL_SAIDA, LOCAL_CHEGADA, OBS) VALUES (?,?,?,?,?,?,?,?,?)');
		$bd->bindParam(1,$idUser );
		$bd->bindParam(2, $vagas);
		$bd->bindParam(3, $horario);
		$bd->bindParam(4, $data);
		$bd->bindParam(5, $preco);
		$bd->bindParam(6, $nomeCarona);
		$bd->bindParam(7, $localSaida);
		$bd->bindParam(8, $localChegada);
		$bd->bindParam(9, $obs);
		
		if($bd->execute()){
			
			$bd2 = $con->prepare('SELECT ID_CARONA FROM `CARONA` WHERE `ID_USUARIO` = ? AND `VAGAS` = ? AND `HORARIO` = ? AND `DATA` = ? AND `PRECO` = ? AND `DISPONIVEL` = true AND `NOME_CARONA` LIKE ? AND `LOCAL_SAIDA` LIKE ? AND `LOCAL_CHEGADA` LIKE ? AND `OBS` LIKE ?');
			$bd2->bindParam(1,$idUser );
			$bd2->bindParam(2, $vagas);
			$bd2->bindParam(3, $horario);
			$bd2->bindParam(4, $data);
			$bd2->bindParam(5, $preco);
			$bd2->bindParam(6, $nomeCarona);
			$bd2->bindParam(7, $localSaida);
			$bd2->bindParam(8, $localChegada);
			$bd2->bindParam(9, $obs);
			
			if($bd2->execute()){
					if($bd2->rowCount() > 0){
						$result = $bd2->fetch(PDO::FETCH_OBJ);
						return Carona::getCaronaFromId($result->ID_CARONA);
					}else{ // IF RowCount
						return NULL;
					}
			}else{ //IF  Execute
				return NULL;
			}
		}else{// IF EXECUTE INSERT
			return NULL;
		}
		
	}
	
	/**
	 * 
	 * @param unknown $idCarona  ID da carona a ser pesquizado
	 * @return Carona | NULL
	 * @author Luiz Felipe Evaristo
	 * @version 1
	 * @access public static
	 * @category Carona
	 * @namespace Carona
	 */
	public static function getCaronaFromId($idCarona){
		$con = Connection::open('mysql');
		$bd = $con->prepare('SELECT * FROM CARONA WHERE ID_CARONA = ?');
		$bd->bindParam(1, $idCarona);
		if($bd->execute()){
			if($bd->rowCount() > 0){
				$result = $bd->fetch(PDO::FETCH_OBJ);
				
				return new Carona($result->ID_CARONA,
						$result->ID_USUARIO, $result->VAGAS, $result->HORARIO, 
						$result->DATA, $result->PRECO, $result->DISPONIVEL, 
						$result->NOME_CARONA, $result->LOCAL_SAIDA, $result->LOCAL_CHEGADA, $result->OBS);
			}else{
				return NULL;
			}
		}else{//IF execute
			return NULL;
		}
	}
	
	/**
	 * Busca por todas  validas as caronas presentes no banco de dados.
	 * @return NULL | Array com todos os campos da tabela
	 * @access public static
	 * @category Carona
	 * @namespace Carona
	 */
	public static function getAllCarona(){
		$con = Connection::open('mysql');
		
		$bd = $con->prepare('SELECT ID_CARONA, ID_USUARIO, VAGAS, HORARIO, DATA, PRECO, NOME_CARONA, LOCAL_SAIDA, LOCAL_CHEGADA, OBS FROM CARONA WHERE ((DATA = CURRENT_DATE AND HORARIO > CURRENT_TIME) OR (DATA > CURRENT_DATE )) AND VAGAS > 0');
		
		if($bd->execute()){
			if($bd->rowCount() > 0){
				return $bd->fetchAll(PDO::FETCH_ASSOC);
			}else{
				return NULL;
			}
		}else{
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
		return "
				ID: <b>{$this->idCarona}</b>
		<br>Nome: <b>{$this->nomeCarona}</b>
		<br>Data: <b>{$this->data}</b>
		<br>Horario: <b>{$this->horario}</b>
		<br>Numero de Vagas: <b>{$this->vagas}</b>
		<br>Preço: <b>{$this->preco}</b>
		<br>Local de Saida: <b>{$this->localSaida}</b>
		<br>Local de Chegada: <b>{$this->localChegada}</b>
		<br>Observações: <b>{$this->obs}</b>
		<br><br>Quem publicou: <b>{$u->getNome()}</b>
		<br>Foto: <b>{$u->getLinkFoto()}</b>
		";
	}
	
	function getIdCarona(){return $this->idCarona;}
	
	function getIdUser(){return $this->idUser;}
	
	function getVagas(){return $this->vagas;}
	
	function getHorario(){return $this->horario;}
	
	function getData(){return $this->data;}
	
	function getPreco(){return $this->preco;}
	
	function isDisponivel(){return $this->disponivel;}
	
	function getNomeCarona(){return $this->nomeCarona;}
	
	function getLocalSaida(){return $this->localSaida;}
	
	function getLocalChegada(){return $this->localChegada;}
	
	function getObs(){return $this->obs;}
		
}