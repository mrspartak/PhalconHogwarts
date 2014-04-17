<?php

class LogController extends BaseController {
	
	public function initialize()
	{
		parent::initialize();
	}
	
	public function IndexAction() {		
		$userAdapter = new UserAdapter();
		$user = $userAdapter->getUser();
		
		$works = $this->modelsManager
			->createBuilder()
			->columns(
				array(
					'wi.name',
					'wd.date',
					'CAST(wi.cost as signed) * wi.type as cost', 
					'CONCAT(st_to.fn, " ", st_to.ln) as st_to_name', 
					'CONCAT(st_from.fn, " ", st_from.ln) as st_from_name',
					'fc_to.name as fc_to_name',
					'fc_from.name as fc_from_name',
				)
			)
			->from(array('wd' => 'WorkDone'))
    	->join('WorkItems', 'wi.id = wd.work', 'wi')
			->join('Students', 'st_to.uid = wd.student_to', 'st_to')
			->join('Students', 'st_from.uid = wd.student_from', 'st_from')
			->join('Faculty', 'fc_to.id = st_to.faculty', 'fc_to')
			->join('Faculty', 'fc_from.id = st_from.faculty', 'fc_from')
			->where('st_to.faculty = '. $user->faculty)
			->andWhere('MONTH( wd.date ) = MONTH( NOW() )')
			->orderBy('wd.date DESC')
			->getQuery()
    	->execute();
		
		$this->view->setVar('works', $works);
	}
}


