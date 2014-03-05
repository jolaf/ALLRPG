<?php

// Создание объекта
$obj=new netObj(
	'files',
	$prefix."files",
	"файл",
	Array("Файл успешно загружен.","Файлы успешно изменены","Файл успешно удален."),
	Array(
		'0'=>Array(
			Array("date", "DESC", false, true),
			Array("name", "ASC", true, true),
		)
	),
	1,
	700,
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
			'name'	=>	"name",
			'sname'	=>	"Описание",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_1);

$obj_2=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Последнее изменение",
			'type'	=>	"timestamp",
			'read'	=>	100,
			'write'	=>	100,
			'mustbe'	=>	true,
			'show'	=>	true,
		)
);
$obj->setElem($obj_2);

$obj_3=createElem(Array(
			'name'	=>	"im",
			'sname'	=>	"Файл",
			'type'	=>	"file",
			'help'	=>	"чтобы сгрузить файл, щелкните правой кнопкой мыши на ссылке «ПОСМОТРЕТЬ» требуемого вам файла и выберите «Сохранить как...»",
			'upload'	=>	3,
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_3);

// Исполнение dynamicaction, если необходимо
if($action=="dynamicaction")
{
	require_once($server_inner_path.$direct."/dynamicaction.php");
	if($object=="files")
	{
		dynamicaction($obj);
	}
}

// Исполнение дополнительных действий после dynamicaction, если необходимо
if(!$trouble && count($trouble2)==0)
{

}

// Добавление параметра values к select'ам и multiselect'ам.

// Инициализация элементов поиска, если нужен.
//$obj->setSearch($obj_1);

// Отрисовка всего объекта html'ем в переменную
$obj_html.=$obj->draw();

// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
$content2.='<h1>ФАЙЛЫ НА САЙТЕ</h1>'.$obj_html;
?>