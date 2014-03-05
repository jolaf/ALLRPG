<?php
	// движок "Задай вопрос"
	$act="add";

	// Создание объекта
	$obj=new netObj(
		'help',
		$prefix."help",
		"вопрос",
		Array(),
		Array(),
		2,
		'100%',
		50
	);

	// Создание схемы прав объекта
	$obj_r=new netRight(
		true,
		true,
		false,
		false,
		100,
		'',
		'',
		''
	);
	$obj->setRight($obj_r);

	// Создание полей объекта

	if($_SESSION["user_id"]!='') {
		$result=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$_SESSION["user_id"]);
		$a = mysql_fetch_array($result);
	}

	$obj_1=createElem(Array(
				'name'	=>	"beforeask",
				'sname'	=>	"Прежде чем задать вопрос / сообщить об ошибке",
				'type'	=>	"wysiwyg",
				'default'	=>	'<ol><li>убедитесь в том, что Ваш вопрос не описан в статье <a href="'.$server_absolute_path_info.'articles/155/subobj=3">FAQ</a>.<br>
<li>убедитесь в том, что Вы используете последнюю версию браузера (Internet Explorer, Mozilla Firefox, Opera).
<li>заполните, пожалуйста, форму, идущую ниже.</ol>',
				'read'	=>	10,
				'write'	=>	100000,
			)
	);
	$obj->setElem($obj_1);

	$obj_2=createElem(Array(
				'name'	=>	"name",
				'sname'	=>	"Как Вас зовут",
				'type'	=>	"text",
				'read'	=>	10,
				'write'	=>	10,
				'default'	=>	decode($a["fio"]),
				'mustbe'	=>	true
			)
	);
	$obj->setElem($obj_2);

	$obj_3=createElem(Array(
				'name'	=>	"em",
				'sname'	=>	"Е-mail",
				'type'	=>	"email",
				'read'	=>	10,
				'write'	=>	10,
				'default'	=>	decode($a["em"]),
				'mustbe'	=>	true
			)
	);
	$obj->setElem($obj_3);

	$obj_4=createElem(Array(
				'name'	=>	"maintext",
				'sname'	=>	"Суть вопроса / ошибки",
				'type'	=>	"text",
				'read'	=>	10,
				'write'	=>	10,
				'mustbe'	=>	true,
			)
	);
	$obj->setElem($obj_4);

	$obj_5=createElem(Array(
				'name'	=>	"details",
				'sname'	=>	"Детальное описание вопроса / ошибки",
				'type'	=>	"textarea",
				'rows'	=>	10,
				'read'	=>	10,
				'write'	=>	10,
				'mustbe'	=>	true,
			)
	);
	$obj->setElem($obj_5);

	$obj_8=createElem(Array(
				'name'	=>	"project",
				'sname'	=>	"Название проекта",
				'type'	=>	"text",
				'help'	=>	"если ошибка происходит при работе с одним из Ваших проектов, укажите, пожалуйста, с каким.",
				'rows'	=>	10,
				'read'	=>	10,
				'write'	=>	10,
			)
	);
	$obj->setElem($obj_8);

	$obj_6=createElem(Array(
				'name'	=>	"link",
				'sname'	=>	"Ссылка на страницу",
				'type'	=>	"text",
				'help'	=>	"если ошибка происходит на какой-то конкретной странице, укажите ее здесь, пожалуйста.",
				'rows'	=>	10,
				'read'	=>	10,
				'write'	=>	10,
			)
	);
	$obj->setElem($obj_6);

	$obj_7=createElem(Array(
				'name'	=>	"technical",
				'sname'	=>	"Техническая информация",
				'type'	=>	"textarea",
				'help'	=>	"каким браузером Вы пользуетесь (Internet Explorer, Mozilla Firefox, Opera)? Если возможно, укажите также версию браузера.",
				'rows'	=>	3,
				'read'	=>	10,
				'write'	=>	10,
			)
	);
	$obj->setElem($obj_7);

	// Исполнение dynamicaction, если необходимо
	if($action=="dynamicaction")
	{
		require_once($server_inner_path.$direct."/classes/base_mails.php");
		if($object=="help" && $actiontype=="add")
		{
			$myname=encode_to_cp1251($_POST["name"]);
			$myemail=encode_to_cp1251($_POST["em"]);
			$contactemail="project@allrpg.info";
			$subject='Поддержка allrpg.info: '.encode_to_cp1251($_POST["maintext"]);
			$message=decode(encode_to_cp1251($_POST["details"]));
			if($myname!='' && $myemail!='' && $message!='') {
				if(encode_to_cp1251($_POST["project"])!='') {
					$message.='

	Название проекта: '.encode_to_cp1251($_POST["project"]);
				}
				if(encode_to_cp1251($_POST["link"])!='') {
					$message.='

	Ссылка на страницу: '.encode_to_cp1251($_POST["link"]);
				}
				if(encode_to_cp1251($_POST["technical"])!='') {
					$message.='

	Техническая информация: '.decode(encode_to_cp1251($_POST["technical"]));
				}
				if(send_mail($myname, $myemail, $contactemail, $subject, $message)) {
					dynamic_err(array(array('success','Ваш запрос успешно отправлен. Администрация ответит Вам на e-mail при первой же возможности. Спасибо.')),$server_absolute_path);
				}
			}
			else {
				dynamic_err_one('error','Пожалуйста, заполните все обязательные поля.');
			}
		}
	}

	$content2=$obj->draw();

	$pagetitle=h1line('Помощь / сообщение об ошибке');
	$content2=str_replace('Добавить вопрос','Отправить вопрос администрации',$content2);
?>