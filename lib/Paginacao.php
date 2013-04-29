<?php

/*
 *
 * LG Framework v1 [classe de paginação]
 *
 * @author Ivan Ferrer - ivanbyferrer@gmail.com
 * @package LGFramework
 * @version v1
 *
*/
class Paginacao {
  public $limit;
  public $proxima;
	public $anterior;
	public $NumeroPaginaAtual;
	public $urlPagina;
	public $urlSite;
	public $limitListaPaginas;


    public function setLimitPorPagina($limit = null){
		$this->limit = ($limit) ? $limit : 3;
		}

	public function setProxima($next = null){
		$this->proxima = ($next) ? $next : 'Pr&oacute;xima &raquo;';
		}	
	public function setAnterior($before = null){
		$this->anterior = ($before) ? $before : '&laquo; Anterior';
		}		
	public function setLinkPagina($urlPagina='pg'){
		$this->urlPagina = ($urlPagina) ? $urlPagina : 'pg';
		}
		public function setUrl($url=null){
		$this->urlSite = $url;
		}	
	public function setlimitListaPaginas($limitPerPage = null){
		$this->limitListaPaginas = ($limitPerPage) ? $limitPerPage : 4;
		}	
	public function getInicio($NumeroPaginaAtual=1,$limit=1){
		//echo 'pagina='.$pagina.' - max='.$max;
		$this->NumeroPaginaAtual = $NumeroPaginaAtual;
		$this->limit = $limit;
		$inicio = $this->NumeroPaginaAtual - 1;
		$inicio = $this->limit * $inicio;
		return $inicio;
	}
	
	public function getPaginacao($addpage='',$RegistrosTotais=null,$pagina=null){
	$this->NumeroPaginaAtual = ($pagina) ? $pagina : $this->NumeroPaginaAtual;

			$html= "<div align=\"center\" id=\"paginacao\">";
			// Calculando pagina anterior
			$menos = $this->NumeroPaginaAtual - 1;
			// Calculando pagina posterior
			$mais = $this->NumeroPaginaAtual + 1;
			$pgs = ceil($RegistrosTotais / $this->limit);
			
			if($pgs > 1 ) 
			{
				if($menos >0) 
				$html.= "<a href=\"{$addpage}/".$this->linkPagina."/{$menos}\" class=\"navegacao\">".$this->anterior."</a> "; 
				
				if (($this->NumeroPaginaAtual-$this->limitListaPaginas ) < 1 )
				$anterior = 1;
				else 
				$anterior = $this->NumeroPaginaAtual - $this->limitListaPaginas;
				
				if (($pagina+$this->limitListaPaginas ) > $pgs )
				$posterior = $pgs;
				else
				$posterior = $this->NumeroPaginaAtual + $this->limitListaPaginas;
				
				for($i=$anterior;$i <= $posterior; $i++) {
						if($i != $this->NumeroPaginaAtual) 
						$html.= " <a href=\"{$addpage}/".$this->urlPagina."/{$i}\" class=\"navegacao\">$i</a>";
						else
						$html.= " <span class=\"atual\">$i</span>";
				   }
				if($mais <= $pgs) 
				$html.= " <a href=\"{$addpage}/".$this->urlPagina."/{$mais}\" class=\"navegacao\">".$this->proxima."</a>";
			}
			$html.= "</div>";
			return $html;
}
}
?>
