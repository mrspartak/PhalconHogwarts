<?php

class WorkController extends BaseController {
	
	public function initialize()
	{
		parent::initialize();
	}
	
	public function IndexAction() {
		$isPost = $this->request->isPost();
		if($isPost) {
			$data = $this->request->getPost('data');
			
			$name = new \Prepare\String($data['name']);
			$name->trim();
			if(strlen($name->get()) == 0)
				$errors[] = 'Название должно быть не пустым';
			
			$type = $data['type'];
			if(!in_array($type, array(1, -1)))
				$errors[] = 'Неверный тип дела';
			
			$cost = (int) $data['cost'];
			if($cost <= 0 or $cost > 10)
				$errors[] = 'Оценка не должна превышать 10';
			
			if(empty($errors)) {
				$workItem = new WorkItems();
				$workItem->name = $name->get();
				$workItem->type = $type;
				$workItem->cost = $cost;
				if($workItem->save() === false) {
					$errors[] = 'Ошибка сохранения';
				} else {
					$this->view->disable();
					$this->response->redirect('work/index')->sendHeaders();
					return false;
				}
			}
		}
		
		$this->view->setVar('filteredWorks', WorkItems::filterWorks());
		$this->view->setVar('errors', $errors);
		if(!empty($errors))
			$this->view->setVar('data', $data);
	}
}


