<?php
if($_SESSION["user_id"]!='' && $workrights["site"]["news"]) {
	// новости

	$result=mysql_query("SELECT * FROM ".$prefix."users where sid in (SELECT user_id from ".$prefix."allrights2 where site_id=".$_SESSION["siteid"]." and (rights=1 OR rights=3))");
	while($a = mysql_fetch_array($result))
	{
		$allusers[]=Array($a["id"],usname($a,true));
		$allusers2[]=Array($a["id"],usname($a,true));
	}
	foreach ($allusers as $key => $row)
	{
		$allusers_sort[$key]  = strtolower($row[1]);
	}
	array_multisort($allusers_sort, SORT_ASC, $allusers);
	foreach ($allusers2 as $key => $row)
	{
		$allusers2_sort[$key]  = strtolower($row[1]);
	}
	array_multisort($allusers2_sort, SORT_ASC, $allusers2);

	// Создание объекта
	$obj=new netObj(
		'news',
		$prefix."news",
		"новость",
		Array("Новость добавлена.","Новость изменена.","Новость удалена."),
		Array(
			'0'	=>	Array(
				Array("date", "DESC", true, true),
				Array("author", "ASC", true, true, Array(2,$allusers2)),
				Array("name", "ASC", true, true)
			),
		),
		2,
		'100%',
		50
	);

	// Создание схемы прав объекта
	if($_SESSION["user_id"]!='') {
		$obj_r=new netRight(
			true,
			true,
			true,
			true,
			100,
			'site_id='.$_SESSION["siteid"],
			'site_id='.$_SESSION["siteid"],
			'site_id='.$_SESSION["siteid"]
		);
		$obj->setRight($obj_r);
	}

	if($rights[1])
	{
		$news_fa=Array(
			'name'	=>	"author",
			'sname'	=>	"Автор",
			'type'	=>	"select",
			'values'	=>	$allusers,
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		);
	}
	elseif($rights[3])
	{
		$news_fa=Array(
			'name'	=>	"author",
			'sname'	=>	"Автор",
			'type'	=>	"hidden",
			'default'	=>	$_SESSION["user_id"],
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		);
	}

	// Создание полей объекта

	$obj_1=createElem(Array(
			'name'	=>	"name",
			'sname'	=>	"Название",
			'type'	=>	"text",
			'help'	=>	"не более 255 символов",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_1);

	$obj_2=createElem($news_fa);
	$obj->setElem($obj_2);

	$obj_3=createElem(Array(
			'name'	=>	"date2",
			'sname'	=>	"Дата новости",
			'type'	=>	"calendar",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true,
		)
	);
	$obj->setElem($obj_3);

	$obj_4=createElem(Array(
			'name'	=>	"sour",
			'sname'	=>	"Источник",
			'type'	=>	"text",
			'help'	=>	"не более 255 символов",
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
			'sname'	=>	"Новость полностью",
			'type'	=>	"wysiwyg",
			'height'	=>	400,
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_7);

	$obj_8=createElem(Array(
			'name'	=>	"site_id",
			'type'	=>	"hidden",
			'default'	=>	$_SESSION["siteid"],
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_8);

	$obj_9=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Последнее изменение",
			'type'	=>	"timestamp",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_9);

	// Исполнение dynamicaction, если необходимо
	if($action=="dynamicaction")
	{
		require_once($server_inner_path.$direct."/dynamicaction.php");
		if($object=="news")
		{
			function dynamic_add_success() {
				global
					$prefix,
					$_SESSION,
					$id;

				mysql_query("UPDATE ".$prefix."news SET site_id=".$_SESSION["siteid"]." WHERE id=".$id);
			}
			dynamicaction($obj);
		}
	}

	// Добавление параметра values к select'ам и multiselect'ам.

	// Инициализация элементов поиска, если нужен.

	// Отрисовка всего объекта html'ем в переменную
	$obj_html.=$obj->draw();

	// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
	$pagetitle=h1line('Новостная лента',$curdir.$kind.'/');
	$content2.='<div class="narrow">'.$obj_html.'</div>';
}
?>