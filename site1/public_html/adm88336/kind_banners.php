<?php

// Создание объекта
$obj=new netObj(
	'ushki',
	$prefix."ushki",
	"баннер",
	Array("Баннер успешно загружен.","Баннеры успешно изменены","Баннер успешно удален."),
	Array(
		'0'=>Array(
			Array("active", "DESC", true, true),
			Array("id", "DESC", false, true),
			Array("name", "ASC", true, true),
		)
	),
	1,
	'850px',
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
			'help'	=>	"title-текст, всплывающий при наведении на баннер.",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_1);

$obj_2=createElem(Array(
			'name'	=>	"link",
			'sname'	=>	"Ссылка",
			'type'	=>	"text",
			'help'	=>	"на какую страницу ведет баннер?",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_2);

$obj_3=createElem(Array(
			'name'	=>	"img",
			'sname'	=>	"Изображение",
			'type'	=>	"file",
			'help'	=>	"на данный момент баннерный стандарт не установлен. Можно загружать любой тип изображений любого размера на ваше усмотрение.",
			'upload'	=>	2,
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_3);

$obj_4=createElem(Array(
			'name'	=>	"chapters",
			'sname'	=>	"Показывать в",
			'type'	=>	"hidden",
			'help'	=>	"в каких разделах показывать баннер?",
			'default'	=>	'-1-',
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_4);

$obj_5=createElem(Array(
			'name'	=>	"active",
			'sname'	=>	"Активность",
			'type'	=>	"checkbox",
			'help'	=>	"убрав галочку, вы временно отключите показ данного баннера на сайте без удаления самого изображения и настроек.",
			'default'	=>	1,
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_5);

$obj_6=createElem(Array(
			'name'	=>	"partner",
			'sname'	=>	"Партнер",
			'type'	=>	"checkbox",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_6);


// Исполнение dynamicaction, если необходимо
if($action=="dynamicaction")
{
	require_once($server_inner_path.$direct."/dynamicaction.php");
	if($object=="ushki")
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
$content2.='<h1>БАННЕРНАЯ СИСТЕМА</h1>'.$obj_html;
?>