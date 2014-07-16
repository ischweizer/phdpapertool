<?php

class Lab extends Eloquent {
	public function groups()
	{
		return $this->hasMany('Group');
	}

	public static function getLabs($groups) {
		$labs = Lab::where('id', '!=', 1)->get();;
		if($groups == null)
			return $labs;
		$result = array();
		foreach($labs as $lab) {
			foreach($groups as $group) {
				if($lab->id == $group->lab_id && $lab->id != 1)
					$result[$lab->id] = $lab;
			}     
		}
		return $result;
	}
}
