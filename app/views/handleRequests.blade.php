@extends('layouts/main')
@section('head')
<?php
function getGroup($user, $groups) {
    foreach($groups as $group) {
        if($user->group_id == $group->id)
            return $group;
    }
    return null;
}

function getLab($group, $labs) {
    foreach($labs as $lab) {
        if($group->lab_id == $lab->id)
            return $lab;
    }
    return null;
}
?>
@stop

@section('content')
    <ul class="list-group">
        @foreach($users as $user)
            <li class="list-group-item">
                <?php
                    $group = getGroup($user, $groups);
                    $lab = getLab($group, $labs);
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
                <div align="right"><a href="confirm?<?php echo $var; ?>"><span class="glyphicon glyphicon-ok"></a></span>
                    <a href="refuse?<?php echo $var; ?>"><span class="glyphicon glyphicon-remove"></span></a></div> 
                <div algin="left"><?php echo $text; ?></div>
            </li>
        @endforeach
    </ul>
@stop
    