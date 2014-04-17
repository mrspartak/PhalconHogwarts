<?

class AclAdapter {
	
	private $_acl;
	private $defaultAction = Phalcon\Acl::DENY;
	private $roleParenting = true;
	
	private $restrictions = array(
		'guest' => array(
			'index' => array(
				'*'
			)
		),
		'enrollee' => array(
			'ajax' => array(
				'open'
			)
		),
		'student' => array(
			'ajax' => array(
				'close'
			),
			'log' => array(
				'*'
			)
		),
		'admin' => array(
			'ajax' => array(
				'admin'
			),
			'work' => array(
				'*'
			)
		)
	);
	private $defaultRoutes = array(
		'guest' => array('index', 'index'),
		'enrollee' => array('index', 'index'),
		'student' => array('index', 'index')
	);	
	
	public function __construct($cache = false) {
		$this->_acl = new \Phalcon\Acl\Adapter\Memory();
		$this->_acl->setDefaultAction($defaultAction);
		
		$this->setRoles();
		$this->setResources();
		$this->setRestrictions();
	}
	
	public function check($User, $controller = false, $action = false) {
		$dispatcher = Phalcon\DI::getDefault()->getDispatcher();
		if(!$controller)
			$controller = $dispatcher->getControllerName();
		if(!$action)
			$action = $dispatcher->getActionName();
		
		return $this->_acl->isAllowed($User->role, $controller, $action);
	}
	
	public function getDefaultRoute($User) {
		$route = $this->defaultRoutes[$User->role];
		if(!$route) $route = array('index', 'index');
		
		return $route;
	}
	
	private function setRoles(){
		foreach($this->restrictions as $role => $resources) {
			$tmpRole = new \Phalcon\Acl\Role($role);
			if($this->roleParenting === true) {
				$this->_acl->addRole($tmpRole, $dumpRole);
				$dumpRole = $tmpRole;
			} else 
				$this->_acl->addRole($tmpRole);
		}
	}
	
	private function setResources(){
		foreach($this->restrictions as $role => $resources) {
			foreach($resources as $controller => $actions) {
				$tmpResource = new \Phalcon\Acl\Resource($controller);
				$this->_acl->addResource($tmpResource, $actions);
			}
		}
	}
	
	private function setRestrictions(){
		foreach($this->restrictions as $role => $resources) {
			foreach($resources as $controller => $actions) {
				foreach($actions as $i => $action) {
					$this->_acl->allow($role, $controller, $action);
				}
			}
		}
	}
}