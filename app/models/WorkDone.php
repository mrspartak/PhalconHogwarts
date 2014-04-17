<?php

class WorkDone extends \Phalcon\Mvc\Model
{

	public function setSource()
	{
		return 'work_done';
	}
	
	public function initialize()
	{
		$this->hasOne("work", "WorkItems", "id");
	}
	
	static function addWork($data) {
		$work = new WorkDone();
		$work->student_from = $data['student_from']->uid;
		$work->student_to 	= $data['student_to']->uid;
		$work->work 				= $data['work']->id;
		$work->date = new Phalcon\Db\RawValue('now()');
		$result = $work->save();
		
		return array($result, $work);
	}
}