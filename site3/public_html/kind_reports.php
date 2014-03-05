<?php

$pagetitle=h1line('Отчеты',$curdir.$kind.'/');

if($id!='') {
	$act='view';
}

$result=mysql_query("SELECT * FROM ".$prefix."users where id in (SELECT distinct user_id from ".$prefix."reports)");
while($a=mysql_fetch_array($result))
{
	if($id!='') {
		$allusers[]=Array($a["id"],usname($a, true, true));
	}
	else {
		$allusers[]=Array($a["id"],usname2($a,true));
	}
}
foreach ($allusers as $key => $row)
{
	$fio[$key]  = strtolower($row[1]);
}
array_multisort($fio, SORT_ASC, $allusers);

// Создание объекта
$obj=new netObj(
	'reports',
	$prefix."reports",
	"отчет",
	Array("Отчет успешно добавлен.","Отчет успешно изменен.","Отчет успешно удален."),
	Array(
		'0'=>Array(
			Array("id", "DESC", false, true),
			Array("game", "ASC", true, true, Array(3,$prefix."allgames","id","name")),
			Array("name", "ASC", true, true),
			Array("user_id", "ASC", true, true, Array(2,$allusers)),
			Array("date", "DESC", true, true),
		)
	),
	2,
	'97%',
	$_SESSION["bazecount"]
);

// Создание схемы прав объекта
$obj_r=new netRight(
	true,
	false,
	false,
	false,
	100,
	'',
	'',
	''
);
$obj->setRight($obj_r);

if($id!='') {
	$result=mysql_query("SELECT * FROM ".$prefix."reports where id=".$id);
	$a=mysql_fetch_array($result);
}
$obj_1=createElem(Array(
		'name'	=>	"game",
		'sname'	=>	"Событие",
		'type'	=>	"select",
		'values'	=>	make5field($prefix."allgames where parent=0 and id in (SELECT distinct game from ".$prefix."reports) order by name","id","name"),
		'read'	=>	10,
		'write'	=>	100,
		'linkatbegin'	=>	'<a href="'.$server_absolute_path_info.'events/'.$a["game"].'/">',
		'linkatend'	=>	'</a>',
		'default'	=>	encode($_GET["game"]),
	)
);
$obj->setElem($obj_1);

$obj_5=createElem(Array(
		'name'	=>	"user_id",
		'sname'	=>	"Автор",
		'type'	=>	"select",
		'read'	=>	10,
		'write'	=>	100,
		'default'	=>	encode($_GET["user"]),
	)
);
$obj->setElem($obj_5);

$obj_2=createElem(Array(
		'name'	=>	"name",
		'sname'	=>	"Заголовок отчета",
		'type'	=>	"text",
		'read'	=>	10,
		'write'	=>	100,
	)
);
$obj->setElem($obj_2);

$obj_3=createElem(Array(
		'name'	=>	"content",
		'sname'	=>	"Текст отчета",
		'type'	=>	"wysiwyg",
		'width'	=>	600,
		'read'	=>	10,
		'write'	=>	100,
		'height'	=>	300,
	)
);
$obj->setElem($obj_3);

$obj_4=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Дата",
			'type'	=>	"timestamp",
			'read'	=>	100,
			'write'	=>	100,
		)
);
$obj->setElem($obj_4);

// Исполнение dynamicaction, если необходимо
if($action=="dynamicaction")
{
	require_once($server_inner_path.$direct."/dynamicaction.php");
	if($object=="reports")
	{
		dynamicaction($obj);
	}
}

// Исполнение дополнительных действий после dynamicaction, если необходимо
if(!$trouble && count($trouble2)==0)
{
	if($actiontype=="add")
	{
		mysql_query("UPDATE ".$prefix."reports SET user_id=".$_SESSION['user_id']." WHERE id=".$id);
	}
}

// Добавление параметра values к select'ам и multiselect'ам.
$obj_5->setValues($allusers);

$obj->setSearch($obj_1);
$obj->setSearch($obj_5);

$content2.='<div class="narrow">'.$obj->draw().'</div>';
?>