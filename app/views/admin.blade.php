@extends('layouts/main')

@section('content')
    <div>
        <h1>
            New Requests
        </h1>
        <ul class="list-group">
            @foreach($users as $user)
                <li class="list-group-item">
                    <?php
                        $group = $groups[$user->group_id];
                        $lab = $labs[$group->lab_id];
                        if($group->active == 1) {
                            $text = $user->email." would like to join group '".$group->name."' in lab '".$lab->name."'";
                            $var = "userId=".$user->id;
                        } else if($lab->active == 1) {
                            $text = $user->email." would like to create group '".$group->name ."' in lab '".$lab->name."'";
                            $var = "groupId=".$group->id;
                        } else {
                            $text = $user->email." would like to create lab '".$lab->name."' with new group '".$group->name."'";
                            $var = "labId=".$lab->id;
                        }
                    ?>                
                    <div align="right"><a href="confirm?{{{ $var }}}"><span class="glyphicon glyphicon-ok"></span></a>
                        <a href="refuse?{{{ $var }}}"><span class="glyphicon glyphicon-remove"></span></a></div> 
                    <div algin="left">{{{ $text }}}</div>
                </li>
            @endforeach
        </ul>
    </div>
    
@stop
    