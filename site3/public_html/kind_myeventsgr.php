<?php
if($_SESSION["user_id"]!='') {
	// группы событий

	// Создание объекта
	$obj=new netObj(
		'myeventsgr',
		$prefix."allgames_groups",
		"группу событий",
		Array("Группа событий добавлена.","Группы событий изменены","Группа событий удалена."),
		Array(
			'0'	=>	Array(
				Array("name", "ASC", true, true),
			),
		),
		1,
		'450px',
		50
	);

	// Создание схемы прав объекта
	if($_SESSION["admin"]!='' || $_SESSION["candoevents"]!='') {
		$obj_r=new netRight(
			true,
			true,
			true,
			true,
			100,
			'',
			'',
			''
		);
		$obj->setRight($obj_r);
	}

	// Создание полей объекта
	$obj_1=createElem(Array(
			'name'	=>	"name",
			'sname'	=>	"Название группы",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_1);

	$obj_2=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Последнее изменение",
			'type'	=>	"timestamp",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_2);

	// Исполнение dynamicaction, если необходимо
	if($action=="dynamicaction")
	{
		require_once($server_inner_path.$direct."/dynamicaction.php");
		if($object=="myeventsgr")
		{
			dynamicaction($obj);
		}
	}

	// Добавление параметра values к select'ам и multiselect'ам.

	// Инициализация элементов поиска, если нужен.

	// Отрисовка всего объекта html'ем в переменную
	$obj_html.=$obj->draw();

	// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
	$pagetitle=h1line('Группы событий');
	$content2.='<div class="narrow">'.$obj_html.'</div>';
}
?>