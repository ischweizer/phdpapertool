<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<div>
		    Dear {{{ $name }}},<br>
			You have been registered to 'PHDPapertool'. To confirm  
			the registration and to activate your account you have to 
			click the following link:<br>
			<br>
			{{link_to_action('RegistrationController@activate', 'Activation link', array('email' => $email, 'code' => $code), array())}}<br>
			 <br>
			 Regards,<br>
			The PHDPapertool-Team
		</div>
	</body>
</html>
