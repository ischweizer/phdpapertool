<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Database\Eloquent\Collection;

class User extends Eloquent implements UserInterface, RemindableInterface {

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the token value for the "remember me" session.
	 *
	 * @return string
	 */
	public function getRememberToken()
	{
		return $this->remember_token;
	}

	/**
	 * Set the token value for the "remember me" session.
	 *
	 * @param  string  $value
	 * @return void
	 */
	public function setRememberToken($value)
	{
		$this->remember_token = $value;
	}

	/**
	 * Get the column name for the "remember me" token.
	 *
	 * @return string
	 */
	public function getRememberTokenName()
	{
		return 'remember_token';
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

	public function author()
	{
		return $this->belongsTo('Author');
	}

	public function group()
	{
		return $this->belongsTo('Group');
	}

	public function userRoles()
	{
		return $this->hasMany('UserRole');
	}

	/**
	 * Review requests created by this user.
	 */
	public function createdReviewRequests()
	{
		return $this->hasMany('ReviewRequest');
	}

	/**
	 * Returns true iff this user is already enrolled in a Group, or the approval is pending
	 */
	public function hasGroup()
	{
		return $this->group_id != NULL;
	}

	/**
	 * Returns true iff this user has a pending(not accepted or refused) lab or group creation and is not a super admin
	 */
	public function hasPendingCreation()
	{
		return ($this->group_id != null && Group::find($this->group_id)->active != 1 && UserRole::getUserRole(UserRole::SUPER_ADMIN) == null);
	}

	/**
	 * Returns true iff the user is some kind of admin (group/lab/super)
	 */
	public function isAdmin()
	{
		return (count($this->userRoles) > 0);
	}

	/**
	 * return the name of this as a readable String
	 */
	public function formatName()
	{
		return $this->author->first_name." ".$this->author->last_name." (".$this->email.")";
	}

	public function scopeNotAdmin($query) {
		return $query->where('id', '<>', '1');
	}

	/**
	 * returns true iff this is a lab leader
	 */
	public function isLabLeader()
	{
		foreach ($this->userRoles as $userRole) {
			if($userRole->role_id == UserRole::LAB_LEADER)
				return true;
		}
		return false;
	}

	/**
	 * returns true iff this is a group leader
	 */
	public function isGroupLeader()
	{
		foreach ($this->userRoles as $userRole) {
			if($userRole->role_id == UserRole::GROUP_LEADER)
				return true;
		}
		return false;
	}

	/**
	 * returns true iff this is a Super Admin
	 */
	public function isSuperAdmin()
	{
		foreach ($this->userRoles as $userRole) {
			if($userRole->role_id == UserRole::SUPER_ADMIN)
				return true;
		}
		return false;
	}

	public static function getUnconfirmedUsers($groups) {
		$unconfirmedUsers = User::where('id', '!=', 1)->where('group_confirmed', '=', 0)->where('group_id', '!=', 'null')->get();
		if($groups == null)
			return $unconfirmedUsers;
		$result = array();
                foreach($unconfirmedUsers as $user) {
                    foreach($groups as $group) {
                        if($user->group_id == $group->id && $user->id != 1) {
                            $result[] = $user;
                            continue 2;
                        }
                    }
		}
		return $result;
	}
        
        public static function getUsers($groups, $exception = 0) {       
            $users =  User::where('id', '!=', 1)->where('group_id', '!=', 'null')->get();
            if($groups == null)
                return $users;
            $result = array();
            foreach($users as $user) {
                foreach($groups as $group) {
                    if($user->group_id == $group->id && $user->id != 1 && $user->id != $exception) {
                        $result[] = $user;
                        continue 2;
                    }
                }
            }
            return $result;
        }
	
	public function scopeFromAuthors($query, $authors) {
	    $authorsIds = array();
	    foreach($authors as $author)
		$authorsIds[] = $author->id;
	    $query->whereIn('users.author_id', $authorsIds);
	}
}
