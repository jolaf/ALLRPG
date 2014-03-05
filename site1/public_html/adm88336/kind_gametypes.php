<?php

// Создание объекта
$obj=new netObj(
	'gametypes',
	$prefix."gametypes",
	"характеристику игр",
	Array("Характеристика игр успешно добавлена.","Характеристики игр успешно изменены","Характеристика игр успешно удалена."),
	Array(
		'0'=>Array(
			Array("tipe", "ASC", true, true),
			Array("name", "ASC", true, true),
		)
	),
	1,
	600,
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
			'name'	=>	"name",
			'sname'	=>	"Название",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_1);

$obj_2=createElem(Array(
			'name'	=>	"im",
			'sname'	=>	"Иконка",
			'type'	=>	"file",
			'upload'	=>	6,
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_2);

$obj_3=createElem(Array(
			'name'	=>	"tipe",
			'sname'	=>	"Группа",
			'type'	=>	"select",
			'values'	=>	Array(Array('1','жанр игры'),Array('2','тип игры'),Array('3','дополнительные иконки')),
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_3);

$obj_4=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Последнее изменение",
			'type'	=>	"timestamp",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_4);


// Исполнение dynamicaction, если необходимо
if($action=="dynamicaction")
{
	require_once($server_inner_path.$direct."/dynamicaction.php");
	if($object=="gametypes")
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
$obj->setSearch($obj_3);

// Отрисовка всего объекта html'ем в переменную
$obj_html.=$obj->draw();

// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
$content2.='<h1>СПРАВОЧНИК ХАРАКТЕРИСТИК ИГР</h1>'.$obj_html;
?>