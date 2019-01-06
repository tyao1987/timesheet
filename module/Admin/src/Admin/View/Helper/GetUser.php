<?php
namespace Admin\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Admin\Model\Auth;
use Zend\Authentication\Storage\Session as sessionStorage;
use Admin\Model\User;
class GetUser extends AbstractHelper
{
	public function __invoke($userId){

	    $user = new User();
	    return $user->getUserById($userId);
	}
}
