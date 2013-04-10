<?php /* Smarty version Smarty-3.1.12, created on 2013-03-01 19:01:22
         compiled from "/var/www/projetos/ge/html/adm/template/estrutura/menu_topo.tpl" */ ?>
<?php /*%%SmartyHeaderCode:29581565151312533674a71-97690415%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '27fb350a149b5616cfeb996fcf23ebcfd0d54a7a' => 
    array (
      0 => '/var/www/projetos/ge/html/adm/template/estrutura/menu_topo.tpl',
      1 => 1362092441,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '29581565151312533674a71-97690415',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'linkLogout' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_51312533697a07_86857830',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51312533697a07_86857830')) {function content_51312533697a07_86857830($_smarty_tpl) {?><div id="menu-topo">
	<div class="menu">
		<div class="navbar navbar-inverse navbar-fixed-top">
		  <div class="navbar-inner">
			<div class='container'>
			   <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		      </a>
			    <ul class='nav pull-right'>
						<li><a href="<?php echo $_smarty_tpl->tpl_vars['linkLogout']->value;?>
">Sair</a></li>
			    </ul>
			    <a class="brand" href="<?php echo $_smarty_tpl->getConfigVariable('pasta_raiz');?>
">Painel Administrativo</a>
			    <div class="nav-collapse collapse">
			    	<ul class='nav'>
						<li class='dropdown'>
							<a class="dropdown-toggle" data-toggle="dropdown" href="<?php echo $_smarty_tpl->getConfigVariable('pasta_raiz');?>
/configuracoes">
							    <span class="caret"></span>
							    Configurações
							</a>
							<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
								<li><a href="<?php echo $_smarty_tpl->getConfigVariable('pasta_raiz');?>
/configuracoes/niveis-de-acesso">Níveis de Acesso</a></li>
								<li><a href="<?php echo $_smarty_tpl->getConfigVariable('pasta_raiz');?>
/configuracoes/usuarios">Usuários</a></li>
								<li><a href="<?php echo $_smarty_tpl->getConfigVariable('pasta_raiz');?>
/configuracoes/exibicao">Exibição</a></li>
  							</ul>
						</li>
						<li><a href="<?php echo $_smarty_tpl->getConfigVariable('pasta_raiz');?>
/dashboards">Dashboards</a></li>
						<li><a href="<?php echo $_smarty_tpl->getConfigVariable('pasta_raiz');?>
/dossies">Dossiês</a></li>
						<li><a href="<?php echo $_smarty_tpl->getConfigVariable('pasta_raiz');?>
/relatorios">Relatórios</a></li>
			    	</ul>
		    	</div>
			</div><!-- 
	<div id="saudacao">
		<p>
			<a class='link' href='<?php echo $_smarty_tpl->getConfigVariable('pasta_raiz');?>
/Usuario/inicio'><img src="<?php echo $_smarty_tpl->getConfigVariable('pasta_raiz');?>
/template/images/logo_menu.png" /></a>
			<span>| Olá <em> <?php echo $_SESSION['usrNome'];?>
</em> , seja bem-vindo ao Eu Vendo APC |</span>
		</p>
	</div> -->
		  </div>
		</div>
	</div>
</div><?php }} ?>