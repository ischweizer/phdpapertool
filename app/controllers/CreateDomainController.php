<?php
/**
 * Description of CreateDomainController
 *
 * @author jost
 */
class CreateDomainController extends BaseController {
	#
	/*public function index() {
		if(!Input::has('upperDomainId') && !Input::has('university'))
			return null;
		$upperDomainId = Input::get('upperDomainId');
		if(Input::has('group'))
			return $this::createGroup(Input::get('group'), $upperDomainId);
		if(Input::has('lab'))
			return $this::createLab(Input::get('lab'), $upperDomainId);
		if(Input::has('department'))
			return $this::createDeparment(Input::get('department'), $upperDomainId);
		if(Input::has('university'))
			return $this::createUniversity(Input::get('university'));
		return null;
	}*/

	public function index() {
		if(!Input::has('groupName'))
			return "Missing parameter groupName";
		if(Auth::user()->group_id != null && Group::find(Auth::user()->group_id)->active != 1 && UserRole::getUserRole(UserRole::SUPER_ADMIN) == null)
			return "You cannot create a new group/lab, because your last group/lab creation is still pending";
		if(!Input::has('labId')) {
			if(!Input::has('labName'))
				return "Missing parameter labName";
			$lab = Lab::where('name', '=', Input::get('labName'))->first();
			if($lab == null) {
				$labId = $this::createLab(Input::get('labName'))->id;
				$labCreated = true;
			}
			else {
				$labId = $lab->id;
				$labCreated = false;
			}

		} else {
			$labId = Input::get('labId');
			$labCreated = false;
		}
		$result = $this::createGroup(Input::get('groupName'), $labId, $labCreated);
		return $result;
	}

	private function createGroup($name, $labId, $labCreated) {
		if(!$labCreated && Lab::find($labId)->active == 0)
			return "Lab is not active";
		$isGroupActive = UserRole::getUserRole(UserRole::LAB_LEADER) != null;
		$group = Group::where('name', '=', $name)->where('lab_id', '=', $labId)->first();
		if($group == null) {
			$group = new Group;
			$group->name = $name;
			$group->lab_id = $labId;
			$group->active = $isGroupActive;
			$group->save();
			//mail sending
			$lab = $group->lab;
			$admin = $lab->getAdmin();
			$author = $admin->author;
			$authorName = $author->first_name.' '.$author->last_name;
			$user = Auth::user();
			Mail::send('emails/group_created', array('user' => $user), function($message) use ($author, $authorName) {
				$message->to($author->email, $authorName)
					->subject('group creation')
					->from('noreply@da-sense.de', 'PHDPapertool');
			});
		}
		$user = Auth::user();
		$user->group_confirmed = $isGroupActive;
		$user->group_id = $group->id;
		$user->save();
		//$this::updateRole(UserRole::GROUP_LEADER, $isGroupActive);
		return "true";
	}

	private function createLab($name) {//, $departmentId) {
		$lab = new Lab;
		$lab->name = $name;
		//$lab->department_id = $departmentId;
		$lab->active = 0;
		$lab->save();
		//$this::updateRole(UserRole::LAB_LEADER, false);

		//Mail an Super-Admin schicken.
		$author = Author::findOrFail(1);
		$authorName = $author->first_name.' '.$author->last_name;
		$user = Auth::user();
		Mail::send('emails/lab_created', array('user' => $user), function($message) use ($author, $authorName) {
			$message->to($author->email, $authorName)
				->subject('lab creation')
				->from('noreply@da-sense.de', 'PHDPapertool');
		});

		return $lab;
	}

   /* private function updateRole($roleId, $isActive) {
		$role = UserRole::getUserRole($roleId);
		if($role != null) {
			$role->active = $isActive;
			return $role;
		}
		$newRole = new User_role;
		$newRole->user_id = Auth::user()->id;
		$newRole->role_id = $roleId;
		$newRole->active = $isActive;
		$newRole->save();
		return $newRole;
	}*/

   /*private function createDepartment($name, $universityId) {
		$department = new Department;
		$department->name = $name;
		$department->university_id = $universityId;
		$department->save();
		return $department;
	}

	private function createUniversity($name) {
		$university = new University;
		$university->name = $name;
		$university->save();
		return $university;
	}*/
}
