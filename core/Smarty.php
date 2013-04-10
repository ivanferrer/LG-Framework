<?php
namespace core;

include_once(LGF_PATH."lib/smarty/Smarty.class.php");

class Smarty extends \Smarty {

	public function __construct(){
			parent::__construct();
			$this->setTemplateDir(APP_DIR."template");
			$this->setCacheDir(APP_DIR."lib".DS."smarty".DS."cache");
			$this->setCompileDir(APP_DIR."lib".DS."smarty".DS.'templates_c');
			$this->setConfigDir(APP_DIR . "config");
			$this->assign("linkLogout",LINK_LOGOUT);
			//Erros de compile do Smarty não são exibidos, pois já tem tratativa pela própria biblioteca
			$this->muteExpectedErrors();
	}
		    
}
