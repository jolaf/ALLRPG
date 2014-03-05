<?php
if($_SESSION["user_id"]!='') {
   	if(encode($_GET["re"])!='') {
    	$result=mysql_query("SELECT * FROM ".$prefix."blog_pms where id=".encode($_GET["re"])." and user_id=".$_SESSION["user_id"]);
		$a = mysql_fetch_array($result);
		if($a["id"]!='') {
			$user_iddef=$a["creator_id"];
			if(strpos(decode($a["title"]),"Re: ")!==false) {
				$titledef=decode($a["title"]);
			}
			else {
				$titledef='Re: '.decode($a["title"]);
			}
		}
   	}
   	elseif(encode($_GET["requote"])!='') {
    	$result=mysql_query("SELECT * FROM ".$prefix."blog_pms where id=".encode($_GET["requote"])." and user_id=".$_SESSION["user_id"]);
		$a = mysql_fetch_array($result);
		if($a["id"]!='') {
			$user_iddef=$a["creator_id"];
			$titledef='Re: '.decode($a["title"]);
			$result2=mysql_query("SELECT * FROM ".$prefix."users where id=".$a["creator_id"]);
			$b = mysql_fetch_array($result2);
			$contentdef='<p>&nbsp;</p><div class="quote">'.usname($b,true).' написал(-а) '.date("d.m.Y в H:i",$a["date"]).'<br /><i>'.decode($a["content"]).'</i></div><p>&nbsp;</p>';
		}
   	}
   	elseif(encode($_GET["fw"])!='') {
    	$result=mysql_query("SELECT * FROM ".$prefix."blog_pms where id=".encode($_GET["fw"])." and user_id=".$_SESSION["user_id"]);
		$a = mysql_fetch_array($result);
		if($a["id"]!='') {
			$titledef='FW: '.decode($a["title"]);
			$result2=mysql_query("SELECT * FROM ".$prefix."users where id=".$a["creator_id"]);
			$b = mysql_fetch_array($result2);
			$contentdef='<p>&nbsp;</p><div class="quote">'.usname($b,true).' написал(-а) '.date("d.m.Y в H:i",$a["date"]).'<br /><i>'.decode($a["content"]).'</i></div><p>&nbsp;</p>';
		}
   	}
   	else {
   		$user_iddef=encode($_GET["user_id"]);
   	}

   	// Создание объекта
	$obj=new netObj(
		'outbox',
		$prefix."blog_pms",
		"сообщение",
		Array("Сообщение отправлено.","","Сообщение удалено."),
		Array(),
		2,
		'100%',
		50
	);

	// Создание схемы прав объекта
	if($_SESSION["user_id"]!='') {
		$obj_r=new netRight(
			true,
			true,
			false,
			true,
			100,
			'creator_id='.$_SESSION["user_id"]." and todelete='0'",
			'creator_id='.$_SESSION["user_id"]." and todelete='0'",
			'creator_id='.$_SESSION["user_id"]." and todelete='0'"
		);
		$obj->setRight($obj_r);
	}

	// Создание полей объекта

	$obj_1=createElem(Array(
			'name'	=>	"user_id",
			'sname'	=>	"Получатель",
			'type'	=>	"sarissa",
			'parents'	=>	'search',
			'file'	=>	$helpers_path.'userslist.php',
			'file2'	=>	$server_inner_path.'helpers/userslist.php',
			'table'	=>	$prefix.'users',
			'help'	=>	'найдите участника allrpg.info через Ф.И.О., никнейм или ИНП.',
			'read'	=>	10,
			'write'	=>	100,
			'default'	=>	$user_iddef,
			'mustbe'	=>	true,
		)
	);
	$obj->setElem($obj_1);

	$obj_2=createElem(Array(
			'name'	=>	"title",
			'sname'	=>	"Заголовок",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	100,
			'maxchar'	=>	255,
			'default'	=>	$titledef,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_2);

	$obj_3=createElem(Array(
			'name'	=>	"icon",
			'sname'	=>	"Иконка",
			'type'	=>	"multiselect",
			'one'	=>	true,
			'values'	=>	make5field($prefix."blog_icons where gr='pms' order by code","id","name"),
			'images'	=>	make5field($prefix."blog_icons where gr='pms' order by code","id","img"),
			'path'	=>	$server_absolute_path.$uploads[1]['path'],
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_3);

	$obj_4=createElem(Array(
			'name'	=>	"content",
			'sname'	=>	"Текст сообщения",
			'type'	=>	"wysiwyg",
			'height'	=>	400,
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true,
			'default'	=>	$contentdef,
		)
	);
	$obj->setElem($obj_4);

	$obj_5=createElem(Array(
			'name'	=>	"creator_id",
			'type'	=>	"hidden",
			'default'	=>	$_SESSION["user_id"],
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_5);

	$obj_6=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Отправлено",
			'type'	=>	"timestamp",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_6);

	// Исполнение dynamicaction, если необходимо
	if($action=="dynamicaction")
	{
		require_once($server_inner_path.$direct."/dynamicaction.php");
		if($object=="outbox")
		{
			function dynamic_add_success() {
				global
					$prefix,
					$server_inner_path,
					$direct,
					$id;

				mysql_query("UPDATE ".$prefix."blog_pms SET creator_id=".$_SESSION["user_id"].", pmread='0' WHERE id=".$id);
				require_once($server_inner_path.$direct."/classes/base_mails.php");
				$result=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".encode($_POST["user_id"]));
   				$a=mysql_fetch_array($result);
   				$result2=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$_SESSION["user_id"]);
   				$b=mysql_fetch_array($result2);
   				$myname=usname($b,true);
				$myemail=decode($b["em"]);
				$contactemail=decode($a["em"]);
				$subject='Новое личное сообщение на allrpg.info';
				$message='Вам поступило новое личное сообщение на allrpg.info от '.$myname.'.
Ответить на сообщение Вы можете здесь: '.$server_absolute_path.'inbox/'.$id.'/ (Вы должны быть залогинены на allrpg.info).

Тема сообщения:
'.decodesafe(encode($_POST["title"])).'

Текст:
'.str_replace("\r\n	","",strip_tags($_POST["content"]));
				if(send_mail($myname, $myemail, $contactemail, $subject, $message)) {
					err_info('Получателю отправлено e-mail уведомление.');
				}
				else {
					err_red("При отправке e-mail уведомления получателю на сервере возникли проблемы.");
				}
			}
			if($actiontype=="add" || $actiontype=="change") {
				dynamicaction($obj);
			}
			else {
				$result=mysql_query("SELECT * FROM ".$prefix."blog_pms where id=".$id);
				$a = mysql_fetch_array($result);
				if($a["todelete2"]=='1') {
					dynamicaction($obj);
				}
				else {
					mysql_query("UPDATE ".$prefix."blog_pms SET todelete='1' WHERE id=".$id);
					dynamic_err(array(array('success',"Сообщение удалено.")),$server_absolute_path.'outbox/');
				}
			}
		}
	}

	// Добавление параметра values к select'ам и multiselect'ам.
    $obj_7=createElem(Array(
			'name'	=>	"id",
			'type'	=>	"select",
			'read'	=>	100000,
			'write'	=>	100000,
		)
	);
	$obj->setElem($obj_7);

	// Инициализация элементов поиска, если нужен.
    $result=mysql_query("SELECT * FROM ".$prefix."users where id in (SELECT user_id from ".$prefix."blog_pms WHERE creator_id=".$_SESSION["user_id"].")");
	while($a = mysql_fetch_array($result)) {
		$allusers[]=Array($a["id"],usname($a,true));
	}
	foreach ($allusers as $key => $row)
	{
		$allusers_sort[$key]  = strtolower($row[1]);
	}
	array_multisort($allusers_sort, SORT_ASC, $allusers);

	$result=mysql_query("SELECT t1.id, t2.img FROM ".$prefix."blog_pms t1 LEFT JOIN ".$prefix."blog_icons t2 ON t1.icon REGEXP '-' + t2.id + '-' WHERE t1.creator_id=".$_SESSION["user_id"]." ORDER BY t1.id DESC");
	while($a = mysql_fetch_array($result)) {
		if($a["img"]!='') {
			$allicons[]=Array($a["id"],'<img src="'.$server_absolute_path.$uploads[1]['path'].$a["img"].'" />');
		}
		else {
			$allicons[]=Array($a["id"],'');
		}
	}

	$obj->setSort(Array(
			'0'	=>	Array(
				Array("date", "DESC", false, true),
				Array("id", "DESC", true, false, Array(2,$allicons)),
				Array("title", "ASC", true, true),
				Array("user_id", "ASC", true, true, Array(2,$allusers)),
				Array("date", "ASC", true, true),
			),
		)
	);

	$obj->setSearch($obj_2);
	$obj->setSearch($obj_4);

	// Отрисовка всего объекта html'ем в переменную
	$obj_html.=$obj->draw();

	// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
	$pagetitle=h1line('Отправленные',$curdir.$kind.'/');

	$obj_html=str_replace('Добавить сообщение','Отправить сообщение',$obj_html);
	$content2.=$obj_html;
}
?>