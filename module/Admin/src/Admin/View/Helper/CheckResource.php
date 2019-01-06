<?php
namespace Admin\View\Helper;
 
use Admin\Model\Auth;
use Test\Data;
use Zend\View\Helper\AbstractHelper;
use Admin\Model\Acl;

class CheckResource extends AbstractHelper
{
     
    public function __invoke($url)
    {
        $urlArray = explode("/", $url);
        $controller = $urlArray[1];
        $action = $urlArray[2];
        $module = 'admin';
        $adminModuleArray = array('index','auth');
        if(!in_array(strtolower($controller), $adminModuleArray)){
            //$module = "mod_".strtolower($controller);
            $module = strtolower($controller);
        }
        $data = Data::getInstance();
        $currentUser = Auth::getIdentity();
        $role = $currentUser['id'];
        $acl = new Acl();
        
        //go from more specific to less specific
        $moduleLevel = $module;
        $controllerLevel = $moduleLevel . '_' . $controller;
        $actionLevel = $controllerLevel . '_' . $action;
        if ($acl->hasResource($actionLevel)) {
            $resource = $actionLevel;
        }elseif ($acl->hasResource($controllerLevel)){
            $resource = $controllerLevel;
        }else{
            $resource = $moduleLevel;
        }
        if ($acl->isAllowed($role, $resource)) {
            return true;
        }
        return false;
    }
}