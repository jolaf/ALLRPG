<?php

// Создание объекта
$obj=new netObj(
	'news',
	$prefix."news",
	"новость",
	Array("Новость успешно добавлена.","Новость успешно изменена.","Новость успешно удалена."),
	Array(
		'0'=>Array(
			Array("date", "DESC", true, true),
			Array("name", "ASC", true, true)
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
			'name'	=>	"name",
			'sname'	=>	"Название",
			'type'	=>	"text",
			'help'	=>	"не более 255 символов.",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_1);

$obj_2=createElem(Array(
			'name'	=>	"author",
			'sname'	=>	"Автор",
			'type'	=>	"select",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_2);

$obj_3=createElem(Array(
			'name'	=>	"date2",
			'sname'	=>	"Дата публикации новости",
			'type'	=>	"calendar",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true,
			'default'	=>	date("Y-m-d"),
		)
);
$obj->setElem($obj_3);

$obj_4=createElem(Array(
			'name'	=>	"sour",
			'sname'	=>	"Источник",
			'type'	=>	"text",
			'help'	=>	"не более 255 символов.",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_4);

$obj_5=createElem(Array(
			'name'	=>	"active",
			'sname'	=>	"Новость активна",
			'type'	=>	"checkbox",
			'default'	=>	1,
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_5);

$obj_6=createElem(Array(
			'name'	=>	"content",
			'sname'	=>	"Новость вкратце (текст в ленте новостей)",
			'type'	=>	"wysiwyg",
			'height'	=>	200,
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_6);

$obj_7=createElem(Array(
			'name'	=>	"main",
			'sname'	=>	"Новость полностью (текст при нажатии \"Подробнее...\")",
			'type'	=>	"wysiwyg",
			'height'	=>	400,
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_7);

$obj_8=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Последнее изменение",
			'type'	=>	"timestamp",
			'read'	=>	100,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_8);

// Исполнение dynamicaction, если необходимо
if($action=="dynamicaction")
{
	require_once($server_inner_path.$direct."/dynamicaction.php");
	if($object=="news")
	{
		dynamicaction($obj);
	}
}

// Исполнение дополнительных действий после dynamicaction, если необходимо
if(!$trouble && count($trouble2)==0)
{

}

// Добавление параметра values к select'ам и multiselect'ам.
$result=mysql_query("SELECT * FROM ".$prefix."users where id in (SELECT distinct author FROM ".$prefix."news)");
while($a = mysql_fetch_array($result))
{
	$allusers[]=Array($a["id"],usname($a,true));
}
foreach ($allusers as $key => $row)
{
	$allusers_sort[$key]  = strtolower($row[1]);
}
array_multisort($allusers_sort, SORT_ASC, $allusers);

$obj_2->setValues($allusers);

// Инициализация элементов поиска, если нужен.
$obj->setSearch($obj_1);
$obj->setSearch($obj_5);
$obj->setSearch($obj_3);
$obj->setSearch($obj_6);
$obj->setSearch($obj_7);
$obj->setSearch($obj_8);

// Отрисовка всего объекта html'ем в переменную
$obj_html.=$obj->draw();

// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
$content2.='<h1>НОВОСТИ</h1>'.$obj_html;
?>