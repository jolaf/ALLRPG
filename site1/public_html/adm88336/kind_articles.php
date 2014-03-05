<?php

// Создание объекта
$obj=new netObj2(
	'articles',
	$prefix."articles",
	"раздел/подраздел",
	Array("Раздел/подраздел успешно добавлен.","Раздел/подраздел успешно изменен.","Раздел/подраздел успешно удален.","Раздел/подраздел и всего его статьи успешно удалены."),
	"статью",
	Array("Статья успешно добавлена.","Статья успешно изменена.","Статья успешно удалена."),
	Array(
		'0'	=>	Array(
			Array("code", "ASC", false, true),
			Array("name", "ASC", true, true),
			Array("code", "ASC", true, true),
		),
		'1'	=>	Array(
			Array("code", "ASC", false, true),
			Array("name", "ASC", true, true),
			Array("date", "ASC", true, true),
		),
	),
	3,
	645,
	50,
	'parent',
	'content',
	'code',
	'name'
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
			'name'	=>	"parent",
			'sname'	=>	"Родительский раздел",
			'type'	=>	"select",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_1);

$obj_2=createElem(Array(
			'name'	=>	"code",
			'sname'	=>	"Порядок сортировки",
			'type'	=>	"number",
			'round'	=>	true,
			'help'	=>	"в каком порядке выводить разделы в списке разделов статей? Разделы с меньшим значением очередности идут раньше. Вводить можно только целые числа: от 1 и более.",
			'default'	=>	"1",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_2);

$obj_3=createElem(Array(
			'name'	=>	"name",
			'sname'	=>	"Название раздела/подраздела",
			'type'	=>	"text",
			'help'	=>	"не более 255 символов.",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_3);

$obj_4=createElem(Array(
			'name'	=>	"content2",
			'sname'	=>	"дополнительный текст под названием раздела.",
			'type'	=>	"textarea",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_4);

$obj_5=createElem(Array(
			'name'	=>	"active",
			'sname'	=>	"Раздел активен",
			'type'	=>	"checkbox",
			'default'	=>	1,
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_5);

$obj_6=createElem(Array(
			'name'	=>	"im",
			'sname'	=>	"Картинка раздела",
			'type'	=>	"file",
			'upload'	=>	11,
			'help'	=>	'не более 50*50 пикселей.',
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_6);

$obj_7=createElem(Array(
			'name'	=>	"content",
			'type'	=>	"hidden",
			'default'	=>	"{menu}",
			'read'	=>	100,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_7);

$obj_8=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Последнее изменение",
			'type'	=>	"timestamp",
			'read'	=>	100,
			'write'	=>	100,
			'mustbe'	=>	true,
			'show'	=>	true,
		)
);
$obj->setElem($obj_8);

if($act!="add")
{
	$result=mysql_query("SELECT * from ".$prefix."articles where id=".$id);
	$a=mysql_fetch_array($result);
	$result2=mysql_query("SELECT * from ".$prefix."articles where id=".$a["parent"]);
	$b=mysql_fetch_array($result2);
	$lin='<a href="'.$server_absolute_path_info.'articles/'.$id.'/subobj='.$b["id"].'" target="_blank">'.$server_absolute_path_info.'articles/'.$id.'/subobj='.$b["id"].'</a>';
}

$obj_9=createElem(Array(
			'name'	=>	"lin",
			'sname'	=>	"Ссылка на статью на сайте",
			'type'	=>	"text",
			'default'	=>	$lin,
			'read'	=>	10,
			'write'	=>	100000,
		)
);
$obj->setElem2($obj_9);

$obj_10=createElem(Array(
			'name'	=>	"parent",
			'sname'	=>	"Разместить в разделе",
			'type'	=>	"select",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem2($obj_10);

$obj_11=createElem(Array(
			'name'	=>	"code",
			'type'	=>	"hidden",
			'default'	=>	'0',
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem2($obj_11);

$obj_12=createElem(Array(
			'name'	=>	"name",
			'sname'	=>	"Название статьи",
			'type'	=>	"text",
			'help'	=>	"не более 255 символов.",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true,
		)
);
$obj->setElem2($obj_12);

$obj_13=createElem(Array(
			'name'	=>	"content2",
			'sname'	=>	"Дополнительный текст под названием статьи",
			'type'	=>	"textarea",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem2($obj_13);

$obj_14=createElem(Array(
			'name'	=>	"author",
			'sname'	=>	"Автор (-ы)",
			'type'	=>	"text",
			'help'	=>	"в данное поле можно ввести Ф.И.О. автора или же указать его ИНП на allrpg.info (одно из двух). Во втором случае имя автора статьи на сайте будет превращено в ссылку, ведущую на его карточку в «<a href=\"http://www.allrpg.info/cp/index.php?kind=base\">Базе знаний</a>». Можно также указывать несколько фамилий или ИНП через запятую.",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem2($obj_14);

$obj_20=createElem(Array(
			'name'	=>	"user_id",
			'sname'	=>	"ИНП пользователя, разместившего статью",
			'type'	=>	"number",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem2($obj_20);

$obj_15=createElem(Array(
			'name'	=>	"active",
			'sname'	=>	"Показывать статью",
			'type'	=>	"checkbox",
			'default'	=>	1,
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem2($obj_15);

$obj_16=createElem(Array(
			'name'	=>	"content",
			'sname'	=>	"Содержимое",
			'type'	=>	"wysiwyg",
			'height'	=>	400,
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem2($obj_16);

$obj_17=createElem(Array(
			'name'	=>	"nocomments",
			'sname'	=>	"Отключить комментарии к статье",
			'type'	=>	"checkbox",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem2($obj_17);

$obj_18=createElem(Array(
			'name'	=>	"tags",
			'sname'	=>	"Теги",
			'type'	=>	"multiselect",
			'values'	=>	make5field($prefix."tags order by code asc, name asc","id","name"),
			'read'	=>	10,
			'write'	=>	100,
			'cols'	=>	3,
		)
);
$obj->setElem2($obj_18);

$obj_19=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Последнее изменение",
			'type'	=>	"timestamp",
			'read'	=>	100,
			'write'	=>	100,
			'mustbe'	=>	true,
			'show'	=>	true,
		)
);
$obj->setElem2($obj_19);

// Исполнение dynamicaction, если необходимо
if($action=="dynamicaction")
{
	require_once($server_inner_path.$direct."/dynamicaction.php");
	if($object=="articles")
	{
		dynamicaction($obj);
	}
}

// Исполнение дополнительных действий после dynamicaction, если необходимо
if(!$trouble && count($trouble2)==0)
{

}

// Добавление параметра values к select'ам и multiselect'ам.
if($id!=0)
{
	$obj_1->setValues(make5fieldtree(true,$prefix."articles","parent",0," AND content='{menu}' AND id!=".$id,"code asc",1,"id","name",1000000));
}
else
{
	$obj_1->setValues(make5fieldtree(true,$prefix."articles","parent",0," AND content='{menu}'","code asc",1,"id","name",1000000));
}
$obj_10->setValues(make5fieldtree(false,$prefix."articles","parent",0," AND content='{menu}'","code asc",1,"id","name",1000000));
$obj_18->setValues(make5field($prefix."tags order by code asc, name asc","id","name"));

// Инициализация элементов поиска, если нужен.
$obj->setSearch($obj_3);
$obj->setSearch2($obj_12);
$obj->setSearch2($obj_16);
$obj->setSearch2($obj_18);

// Отрисовка всего объекта html'ем в переменную
$obj_html.=$obj->draw();

// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
$content2.='<h1>СТАТЬИ</h1>'.$obj_html;
?>