<?php
namespace Admin\View\Helper;

use Admin\Model\WfType;
use Zend\View\Helper\AbstractHelper;
class GetTypeSelect extends AbstractHelper
{
    public function __invoke($noProduct = true){
        
        $obj = new WfType();
        $where = array();
        if($noProduct){
            $where['id > ?'] = 2;
        }else{
            $where['id < ?'] = 3;
        }
        $list = $obj->getList($where,array('id'=>'asc'));
        $str = "<select style=\"height: 30px;\">";
        foreach ($list as $row){
            $str.= "<option value=".$row['id'].">".$row['name']."</option>";
        }
        $str.= "</select>";
        return $str;
    }
}
