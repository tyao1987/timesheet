<?php

namespace Admin\Util;

use Test\Data;
use Admin\Model\Auth;
use Admin\Model\User;
use Application\Model\DbTable;
class Util {

    protected static $cmsWritableDir;
    
    //protected static $ipRegex = '/^((25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|[1-9])\.){3}(25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|[1-9])$/is';
    protected static $ipRegex = '/^((25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d)))\.){3}(25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d)))$/is';
    protected static $excelDateRegex = '/^(0[1-9]|1[012])\/(0[1-9]|[12][0-9]|3[01])\/((?:19|20)\d\d)$/is';
    protected static $macRegex = '/^(([a-f0-9]{2}:){5}|([a-f0-9]{2}-){5})[a-f0-9]{2}$/is';
    protected static $gpsRegex = '/^(-)?\d+(\.\d+)?$/is';
    
    /**
	 * returns a randomly generated string
	 * commonly used for password generation
	 *
	 * @param int $length
	 * @return string
	 */
	static function random($length = 8) {
		// start with a blank string
		$string = "";

		// define possible characters
		$possible = "0123456789abcdfghjkmnpqrstvwxyz";

		// set up a counter
		$i = 0;

		// add random characters to $string until $length is reached
		while ( $i < $length ) {

			// pick a random character from the possible ones
			$char = substr ( $possible, mt_rand ( 0, strlen ( $possible ) - 1 ), 1 );

			// we don't want this character if it's already in the string
			if (! strstr ( $string, $char )) {
				$string .= $char;
				$i ++;
			}
		}

		return $string;
	}
	static function getCmsWritableDir($name) {

	    if (isset(self::$cmsWritableDir[$name])) {
	        return self::$cmsWritableDir[$name];
	    }

	    $config = Data::getInstance()->get('config');

	    if (!isset($config['cmsWritableDir'][$name])) {
	        throw new \Exception('Writable Dir ' . $name . ' not found');
	    }

	    self::$cmsWritableDir[$name] = $config['cmsWritableDir']['base'] . $config['cmsWritableDir'][$name];
	    return self::$cmsWritableDir[$name];
	}
// 	static function clearAkamai($path){

// 		if (APPLICATION_ENV != 'production') {
// 			return;
// 		}

// 		$config = Data::getInstance()->get('config');
// 		$host = $config['cmsHost'];

// 		$path = base64_encode($path);

// 		$cmsHost = $config['cmsHost'];
// 		if(strpos($cmsHost, "http") === false){
// 			$cmsHost ="http://".$cmsHost;
// 		}
// 		$cmsHost = rtrim($cmsHost,"/");

// 		$url = "{$cmsHost}/akamai/clear?path=".$path;

// 		$domain = $_SERVER['SERVER_NAME'];
// 		$httpHeader = array( "HOST: $domain" );

// 		$curlHandler = curl_init();
// 		curl_setopt($curlHandler, CURLOPT_URL, $url);
// 		curl_setopt($curlHandler, CURLOPT_HEADER, false);
// 		curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1);
// 		curl_setopt($curlHandler, CURLOPT_HTTPHEADER, $httpHeader);
// 		//curl_setopt($curlHandler, CURLOPT_TIMEOUT, $this->_timeout);
// 		curl_setopt($curlHandler, CURLOPT_FOLLOWLOCATION,true);
// 		$xml = curl_exec($curlHandler);
// 		$error = curl_error($curlHandler);
// 		curl_close($curlHandler);
// 	}
	
	static function stripUnderscores($string, $relative = false)
	{
		$string = str_replace('_', '/', trim($string));
		if($relative)
		{
			$string = self::stripLeading('/', $string);
		}
		return $string;
	}
	
	/**
	 * strips the leading $replace from the $string
	 *
	 * @param string $replace
	 * @param string $string
	 * @return string
	 */
	static function stripLeading($replace, $string)
	{
		if(substr($string, 0, strlen($replace)) == $replace)
		{
			return substr($string, strlen($replace));
		}else{
			return $string;
		}
	}
	
	static function addHyphens($string)
	{
		return str_replace(' ', '-', trim($string));
	}
	
	static function safe_file_put_contents($filename, $content, $mode = 'wb')
	{
		$fp = @fopen($filename, $mode);
		if ($fp) {
			flock($fp, LOCK_EX);
			fwrite($fp, $content);
			flock($fp, LOCK_UN);
			fclose($fp);
			return true;
		} else {
			return false;
		}
	}
	static function checkActionPermission($resource, $userAclResources) {
	    $user = Auth::getIdentity();
	    if ($user['id'] == User::SUPERUSER_ROLE) return true;
	    if ($user['id'] != User::SUPERUSER_ROLE && $resource == 'admin_index_log') {
	    	return false;
	    }
	    if(array_key_exists($resource, $userAclResources) && 1 == $userAclResources[$resource]) {
	        return true;
	    }
	    foreach ($userAclResources as $k=>$v) {
	        if ($v != 1) continue;
	        if ($k == substr($resource, 0, strlen($k))) {
	            return true;
	        }
	    }
	    return false;
	}
	static function getBreadcrumbs($current, $nav) {
	    $bread = array();
	    if (!$current instanceof \Zend\Navigation\Page\AbstractPage) {
	        return ;
	    }
	    $bread[] = $current;
	    if ($current->getParent() instanceof \Zend\Navigation\Page\AbstractPage) {
	        return array_merge(self::getBreadcrumbs($current->getParent(), $nav), $bread);
	    }
	    return $bread;
	}
	
	static function checkImportUnique($sheetData,$uniqueFiled,$sheetName){
	    $result = array();
	    $check = array();
	    $titleColumn = array_flip($sheetData[0]);
	    foreach ($uniqueFiled as $name){
	        $check[$name] = array();
	        if(isset($titleColumn[$name])){
	            $sheetIndex = $titleColumn[$name];
	            foreach ($sheetData as $key => $value){
	                if($key == 0 || $key == 1 || trim($value[0]) === null || trim($value[0]) === ''){
	                    continue;
	                }
	                $option = (int) $value[0];
	                if(in_array($option, array(0,1,2))){
	                    $errorRow = $sheetName . ' 数据错误 第'. ($key + 1)."行 ";
	                    $checkValue = trim($value[$sheetIndex]);
	                    if(in_array($checkValue, $check[$name]) && $checkValue !== null && $checkValue !== '') {
	                        $result['error'][] = $errorRow.$name.' 重复数据';
	                    }else{
	                        array_push($check[$name],$checkValue);
	                    }
	                }
	            }
	        }
	        
	    }
	    return $result;
	}
	
	static function checkImportRequired($sheetData,$requiredFiled,$sheetName){
	    $result = array();
	    $titleColumn = array_flip($sheetData[0]);
	    foreach ($requiredFiled as $name){
	        if(isset($titleColumn[$name])){
	            $sheetIndex = $titleColumn[$name];
	            foreach ($sheetData as $key => $value){
	                if($key == 0 || $key == 1 || trim($value[0]) === null || trim($value[0]) === ''){
	                    continue;
	                }
	                $option = (int) $value[0];
	                if(in_array($option, array(0,1,2))){
	                    $errorRow = $sheetName . ' 数据错误 第'. ($key + 1)."行 ";
	                    $checkValue = trim($value[$titleColumn[$name]]);
	                    if($checkValue === null || $checkValue === '') {
	                        $result['error'][] = $errorRow.$name.' 数据为空';
	                    }	                
	                }
	            }
	        }
	        
	    }
	    return $result;
	}
	
	static function checkImportEnum($sheetData,$enum,$sheetName){
	    $result = array();
	    $titleColumn = array_flip($sheetData[0]);
	    foreach ($enum as $name => $emunValue){
	        if(isset($titleColumn[$name])){
	            $sheetIndex = $titleColumn[$name];
	            foreach ($sheetData as $key => $value){
	                if($key == 0 || $key == 1 || trim($value[0]) === null || trim($value[0]) === ''){
	                    continue;
	                }
	                $option = (int) $value[0];
	                if(in_array($option, array(0,1,2))){
	                    $errorRow = $sheetName . ' 数据错误 第'. ($key + 1)."行 ";
	                    $checkValue = trim($value[$titleColumn[$name]]);
	                    if(!in_array(strtolower($checkValue),array_map('strtolower',$emunValue)) && $checkValue !== null && $checkValue !== ''){
	                        $result['error'][] = $errorRow.$name.' 类型错误 支持 ' . implode(',',$emunValue);
	                    }
	                }
	            }
	        }
	        
	    }
	    return $result;
	}
	
	static function getError(&$result,$error){
	    if($error['error']){
	        foreach ($error['error'] as $value){
	            $result['error'][] = $value;
	        }
	    }
	}
	
	static function getWhereArr($where){
	    $result = array();
	    foreach ($where  as $key => $value){
	        $result[$key] = $value;
	    }
	    return $result;
	}
	
	static function checkIp($ip){
	    if(preg_match(self::$ipRegex, $ip)){
	        return true;
	    }else{
	        return false;
	    }
	}
	
	static function checkMacAddress($macAddress){
	    if(preg_match(self::$macRegex, $macAddress)){
	        return true;
	    }else{
	        return false;
	    }
	}
	
	static function checkExcelDate($date){
	    if(preg_match(self::$excelDateRegex, $date)){
	        return true;
	    }else{
	        return false;
	    }
	}
	
	static function getExcelDate($date){
	    if(trim($data) !== null && trim($data) !== ''){
	        $arr = explode("/", trim($date));
	        return $arr[2].'-'.$arr[0].'-'.$arr[1];
	    }else{
	        return null;
	    }
	    
	}
	
	static function getIpRegex(){
	    return self::$ipRegex;
	}
	
	static function getMacAddressRegex(){
	    return self::$macRegex;
	}
	
	static function getGpsRegex(){
	    return self::$gpsRegex;
	}
	
	static function emptyToNull($data,$filter){
	    foreach ($data as $key => $value){
	        if(trim($value) == '' && in_array($key, $filter)){
	            $data[$key] = null;
	        }
	    }
	    return $data;
	}
	
	static function checkRowExist($data,$msg,&$result,DbTable $class){
	    foreach ($data as $key => $value){
	        $row = $class->fetchRow(self::getWhereArr($value));
	        if($row){
	            $result['error'][] = $msg.$key." 已存在";
	        }
	    }
	}
	
	static function checkUniqueColumn($column = array(),DbTable $class,$table){
	    $result = array();
	    $result['error'] = array();
	    foreach ($column as $v){
	        $sql = "SELECT `$v`,COUNT(id) AS c FROM $table GROUP BY `$v` HAVING `$v` != '' AND c > 1;";
	        $rows = $class->fetchAll($sql);
	        if($rows){
	            foreach ($rows as $row){
	                $result['error'][] = "数据表 ". $table . " ".$v." 存在重复数据 ".$row[$v];
	            }
	        }
	    }
	    return $result;
	    
	    
	}
	
	static function getOption($option){
	    $option = trim($option);
	    if($option !== '' && $option !== null){
	        $option = (int) $option;
	    }else{
	        $option = -1;
	    }
	    return $option;
	}
}

?>