<?php
namespace Admin\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Admin\Model\WfDepartment;
class GetDepartmentById extends AbstractHelper
{
    public function __invoke($id){
        
        $obj = new WfDepartment();
        return $obj->getRowById($id);
    }
}
