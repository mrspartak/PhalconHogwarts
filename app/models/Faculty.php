<?php

class Faculty extends \Phalcon\Mvc\Model
{

	public function setSource()
	{
		return 'faculty';
	}
	
	public function initialize()
	{
		$this->hasMany("id", "Students", "faculty");
	}
	
	public function getScore() {
		$faculty = Faculty::findFirst($this->id);
		$modelsManager = Phalcon\DI::getDefault()->getModelsManager();
		
		$works = $modelsManager
			->createBuilder()
			->columns(array('SUM( CAST( wi.cost AS signed ) * wi.type ) as sum'))
			->from(array('wd' => 'WorkDone'))
    	->join('WorkItems', 'wi.id = wd.work', 'wi')
			->join('Students', 'st.uid = wd.student_to', 'st')
			->where('st.faculty = '. $faculty->id)
			->andWhere('MONTH( wd.date ) = MONTH( NOW() )')
			->getQuery()
    	->execute();
		
		$score = ($works[0]->sum) ? $works[0]->sum : 0;
		
		return $score;
	}
}