<?php

namespace Admin\Service;

use Admin\Model\Acl;
use Admin\Model\AdminLog;
use Admin\Model\Auth;
use Admin\Model\Sites;
use Admin\Model\User;
use Application\Service\Cache;
use Application\Util\Util;

use Test\Data;
use Test\Util\Common;

use Zend\Mvc\MvcEvent;

class Interceptor {
	
	/**
	 * the current user's identity
	 *
	 * @var zend_db_row
	 */
	private $_identity;
	
	/**
	 * the acl object
	 *
	 * @var zend_acl
	 */
	private $_acl;
	
	
	public function check(MvcEvent $e)
	{
		$data = Data::getInstance();
		$this->_identity = Auth::getIdentity();
		
			$acl = new Acl();
		 
		$this->_acl = $acl;

		if(!empty($this->_identity)){
			// use id instead of role
			$role = $this->_identity['id'];
			//$role = $this->_identity->role;
		}else{
			$role = null;
		}
		
		$module = $data->get('module'); 
		$controller = $data->get('controller'); 
		$action = $data->get('action'); 
		
		//go from more specific to less specific
		$moduleLevel = $module;
		$controllerLevel = $moduleLevel . '_' . $controller;
		$actionLevel = $controllerLevel . '_' . $action;
		
		if ($this->_acl->hasResource($actionLevel)) {
			$resource = $actionLevel;
		}elseif ($this->_acl->hasResource($controllerLevel)){
			$resource = $controllerLevel;
		}else{
			$resource = $moduleLevel;
		}
		/**
		 * @todo make sure this works
		 */
		if($module != 'public' && $controller != 'public'
				&& !($module=="mod_akamai" && $controller=="admin" && $action=='clear') && 
				!($module=="mod_memcache" && $controller=="admin" && ($action=='clear-cache' || $action=='clear-memcache'  || $action=='country'))){
			//if (!$this->_checkIp()) {
				//throw new \Exception('Access denied');
			//}
			
			if($this->_identity && $_SERVER["SCRIPT_NAME"] != '/auth/no-auth'){
			    $user = new User();
			    $info = $user->getUserById($this->_identity['id']);
			    if(!$info || $info['is_active'] != 1 || $info['is_delete'] != 0){
			        $url = '/auth/no-auth';
			        $response = $e->getResponse();
			        $response->getHeaders()->addHeaderLine('Location', $url);
			        $response->setStatusCode(302);
			        $response->sendHeaders();
			        exit;
			    }	        
			}
// 			if($this->_identity && $this->_identity['is_active'] != 1 && $_SERVER["SCRIPT_NAME"] != '/auth/no-auth'){
			   
// 			}
		    if (!$this->_acl->isAllowed($role, $resource)) {
		        if (!$this->_identity) {
		            $url = '/auth/login';
		            $response = $e->getResponse();
		            $response->getHeaders()->addHeaderLine('Location', $url);
		            $response->setStatusCode(302);
		            $response->sendHeaders();
		            exit;
		            
		        }else{
		            $url = '/auth/no-auth';
		            $response = $e->getResponse();
		            $response->getHeaders()->addHeaderLine('Location', $url);
		            $response->setStatusCode(302);
		            $response->sendHeaders();
		            exit;
		            
		        }
		    } else {
		        
		        if($_SERVER["SCRIPT_NAME"] == '/auth/login'){
		            
		        }else{
		            // 			        $site = $data->get('site');
		            
		            // 			        $data = array();
		            // 			        $userId = ($this->_identity['id'])?$this->_identity['id']:0;
		            // 			        $data['user_id'] = $userId;
		            // 			        $data['user_name'] = $this->_identity['name'];
		            
		            // 			        $data['url'] = $_SERVER['REQUEST_URI'];
		            
		            // 			        $postParams = $e->getRequest()->getPost()->toArray();
		            // 			        $getParams = $e->getRequest()->getQuery()->toArray();
		            // 			        $params = array_merge($postParams, $getParams);
		            
		            // 			        $data['params'] = ($params)?serialize($params):'';
		            
		            // 			        $data['ip'] = Common::getRemoteIp();
		            // 			        $data['date'] = date("Y-m-d H:i:s");
		            
		            // 			        $data['site_id'] = $site['site_id'];
		            
		            // 			        $adminLog = new AdminLog();
		            // 			        $adminLog->insert($data);
		        }
		    }
			
			 
		}
		
	}
	
 	/**
 	 * Check Ip  
 	 * 
 	 * @return boolean
 	 */
	protected function _checkIp()
	{
		$currentIp = Common::getRemoteIp();
	
		$allow = false;
		$ipList = $this->_loadList(ROOT_PATH . '/module/Admin/data/adminIp.txt');
		foreach ($ipList as $search) {
			if (false === strpos($search, '*')) {
				if ($currentIp === $search) {
					return true;
				}
			} else {
				 
				if ($this->_checkRange($currentIp, $search)) {
					return true;
				}
			}
		}
		return $allow;
	}
	
	private function _checkRange($ipAddr, $range)
	{
		$long = ip2long($ipAddr);
		$first = str_replace('*', '0', $range);
		$last = str_replace('*', '255', $range);
		if ($long >= ip2long($first) && $long <= ip2long($last)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	private function _loadList($filename)
	{
		$result = array();
	
		if (is_readable($filename) && ($data = file($filename, TRUE))) {
			foreach ($data as $line) {
				$line = trim($line);
				if ($line == '' || $line{0} == '#') {continue;}
				$result[] = $line;
			}
		}
	
		return $result;
	}
}

?>