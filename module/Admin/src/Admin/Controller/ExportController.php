<?php
namespace Admin\Controller;

use Admin\Model\Controller;
use Admin\Model\User;






use Admin\Model\WfProject;
use Admin\Model\WfType;
use Admin\Model\WfWork;
use Zend\View\Model\ViewModel;


class ExportController extends AbstractController {

    protected $_existMessage = '用户名已存在';
    
    protected $_listPath = null;

    public function listAction(){
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

}