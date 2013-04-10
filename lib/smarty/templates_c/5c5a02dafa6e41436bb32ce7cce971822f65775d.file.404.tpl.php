<?php /* Smarty version Smarty-3.1.12, created on 2013-03-01 19:01:22
         compiled from "/var/www/projetos/ge/html/adm/template/404.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15390505513125335f71e0-03603817%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5c5a02dafa6e41436bb32ce7cce971822f65775d' => 
    array (
      0 => '/var/www/projetos/ge/html/adm/template/404.tpl',
      1 => 1361802659,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15390505513125335f71e0-03603817',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5131253362ced1_32184785',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5131253362ced1_32184785')) {function content_5131253362ced1_32184785($_smarty_tpl) {?>
<?php  $_config = new Smarty_Internal_Config("config_smarty.conf", $_smarty_tpl->smarty, $_smarty_tpl);$_config->loadConfigVars("setup", 'local'); ?>
<?php echo $_smarty_tpl->getSubTemplate ("estrutura/template_start.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="span12">
	<div class="hero-unit text-center">
		<div class='row'>
		  <h1 class='span11'>Oops!<br><br> =/</h1>
		</div>
		<div class='row'>
		  <p class='span11'>Não encontramos a página que você tentou acessar</p>
		</div>
		<div class='row'>
		  <p class='span11'>
		  <br>
		    <a class="btn btn-primary btn-large" href="<?php echo $_smarty_tpl->getConfigVariable('pasta_raiz');?>
">
		    <i class="icon-home icon-white"></i>
		      Página Inicial
		    </a>
		  </p>
		</div>
	</div>
</div>

<?php echo $_smarty_tpl->getSubTemplate ("estrutura/template_end.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>