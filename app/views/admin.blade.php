@extends('layouts/main')

@section('head')
	<script type="text/javascript">
	$(document).ready(function() {
		$('.glyphicon').tooltip();
	});
	</script>
@stop

@section('content')
	<div>
		<h1>
			New Requests
		</h1>
		<ul class="list-group">
			@foreach($users as $user)
				@if (!$user->group_confirmed)
					<li class="list-group-item">
						<?php
							$group = $groups[$user->group_id];
							$lab = $labs[$group->lab_id];
							if($group->active == 1) {
								$text = $user->formatName()." would like to join group '".$group->name."' in lab '".$lab->name."'";
								$var = "userId=".$user->id;
							} else if($lab->active == 1) {
								$text = $user->formatName()." would like to create group '".$group->name ."' in lab '".$lab->name."'";
								$var = "groupId=".$group->id;
							} else {
								$text = $user->formatName()." would like to create lab '".$lab->name."' with new group '".$group->name."'";
								$var = "labId=".$lab->id;
							}
						?>  
						<span>
							{{{ $text }}}
						</span>              
						<span style="float:right">
							<a href="confirm?{{{ $var }}}"><span class="glyphicon glyphicon-ok" title="confirm"></span></a>
							<a href="refuse?{{{ $var }}}"><span class="glyphicon glyphicon-remove" title="deny"></span></a>
						</span> 
					</li>
				@endif
			@endforeach
		</ul>
	</div>

	<div>
		@foreach ($labs as $lab)
			<h1>
				Lab: {{{$lab->name}}}
			</h1>
			@foreach ($groups as $group)
				<h2>
					Group: {{{$group->name}}}
				</h2>
				<h3>
					Member
				</h3>
				<table class="table">
					<tr>
						<th>Name</th>
						<th>Lab Leader</th>
						<th>Group Leader</th>
						<th>Remove</th>
					</tr>

					@foreach ($users as $user)
						@if ($user->group_confirmed && $user->group->lab_id == $lab->id && $user->group_id == $group->id)
							<tr>
								<td>
									{{{$user->formatName()}}}
								</td>
								<td>
									@if ($roleId == UserRole::SUPER_ADMIN)
										@if ($user->isLabLeader())
											<a href="removeRole?userId={{{$user->id}}}&roleId={{{UserRole::LAB_LEADER}}}"><span class="glyphicon glyphicon-ban-circle" title="remove lab leader rights"></span></a> 
										@else
											<a href="giveRole?userId={{{$user->id}}}&roleId={{{UserRole::LAB_LEADER}}}"><span class="glyphicon glyphicon-plus-sign" title="give lab leader rights"></span></a> 
										@endif
									@else
										 @if ($user->isLabLeader())
										 	<span class="glyphicon glyphicon-ok" title="you have no right to change this"></span>
										 @endif
									@endif
								</td>
								<td>
									@if ($roleId == UserRole::SUPER_ADMIN || ($roleId == UserRole::LAB_LEADER && !$user->isSuperAdmin() && !$user->isLabLeader()))
										@if ($user->isGroupLeader())
											<a href="removeRole?userId={{{$user->id}}}&roleId={{{UserRole::GROUP_LEADER}}}"><span class="glyphicon glyphicon-ban-circle" title="remove group leader rights"></span></a> 
										@elseif (!$user->isLabLeader())
											<a href="giveRole?userId={{{$user->id}}}&roleId={{{UserRole::GROUP_LEADER}}}"><span class="glyphicon glyphicon-plus-sign" title="give group leader rights"></span></a>
										@endif
									@else
										@if ($user->isGroupLeader())
											<span class="glyphicon glyphicon-ok" title="you have no right to change this"></span>
										@endif
									@endif
								</td>
								<td>
									@if ($roleId == UserRole::SUPER_ADMIN || ($roleId == UserRole::LAB_LEADER && !$user->isSuperAdmin() && !$user->isLabLeader()) || ($roleId == UserRole::GROUP_LEADER && !$user->isSuperAdmin() && !$user->isLabLeader() && !$user->isGroupLeader()))
										<a href="refuse?userId={{{$user->id}}}"><span class="glyphicon glyphicon-remove" title="remove from group"></span></a>
									@else
										<span class="glyphicon glyphicon-ok" title="you have no right to change this"></span>
									@endif
								</td>
							</tr>
						@endif
					@endforeach
				</table>
			@endforeach	
		@endforeach
	</div>
@stop
	