<?php /* Smarty version Smarty-3.1.12, created on 2013-03-01 19:01:22
         compiled from "/var/www/projetos/ge/html/adm/template/estrutura/template_start.tpl" */ ?>
<?php /*%%SmartyHeaderCode:180417172551312533632260-52939382%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4a31e8acd58d2ec40862df73ff999bc8c182bef5' => 
    array (
      0 => '/var/www/projetos/ge/html/adm/template/estrutura/template_start.tpl',
      1 => 1361755627,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '180417172551312533632260-52939382',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_51312533648501_82082260',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51312533648501_82082260')) {function content_51312533648501_82082260($_smarty_tpl) {?>
<?php if ($_SERVER['HTTP_CONTENT']!="true"){?>
	<?php echo $_smarty_tpl->getSubTemplate ("estrutura/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	
	<div id="conteudo" class='container'>
		<div class="row">
		<?php echo $_smarty_tpl->getSubTemplate ("estrutura/menu_topo.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		<?php echo $_smarty_tpl->getSubTemplate ("estrutura/menu_lateral.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php }?><?php }} ?>