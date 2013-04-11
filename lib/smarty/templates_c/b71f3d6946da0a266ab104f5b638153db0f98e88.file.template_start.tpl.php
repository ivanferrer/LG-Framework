<?php /* Smarty version Smarty-3.1.12, created on 2013-03-01 18:52:11
         compiled from "/var/www/projetos/ge/html/template/estrutura/template_start.tpl" */ ?>
<?php /*%%SmartyHeaderCode:8529077055131230c18f778-18823293%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b71f3d6946da0a266ab104f5b638153db0f98e88' => 
    array (
      0 => '/var/www/projetos/ge/html/template/estrutura/template_start.tpl',
      1 => 1361755627,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8529077055131230c18f778-18823293',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5131230c1c7f82_57595900',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5131230c1c7f82_57595900')) {function content_5131230c1c7f82_57595900($_smarty_tpl) {?>
<?php if ($_SERVER['HTTP_CONTENT']!="true"){?>
	<?php echo $_smarty_tpl->getSubTemplate ("estrutura/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	
	<div id="conteudo" class='container'>
		<div class="row">
		<?php echo $_smarty_tpl->getSubTemplate ("estrutura/menu_topo.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		<?php echo $_smarty_tpl->getSubTemplate ("estrutura/menu_lateral.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php }?><?php }} ?>