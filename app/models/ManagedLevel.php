<?php

interface ManagedLevel {

	/**
	 * Returns all users belonging to this level waiting for activation.
	 *
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function getInactiveUsersQuery();

}
