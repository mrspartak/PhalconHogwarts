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
		
		$this->assets
			->collection('cssHeader')
			->setTargetPath('css/final.css')
			->setTargetUri('css/final.css')
			->addCss('css/bootstrap.min.css')
			->addCss('css/bootstrap-theme.min.css')
			->addCss('css/select2.css')
			->addCss('css/select2-bootstrap.css')
			->addCss('css/style.css')
			->join(true)
			->addFilter(new Phalcon\Assets\Filters\Cssmin());
			
		$this->assets
			->collection('jsHeader')
			->setTargetPath('js/final.js')
			->setTargetUri('js/final.js')
			->addJs('js/jquery-1.10.1.min.js')
			->addJs('js/jquery.color-2.1.2.min.js')
			->addJs('js/bootstrap.min.js')
			->addJs('js/select2.min.js')
			->addJs('js/jquery-sortable-min.js')
			->addJs('js/script.js')	
			->join(true)
			->addFilter(new Phalcon\Assets\Filters\Jsmin());
		
	}
}


