<?php
namespace Admin\Controller;

use Admin\Model\Action;
use Admin\Model\Controller;
use Admin\Model\DealCache;
use Admin\Model\Module;
use Admin\Model\Role;
use Admin\Model\RoleAction;
use Admin\Model\SiteGroup;
use Admin\Model\Sites;
use Admin\Model\User;

use Application\Util\Util;

use Zend\Form\Form;


use Admin\Model\WfWork;
use Admin\Util\Post;
use Zend\View\Model\ViewModel;
use Admin\Model\WfJob;
use Admin\Model\WfProject;
use Admin\Model\WfType;


class AclController extends AbstractController {

    protected $_existMessage = '用户名已存在';
    
    protected $_listPath = null;

	public function indexAction(){
		return $this->redirect()->toRoute('default', array(
						'controller'=> 'acl',
						'action'    => 'module-list',
				));
	}

	public function moduleListAction(){
		$param = $this->params()->fromQuery();

		$module = new Module();
		$paginator = $module->paginator($param);
		$paginator->setCurrentPageNumber((int)$param['page']);
		if(empty($param['perpage'])){
			$param['perpage'] = 20; 
		}
		$paginator->setItemCountPerPage ( $param['perpage'] );

		$viewData ['paginator'] = $paginator;
		$viewData = array_merge ( $viewData, $param);

		return new ViewModel ($viewData);

	}

	public function controllerListAction(){
		$param = $this->params()->fromQuery();

		$controller = new Controller();
		$paginator = $controller->paginator ( $param );
		$paginator->setCurrentPageNumber (( int )$param ['page']);
		if(empty($param['perpage'])){
			$param['perpage'] = 20; 
		}
		$paginator->setItemCountPerPage ($param['perpage']);
		

		$module = new Module();
		$modules = $module->getModulesPairs();

		$viewData['modules'] = $modules;
		$viewData ['paginator'] = $paginator;
		$viewData = array_merge ($viewData, $param);

		return new ViewModel ($viewData);

	}

	public function actionListAction(){
		$param = $this->params()->fromQuery();

		$clause = array();

		$controllerId = (int)$this->params()->fromQuery('controller_id', 0);
		if ($controllerId) {
			$clause['controller_id'] = $controllerId;
		}

		$actionName = (string)$this->params()->fromQuery('action_name', '');
		if ($actionName) {
			$clause['name'] = $actionName;
		}

		$action = new Action();
		$paginator = $action->paginator($clause);
		$paginator->setCurrentPageNumber ((int)$param['page']);
		if(empty($param['perpage'])){
			$param['perpage'] = 20; 
		}
		$paginator->setItemCountPerPage ( $param['perpage'] );
		

		$Controller = new Controller();
		$controllers = $Controller->getControllersPairs();
		natsort($controllers);
		$viewData['controllers'] = $controllers;
		$viewData ['paginator'] = $paginator;
		$viewData = array_merge ( $viewData, $param);

		return new ViewModel ( $viewData );

	}


	public function roleListAction(){
		$param = $this->params ()->fromQuery ();

		$role = new Role();
		$paginator = $role->paginator ();
		$paginator->setCurrentPageNumber ( ( int ) $param ['page'] );
		if(empty($param['perpage'])){
			$param['perpage'] = 20; 
		}
		$paginator->setItemCountPerPage ( $param['perpage'] );
		

		$viewData ['paginator'] = $paginator;
		$viewData = array_merge ( $viewData, $param);

		return new ViewModel ( $viewData );

	}

	public function siteGroupListAction(){
		$param = $this->params ()->fromQuery ();

		$siteGroup = new SiteGroup();
		$paginator = $siteGroup->paginator ( array('name'=>$param['name']) );
		$paginator->setCurrentPageNumber ( ( int ) $param ['page'] );
		if(empty($param['perpage'])){
			$param['perpage'] = 20; 
		}
		$paginator->setItemCountPerPage ( $param['perpage'] );
		

		$viewData ['paginator'] = $paginator;
		$viewData = array_merge ( $viewData, $param);

		return new ViewModel ( $viewData );

	}

	public function userListAction(){
		$param = $this->params()->fromQuery();
		$user = new User();
		if(!isset($param['is_delete'])){
		    $param['is_delete'] = 0;
		}
		
		$paginator = $user->paginator($param);
		$paginator->setCurrentPageNumber ((int) $param ['page']);
		if(empty($param['perpage'])){
			$param['perpage'] = 20; 
		}
		$paginator->setItemCountPerPage ($param['perpage']);
		

		$viewData ['paginator'] = $paginator;
		$viewData ['param'] = $param;
		$viewData = array_merge ($viewData, $param);

		return new ViewModel ( $viewData );

	}

	public function moduleEditAction() {

		$module = new Module();
		$form = $module->getAclModuleForm($_POST);

		if ($this->request->isPost() && $form->isValid()) {
			$data = $form->getData();

			unset($data['submit']);
			unset($data['cancel']);

			$id = (int)$data['id'];
			if ($id) {
				$module->updateModule($id, $data);
				$logMessage = "修改Module id:".$id;
				//$this->saveLog($logMessage,$this->objToArray($data));
			} else {
				$insertId = $module->insertModule($data);
				$logMessage = "新建Module id:".$insertId;
				$data['id'] = $insertId;
				//$this->saveLog($logMessage,$this->objToArray($data));
			}

			$this->_clearResources();
			return $this->redirect()->toRoute('default', array(
						'controller'=> 'acl',
						'action'    => 'module-list',
				));
		}


		$id = ( int ) $this->params()->fromRoute ( "id", 0 );

		// edit, then get old data
		if ($id) {
			$data = $module->getModuleById($id);
			if (!$data) {
				return $this->redirect()->toRoute('default', array(
						'controller'=> 'acl',
						'action'    => 'module-list',
				));
			}
			$form->setData( $data);
			$form->get('submit')->setValue('编辑');
		}
		$viewData = array ();
		$viewData['form'] = $form;
		return new ViewModel ( $viewData );

	}

	public function controllerEditAction() {

		$controller = new Controller();
		$form = $controller->getAclControllerForm($_POST);

		if ($this->request->isPost() && $form->isValid()) {
			$data = $form->getData();

			unset($data['submit']);
			unset($data['cancel']);

			$id = (int)$data['id'];
			if ($id) {
				$controller->updateController($id, $data);
				$logMessage = "修改Controller id:".$id;
				//$this->saveLog($logMessage,$this->objToArray($data));
			} else {
				$insertId = $controller->insertController($data);
				$logMessage = "新建Controller id:".$insertId;
				$data['id'] = $insertId;
				//$this->saveLog($logMessage,$this->objToArray($data));
			}

			$this->_clearResources();

			return $this->redirect()->toRoute('default', array(
					'controller'=> 'acl',
					'action'    => 'controller-list',
			));
		}


		$id = ( int ) $this->params ()->fromRoute ( "id", 0 );

		// edit, then get old data
		if ($id) {
			$data = $controller->getControllerById($id);
			if (!$data) {
				return $this->redirect()->toRoute('default', array(
						'controller'=> 'acl',
						'action'    => 'controller-list',
				));
			}
			$form->setData( $data);
			$form->get('submit')->setValue('编辑');
		}
		$viewData = array ();
		$viewData['form'] = $form;
		return new ViewModel ( $viewData );

	}

	public function actionEditAction() {

		$action = new Action();

		$form = $action->getAclActionForm($_POST);

		if ($this->request->isPost() && $form->isValid()) {
			$data = $form->getData();

			unset($data['submit']);
			unset($data['cancel']);

			$id = (int)$data['id'];
			if ($data['controller_id']) {
			    $controller = new Controller();
				$module = $controller->getControllerById($data['controller_id']);
				$data['module_id'] = $module->module_id;
			}
			if ($id) {
				$action->updateAction($id, $data);
				$logMessage = "修改Action id:".$id;
				//$this->saveLog($logMessage,$this->objToArray($data));
			} else {
			    $insertId = $action->insertAction($data);
				$logMessage = "新建Action id:".$insertId;
				$data['id'] = $insertId;
				//$this->saveLog($logMessage,$this->objToArray($data));
			}

			$this->_clearResources();

			return $this->redirect()->toRoute('default', array(
					'controller'=> 'acl',
					'action'    => 'action-list',
			));
		}


		$id = ( int ) $this->params ()->fromRoute ( "id", 0 );

		// edit, then get old data
		if ($id) {
			$data = $action->getActionById($id);
			if (!$data) {
				return $this->redirect()->toRoute('default', array(
						'controller'=> 'acl',
						'action'    => 'action-list',
				));
			}
			$form->setData( $data);
			$form->get('submit')->setValue('Edit Action');
		}
		$viewData = array ();
		$viewData['form'] = $form;
		return new ViewModel ( $viewData );

	}

	public function roleEditAction() {

		$role = new Role();
		$form = $role->getAclRoleForm($_POST);

		if ($this->request->isPost() && $form->isValid()) {
			$data = $form->getData();

			unset($data['submit']);
			unset($data['cancel']);

			$id = (int)$data['id'];
			if ($id) {
				$role->updateRole($id, $data);
				$logMessage = "修改角色 id:".$id;
				//$this->saveLog($logMessage,$this->objToArray($data));
			} else {
			    $insertId = $role->insertRole($data);
			    $logMessage = "添加角色 id:".$insertId;
			    $data['id'] = $insertId;
			    //$this->saveLog($logMessage,$this->objToArray($data));
			}

			$this->_clearResources();

			return $this->redirect()->toRoute('default', array(
					'controller'=> 'acl',
					'action'    => 'role-list',
			));
		}


		$id = ( int ) $this->params ()->fromRoute ( "id", 0 );

		// edit, then get old data
		if ($id) {
			$data = $role->getRoleById($id);
			if (!$data) {
				return $this->redirect()->toRoute('default', array(
						'controller'=> 'acl',
						'action'    => 'role-list',
				));
			}
			$form->get('submit')->setValue('编辑');
			$form->setData( $data);
		}
		$viewData = array ();
		$viewData['form'] = $form;
		return new ViewModel ( $viewData );

	}

	public function siteGroupEditAction() {
		$siteGroup = new SiteGroup();
		$form = $siteGroup->getAclSiteGroupForm($_POST);

		if ($this->request->isPost() && $form->isValid()) {
			$data = $form->getData();

			unset($data['submit']);
			unset($data['cancel']);


			$id = (int)$data['id'];
			if ($id) {
				$siteGroup->updateSiteGroup($id, $data);
			} else {
				$siteGroup->insertSiteGroup($data);
			}

			$this->_clearResources();

			return $this->redirect()->toRoute('default', array(
					'controller'=> 'acl',
					'action'    => 'site-group-list',
			));
		}


		$id = ( int ) $this->params ()->fromRoute ( "id", 0 );

		// edit, then get old data
		if ($id) {
			$data = $siteGroup->getSiteGroupById($id);
			if (!$data) {
				return $this->redirect()->toRoute('default', array(
						'controller'=> 'acl',
						'action'    => 'site-group-list',
				));
			}
			$form->setData( $data);
			$form->get('submit')->setValue('Edit Site Group');
		}
		$viewData = array ();
		$viewData['form'] = $form;
		return new ViewModel ( $viewData );

	}

	public function userAddAction() {
	    
		$user = new User();

		$form = $user->getAclUserForm($_POST);

		if ($this->request->isPost() && $form->isValid()) {
		    
			$data = $form->getData();
			$id = (int)$data['id'];
			$roleId = $data['role_id'];
			if ($id) {
				$user->updateUser($id, $data);
			} else {
			    $result = $user->getUserByName($data['name']);
			    if($result){
			        $url = '/acl/user-add?error=exist';
			        return $this->redirect()->toUrl($url);
			    }
				$id = $user->insertUser($data);
				$data = array(1);
				$user->updateSelectedSiteGroups($data, $id);
				//$data = array();
				//$data[] = $roleId;
				$user->updateSelectedRoles($roleId, $id);
				//$logMessage = "新建用户 id:".$id;
				//$this->saveLog($logMessage,$this->objToArray($user->getUserById($id)));
			}

			$this->_clearResources();

			return $this->redirect()->toRoute('default', array(
					'controller'=> 'acl',
					'action'    => 'user-list',
					//'id'		=> $id,
			));
		}


		$id = ( int ) $this->params ()->fromRoute ( "id", 0 );

		// edit, then get old data
		if ($id) {
			$data = $user->getUserById($id);
			if (!$data) {
				return $this->redirect()->toRoute('default', array(
						'controller'=> 'acl',
						'action'    => 'user-list',
				));
			}
			$form->setData( $data);
		}
		$viewData = array ();
		$viewData['form'] = $form;
		$viewData['error'] = $form->getMessages();
		$error = $this->params()->fromQuery('error');
		if($error == 'exist'){
		    $viewData['error']['exist'] = $this->_existMessage;
		}
		return new ViewModel ( $viewData );

	}

	public function moduleDeleteAction() {
		$id = ( int ) $this->params ()->fromRoute ( "id", 0 );
		$module = new Module();
		$module->deleteModule($id);
		$logMessage = "删除Module id:".$id;
		//$this->saveLog($logMessage);
		$refer = $_SERVER['HTTP_REFERER'];
		if ($refer) {
			return $this->redirect()->toUrl($refer);
		} else {
			return $this->redirect()->toRoute('default', array(
						'controller'=> 'acl',
						'action'    => 'module-list',
				));
		}
	}

	public function controllerDeleteAction() {
		$id = ( int ) $this->params ()->fromRoute ( "id", 0 );
		$controller = new Controller();
		$controller->deleteController($id);
		
		$logMessage = "删除Controller id:".$id;
		//$this->saveLog($logMessage);
		$refer = $_SERVER['HTTP_REFERER'];
		if ($refer) {
			return $this->redirect()->toUrl($refer);
		} else {
			return $this->redirect()->toRoute('default', array(
					'controller'=> 'acl',
					'action'    => 'controller-list',
			));
		}
	}

	public function actionDeleteAction() {
		$id = ( int ) $this->params ()->fromRoute ( "id", 0 );
		$action = new Action();
		$action->deleteAction($id);
		$logMessage = "删除Action id:".$id;
		//$this->saveLog($logMessage);
		$refer = $_SERVER['HTTP_REFERER'];
		if ($refer) {
			return $this->redirect()->toUrl($refer);
		} else {
			return $this->redirect()->toRoute('default', array(
					'controller'=> 'acl',
					'action'    => 'action-list',
			));
		}
	}

	public function roleDeleteAction() {
		$id = ( int ) $this->params ()->fromRoute ( "id", 0 );
		$role = new Role();
		$role->deleteRole($id);
		$logMessage = "删除角色 id:".$id;
		//$this->saveLog($logMessage);
		$refer = $_SERVER['HTTP_REFERER'];
		if ($refer) {
			return $this->redirect()->toUrl($refer);
		} else {
			return $this->redirect()->toRoute('default', array(
					'controller'=> 'acl',
					'action'    => 'role-list',
			));
		}
	}


	public function siteGroupDeleteAction() {
		$id = ( int ) $this->params ()->fromRoute ( "id", 0 );
		$siteGroup = new SiteGroup();
		$siteGroup->deleteSiteGroup($id);

		$refer = $_SERVER['HTTP_REFERER'];
		if ($refer) {
			return $this->redirect()->toUrl($refer);
		} else {
			return $this->redirect()->toRoute('default', array(
					'controller'=> 'acl',
					'action'    => 'site-group-list',
			));
		}
	}

	public function roleManageAction() {

		$role = new Role();
		$form = $role->getAclRoleManageForm($_POST);

		if ($this->request->isPost() && $form->isValid()) {

			$data = $form->getData();

			unset($data['submit']);

// 			$selectedData = $data['selectedData'];
            $selectedData = Post::get('selectedData');
			$actions = explode(',', $selectedData);


			$roleAction = new RoleAction();

			$id = (int)$data['id'];

			$role->updateRole($id, array('name'=>$data['name'],'description'=>$data['description']));
			$roleAction->updateRoleByActions($id, $actions);
           
			$logMessage = "编辑角色信息及权限 id:".$id;
			$logData = array();
			$logData['name'] = $data['name'];
			$logData['description'] = $data['description'];
			$logData['id'] = $id;
			$logData['action'] = $_POST['selectedData'];
			//$this->saveLog($logMessage,$logData);
			
			$this->_clearResources();

			return $this->redirect()->toRoute('default', array(
					'controller'=> 'acl',
					'action'    => 'role-list',
			));
		}

		$id = (int) $this->params ()->fromRoute ( "id", 0 );
		if(!$id){
			$id = ( int ) $this->params ()->fromPost ( "id", 0 ); 
		}
		// edit, then get old data
		if ($id) {
			$data = $role->getRoleById($id);
			if (!$data) {
				return $this->redirect()->toRoute('default', array(
						'controller'=> 'acl',
						'action'    => 'role-list',
				));
			}
			$form->setData( $data);

			$roleAction = new RoleAction();
			$selectedRoles = $roleAction->getSelectedActions($id);
			$unselectedRoles = $roleAction->getUnselectedActions($id);

			$form->get('leftSelector')->setValueOptions($unselectedRoles);
			$form->get('selected')->setValueOptions($selectedRoles);

		}

		$viewData = array ();
		$viewData['form'] = $form;
		return new ViewModel ( $viewData );
	}


	public function siteGroupManageAction() {


		$siteGroup= new SiteGroup();
		$form = $siteGroup->getAclSiteGroupManageForm($_POST);
		
		$id = ( int ) $this->params ()->fromRoute ( "id", ( int ) $this->params ()->fromPost ( "id", 0 ) );
		
		if ($this->request->isPost() && $form->isValid()) {

			$data = $form->getData();

			unset($data['submit']);

			$selectedData = $data['selectedData'];
			$selected = explode(',', $selectedData);
			$selected = array_filter($selected);

			$id = (int)$data['id'];

			$siteGroup = new SiteGroup();

			$siteGroup->updateSiteGroup($id, array('name'=>$data['name'],'description'=>$data['description']));

			$siteGroup->updateRelationById($selected, $id);

			$this->_clearResources();

			return $this->redirect()->toRoute('default', array(
					'controller'=> 'acl',
					'action'    => 'site-group-list',
			));
		}


		// edit, then get old data
		if ($id) {

			$data = $siteGroup->getSiteGroupById($id);
			if (!$data) {
				return $this->redirect()->toRoute('default', array(
						'controller'=> 'acl',
						'action'    => 'site-group-list',
				));
			}
			$form->setData( $data);

			$site = new Sites();
			$sites = $site->getSitesPairs();

			$siteGroup = new SiteGroup();
			$selected = $siteGroup->getSelectedSitesBySiteGroupId($id);

			foreach($selected as $item => $svalue) {
				foreach($sites as $key => $value) {
					if($item == $key) {
						unset($sites[$key]);
					}
				}
			}

			$form->get('site')->setValueOptions($sites);
			$form->get('selected')->setValueOptions($selected);

		}

		$viewData = array();
		$viewData['form'] = $form;
		return new ViewModel($viewData);
	}

	public function userManageAction() {
		$user = new User();
		$viewData = array();

		$id = (int) $this->params()->fromRoute("id", (int)$this->params()->fromPost("id", 0));
		if(empty($id)){
			return $this->redirect()->toRoute('default', array(
					'controller'=> 'acl',
					'action'    => 'user-list',
			));
		}



		$aclUserForm = $user->getAclUserForm($_POST,$id);
		$aclUserForm->setAttribute('action', '/acl/user-manage')
					->setAttribute('name', 'form_general');
		$aclUserForm->get('submit')->setAttribute('value','修改用户信息');

		$aclUserRolesForm = $user->getAclUserRolesForm($id);
		$aclUserSiteGroupsForm = $user->getAclUserSiteGroupsForm($id);
		$aclUserSitesForm = $user->getAclUserSitesForm($id);

		if ($this->request->isPost()) {
			$redirect = true;
			if(key_exists('selectedRolesData', $_POST)){
				$selected = explode(',', $this->params ()->fromPost('selectedRolesData'));
				$selected = array_filter($selected);
				$user->updateSelectedRoles($selected, $id);
				$data = $user->getSelectedRolesByUserId($id);
				$logMessage = "修改用户角色 用户id:".$id;
				$data = array();
				$data['user_id'] = $id;
				$data['role_id'] = $_POST['selectedRolesData'];
				//$this->saveLog($logMessage,$data);
				unset($data);
			}elseif(key_exists('selectedSitesData', $_POST)){
				$selected = explode(',', $this->params ()->fromPost('selectedSitesData'));
				$selected = array_filter($selected);
				$user->updateSelectedSites($selected, $id);
			}elseif(key_exists('selectedSiteGroupsData', $_POST)){
				$selected = explode(',', $this->params ()->fromPost('selectedSiteGroupsData'));
				$selected = array_filter($selected);
				$user->updateSelectedSiteGroups($selected, $id);
			}else{
				$redirect = false;
				if($aclUserForm->isValid()){
					$data = $aclUserForm->getData();
					if($data['update_password']=='1'){
						//if(empty($data['newPassword']) || empty($data['newConfirmPassword']) || $data['newPassword']!=$data['newConfirmPassword']){
							//$viewData['error'] = array('password'=>'密码不一致');
						//}
						//$data['password'] = md5(User::INIT_PWD);
						//$data['update_pwd'] = 1;
					}
					if(empty($viewData['error'])){
						$id = (int)$data['id'];
					    $userInfo = $user->getUserById($id);
					    if($data['name'] != $userInfo['name']){
					        $result = $user->getUserByName($data['name']);
					        if($result){
					            $url = "/acl/user-manage/id/".$id."?error=exist";
					            return $this->redirect()->toUrl($url);
					        }
					    }
						$user->updateUser($id, $data);
						$roleId = $data['role_id'];
						$user->updateSelectedRoles($roleId, $id);
						
						
						//$userInfo = $user->getUserById($id);
						//$logMessage = "编辑用户 id:".$id;
						//if($data['update_password']=='1'){
						    //$logMessage = "编辑用户并重置用户密码 id:".$id;
						//}
						//$this->saveLog($logMessage,$this->objToArray($userInfo));
					}
				}else{
					$viewData['error'] = $aclUserForm->getMessages();
				}
			}
			if(empty($viewData['error'])){
				$this->_clearResources();
				$url = '/acl/user-list';
				return $this->redirect()->toUrl($url);
			}
            
// 			if($redirect){
// 				$url = '/acl/user-manage/id/' . $id . '?scope=' . $this->params ()->fromPost('scope');
// 				return $this->redirect()->toUrl($url);
// 			}

			
		}else{

			$scope = $this->params()->fromQuery('scope', 'general');
			if ($id) {
				$data = $user->getUserById($id);
				if (!$data) {
					return $this->redirect()->toRoute('default', array(
							'controller'=> 'acl',
							'action'    => 'user-list',
					));
				}
				$aclUserForm->setData( $data);
			}
		}

		$viewData['aclUserForm'] = $aclUserForm;
		$viewData['aclUserRolesForm'] = $aclUserRolesForm;
		$viewData['aclUserSiteGroupsForm'] = $aclUserSiteGroupsForm;
		$viewData['aclUserSitesForm'] = $aclUserSitesForm;
		$viewData['scope'] = $scope;
		$viewData['id'] = $id;
		//$error = $this->params()->fromQuery('error');
		//if($error == 'exist'){
		    //$viewData['error']['exist'] = $this->_existMessage;
		//}
		return new ViewModel($viewData);
	}

	public function userDeleteAction() {
		$id = (int) $this->params()->fromRoute( "id", 0 );
	   	if($id == 1) {
	   		throw new \Exception("Can't delete the system default user!");
	   	}
	   	
	   	$user = new User();
	   	$userInfo = $user->getUserById($id);
	   	if(!$userInfo){
	   	    throw new \Exception("User not exist");
	   	}
	   	$data = array();
	   	if($userInfo['is_delete'] == 0){
	   	    $data['is_delete'] = 1;
	   	}
	   	$user->updateUser($id, $data);
	   	$userInfo['is_delete'] = 1;
	   	$logMessage = "删除用户 id:".$id;
	   	//$this->saveLog($logMessage,$this->objToArray($userInfo));
	   	$url = "/acl/user-list";
	   	return $this->redirect()->toUrl($url);
	   	
	}

	public function userReactiveAction() {
	    $id = (int) $this->params()->fromRoute( "id", 0 );
	    if($id == 1) {
	        throw new \Exception("不能删除超级管理员");
	    }
	    
	    $user = new User();
	    $userInfo = $user->getUserById($id);
	    if(!$userInfo){
	        throw new \Exception("User not exist");
	    }
	    $data = array();
	    if($userInfo['is_delete'] == 1){
	        $data['is_delete'] = 0;
	    }
	    $user->updateUser($id, $data);
	    $userInfo['is_delete'] = 0;
	    $logMessage = "还原删除用户 id:".$id;
	    //$this->saveLog($logMessage,$this->objToArray($userInfo));
	    $url = "/acl/user-list";
	    return $this->redirect()->toUrl($url);
	    
	}
	
	
	public function userActiveAction() {
	    $id = (int) $this->params()->fromRoute( "id", 0 );
	    if($id == 1) {
	        throw new \Exception("Can't active the system default user!");
	    }
	    $user = new User();
	    $userInfo = $user->getUserById($id);
	    if(!$userInfo){
	        throw new \Exception("用户不存在");
	    }
	    $data = array();	    
	    if($userInfo['is_active'] == 1){
	        $data['is_active'] = 0;
	    }else{
	        $data['is_active'] = 1;
	    }
	    $user->updateUser($id, $data);
	    $userInfo['is_active'] = $data['is_active'];
	    if($data['is_active'] == 0){
	        $logMessage = "禁用用户 id:".$id;
	        //$this->saveLog($logMessage,$this->objToArray($userInfo));
	    }else{
	        $logMessage = "启用用户 id:".$id;
	        //$this->saveLog($logMessage,$this->objToArray($userInfo));
	    }
	    $url = "/acl/user-list";
	    return $this->redirect()->toUrl($url);
	}
	
	public function updateMyPasswordAction()
	{
	    if ($this->request->isPost()) {
	        $user = new User();
	        $data = $this->params()->fromPost();
	        $user->updateMyPassword($data);
	        $url = '/';
	        return $this->redirect()->toUrl($url);
	    }
	}
	
	protected function _clearResources(){
		//更新缓存文件
		$dealCache = new DealCache() ;
		$dealCache->dealAclResources() ;
	}
	
	public  function exportAction(){
	    
	    $where['start_date'] = $this->params()->fromPost("start_date",'');
	    $where['end_date'] = $this->params()->fromPost('end_date','');
	    $work = new WfWork();
	    $list = $work->load($where);
	    $result = array();
	    if($list){
	    	$user = new User();
	    	$userList = $user->getList();
	    	$userListArray = array();
	    	foreach ($userList as $row){
	    		$userListArray[$row['id']] = $row['real_name'];
	    	}
	    	$project = new WfProject();
	    	$projectList = $project->getList();
	    	$projectListArray = array();
	    	foreach ($projectList as $row){
	    		$projectListArray[$row['id']] = $row['name'];
	    	}
	    	$type = new WfType();
	    	$typeList = $type->getList();
	    	$typeListArray = array();
	    	foreach ($typeList as $row){
	    		$typeListArray[$row['id']] = $row['name'];
	    	}
	    	//array(8) { ["user_id"]=> string(2) "62" ["area"]=> string(6) "上海" ["department"]=> string(15) "产品管理部" ["job"]=> string(12) "数据分析" ["type_id"]=> string(1) "1" ["project_id"]=> string(2) "30" ["work_date"]=> string(10) "2018-12-10" ["work_time"]=> string(1) "1" } 
	    	foreach ($list as $key => $value){
	    		$value['real_name'] = $userListArray[$value['user_id']];
	    		$value['project_name'] = $projectListArray[$value['project_id']];
	    		$value['type_name'] = $typeListArray[$value['type_id']];
	    		unset($value['user_id']);
	    		unset($value['type_id']);
	    		unset($value['project_id']);
	    		$list[$key] = $value;
	    	}
	        $filename = $where['start_date']."----".$where['end_date']."工时.xlsx";
	        $exce = $this->exportExcel($list,$filename,array(),2,true);
	        $result['code'] = 1;
	        $result['name'] = $filename;
	    }else{
	        $result['code'] = 2;
	        
	    }
	    echo json_encode($result);exit;
	    
	}
	
	public function exportExcel($list,$filename,$indexKey,$startRow = 1,$excel2007 = false){
	    
	    $this->_listPath = ROOT_PATH . '/public/export/';
	    
	    if (!is_dir($this->_listPath)) {
	        @mkdir ($this->_listPath, 0755, true );
	    }
	    
	    require_once ROOT_PATH.'/vendor/PHPExcel/PHPExcel.php';
	    require_once ROOT_PATH.'/vendor/PHPExcel/PHPExcel/Writer/Excel2007.php';
	    if(empty($filename)) $filename = time();
	    //初始化PHPExcel()
	    $objPHPExcel = new \PHPExcel();
	    
	    //设置保存版本格式
	    if($excel2007){
	        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
	        //$filename = $filename.'.xlsx';
	    }else{
	        $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
	        //$filename = $filename.'.xls';
	    }
	    
	    //接下来就是写数据到表格里面去
	    $objActSheet = $objPHPExcel->getActiveSheet();
	    $objActSheet->setCellValue('A1',  "真实姓名");
	    $objActSheet->setCellValue('B1',  "地区");
	    $objActSheet->setCellValue('C1',  "部门");
	    $objActSheet->setCellValue('D1',  "职位");
	    $objActSheet->setCellValue('E1',  "项目名称");
	    $objActSheet->setCellValue('F1',  "类型");
	    $objActSheet->setCellValue('G1',  "日期");
	    $objActSheet->setCellValue('H1',  "工时");
	    $header_arr = array('A','B','C','D','E','F','G','H');
	    $indexKey =array('real_name','area','department','job','project_name','type_name','work_date','work_time');
	    
	    foreach ($list as $row){
	        unset($row['id']);
	        //echo $startRow;exit;
	        foreach ($indexKey as $key => $value){
	            //这里是设置单元格的内容
	            $objActSheet->setCellValue($header_arr[$key].$startRow,$row[$value]);
	        }
	        $startRow++;
	    }
	    $dir = $this->_listPath . '/'. $filename;
	    
	    $objWriter->save($dir,$filename);
	}

// 	public function testAction(){
// 	    require_once ROOT_PATH . '/vendor/PHPExcel/PHPExcel.php';
// 	    $phpExcel = new \PHPExcel();
// 	    $result = array();
// 	    $file = ROOT_PATH .'/public/test.xlsx';
// 	    $userModel = new User();
// 	    $objReader = \PHPExcel_IOFactory::createReaderForFile($file);
// 	    $objPHPExcel = $objReader->load($file,$encode='utf-8');
// 	        $sheetObj = $objPHPExcel->getSheetByName('Sheet1');
// 	        $sheetData = $sheetObj->toArray();
// 	        foreach ($sheetData as $key => $row){
// 	            $data = array();
// 	            if($key > 0){
// 	                $data['name'] = $row[0];
// 	                $data['real_name'] = $row[1];
// 	                $data['department_id'] = $row[2];
// 	                $data['job_id'] = $row[3];
// 	                $data['area_id'] = 12;
// 	                $id = $userModel->insertUser($data);
// 	                $data = array(1);
// 	                $userModel->updateSelectedSiteGroups($data, $id);
// 	                $data = array(7);
// 	                $userModel->updateSelectedRoles($data, $id);
// 	            }
// 	        }
// 	    return $result;
// 	}
}