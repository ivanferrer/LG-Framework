<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class
 *
 * @author User
 */
class Form {
    
    private $action;
    private $method;
    private $titulo;
    private $nome;
    private $botaoNome;
    private $botao = array();
    private $fields = array();
    private $hidden = array();
    private $script = array();
    private $obrigatorios = array();
    private $confirmaSenha;
    private $atributo = array();
    private $tips = array();
    
    function __construct($nome, $titulo = '', $botao = NULL,$action='#',$method='POST') {
        $this->nome= $nome;
        $this->titulo= $titulo;
        $this->action = $action;
        $this->method = $method;
        $this->botaoNome = $botao;
        $this->confirmaSenha = false;
    }

    public function addBotao($nome,$valor = NULL, $attrib = NULL ){
        $valor = ($valor == NULL) ? $this->botaoNome : $valor;
        $this->botao[] = "<input name='$nome' id='$nome' type='button' value='$valor' $attrib ".'/'."> ";
    }
  
    public function addHidden($nome,$valor=''){
        $this->hidden[] = "<input name='$nome' id='$nome' type='hidden' value='$valor' ".'/'.">\n";
    }
    

    public static function fromObject($object,$campoId){
        $dao = new DAO();
        $ret = $dao->describe($object);
        foreach($ret as $describe){
            $describe->getField();
        }
    }
                            
}

?>