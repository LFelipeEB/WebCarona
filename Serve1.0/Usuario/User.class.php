<?php

require_once '../Configure/Connection.class.php';

/**
 * Esta classe tem intuito de controlar todas as atividades que tem relaçãoo com o Usuario no banco de dados
 * do Aplicativo PartiuFacu
 * 
 * @since Abril/2016
 * @namespace Usuario
 * @author Luiz Felipe Evaristo
 * @access public
 */
class User {
	private $idUser, $nome, $email, $curso, $telefone, $endereco, $link_foto, $idFacebook;

	/*
	 * ------------------    CONSTRUCT    --------------------- 
	 */
	
	/**
	 * <b>Construtor da classe para criar objetos do tipo User como atributos passado nos parametros;<\b>
	 *
	 * @param unknown $idUser
	 *        	 id correspondendo do banco de dados
	 * @param unknown $nome
	 *        	 nome do usuario
	 * @param unknown $email
	 *        	 email do usuario
	 * @param unknown $curso
	 *        	 curso do usuario
	 * @param unknown $telefone
	 *        	 telefone do usuario
	 * @param unknown $endereco
	 *        	 endereço do usuario, normalmente é um bairro
	 * @param unknown $link_foto
	 *        	 link que da acesso a foto do usuario
	 * @param unknown $idFacebook
	 *        	 id do facebook, caso ele tenha sincronizado com facebook
	 * @access public
	 * @version 1.0
	 * @author Luiz Felipe Evaristo
	 * @category Usuario
	 * @namespace Usuario
	 */
	public function __construct($idUser, $nome, $email, $curso, $telefone, $endereco, $link_foto, $idFacebook) {
		$this->curso = $curso;
		$this->nome = $nome;
		$this->email = $email;
		$this->endereco = $endereco;
		$this->idFacebook = $idFacebook;
		$this->idUser = $idUser;
		$this->link_foto = $link_foto;
		$this->idFacebook = $idFacebook;
	}
	
	/*
	 * ------------------   PRIVATE FUNCTIONS ----------------------------
	 */
		
	/**
	 * <b.Função que criptografa a senha do usuario de forma segura usando bcrypt.
	 * A função usa uma forma de salt seguro, com base no tempo, tornando assim um unico salt.<\b>
	 *
	 * @param $p -recebe
	 *        	a senha a ser criptografada.
	 * @access private
	 * @version 1.0
	 * @author Mauricio
	 * @namespace Usuario
	 */
	private static function hash_pass($p) { // hash de senha dos usuarios
		static $cost = 11;
		static $type = "$2a$";
		static $salt_length = 22;
		
		// GERANDO SALT
		$string = uniqid ( mt_rand (), true );
		
		$salt = base64_encode ( $string );
		$salt = str_replace ( '+', '.', $salt );
		
		$salt_final = substr ( $salt, 0, $salt_length );
		
		// ENCRYPT
		$hash_bcrypt = crypt ( $p, '$2a$' . $cost . '$' . $salt_final . '$' );
		
		return $hash_bcrypt;
	}
	
	/*
	 * ------------------ STATIC FUNCTIONS ------------------- 
	 */
	
	
	/**
	 * <b>Função que busca um usuario no banco de dados atravez do seu id<\b>
	 *
	 * @param unknown $idUser
	 *        	 id do banco de bados a ser buscado
	 * @return User  |  NULL
	 * @access public
	 * @author Luiz Felipe Evaristo
	 * @static
	 *
	 * @namespace Usuario
	 */
	public static function getUserFromId($idUser) {
		$con = Connection::open ( 'mysql' );
		
		$bd = $con->prepare ( "SELECT id_usuario, nome_user, email, curso, telefone, endereco, link_foto, id_facebook FROM USUARIO WHERE id_usuario = ?" );
		$bd->bindParam ( 1, $idUser );
		
		if ($bd->execute ()) {
			if ($bd->rowCount () > 0) {
				while ( $row = $bd->fetch ( PDO::FETCH_OBJ ) ) {
					return new User ( $row->id_usuario, $row->nome_user, $row->email, $row->curso, $row->telefone, $row->endereco, $row->link_foto, $row->id_facebook );
				}
			} else { // id rowCount()
				return null;
			}
		} else { // if execute()
			return NULL;
		}
	}
	
	/**
	 * <b>Função responsavel por inserir um usuario no banco de dados;
	 *
	 * @param unknown $idUser
	 *        	 id correspondendo do banco de dados
	 * @param unknown $nome
	 *        	 nome do usuario
	 * @param unknown $email
	 *        	 email do usuario
	 * @param unknown $curso
	 *        	 curso do usuario
	 * @param unknown $telefone
	 *        	 telefone do usuario
	 * @param unknown $endereco
	 *        	 endereço do usuario, normalmente é um bairro
	 * @param unknown $link_foto
	 *        	 link que da acesso a foto do usuario
	 * @param unknown $idFacebook
	 *        	 id do facebook, caso ele tenha sincronizado com facebook
	 * @return User, caso verdadeiro | false, caso acontece alguma coisa de errado;
	 * @access public
	 * @version 1.0
	 * @author Mauricio Santana  |  Luiz Felipe Evaristo
	 * @category Usuario
	 * @namespace Usuario
	 */
	public static function insertUser($nome,$senha, $email, $curso, $telefone, $endereco, $link_foto, $idFacebook) {
		$con = Connection::open ('mysql');

		$cryptSenha = User::hash_pass($senha);
		
		$bd = $con->prepare( 'INSERT INTO USUARIO (NOME_USER, SENHA, EMAIL,CURSO, TELEFONE, ENDERECO, LINK_FOTO, ID_FACEBOOK) VALUES (?,?,?,?,?,?,?,?)' );
		$bd->bindParam ( 1, $nome );
		$bd->bindParam ( 2, $cryptSenha);
		$bd->bindParam ( 3, $email );
		$bd->bindParam ( 4, $curso );
		$bd->bindParam ( 5, $telefone );
		$bd->bindParam ( 6, $endereco );
		$bd->bindParam ( 7, $link_foto );
		$bd->bindParam ( 8, $idFacebook );
		
		if ($bd->execute ()) {
			$bd2 = $con->prepare ( 'SELECT ID_USUARIO FROM USUARIO WHERE EMAIL = ?;' );
			$bd2->bindParam ( 1, $email );
			if ($bd2->execute ()) {
				if ($bd2->rowCount () > 0) {
					$row = $bd2->fetch ( PDO::FETCH_OBJ );
					return new User ( $row->ID_USUARIO, $nome, $email, $curso, $telefone, $endereco, $link_foto, $idFacebook );
				} else { // if rowCount()
					return NULL;
				}
			} else { // if execute(SELECT)
				return NULL;
			}
		} else { // if execute(INSERT)
			echo "Falha";
			return NULL;
		}
	}
	
	/**
	 * <b>Verifica se o email existe está cadastrado no Banco de Dados.<\b>
	 * 
	 * @param unknown $email
	 * @return ID do Usuario | NULL caso não exista
	 * @access public
	 * @version 1.0
	 * @author Mauricio Santana  |  Luiz Felipe Evaristo
	 * @category Usuario
	 * @namespace Usuario
	 */
	public static function verificaEmail($email){		
		$con = Connection::open('mysql');
		$bd = $con->prepare("SELECT id_usuario FROM USUARIO WHERE email = ?;");
		$bd->bindParam(1, $email);
		if($bd->execute()){
			if($bd->rowCount() > 0){
				$row = $bd->fetch(PDO::FETCH_OBJ);
				return $row->id_usuario;
			}else{ //If rowCount()
				return NULL;
			}
		}else{ //IF execute()
			return NULL;
		}
		return NULL;		
	}

	public static function verificaUser($email, $senha){
		$con = Connection::open('mysql');
		$bd = $con->prepare("SELECT email, senha, id_usuario FROM USUARIO WHERE email = ?");
		$bd->bindParam(1, $email);
		$bd->execute();
		
		if($bd->rowCount() > 0){
			$row = $bd->fetch(PDO::FETCH_OBJ);
			if(crypt($senha, $row->senha) == $row->senha){
				return User::getUserFromId($row->id_usuario);
			}else{// IF CRYPT   | SENHA ERRADA
				return FALSE;
			}
		}else{// IF ROW COUNT
			return FALSE;
		}
		
	}
	
	/*
	 *  -------- Override Functions AND GETs ---------------
	 */
	function __toString(){
		return "Nome: <b>{$this->nome}</b>
		<br>Email: <b>{$this->email}</b>
		<br>Curso: <b>{$this->curso}</b>
		<br>Endereco: <b>{$this->endereco}</b>
		<br>ID: <b>{$this->idUser}</b>
		<br>ID Facebook: <b>{$this->idFacebook}</b>
		<br>Foto perfil: <b>{$this->link_foto}</b>";
	}
	
	function getNome(){return $this->nome;}
	
	function getEmail(){return $this->email;}
	
	function getIdUser(){return $this->idUser;}
	
	function getCurso(){return $this->curso;}
	
	function getTelefone(){return $this->telefone;}
	
	function getEndereco(){return $this->endereco;}
	
	function getLinkFoto(){return $this->link_foto;}
	
	function getIdFacebook(){return $this->idFacebook;}
}
	