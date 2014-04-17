<?php

class IndexController extends BaseController {
	
	public function initialize()
	{
		parent::initialize();
	}
	
	public function IndexAction() {
		$faculties = Faculty::find();
		
		$this->view->setVar('faculties', $faculties);
		$this->view->setVar('filteredWorks', WorkItems::filterWorks());
	}
	
	public function LogoutAction() {
		$this->view->disable();
		UserAdapter::logOut();
		
		$userAdapter = new UserAdapter();
		$aclAdapter = new AclAdapter();
		$defaultRoute = $aclAdapter->getDefaultRoute($userAdapter);
		$this->response->redirect($defaultRoute[0] ."/". $defaultRoute[1]);
	}
	
	public function OauthAction() {
		$this->view->disable();
		$s = file_get_contents('http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $_SERVER['HTTP_HOST']);
    $user = json_decode($s, true);
		
		$student = Students::findFirst($user['uid']);
		if($student) {
			UserAdapter::logIn($student);
		} else {
			$student = new Students();
			$student->uid = $user['uid'];
			$student->fn = $user['first_name'];
			$student->ln = $user['last_name'];
			$student->userpic = $user['photo'];
			$student->admin = 0;
			
			if($student->save()) {
				UserAdapter::logIn($student);
			}
		}
		
		$userAdapter = new UserAdapter();
		$aclAdapter = new AclAdapter();
		$defaultRoute = $aclAdapter->getDefaultRoute($userAdapter);
		$this->response->redirect($defaultRoute[0] ."/". $defaultRoute[1]);
	}
	
	public function testAction() {
		$this->view->disable();
		
		$works = WorkItems::find();
		$students = Students::find();
		$works_c = count($works) - 1;
		$students_c = count($students) - 1;
		
		for($i=0; $i<100; $i++) {
			$workDone = new WorkDone();
			$workDone->student_from = $students[rand(0, $students_c)]->uid;
			$workDone->student_to 	= $students[rand(0, $students_c)]->uid;
			$workDone->work 				= $works[rand(0, $works_c-5)]->id;
			
			$workDone->date = new Phalcon\Db\RawValue('now()');
			$workDone->save();
		}
	}
}


