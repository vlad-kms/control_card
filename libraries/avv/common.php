<?php

class AvvCommon{

	public static function deleteBOM($text) {
		$bom = pack('H*','EFBBBF');
		$res = preg_replace("/^$bom/", '', $text);
		return $res;
	}
	
	public static function getOvoSecurityWord($params){
		$result ='';
		if ( isset($params) ) {
			$result = $params['SecurityWord'];
		}
		if (!isset($result) || ($result =='') ) {
			$result = $_SERVER['OVO_SECURITYWORD'];
		}
		return (string)$result;
	}
}
?>