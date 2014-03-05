<?php
if($_SESSION["user_id"]!='') {
	$bazecount=$_SESSION["bazecount"];
	if($bazecount=='') {
		$bazecount=50;
	}

	//мои отчеты в базе

	// Создание объекта
	$obj=new netObj(
		'myreports',
		$prefix."reports",
		"отчет",
		Array("Отчет успешно добавлен.","Отчет успешно изменен.","Отчет успешно удален."),
		Array(
			'0'=>Array(
				Array("game", "ASC", true, true,Array(3,$prefix."allgames","id","name")),
				Array("name", "ASC", true, true),
			)
		),
		2,
		'100%',
		$bazecount
	);

	// Создание схемы прав объекта
	if($_SESSION["admin"]) {
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
	else {
		$obj_r=new netRight(
			true,
			true,
			true,
			true,
			100,
			'user_id='.$_SESSION["user_id"],
			'user_id='.$_SESSION["user_id"],
			'user_id='.$_SESSION["user_id"]
		);
		$obj->setRight($obj_r);
	}

	$obj_1=createElem(Array(
			'name'	=>	"game",
			'sname'	=>	"Событие из инфотеки",
			'type'	=>	"sarissa",
			'parents'	=>	'search',
			'file'	=>	$helpers_path.'gameslist.php',
			'table'	=>	$prefix.'allgames',
			'order'	=>	'name',
			'moreparams'	=>	$moreparams,
			'help'	=>	"в данном списке представлены только те события, которые есть в <a href=\"".$server_absolute_path_info."events/\">инфотеке</a>. Добавить событие в этот список Вы можете <a href=\"".$server_absolute_path_info."myevents/\">здесь</a>.",
			'read'	=>	10,
			'write'	=>	100,
			'default'	=>	encode($_GET["game"]),
			'mustbe'	=>	true,
		)
	);
	$obj->setElem($obj_1);

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
			'read'	=>	10,
			'write'	=>	100,
			'height'	=>	400,
			'mustbe'	=>	true,
		)
	);
	$obj->setElem($obj_3);

	$obj_4=createElem(Array(
				'name'	=>	"date",
				'sname'	=>	"Последнее изменение",
				'type'	=>	"timestamp",
				'read'	=>	100,
				'write'	=>	100,
				'mustbe'	=>	true
			)
	);
	$obj->setElem($obj_4);

	// Исполнение dynamicaction, если необходимо
	if($action=="dynamicaction")
	{
		require_once($server_inner_path.$direct."/dynamicaction.php");
		if($object=="myreports")
		{
			function dynamic_add_success() {
				global
					$prefix,
					$_SESSION,
					$id;

				mysql_query("UPDATE ".$prefix."reports SET user_id=".$_SESSION['user_id']." WHERE id=".$id);
			}
			dynamicaction($obj);
		}
	}

	// Добавление параметра values к select'ам и multiselect'ам.

	$pagetitle=h1line('Мои отчеты',$curdir.$kind.'/');
	$content2.='<div class="narrow">'.$obj->draw().'</div>';
}
?>