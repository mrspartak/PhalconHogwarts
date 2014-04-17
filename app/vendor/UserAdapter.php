<?

class UserAdapter {
	
	private $session;
	public $role;
	
	public function __construct() {
		$this->session = Phalcon\DI::getDefault()->getSession();
		$this->getRole();
	}
	
	static function logIn($user) {
		$session = Phalcon\DI::getDefault()->getSession();
		$session->set("user", $user);
	}
	
	static function logOut() {
		$session = Phalcon\DI::getDefault()->getSession();
		$session->destroy();
	}
	
	public function loggedIn() {
		return ($this->role == 'guest') ? false : true;
	}
	
	public function getUser() {
		$user = $this->session->get("user");
		$uid = ($user->uid) ? $user->uid : 0;
		$user = Students::findFirst($uid);
		return $user;
	}
	
	public function getStudents() {
		$students = Students::find("faculty > 0");
		return $students;
	}
	
	private function getRole() {
		$role = 'guest';
		$user = $this->getUser();
		if($user) {
			if($user->admin == 1)
				$role = 'admin';
			elseif($user->faculty > 0)
				$role = 'student';
			elseif($user->uid > 0)
				$role = 'enrollee';
		}
		
		$this->role = $role;
	}
}