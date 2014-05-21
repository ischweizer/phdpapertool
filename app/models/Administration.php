<?php

class Administration extends Eloquent implements ManagedLevel {
	public function universities()
	{
		return $this->hasMany('University');
	}

	public function getInactiveUsersQuery()
	{
		// no need to join any other tables as there is only one administration
		return User::where('active', '=', '0')->select('users.*');
	}
}
