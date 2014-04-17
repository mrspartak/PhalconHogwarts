<?php

class WorkItems extends \Phalcon\Mvc\Model
{

	public function setSource()
	{
		return 'work_items';
	}
	
	public function initialize()
	{
		
	}
	
	static function filterWorks() {
		$works = self::find(array(
			'order' => 'sort'
		));
		$filteredWorks = array(
			'1' => array(),
			'-1' => array()
		);
		foreach($works as $work) {			
			$filteredWorks[$work->type][] = $work;
		}
		return $filteredWorks;
	}
}