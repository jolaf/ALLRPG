<?php
if($_SESSION["user_id"]!='' && $workrights["site"]["settings"]) {
	// общие установки
	$act="view";
	$id=$_SESSION["siteid"];
	$stayhere=true;

	// Создание объекта
	$obj=new netObj(
		'settings',
		$prefix."sites",
		"установки",
		Array("","Установки успешно изменены.",""),
		Array(),
		2,
		'100%',
		50
	);

	// Создание схемы прав объекта
	if($_SESSION["siteid"]!='') {
		$obj_r=new netRight(
			true,
			false,
			true,
			false,
			100,
			'id='.$id,
			'id='.$id,
			'id='.$id
		);
		$obj->setRight($obj_r);
	}

	$result=mysql_query("SELECT * FROM ".$prefix."sites WHERE id=".$id);
	$a = mysql_fetch_array($result);
	$usetemp=$a["usetemp"];

	if($usetemp==1) {
		err_info('Если Вам потребуется подключить систему заявок к Вашему сайту, обратитесь, пожалуйста, к администрации.');
	}
	elseif($usetemp==2) {
		err_info('Если Вам потребуется сайт к Вашей системе заявок, обратитесь, пожалуйста, к администрации.');
	}

	// Создание полей объекта
	$obj_21=createElem(Array(
			'name'	=>	"id",
			'sname'	=>	"ID проекта",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	100000,
		)
	);
	$obj->setElem($obj_21);

	$obj_6=createElem(Array(
			'name'	=>	"title",
			'sname'	=>	"Название проекта",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_6);

	if($usetemp!=2) {
		$obj_3=createElem(Array(
				'name'	=>	"path",
				'sname'	=>	"Субдомен",
				'type'	=>	"text",
				'read'	=>	100,
				'write'	=>	100000,
			)
		);
		$obj->setElem($obj_3);
	}
	else {
		$obj_3=createElem(Array(
				'name'	=>	"path2",
				'sname'	=>	"Внешний сайт Вашей системы заявок",
				'type'	=>	"text",
				'read'	=>	10,
				'write'	=>	100,
			)
		);
		$obj->setElem($obj_3);
	}

	$obj_14=createElem(Array(
			'name'	=>	"status",
			'sname'	=>	"Статус проекта",
			'type'	=>	"select",
			'values'	=>	Array(Array('1','не активен'),Array('2','работает'),Array('3','закрыт')),
			'help'	=>	'в статусе «не активен» на сайте проекта вместо информации показывается стандартная текстовая заглушка. Изменив статус на «работает», Вы делаете сайт активным. Поставив статус «закрыт», Вы уберете упоминания о проекте из Ваших рабочих панелей (таких как «Заявки на мои проекты»).',
			'default'	=>	'1',
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true,
		)
	);
	$obj->setElem($obj_14);

	if($usetemp!=2 && $indexexists) {
		$obj_11=createElem(Array(
				'name'	=>	"defcode",
				'sname'	=>	"Раздел по умолчанию",
				'type'	=>	"select",
				'values'	=>	make5fieldtree(false,$prefix."pages","parent",0," AND content='{menu}' AND site_id=".$_SESSION["siteid"],"code asc",1,"id","name",3),
				'help'	=>	"выберите раздел, который станет первой страницей Вашего проекта.",
				'read'	=>	10,
				'write'	=>	100,
			)
		);
		$obj->setElem($obj_11);

		$obj_12=createElem(Array(
				'name'	=>	"newscode",
				'sname'	=>	"Раздел новостей",
				'type'	=>	"select",
				'values'	=>	make5fieldtree(false,$prefix."pages","parent",0," AND content='{menu}' AND site_id=".$_SESSION["siteid"],"code asc",1,"id","name",3),
				'help'	=>	"выберите раздел, в котором будет выводиться новостная лента Вашего проекта.",
				'read'	=>	10,
				'write'	=>	100,
			)
		);
		$obj->setElem($obj_12);
    }

    if($usetemp==0 && $indexexists) {
		$obj_13=createElem(Array(
				'name'	=>	"rolescode",
				'sname'	=>	"Раздел сетки ролей и списка поданных заявок",
				'type'	=>	"select",
				'values'	=>	make5fieldtree(false,$prefix."pages","parent",0," AND content='{menu}' AND site_id=".$_SESSION["siteid"],"code asc",1,"id","name",3),
				'help'	=>	'выберите раздел, в котором будет выводиться сетка ролей Вашего проекта и список всех поданных на Ваш проект заявок. Функции скрытия локаций можно настроить в разделе «<a href="'.$server_absolute_path_site.'locations/">Дерево локаций / команд</a>». Вывод отдельных полей можно настроить в разделе «<a href="'.$server_absolute_path_site.'rolessetup/">Настроить поля заявки</a>».',
				'read'	=>	10,
				'write'	=>	100,
			)
		);
		$obj->setElem($obj_13);
	}

	if($usetemp!=1) {
		$obj_20=createElem(Array(
				'name'	=>	"showonlyacceptedroles",
				'sname'	=>	"Показывать в списке заявок / сетке ролей только принятые заявки",
				'help'	=>	'выводить в списке заявок / сетке ролей на сайте (если есть) и на <a href="'.$server_absolute_path.'siteroles/'.$_SESSION["siteid"].'/">allrpg.info</a> информацию только о принятых заявках (остальные скрывать).',
				'type'	=>	"checkbox",
				'read'	=>	10,
				'write'	=>	100,
			)
		);
		$obj->setElem($obj_20);

		$obj_22=createElem(Array(
					'name'	=>	"oneorderfromplayer",
					'sname'	=>	"Пользователь не может подать более одной заявки на проект",
					'type'	=>	"checkbox",
					'default'	=>	0,
					'read'	=>	10,
					'write'	=>	100,
				)
		);
		$obj->setElem($obj_22);
	}

	if($usetemp!=2 && $indexexists) {
		$obj_16=createElem(Array(
				'name'	=>	"commentson",
				'sname'	=>	"Включить систему комментирования страниц пользователями",
				'type'	=>	"checkbox",
				'read'	=>	10,
				'write'	=>	100,
			)
		);
		$obj->setElem($obj_16);
	}

	if($usetemp!=1) {
		$obj_15=createElem(Array(
				'name'	=>	"status2",
				'sname'	=>	"Подача заявок",
				'type'	=>	"select",
				'values'	=>	Array(Array('1','закрыта'),Array('2','открыта')),
				'help'	=>	'при статусе «закрыта» пользователи не могут подавать заявки на Ваш проект. При статусе «открыта» пользователи могут подавать заявки на Ваш проект через пункт «<a href="'.$server_absolute_path.'order/">Мои заявки</a>» или через «Подать заявку» на заглавной странице портала.',
				'default'	=>	'1',
				'read'	=>	10,
				'write'	=>	100,
				'mustbe'	=>	true,
			)
		);
		$obj->setElem($obj_15);
	}

	if($usetemp!=1) {
		$obj_8=createElem(Array(
				'name'	=>	"sorter",
				'sname'	=>	"Сортировка индивидуальных заявок",
				'type'	=>	"select",
				'help'	=>	"по данному полю (<u>только формата «Строка для текста»</u>) будут сортироваться все индивидуальные заявки. Будьте осторожны: не помещайте в поле «Сортировка» значение тех полей, которые Вы хотите скрыть от других игроков, если на Вашем сайте работает автоматическое выведение списка заявок.",
				'values'	=>	make5field($prefix."rolefields where site_id=".$_SESSION["siteid"]." and roletype='text' and team='0' ORDER by id asc","id","rolename"),
				'read'	=>	10,
				'write'	=>	100,
				'mustbe'	=>	true
			)
		);
		$obj->setElem($obj_8);

		$obj_9=createElem(Array(
				'name'	=>	"sorter2",
				'sname'	=>	"Сортировка командных заявок",
				'type'	=>	"select",
				'help'	=>	"по данному полю (<u>только формата «Строка для текста»</u>) будут сортироваться все командные заявки. Будьте осторожны: не помещайте в поле «Сортировка» значение тех полей, которые Вы хотите скрыть от других игроков, если на Вашем сайте работает автоматическое выведение списка заявок.",
				'values'	=>	make5field($prefix."rolefields where site_id=".$_SESSION["siteid"]." and roletype='text' and team='1' ORDER by id asc","id","rolename"),
				'read'	=>	10,
				'write'	=>	100,
			)
		);
		$obj->setElem($obj_9);

		$obj_10=createElem(Array(
				'name'	=>	"money",
				'sname'	=>	"Взнос",
				'type'	=>	"text",
				'help'	=>	"данный взнос будет автоматически устанавливаться всем новым заявкам. Кроме того, при изменении взноса он автоматически изменится во всех заявках, где был указан старый взнос и при этом не стоит галочка «Взнос сдан».",
				'read'	=>	10,
				'write'	=>	100,
				'mustbe'	=>	true
			)
		);
		$obj->setElem($obj_10);
	}

	$obj_17=createElem(Array(
			'name'	=>	"datestart",
			'sname'	=>	"Дата начала",
			'type'	=>	"calendar",
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_17);

	$obj_18=createElem(Array(
			'name'	=>	"datefinish",
			'sname'	=>	"Дата окончания",
			'type'	=>	"calendar",
			'help'	=>	"после этой даты проект автоматически исчезнет из списков «Открыт прием заявок», однако, по прямой ссылке прием заявок всё так же будет работать.",
			'read'	=>	10,
			'write'	=>	100,
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

	if($usetemp!=2 && $indexexists) {
		/*$obj_4=createElem(Array(
				'name'	=>	"descr",
				'sname'	=>	"META Description субдомена",
				'type'	=>	"text",
				'help'	=>	"не более 255 символов.",
				'read'	=>	10,
				'write'	=>	100,
			)
		);
		$obj->setElem($obj_4);

		$obj_5=createElem(Array(
				'name'	=>	"keywords",
				'sname'	=>	"META Keywords субдомена",
				'type'	=>	"text",
				'help'	=>	"не более 255 символов.",
				'read'	=>	10,
				'write'	=>	100,
			)
		);
		$obj->setElem($obj_5);*/
	}

	// Исполнение dynamicaction, если необходимо
	if($action=="dynamicaction")
	{
		require_once($server_inner_path.$direct."/dynamicaction.php");
		if($object=="settings")
		{
			if($actiontype=="change") {
				$result=mysql_query("SELECT * from ".$prefix."sites where id=".$id);
				$a=mysql_fetch_array($result);
				if($a["money"]!=encode_to_cp1251($_REQUEST["money"])) {
					$result2=mysql_query("SELECT * from ".$prefix."roles where site_id=".$id." and moneydone='0' and money='".$a["money"]."'");
					while($b=mysql_fetch_array($result2)) {
						mysql_query("UPDATE ".$prefix."roles SET money='".encode_to_cp1251($_REQUEST["money"])."' where id=".$b["id"]);
					}
					err("Взнос изменен в заявках, в которых не проставлена галочка «Взнос сдан».");
				}
			}
			dynamicaction($obj);
		}
	}

	// Исполнение дополнительных действий после dynamicaction, если необходимо
	if(!$trouble && count($trouble2)==0)
	{
		if($object=="settings")
		{
			if($actiontype=="change") {
			}
		}
	}

	// Добавление параметра values к select'ам и multiselect'ам.

	// Инициализация элементов поиска, если нужен.

	// Отрисовка всего объекта html'ем в переменную
	$obj_html.=$obj->draw();

	// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
	$pagetitle=h1line('Основные свойства');
	$content2.='<div class="narrow">'.$obj_html.'</div>';
}
?>