<?php
namespace Admin\Model;


use Application\Model\DbTable;

use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;



use Admin\Util\Util;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class WfWork extends DbTable
{
    protected $_defaultNullFilter = array();
	protected $_name = 'wf_work';

	function __construct(){
		$this->setTableGateway("cmsdb", $this->_name);
		$this->_select = $this->tableGateway->getSql()->select();
	}

	
	public function paginator($conditions = array(),$where,$order = array()) {
	    unset($conditions['page']);
	    unset($conditions['perpage']);
	    unset($conditions['start_date']);
	    unset($conditions['end_date']);
	    $dbAdapter = $this->tableGateway->getAdapter ();
	    $sql = new Sql ( $dbAdapter );
	    if ($conditions) {
	        foreach ($conditions as $key => $val) {
	            $this->_select->where($this->quoteInto("{$key} like ?", '%' .$val. '%'));
	        }
	    }
	    if($where){
	        $this->_select->where($where);
	    }
	    
	    if($order){
	        $this->_select->order($order);
	    }
	    
	    $adapter = new DbSelect ($this->_select, $sql);
	    $paginator = new Paginator ( $adapter );
	    return $paginator;
	    
	}
	
	public function insertRow($data){
	    if(isset($this->_defaultNullFilter)){
	        $data = Util::emptyToNull($data, $this->_defaultNullFilter);
	    }
	    unset($data['id']);
	    unset($data['submit']);
	    unset($data['cancel']);
	    $this->insert($data);
	    return $this->tableGateway->lastInsertValue;
	}
	
    public function getDbAdapter(){
        return $this->tableGateway->getAdapter();
    }
    
    public function getList($where,$order = array()){
        $data = $this->tableGateway->getAdapter();
        $select = new Select();
        $select->from($this->_name);
        $this->_select->where($where);
        if($order){
            $this->_select->order($order);
        }
        $sql = $this->_select->getSqlString($data->getPlatform());
        return $this->fetchAll($sql);
    }

	public function getRowById($id)
	{
	    $result = $this->fetchRow(array('id'=> $id));
	    return $result;
	}
	
	public function updateRow($id,$data){
	    unset($data['id']);
	    unset($data['submit']);
	    unset($data['cancel']);
	    $where[] = $this->quoteInto('id = ?', $id);
	    return $this->tableGateway->update($data, $where);
	}
	
	public  function load($where){
	    $sql = "SELECT `user_id`,`area`,`department`,`job`,`type_id`,`project_id`,`work_date`,`work_time` FROM `wf_work` WHERE `is_delete` = 0
    	    AND `work_date` >= '{$where['start_date']}'
    	    AND `work_date` <= '{$where['end_date']}' ORDER BY `work_date` ASC,id ASC ";
	    return $this->fetchAll($sql);
	    
	}
}