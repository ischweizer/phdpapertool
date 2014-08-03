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
			    $timeForm = $content["entry"][$content["attrName"]]->year."-".
				$content["entry"][$content["attrName"]]->month."-".
				$content["entry"][$content["attrName"]]->day;
			?>
			@if($content["tableName"] == "review_requests")
			    There is request for a review on the paper '{{{$content["papers"][0]->title}}}'
			    with deadline on {{{$timeForm}}}.
			@elseif($content["tableName"] == "events")
			    @if($content["attrName"] == "start")
				A {{{$content["entry"]->detail_type}}} starts 
				on {{{$timeForm}}}.
			    @elseif($content["attrName"] == "end")
				A {{{$content["entry"]->detail_type}}} ends 
				on {{{$timeForm}}}.
			    @elseif($content["attrName"] == "abstract_due")
				There is an abstract submission deadline on {{{$timeForm}}}.
			    @elseif($content["attrName"] == "paper_due")
				There is a paper submission deadline on {{{$timeForm}}}.
			    @elseif($content["attrName"] == "notification_date")
				There is a notification date on {{{$timeForm}}}.
			    @elseif($content["attrName"] == "camera_ready_due")
				There is a camera ready submission deadline on {{{$timeForm}}}.
			    @endif
			    <br>
			    Following papers are concerned:<br>
			    @foreach($content["papers"] as $paper)
				{{{$paper->title}}} 
			    @endforeach
			@endif
			<br>
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

