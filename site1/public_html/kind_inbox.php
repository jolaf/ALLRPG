<?php
if($_SESSION["user_id"]!='') {
   	if($id>0) {
    	$result=mysql_query("SELECT * FROM ".$prefix."blog_pms where id=".$id." and user_id=".$_SESSION["user_id"]." and (pmread='0' OR pmread IS NULL)");
		$a = mysql_fetch_array($result);
		if($a["id"]!='') {
			mysql_query("UPDATE ".$prefix."blog_pms SET pmread='1' WHERE id=".$id);
		}
   	}

   	// Создание объекта
	$obj=new netObj(
		'inbox',
		$prefix."blog_pms",
		"сообщение",
		Array("","","Сообщение удалено."),
		Array(),
		2,
		'100%',
		50
	);

	// Создание схемы прав объекта
	if($_SESSION["user_id"]!='') {
		$obj_r=new netRight(
			true,
			true,
			false,
			true,
			100,
			'user_id='.$_SESSION["user_id"]." and todelete2='0'",
			'user_id='.$_SESSION["user_id"]." and todelete2='0'",
			'user_id='.$_SESSION["user_id"]." and todelete2='0'"
		);
		$obj->setRight($obj_r);
	}

	// Создание полей объекта

	$obj_1=createElem(Array(
			'name'	=>	"creator_id",
			'sname'	=>	"Автор",
			'type'	=>	"select",
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_1);

	$obj_2=createElem(Array(
			'name'	=>	"title",
			'sname'	=>	"Заголовок",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_2);

	$obj_3=createElem(Array(
			'name'	=>	"icon",
			'sname'	=>	"Иконка",
			'type'	=>	"multiselect",
			'one'	=>	true,
			'values'	=>	make5field($prefix."blog_icons where gr='pms' order by code","id","name"),
			'images'	=>	make5field($prefix."blog_icons where gr='pms' order by code","id","img"),
			'path'	=>	$server_absolute_path.$uploads[1]['path'],
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_3);

	$obj_4=createElem(Array(
			'name'	=>	"content",
			'sname'	=>	"Текст сообщения",
			'type'	=>	"wysiwyg",
			'height'	=>	400,
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_4);

	$obj_6=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Получено",
			'type'	=>	"timestamp",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_6);

	// Исполнение dynamicaction, если необходимо
	if($action=="dynamicaction")
	{
		require_once($server_inner_path.$direct."/dynamicaction.php");
		if($object=="inbox")
		{
			if($actiontype=="delete") {
				$result=mysql_query("SELECT * FROM ".$prefix."blog_pms where id=".$id);
				$a = mysql_fetch_array($result);
				if($a["todelete"]=='1') {
					dynamicaction($obj);
				}
				else {
					mysql_query("UPDATE ".$prefix."blog_pms SET todelete2='1' WHERE id=".$id);
					redirect_construct();
					dynamic_err(array(array('success',"Сообщение удалено.")),$redirect_path);
				}
			}
		}
	}

	// Добавление параметра values к select'ам и multiselect'ам.
    $obj_7=createElem(Array(
			'name'	=>	"id",
			'type'	=>	"select",
			'read'	=>	100000,
			'write'	=>	100000,
		)
	);
	$obj->setElem($obj_7);

	$result=mysql_query("SELECT * FROM ".$prefix."users where id in (SELECT creator_id from ".$prefix."blog_pms WHERE user_id=".$_SESSION["user_id"].")");
	while($a = mysql_fetch_array($result)) {
		$allusers[]=Array($a["id"],usname($a,true));
	}
	foreach ($allusers as $key => $row)
	{
		$allusers_sort[$key]  = strtolower($row[1]);
	}
	array_multisort($allusers_sort, SORT_ASC, $allusers);

	$obj_1->setValues($allusers);

	$result=mysql_query("SELECT id,icon FROM ".$prefix."blog_pms WHERE user_id=".$_SESSION["user_id"]." ORDER BY id DESC");
	while($a = mysql_fetch_array($result)) {
		$result2=mysql_query("SELECT img FROM ".$prefix."blog_icons WHERE id=".str_replace('-','',$a["icon"]));
		$b = mysql_fetch_array($result2);
		if($b["img"]!='') {
			$allicons[]=Array($a["id"],'<img src="'.$server_absolute_path.$uploads[1]['path'].$b["img"].'" />');
		}
		else {
			$allicons[]=Array($a["id"],'');
		}
	}

	$obj->setSort(Array(
			'0'	=>	Array(
				Array("date", "DESC", false, true),
				Array("id", "DESC", true, false, Array(2,$allicons)),
				Array("title", "ASC", true, true),
				Array("creator_id", "ASC", true, true, Array(2,$allusers)),
				Array("date", "ASC", true, true),
			),
		)
	);

	// Инициализация элементов поиска, если нужен.
    $obj->setSearch($obj_2);
	$obj->setSearch($obj_4);

	// Отрисовка всего объекта html'ем в переменную
	//if($act!='view') {
		$obj_html.=$obj->draw();
	/*}
	else {
		$result=mysql_query("SELECT * FROM ".$prefix."blog_pms where id=".$id);
		$a = mysql_fetch_array($result);
		$obj_html.='<h2>';
		if($a["icon"]!='') {
			$obj_html.='<img src="'.$server_absolute_path.$uploads[1]['path'].$a["icon"].'"/> ';
		}
		$obj_html.=decode($a["title"]).'</h2>';
	}*/

	if(preg_match('#Удалить сообщение#',$obj_html)) {
		$obj_html=str_replace('<button','<button class="main" onClick="window.location=\''.$server_absolute_path.'outbox/outbox/act=add&re='.$id.'\'">Ответить</button> <button class="nonimportant" onClick="window.location=\''.$server_absolute_path.'outbox/outbox/act=add&requote='.$id.'\'">С цитированием</button> <button',$obj_html);
    }

	// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
	$pagetitle=h1line('Входящие',$curdir.$kind.'/');
	$content2=str_replace('/inbox/inbox/act=add','/outbox/outbox/act=add',$obj_html);
}
?>