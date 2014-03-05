<?php

// Создание объекта
$obj=new netObj(
	'projectsrights',
	$prefix."allrights2",
	"право управления проектом",
	Array("Право управления проектом успешно создано.","Права управления проектами успешно изменены","Право управления проектом успешно удалено."),
	Array(
		'0'=>Array(
			Array("site_id", "ASC", true, true, Array(3,$prefix."sites","id","title")),
			Array("rights", "ASC", true, true),
		)
	),
	1,
	900,
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
			'name'	=>	"site_id",
			'sname'	=>	"Сайт",
			'type'	=>	"select",
			'values'	=>	make5field($prefix."sites ORDER by title asc","id","title"),
			'read'	=>	100,
			'write'	=>	100,
			'width'	=>	350,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_1);

$obj_2=createElem(Array(
			'name'	=>	"user_id",
			'sname'	=>	"ИНП",
			'type'	=>	"number",
			'help'	=>	"введите Идентификационный Номер Пользователя, которому хотите выдать права.",
			'read'	=>	100,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_2);

$obj_3=createElem(Array(
			'name'	=>	"user_id",
			'sname'	=>	"Пользователь",
			'type'	=>	"select",
			'values'	=>	make5field($prefix."users ORDER by nick asc","sid",Array("Ф.И.О.: ", "fio", "<br>Никнейм: ", "nick")),
			'read'	=>	100,
			'write'	=>	100000,
		)
);
$obj->setElem($obj_3);

$obj_4=createElem(Array(
			'name'	=>	"rights",
			'sname'	=>	"Права",
			'type'	=>	"select",
			'values'	=>	Array(Array('1','Глав.мастер'),Array('4','Дизайнер'),Array('2','Мастер'),Array('3','Автор новостей')),
			'help'	=>	"глав.мастер имеет право настраивать сайт и работать с заявками.<br>
Мастер имеет право работать с заявками.<br>
Автор новостей может писать новости сайта.",
			'read'	=>	100,
			'write'	=>	100,
			'width'	=>	200,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_4);

$obj_5=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Последнее изменение",
			'type'	=>	"timestamp",
			'read'	=>	100,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_5);



// Исполнение dynamicaction, если необходимо
if($action=="dynamicaction")
{
	require_once($server_inner_path.$direct."/dynamicaction.php");
	if($object=="projectsrights")
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
$obj->setSearch($obj_1);
$obj->setSearch($obj_2);
$obj->setSearch($obj_4);

// Отрисовка всего объекта html'ем в переменную
$obj_html.=$obj->draw();

// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
$content2.='<h1>ПРАВА УПРАВЛЕНИЯ ПРОЕКТАМИ</h1>'.$obj_html;
?>