<?php
namespace Admin\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Admin\Model\WfType;
class GetTypeById extends AbstractHelper
{
    public function __invoke($id){
        
        $obj = new WfType();
        return $obj->getRowById($id);
    }
}
