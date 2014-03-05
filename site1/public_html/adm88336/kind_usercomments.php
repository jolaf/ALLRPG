<?php

// Создание объекта
$obj=new netObj(
	'usercomments',
	$prefix."comments",
	"отзыв",
	Array("Отзыв успешно добавлен.","Отзыв успешно изменен.","Отзыв успешно удален."),
	Array(
		'0'=>Array(
			Array("date", "DESC", true, true),
			Array("user_id", "ASC", true, true),
			Array("whom", "ASC", true, true),
			Array("game", "ASC", true, true, Array(3,$prefix."allgames","id","name")),
		)
	),
	2,
	645,
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
			'name'	=>	"user_id",
			'sname'	=>	"ID автора",
			'type'	=>	"number",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_1);

$obj_2=createElem(Array(
			'name'	=>	"user_id",
			'sname'	=>	"Информация по автору",
			'type'	=>	"select",
			'read'	=>	10,
			'write'	=>	100000,
		)
);
$obj->setElem($obj_2);

$obj_3=createElem(Array(
			'name'	=>	"whom",
			'sname'	=>	"ID получателя",
			'type'	=>	"number",
			'help'	=>	"введите ID (не ИНП!) получателя <u>ИЛИ</u> выберите игру ниже.",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_3);

$obj_4=createElem(Array(
			'name'	=>	"whom",
			'sname'	=>	"Информация по получателю",
			'type'	=>	"select",
			'read'	=>	10,
			'write'	=>	100000,
		)
);
$obj->setElem($obj_4);

$obj_5=createElem(Array(
			'name'	=>	"game",
			'sname'	=>	"Игра",
			'type'	=>	"select",
			'values'	=>	make5field($prefix."allgames where parent=0 order by name asc","id","name"),
			'help'	=>	"выберите игру <u>ИЛИ</u> введите ID (не ИНП!) получателя выше.",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_5);

$obj_6=createElem(Array(
			'name'	=>	"content",
			'sname'	=>	"Содержание",
			'type'	=>	"textarea",
			'rows'	=>	6,
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_6);

$obj_7=createElem(Array(
			'name'	=>	"rating",
			'sname'	=>	"Рейтинг",
			'type'	=>	"select",
			'values'	=>	Array(Array('-1','-1'),Array('1','+1')),
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_7);

$obj_8=createElem(Array(
			'name'	=>	"active",
			'sname'	=>	"Не скрывать комментарий",
			'default'	=>	1,
			'type'	=>	"checkbox",
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_8);

$obj_9=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Последнее изменение",
			'type'	=>	"timestamp",
			'read'	=>	100,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_9);

// Исполнение dynamicaction, если необходимо
if($action=="dynamicaction")
{
	require_once($server_inner_path.$direct."/dynamicaction.php");
	if($object=="usercomments")
	{
		dynamicaction($obj);
	}
}

// Исполнение дополнительных действий после dynamicaction, если необходимо
if(!$trouble && count($trouble2)==0)
{

}

// Добавление параметра values к select'ам и multiselect'ам.
$obj_2->setValues(make5field($prefix."users ORDER by fio asc","id",Array("<br>Ф.И.О.: ", "fio", "<br>Никнейм: ", "nick", "<br>ИНП: ", "sid")));
$obj_4->setValues(make5field($prefix."users ORDER by fio asc","id",Array("<br>Ф.И.О.: ", "fio", "<br>Никнейм: ", "nick", "<br>ИНП: ", "sid")));

// Инициализация элементов поиска, если нужен.
/*$obj->setSearch($obj_1);
$obj->setSearch($obj_5);
$obj->setSearch($obj_3);
$obj->setSearch($obj_6);
$obj->setSearch($obj_7);
$obj->setSearch($obj_8);*/

// Отрисовка всего объекта html'ем в переменную
$obj_html.=$obj->draw();

// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
$content2.='<h1>ОТЗЫВЫ</h1>'.$obj_html;
?>