<?php

require_once($server_inner_path.$direct."/classes/base_mails.php");

// Создание объекта
$obj=new netObj(
	'orders',
	$prefix."orders",
	"проект",
	Array("Проект успешно создан."),
	Array(
		'0'=>Array(
			Array("usetemp", "ASC", true, true, Array(2,Array(Array('0','хостинг + система заявок'),Array('1','только сайт'),Array('2','только система заявок')))),
			Array("name", "ASC", true, true),
			Array("author", "ASC", true, true, Array(3,$prefix."users","id","fio")),
			Array("sid", "ASC", true, true)
		)
	),
	2,
	645,
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
			'name'	=>	"sid",
			'sname'	=>	"ИНЗ",
			'help'	=>	"Идентификационный номер заявки",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	100000,
		)
);
$obj->setElem($obj_1);

$obj_2=createElem(Array(
			'name'	=>	"author",
			'sname'	=>	"Инициатор",
			'type'	=>	"select",
			'values'	=>	make5field($prefix."users ORDER by fio asc","id",Array("ИНП: ", "sid", "<br> Ф.И.О.: ", "fio", "<br> Логин: ", "login", "<br> E-mail: <a href=\"mailto:", "em", "\">", "em", "</a><br> ICQ: ", "icq", "")),
			'read'	=>	10,
			'write'	=>	100000,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_2);

$obj_3=createElem(Array(
			'name'	=>	"usetemp",
			'sname'	=>	"Схема",
			'type'	=>	"select",
			'values'	=>	Array(Array('0','хостинг + система заявок'),Array('1','только сайт'),Array('2','только система заявок')),
			'read'	=>	10,
			'write'	=>	10,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_3);

$obj_4=createElem(Array(
			'name'	=>	"name",
			'sname'	=>	"Субдомен / внешний сайт",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	10,
		)
);
$obj->setElem($obj_4);

$obj_5=createElem(Array(
			'name'	=>	"title",
			'sname'	=>	"Название проекта",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	10,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_5);

$obj_6=createElem(Array(
			'name'	=>	"datestart",
			'sname'	=>	"Дата начала",
			'type'	=>	"calendar",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_6);

$obj_7=createElem(Array(
			'name'	=>	"datefinish",
			'sname'	=>	"Дата окончания",
			'type'	=>	"calendar",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_7);

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
			'width'	=>	200,
			'order'	=>	'name',
			'moreparams2'	=>	" and id!=2562 and parent!=2562",
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_14);

$obj_8=createElem(Array(
			'name'	=>	"em",
			'sname'	=>	"E-mail",
			'type'	=>	"checkbox",
			'read'	=>	10,
			'write'	=>	10,
		)
);
$obj->setElem($obj_8);

$obj_9=createElem(Array(
			'name'	=>	"blog",
			'sname'	=>	"Коммьюнити",
			'type'	=>	"checkbox",
			'read'	=>	10,
			'write'	=>	10,
		)
);
$obj->setElem($obj_9);

$obj_15=createElem(Array(
			'name'	=>	"blogname",
			'sname'	=>	"Название коммьюнити",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	10,
		)
);
$obj->setElem($obj_15);

$obj_10=createElem(Array(
			'name'	=>	"description",
			'sname'	=>	"Подробное описание проекта",
			'type'	=>	"textarea",
			'rows'	=>	5,
			'read'	=>	10,
			'write'	=>	10,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_10);

$obj_13=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Последнее изменение",
			'type'	=>	"timestamp",
			'read'	=>	100,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_13);

// Исполнение dynamicaction, если необходимо
if($action=="dynamicaction")
{
	require_once($server_inner_path.$direct."/dynamicaction.php");
	if($object=="orders")
	{
		dynamicaction($obj);
	}
}

// Исполнение дополнительных действий после dynamicaction, если необходимо
if(!$trouble && count($trouble2)==0 && ($actiontype=='add' || $actiontype=='change'))
{
	if($object=="orders")
	{

	}
}

// Добавление параметра values к select'ам и multiselect'ам.


// Инициализация элементов поиска, если нужен.
$obj->setSearch($obj_3);
$obj->setSearch($obj_4);
$obj->setSearch($obj_5);

// Отрисовка всего объекта html'ем в переменную
$obj_html.=$obj->draw();

// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
$content2.='<h1>ЗАЯВКИ НА ПРОЕКТЫ</h1>'.$obj_html;
?>