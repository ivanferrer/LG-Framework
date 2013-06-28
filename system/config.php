<?php
// DEFINE Nome do Projeto
    $config_app = APP_DIR.LGF_SUBAPP.DS."config".DS."config.php";
    $config_app = str_replace("//","/",$config_app);
    include_once($config_app);
    define('LGF_VERSAO','v1.3.1');
    date_default_timezone_set('America/Sao_Paulo');
    
    LGF_definirConstante("PROJETO_NOME","LG Framwork",$_PROJETO_NOME);
// DEFINE Controller e o M�todo que receber� as requisi��es do dom�nio "cru", ex: www.dominio.com.br
    LGF_definirConstante("VIEW_PADRAO","Home",$_VIEW_PADRAO);
    LGF_definirConstante("METODO_PADRAO","index",$_METODO_PADRAO);
// DEFINE Controller e o M�todo que exibir� a p�gina de Login. O Controller deve herdar a classe P�gina
    LGF_definirConstante("VIEW_LOGIN","Usuario",$_VIEW_LOGIN);
    LGF_definirConstante("METODO_LOGIN","login",$_METODO_LOGIN);
// DEFINE Controller respons�vel por autenticar o usuario no sistema, este deve herdar a classe Autenticador
    LGF_definirConstante("CONTROLLER_LOGIN","controller\\Login","controller\\".$_CONTROLLER_LOGIN);
// DEFINE Link de Logout
    LGF_definirConstante("LINK_LOGOUT",false,HTTP_PATH.LGF_SUBAPP."/".'c/'.$_CONTROLLER_LOGIN.'/logout');
// DEFINE template que ser� carregado se n�o houver um explicitamente definido, deve ser o nomedoarquivo.tpl;
    LGF_definirConstante("TEMPLATE_PADRAO",'index.tpl',$_TEMPLATE_PADRAO);
// DEFINE se o padr�o para autentica��o de acesso � exigida ou n�o (true/false)
// esta op��o pode ser sobreescrita para p�ginas individuais no Controller
    LGF_definirConstante("AUTENTICACAO_PADRAO",true,$_AUTENTICACAO_PADRAO);
// DEFINE C�digo do Google Analytics
    LGF_definirConstante("GOOGLE_ANALYTICS","",$_GOOGLE_ANALYTICS);
// DEFINE Se exceptions devem ser tratadas com ou sem modo de debug;
    LGF_definirConstante("DEBUG_LIGADO",true,$_DEBUG_LIGADO);
// DEFINE Se exceptions devem ser tratadas com ou sem modo de debug;
    LGF_definirConstante("LGF_AMBIENTE_PRODUCAO",true,$_AMBIENTE_PRODUCAO);
// DEFINE Se p�gina de setup est� habilitada ou n�o;
    LGF_definirConstante("HABILITA_SETUP",false,$_HABILITA_SETUP);
    

    function LGF_definirConstante($nome,$valorPadrao,$valorDefinido){
        if(!is_null($valorDefinido)){
            define($nome,$valorDefinido);
        }else{
            define($nome,$valorPadrao);
        }
    }
?>
