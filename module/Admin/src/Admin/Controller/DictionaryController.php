<?php
namespace Admin\Controller;

use Admin\Model\Controller;
use Test\Data;



use Admin\Model\User;
use Admin\Model\WfArea;
use Admin\Model\WfDepartment;
use Admin\Model\WfJob;
use Admin\Model\WfProject;
use Admin\Model\WfType;
use Admin\Model\WfWork;
use Zend\View\Model\ViewModel;


class DictionaryController extends AbstractController {

    protected $_controller = 'dictionary';
    public function __construct(){
        
    }
    
    public function areaAction(){
        $obj = new WfArea();
        $list = $obj->getList(array(),array('id'=>'asc'));
        $viewData = array();
        $viewData['paginator'] = $list;
        return new ViewModel ($viewData);
    }
    
    public function addAreaAction() {
        $obj = new WfArea();
        $result = array('code'=>0,'name' => '','id'=>0);
        $name = $this->params()->fromPost("name",'');
        if($name){
            $row = $obj->fetchRow(array('name'=>$name));
            if($row){
                $result['code'] = -2;
            }else{
                $data = array();
                $data['name'] = $name;
                $id = $obj->insertRow($data);
                $result['id'] = $id;
                $result['name'] = $name;
            }
        }else{
            $result['code'] = -1;
        }
        echo json_encode(array('data' => $result));exit;
    }
    
    public function deleteAreaAction() {
        $result = array();
        $result['code'] = -2;
        $obj = new WfArea();
        $id = (int)$this->params()->fromPost("id", 0);
        $row = $obj->getRowById($id);
        if($row){
            $user = new User();
            $where = array();
            $where['area_id'] = $id;
            $userList = $user->getList($where);
            if($userList){
                $result['code'] = -1;
            }else{
                $obj->tableGateway->delete(array('id' => $id));
                $result['code'] = 0;
            }
        }
        echo json_encode($result);
        exit;
        
    }
    
    public function departmentAction(){
        $obj = new WfDepartment();
        $list = $obj->getList(array(),array('id'=>'asc'));
        $viewData = array();
        $viewData['paginator'] = $list;
        return new ViewModel ($viewData);
    }
    
    public function addDepartmentAction() {
        $obj = new WfDepartment();
        $result = array('code'=>0,'name' => '','id'=>0);
        $name = $this->params()->fromPost("name",'');
        if($name){
            $row = $obj->fetchRow(array('name'=>$name));
            if($row){
                $result['code'] = -2;
            }else{
                $data = array();
                $data['name'] = $name;
                $id = $obj->insertRow($data);
                $result['id'] = $id;
                $result['name'] = $name;
            }
        }else{
            $result['code'] = -1;
        }
        echo json_encode(array('data' => $result));exit;
    }
    
    public function deleteDepartmentAction() {
        $result = array();
        $result['code'] = -2;
        $obj = new WfDepartment();
        $id = (int)$this->params()->fromPost("id", 0);
        $row = $obj->getRowById($id);
        if($row){
            $user = new User();
            $where = array();
            $where['department_id'] = $id;
            $userList = $user->getList($where);
            if($userList){
                $result['code'] = -1;
            }else{
                $obj->tableGateway->delete(array('id' => $id));
                $result['code'] = 0;
            }
        }
        echo json_encode($result);
        exit;
    }
    
    public function jobAction(){
        $obj = new WfJob();
        $department = new WfDepartment();
        $list = $obj->getList(array(),array('id'=>'asc'));
        $viewData = array();
        $viewData['paginator'] = $list;
        return new ViewModel ($viewData);
    }
    
    public function addJobAction() {
        $obj = new WfJob();
        $result = array('code'=>0,'name' => '','id'=>0);
        $name = $this->params()->fromPost("name",'');
        if($name){
            $row = $obj->fetchRow(array('name'=>$name));
            if($row){
                $result['code'] = -2;
            }else{
                $data = array();
                $data['name'] = $name;
                $id = $obj->insertRow($data);
                $result['id'] = $id;
                $result['name'] = $name;
            }
        }else{
            $result['code'] = -1;
        }
        echo json_encode(array('data' => $result));exit;
    }
    
    public function deleteJobAction() {
        $result = array();
        $result['code'] = -2;
        $obj = new WfJob();
        $id = (int)$this->params()->fromPost("id", 0);
        $row = $obj->getRowById($id);
        if($row){
            $user = new User();
            $where = array();
            $where['job_id'] = $id;
            $userList = $user->getList($where);
            if($userList){
                $result['code'] = -1;
            }else{
                $obj->tableGateway->delete(array('id' => $id));
                $result['code'] = 0;
            }
        }
        echo json_encode($result);
        exit;
    }
    
    public function projectAction(){
        $obj = new WfProject();
        $department = new WfDepartment();
        $list = $obj->getList(array(),array('id'=>'asc'));
        $viewData = array();
        $viewData['paginator'] = $list;
        return new ViewModel ($viewData);
    }
    
    public function addProjectAction() {
        $obj = new WfProject();
        $result = array('code'=>0,'name' => '','id'=>0);
        $name = $this->params()->fromPost("name",'');
        if($name){
            $row = $obj->fetchRow(array('name'=>$name));
            if($row){
                $result['code'] = -2;
            }else{
                $data = array();
                $data['name'] = $name;
                $id = $obj->insertRow($data);
                $result['id'] = $id;
                $result['name'] = $name;
            }
        }else{
            $result['code'] = -1;
        }
        echo json_encode(array('data' => $result));exit;
    }
    
    public function deleteProjectAction() {
        $result = array();
        $result['code'] = -2;
        $obj = new WfProject();
        $id = (int)$this->params()->fromPost("id", 0);
        $row = $obj->getRowById($id);
        if($row){
            $work = new WfWork();
            $where = array();
            $where['project_id'] = $id;
            $where['is_delete'] = 0;
            $workList = $work->getList($where);
            if($workList){
                $result['code'] = -1;
            }else{
                $obj->tableGateway->delete(array('id' => $id));
                $result['code'] = 0;
            }
        }
        echo json_encode($result);
        exit;
    }
    
    
    public function typeAction(){
        $obj = new WfType();
        $list = $obj->getList(array(),array('id'=>'asc'));
        $viewData = array();
        $viewData['paginator'] = $list;
        return new ViewModel ($viewData);
    }
    
    public function addTypeAction() {
        $obj = new WfType();
        $result = array('code'=>0,'name' => '','id'=>0);
        $name = $this->params()->fromPost("name",'');
        if($name){
            $row = $obj->fetchRow(array('name'=>$name));
            if($row){
                $result['code'] = -2;
            }else{
                $data = array();
                $data['name'] = $name;
                $id = $obj->insertRow($data);
                $result['id'] = $id;
                $result['name'] = $name;
            }
        }else{
            $result['code'] = -1;
        }
        echo json_encode(array('data' => $result));exit;
    }
    
    public function deleteTypeAction() {
        $result = array();
        $result['code'] = -2;
        $obj = new WfType();
        $id = (int)$this->params()->fromPost("id", 0);
        $row = $obj->getRowById($id);
        if($row){
            $work = new WfWork();
            $where = array();
            $where['type_id'] = $id;
            $where['is_delete'] = 0;
            $workList = $work->getList($where);
            if($workList){
                $result['code'] = -1;
            }else{
                $obj->tableGateway->delete(array('id' => $id));
                $result['code'] = 0;
            }
        }
        echo json_encode($result);
        exit;
    }
    
    public function disableProjectAction() {
    	$result = array();
    	$result['code'] = -2;
    	$obj = new WfProject();
    	$id = (int)$this->params()->fromPost("id", 0);
    	$row = $obj->getRowById($id);
    	if($row){
    		$data = array();
    		$data['is_delete'] = 0;
    		if($row['is_delete'] == 0){
    			$data['is_delete'] = 1;
    		}
    		$obj->updateRowById($data,$id);
    		$result['code'] = 0;
    		$result['status'] = $data['is_delete'];
    	}
    	echo json_encode($result);
    	exit;
    }
    
    
    
}