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

use Admin\Model\AdminLog;
use Admin\Model\Auth;
use Admin\Model\User;


class LogController extends AbstractController
{

    function indexAction()
    {
    	$user = Auth::getIdentity();

    	$param = $this->params ()->fromQuery ();

    	$adminLog = new AdminLog();
    	$paginator = $adminLog->paginator ( $param );
		$paginator->setCurrentPageNumber ( ( int ) $param ['page'] );
		if(empty($param['perpage'])){
			$param['perpage'] = 20; 
		}
		$paginator->setItemCountPerPage ( $param['perpage'] );

		$user = new User();
		$users = $user->getUsersPairs();
		$users[0] = 'All';
		ksort($users);

// 		$site = new Sites();
// 		$sites = $site->getSiteShortNameList();
// 		$sites[0] = 'All';
// 		ksort($sites);

		$viewData ['paginator'] = $paginator;
		$viewData = array_merge ( $viewData, $param ,array('users'=>$users) );

		return new ViewModel ( $viewData );

    }

    public function logInfoAction() {
        $id = (int)$this->params()->fromRoute("id", 0);
        $log = new AdminLog();
        $row = $log->getLogById($id);
        $viewData = array ();
        $viewData['data'] = $row;
        return new ViewModel($viewData);
    }
    

}
