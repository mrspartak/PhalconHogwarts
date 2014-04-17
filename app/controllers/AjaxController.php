<?php

class AjaxController extends BaseController {
	public $action;
	public $data;
	
	public function initialize()
	{
		parent::initialize();
		$this->view->disable();
		
		if($this->config->app->debug = 1) {
			$action = $this->request->getPost('action');
			$data   = $this->request->getPost('data');
			
			$action = ($action) ? $action : $this->request->get('action');
			$data = ($data) ? $data : $this->request->get('data');
		} else {
			if (!$app->request->isAjax()) {
				$app->response->setStatusCode(404, "Not Found")->sendHeaders();
				return false;
			}
			$action = $this->request->getPost('action');
			$data   = $this->request->getPost('data');
		}
		
		$this->action = $action;
		$this->data = $data;
	}
	
	public function OpenAction() {
		$response = array();
		switch($this->action) {
			case 'applyForAdmission':
				$faculty = $this->data['faculty'];
				$userAdapter = new UserAdapter();
				$user = $userAdapter->getUser();
				
				$facultyObj = Faculty::findFirst($faculty);
				if(!$facultyObj) {
					$response = array(
						'error' => 'Нет такого факультета.'
					);
					break;
				}
				$user->faculty = $faculty*-1;
				if($user->save())
					$response = array(
						'faculty' => $faculty
					);
				else
					$response = array(
						'error' => 'Ошибка сохранения в базу данных.'
					);
			break;
			
			default:
			$this->response->setStatusCode(404, "Not Found")->sendHeaders();
		}
		
		echo json_encode($response);
	}
	
	public function CloseAction() {
		$response = array();
		switch($this->action) {
			case 'markWork':
				$userAdapter = new UserAdapter();
				$user = $userAdapter->getUser();
				
				$student = $this->data['student'];
				$checkStudent = Students::findFirst($student);
				if(!$checkStudent) {
					$response['error'] = 'Нет такого студента';
					break;
				}
				if($checkStudent->faculty == $user->faculty) {
					$response['error'] = 'Дело можно выставить только другому факультету';
					break;
				}
				
				$work = $this->data['work'];
				$checkWork = WorkItems::findFirst($work);
				if(!$checkWork) {
					$response['error'] = 'Нет такой работы';
					break;
				}				
				
				list($result, $work) = WorkDone::addWork(array(
					'student_from' => $user,
					'student_to' => $checkStudent,
					'work' => $checkWork
				));
				if($result === false) {
					foreach ($work->getMessages() as $message) {
        		$response['error'] .= $message. "\n";
    			}
					break;
				}
				
				$response[] = 'ok';	
			break;
			
			default:
			$this->response->setStatusCode(404, "Not Found")->sendHeaders();
		}
		
		echo json_encode($response);
	}
	
	public function AdminAction() {
		$response = array();
		switch($this->action) {
			case 'saveWork':
				$field = $this->data['field'];
				if($field == 'name') {
					$value = new \Prepare\String($this->data['data']);
					$value->trim();
					$value = $value->get();
					if(strlen($value) == 0)
						$errors[] = 'Название должно быть не пустым';
				} elseif($field == 'cost') {
					$value = (int) $this->data['data'];
					if($value <= 0 or $value > 10)
						$errors[] = 'Оценка не должна превышать 10';
				} else
					$errors[] = 'Неверное поле';
				
				$work = WorkItems::findFirst($this->data['id']);
				if(!$work)
					$errors[] = 'Нет записи';
				
				if(!$errors) {
					$work->$field = $value;
					$result = $work->save();
					if($result === false)
						$errors[] = 'Ошибка сохранения';
				}
				
				if($errors)
					$response['error'] = $errors;
				else
					$response[] = 'ok';	
			break;
			
			default:
			$this->response->setStatusCode(404, "Not Found")->sendHeaders();
		}
		
		echo json_encode($response);
	}
}


