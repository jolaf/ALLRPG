<?php
if($_SESSION["user_id"]!='' && $workrights["site"]["subscribe"]) {
	if($action=="sndmsg") {
		require_once($server_inner_path.$direct."/classes/base_mails.php");

		$result=mysql_query("SELECT * from ".$prefix."users where id=".$_SESSION['user_id']);
		$a=mysql_fetch_array($result);
		$myname=usname($a, true);
		$myemail=decode($a["em"]);

		$result=mysql_query("SELECT * from ".$prefix."sites where id=".$_SESSION["siteid"]);
		$a=mysql_fetch_array($result);

		$subject='Рассылка проекта «'.decode($a["title"]).'»';

		if(encode_to_cp1251($_REQUEST["msg"])!='' && strlen(strip_tags(encode_to_cp1251($_REQUEST["msg"])))>=10) {
			$message='<html>
<head>
<title>Рассылка проекта «'.decode($a["title"]).'»</title>
</head>

<body>
'.$_REQUEST["msg"].'
</body>
</html>';
		}
		else {
			dynamic_err_one('error',"Текст рассылки должен быть не менее 10 символов.");
		}

		if($myname!='' && $myemail!='') {
			if($message!='') {
				$sendlist=Array();
				if(encode_to_cp1251($_REQUEST["tosend"]["allorders"])=='on') {
					$result=mysql_query("SELECT * FROM ".$prefix."roles where site_id=".$_SESSION["siteid"]." and status!=4 and todelete!=1 and todelete2!=1");
					while($a=mysql_fetch_array($result)) {
						if(!in_array($a["player_id"],$sendlist)) {
							$sendlist[]=$a["player_id"];
						}
					}
				}
				if(encode_to_cp1251($_REQUEST["tosend"]["allordersaccepted"])=='on') {
					$result=mysql_query("SELECT * FROM ".$prefix."roles where site_id=".$_SESSION["siteid"]." and status=3 and todelete!=1 and todelete2!=1");
					while($a=mysql_fetch_array($result)) {
						if(!in_array($a["player_id"],$sendlist)) {
							$sendlist[]=$a["player_id"];
						}
					}
				}
				if(encode_to_cp1251($_REQUEST["tosend"]["allordersnotaccepted"])=='on') {
					$result=mysql_query("SELECT * FROM ".$prefix."roles where site_id=".$_SESSION["siteid"]." and (status=1 OR status=2) and todelete!=1 and todelete2!=1");
					while($a=mysql_fetch_array($result)) {
						if(!in_array($a["player_id"],$sendlist)) {
							$sendlist[]=$a["player_id"];
						}
					}
				}
				if(encode_to_cp1251($_REQUEST["tosend"]["debitors"])=='on') {
					$result=mysql_query("SELECT * FROM ".$prefix."roles where site_id=".$_SESSION["siteid"]." and moneydone='0' and status!=4 and todelete!=1 and todelete2!=1");
					while($a=mysql_fetch_array($result)) {
						if(!in_array($a["player_id"],$sendlist)) {
							$sendlist[]=$a["player_id"];
						}
					}
				}
				$result=mysql_query("SELECT * FROM ".$prefix."roles where site_id=".$_SESSION["siteid"]." and status!=4 and todelete!=1 and todelete2!=1");
				while($a=mysql_fetch_array($result)) {
					if(encode_to_cp1251($_REQUEST["tosend"]["order_".$a["id"]])=='on') {
						if(!in_array($a["player_id"],$sendlist)) {
							$sendlist[]=$a["player_id"];
						}
					}
				}
				$result2=mysql_query("SELECT * from ".$prefix."roleslocat where site_id=".$_SESSION["siteid"]);
				while($b=mysql_fetch_array($result2)) {
                	if(encode_to_cp1251($_REQUEST["tosend"]["locat_".$b["id"]])=='on') {
                		$result=mysql_query("SELECT * FROM ".$prefix."roles where locat=".$b["id"]." and status!=4 and todelete!=1 and todelete2!=1");
						while($a=mysql_fetch_array($result)) {
							if(!in_array($a["player_id"],$sendlist)) {
								$sendlist[]=$a["player_id"];
							}
						}
                	}
				}
				$result=mysql_query("SELECT DISTINCT * from ".$prefix."virtrights where site_id=".$_SESSION["siteid"]." GROUP by gr order by gr asc");
				while($a=mysql_fetch_array($result)) {
					if(encode_to_cp1251($_REQUEST["tosend"][$a["gr"]])=='on') {
						$result2=mysql_query("SELECT * FROM ".$prefix."virtrights where site_id=".$_SESSION["siteid"]." and gr=".$a["gr"]);
						while($b=mysql_fetch_array($result2)) {
							$result3=mysql_query("SELECT * FROM ".$prefix."users where sid=".$b["user_id"]);
							$c=mysql_fetch_array($result3);
							if(!in_array($c["id"],$sendlist)) {
								$sendlist[]=$c["id"];
							}
						}
					}
				}
				
				$not_all_regions = encode_to_cp1251($_REQUEST["tosend2"]["allregions"])!='on';

				if($not_all_regions) {
					unset($region_list);
					$region_list=Array();
					$result=mysql_query("SELECT DISTINCT city.parent AS id 
					FROM {$prefix}geography city, {$prefix}users u, {$prefix}roles r WHERE city.id = u.city AND u.id = player_id AND r.site_id =".$_SESSION["siteid"]." AND r.todelete2 !=1");
					while($a=mysql_fetch_array($result)) {
						if(encode_to_cp1251($_REQUEST["tosend2"][$a["id"]])=='on') {
							$region_list[] = $a['id'];
						}
					}
					}

                    foreach($sendlist as $key=>$value) {
                    	$result=mysql_query("
                    	SELECT city.parent AS region_id, em 
                    	from {$prefix}users u
                    	INNER JOIN geography city ON city.id = u.city
                    	where u.id=$value");
						$a=mysql_fetch_array($result);
						if($not_all_regions && !in_array($a["region_id"],$region_list)) {
							unset($sendlist[$key]);
						}
						else
						{
							$contactemail=decode($a["em"]);
							send_mail($myname, $myemail, $contactemail, $subject, $message, true);
						}
					}
				

				if(count($sendlist)>0) {
					dynamic_err_one('success',"Рассылка по ".count($sendlist)." пользователям успешно проведена.");
				}
				else {
					if(encode_to_cp1251($_REQUEST["tosend"])=='') {
						dynamic_err_one('error',"Необходимо выбрать хотя бы одну категорию пользователей для рассылки.");
					}
					elseif(encode_to_cp1251($_REQUEST["tosend2"])=='') {
						dynamic_err_one('error',"Необходимо выбрать хотя бы один регион для рассылки.");
					}
					else {
						dynamic_err_one('error',"Нет ни одного пользователя среди выбранных для рассылки категорий и регионов.");
					}
				}
			}
		}
		else {
			dynamic_err_one('error',"Заполните, пожалуйста, свои регистрационные данные (Ф.И.О, ник, e-mail), иначе рассылка будет Вам не доступна.");
		}
	}

	$pagetitle=h1line('E-mail рассылка');
	$content2 .= '
<center><div class="cb_editor">
<form action="'.$curdir.$kind.'/" method="post" enctype="multipart/form-data">
<input type="hidden" name="kind" value="'.$kind.'">
<input type="hidden" name="object" value="'.$object.'">
<input type="hidden" name="action" value="sndmsg">
';

	$obj_1=createElem(Array(
			'name'	=>	"msg",
			'sname'	=>	"Текст рассылки",
			'type'	=>	"wysiwyg",
			'height'	=>	400,
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true,
		)
	);
	$obj_1->setVal('',true);
	$content2.='<div class="fieldname" id="name_msg">Текст рассылки</div><div class="fieldvalue" id="div_msg">'.$obj_1->draw(2,"write").'</div><div class="clear"></div><br />';

	$grs=Array();
	$grs[]=Array('allorders','<b>все заявившиеся</b> (кроме отклоненных/удаленных)');
	$grs[]=Array('allordersaccepted','<b>все пользователи, чьи заявки приняты</b>');
	$grs[]=Array('allordersnotaccepted','<b>все пользователи, чьи заявки еще не приняты</b>');
	$grs[]=Array('debitors','<b>все пользователи, не сдавшие взнос</b>');

	$result=mysql_query("SELECT * from ".$prefix."roleslocat where site_id=".$_SESSION["siteid"]." order by code");
	while($a=mysql_fetch_array($result)) {
		$result2=mysql_query("SELECT * from ".$prefix."roles where locat=".$a["id"]." and status!=4 and todelete!='1' and todelete2!='1' order by status, sorter");
		if(mysql_affected_rows($link)>0) {
			$grs[]=Array("locat_".$a["id"],'<b>'.$a["name"].'</b>');
		}
		while($b=mysql_fetch_array($result2)) {
			$stat='';
			if($b["status"]==1) {
				$stat='подана';
			}
			elseif($b["status"]==2) {
				$stat='обсуждается';
			}
			elseif($b["status"]==3) {
				$stat='принята';
			}
			if(str_replace(' ','',$b["sorter"])!='') {
				$grs[]=Array("order_".$b["id"],'&nbsp;&nbsp;&nbsp;&nbsp;'.$b["sorter"].' ('.$stat.')');
			}
			else {
				$result3=mysql_query("SELECT * from ".$prefix."users where id=".$b["player_id"]);
				$c=mysql_fetch_array($result3);
				$grs[]=Array("order_".$b["id"],'&nbsp;&nbsp;&nbsp;&nbsp;{'.usname($c,true).'} ('.$stat.')');
			}
		}
	}

	$result=mysql_query("SELECT DISTINCT * from ".$prefix."virtrights where site_id=".$_SESSION["siteid"]." GROUP by gr order by gr asc");
	while($a=mysql_fetch_array($result))
	{
		$grs[]=Array($a["gr"],'<b>Группа №'.$a["gr"].'</b>');
	}

	$obj_2=createElem(Array(
			'name'	=>	"tosend",
			'sname'	=>	"Разослать следующим пользователям",
			'type'	=>	"multiselect",
			'values'	=>	$grs,
			'default'	=>	'-allorders-',
			'read'	=>	10,
			'write'	=>	100,
			'height'	=>	'200px',
			'mustbe'	=>	true,
		)
	);
	$obj_2->setVal('',true);
	$content2.='<div class="fieldname" id="name_tosend">Разослать следующим пользователям</div>
<div class="help" id="help_tosend">настройте «<a href="'.$server_absolute_path_site.'groups/">Группы пользователей</a>» для рассылки по вашим группам пользователей.</div><div class="fieldvalue" id="div_tosend">'.$obj_2->draw(2,"write").'</div><div class="clear"></div><br />';

    $regions=Array();
	$regions[]=Array('allregions','<b>все регионы</b>');

	$result = mysql_query("seLECT COUNT(u.id) AS count, g2.id, g2.name
		from {$prefix}users u
		INNER JOIN {$prefix}roles r ON u.id = r.player_id
		INNER JOIN {$prefix}geography g1 ON g1.id = u.city
		INNER JOIN {$prefix}geography g2 ON g2.id = g1.parent
		WHERE r.site_id = {$_SESSION['siteid']} and status!=4 and todelete!=1 and todelete2!=1
		GROUP BY g2.id, g2.name");
	while($a=mysql_fetch_array($result))
	{
		$regions[]=Array($a["id"],'<b>'.$a["name"].' ('.$a['count'].')</b>');
	}

	$obj_3=createElem(Array(
			'name'	=>	"tosend2",
			'sname'	=>	"Разослать по следующим регионам",
			'type'	=>	"multiselect",
			'values'	=>	$regions,
			'default'	=>	'-allregions-',
			'read'	=>	10,
			'write'	=>	100,
			'height'	=>	'200px',
			'mustbe'	=>	true,
		)
	);
	$obj_3->setVal('',true);
	$content2.='<div class="fieldname" id="name_tosend2">Разослать по следующим регионам</div>
<div class="help" id="help_tosend2">рассылка будет направлена только пользователям, соответствующим условиям в «Разослать следующим пользователям» и из указанных здесь регионов.</div><div class="fieldvalue" id="div_tosend2">'.$obj_3->draw(2,"write").'</div><div class="clear"></div><br />
<center><button class="main">Разослать рассылку</button></center>
</form></div>
';
}
?>