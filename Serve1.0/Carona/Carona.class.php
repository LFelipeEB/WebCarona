<?php

require_once '../Usuario/User.class.php';

class Carona{
	private $idCarona, $idUser, $vagas, $horario, $data, $preco, $disponivel, $nomeCarona, $localSaida, $localChegada, $obs;
	
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
}