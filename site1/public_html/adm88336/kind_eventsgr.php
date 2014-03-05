<?php
// группы событий

// Создание объекта
$obj=new netObj(
	'eventsgr',
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
if($allrights["admin"] || $allrights["info"])
{
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
	if($object=="eventsgr")
	{
		dynamicaction($obj);
	}
}

// Исполнение дополнительных действий после dynamicaction, если необходимо
if(!$trouble && count($trouble2)==0)
{
	if($object=="eventsgr")
	{
		if($actiontype=="add")
		{
		}
	}
}

// Добавление параметра values к select'ам и multiselect'ам.

// Инициализация элементов поиска, если нужен.

// Отрисовка всего объекта html'ем в переменную
$obj_html.=$obj->draw();

// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
$content2.='<h1>ГРУППЫ СОБЫТИЙ</h1>'.$obj_html;
?>