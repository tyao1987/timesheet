<?php
namespace Admin\View\Helper;

use Zend\View\Helper\AbstractHelper;
class GetResultOfEnum extends AbstractHelper
{
    public function __invoke($result){
		$return = "未知";
		if($result == 1){
			$return = "是";
		}
		if($result == 0){
			$return = "否";
		}
		return $return;
	}
}
