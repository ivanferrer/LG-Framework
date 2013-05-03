<?php
namespace core;
class Setup extends DAO {
	
	private $smarty;
	private $arquivo;
	
	public function __construct(){
		parent::__construct();
		$this->con = parent::getConnection();
		$this->smarty = new Smarty();
		$this->smarty->setTemplateDir(APP_DIR."setup");
		$this->smarty->setCacheDir(APP_DIR."lib".DS."smarty".DS."cache");
		$this->smarty->setCompileDir(APP_DIR."lib".DS."smarty".DS.'templates_c');
		$this->smarty->setConfigDir(APP_DIR . "setup");
		$this->smarty->assign("link_raiz",HTTP_PATH."setup");
		//Erros de compile do Smarty não são exibidos, pois já tem tratativa pela própria biblioteca
		$this->smarty->muteExpectedErrors();
	}
	
	public function montarFormulario(){
		$form = "Esta página ira gerar os itens selecionados para cada tabela do banco de dados '".LGF_BD_NOME."'.<br>
				<span class='text-warning'> Os arquivos que já existem serão substituídos.</span><br><br>";
		$form.= '<form method=POST><p>';
		$form.= '<select style="height:400px;width:300px;" name="tabelas[]" multiple="multiple">';
		$result = $this->con->query('show tables');
		while($row = $result->fetch(\PDO::FETCH_ASSOC)){
			  $form .= '<option>'.$row['Tables_in_'.LGF_BD_NOME].'</option>';
		}
		$form.= '
				</select>
		        
				</p>
		        <p>
		            Tipos de arquivos:<br>
    		        <label class="checkbox inline">
    		            <input type="checkbox" name="dao" value="true"/>
    		            DAO
                    </label>
    		        <label class="checkbox inline">
    		            <input type="checkbox" name="model" value="true"/>
    		            Model
                    </label>
    		        <label class="checkbox inline">
    		            <input type="checkbox" name="controller" value="true" disabled/>
                        Controller
                    </label>
		        </p>
				<p>
		<button class="btn btn-primary" type="submit" name="gerar"/>Gerar Arquivos</button>
				</p>
	</form>';
		$this->smarty->assign('form',$form);
	}
	
	public function tratarAcao(){
		if(isset($_POST['gerar']) && count($_POST['tabelas']) > 0 && (isset($_POST['dao']) || isset($_POST['model']))){
			foreach($_POST['tabelas'] as $tabela){
				//echo '<textarea style="width: 800px;height:600px">'.$this->gerarDAO($tabela).'</textarea>';
				if(isset($_POST['model'])){
    				$conteudo = $this->gerarModelo($tabela);
    				$this->salvarArquivo(APP_DIR.'model'.DS.$this->classeFromTabela($tabela).".php",$conteudo);
				}if(isset($_POST['dao'])){
    				$conteudo = $this->gerarDAO($tabela);
    				$this->salvarArquivo(APP_DIR.'dao'.DS.$this->classeFromTabela($tabela).".php",$conteudo);
				}
			}
			$mensagem = 'Arquivos gerados com sucesso!';
			$classeAlerta = 'alert-success';
		}elseif(isset($_POST['gerar']) && !isset($_POST['dao']) && !isset($_POST['model'])){
			$mensagem = 'Escolha os tipos de arquivos que devem ser gerados.';
			$classeAlerta = 'alert';
		}elseif(isset($_POST['gerar']) && count($_POST['tabelas']) == 0){
			$mensagem = 'Não foram gerados arquivos.';
			$classeAlerta = 'alert-error';
		}
		$this->smarty->assign('mensagem',$mensagem);
		$this->smarty->assign('classe',$classeAlerta);
	}

	private function classeFromTabela($tabela){
		$tabela = str_replace("_"," ",$tabela);
		return str_replace(" ","",ucwords($tabela));
	}
	
	private function attrFromCampo($campo){
		$campo = str_replace("_"," ",$campo);
		$campo = str_replace(" ","",ucwords($campo));
		return lcfirst($campo);
	}
	
	private function salvarArquivo($caminho,$conteudo){
		$pasta  = substr($caminho, 0, strrpos($caminho, DS));
		try{@mkdir($pasta);chmod($pasta, 0777);}catch(\Exception $e){}
		file_put_contents($caminho, $conteudo);
		chmod($caminho, 0777);
	}
	
	private function gerarModelo($tabela){
		$modelo = $this->gerarHeader();
		$modelo .="
namespace model;
use \core as c;
use \exceptions as e;
use \lib as l;
class ".$this->classeFromTabela($tabela)." extends c\Modelo{
";
		$result = $this->con->query('describe '.$tabela);
		$constraints = $this->con->query("select * from INFORMATION_SCHEMA.TABLE_CONSTRAINTS where CONSTRAINT_SCHEMA = '".LGF_BD_NOME."' and TABLE_NAME = '$tabela'");
		$constraints = $constraints->fetchAll();
		while($linha = $result->fetch(\PDO::FETCH_ASSOC)){
			$encap = ($linha['Key'] == 'PRI' && $linha['Extra'] == 'auto_increment') ? "protected" : "private";
			$modelo .= "
		$encap $".$linha['Field'].';';
		}
		
		$modelo .= "

		public function __construct(){";

		$pk_auto = null;
		$fks = array();
		$pks = array();
		$uniques = array();
		$result = $this->con->query('describe '.$tabela);
		while($linha = $result->fetch(\PDO::FETCH_ASSOC)){
			$val = (is_int($linha['Default'])) ? $linha['Default'] : "'".$linha['Default']."'" ;
			$val = ($val == "''") ? "NULL" : $val;
			$val = ($linha['Key'] == 'PRI' && $linha['Extra'] == 'auto_increment') ? "0" : $val;
			if($linha['Extra'] == 'auto_increment'){
				$pk_auto = $linha['Field'];
			}
			if(strstr(strtolower($linha['Type']),'datetime') || strstr(strtolower($linha['Type']),'timestamp')){
				$val = "date('Y-m-d H:i:s')";
			}
			$modelo .='
			$this->'.$linha['Field']." = ".$val.';';

			if($linha['Key'] == 'PRI' || $linha['Key'] == 'MUL' || $linha['Key'] == 'UNI'){
			    foreach($constraints as $cnst){
			        if(strstr($cnst['CONSTRAINT_NAME'], $linha['Field'])){
			            switch($cnst['CONSTRAINT_TYPE']){
			                case 'UNIQUE':
			                    $uniques[] = "'".$linha['Field']."'=>'".$cnst['CONSTRAINT_NAME']."'";
			                    break;
			                case 'FOREIGN KEY':
			                    $fks[] = "'".$linha['Field']."'=>'".$cnst['CONSTRAINT_NAME']."'";
			                    break;
			            }
			        }
			    }
			}
			if($linha['Key'] == 'PRI'){
			    $pks[] = "'".$linha['Field']."'";
			};
		}
		$modelo .="
			parent::__construct(".implode(",", $pks).",'$tabela',array(".implode(",", $fks)."),array(".implode(",", $uniques)."));
		}
";

		$result = $this->con->query('describe '.$tabela);
		while($linha = $result->fetch(\PDO::FETCH_ASSOC)){
			$modelo .= $this->gerarSetter($linha);
			$modelo .= $this->gerarGetter($linha);
		}
		$modelo .='
}';
		
		return $modelo;
	}
	
	private function gerarDAO($tabela){
		$modelo = $this->gerarHeader();
		$modelo .='
namespace dao;
use \core as c;
use \model as m;
use \exceptions as e;
use \lib as l;
class '.$this->classeFromTabela($tabela).' extends c\DAO{
		
		private $modelo;
				
		public function __construct(m\\'.$this->classeFromTabela($tabela).' $'.$this->classeFromTabela($tabela).' = null){
			parent::__construct();
			$this->modelo = (is_null($'.$this->classeFromTabela($tabela).')) ? new m\\'.$this->classeFromTabela($tabela).' : $'.$this->classeFromTabela($tabela).';
		}
					
}
';
		return $modelo;
	}
	
	private function gerarController($tabela){
		
	}
	
	private function gerarHeader(){
        $ret = '<?'."php
/*
 *
 * LG Framework ".LGF_VERSAO."
 *
 * @author Luiz Guilherme - luizguilherme00@hotmail.com
 * @package LGFramework
 * @version ".LGF_VERSAO."
 *
*/
";
        return $ret;
	}
	
	private function gerarGetter($describeCampo){
		switch($describeCampo['Type']){
			case 'timestamp':
			case 'datetime':
				$param = '$formato = null';
				$codigo = '			if($formato == "brasil"){
				return date_format(date_create($this->'.$describeCampo['Field'].'), "d/m/Y H:i:s");
			}else{
				return $this->'.$describeCampo['Field'].';
			}';
				break;
			case 'date':
				$param = '$formato = null';
				$codigo = '			if($formato == "brasil"){
				return date_format(date_create($this->'.$describeCampo['Field'].'), "d/m/Y");
			}else{
				return $this->'.$describeCampo['Field'].';
			}';
				break;
			default:
				$param = '';
				$codigo = '			return $this->'.$describeCampo['Field'].';';
		}
		$return ="
		public function get".$this->classeFromTabela($describeCampo['Field']).'('.$param.'){
'.$codigo.'
		}
';
		return $return;
	}
	
	private function gerarSetter($describeCampo){
		$return ="
		public function set".$this->classeFromTabela($describeCampo['Field']).'($value){';
		$tamanho = $this->getTamanhoTipo($describeCampo['Type'],'tamanho');
		$tipo = $this->getTamanhoTipo($describeCampo['Type'],'tipo');
		$nome = (strstr($describeCampo['Field'],"_")) ? ucwords(str_replace("_"," ",strstr($describeCampo['Field'],"_"))) : ucfirst($describeCampo['Field']);
		$nome = trim($nome);
		if(!is_null($tamanho)){
			if ($tipo == 'enum'){
				$return.= '
			if(!in_array($value, array('.$tamanho.'))){';
				$mensagem = 'um destes valores: '.str_replace("'","", $tamanho);
			}elseif($tipo == 'double'){
			    $tamanho = explode(",", $tamanho);
			    $antVirg = $tamanho[0]; 
			    $depVirg = $tamanho[1];
				$return.= '
	        $value = str_replace(",", ".", $value);
			$valueCheck = explode(".", $value);
			if(strlen($valueCheck[0]) > '.$tamanho[0].' || strlen($valueCheck[1]) > '.$tamanho[1].'){';
				$mensagem = 'no máximo '.$tamanho[0].' inteiros e '.$tamanho[1].' decimais';
			}else{
				$return.= '
			if(strlen($value) > '.$tamanho.'){';
				$mensagem = 'no máximo '.$tamanho.' caracteres';
			}
				$return.='
				throw new e\ModeloException("O campo '.$nome.' deve conter '.$mensagem.'");
			}';
		}
		
		if($describeCampo['Null'] == 'NO'){
			$return.='
			if($value == "" || is_null($value)){
				throw new e\ModeloException("O campo '.$nome.' não deve estar vazio");
			}';
		}
		if($describeCampo['Type'] == 'date'){
			$return.='
		    $value = implode("-",array_reverse(explode("/",$value)));
		    ';
		}
		if($describeCampo['Type'] == 'datetime'){
			$return.='
			$datetime = implode(" ",$value);
		    $value = implode("-",array_reverse(explode("/",$datetime[0])))." ".$datetime[1];
		    ';
		}
		$return.='
			$this->'.$describeCampo['Field'].' = $value;';
		$return.="
		}
";
		return $return;
	}
	
	private function getTamanhoTipo($campoType,$tipo){
		if($tipo == 'tipo'){
			return substr($campoType,0,strpos($campoType, '('));
		}else{
			if(strstr($campoType,'(')){
				$a = strpos($campoType, '(')+1;
				$b = strpos($campoType, ')') - $a;
				return substr($campoType,$a,$b);
			}else{
				return null;
			}
		}
	}
	
	public function exibir(){
		$this->smarty->display('setup.tpl');
	}
	
}
