<?php

class Students extends \Phalcon\Mvc\Model
{

	public function setSource()
	{
		return 'students';
	}
	
	public function initialize()
	{
		$this->hasOne("faculty", "Faculty", "id");
		$this->hasMany("uid", "WorkDone", "student_to");
	}
}