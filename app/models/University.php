<?php

class University extends Eloquent {
	public function departments()
	{
		return $this->hasMany('Department');
	}
}
