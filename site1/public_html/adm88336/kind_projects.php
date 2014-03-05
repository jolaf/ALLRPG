<?php

$allsiteslist=encode($_GET["list"]);
if($allsiteslist==1) {
	$content2.='<h1>УПРАВЛЕНИЕ ПРОЕКТАМИ</h1>
<center><b><a href="'.$curdir.$kind.'/">вернуться к карточкам проектов</a></b></center><br />
<table cellpadding="0" cellspacing="0" border="0" width=1200 align=center>
<tr valign="bottom">
<td>
<table class="menutable"><tr><td class="menu">ИНС</td><td class="menu">Название проекта</td><td class="menu">Тип</td><td class="menu">Статус субдомена</td><td class="menu">Статус системы заявок</td><td class="menu">Сайт</td><td class="menu">Дата начала</td><td class="menu">Дата окончания</td><td class="menu">Заявку на проект подал(-а)</td></tr>';
	$result=mysql_query("SELECT * FROM ".$prefix."sites where testing!='1' order by sid desc");
	while($a=mysql_fetch_array($result)) {
		$content2.='<tr><td>'.decode($a["sid"]).'</td><td>'.decode($a["title"]).'</td><td>';
		if($a["usetemp"]==0) {
			$content2.='конструктор';
		}
		elseif($a["usetemp"]==1) {
			$content2.='только сайт';
		}
		elseif($a["usetemp"]==2) {
			$content2.='только система заявок';
		}
		$content2.='</td><td>';
		if($a["status"]==1) {
			$content2.='не активирован';
		}
		elseif($a["status"]==2) {
			$content2.='работает';
		}
		elseif($a["status"]==3) {
			$content2.='закрыт';
		}
		$content2.='</td><td>';
		if($a["status2"]==1) {
			$content2.='закрыта';
		}
		elseif($a["status2"]==2) {
			$content2.='открыта';
		}
		$content2.='</td><td>';
		if($a["path"]!='' && ($a["usetemp"]==0 || $a["usetemp"]==1)) {
			$content2.='<a href="http://'.decode($a["path"]).'.allrpg.info" target="_blank">http://'.decode($a["path"]).'.allrpg.info</a>';
		}
		if($a["path2"]!='' && ($a["usetemp"]==0 || $a["usetemp"]==1)) {
			$content2.='<br><a href="'.decode($a["path2"]).'" target="_blank">'.decode($a["path2"]).'</a>';
		}
		$content2.='</td><td>'.decode($a["datestart"]).'</td><td>'.decode($a["datefinish"]).'</td><td>';

		$result2=mysql_query("SELECT * FROM ".$prefix."orders where sid=".$a["sio"]);
		$b=mysql_fetch_array($result2);
		$result3=mysql_query("SELECT * FROM ".$prefix."users where id=".$b["author"]);
		$c=mysql_fetch_array($result3);

		$content2.=usname($c,true,true);
		if($c["icq"]!='') {
			$content2.='<br>ICQ: '.decode($c["icq"]);
		}
		if($c["em"]!='') {
			$content2.='<br>E-mail: <a href="mailto:'.decode($c["em"]).'">'.decode($c["em"]).'</a>';
		}
		if($c["phone"]!='') {
			$content2.='<br>Дом.тел: '.decode($c["phone"]);
		}
		if($c["phone2"]!='') {
			$content2.='<br>Конт.тел: '.decode($c["phone2"]);
		}

		$content2.='</td></tr>';
	}
	$content2.='</table>
</td>
</tr>
</table>';
}
else {
	// Создание объекта
	$obj=new netObj(
		'projects',
		$prefix."sites",
		"проект",
		Array("Проект успешно добавлен.","Проект успешно изменен.","Проект успешно удален."),
		Array(
			'0'=>Array(
				Array("usetemp", "ASC", true, true, Array(2,Array(Array('0','хостинг+система заявок'),Array('1','только сайт'),Array('2','только система заявок')))),
				Array("status", "ASC", true, true, Array(2,Array(Array('1','не активирован'),Array('2','работает'),Array('3','закрыт')))),
				Array("status2", "ASC", true, true, Array(2,Array(Array('1','закрыта'),Array('2','открыта')))),
				Array("title", "ASC", true, true),
				Array("sid", "ASC", true, true),
				Array("datestart", "ASC", true, true),
				Array("datefinish", "ASC", true, true),
			)
		),
		2,
		800,
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
				'sname'	=>	"ИНС",
				'help'	=>	"идентификационный номер сайта",
				'type'	=>	"text",
				'read'	=>	10,
				'write'	=>	100000,
			)
	);
	$obj->setElem($obj_1);

	$obj_2=createElem(Array(
				'name'	=>	"sio",
				'sname'	=>	"ИНЗ",
				'help'	=>	"идентификационный номер заявки, на основе которой был создан субдомен.",
				'type'	=>	"text",
				'read'	=>	10,
				'write'	=>	100000,
			)
	);
	$obj->setElem($obj_2);

	$obj_3=createElem(Array(
				'name'	=>	"path",
				'sname'	=>	"Субдомен",
				'type'	=>	"text",
				'read'	=>	10,
				'write'	=>	10,
			)
	);
	$obj->setElem($obj_3);

	$obj_4=createElem(Array(
				'name'	=>	"path2",
				'sname'	=>	"Внешний сайт",
				'type'	=>	"text",
				'read'	=>	10,
				'write'	=>	10,
			)
	);
	$obj->setElem($obj_4);

	$obj_17=createElem(Array(
				'name'	=>	"title",
				'sname'	=>	"Название проекта",
				'type'	=>	"text",
				'read'	=>	10,
				'write'	=>	10,
				'mustbe'	=>	true
			)
	);
	$obj->setElem($obj_17);

	$obj_18=createElem(Array(
				'name'	=>	"usetemp",
				'sname'	=>	"Схема проекта",
				'type'	=>	"select",
				'values'	=>	Array(Array('0','хостинг+система заявок'),Array('1','только сайт'),Array('2','только система заявок')),
				'read'	=>	10,
				'write'	=>	10,
				'mustbe'	=>	true
			)
	);
	$obj->setElem($obj_18);

	/*$obj_19=createElem(Array(
				'name'	=>	"rolesubs",
				'sname'	=>	"Подпись мастеров",
				'type'	=>	"wysiwyg",
				'height'	=>	200,
				'help'	=>	"данный текст будет крепиться в качестве подписи ко всем автоматическим письмам, генерируемым мастерами в системе управления заявками.",
				'default'	=>	"С уважением,
		мастерская группа игры...",
				'read'	=>	10,
				'write'	=>	10,
				'mustbe'	=>	true
			)
	);
	$obj->setElem($obj_19);*/

	$obj_20=createElem(Array(
				'name'	=>	"sorter",
				'sname'	=>	"«Сортировка» по умолчанию",
				'type'	=>	"select",
				'values'	=>	make5field($prefix."rolefields where site_id=".$id." and roletype='text' ORDER by id asc","id","rolename"),
				'read'	=>	10,
				'write'	=>	10,
				'mustbe'	=>	true
			)
	);
	$obj->setElem($obj_20);

	$obj_32=createElem(Array(
				'name'	=>	"sorter2",
				'sname'	=>	"«Сортировка» по умолчанию (для командных заявок)",
				'type'	=>	"select",
				'values'	=>	make5field($prefix."rolefields where site_id=".$id." and roletype='text' and team='1' ORDER by id asc","id","rolename"),
				'read'	=>	10,
				'write'	=>	100,
			)
	);
	$obj->setElem($obj_32);

	$obj_21=createElem(Array(
				'name'	=>	"money",
				'sname'	=>	"Взнос",
				'type'	=>	"text",
				'read'	=>	10,
				'write'	=>	10,
				'mustbe'	=>	true
			)
	);
	$obj->setElem($obj_21);

	$obj_22=createElem(Array(
				'name'	=>	"status",
				'sname'	=>	"Статус проекта",
				'type'	=>	"select",
				'values'	=>	Array(Array('1','не активирован'),Array('2','работает'),Array('3','закрыт')),
				'default'	=>	'1',
				'read'	=>	10,
				'write'	=>	10,
				'mustbe'	=>	true,
			)
	);
	$obj->setElem($obj_22);

	$obj_23=createElem(Array(
				'name'	=>	"status2",
				'sname'	=>	"Подача заявок",
				'type'	=>	"select",
				'values'	=>	Array(Array('1','закрыта'),Array('2','открыта')),
				'default'	=>	'1',
				'read'	=>	10,
				'write'	=>	10,
				'mustbe'	=>	true,
			)
	);
	$obj->setElem($obj_23);

	$obj_24=createElem(Array(
				'name'	=>	"allspace",
				'sname'	=>	"Место, выделенное проекту",
				'type'	=>	"number",
				'help'	=>	"в байтах. 5242880 байт = 5 мегабайт (5*1024*1024 байт).",
				'read'	=>	10,
				'write'	=>	10,
				'mustbe'	=>	true
			)
	);
	$obj->setElem($obj_24);

	$obj_25=createElem(Array(
				'name'	=>	"defcode",
				'sname'	=>	"Раздел по умолчанию",
				'type'	=>	"select",
				'values'	=>	make5fieldtree(false,$prefix."pages","parent",0," AND content='{menu}' AND site_id=".$id,"code asc",1,"id","name",3),
				'read'	=>	10,
				'write'	=>	10,
			)
	);
	$obj->setElem($obj_25);

	$obj_26=createElem(Array(
				'name'	=>	"newscode",
				'sname'	=>	"Раздел новостей",
				'type'	=>	"select",
				'values'	=>	make5fieldtree(false,$prefix."pages","parent",0," AND content='{menu}' AND site_id=".$id,"code asc",1,"id","name",3),
				'read'	=>	10,
				'write'	=>	10,
			)
	);
	$obj->setElem($obj_26);

	$obj_27=createElem(Array(
				'name'	=>	"rolescode",
				'sname'	=>	"Раздел сетки ролей и списка поданных заявок",
				'type'	=>	"select",
				'values'	=>	make5fieldtree(false,$prefix."pages","parent",0," AND content='{menu}' AND site_id=".$id,"code asc",1,"id","name",3),
				'read'	=>	10,
				'write'	=>	10,
			)
	);
	$obj->setElem($obj_27);

	$obj_28=createElem(Array(
				'name'	=>	"datestart",
				'sname'	=>	"Дата начала",
				'type'	=>	"calendar",
				'read'	=>	10,
				'write'	=>	100,
			)
	);
	$obj->setElem($obj_28);

	$obj_29=createElem(Array(
				'name'	=>	"datefinish",
				'sname'	=>	"Дата окончания",
				'type'	=>	"calendar",
				'read'	=>	10,
				'write'	=>	100,
			)
	);
	$obj->setElem($obj_29);

	$obj_30=createElem(Array(
				'name'	=>	"testing",
				'sname'	=>	"Проект является тестовым",
				'type'	=>	"checkbox",
				'help'	=>	"не показывать нигде на allrpg.info информацию о данном проекте, не давать подавать на него заявки.",
				'default'	=>	0,
				'read'	=>	10,
				'write'	=>	100,
			)
	);
	$obj->setElem($obj_30);

	$obj_5=createElem(Array(
				'name'	=>	"htmlcode",
				'sname'	=>	"HTML-код шаблона",
				'type'	=>	"textarea",
				'rows'	=>	30,
				'read'	=>	10,
				'write'	=>	10,
			)
	);
	$obj->setElem($obj_5);

	$obj_6=createElem(Array(
				'name'	=>	"css",
				'sname'	=>	"Переменные шаблона",
				'type'	=>	"textarea",
				'rows'	=>	30,
				'read'	=>	10,
				'write'	=>	100,
			)
	);
	$obj->setElem($obj_6);

	$obj_7=createElem(Array(
				'name'	=>	"usercss",
				'sname'	=>	"CSS",
				'type'	=>	"textarea",
				'rows'	=>	10,
				'read'	=>	10,
				'write'	=>	100,
			)
	);
	$obj->setElem($obj_7);

	$obj_8=createElem(Array(
				'name'	=>	"menualign",
				'sname'	=>	"Расположение меню",
				'type'	=>	"select",
				'values'	=>	Array(Array('1','вертикально'),Array('2','горизонтально')),
				'default'	=>	'1',
				'read'	=>	10,
				'write'	=>	10,
				'mustbe'	=>	true
			)
	);
	$obj->setElem($obj_8);

	$obj_9=createElem(Array(
				'name'	=>	"separkind",
				'sname'	=>	"Разделитель для пунктов меню",
				'type'	=>	"text",
				'default'	=>	" | ",
				'read'	=>	10,
				'write'	=>	100,
			)
	);
	$obj->setElem($obj_9);

	$obj_10=createElem(Array(
				'name'	=>	"submenualign",
				'sname'	=>	"Расположение подменю",
				'type'	=>	"select",
				'values'	=>	Array(Array('1','вертикально'),Array('2','горизонтально')),
				'default'	=>	'1',
				'read'	=>	10,
				'write'	=>	10,
			)
	);
	$obj->setElem($obj_10);

	$obj_11=createElem(Array(
				'name'	=>	"separsub",
				'sname'	=>	"Разделитель для пунктов подменю",
				'type'	=>	"text",
				'default'	=>	" | ",
				'read'	=>	10,
				'write'	=>	100,
			)
	);
	$obj->setElem($obj_11);

	$obj_12=createElem(Array(
				'name'	=>	"newsformat1",
				'sname'	=>	"Конструктор внешнего вида новостной ленты",
				'type'	=>	"textarea",
				'rows'	=>	10,
				'read'	=>	10,
				'write'	=>	100,
				'mustbe'	=>	true,
			)
	);
	$obj->setElem($obj_12);

	$obj_13=createElem(Array(
				'name'	=>	"newsformat2",
				'sname'	=>	"Конструктор внешнего вида новости",
				'type'	=>	"textarea",
				'rows'	=>	10,
				'read'	=>	10,
				'write'	=>	100,
				'mustbe'	=>	true,
			)
	);
	$obj->setElem($obj_13);

	$obj_14=createElem(Array(
				'name'	=>	"separ",
				'sname'	=>	"Разделитель для «путеводителя»",
				'type'	=>	"text",
				'default'	=>	" &#150;&#187; ",
				'read'	=>	10,
				'write'	=>	100,
				'mustbe'	=>	true,
			)
	);
	$obj->setElem($obj_14);

	/*$obj_15=createElem(Array(
				'name'	=>	"descr",
				'sname'	=>	"META Description",
				'type'	=>	"text",
				'maxchar'	=>	255,
				'read'	=>	10,
				'write'	=>	100,
			)
	);
	$obj->setElem($obj_15);

	$obj_16=createElem(Array(
				'name'	=>	"keywords",
				'sname'	=>	"META Keywords",
				'type'	=>	"text",
				'maxchar'	=>	255,
				'read'	=>	10,
				'write'	=>	100,
			)
	);
	$obj->setElem($obj_16);*/

	if($id!='') {
		$obj_33=createElem(Array(
					'name'	=>	"alter_rolefield",
					'sname'	=>	"Сортировать сетку заявок по полю",
					'type'	=>	"select",
					'values'	=>	make5field($prefix."rolefields where site_id=".$id." and (roletype='select' OR roletype='multiselect') ORDER by rolename asc","id","rolename"),
					'read'	=>	10,
					'write'	=>	10,
				)
		);
		$obj->setElem($obj_33);

		$obj_34=createElem(Array(
					'name'	=>	"alter_byname",
					'sname'	=>	"Переменные поля отсортировать по имени",
					'type'	=>	"checkbox",
					'read'	=>	10,
					'write'	=>	10,
				)
		);
		$obj->setElem($obj_34);

		$obj_35=createElem(Array(
					'name'	=>	"oneorderfromplayer",
					'sname'	=>	"Пользователь не может подать более одной заявки на проект",
					'type'	=>	"checkbox",
					'default'	=>	0,
					'read'	=>	10,
					'write'	=>	100,
				)
		);
		$obj->setElem($obj_35);

		$obj_36=createElem(Array(
					'name'	=>	"showonlyacceptedroles",
					'sname'	=>	"Показывать в списке заявок / сетке ролей только принятые заявки",
					'type'	=>	"checkbox",
					'default'	=>	0,
					'read'	=>	10,
					'write'	=>	100,
				)
		);
		$obj->setElem($obj_36);
	}

	$obj_31=createElem(Array(
				'name'	=>	"date",
				'sname'	=>	"Последнее изменение",
				'type'	=>	"timestamp",
				'read'	=>	100,
				'write'	=>	100,
				'mustbe'	=>	true
			)
	);
	$obj->setElem($obj_31);

	// Исполнение dynamicaction, если необходимо
	if($action=="dynamicaction")
	{
		require_once($server_inner_path.$direct."/dynamicaction.php");
		if($object=="projects")
		{
			dynamicaction($obj);
		}
	}

	// Исполнение дополнительных действий после dynamicaction, если необходимо
	if(!$trouble && count($trouble2)==0)
	{
		if($object=="projects")
		{
			if($actiontype=="add")
			{
				$sid=$id;
				mysql_query("UPDATE ".$prefix."sites SET sid='".$sid."' WHERE id=".$id);
			}

			if($actiontype=="change" && encode($_POST["status"])==3)
			{
				mysql_query("UPDATE ".$prefix."sites set status2=1 where id=".$id);
				mysql_query("DELETE from ".$prefix."roleshistory where role_id IN (SELECT id from ".$prefix."roles where site_id=".$id.")");
				err('История изменения заявок очищена. Проект закрыт.');
			}
			if($actiontype=="delete")
			{
				mysql_query("DELETE from table ".$prefix."roleshistory where role_id IN (SELECT id from ".$prefix."roles where site_id=".$id.")");
				mysql_query("DELETE from ".$prefix."rolescomments where role_id IN (SELECT id from ".$prefix."roles where site_id=".$id.")");
				mysql_query("DELETE from ".$prefix."rolescommentsread where role_id IN (SELECT id from ".$prefix."roles where site_id=".$id.")");
				mysql_query("DELETE from ".$prefix."allrights2 where site_id=".$id);
				mysql_query("DELETE from ".$prefix."pages where site_id=".$id);
				mysql_query("DELETE from ".$prefix."news where site_id=".$id);
				mysql_query("DELETE from ".$prefix."roles where site_id=".$id);
				mysql_query("DELETE from ".$prefix."roleslocat where site_id=".$id);
				mysql_query("DELETE from ".$prefix."rolefields where site_id=".$id);
				mysql_query("DELETE from ".$prefix."virtrights where site_id=".$id);
				err('История изменения заявок очищена. Доступы удалены. Заявки удалены. Страницы удалены. Новости удалены. Группы прав удалены.');
			}
		}
	}

	// Добавление параметра values к select'ам и multiselect'ам.


	// Инициализация элементов поиска, если нужен.
	$obj->setSearch($obj_3);
	$obj->setSearch($obj_17);
	$obj->setSearch($obj_18);
	$obj->setSearch($obj_22);
	$obj->setSearch($obj_23);
	$obj->setSearch($obj_28);
	$obj->setSearch($obj_29);

	// Отрисовка всего объекта html'ем в переменную
	$obj_html.=$obj->draw();

	// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
	$content2.='<h1>УПРАВЛЕНИЕ ПРОЕКТАМИ</h1><center><b><a href="'.$curdir.'projects/list=1">перейти к сводной таблице по проектам</a></b></center><br>'.$obj_html;
}

?>