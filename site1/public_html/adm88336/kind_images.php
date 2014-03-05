<?php

// Создание объекта
$obj=new netObj(
	'images',
	$prefix."blog_icons",
	"иконку блогов",
	Array("Иконка успешно добавлена.","Иконки успешно изменены","Иконка успешно удалена."),
	Array(
		'0'=>Array(
			Array("gr", "ASC", true, true),
			Array("code", "ASC", true, true),
			Array("name", "ASC", true, true),
		)
	),
	1,
	700,
	50
);

// Создание схемы прав объекта
if($allrights["admin"])
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
else
{
	$obj_r=new netRight(
		false,
		false,
		false,
		false,
		100,
		'',
		'',
		''
	);
	$obj->setRight($obj_r);
}

// Создание полей объекта
$obj_1=createElem(Array(
			'name'	=>	"gr",
			'sname'	=>	"Группа",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_1);

$obj_2=createElem(Array(
			'name'	=>	"code",
			'sname'	=>	"Очередность",
			'type'	=>	"number",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_2);

$obj_3=createElem(Array(
			'name'	=>	"name",
			'sname'	=>	"Название",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_3);

$obj_4=createElem(Array(
			'name'	=>	"img",
			'sname'	=>	"Иконка",
			'type'	=>	"file",
			'upload'	=>	1,
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
	if($object=="images")
	{
		dynamicaction($obj);
	}
}

// Исполнение дополнительных действий после dynamicaction, если необходимо
if(!$trouble && count($trouble2)==0)
{

}

// Добавление параметра values к select'ам и multiselect'ам.

// Инициализация элементов поиска, если нужен.
//$obj->setSearch($obj_1);

// Отрисовка всего объекта html'ем в переменную
$obj_html.=$obj->draw();

// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
$content2.='<h1>ИКОНКИ БЛОГОВ</h1>'.$obj_html;
?>