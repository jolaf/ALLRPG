<?php
if($_SESSION["user_id"]!='' && $workrights["site"]["pages"]) {
	// структура и страницы

	// Создание объекта
	$obj=new netObj2(
		'pages',
		$prefix."pages",
		"раздел/подраздел",
		Array("Раздел/подраздел успешно добавлен.","Раздел/подраздел успешно изменен.","Раздел/подраздел успешно удален.","Раздел/подраздел и все его страницы успешно удалены."),
		"страницу",
		Array("Страница успешно добавлена.","Страница успешно изменена.","Страница успешно удалена."),
		Array(
			'0'	=>	Array(
				Array("code", "ASC", false, true),
				Array("name", "ASC", true, false),
				Array("code", "ASC", true, true),
			),
			'1'	=>	Array(
				Array("code", "DESC", false, true),
				Array("name", "ASC", true, false),
			),
		),
		3,
		'100%',
		50,
		'parent',
		'content',
		'code',
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

	$grs=Array();
	$grs[]=Array('0','без ограничений');
	$grs[]=Array('10001','все пользователи allrpg.info');
	$grs[]=Array('10002','все пользователи, подавшие заявку');
	$grs[]=Array('10003','все пользователи, чьи заявки приняты');
	$result=mysql_query("SELECT DISTINCT * from ".$prefix."virtrights where site_id=".$_SESSION["siteid"]." GROUP by gr order by gr asc");
	while($a=mysql_fetch_array($result))
	{
		$grs[]=Array($a["gr"],'Группа №'.$a["gr"]);
	}

	if($act=="view" && ($actiontype=='' || $trouble))
	{
		$result=mysql_query("SELECT * from ".$prefix."pages where id=".$id." AND site_id=".$_SESSION["siteid"]);
		$a=mysql_fetch_array($result);
		$result4=mysql_query("SELECT * from ".$prefix."sites where id=".$_SESSION["siteid"]);
		$d=mysql_fetch_array($result4);
		$result2=mysql_query("SELECT * from ".$prefix."pages where id=".$a["parent"]." AND site_id=".$_SESSION["siteid"]);
		$b=mysql_fetch_array($result2);
		if($b["alias"]!='') {
			$linkup=$lead1.$d["path"].$lead2.decode($b["alias"]).'/'.$id.'/';
		}
		else {
			if($b["parent"]!=0) {
				$result3=mysql_query("SELECT * from ".$prefix."pages where id=".$b["parent"]." AND site_id=".$_SESSION["siteid"]);
				$c=mysql_fetch_array($result3);
				$linkup=$lead1.$d["path"].$lead2.$c["code"].'/'.$b["code"].'/'.$id.'/';
			}
			else
			{
				$linkup=$lead1.$d["path"].$lead2.$b["code"].'/'.$id.'/';
			}
		}
		$linkup='<a href="'.$linkup.'" target="_blank">'.$linkup.'</a>';
	}

	$result=mysql_query("SELECT * from ".$prefix."users where sid IN (SELECT user_id from ".$prefix."allrights2 where (rights=1 OR rights=4) and site_id=".$_SESSION["siteid"].")");
	while($a=mysql_fetch_array($result))
	{
		$pages3_f[]=Array($a["id"],usname($a,true));
	}

	// Создание полей объекта
	$obj_1=createElem(Array(
			'name'	=>	"parent",
			'sname'	=>	"Родительский раздел",
			'type'	=>	"select",
			'values'	=>	make5fieldtree(true,$prefix."pages","parent",0," AND content='{menu}' AND site_id=".$_SESSION["siteid"]." AND id!=".$id,"code asc",1,"id","name",1),
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
			'help'	=>	"число от 1 и далее",
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
			'help'	=>	"не более 255 символов",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_3);

	$obj_22=createElem(Array(
			'name'	=>	"alias",
			'sname'	=>	"Имя раздела в ссылках",
			'type'	=>	"text",
			'help'	=>	"раздел, у которого в данное поле вписать, например, «rules», будет выводиться по адресу: http://ваш_субдомен.allrpg.info/rules/",
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_22);

	$obj_4=createElem(Array(
			'name'	=>	"http",
			'sname'	=>	"Адрес пересылки",
			'type'	=>	"text",
			'help'	=>	"введите http-адрес, если хотите, чтобы при щелчке на ссылку, ведущую на данный раздел, пользователь автоматически переходил на какой-либо внешний ресурс.",
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_4);

	$obj_5=createElem(Array(
			'name'	=>	"active",
			'sname'	=>	"Раздел/подраздел активен",
			'type'	=>	"checkbox",
			'default'	=>	1,
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_5);

	$obj_6=createElem(Array(
			'name'	=>	"rights",
			'sname'	=>	"Группы пользователей, которые видят раздел/подраздел",
			'type'	=>	"multiselect",
			'values'	=>	$grs,
			'default'	=>	'-0-',
			'help'	=>	'настройте «<a href="'.$server_absolute_path_site.'groups/">Группы пользователей</a>». Только <a href="'.$server_absolute_path_site.'allrights/">глав.мастера сайта</a> имеют доступ ко всем страницам на сайте без исключения. Права раздела автоматически наследуются всеми его подразделами и страницами, однако, после наследования Вы можете изменить права в каждом отдельно взятом подразделе и/или странице. Чтобы пользователи видели эту страницу с учетом своих прав, они должны быть залогинены на allrpg.info с выставленной галочкой «Запомнить».',
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_6);

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

	$obj_8=createElem(Array(
			'name'	=>	"site_id",
			'type'	=>	"hidden",
			'default'	=>	$_SESSION["siteid"],
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_8);

	$obj_9=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Последнее изменение",
			'type'	=>	"timestamp",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_9);

	$obj_10=createElem(Array(
			'name'	=>	"link",
			'sname'	=>	"Адрес данной страницы",
			'type'	=>	"text",
			'default'	=>	$linkup,
			'read'	=>	10,
			'write'	=>	100000,
		)
	);
	$obj->setElem2($obj_10);

	$obj_11=createElem(Array(
			'name'	=>	"parent",
			'sname'	=>	"Разместить в разделе",
			'type'	=>	"select",
			'values'	=>	make5fieldtree(false,$prefix."pages","parent",0," AND content='{menu}' AND site_id=".$_SESSION["siteid"],"code asc",1,"id","name",3),
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem2($obj_11);

	$obj_12=createElem(Array(
			'name'	=>	"code",
			'sname'	=>	"Страница по умолчанию в разделе",
			'type'	=>	"select",
			'values'	=>	Array(Array('1','да'),Array('0','нет')),
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem2($obj_12);

	$obj_13=createElem(Array(
			'name'	=>	"name",
			'sname'	=>	"Название страницы",
			'type'	=>	"text",
			'help'	=>	"не более 255 символов",
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem2($obj_13);

	$obj_14=createElem(Array(
			'name'	=>	"author",
			'sname'	=>	"Автор",
			'type'	=>	"select",
			'values'	=>	$pages3_f,
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem2($obj_14);

	$obj_15=createElem(Array(
			'name'	=>	"active",
			'sname'	=>	"Страница активна",
			'type'	=>	"checkbox",
			'default'	=>	1,
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem2($obj_15);

	$obj_16=createElem(Array(
			'name'	=>	"http",
			'sname'	=>	"Адрес пересылки",
			'type'	=>	"text",
			'help'	=>	"введите http-адрес, если хотите, чтобы при щелчке на ссылку, ведущую на данную страницу, пользователь автоматически переходил на какой-либо внешний ресурс.",
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem2($obj_16);

	$obj_17=createElem(Array(
			'name'	=>	"content",
			'sname'	=>	"Содержимое страницы",
			'type'	=>	"wysiwyg",
			'height'	=>	400,
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem2($obj_17);

	$obj_18=createElem(Array(
			'name'	=>	"rights",
			'sname'	=>	"Группы пользователей, которые видят страницу",
			'type'	=>	"multiselect",
			'values'	=>	$grs,
			'default'	=>	'-0-',
			'help'	=>	'настройте «<a href="'.$server_absolute_path_site.'groups/">Группы пользователей</a>». Только <a href="'.$server_absolute_path_site.'allrights/">глав.мастера сайта</a> имеют доступ ко всем страницам на сайте без исключения. Чтобы пользователи видели эту страницу с учетом своих прав, они должны быть залогинены на allrpg.info с выставленной галочкой «Запомнить».',
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem2($obj_18);

	$obj_19=createElem(Array(
			'name'	=>	"nocomments",
			'sname'	=>	"Отключить систему комментариев на данной странице",
			'type'	=>	"checkbox",
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem2($obj_19);

	$obj_20=createElem(Array(
			'name'	=>	"site_id",
			'type'	=>	"hidden",
			'default'	=>	$_SESSION["siteid"],
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem2($obj_20);

	$obj_21=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Последнее изменение",
			'type'	=>	"timestamp",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem2($obj_21);

	// Исполнение dynamicaction, если необходимо
	if($action=="dynamicaction")
	{
		require_once($server_inner_path.$direct."/dynamicaction.php");
		if($object=="pages") {
			if($actiontype=="add") {
				function dynamic_add_success() {
					global
						$prefix,
						$_SESSION,
						$id;

					mysql_query("UPDATE ".$prefix."pages SET site_id=".$_SESSION["siteid"]." WHERE id=".$id);
				}
			}
			elseif($actiontype=="change") {
				function dynamic_save_success() {
					global
						$prefix,
						$_SESSION,
						$id;

					$result=mysql_query("SELECT * FROM ".$prefix."pages where site_id=".$_SESSION["siteid"]." and id=".$id);
					$a=mysql_fetch_array($result);
					$result2=mysql_query("SELECT * FROM ".$prefix."pages where site_id=".$_SESSION["siteid"]." and parent=".$id);
					while($b=mysql_fetch_array($result2)) {
						mysql_query("UPDATE ".$prefix."pages SET rights='".$a["rights"]."' WHERE id=".$b["id"]);
						$result3=mysql_query("SELECT * FROM ".$prefix."pages where site_id=".$_SESSION["siteid"]." and parent=".$b["id"]);
						while($c=mysql_fetch_array($result3)) {
							mysql_query("UPDATE ".$prefix."pages SET rights='".$a["rights"]."' WHERE id=".$c["id"]);
						}
					}
				}
			}

			if(encode_to_cp1251($_REQUEST["alias"])!='') {
				if(is_numeric(encode_to_cp1251($_REQUEST["alias"]))) {
					dynamic_err_one('error',"Имя раздела в ссылках не может быть числовым.");
				}
				else {
					if($actiontype=="add") {
						$result=mysql_query("SELECT * FROM ".$prefix."pages where site_id=".$_SESSION["siteid"]." and alias='".encode_to_cp1251($_REQUEST["alias"])."'");
					}
					elseif($actiontype=="change") {
						$result=mysql_query("SELECT * FROM ".$prefix."pages where site_id=".$_SESSION["siteid"]." and alias='".encode_to_cp1251($_REQUEST["alias"])."' and id!=".$id);
					}
					$a=mysql_fetch_array($result);
					if($a["id"]!='') {
						dynamic_err_one('error',"Такое имя раздела в ссылках уже используется. Выберите другое.");
					}
					else {
						dynamicaction($obj);
					}
				}
			}
			else {
				dynamicaction($obj);
			}
		}
	}

	// Добавление параметра values к select'ам и multiselect'ам.
	$obj_1->setValues(make5fieldtree(true,$prefix."pages","parent",0," AND content='{menu}' AND site_id=".$_SESSION["siteid"]." AND id!=".$id,"code asc",1,"id","name",1));

	// Инициализация элементов поиска, если нужен.

	// Отрисовка всего объекта html'ем в переменную
	$obj_html.=$obj->draw();

	// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
	$pagetitle=h1line('Разделы и страницы',$curdir.$kind.'/');
	$content2.='<div class="narrow">'.$obj_html.'</div>';
}
?>