<?php
if($_SESSION["user_id"]!='' && $workrights["site"]["groups"]) {
	// группы доступа

	// Создание объекта
	$obj=new netObj(
		'groups',
		$prefix."virtrights",
		"группу доступа",
		Array("Группа доступа добавлена.","Группы доступа изменены","Группа доступа удалена."),
		Array(
			'0'	=>	Array(
				Array("gr", "ASC", true, true),
				Array("user_id", "ASC", true, true),
			),
		),
		1,
		'100%',
		50
	);

	// Создание схемы прав объекта
	if($_SESSION["siteid"]!='') {
		$obj_r=new netRight(
			true,
			true,
			true,
			true,
			100,
			'site_id='.$_SESSION["siteid"],
			'site_id='.$_SESSION["siteid"],
			'site_id='.$_SESSION["siteid"]
		);
		$obj->setRight($obj_r);
	}

	// Создание полей объекта
	$obj_1=createElem(Array(
			'name'	=>	"gr",
			'sname'	=>	"Номер группы",
			'type'	=>	"number",
			'help'	=>	"введите номер (от 1 до 10000) группы, в которую хотите поместить пользователя.",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_1);

	$obj_2=createElem(Array(
			'name'	=>	"user_id",
			'sname'	=>	"ИНП",
			'type'	=>	"number",
			'help'	=>	"введите Идентификационный Номер Пользователя, которому хотите выдать права.",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_2);

	$obj_3=createElem(Array(
			'name'	=>	"user_id",
			'sname'	=>	"Пользователь",
			'type'	=>	"select",
			'read'	=>	10,
			'write'	=>	1000000,
		)
	);
	$obj->setElem($obj_3);

	$obj_4=createElem(Array(
			'name'	=>	"site_id",
			'type'	=>	"hidden",
			'default'	=>	$_SESSION["siteid"],
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_4);

	$obj_5=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Последнее изменение",
			'type'	=>	"timestamp",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_5);

	// Исполнение dynamicaction, если необходимо
	if($action=="dynamicaction")
	{
		require_once($server_inner_path.$direct."/dynamicaction.php");
		if($object=="groups")
		{
			if($actiontype=="add") {
				function dynamic_add_success() {
					global
						$prefix,
						$_SESSION,
						$id;

					mysql_query("UPDATE ".$prefix."virtrights SET site_id=".$_SESSION["siteid"]." WHERE id=".$id);
				}
			}

			dynamicaction($obj);
		}
	}

	// Добавление параметра values к select'ам и multiselect'ам.
	$result=mysql_query("SELECT * FROM ".$prefix."users where sid in (SELECT user_id FROM ".$prefix."virtrights where site_id=".$_SESSION["siteid"].")");
	while($a = mysql_fetch_array($result))
	{
		$allusers[]=Array($a["sid"],usname($a,true,true));
	}
	$obj_3->setValues($allusers);

	// Инициализация элементов поиска, если нужен.

	// Отрисовка всего объекта html'ем в переменную
	$obj_html.=$obj->draw();

	// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
	$pagetitle=h1line('Группы доступа');
	$content2.='<div class="narrow">'.$obj_html.'</div>';
}
?>