<?php

class BaseController extends \Phalcon\Mvc\Controller {
	
	public function initialize()
	{
		$this->session->start();
		$userAdapter = new UserAdapter();
		$aclAdapter = new AclAdapter();
		
		$allowed = $aclAdapter->check($userAdapter);
		if($allowed === false) {
			$defaultRoute = $aclAdapter->getDefaultRoute($userAdapter);
			$this->response->redirect($defaultRoute[0] ."/". $defaultRoute[1])->sendHeaders();
			return false;
		}
		
		$this->view->setVar('userAdapter', $userAdapter);
		$this->view->setVar('aclAdapter', $aclAdapter);
	}
}


