<?php
$bazecount=$_SESSION["bazecount"];
if($bazecount=='') {
	$bazecount=50;
}

//мои полигоны в базе

// Создание объекта
$obj=new netObj(
	'myareas',
	$prefix."areas",
	"полигон",
	Array("Полигон успешно добавлен в Базу знаний.","Полигон успешно изменен.","Полигон успешно удален из Базы знаний."),
	Array(
		'0'=>Array(
			Array("name", "ASC", true, true),
		)
	),
	2,
	'100%',
	$bazecount
);

// Создание схемы прав объекта
if($_SESSION["admin"] || $_SESSION["candoevents"])
{
	$obj_r=new netRight(
		true,
		true,
		true,
		false,
		100,
		'',
		'',
		''
	);
	$obj->setRight($obj_r);
}
elseif($_SESSION["user_sid"]!='')
{
	$obj_r=new netRight(
		true,
		true,
		true,
		false,
		100,
		'user_id='.$_SESSION["user_sid"],
		'user_id='.$_SESSION["user_sid"],
		'user_id='.$_SESSION["user_sid"]
	);
	$obj->setRight($obj_r);
}
else {
	$obj_r=new netRight(
		true,
		true,
		true,
		false,
		100,
		"addip='".get_real_ip()."' and tomoderate='1'",
		"addip='".get_real_ip()."' and tomoderate='1'",
		"addip='".get_real_ip()."' and tomoderate='1'"
	);
	$obj->setRight($obj_r);
}

// Создание полей объекта
$obj_3=createElem(Array(
		'name'	=>	"togame",
		'sname'	=>	"Карточка полигона в инфотеке",
		'type'	=>	"text",
		'read'	=>	10,
		'write'	=>	100000,
	)
);
$obj->setElem($obj_3);

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

if($_SESSION["admin"] || $_SESSION["candoevents"])
{
	$obj_13=createElem(Array(
				'name'	=>	"kogdaigra_id",
				'sname'	=>	"Kogda-igra",
				'type'	=>	"number",
				'read'	=>	10,
				'write'	=>	100,
			)
	);
	$obj->setElem($obj_13);
}

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
	if($object=="myareas")
	{
		function dynamic_add_success() {
			global
				$prefix,
				$_SESSION,
				$id;

			if($_SESSION['user_sid']!='') {
				mysql_query("UPDATE ".$prefix."areas SET user_id=".$_SESSION['user_sid']." WHERE id=".$id);
			}
			else {
				mysql_query("UPDATE ".$prefix."areas SET addip='".get_real_ip()."', tomoderate='1' WHERE id=".$id);
			}
		}
		dynamicaction($obj);
	}
}

// Добавление параметра values к select'ам и multiselect'ам.
if($id!='') {
	$obj_3->setDefault('<a href="'.$server_absolute_path_info.'areas/'.$id.'/" target="_blank">открыть</a>');
}

$pagetitle=h1line('Мои полигоны в инфотеке',$curdir.$kind.'/');
$content2.='<div class="narrow">'.$obj->draw().'</div>';
?>