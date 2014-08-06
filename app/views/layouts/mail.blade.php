<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<div>
		    Dear {{{ $name }}},<br>
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
