<?php
namespace Admin\View\Helper;

use Admin\Model\WfProject;
use Zend\View\Helper\AbstractHelper;
class GetProductSelect extends AbstractHelper
{
    public function __invoke(){
        
        $obj = new WfProject();
        $list = $obj->getList(array('is_delete' => 0),array('id'=>'asc'));
        $str = "<select class=\"selectProduct\" style=\"height: 30px;\"><option value='0'>未选择</option>";
        foreach ($list as $row){
            $str.= "<option value=".$row['id'].">".$row['name']."</option>";
        }
        $str.= "</select>";
        return $str;
    }
}
