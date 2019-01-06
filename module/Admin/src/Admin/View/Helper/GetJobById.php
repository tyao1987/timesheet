<?php
namespace Admin\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Admin\Model\WfJob;
class GetJobById extends AbstractHelper
{
    public function __invoke($id){
        
        $obj = new WfJob();
        return $obj->getRowById($id);
    }
}
