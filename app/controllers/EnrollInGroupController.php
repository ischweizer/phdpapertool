<?php
/**
 * Description of ErrolInGroupController
 *
 * @author jost
 */
class EnrollInGroupController extends BaseController {

	public function index() {
		if(Request::ajax())
			return $this::getDomain();
		return $this::showLabs();
	}

	public function enroll() {
		if(Auth::user()->group_id == null)
			$group = null;
		else {
			$group = Group::find(Auth::user()->group_id);
			$oldGroupLabId = $group->lab_id;
		}
		if(!Input::has('group') || !Auth::check() || ($group != null && $group->active != 1))
			return Response::json(false);
		$groupId = Input::get('group');
		$group = Group::find($groupId);
		if($group == null || $group->active != 1)
			return Response::json(false);
		$user = Auth::user();
		$user->group_confirmed = 0;
		$role = UserRole::where('user_id', '=', $user->id)->first();
		if($role != null) {
			if($role->role_id == UserRole::GROUP_LEADER)
				$role->delete();
			else if($role->role_id == UserRole::LAB_LEADER) {
				if($oldGroupLabId != $group->lab_id)
					$role->delete();
				else
					$user->group_confirmed = 1;
			}

		}
		if($user->group_confirmed == 0){
			//send Mail
			$admin = $group->getAdmin();
			$author = $admin->author;
			$authorName = $author->first_name.' '.$author->last_name;
			$user = Auth::user();
			Mail::send('emails/enroll_in_group_request', array('user' => $user, 'group' => $group), function($message) use ($author, $authorName) {
				$message->to($author->email, $authorName)
					->subject('join group request')
					->from('noreply@da-sense.de', 'PHDPapertool');
			});
		}
		$user->group_id = $groupId;
		$user->save();
		return Response::json(true);
	}

	public function showLabs() {
		//TODO Nur active Labs
		$labs = Lab::where('active', '=', '1')->get();
		$groupAccepted = false;
		$user = Auth::user();

		if($user->hasGroup()){
			$labGroups = Group::where('active','=','1')->where('lab_id', '=', $user->group->lab_id)->get();
			$groupAccepted = $user->group_confirmed;
		} else {
			$labGroups = false;
		}



		return View::make('enroll_in_group')->with('labs', $labs)->with('labGroups', $labGroups)->with('groupAccepted', $groupAccepted);
	}

	public function getDomain() {

		//TODO Nur active groups
		if(Input::has('lab'))
			return $this::getGroups(Input::get('lab'));
		/*if(Input::has('department'))
			return $this::getLabs(Input::get('department'));
		if(Input::has('university'))
			return $this::getDepartments(Input::get('university'));*/
		return null;
	}

	/*private function getDepartments($universityId) {
		$departments = Department::where('university_id', '=', $universityId)->get();
		return Response::json($departments);
	}*/

	/*private function getLabs($departmentId) {
		$labs = Lab::where('department_id', '=', $departmentId)->get();
		return Response::json($labs);
	}*/

	private function getGroups($labId) {
		$groups = Group::where('active','=','1')->where('lab_id', '=', $labId)->get();
		return Response::json($groups);
	}
}
