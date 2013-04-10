<?php
namespace lib;
class Functions{
	
	public static function setObjectFromArray($object,$array){
		foreach($array as $k => $v){
			$str = str_replace("_"," ",$k);
			$str = ucwords($str);
			$str = str_replace(" ","",$str);
			$f = 's';
			$$f = 'set'.$str;
			if(method_exists($object, $s)){
				$object->$s($v);
			}
		}
		return $object;
	}
	
	public static function getObjectAsArray($object){
		$campos = $object->getCamposTabela();
		foreach($campos as $v){
			$f = 'g';
			$$f = 'get'.ucfirst($v);
			if(method_exists($object, $g)){
				$array[$v] = $object->$g();
			}
		}
		return $array;
	}
	
	public static function dateToMysql($data){
		return implode('-',array_reverse(explode("/",$data)));
	}
	public static function is_mobile(){

            $mobile_browser = '0';

            if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
                $mobile_browser++;
            }

            if((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml')>0) or
            ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
                $mobile_browser++;
            }

            $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
            $mobile_agents = array(
                'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
                'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
                'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
                'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
                'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
                'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
                'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
                'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
                'wapr','webc','winw','winw','xda','xda-');

            if(in_array($mobile_ua,$mobile_agents)) {
                $mobile_browser++;
            }

            if (strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini')>0) {
                $mobile_browser++;
            }

            if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'windows')>0) {
                $mobile_browser=0;
            }

            return (bool) $mobile_browser > 0;

    }
    
	public static function sendMail($corpo ,$assunto ,array $destinatarios ,$CC = array(),$CCO = array(),$remetente = null,$anexo = null){
		$mail = new PHPMailer(true);
		$mail->SMTPDebug = 0;
		$mail->setFrom(MAIL_FROM);
		$mail->Host = MAIL_SMTP_HOST;
		$mail->Port = MAIL_SMTP_PORT;
		if(is_null($remetente)){
			$mail->setFrom(MAIL_FROM,MAIL_FROM_NAME);
		}else{
			$mail->setFrom($remetente);
		}
		if(!is_null($anexo)){
			$mail->AddAttachment($anexo);
		}
		$mail->Subject = $assunto;
		$mail->Body = $corpo;
		foreach($destinatarios as $email){
			$mail->AddAddress($email);
		}
		$mail->Send();
	}
	
	/**
	 * Fun��o pra tirar pontos e barras e tra�os de cnpjs e cpfs
	 *
	 */
	public static function soNumeros($s) {
		return preg_replace("/[^0-9]/",'',$s);
	}
	
	/**
	 * Fun��o para validar o cpf
	 * retorna true ou false
	 */
	public static function validaCPF($s){
		$s = Functions::soNumeros($s);
	
		//verifica se tem mais que 11 catacteres, quando a merda � cnpj as vezes valida!
		if(strlen($s)>11) return false;
	
		//verifica se os digitos s�o iguais, por incrivel que parece isso � v�lido!
		$iguais = true;
		for ($i=1; $i< strlen($s); $i++){
			if (substr($s, $i, 1) != substr($s, ($i-1), 1)) $iguais = false;
		}
		if ($iguais) return false;
	
		$c = substr($s, 0, 9);
		$dv = substr($s, 9, 2);
		$d1 = 0;
	
		for ($i=0; $i<9; $i++) {
			$d1+= substr($c, $i, 1)*(10-$i);
		}
		if ($d1 == false) return false;
		$d1 = 11 - ($d1 % 11);
		if ($d1 > 9) $d1 = 0;
		if(substr($dv, 0, 1) != $d1) return false;
	
		$d1 *= 2;
		for ($i=0; $i<9; $i++) {
			$d1 += substr($c, $i, 1)*(11-$i);
		}
		$d1 = 11 - ($d1 % 11);
		if ($d1 > 9) $d1 = 0;
		if(substr($dv, 1, 1) != $d1) return false;
	
		return true;
	}
	
	/**
	 * Fun��o para validar o CNPJ
	 * retorna true ou false
	 */
	public static function validaCNPJ($cnpj) {
		$cnpj = str_replace('.','',$cnpj);
		$cnpj = str_replace('/','',$cnpj);
		$cnpj = str_replace('-','',$cnpj);
	
		//verifica se os digitos s�o iguais, por incrivel que parece quando � tudo 0 isso � v�lido!
		$iguais = true;
		for ($i=1; $i< strlen($cnpj); $i++){
			if (subzstr($cnpj, $i, 1) != substr($cnpj, ($i-1), 1)) $iguais = false;
		}
		if ($iguais) return false;
	
		$a = array();
		$b = 0;
		$c = array(6,5,4,3,2,9,8,7,6,5,4,3,2);
		$x = 0;
		for($i=0; $i<12; $i++){
			$a[$i] = substr($cnpj, $i, 1);
			$b += $a[$i] * $c[$i+1];
		}
	
		if (($x = $b % 11) < 2) {
			$a[12] = 0;
		} else {
			$a[12] = 11 - $x;
		}
	
		$b = 0;
		$x = 0;
		for($y=0; $y<13; $y++) {
			$b += ($a[$y] * $c[$y]);
		}
		if (($x = $b % 11) < 2) {
			$a[13] = 0;
		} else {
			$a[13] = 11-$x;
		}
	
		if ((substr($cnpj, 12, 1) != $a[12]) || (substr($cnpj, 13, 1) != $a[13])){
			return false;
		} else {
			return true;
		}
	}
	
	public static function toJson(array $array){
		$dados = array();
		foreach($array as $key => &$value){
			$dados[] .= '"'.$key.'":"'.$value.'"';
		}
		$return = "{ ";
		$return.= implode(",",$dados);
		$return.= " }";
		return $return;
	}
	
	public static function buscaCEP($cep){
	    $cep = preg_replace("/[^0-9]/",'',$cep);
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL,'http://cep.republicavirtual.com.br/web_cep.php?cep='.$cep);
	    curl_setopt($ch, CURLOPT_FAILONERROR,1);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	    $retValue = curl_exec($ch);
	    curl_close($ch);
	    $xml = new \SimpleXMLElement($retValue);
	    $rua=$xml->tipo_logradouro." ".$xml->logradouro;
	    $rua = ($rua == ' ') ? "" : $rua;
	    $bairro=$xml->bairro;
	    $cidade=$xml->cidade;
	    $uf=$xml->uf;
	    return '{"endereco":"'.utf8_decode($rua).'","bairro":"'.utf8_decode($bairro).'","cidade":"'.utf8_decode($cidade).'","uf":"'.utf8_decode($uf).'"}';
	}
	
	public static function CalculaDigitoMod11($numero){
         $base = 9;
         $result = 0;
         $sum = 0;
         $factor = 2;
         
         for ($i = strlen($numero); $i > 0; $i--) {
             $numbers[$i] = substr($numero,$i-1,1);
             $partial[$i] = $numbers[$i] * $factor;
             $sum += $partial[$i];
             if ($factor == $base) {
                 $factor = 1;
             }
             $factor++;
         }
         
         if ($result == 0) {
             $sum *= 10;
             $digit = $sum % 11;
             if ($digit == 10) {
             $digit = 0;
             }
             return $digit;
         } elseif ($result == 1){
             $rest = $sum % 11;
             return $rest;
         }
	}
}