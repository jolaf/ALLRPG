<?php
if($_SESSION["user_id"]!='' && $workrights["site"]["allrights"]) {
	// права управления

	// Создание объекта
	$obj=new netObj(
		'allrights',
		$prefix."allrights2",
		"право доступа",
		Array("Право доступа добавлено.","Права доступа изменены","Право доступа удалено."),
		Array(
			'0'	=>	Array(
				Array("rights", "ASC", true, true),
			),
		),
		1,
		'100%',
		50
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
	$obj_1=createElem(Array(
			'name'	=>	"user_id",
			'sname'	=>	"ИНП",
			'type'	=>	"number",
			'help'	=>	"введите Идентификационный Номер Пользователя, которому хотите выдать права.",
			'width'	=>	60,
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_1);

	$obj_2=createElem(Array(
			'name'	=>	"user_id",
			'sname'	=>	"Пользователь",
			'type'	=>	"select",
			'read'	=>	10,
			'write'	=>	1000000,
		)
	);
	$obj->setElem($obj_2);

	$obj_3=createElem(Array(
			'name'	=>	"rights",
			'sname'	=>	"Права",
			'type'	=>	"select",
			'values'	=>	Array(Array('1','Глав.мастер'),Array('4','Дизайнер'),Array('2','Мастер'),Array('3','Автор новостей')),
			'help'	=>	"глав.мастер имеет право настраивать сайт и работать с заявками.<br>
Мастер имеет право работать с заявками.<br>
Автор новостей может писать новости сайта.<br>
Дизайнер имеет обширные права, близкие к глав.мастеру.",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_3);

	$obj_6=createElem(Array(
			'name'	=>	"locations",
			'sname'	=>	"Локации",
			'type'	=>	"multiselect",
			'values'	=>	array_merge(Array(Array('0','Все локации')),make5fieldtree(false,$prefix."roleslocat","parent",0," AND site_id=".$_SESSION["siteid"],"code asc, name asc",0,"id","name",2)),
			'help'	=>	'пользователям с правами «глав. мастер» и «мастер» можно назначить локации, с которыми они будут работать. Заявки остальных локаций они видеть не будут.',
			'default'	=>	'-0-',
			'width'	=>	200,
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_6);

	$obj_7=createElem(Array(
			'name'	=>	"notifications",
			'sname'	=>	"Оповещения",
			'type'	=>	"multiselect",
			'values'	=>	array_merge(Array(Array('0','Как в «локациях»')),make5fieldtree(false,$prefix."roleslocat","parent",0," AND site_id=".$_SESSION["siteid"],"code asc, name asc",0,"id","name",2)),
			'help'	=>	'только по этим локациям пользователь будет получать e-mail оповещения о новых и об измененных заявках (если включит себе соответствующие функции в личных настройках в «<a href="'.$server_absolute_path_site.'orders/">Поданных заявках</a>»). Для снижения нагрузки вложенность локаций ограничена (вы можете настраивать уведомления и доступ только по локациям верхних уровней)',
			'default'	=>	'-0-',
			'width'	=>	200,
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_7);

	$obj_4=createElem(Array(
			'name'	=>	"site_id",
			'type'	=>	"hidden",
			'default'	=>	$_SESSION["siteid"],
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_4);

	$obj_5=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Последнее изменение",
			'type'	=>	"timestamp",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_5);

	// Исполнение dynamicaction, если необходимо
	if($action=="dynamicaction")
	{
		require_once($server_inner_path.$direct."/dynamicaction.php");
		if($object=="allrights")
		{
			if($actiontype=="delete") {
				$result=mysql_query("SELECT * FROM ".$prefix."allrights2 where site_id=".$_SESSION["siteid"]." and rights=1 and id!=".$id);
				$a = mysql_fetch_array($result);
				if($a["id"]!='') {
					dynamicaction($obj);
				}
				else {
					err_red("Должен остаться хотя бы один пользователь с правами глав.мастера. Сначала определите нового глав.мастера и только затем снимайте права с имеющегося.");
				}
			}
			elseif($actiontype=="change") {
				$cantsave=false;
				$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."allrights2 where site_id=".$_SESSION["siteid"]." and rights=1");
				$a = mysql_fetch_array($result);
				$gms=$a[0];
				$countnotgms=0;
				$result=mysql_query("SELECT * FROM ".$prefix."allrights2 where site_id=".$_SESSION["siteid"]);
				while($a = mysql_fetch_array($result)) {
					foreach($_REQUEST["id"] as $key=>$check_id) {
                    	$check_id=encode_to_cp1251($check_id);
                    	if($check_id==$a["id"]) {
                            if(encode_to_cp1251($_REQUEST["rights"][$key])!=1 && $a["rights"]==1) {
                            	$countnotgms++;
                            }
                            elseif(encode_to_cp1251($_REQUEST["rights"][$key])==1 && $a["rights"]!=1) {
                            	$countnotgms--;
                            }
                            if($countnotgms>=$gms) {
                            	$cantsave=true;
                            }
                    	}
                    }
     			}
				if(!$cantsave) {
					dynamicaction($obj);
				}
				else {
					dynamic_err_one('error',"Должен остаться хотя бы один пользователь с правами глав.мастера. Сначала определите нового глав.мастера и только затем снимайте права с имеющегося.");
				}
			}
			else {
				dynamicaction($obj);
			}
		}
	}

	// Исполнение дополнительных действий после dynamicaction, если необходимо
	if(!$trouble && count($trouble2)==0)
	{
		if($object=="allrights")
		{
			if($actiontype=="add")
			{
				mysql_query("UPDATE ".$prefix."allrights2 SET site_id=".$_SESSION["siteid"].", signtonew='1', signtochange='1', signtocomments='1' WHERE id=".$id);
			}
		}
	}

	// Добавление параметра values к select'ам и multiselect'ам.
	$result=mysql_query("SELECT * FROM ".$prefix."users where sid in (SELECT user_id FROM ".$prefix."allrights2 where site_id=".$_SESSION["siteid"].")");
	while($a = mysql_fetch_array($result))
	{
		$masternotify=usname($a,true,true).'<br><span style="font-size: 90%;">уведомления:<br>о новых заявках: ';
		$result2=mysql_query("SELECT * FROM ".$prefix."allrights2 where site_id=".$_SESSION["siteid"]." and user_id=".$a["sid"]);
		$b = mysql_fetch_array($result2);
		if($b["signtonew"]=='1') {
			$masternotify.='<font color="green">включены</font>';
		}
		else {
			$masternotify.='<font color="red">выключены</font>';
		}
		$masternotify.='<br>об изменениях: ';
		if($b["signtochange"]=='1') {
			$masternotify.='<font color="green">включены</font>';
		}
		else {
			$masternotify.='<font color="red">выключены</font>';
		}
		$masternotify.='<br>о комментариях: ';
		if($b["signtocomments"]=='1') {
			$masternotify.='<font color="green">включены</font>';
		}
		else {
			$masternotify.='<font color="red">выключены</font>';
		}
		$masternotify.='</span>';
		$allusers[]=Array($a["sid"],$masternotify);
	}
	$obj_2->setValues($allusers);

	// Инициализация элементов поиска, если нужен.

	// Отрисовка всего объекта html'ем в переменную
	$obj_html.=$obj->draw();

	// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
	$pagetitle=h1line('Права управления');
	$content2.='<div class="narrow">'.$obj_html.'</div>';
}
?>