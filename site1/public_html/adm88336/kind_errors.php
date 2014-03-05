<?php

// Создание объекта
$obj=new netObj(
	'errorlog',
	$prefix."errorlog",
	"ошибку",
	Array("","","Ошибка успешно удалена."),
	Array(
		'0'=>Array(
			Array("date", "DESC", true, true),
		)
	),
	1,
	'100%',
	50
);

// Создание схемы прав объекта
if($allrights["admin"])
{
	$obj_r=new netRight(
		true,
		false,
		false,
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
			'name'	=>	"user_id",
			'sname'	=>	"Пользователь",
			'type'	=>	"number",
			'read'	=>	10,
			'write'	=>	10,
		)
);
$obj->setElem($obj_1);

$obj_2=createElem(Array(
			'name'	=>	"address",
			'sname'	=>	"Адрес страницы",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	10,
		)
);
$obj->setElem($obj_2);

$obj_3=createElem(Array(
			'name'	=>	"postarray",
			'sname'	=>	"Запрос POST",
			'type'	=>	"textarea",
			'read'	=>	10,
			'write'	=>	10,
		)
);
$obj->setElem($obj_3);

$obj_4=createElem(Array(
			'name'	=>	"secs",
			'sname'	=>	"Кол-во секунд",
			'type'	=>	"number",
			'read'	=>	10,
			'write'	=>	10,
		)
);
$obj->setElem($obj_4);

$obj_5=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Дата и время",
			'type'	=>	"timestamp",
			'read'	=>	10,
			'write'	=>	10,
			'show'	=>	true,
		)
);
$obj->setElem($obj_5);

// Исполнение dynamicaction, если необходимо
if($action=="dynamicaction")
{
	require_once($server_inner_path.$direct."/dynamicaction.php");
	if($object=="errorlog")
	{
		dynamicaction($obj);
	}
}

// Исполнение дополнительных действий после dynamicaction, если необходимо
if(!$trouble && count($trouble2)==0)
{
	if($object=="errorlog")
	{
		if($actiontype=="add") {
		}
	}
}

// Добавление параметра values к select'ам и multiselect'ам.

// Инициализация элементов поиска, если нужен.

// Отрисовка всего объекта html'ем в переменную
$obj_html.=$obj->draw();

// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
$content2.='<h1>ЛОГ ОШИБОК ПО ТАЙМЕРУ</h1>'.$obj_html;
?>