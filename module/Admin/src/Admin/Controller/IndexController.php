<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Zend\View\Model\ViewModel;

use Test\Data;

use Admin\Model\Auth;
use Admin\Model\User;


class IndexController extends AbstractController
{
    public function indexAction()
    {
        $identity = Auth::getIdentity();
        if($identity['id'] != 1){
            return $this->_redirect('/work/list');
        }else{
            return $this->_redirect('/acl/user-list');
           
        }
    	return new ViewModel();
    }
    
    public function updateMyPasswordAction()
    {
        if ($this->request->isPost()) {
            $user = new User();
            $data = $this->params()->fromPost();
            $oldPassword = $data['old_password'];
            $identity = Auth::getIdentity();
            $userInfo = $user->getUserById($identity['id']);
            if($userInfo['password'] != md5($oldPassword)){
                $viewData = array ();
                $viewData['error']['save'] = '原密码错误';
                return new ViewModel($viewData);
            }else{
                unset($data['old_password']);
                $user->updateMyPassword($data);
                Auth::destroy();
                return  $this->redirect()->toRoute('default', array('controller'=> 'auth',"action"=>"login"));
            }
        }
    }
    
}
