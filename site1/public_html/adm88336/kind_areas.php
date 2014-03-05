<?php

$allcities=Array();
$result=mysql_query("SELECT * FROM ".$prefix."geography WHERE id IN (SELECT DISTINCT city from ".$prefix."areas)");
while($a = mysql_fetch_array($result)) {
	$result2=mysql_query("SELECT * FROM ".$prefix."geography WHERE id=".$a["parent"]);
	$b = mysql_fetch_array($result2);
	$result3=mysql_query("SELECT * FROM ".$prefix."geography WHERE id=".$b["parent"]);
	$c = mysql_fetch_array($result3);
    $allcities[]=Array($a["id"],$c["name"].' / '.$b["name"].' / '.$a["name"]);
}
foreach ($allcities as $key => $row) {
	$allmasterssort[$key]  = strtolower($row[1]);
}
array_multisort($allmasterssort, SORT_ASC, $allcities);

// Создание объекта
$obj=new netObj(
	'areas',
	$prefix."areas",
	"полигон",
	Array("Полигон успешно добавлен.","Полигон успешно изменен.","Полигон успешно удален."),
	Array(
		'0'=>Array(
			Array("city", "ASC", true, true, Array(2,$allcities)),
			Array("name", "ASC", true, true),
		)
	),
	2,
	645,
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
			'name'	=>	"tipe",
			'sname'	=>	"Тип",
			'type'	=>	"select",
			'values'	=>	Array(Array('1','городской'),Array('2','лесной'),Array('3','турбаза'),Array('4','на воде')),
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_1);

$obj_2=createElem(Array(
			'name'	=>	"name",
			'sname'	=>	"Название",
			'type'	=>	"text",
			'help'	=>	"не более 255 символов.",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_2);

$obj_3=createElem(Array(
			'name'	=>	"user_id",
			'sname'	=>	"ИНП создателя полигона",
			'type'	=>	"number",
			'read'	=>	10,
			'write'	=>	100,
			'default'	=>	$_SESSION["user_sid"],
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_3);

$obj_4=createElem(Array(
			'name'	=>	"user_id",
			'sname'	=>	"Создатель полигона",
			'type'	=>	"select",
			'read'	=>	10,
			'write'	=>	100000,
		)
);
$obj->setElem($obj_4);

$obj_5=createElem(Array(
			'name'	=>	"city",
			'sname'	=>	"Город",
			'type'	=>	"sarissa",
			'parents'	=>	Array(Array('country','Страна'),Array('region','Регион')),
			'file'	=>	$helpers_path.'geo.php',
			'table'	=>	$prefix.'geography',
			'parent'	=>	'parent',
			'moreparams'	=>	Array(Array('all',1)),
			'read'	=>	10,
			'write'	=>	100,
			'width'	=>	200,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_5);

$obj_6=createElem(Array(
			'name'	=>	"descr",
			'sname'	=>	"Описание",
			'type'	=>	"wysiwyg",
			'height'	=>	400,
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_6);

$obj_7=createElem(Array(
			'name'	=>	"havegood",
			'sname'	=>	"Плюсы",
			'type'	=>	"multiselect",
			'values'	=>	make5field($prefix."areahave where gr=1 order by name","id","name"),
			'images'	=>	make5field($prefix."areahave where gr=1 order by name","id","im"),
			'path'	=>	$server_absolute_path.$uploads[8]['path'],
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_7);

$obj_8=createElem(Array(
			'name'	=>	"havebad",
			'sname'	=>	"Минусы",
			'type'	=>	"multiselect",
			'values'	=>	make5field($prefix."areahave where gr=2 order by name","id","name"),
			'images'	=>	make5field($prefix."areahave where gr=2 order by name","id","im"),
			'path'	=>	$server_absolute_path.$uploads[8]['path'],
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_8);

$obj_9=createElem(Array(
			'name'	=>	"map",
			'sname'	=>	"Карта проезда",
			'type'	=>	"file",
			'upload'	=>	9,
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_9);

$obj_10=createElem(Array(
			'name'	=>	"way",
			'sname'	=>	"Проезд",
			'type'	=>	"wysiwyg",
			'height'	=>	400,
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_10);

$obj_11=createElem(Array(
			'name'	=>	"coordinates",
			'sname'	=>	"Контакты оф. властей",
			'type'	=>	"wysiwyg",
			'height'	=>	300,
			'help'	=>	"скрыто для всех, кроме автора",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_11);

$obj_13=createElem(Array(
			'name'	=>	"tomoderate",
			'sname'	=>	"Требует модерации",
			'type'	=>	"checkbox",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_13);

$obj_14=createElem(Array(
			'name'	=>	"addip",
			'sname'	=>	"IP человека, внесшего полигон",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	100000,
		)
);
$obj->setElem($obj_14);

$obj_15=createElem(Array(
			'name'	=>	"kogdaigra_id",
			'sname'	=>	"Kogda-igra",
			'type'	=>	"number",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_15);

$obj_12=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Последнее изменение",
			'type'	=>	"timestamp",
			'read'	=>	100,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_12);

// Исполнение dynamicaction, если необходимо
if($action=="dynamicaction")
{
	require_once($server_inner_path.$direct."/dynamicaction.php");
	if($object=="areas")
	{
		dynamicaction($obj);
	}
}

// Исполнение дополнительных действий после dynamicaction, если необходимо
if(!$trouble && count($trouble2)==0)
{
	if($actiontype=="change") {
		mysql_query("UPDATE ".$prefix."areas SET tomoderate='0' where id=".$id);
	}
}

// Добавление параметра values к select'ам и multiselect'ам.
$obj_4->setValues(make5field($prefix."users ORDER by fio asc","sid",Array("<br> Ф.И.О.: ", "fio", "<br> Имя на форумах: ", "login", "<br> E-mail: <a href=\"mailto:", "em", "\">", "em", "</a><br> ICQ: ", "icq")));

// Инициализация элементов поиска, если нужен.
$obj->setSearch($obj_1);
$obj->setSearch($obj_2);
$obj->setSearch($obj_5);
$obj->setSearch($obj_6);
$obj->setSearch($obj_7);
$obj->setSearch($obj_8);
$obj->setSearch($obj_9);
$obj->setSearch($obj_13);

// Отрисовка всего объекта html'ем в переменную
$obj_html.=$obj->draw();

// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
$content2.='<h1>ВСЕ ПОЛИГОНЫ</h1>'.$obj_html;
?>