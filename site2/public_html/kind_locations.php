<?php
if($_SESSION["user_id"]!='' && $workrights["site"]["locations"]) {
	// локации

	// Создание объекта
	$obj=new netObj2(
		'locations',
		$prefix."roleslocat",
		"локацию / команду",
		Array("Локация / команда успешно добавлена.","Локация / команда успешно изменена.","Локация / команда успешно удалена.","Локация / команда успешно удалена."),
		"",
		Array("","",""),
		Array(
			'0'	=>	Array(
				Array("parent", "ASC", false, false, Array(3,$prefix."roleslocat","id","name")),
				Array("code", "ASC", false, false),
				Array("name", "ASC", true, false),
				Array("code", "ASC", true, true),
			),
			'1'	=>	Array(
				Array("name", "ASC", true, true),
			),
		),
		3,
		'100%',
		50,
		'parent',
		'content',
		'code asc, name',
		'name'
	);

	// Создание схемы прав объекта
	if($_SESSION["siteid"]!='') {
		$obj_r=new netRight(
			true,
			true,
			true,
			true,
			100,
			'site_id='.$_SESSION["siteid"],
			'site_id='.$_SESSION["siteid"],
			'site_id='.$_SESSION["siteid"]
		);
		$obj->setRight($obj_r);
	}

	// Создание полей объекта
	$obj_5=createElem(Array(
			'name'	=>	"parent",
			'sname'	=>	"Является частью локации",
			'type'	=>	"select",
			'help'	=>	"Вы можете выстроить произвольное дерево локаций/микролокаций/команд.",
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_5);

	$obj_1=createElem(Array(
			'name'	=>	"name",
			'sname'	=>	"Название",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true,
		)
	);
	$obj->setElem($obj_1);

	$obj_2=createElem(Array(
			'name'	=>	"rights",
			'sname'	=>	"Видимость в сетке ролей",
			'type'	=>	"select",
			'values'	=>	Array(Array('0','показывать заявки этой локации в сетке ролей'),Array('1','показывать исключительно количество заявок локации в сетке ролей')),
			'default'	=>	0,
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true,
		)
	);
	$obj->setElem($obj_2);

	$obj_8=createElem(Array(
			'name'	=>	"description",
			'sname'	=>	"Описание",
			'help'	=>	"для сетки ролей",
			'type'	=>	"textarea",
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_8);

	$obj_6=createElem(Array(
			'name'	=>	"code",
			'sname'	=>	"Очередность в сетке ролей",
			'type'	=>	"number",
			'help'	=>	"опциональное поле. Чем меньше здесь цифра, тем выше в сетке ролей будет выводиться локация.",
			'default'	=>	100,
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_6);
	
	$obj -> setElem ( createElem( array(
    'name'    => 'url',
    'sname'   => 'Ссылка на сетку ролей',
    'type'    => 'text',
    'help'    => '',
    'default' => 'http://www.allrpg.info/gameorders.php?game=592&locat=4444',
    'read'    => 10,
    'write'   => 100000
	)));

	$obj_3=createElem(Array(
			'name'	=>	"site_id",
			'type'	=>	"hidden",
			'default'	=>	$_SESSION["siteid"],
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_3);

	$obj_7=createElem(Array(
			'name'	=>	"content",
			'type'	=>	"hidden",
			'default'	=>	"{menu}",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_7);

	$obj_4=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Последнее изменение",
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
		if($object=="locations")
		{
			if($actiontype=="add") {
				function dynamic_add_success() {
					global
						$prefix,
						$_SESSION,
						$id;

					mysql_query("UPDATE ".$prefix."roleslocat SET site_id=".$_SESSION["siteid"]." WHERE id=".$id);
				}
			}
			dynamicaction($obj);
		}
	}

	// Добавление параметра values к select'ам и multiselect'ам.
	if($id=='') {
		$obj_5->setValues(make5fieldtree(false,$prefix."roleslocat","parent",0," AND site_id=".$_SESSION["siteid"],"code asc, name asc",0,"id","name",1000000));
	}
	else {
		$obj_5->setValues(make5fieldtree(false,$prefix."roleslocat","parent",0," AND id!=".$id." AND site_id=".$_SESSION["siteid"],"code asc, name asc",0,"id","name",1000000));
	}

	// Инициализация элементов поиска, если нужен.

	// Отрисовка всего объекта html'ем в переменную
	$obj_html.=$obj->draw();

	// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
	$pagetitle=h1line('Дерево локаций / команд',$curdir.$kind.'/');
	$content2.='<div class="narrow">'.$obj_html.'</div>';
	$content2=preg_replace('#<a [^>]+>\[\+\] добавить <\/a>#','',$content2);
}
?>