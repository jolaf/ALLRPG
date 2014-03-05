<?php

// Создание объекта
$obj=new netObj(
	'users',
	$prefix."users",
	"пользователя",
	Array("Пользователь успешно добавлен.","Пользователь успешно изменен.","Пользователь успешно удален."),
	Array(
		'0'=>Array(
			Array("sid", "DESC", true, true),
			Array("nick", "ASC", true, true),
			Array("fio", "ASC", true, true),
			Array("login", "ASC", true, true),
		)
	),
	2,
	600,
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
			'name'	=>	"sid",
			'sname'	=>	"ИНП",
			'help'	=>	"идентификационный номер пользователя.",
			'type'	=>	"number",
			'read'	=>	10,
			'write'	=>	10,
		)
);
$obj->setElem($obj_1);

$obj_2=createElem(Array(
			'name'	=>	"fio",
			'sname'	=>	"Ф.И.О.",
			'type'	=>	"text",
			'help'	=>	"фамилия, имя, отчетство полностью.",
			'read'	=>	10,
			'write'	=>	10,
		)
);
$obj->setElem($obj_2);

$obj_3=createElem(Array(
			'name'	=>	"nick",
			'sname'	=>	"Никнейм",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	10,
		)
);
$obj->setElem($obj_3);

$obj_4=createElem(Array(
			'name'	=>	"gender",
			'sname'	=>	"Пол",
			'type'	=>	"select",
			'values'	=>	Array(Array('1','мужской'),Array('2','женский')),
			'read'	=>	10,
			'write'	=>	10,
		)
);
$obj->setElem($obj_4);

$obj_5=createElem(Array(
			'name'	=>	"em",
			'sname'	=>	"Основной е-mail",
			'type'	=>	"email",
			'help'	=>	"вводите корректно!",
			'read'	=>	10,
			'write'	=>	10,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_5);

$obj_6=createElem(Array(
			'name'	=>	"em2",
			'sname'	=>	"Дополнительный е-mail",
			'type'	=>	"email",
			'read'	=>	10,
			'write'	=>	10,
		)
);
$obj->setElem($obj_6);

$obj_8=createElem(Array(
			'name'	=>	"phone2",
			'sname'	=>	"Контактный телефон",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	10,
		)
);
$obj->setElem($obj_8);

$obj_9=createElem(Array(
			'name'	=>	"icq",
			'sname'	=>	"ICQ",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	10,
		)
);
$obj->setElem($obj_9);

$obj_10=createElem(Array(
			'name'	=>	"skype",
			'sname'	=>	"Skype",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	10,
		)
);
$obj->setElem($obj_10);

$obj_11=createElem(Array(
			'name'	=>	"jabber",
			'sname'	=>	"Jabber",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	10,
		)
);
$obj->setElem($obj_11);

$obj_31=createElem(Array(
			'name'	=>	"vkontakte",
			'sname'	=>	"ВКонтакте",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	10,
		)
);
$obj->setElem($obj_31);

$obj_32=createElem(Array(
			'name'	=>	"livejournal",
			'sname'	=>	"Живой Журнал",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	10,
		)
);
$obj->setElem($obj_32);

$obj_33=createElem(Array(
			'name'	=>	"facebook",
			'sname'	=>	"Facebook",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	10,
		)
);
$obj->setElem($obj_33);

$obj_34=createElem(Array(
			'name'	=>	"googleplus",
			'sname'	=>	"Google+",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	10,
		)
);
$obj->setElem($obj_34);

$obj_35=createElem(Array(
			'name'	=>	"tweeter",
			'sname'	=>	"Twitter",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	10,
		)
);
$obj->setElem($obj_35);

$obj_12=createElem(Array(
			'name'	=>	"photo",
			'sname'	=>	"Фотография",
			'type'	=>	"file",
			'upload'	=>	4,
			'help'	=>	'не более 200*200 пикселей.',
			'read'	=>	10,
			'write'	=>	10,
		)
);
$obj->setElem($obj_12);

$obj_13=createElem(Array(
			'name'	=>	"login",
			'sname'	=>	"Логин",
			'type'	=>	"login",
			'help'	=>	"не менее 3 и не более 16 символов.",
			'minchar'	=>	3,
			'maxchar'	=>	16,
			'read'	=>	10,
			'write'	=>	10,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_13);

$obj_14=createElem(Array(
			'name'	=>	"pass",
			'sname'	=>	"Пароль",
			'type'	=>	"password",
			'help'	=>	"не менее 3 и не более 20 символов.",
			'minchar'	=>	3,
			'maxchar'	=>	20,
			'read'	=>	10,
			'write'	=>	10,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_14);

$obj_15=createElem(Array(
			'name'	=>	"pass2",
			'sname'	=>	"Повторите пароль",
			'type'	=>	"password2",
			'minchar'	=>	3,
			'maxchar'	=>	20,
			'read'	=>	10,
			'write'	=>	10,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_15);

$obj_16=createElem(Array(
			'name'	=>	"birth",
			'sname'	=>	"Дата рождения",
			'type'	=>	"calendar",
			'read'	=>	10,
			'write'	=>	10,
		)
);
$obj->setElem($obj_16);

$obj_17=createElem(Array(
			'name'	=>	"city",
			'sname'	=>	"Город",
			'type'	=>	"sarissa",
			'parents'	=>	Array(Array('country','Страна'),Array('region','Регион')),
			'file'	=>	$helpers_path.'geo.php',
			'table'	=>	$prefix.'geography',
			'parent'	=>	'parent',
			'read'	=>	10,
			'write'	=>	10,
			'width'	=>	200,
		)
);
$obj->setElem($obj_17);

$obj_18=createElem(Array(
			'name'	=>	"sickness",
			'sname'	=>	"Медицинские противопоказания",
			'type'	=>	"textarea",
			'read'	=>	10,
			'write'	=>	10,
			'help'	=>	"автоматически добавляется ко всем вашим заявкам."
		)
);
$obj->setElem($obj_18);

$obj_19=createElem(Array(
			'name'	=>	"ingroup",
			'sname'	=>	"Состою в мастерской группе",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	10,
		)
);
$obj->setElem($obj_19);

$obj_20=createElem(Array(
			'name'	=>	"prefer",
			'sname'	=>	"Предпочитаемые жанры игр",
			'type'	=>	"multiselect",
			'values'	=>	make5field($prefix."gametypes where tipe=1 order by name","id","name"),
			'images'	=>	make5field($prefix."gametypes where tipe=1","id","im"),
			'path'	=>	$server_absolute_path.$uploads[6]['path'],
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_20);

$obj_21=createElem(Array(
			'name'	=>	"prefer2",
			'sname'	=>	"Предпочитаемые типы игр",
			'type'	=>	"multiselect",
			'values'	=>	make5field($prefix."gametypes where tipe=2 order by name","id","name"),
			'images'	=>	make5field($prefix."gametypes where tipe=2","id","im"),
			'path'	=>	$server_absolute_path.$uploads[6]['path'],
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_21);

$obj_22=createElem(Array(
			'name'	=>	"prefer3",
			'sname'	=>	"Предпочитаемые миры игр",
			'type'	=>	"multiselect",
			'values'	=>	make5field($prefix."gameworlds order by name","id","name"),
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_22);

$obj_23=createElem(Array(
			'name'	=>	"prefer4",
			'sname'	=>	"Дополнительные предпочтения",
			'type'	=>	"multiselect",
			'values'	=>	make5field($prefix."gametypes where tipe=3 order by name","id","name"),
			'images'	=>	make5field($prefix."gametypes where tipe=3","id","im"),
			'path'	=>	$server_absolute_path.$uploads[6]['path'],
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_23);

$obj_24=createElem(Array(
			'name'	=>	"specializ",
			'sname'	=>	"Основная специализация на играх",
			'type'	=>	"multiselect",
			'values'	=>	make5field($prefix."specializ where gr=1 order by name","id","name"),
			'images'	=>	make5field($prefix."specializ where gr=1","id","im"),
			'path'	=>	$server_absolute_path.$uploads[7]['path'],
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_24);

$obj_25=createElem(Array(
			'name'	=>	"additional",
			'sname'	=>	"Дополнительная информация",
			'type'	=>	"textarea",
			'read'	=>	10,
			'write'	=>	10,
		)
);
$obj->setElem($obj_25);

$obj_26=createElem(Array(
			'name'	=>	"hidesome",
			'sname'	=>	"Скрыть в моем профиле следующие данные",
			'type'	=>	"multiselect",
			'values'	=>	Array(Array('0','никнейм'),Array('10','ф.и.о.'),Array('1','фото'),Array('2','основной e-mail'),Array('3','дополнительный e-mail'),Array('5','контактный телефон'),Array('6','ICQ'),Array('7','Skype'),Array('8','Jabber'),Array('9','медицинские противопоказания')),
			'read'	=>	10,
			'write'	=>	10,
			'default'	=>	'-2-',
		)
);
$obj->setElem($obj_26);

$obj_29=createElem(Array(
			'name'	=>	"rights",
			'sname'	=>	"Права",
			'type'	=>	"multiselect",
			'values'	=>	Array(Array('1','Администратор'),Array('2','Автор новостей'),Array('3','Редактор событий/полигонов'),Array('5','Может добавлять статьи в БЗ')),
			'read'	=>	100,
			'write'	=>	100,
		)
);
$obj->setElem($obj_29);

$obj_30=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Последнее изменение",
			'type'	=>	"timestamp",
			'read'	=>	100,
			'write'	=>	100,
			'mustbe'	=>	true,
			'show'	=>	false,
		)
);
$obj->setElem($obj_30);

// Исполнение dynamicaction, если необходимо
if($action=="dynamicaction")
{
	require_once($server_inner_path.$direct."/dynamicaction.php");
	if($object=="users")
	{
		dynamicaction($obj);
	}
}

// Исполнение дополнительных действий после dynamicaction, если необходимо
if(!$trouble && count($trouble2)==0)
{
	if($object=="users")
	{
		if($actiontype=="add") {
			$result=mysql_query("SELECT * from ".$prefix."users order by sid desc limit 0,1");
			$a=mysql_fetch_array($result);
			$sid=$a["sid"]+1;
			mysql_query("UPDATE ".$prefix."users SET sid='".$sid."' WHERE id='$id'");
		}
	}
}

// Добавление параметра values к select'ам и multiselect'ам.

// Инициализация элементов поиска, если нужен.
$obj->setSearch($obj_1);
$obj->setSearch($obj_2);
$obj->setSearch($obj_3);
$obj->setSearch($obj_5);
$obj->setSearch($obj_6);
$obj->setSearch($obj_13);
$obj->setSearch($obj_29);

// Отрисовка всего объекта html'ем в переменную
$obj_html.=$obj->draw();

// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
$content2.='<h1>ПОЛЬЗОВАТЕЛИ</h1>'.$obj_html;
?>