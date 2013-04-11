<?php
namespace exceptions;
/**
 * Classe de exce��o para substituir os trigger errors do PHP.
 * 
 * @author  Andr� Mendon�a 
 * @version 1.0
 * @since   15/03/2006
 * @see 
 */

class PHPException extends \Exception 
{
    
    
    /**
     * Construtor que sobrescreve o da superclasse
     *
     * @param int       $code       C�digo de erro usado pelas constantes de 
     * erro do PHP
     * @param string    $message    Mensagem de erro do trigger
     * @param string    $file       Arquivo que disparou o erro
     * @param int       $line       Linha do arquivo que ocorreu o erro
     */
    public function __construct($code, $message, $file, $line)
    {
        parent::__construct($message,$code);
        
        /**
         * Sobrescreve os atributos file, line da classe Exception para que n�o
         * acuse o erro no m�todo ErrorException::throwError() aonde ser� jogada
         * a Exception.
         */
        $this->file = $file;
        $this->line = $line;
    }
    
    
    /**
     * Usado para jogar uma nova exce��o pelo set_error_handler().
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