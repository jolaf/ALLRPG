<?php
if($_SESSION["user_id"]!='' && $workrights["site"]["comments"]) {
	// комментарии

	$result=mysql_query("SELECT * FROM ".$prefix."users where id in (SELECT user_id FROM ".$prefix."pagecomments where site_id=".$_SESSION["siteid"].")");
	while($a = mysql_fetch_array($result))
	{
		$allusers[]=Array($a["id"],usname($a,true,true));
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
		'comments',
		$prefix."pagecomments",
		"комментарий",
		Array("","","Комментарий удален."),
		Array(
			'0'	=>	Array(
				Array("date", "DESC", true, true),
				Array("user_id", "ASC", true, true, Array(2,$allusers2)),
				Array("false_id", "ASC", true, true, Array(3,$prefix."pages","id","name"))
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
			false,
			false,
			true,
			100,
			'site_id='.$_SESSION["siteid"],
			'site_id='.$_SESSION["siteid"],
			'site_id='.$_SESSION["siteid"]
		);
		$obj->setRight($obj_r);
	}

	// Создание полей объекта

	$obj_1=createElem(Array(
			'name'	=>	"user_id",
			'sname'	=>	"Пользователь",
			'type'	=>	"select",
			'values'	=>	$allusers,
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_1);

	$obj_2=createElem(Array(
			'name'	=>	"content",
			'sname'	=>	"Текст комментария",
			'type'	=>	"textarea",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_2);

	$obj_3=createElem(Array(
			'name'	=>	"false_id",
			'sname'	=>	"Страница",
			'type'	=>	"select",
			'values'	=>	make5field($prefix."pages where site_id=".$_SESSION["siteid"],"id","name"),
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_3);

	$obj_4=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Дата/время",
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
		if($object=="comments")
		{
			dynamicaction($obj);
		}
	}

	// Добавление параметра values к select'ам и multiselect'ам.

	// Инициализация элементов поиска, если нужен.

	// Отрисовка всего объекта html'ем в переменную
	$obj_html.=$obj->draw();

	// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
	$pagetitle=h1line('Комментарии к страницам',$curdir.$kind.'/');
	$content2.='<div class="narrow">'.$obj_html.'</div>';
}
?>