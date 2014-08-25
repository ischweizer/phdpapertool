<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<div>
		    @if(isset($name))
			Dear {{{ $name }}},
		    @else
			Hi,
		    @endif
		    <br>
		    @yield('mailContent')
		    <br><br>
		    Please do not reply to this email. Mails sent to this address 
		    cannot be answered.<br>
		    <br>
		    Regards,<br>
		    The PHDPapertool-Team
		</div>
	</body>
</html>
