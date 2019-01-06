<?php
namespace Admin\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Admin\Model\WfTag;
use Admin\Model\WfProject;
class GetProductById extends AbstractHelper
{
    public function __invoke($id){
        
        $obj = new WfProject();
        return $obj->getRowById($id);
    }
}
