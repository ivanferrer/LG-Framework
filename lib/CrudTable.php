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
class CrudTable {

    private $objeto;
    private $objetoNome;
    private $classeTable;
    private $campos;
    private $valores;
    private $camposExibir;
    private $id;
    private $idField;
    private $inputLabel;
    private $inputOff;
    private $relacionamentos;
    
    
    public function __construct() {
        
    }
    
    public function setInputLabel($campo,$label){
        $this->inputLabel[$campo] = $label;
    }
    
    public function setInputOff($campo){
        $this->inputOff[] = $campo;
    }
    
    
    public function setList(\SplObjectStorage $lista){
        $lista->rewind();
        $objetoEx = $lista->current();
        $this->relacionamentos = $objetoEx->getChaveRelacionamentos();
        $nome = get_class($objetoEx);
        $this->id = str_replace("\\","_",$nome);
        $nome = explode('\\', $nome);
        $this->objetoNome = $nome[1]; 
        $lista->rewind();
        $_ref = new \ReflectionClass(get_class($objetoEx));
        $_ret = $_ref->getProperties();
        foreach($lista as $linha => $valor){
            $pk = $valor->getChavePrimaria();
            $pk = "get".str_replace(" ","",ucwords(str_replace("_"," ",$pk)));
            foreach($_ret as $key => $_attributo){
                $call = 'get'.str_replace(" ","",ucwords(str_replace("_"," ",$_attributo->name)));
                $this->valores[$valor->$pk()][$_attributo->name] = $valor->$call();
                
                if($linha == 0){
                    $this->campos[$_attributo->name] = $_attributo->name;
                }
            }
        }
        $lista->rewind();
        $obj = $lista->current();
        $this->idField = $obj->getChavePrimaria();
    }
    
    public function setClasse($classe){
        $this->classeTable = $classe;
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
    
    private function getHTMLModal($action){
        $body = array();
        switch($action){
            case 'excluir':
                $body[]= "Tem certeza que deseja excluir este registro?";
                foreach($this->campos as $key => $campo){
                    if($campo == $this->idField){
                        $body[] = "<input type='hidden' class='".$campo."' name='".$campo."' value=''/>";
                    }
                }
                break;
            case 'alterar':
            case 'inserir':
                foreach($this->campos as $key => $campo){
                    if($campo != $this->idField || $action == 'alterar'){
                        $label = null;
                        foreach($this->inputLabel as $cmp => $lbl){
                            if($campo == $cmp){
                                $label = $lbl; 
                            }
                        }
                        $label = (is_null($label)) ? ucwords(str_replace("_"," ",strstr($campo,"_"))) : $label;
                        $body[] = '<div class="control-group">';
                        $body[] = '<label class="control-label" for="'.$campo.'" >'. $label."</label>";
                        $body[] = "<div class='controls'><input type='text' id='$campo' class='$campo' name='$campo' value=''/></div>";
                        $body[] = '</div>';
                    }
                }
                
        };
        $ret = '
        <div id="'.$this->id."-".$action.'" class="modal modal-form hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" aria-hidden="true">
            <form class="form-horizontal" method="POST" action="'.HTTP_PATH."c/".$this->objetoNome."/".$action.'">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 id="myModalLabel3">'.ucfirst($action).' '.$this->objetoNome.'</h3>
                </div>
                <div class="modal-body">
                  '.implode("\n",$body).'
                </div>
                <div class="modal-footer form-actions">
                    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
                    <button class="btn btn-primary" type="submit">'.ucfirst($action).'</button>
                </div>
            </form>
        </div>';
        return $ret;
    } 
    
    public function getHTMLTable(){
        $_return = "<table class='".$this->classeTable."' id='".$this->id."'><thead><tr>";
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
        $_return.= "<th>Ação</th>";
        $_return.= "</tr>";
        $_return.= "</thead>";
        $_return.= "<tbody>";
        foreach($this->valores as $valor){
            $_return.= "<tr>";
            foreach($this->campos as $key => $campo){
                if(in_array($campo,$this->camposExibir) || array_key_exists($campo, $this->camposExibir)){
                    $_return.="<td>".$valor[$key]."</td>";
                }else{
                    
                }
            }
            $_return.='
    		<td id="'.$this->id.'-'.$valor[$this->idField].'">
    		    <a href="javascript:ajax_crud_alterar_call('.$valor[$this->idField].')" role="button">
    		        <img class="icon" src="'.HTTP_FULL_PATH.'template/images/icon-edit.png"/>
    	        </a>
    		    <a href="javascript:ajax_crud_excluir_call('.$valor[$this->idField].')" role="button">
    		        <img class="icon" src="'.HTTP_FULL_PATH.'template/images/icon-delete.png"/>
    	        </a>
    		    <!--<a href="javascript:ajax_crud_configurar_call()" role="button">
    		        <img class="icon" src="'.HTTP_FULL_PATH.'template/images/icon-list.png"/>
    	        </a>!-->
            </td>';
            $_return.= "</tr>";
        }
        $_return.="";
        $_return.= "</tbody>";
        $_return.= "</table>";
        $_return.= "<script>var controller = '$this->id'; var classeNome = '$this->objetoNome'</script>";
        $_return.= "<script> var model_dados = ".Functions::toJson($this->valores)."</script>";
        $_return.= $this->getHTMLModal("inserir");
        $_return.= $this->getHTMLModal("excluir");
        $_return.= $this->getHTMLModal("alterar");
        return $_return;
    }
                            
}

?>