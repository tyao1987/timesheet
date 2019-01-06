<?php
namespace Admin\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Admin\Model\Role;
class GetRoleSelect extends AbstractHelper
{
    public function __invoke($userId = 0){
        
        $obj = new Role();
        $where = array();
        $list = $obj->getList($where,array('id'=>'asc'));
        $str = "<select multiple=\"multiple\" name=\"role_id\" id=\"role_id\" class=\"form-control\" required=\"required\">";
        foreach ($list as $row){
            $str.= "<option value=".$row['id'].">".$row['name']."</option>";
        }
        $str.= "</select>";
        return $str;
    }
}
