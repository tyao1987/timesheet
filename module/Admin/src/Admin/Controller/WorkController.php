<?php
namespace Admin\Controller;

use Admin\Model\Controller;


use Admin\Model\Auth;
use Admin\Model\WfWork;
use Zend\View\Model\ViewModel;
use Admin\Model\User;
use Admin\Model\WfArea;
use Admin\Model\WfJob;
use Admin\Model\WfDepartment;
use Admin\Model\WfProject;
use Admin\Model\WfType;


class WorkController extends AbstractController {

    protected $_controller = 'work';
    public function __construct(){
        $identity = Auth::getIdentity();
        if($identity['id'] == 1){
            return $this->_redirect('/acl/user-list');
        }
    }
    
    public function addAction(){
        $identity = Auth::getIdentity();
        $result = array();
        $user = new User();
        $userInfo = $user->getCurrentUser();
        $areaId = $userInfo['area_id'];
        $departmentId = $userInfo['department_id'];
        $jobId = $userInfo['job_id'];
        $area = new WfArea();
        $areaInfo = $area->getRowById($areaId);
        $job = new WfJob();
        $jobInfo = $job->getRowById($jobId);
        $department = new WfDepartment();
        $departmentInfo = $department->getRowById($departmentId);
        $result = array();
        $result['code'] = 0;
        $obj = new WfWork();
        $data = array();
        $data['user_id'] = $identity['id'];
        $data['area'] = $areaInfo['name'];
        $data['department'] = $departmentInfo['name'];
        $data['job'] = $jobInfo['name'];
        $productId = $this->params()->fromPost("productId",0);
        $data['project_id'] = $productId;
        $typeTd = $this->params()->fromPost("typeTd",0);
        
        $product = new WfProject();
        $productRow = $product->getRowById($productId);
        $type = new WfType();
        $typeRow = $type->getRowById($typeTd);
        if(!$productRow || !$typeRow){
            $result['code'] = -2;
            echo json_encode($result);exit;
        }
        $data['type_id'] = $typeTd;
        $date = $this->params()->fromPost("date","");
        $data['work_date'] = $date;
        $time = $this->params()->fromPost("time","");
        $data['work_time'] = $time;
        if($time == '' || $productId == 0 || $typeTd == 0 || $date == ''){
            $result['code'] = -1;
            echo json_encode($result);exit;
        }
        
        $where = array();
        $where['user_id'] = $userInfo['id'];
        $where['is_delete'] = 0;
        $where['work_date'] = $date;
        $list = $obj->getList($where);
        $allTime = 0;
        if($list){
            foreach ($list as $row){
                $allTime += $row['work_time'];
            }
        }
        $allTime += $time;
        if($allTime > 24){
            $result['code'] = -3;
        }else{
            $id = $obj->insertRow($data);
            $result['id'] = $id;
            $result['code'] = 0;
        }
        echo json_encode($result);exit;
    }
    
    public function listAction(){
        
        $where = array();
        $where['is_delete'] = 0;
        $identity = Auth::getIdentity();
        $where['user_id'] = $identity['id'];
        $startDate = '';
        $endDate = '';
        $day = date("w");
        if(!isset($this->params['start_date']) && !isset($this->params['end_date'])){
            $day = date("w");
            if($day == 6){
                //如果是星期六
                $startDate = date('Y-m-d');
                $endDate = date('Y-m-d',strtotime("next Friday"));
            }else if($day == 5){
                //如果是星期五
                $startDate = date('Y-m-d',strtotime("last Saturday"));
                $endDate = date('Y-m-d');
            }
            else{
                $startDate = date('Y-m-d',strtotime("last Saturday"));
                $endDate = date('Y-m-d',strtotime("next Friday"));
            }
        }
        if(isset($this->params['start_date']) && $this->params['start_date'] != ''){
            $startDate = $this->params['start_date'];
        }
        if(isset($this->params['end_date']) && $this->params['end_date'] != ''){
            $endDate = $this->params['end_date'];
        }
        $today = date('Y-m-d');
        if($endDate >= $today && $endDate != ''){
            $endDate = $today;
        }
        if($startDate){
            $where['work_date >= ?'] = $startDate;
        }
        if($endDate){
            $where['work_date <= ?'] = $endDate;
        }
        
        
        $param = $this->params()->fromQuery();
        if(!isset($param['is_delete'])){
            $param['is_delete'] = 0;
        }
        $obj = new WfWork();
        $paginator = $obj->paginator($param,$where,array('work_date'=>'desc','id'=>'desc'));
        $paginator->setCurrentPageNumber ( ( int ) $param ['page'] );
        if(empty($param['perpage'])){
            $param['perpage'] = 20;
        }
        $paginator->setItemCountPerPage ( $param['perpage'] );
        
        $viewData ['paginator'] = $paginator;
        $viewData ['param'] = $param;
        $viewData['startDate'] = $startDate;
        $viewData['endDate'] = $endDate;
        $viewData = array_merge ($viewData, $param);
        
        return new ViewModel ($viewData);
        
        
    }
    
    public function deleteAction() {
        $identity = Auth::getIdentity();
        $userId = $identity['id'];
        $result = array();
        $result['code'] = -2;
        $obj = new WfWork();
        $id = (int)$this->params()->fromPost("id", 0);
        $row = $obj->getRowById($id);
        if($row && $row['user_id'] == $userId){
            $data = $this->objToArray($row);
            $data['is_delete'] = 1;
            $obj->updateRow($id,$data);
            $result['code'] = 0;
        }
        echo json_encode($result);
        exit;
        
    }
}