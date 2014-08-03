<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<div>
		    Dear {{{ $name }}},<br>
		    This is a reminder of some dates concerning your papers.<br>
		    <br>
		    @foreach($contents as $content)
			<?php
			    if($content["entry"][$content["attrName"]] == Carbon::today())
				$timeForm = "today";
			    else if($content["entry"][$content["attrName"]] == Carbon::tomorrow())
				$timeForm = "tomorrow";
			    else
				$timeForm = "on ".
				    $content["entry"][$content["attrName"]]->year."-".
				    $content["entry"][$content["attrName"]]->month."-".
				    $content["entry"][$content["attrName"]]->day;
			?>
			@if($content["tableName"] == "review_requests")
			    There is request for a review on the paper '{{{$content["papers"][0]->title}}}'
			    which deadline is {{{$timeForm}}}.
			    <br>
			@elseif($content["tableName"] == "events")
			    @if($content["attrName"] == "start")
				@if($content["entry"]->detail_type == "Workshop")
				    The workshop "{{{$entry["workshop"]->name}}}" 
				    of conference "{{{$entry["conference"]->name}}}"
				    starts {{{$timeForm}}}.
				@elseif($content["entry"]->detail_type == "ConferenceEdition")
				    The conference "{{{$content["conference"]->name}}}"
				    starts {{{$timeForm}}}.
				@endif
			     @elseif($content["attrName"] == "end")
				@if($content["entry"]->detail_type == "Workshop")
				    The workshop "{{{$content["workshop"]->name}}}" 
				    of conference "{{{$content["conference"]->name}}}"
				    ends {{{$timeForm}}}.
				@elseif($content["entry"]->detail_type == "ConferenceEdition")
				    The conference "{{{$content["conference"]->name}}}"
				    ends {{{$timeForm}}}.
				@endif 
			    @else
				@foreach($content["papers"] as $paper)
				    @if($content["attrName"] == "abstract_due")
					The abstract submission deadline for your paper
					"{{{$paper->title}}}" is {{{$timeForm}}}.
				    @elseif($content["attrName"] == "paper_due")
					The paper submission deadline for your paper
					"{{{$paper->title}}}" is {{{$timeForm}}}.
				    @elseif($content["attrName"] == "notification_date")
					The notification date for your paper
					"{{{$paper->title}}}" is {{{$timeForm}}}.
				    @elseif($content["attrName"] == "camera_ready_due")
					The camera ready submission deadline for your paper
					"{{{$paper->title}}}" is {{{$timeForm}}}.
				    @endif
				    <br>
				@endforeach
			    @endif
			    <br>
			@endif
			<br>
		    @endforeach
		    <br>
		    Please do not reply to this email. Mails sent to this address 
		    cannot be answered.<br>
		    <br>
		    Regards,<br>
		    The PHDPapertool-Team
		</div>
	</body>
</html>

