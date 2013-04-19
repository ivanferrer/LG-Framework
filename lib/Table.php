<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class
 *
 * @author Luiz Guilherme da Silva Junior
 */
namespace lib;
class Table {
    
    private $objeto;
    private $classe;
    private $campos;
    private $valores;
    private $camposExibir;
    private $id;
    
    
    public function __construct() {
        
    }
    
    public function setList(\SplObjectStorage $lista){
        $lista->rewind();
        $objetoEx = $lista->current();
        $nome = get_class($objetoEx);
        $this->id = str_replace("\\","_",$nome);
        $lista->rewind();
        $_ref = new \ReflectionClass(get_class($objetoEx));
        $_ret = $_ref->getProperties();
        foreach($lista as $linha => $valor){
            foreach($_ret as $key => $_attributo){
                $call = 'get'.str_replace(" ","",ucwords(str_replace("_"," ",$_attributo->name)));
                $this->valores[$linha][$_attributo->name] = $valor->$call();
                
                if($linha == 0){
                    $this->campos[$_attributo->name] = $_attributo->name;
                }
            }
        }
    }
    
    public function setArray(array $lista,$idDiv){
        $this->id = $idDiv;
        foreach($lista as $linha => $coluna){
            foreach($coluna as $campo => $valor){
                $this->valores[$linha][$campo] = $valor;
                if($linha == 0){
                    $this->campos[$campo] = $campo;
                }
            }
        }
        
    }
    
    public function getHTML(){
        $_return = "<table class='sortable' id='".$this->id."'><thead><tr>";
        foreach($this->campos as $nome){
            if(in_array($nome,$this->camposExibir) || array_key_exists($nome, $this->camposExibir)){
                if(array_key_exists($nome, $this->camposExibir)){
                    $label = $this->camposExibir[$nome];
                }else{
                    $label = (strstr($nome,"_")) ? ucwords(str_replace("_"," ",strstr($nome,"_"))) : $nome;
                }
                $_return.= "<th>".$label."</th>";
            }
        }
        $_return.= "</tr>";
        $_return.= "</thead>";
        $_return.= "<tbody>";
        foreach($this->valores as $valor){
            $_return.= "<tr>";
            foreach($this->campos as $key => $campo){
                if(in_array($campo,$this->camposExibir) || array_key_exists($campo, $this->camposExibir)){
                    $_return.="<td>".$valor[$key]."</td>";
                }
            }
            $_return.= "</tr>";
        }
        $_return.= "</tbody>";
        $_return.= "</table>";
        return $_return;
    }
    
    public function addLink($nomeColuna,$url,$campoParametroUrl,$imagemSrc = null){
        $this->campos[$nomeColuna] = $nomeColuna;
        $this->addCampoExibir($nomeColuna);
        $image = (is_null($imagemSrc)) ? $nomeColuna : "<img width=20 height=20 src='$imagemSrc' />";
        foreach($this->valores as &$valor){
            $html = "<a href='".$url.$valor[$campoParametroUrl]."'>".$image."</a>";
            $valor[$nomeColuna] = $html;
        }
        /*
        echo "<pre>";
        print_r($this->campos);
        print_r($this->camposExibir);
        print_r($this->valores);
        echo "</pre>";
       // */
    }
    
    public function addCampoExibir($campo){
        $this->camposExibir[] = $campo;
    }
    public function setCamposExibir(array $campos){
        $this->camposExibir = $campos;
    }
                            
}

?>