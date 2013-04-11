<?php
namespace core;
class ConnectionFactory{
	
	private $usuario;
	private $senha;
	private $servidor;
	private $baseDeDados;
	private static $con;
	
	/**
	 * @return \PDO Conexão com o Banco
	 */
	protected static function getConnection(){
		if (!ConnectionFactory::$con)
			new ConnectionFactory();
		return ConnectionFactory::$con;
	}
	
	private function __construct(){
		$this->servidor = LGF_BD_SERVIDOR;
		$this->usuario = LGF_BD_USUARIO;
		$this->senha = LGF_BD_SENHA;
		$this->baseDeDados = LGF_BD_NOME;
            ConnectionFactory::$con = new \PDO("mysql:host=".$this->servidor.";dbname=".$this->baseDeDados,
            		$this->usuario,
            		$this->senha,
      				array( \PDO::ATTR_PERSISTENT => false ) );
    		ConnectionFactory::$con->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION); // Set Errorhandling to Exception
    		ConnectionFactory::$con->beginTransaction();
    		
	}
}
?>