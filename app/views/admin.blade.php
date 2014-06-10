@extends('layouts/main')

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
							<a href="confirm?{{{ $var }}}"><span class="glyphicon glyphicon-ok"></span></a>
							<a href="refuse?{{{ $var }}}"><span class="glyphicon glyphicon-remove"></span></a>
						</span> 
					</li>
				@endif
			@endforeach
		</ul>
	</div>
	@if ($roleId == UserRole::SUPER_ADMIN)
		admin
	@elseif ($roleId == UserRole::LAB_LEADER)
		<h1>
			Lab: {{{reset($labs)->name}}}
		</h1>
		<h3>
			Admins
		</h3>
		<ul class="list-group">
			@foreach ($users as $user)
				@if ($user->isAdmin())
					<li class="list-group-item">
						<span>
							{{{$user->formatName()}}}
						</span>
						<span style="float:right">
							@if ($user->isLabLeader())
								Lab Leader
							@elseif ($user->isGroupLeader())
								<a href=""><span class="glyphicon glyphicon-ban-circle" title="remove admin rights"></span></a> 
							@endif
						</span>
					</li>
				@endif
			@endforeach
		</ul>
		<h1>
			Groups
		</h1>
		@foreach ($groups as $group)
			<h2>
				Group: {{{$group->name}}}
			</h2>
			<h3>
				Members
			</h3>
			<ul class="list-group">
				@foreach ($users as $user)
					@if ($user->group_confirmed && $user->group_id == $group->id)
						<li class="list-group-item">
							<span>
								{{{$user->formatName()}}}
							</span>
							<span style="float:right">
								@if ($user->isLabLeader())
									Lab Leader
								@elseif ($user->isGroupLeader())
									Group Leader
								@else
									<a href=""><span class="glyphicon glyphicon-ban-circle"></span></a>
								@endif
							</span>
						</li>
					@endif
				@endforeach
			</ul>
		@endforeach
	@elseif ($roleId == UserRole::GROUP_LEADER) 
		<h1>
			Group: {{{reset($groups)->name}}}
		</h1>
		<h3>
			Members
		</h3>
		<ul class="list-group">
			@foreach ($users as $user)
				@if ($user->group_confirmed)
					<li class="list-group-item">
						<span>
							{{{$user->formatName()}}} 
						</span>
						<span style="float:right">
							@if ($user->isLabLeader())
								Lab Leader
							@elseif ($user->isGroupLeader()) 
								Group Leader
							@else
								<a href="refuse?userId={{{$user->id}}}"><span class="glyphicon glyphicon-ban-circle" title="remove from group"></span></a>
							@endif
						</span>
					</li>
				@endif
			@endforeach
		</ul>
	@endif
@stop
	