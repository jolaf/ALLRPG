﻿<?php
if($_SESSION["user_id"]=="") {
	redirect($server_absolute_path.'register/redirectobj=hosting2');
}
if($_SESSION["user_id"]!='') {
	//заявки на хостинг

	// Создание объекта
	$obj=new netObj(
		'hosting2',
		$prefix."orders",
		"систему заявок",
		Array("Система заявок успешно создана."),
		Array(
			'0'	=>	Array(
				Array("title", "ASC", true, true),
				Array("name", "ASC", true, true),
			),
		),
		2,
		'100%',
		50
	);

	// Создание схемы прав объекта
	if($_SESSION["user_id"]!='') {
		$obj_r=new netRight(
			true,
			true,
			false,
			false,
			100,
			'author='.$_SESSION["user_id"].' and usetemp=2',
			'author='.$_SESSION["user_id"].' and usetemp=2',
			'author='.$_SESSION["user_id"].' and usetemp=2'
		);
		$obj->setRight($obj_r);
	}

	// Создание полей объекта

	if($act=="add") {
		$vie=10;
		err_red('Этот раздел не имеет никакого отношения к инфотеке. Размещение событий в инфотеке/календаре осуществляется через раздел «<a href="'.$server_absolute_path_info.'myevents/">Добавить/изменить событие в календаре</a>».');
	}
	else {
		$vie=100000;
		if($act=="view" && $actiontype=='') {
			err('Если Вам требуется произвести изменения в глобальных настройках проекта, которые Вы не можете произвести самостоятельно через «Основные свойства», обратитесь к администрации.');
		}
	}

	$obj_7=createElem(Array(
			'name'	=>	"title",
			'sname'	=>	"Название проекта",
			'type'	=>	"text",
			'help'	=>	"введите полное название проекта без кавычек, например: «Моя игра (ГРИ)». Принятые сокращения: ГРИ – городская ролевая игра; КРИ – кабинетная; ПвРИ – павильонная. Для полигонной игры тип указывать не нужно.",
			'read'	=>	10,
			'write'	=>	10,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_7);

	$obj_6=createElem(Array(
			'name'	=>	"name",
			'sname'	=>	"Внешний сайт",
			'type'	=>	"text",
			'help'	=>	"если у Вашего проекта есть сайт не на allrpg.info, можете указать путь к нему в формате: http://www.allrpg.info",
			'read'	=>	10,
			'write'	=>	10,
		)
	);
	$obj->setElem($obj_6);

	$obj_8=createElem(Array(
			'name'	=>	"datestart",
			'sname'	=>	"Дата начала",
			'type'	=>	"calendar",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_8);

	$obj_9=createElem(Array(
			'name'	=>	"datefinish",
			'sname'	=>	"Дата окончания",
			'type'	=>	"calendar",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_9);

	$obj_14=createElem(Array(
			'name'	=>	"region",
			'sname'	=>	"Регион",
			'type'	=>	"sarissa",
			'parents'	=>	Array(Array('country','Страна')),
			'file'	=>	$helpers_path.'geo.php',
			'table'	=>	$prefix.'geography',
			'parent'	=>	'parent',
			'read'	=>	10,
			'write'	=>	100,
			'order'	=>	'name',
			'moreparams2'	=>	" and id!=2562 and parent!=2562",
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_14);

	$obj_12=createElem(Array(
			'name'	=>	"description",
			'sname'	=>	"Подробное описание проекта",
			'type'	=>	"textarea",
			'rows'	=>	5,
			'read'	=>	10,
			'write'	=>	10,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_12);

	$obj_13=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Последнее изменение",
			'type'	=>	"timestamp",
			'read'	=>	10,
			'write'	=>	10,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_13);

	// Исполнение dynamicaction, если необходимо
	if($action=="dynamicaction")
	{
		require_once($server_inner_path.$direct."/dynamicaction.php");
		if($object=="hosting2")
		{
			require_once($server_inner_path_site."createsite.php");
			createsite(2);
		}
	}

	// Исполнение дополнительных действий после dynamicaction, если необходимо

	// Добавление параметра values к select'ам и multiselect'ам.

	// Инициализация элементов поиска, если нужен.

	// Отрисовка всего объекта html'ем в переменную
	$obj_html.=$obj->draw();

	// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
	$pagetitle=h1line('Открыть систему заявок',$curdir.$kind.'/');
	$content2.='<div class="narrow">'.$obj_html.'</div>';
}
?>