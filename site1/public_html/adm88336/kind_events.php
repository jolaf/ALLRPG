<?php

if($id!='' && $actiontype=='') {
	$result=mysql_query("SELECT * FROM ".$prefix."allgames where parent=0 and id=".$id);
}
else {
	$result=mysql_query("SELECT * FROM ".$prefix."allgames where parent=0 order by id");
}
while($a=mysql_fetch_array($result)) {
	$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."played where game=".$a["id"]);
	$b=mysql_fetch_array($result2);
	$portfolio[]=Array($a["id"],$b[0]);
}

// Создание объекта
$obj=new netObj2(
	'events',
	$prefix."allgames",
	"событие",
	Array("Событие успешно добавлено.","Событие успешно изменено.","Событие успешно удалено.","Событие и все его мастера успешно удалены."),
	"мастера",
	Array("Мастер успешно добавлен.","Мастер успешно изменен.","Мастер успешно удален."),
	Array(
		'0'	=>	Array(
			Array("name", "ASC", true, true),
		),
		'1'	=>	Array(
			Array("name", "ASC", true, true),
		),
	),
	3,
	900,
	50,
	'parent',
	'master',
	'datestart',
	'name'
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
			'name'	=>	"name",
			'sname'	=>	"Название",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_1);

$obj_2=createElem(Array(
			'name'	=>	"sid",
			'sname'	=>	"ИНП автора информации по событию",
			'type'	=>	"number",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_2);

$obj_3=createElem(Array(
			'name'	=>	"sid",
			'sname'	=>	"Автор информации",
			'type'	=>	"select",
			'read'	=>	10,
			'write'	=>	100000,
		)
);
$obj->setElem($obj_3);

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
			'width'	=>	200,
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

$obj_35=createElem(Array(
			'name'	=>	"orderpage",
			'sname'	=>	"Страница подачи заявок",
			'type'	=>	"text",
			'help'	=>	"если у Вашего события есть страница подачи заявки, можете указать ее http-адрес здесь.",
			'maxchar'	=>	255,
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_35);

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

$obj_29=createElem(Array(
			'name'	=>	"tomoderate",
			'sname'	=>	"Требует модерации",
			'type'	=>	"checkbox",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_29);

$obj_30=createElem(Array(
			'name'	=>	"addip",
			'sname'	=>	"IP человека, внесшего событие",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	100000,
		)
);
$obj->setElem($obj_30);

$obj_31=createElem(Array(
			'name'	=>	"wascancelled",
			'sname'	=>	"Отменено",
			'type'	=>	"checkbox",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_31);

$obj_36=createElem(Array(
			'name'	=>	"moved",
			'sname'	=>	"Событие перенесено (новые даты не определены)",
			'type'	=>	"checkbox",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_36);

$obj_37=createElem(Array(
			'name'	=>	"id",
			'sname'	=>	"В портфолио у",
			'type'	=>	"select",
			'read'	=>	10,
			'write'	=>	100000,
		)
);
$obj->setElem($obj_37);

$obj_37->setValues($portfolio);

$obj_38=createElem(Array(
			'name'	=>	"kogdaigra_id",
			'sname'	=>	"Kogda-igra",
			'type'	=>	"number",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_38);

$obj_39=createElem(Array(
			'name'	=>	"agroup",
			'sname'	=>	"Группа событий",
			'type'	=>	"select",
			'values'	=>	make5field($prefix."allgames_groups order by name","id","name"),
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_39);

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

$obj_20=createElem(Array(
			'name'	=>	"parent",
			'sname'	=>	"Игра",
			'type'	=>	"select",
			'values'	=>	make5field($prefix."allgames where master='{menu}' order by name","id","name"),
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem2($obj_20);

$obj_21=createElem(Array(
			'name'	=>	"sid",
			'sname'	=>	"ИНП автора информации по мастеру",
			'type'	=>	"number",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem2($obj_21);

$obj_22=createElem(Array(
			'name'	=>	"sid",
			'sname'	=>	"Автор информации",
			'type'	=>	"select",
			'read'	=>	10,
			'write'	=>	100000,
		)
);
$obj->setElem2($obj_22);

$obj_23=createElem(Array(
			'name'	=>	"user_id",
			'sname'	=>	"ИНП мастера",
			'type'	=>	"number",
			'help'	=>	"заполните данное поле, если данный человек зарегистрирован на allrpg.info, <u>ИЛИ</u> заполните поле «Ф.И.О. мастера» ниже",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem2($obj_23);

$obj_24=createElem(Array(
			'name'	=>	"user_id",
			'sname'	=>	"Информация по мастеру из инфотеки",
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
			'help'	=>	"заполните данное поле, если данный человек не зарегистрирован на allrpg.info, <u>ИЛИ</u> заполните поле «ИНП мастера» выше",
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
			'width'	=>	260,
			'mustbe'	=>	true
		)
);
$obj->setElem2($obj_26);

$obj_32=createElem(Array(
			'name'	=>	"tomoderate",
			'sname'	=>	"Требует модерации",
			'type'	=>	"checkbox",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem2($obj_32);

$obj_33=createElem(Array(
			'name'	=>	"addip",
			'sname'	=>	"IP человека, внесшего мастера",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	100000,
		)
);
$obj->setElem2($obj_33);

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
	if($object=="events")
	{
		dynamicaction($obj);
	}
}

// Исполнение дополнительных действий после dynamicaction, если необходимо
if(!$trouble && count($trouble2)==0)
{
	if($actiontype=="change" && encode($_POST["master"])=='{menu}') {
		mysql_query("UPDATE ".$prefix."allgames SET tomoderate='0' where id=".$id);
		$result=mysql_query("SELECT * FROM ".$prefix."allgames where parent=".$id);
		while($a=mysql_fetch_array($result)) {
			mysql_query("UPDATE ".$prefix."allgames SET sid=".encode($_POST["sid"]).", tomoderate='0' where id=".$a["id"]);
		}
	}
}

// Добавление параметра values к select'ам и multiselect'ам.
$result=mysql_query("SELECT sid FROM ".$prefix."allgames where sid!=0");
while($a=mysql_fetch_array($result)) {
	$result2=mysql_query("SELECT * FROM ".$prefix."users where sid=".$a["sid"]);
	$b=mysql_fetch_array($result2);
	$sidinfo[]=Array($a["sid"],usname($b,true,true));
}

$result=mysql_query("SELECT user_id FROM ".$prefix."allgames where parent>0 and user_id>0");
while($a=mysql_fetch_array($result)) {
	$result2=mysql_query("SELECT * FROM ".$prefix."users where sid=".$a["user_id"]);
	$b=mysql_fetch_array($result2);
	$userinfo[]=Array($a["user_id"],usname($b,true,true));
}

$obj_3->setValues($sidinfo);
$obj_22->setValues($sidinfo);
$obj_24->setValues($userinfo);

// Инициализация элементов поиска, если нужен.
$obj->setSearch($obj_1);
$obj->setSearch($obj_11);
$obj->setSearch($obj_12);
$obj->setSearch($obj_29);
$obj->setSearch($obj_31);
$obj->setSearch($obj_38);


if($id!='' && $actiontype=='') {
	$result=mysql_query("SELECT * FROM ".$prefix."allgames WHERE parent!=0 and id=".$id);
}
else {
	$result=mysql_query("SELECT * FROM ".$prefix."allgames WHERE parent!=0");
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

$obj_34=createElem(Array(
			'name'	=>	"id",
			'sname'	=>	"Мастер",
			'type'	=>	"select",
			'read'	=>	100000,
			'write'	=>	100000,
		)
);
$obj->setElem2($obj_34);

foreach ($allmasters as $key => $row) {
	$allmasterssort[$key]  = strtolower($row[1]);
}
array_multisort($allmasterssort, SORT_ASC, $allmasters);

$obj->setSort(Array(
		'0'	=>	Array(
			Array("datestart", "DESC", true, true),
			Array("name", "ASC", true, true),
			Array("id", "ASC", true, true, Array(2, $portfolio)),
			Array("kogdaigra_id", "ASC", true, true),
		),
		'1'	=>	Array(
			Array("id", "ASC", true, true, Array(2,$allmasters)),
		),
	)
);

// Отрисовка всего объекта html'ем в переменную
$obj_html.=$obj->draw();

// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
$content2.='<h1>СОБЫТИЯ И МАСТЕРА</h1>'.$obj_html;
?>