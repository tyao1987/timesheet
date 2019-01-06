<?php
namespace Admin\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Admin\Model\WfArea;
class GetAreaById extends AbstractHelper
{
    public function __invoke($id){

	    $obj = new WfArea();
	    return $obj->getRowById($id);
	}
}
