<?php /* Smarty version Smarty-3.1.12, created on 2013-03-01 18:52:11
         compiled from "/var/www/projetos/ge/html/template/login.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14283177785131230b9c45b6-04028373%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8f33afdfc6437dca23a485e483ff2bd3dc6ee466' => 
    array (
      0 => '/var/www/projetos/ge/html/template/login.tpl',
      1 => 1362155644,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14283177785131230b9c45b6-04028373',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5131230ba04a54_65627541',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5131230ba04a54_65627541')) {function content_5131230ba04a54_65627541($_smarty_tpl) {?>
<?php  $_config = new Smarty_Internal_Config("config_smarty.conf", $_smarty_tpl->smarty, $_smarty_tpl);$_config->loadConfigVars("setup", 'local'); ?>
	<?php echo $_smarty_tpl->getSubTemplate ("estrutura/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<style type="text/css">
      body {
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
      }

      .form-signin {
        max-width: 400px;
        padding: 19px 29px 29px;
        margin: 10% auto;
        background-color: #fff;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
           -moz-border-radius: 5px;
                border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
      }
      .form-signin .form-signin-heading,
      .form-signin .checkbox {
        margin-bottom: 10px;
      }
      .form-signin input[type="text"],
      .form-signin input[type="password"]{
        font-size: 16px;
        height: auto;
        margin-bottom: 15px;
      }
      .form-signin input:-webkit-autofill {
		background-color: white !important;
		}
    </style>
      <form class="form-signin" action="<?php echo $_smarty_tpl->getConfigVariable('pasta_raiz');?>
/c/Login/autenticar" method="POST">
		<fieldset>
        <h2 class="form-signin-heading">Por favor, faça login</h2>
			<!--[if IE]><label>E-mail</label><![endif]-->
			<input type="text"  class="input-block-level" name="email" placeholder="email@provedor.com">
			<br>
			<!--[if IE]><label class="control-label">Senha</label><![endif]-->
			<input type="password" class="input-block-level" name="senha" placeholder="senha">
	        <label class="checkbox">
	          <input type="checkbox" value="remember-me"> Lembrar
	        </label>
	        <button class="btn btn-large btn-primary" type="submit">Entrar</button>
	        <a href="<?php echo $_smarty_tpl->getConfigVariable('pasta_raiz');?>
"><button class="btn btn-link" type="button">voltar</button></a>
		</fieldset>
      </form>

    </div><?php }} ?>