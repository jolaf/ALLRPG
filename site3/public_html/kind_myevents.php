<?php
$bazecount=$_SESSION["bazecount"];
if($bazecount=='') {
	$bazecount=50;
}

if ($_GET['act'] == 'add')
{
  redirect('http://kogda-igra.ru/edit/game/?from_allrpg');
}

$game_iddef=encode($_GET["game"]);

//мои события в базе

// Создание объекта
$obj=new netObj2(
	'myevents',
	$prefix."allgames",
	"событие",
	Array("Событие успешно добавлено в инфотеку.","Событие успешно изменено.","Событию успешно проставлен статус 'Отменено'.","Событию успешно проставлен статус 'Отменено'."),
	"мастера",
	Array("Мастер успешно добавлен в инфотеку.","Мастер успешно изменен.","Мастер успешно удален из инфотеки."),
	Array(
		'0'	=>	Array(
			Array("name", "ASC", true, true),
		),
		'1'	=>	Array(
			Array("id", "ASC", true, true),
		),
	),
	3,
	'100%',
	$bazecount,
	'parent',
	'master',
	'name',
	'name'
);

// Создание схемы прав объекта
if($_SESSION["admin"] || $_SESSION["candoevents"])
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
elseif($_SESSION["user_sid"]!='')
{
	$obj_r=new netRight(
		true,
		true,
		true,
		false,
		100,
		'sid='.$_SESSION["user_sid"],
		'sid='.$_SESSION["user_sid"],
		'sid='.$_SESSION["user_sid"]
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
$obj_2=createElem(Array(
		'name'	=>	"togame",
		'sname'	=>	"Карточка события в инфотеке",
		'type'	=>	"text",
		'read'	=>	10,
		'write'	=>	100000,
	)
);
$obj->setElem($obj_2);

$obj_31=createElem(Array(
		'name'	=>	"togame2",
		'sname'	=>	"Добавить мастера к событию",
		'type'	=>	"text",
		'read'	=>	10,
		'write'	=>	100000,
	)
);
$obj->setElem($obj_31);

$obj_1=createElem(Array(
			'name'	=>	"name",
			'sname'	=>	"Название",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_1);

$obj_32=createElem(Array(
			'name'	=>	"wascancelled",
			'sname'	=>	"Событие отменено",
			'type'	=>	"checkbox",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_32);

$obj_33=createElem(Array(
			'name'	=>	"moved",
			'sname'	=>	"Событие перенесено (новые даты не определены)",
			'type'	=>	"checkbox",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_33);

$obj_28=createElem(Array(
			'name'	=>	"region",
			'sname'	=>	"Регион",
			'type'	=>	"sarissa",
			'parents'	=>	Array(Array('country','Страна')),
			'file'	=>	$helpers_path.'geo.php',
			'table'	=>	$prefix.'geography',
			'parent'	=>	'parent',
			'read'	=>	10,
			'write'	=>	100,
			'order'	=>	'name',
			'moreparams2'	=>	" and id!=2562 and parent!=2562",
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_28);

$obj_4=createElem(Array(
			'name'	=>	"area",
			'sname'	=>	"Полигон",
			'type'	=>	"select",
			'values'	=>	make5field($prefix."areas order by name","id","name"),
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_4);

$obj_5=createElem(Array(
			'name'	=>	"gametype",
			'sname'	=>	"Жанр",
			'type'	=>	"multiselect",
			'one'	=>	true,
			'values'	=>	make5field($prefix."gametypes where tipe=1 order by name","id","name"),
			'images'	=>	make5field($prefix."gametypes where tipe=1 order by name","id","im"),
			'path'	=>	$server_absolute_path.$uploads[6]['path'],
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_5);

$obj_6=createElem(Array(
			'name'	=>	"gametype2",
			'sname'	=>	"Тип",
			'type'	=>	"multiselect",
			'one'	=>	true,
			'values'	=>	make5field($prefix."gametypes where tipe=2 order by name","id","name"),
			'images'	=>	make5field($prefix."gametypes where tipe=2 order by name","id","im"),
			'path'	=>	$server_absolute_path.$uploads[6]['path'],
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_6);

$obj_7=createElem(Array(
			'name'	=>	"gametype3",
			'sname'	=>	"Мир",
			'type'	=>	"select",
			'one'	=>	true,
			'values'	=>	make5field($prefix."gameworlds order by name","id","name"),
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_7);

$obj_8=createElem(Array(
			'name'	=>	"gametype4",
			'sname'	=>	"Дополнительно",
			'type'	=>	"multiselect",
			'values'	=>	make5field($prefix."gametypes where tipe=3 order by name","id","name"),
			'images'	=>	make5field($prefix."gametypes where tipe=3 order by name","id","im"),
			'path'	=>	$server_absolute_path.$uploads[6]['path'],
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_8);

$obj_9=createElem(Array(
			'name'	=>	"mg",
			'sname'	=>	"Мастерская группа",
			'type'	=>	"text",
			'help'	=>	'<b>крайне рекомендуем</b> ознакомиться с имеющимся списком МГ <a href="http://inf.allrpg.info/mg/" target="_blank">здесь</a>. Если нужная МГ уже есть в данном списке, скопируйте название МГ, иначе событие будет неверно отображаться в этом списке. Можно перечислять несколько МГ через запятую.',
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_9);

$obj_10=createElem(Array(
			'name'	=>	"site",
			'sname'	=>	"Сайт",
			'type'	=>	"text",
			'help'	=>	"например: http://www.allrpg.info",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_10);

$obj_30=createElem(Array(
			'name'	=>	"orderpage",
			'sname'	=>	"Страница подачи заявок",
			'type'	=>	"text",
			'help'	=>	"если у Вашего события есть страница подачи заявки, можете указать ее http-адрес здесь.",
			'maxchar'	=>	255,
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_30);

$obj_11=createElem(Array(
			'name'	=>	"datestart",
			'sname'	=>	"Дата начала",
			'type'	=>	"calendar",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_11);

$obj_12=createElem(Array(
			'name'	=>	"datefinish",
			'sname'	=>	"Дата окончания",
			'type'	=>	"calendar",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_12);

$obj_13=createElem(Array(
			'name'	=>	"datearrival",
			'sname'	=>	"Дата заезда",
			'type'	=>	"calendar",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_13);

$obj_14=createElem(Array(
			'name'	=>	"playernum",
			'sname'	=>	"Количество участников",
			'type'	=>	"number",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_14);

$obj_15=createElem(Array(
			'name'	=>	"descr",
			'sname'	=>	"Описание события",
			'type'	=>	"wysiwyg",
			'help'	=>	"cюда можно вписать ссылки на ЖЖ и форум игры.",
			'height'	=>	400,
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_15);

$obj_16=createElem(Array(
			'name'	=>	"logo",
			'sname'	=>	"Логотип события",
			'type'	=>	"file",
			'upload'	=>	10,
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_16);

if($_SESSION["admin"] || $_SESSION["candoevents"])
{
	$obj_34=createElem(Array(
				'name'	=>	"kogdaigra_id",
				'sname'	=>	"Kogda-igra",
				'type'	=>	"number",
				'read'	=>	10,
				'write'	=>	100,
			)
	);
	$obj->setElem($obj_34);
}

if($_SESSION["admin"] || $_SESSION["candoevents"])
{
	$obj_35=createElem(Array(
				'name'	=>	"agroup",
				'sname'	=>	"Группа событий",
				'type'	=>	"select",
				'values'	=>	make5field($prefix."allgames_groups order by name","id","name"),
				'read'	=>	10,
				'write'	=>	100,
			)
	);
	$obj->setElem($obj_35);
}

$obj_17=createElem(Array(
			'name'	=>	"master",
			'sname'	=>	"Проверочное поле",
			'type'	=>	"hidden",
			'default'	=>	"{menu}",
			'read'	=>	100,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_17);

$obj_18=createElem(Array(
			'name'	=>	"parent",
			'sname'	=>	"Проверочное поле 2",
			'type'	=>	"hidden",
			'default'	=>	"0",
			'read'	=>	100,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_18);

$obj_19=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Последнее изменение",
			'type'	=>	"timestamp",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_19);

if($_SESSION["user_sid"]!='') {
	$obj_20=createElem(Array(
				'name'	=>	"parent",
				'sname'	=>	"Игра",
				'type'	=>	"select",
				'values'	=>	make5field($prefix."allgames where master='{menu}' and sid=".$_SESSION["user_sid"]." order by name","id","name"),
				'read'	=>	10,
				'write'	=>	100,
				'default'	=>	$game_iddef,
				'mustbe'	=>	true
			)
	);
	$obj->setElem2($obj_20);
}
else {
	$obj_20=createElem(Array(
				'name'	=>	"parent",
				'sname'	=>	"Игра",
				'type'	=>	"select",
				'values'	=>	make5field($prefix."allgames where master='{menu}' and addip='".get_real_ip()."' and tomoderate='1' order by name","id","name"),
				'read'	=>	10,
				'write'	=>	100,
				'default'	=>	$game_iddef,
				'mustbe'	=>	true
			)
	);
	$obj->setElem2($obj_20);
}

$obj_23=createElem(Array(
			'name'	=>	"user_id",
			'sname'	=>	"Мастер",
			'type'	=>	"sarissa",
			'parents'	=>	'search',
			'searchfield'	=>	'sid',
			'file'	=>	$helpers_path.'userslist.php',
			'table'	=>	$prefix.'users',
			'help'	=>	'найдите участника allrpg.info через Ф.И.О., никнейм или ИНП.',
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem2($obj_23);

$obj_24=createElem(Array(
			'name'	=>	"user_id",
			'sname'	=>	"Профиль мастера в инфотеке",
			'type'	=>	"select",
			'read'	=>	10,
			'write'	=>	100000,
		)
);
$obj->setElem2($obj_24);

$obj_25=createElem(Array(
			'name'	=>	"name",
			'sname'	=>	"Ф.И.О. мастера",
			'type'	=>	"text",
			'help'	=>	"заполните данное поле, только если данный человек <b>НЕ ЗАРЕГИСТРИРОВАН</b> на allrpg.info.",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem2($obj_25);

$obj_26=createElem(Array(
			'name'	=>	"master",
			'sname'	=>	"Тип мастера",
			'type'	=>	"multiselect",
			'values'	=>	make5field($prefix."specializ where gr=2 OR gr=3 order by gr, name","id","name"),
			'images'	=>	make5field($prefix."specializ where gr=2 OR gr=3 order by gr, name","id","im"),
			'path'	=>	$server_absolute_path.$uploads[7]['path'],
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem2($obj_26);

$obj_27=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Последнее изменение",
			'type'	=>	"timestamp",
			'read'	=>	100,
			'write'	=>	100,
			'mustbe'	=>	true,
			'show'	=>	true,
		)
);
$obj->setElem2($obj_27);

// Исполнение dynamicaction, если необходимо
if($action=="dynamicaction")
{
	require_once($server_inner_path.$direct."/dynamicaction.php");
	if($object=="myevents" && $actiontype!="")
	{
		function dynamic_add_success() {
			global
				$prefix,
				$id,
				$_SESSION,
				$_POST,
				$server_absolute_path_info;

			if($_SESSION['user_sid']!='') {
				mysql_query("UPDATE ".$prefix."allgames SET sid=".$_SESSION['user_sid']." WHERE id=".$id);
			}
			else {
				mysql_query("UPDATE ".$prefix."allgames SET addip='".get_real_ip()."', tomoderate='1' WHERE id=".$id);
			}
			if(encode_to_cp1251($_POST["parent"])==0) {
				file("http://kogda-igra.ru/api/game/add.php?uri=".$server_absolute_path_info."events/".$id."/&automated=1");
			}
		}

		if($valuestype==0 && strtotime(encode_to_cp1251($_POST["datefinish"]))<strtotime(encode_to_cp1251($_POST["datestart"]))) {
			$_POST["datefinish"]=$_POST["datestart"];
		}
		if($valuestype==1 && encode_to_cp1251($_POST["user_id"])>0 && encode_to_cp1251($_POST["name"])!='') {
			dynamic_err_one('error',"Необходимо или найти мастера, или заполнить вручную Ф.И.О. мастера, а не оба поля!",array('name','user_id'));
		}
		else {
			dynamicaction($obj);
		}
	}
}

// Добавление параметра values к select'ам и multiselect'ам.
if($id!='') {
	$obj_2->setDefault('<a href="'.$server_absolute_path_info.'events/'.$id.'/" target="_blank">открыть</a>');
	$obj_31->setDefault('<a href="'.$server_absolute_path_info.'myevents/act=add&valuestype=1&game='.$id.'" target="_blank">добавить</a>');
}
if($_SESSION['admin'] || $_SESSION["candoevents"]) {
	$result=mysql_query("SELECT DISTINCT user_id FROM ".$prefix."allgames WHERE parent!=0 and user_id>0");
}
elseif($_SESSION['user_sid']!='') {
	$result=mysql_query("SELECT DISTINCT user_id FROM ".$prefix."allgames WHERE parent!=0 and user_id>0 and sid=".$_SESSION['user_sid']);
}
else {
	$result=mysql_query("SELECT DISTINCT user_id FROM ".$prefix."allgames WHERE parent!=0 and user_id>0 and addip='".get_real_ip()."'");
}
while($a=mysql_fetch_array($result)) {
	$result2=mysql_query("SELECT * FROM ".$prefix."users WHERE sid=".$a["user_id"]);
	$b=mysql_fetch_array($result2);
	$mastersvalues[]=Array($a["user_id"],usname($b,true,true));
}
$obj_24->setValues($mastersvalues);

if($_SESSION['admin'] || $_SESSION["candoevents"]) {
	$result=mysql_query("SELECT * FROM ".$prefix."allgames WHERE parent!=0");
}
elseif($_SESSION['user_sid']!='') {
	$result=mysql_query("SELECT * FROM ".$prefix."allgames WHERE parent!=0 and sid=".$_SESSION['user_sid']);
}
else {
	$result=mysql_query("SELECT * FROM ".$prefix."allgames WHERE parent!=0 and addip='".get_real_ip()."'");
}
while($a=mysql_fetch_array($result)) {
	if($a["user_id"]>0) {
		$result2=mysql_query("SELECT * FROM ".$prefix."users WHERE sid=".$a["user_id"]);
		$b=mysql_fetch_array($result2);
		$allmasters[]=Array($a["id"],usname($b,true));
	}
	else {
		$allmasters[]=Array($a["id"],decode($a["name"]));
	}
}

$obj_29=createElem(Array(
			'name'	=>	"id",
			'sname'	=>	"Мастер",
			'type'	=>	"select",
			'read'	=>	100000,
			'write'	=>	100000,
		)
);
$obj->setElem2($obj_29);

foreach ($allmasters as $key => $row) {
	$allmasterssort[$key]  = strtolower($row[1]);
}
array_multisort($allmasterssort, SORT_ASC, $allmasters);

$obj->setSort(Array(
		'0'	=>	Array(
			Array("name", "ASC", true, true),
		),
		'1'	=>	Array(
			Array("id", "ASC", true, true, Array(2,$allmasters)),
		),
	)
);

$pagetitle=h1line('Мои события в инфотеке',$curdir.$kind.'/');
$content2.='<div class="narrow">'.$obj->draw().'</div>';
$content2=str_replace("удалить событие","отменить событие", $content2);
?>