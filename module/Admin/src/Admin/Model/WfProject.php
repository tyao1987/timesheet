<?php
namespace Admin\Model;


use Application\Model\DbTable;

use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;



use Admin\Util\Util;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class WfProject extends DbTable
{
    protected $_defaultNullFilter = array();
	protected $_name = 'wf_project';

	function __construct(){
		$this->setTableGateway("cmsdb", $this->_name);
		$this->_select = $this->tableGateway->getSql()->select();
	}

	
	public function paginator($conditions = array()) {
	    unset($conditions['page']);
	    unset($conditions['perpage']);
	    $dbAdapter = $this->tableGateway->getAdapter();
	    $sql = new Sql ( $dbAdapter );
	    if ($conditions) {
	        foreach ($conditions as $key => $val) {
	            $this->_select->where($this->quoteInto("{$key} like ?", '%' .$val. '%'));
	        }
	    }
	    
	    $this->_select->order(array('id ASC'));
	    
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
            $select->order($order);
        }
        $sql = $this->_select->getSqlString($data->getPlatform());
        //echo $sql;exit;
        return $this->fetchAll($sql);
    }

	public function getRowById($id)
	{
	    $result = $this->fetchRow(array('id'=> $id));
	    return $result;
	}
	
	public function updateRowById($data,$id){
		$where[] = $this->quoteInto('id = ?', $id);
		return $this->tableGateway->update($data, $where);
	}
	
}