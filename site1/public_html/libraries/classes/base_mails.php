<?php
#*************************************************************
function send_mail($myname, $myemail, $contactemail, $subject, $message, $html) {
	$message=delback($message);
	$message=str_replace('<font color="green"><b>&#8730</b></font>','да',$message);
	$message=str_replace('<font color="red"><b>X</b></font>','нет',$message);
	if(!$html) {
		$message=decode($message);
	}
    $headers='From: =?UTF-8?b?'.base64_encode($myname).'?= <notify@allrpg.info>' . "\r\n"
                 . 'Reply-To: '.$myemail . "\r\n";

    $contactemail=preg_replace("/[\r\n]/", " ", $contactemail);
    $subject=preg_replace("/[\r\n]/", " ", $subject);

	if($html) {
		$headers.='Content-type: text/html; charset="UTF-8"' . "\r\n";
	}
	else {
		$headers.='Content-type: text/plain; charset="UTF-8"' . "\r\n";
	}
	$headers.='Content-Transfer-Encoding: base64' . "\r\n";
	return(mail($contactemail, $subject, base64_encode($message), $headers));
}
#*************************************************************
function subssend($myname,$myemail,$subject,$contactemail) {
	global
		$_POST;

	if(encode($_POST["msg"])!='' && strlen(encode($_POST["msg"]))>9) {
		$message='<html>
<body>
'.$_POST["msg"].'
</body>
</html>';
		if($myname!='' && $myemail!='' && $contactemail!='' && $message!='') {
			if(send_mail($myname, $myemail, $contactemail, $subject, $message, true)) {
				$_POST["msg"]='';
				return(true);
			}
			else {
				return(false);
			}
		}
		else {
			err_red("Не заполнено одно из обязательных для отправки полей.");
			return(false);
		}
	}
	else {
		err_red("Текст сообщения должен быть не менее 10 символов.");
		return(false);
	}
}
?>