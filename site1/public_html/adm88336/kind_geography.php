<?php

// Создание объекта
$obj=new netObj2(
	'geography',
	$prefix."geography",
	"страну/регион",
	Array("Страна/регион успешно добавлена.","Страна/регион успешно изменена.","Страна/регион успешно удалена.","Страна/регион и все ее города успешно удалены."),
	"город",
	Array("Город успешно добавлен.","Город успешно изменен.","Город успешно удален."),
	Array(
		'0'	=>	Array(
			Array("name", "ASC", true, true),
		),
		'1'	=>	Array(
			Array("name", "ASC", true, true),
		),
	),
	3,
	500,
	50,
	'parent',
	'content',
	'name',
	'name'
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
			'name'	=>	"parent",
			'sname'	=>	"Находится в",
			'type'	=>	"select",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_1);

$obj_2=createElem(Array(
			'name'	=>	"name",
			'sname'	=>	"Страна/регион",
			'type'	=>	"text",
			'help'	=>	"не более 255 символов.",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_2);

$obj_3=createElem(Array(
			'name'	=>	"content",
			'type'	=>	"hidden",
			'default'	=>	"{menu}",
			'read'	=>	100,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_3);

$obj_4=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Последнее изменение",
			'type'	=>	"timestamp",
			'read'	=>	100,
			'write'	=>	100,
			'mustbe'	=>	true,
			'show'	=>	true,
		)
);
$obj->setElem($obj_4);

$obj_5=createElem(Array(
			'name'	=>	"parent",
			'sname'	=>	"Находится в",
			'type'	=>	"select",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true,
			'br'	=>	true,
		)
);
$obj->setElem2($obj_5);

$obj_6=createElem(Array(
			'name'	=>	"name",
			'sname'	=>	"Город",
			'type'	=>	"text",
			'help'	=>	"не более 255 символов.",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem2($obj_6);

$obj_7=createElem(Array(
			'name'	=>	"content",
			'type'	=>	"hidden",
			'read'	=>	100,
			'write'	=>	100,
		)
);
$obj->setElem2($obj_7);

$obj_8=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Последнее изменение",
			'type'	=>	"timestamp",
			'read'	=>	100,
			'write'	=>	100,
			'mustbe'	=>	true,
			'show'	=>	true,
		)
);
$obj->setElem2($obj_8);

// Исполнение dynamicaction, если необходимо
if($action=="dynamicaction")
{
	require_once($server_inner_path.$direct."/dynamicaction.php");
	if($object=="geography")
	{
		dynamicaction($obj);
	}
}

// Исполнение дополнительных действий после dynamicaction, если необходимо
if(!$trouble && count($trouble2)==0)
{

}

// Добавление параметра values к select'ам и multiselect'ам.
$obj_1->setValues(make5fieldtree(true,$prefix."geography","parent",0," AND content='{menu}'","name asc",1,"id","name",1));
$obj_5->setValues(make5fieldtree(false,$prefix."geography","parent",0," AND content='{menu}'","name asc",1,"id","name",3));

// Инициализация элементов поиска, если нужен.

// Отрисовка всего объекта html'ем в переменную
$obj_html.=$obj->draw();

// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
$content2.='<h1>СПРАВОЧНИК ГЕОГРАФИИ</h1>'.$obj_html;
?>