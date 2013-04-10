<?php
namespace exceptions;
/**
 * Classe de exceзгo para substituir os trigger errors do PHP.
 * 
 * @author  Andrй Mendonзa 
 * @version 1.0
 * @since   15/03/2006
 * @see 
 */

class PHPException extends \Exception 
{
    
    
    /**
     * Construtor que sobrescreve o da superclasse
     *
     * @param int       $code       Cуdigo de erro usado pelas constantes de 
     * erro do PHP
     * @param string    $message    Mensagem de erro do trigger
     * @param string    $file       Arquivo que disparou o erro
     * @param int       $line       Linha do arquivo que ocorreu o erro
     */
    public function __construct($code, $message, $file, $line)
    {
        parent::__construct($message,$code);
        
        /**
         * Sobrescreve os atributos file, line da classe Exception para que nгo
         * acuse o erro no mйtodo ErrorException::throwError() aonde serб jogada
         * a Exception.
         */
        $this->file = $file;
        $this->line = $line;
    }
    
    
    /**
     * Usado para jogar uma nova exceзгo pelo set_error_handler().
     *
     * @param int       $code
     * @param string    $message
     * @param string    $file
     * @param int       $line
     * 
     * @throws    ErrorException
     * @static 
     */
    static public function throwError($code, $message, $file, $line)
    {
    	if(strstr($file,"autoload.php") === false){
   	    	throw new PHPException($code, $message, $file, $line);
    	}
    }
    
    
}

?>