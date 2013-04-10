<?php /* Smarty version Smarty-3.1.12, created on 2013-03-01 18:52:11
         compiled from "/var/www/projetos/ge/html/template/404.tpl" */ ?>
<?php /*%%SmartyHeaderCode:11905528215131230c1518a5-26147672%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6ffaa0695992d15581c50c58b3f6ef97d084a1d4' => 
    array (
      0 => '/var/www/projetos/ge/html/template/404.tpl',
      1 => 1361802659,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11905528215131230c1518a5-26147672',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5131230c189eb7_93069216',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5131230c189eb7_93069216')) {function content_5131230c189eb7_93069216($_smarty_tpl) {?>
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