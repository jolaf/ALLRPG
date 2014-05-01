<?php
function getlocatnotifications($locat) {
	global
		$prefix,
		$subobj;

	if($locat>0) {
		$list.=" OR notifications LIKE '%-".$locat."-%'";

		$result3=mysql_query("SELECT parent FROM ".$prefix."roleslocat WHERE site_id=".$subobj." and id=".$locat);
		$c = mysql_fetch_array($result3);
		if($c["parent"]>0) {
			$list.=getlocatnotifications($c["parent"]);
		}
	}
	return $list;
}

if($_SESSION["user_id"]!="") {
	$result2=mysql_query("SELECT * FROM ".$prefix."users where id=".$_SESSION["user_id"]);
	$b = mysql_fetch_array($result2);
	if($b["phone2"]=='' || $b["fio"]=='' || $b["city"]==0 || $b["birth"]=='0000-00-00') {
		redirect($server_absolute_path.'profile/redirectobj=order&redirectid='.$subobj);
	}
}
elseif($act!="add") {
	redirect($server_absolute_path.'register/redirectobj=order&redirectid='.$subobj);
}
elseif($act=="add" && $subobj!='') {
	redirect($server_absolute_path.'register/redirectobj=order&redirectid='.$subobj);
}
if($_SESSION["user_id"]!="" || $act=="add") {
	//мои заявки

	if(($id=='' || $id==0) && $act=="view") {
		$act='';
	}

	$pagetitle=h1line('Мои заявки',$curdir.$kind.'/');

	$history=encode($_GET["history"]);

	if(encode($_POST["site_id"])!='') {
		$subobj=encode($_POST["site_id"]);
	}

	if(encode($_GET["roletype"])!='') {
		$roletype=encode($_GET["roletype"]);
	}
	else {
		$roletype='0';
	}

	if(encode($_POST["team"])!='') {
		$roletype=encode($_POST["team"]);
	}

	if($id!='' && $action!="dynamicaction" && $actiontype!="delete") {
		$result=mysql_query("SELECT * FROM ".$prefix."roles where id=".$id);
		$a = mysql_fetch_array($result);
		$subobj=$a["site_id"];
		if($a["todelete2"]==1) {
			err_red("Мастера удалили данную заявку.");
		}
	}

	if($subobj!='') {
		$result=mysql_query("SELECT * FROM ".$prefix."sites where status!=3 and id=".$subobj);
		$a = mysql_fetch_array($result);
		if($a["id"]!='') {
			$siteisopen=true;
		}
        if($a["oneorderfromplayer"]=='1') {
        	$oneorderfromplayer=true;
        }
		$result=mysql_query("SELECT * FROM ".$prefix."sites where status2=2 and testing!='1' and id=".$subobj);
		$a = mysql_fetch_array($result);
		if($a["id"]!='' || $_SESSION["admin"]) {
			$lettomakeorder=true;
		}
		$result=mysql_query("SELECT * FROM ".$prefix."sites where id=".$subobj);
		$a = mysql_fetch_array($result);
		$sitetitle=decode($a["title"]);
	}
	else {
		$siteisopen=true;
	}

	if($id!='') {
		$lettomakeorder=true;
	}

	if($id!='' && $history!=1 && $act=="" && $action=="orderdeny" && $siteisopen) {
		$result6=mysql_query("SELECT * FROM ".$prefix."roles where id=".$id);
		$e = mysql_fetch_array($result6);
		if($e["new_player_sid"]==$_SESSION["user_sid"]) {
			mysql_query("UPDATE ".$prefix."roles set new_player_deny=1 WHERE id=".$id);
			err("Вы отказались от заявки.");
		}
	}

	if($subobj!='' && $actiontype=='') {
		$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."rolevacancy WHERE site_id=".$a["id"]);
		$b = mysql_fetch_array($result2);

		$result3=mysql_query("SELECT COUNT(id) FROM ".$prefix."roles WHERE site_id=".$a["id"]);
		$c = mysql_fetch_array($result3);

		if($roletype==0) {
			$result4=mysql_query("SELECT COUNT(id) FROM ".$prefix."rolefields where team='1' and site_id=".$a["id"]);
			$d = mysql_fetch_array($result4);
		}
		else {
			$result4=mysql_query("SELECT COUNT(id) FROM ".$prefix."rolefields where team='0' and site_id=".$a["id"]);
			$d = mysql_fetch_array($result4);
		}

		if($a["path"]!='' || $a["path2"]!='') {
			if($a["path"]!='') {
				$additional_commands.='<a href="http://'.decode($a["path"]).'.allrpg.info">';
			}
			elseif(decode($a["path2"])!='') {
   				if(stripos(decode($a["path2"]),'http://')===false && stripos('www.',decode($a["path2"]))===false) {
   					$additional_commands.='<a href="http://'.decode($a["path2"]).'">';
   				}
   				else {
   					$additional_commands.='<a href="'.decode($a["path2"]).'">';
   				}
   			}
			$additional_commands.='Сайт проекта</a><br>';
		}

		if($b[0]>0) {
			$additional_commands.='<a href="'.$server_absolute_path.'siteroles/'.$a["id"].'/">Сетка ролей</a><br>';
		}
		if($c[0]>0) {
			if($a["alter_rolefield"]>0) {
				$additional_commands.='<a href="'.$server_absolute_path.'siteroles2/'.$a["id"].'/orders=1">Поданные заявки</a><br>';
			}
			else {
				$additional_commands.='<a href="'.$server_absolute_path.'siteroles/'.$a["id"].'/orders=1">Поданные заявки</a><br>';
			}
		}
		if($d[0]>0 && $act=="add") {
			if($roletype==0) {
				$additional_commands.='<a href="'.$server_absolute_path.'order/act=add&subobj='.$a["id"].'&roletype=1">Подать командную заявку</a><br>';
			}
			else {
				$additional_commands.='<a href="'.$server_absolute_path.'order/act=add&subobj='.$a["id"].'&roletype=0">Подать индивидуальную заявку</a><br>';
			}
		}
	}
	if($act=="view" && $id!='' && ($actiontype=='' || $trouble) && $history!=1) {
		$result6=mysql_query("SELECT * FROM ".$prefix."roles where id=".$id);
		$e = mysql_fetch_array($result6);
		if($action=="orderaccept" && $e["new_player_sid"]==$_SESSION["user_sid"] && $siteisopen) {
			mysql_query("UPDATE ".$prefix."roles set player_id=".$_SESSION["user_id"].", new_player_sid=0, new_player_deny=0, todelete=0 WHERE id=".$id);
			err("Вы приняли на себя управление заявкой.");
		}

        $obj_html.='<div class="cb_editor">';

		if($e["new_player_sid"]==$_SESSION["user_sid"] && $e["new_player_deny"]!=1 && $action!="orderaccept" && $action!="orderdeny" && $siteisopen) {
			err_info("Мастера проекта «".$sitetitle."» предлагают вам взять на себя управление данной заявкой.");
			$obj_html.='<button class="main" onClick="if (confirm(\'Вы уверены, что хотите взять на себя данную заявку?\')) {document.location=\''.$server_absolute_path.$kind.'/'.$object.'/'.$id.'/act=view&subobj='.$subobj.'&action=orderaccept\';}">Принять заявку</button><br><button class="nonimportant" onClick="if (confirm(\'Вы уверены, что хотите отказаться от управления данной заявкой?\')) {document.location=\''.$server_absolute_path.$kind.'/'.$object.'/'.$id.'/act=view&subobj='.$subobj.'&action=orderdeny\';}">Отказаться от заявки</button><br>';
		}
		elseif($e["player_id"]==$_SESSION["user_id"] && $e["todelete"]!=1) {
			$additional_commands.='<a href onClick="if (confirm(\'Прежде чем перейти к истории изменений заявки, не забудьте сохранить изменения! Перейти?\')) {document.location=\''.$server_absolute_path.$kind.'/'.$object.'/'.$id.'/act=view&subobj='.$subobj.'&history=1\';}">История изменений заявки</a><br>';

			if($action=="comment_add" && $siteisopen) {
				$comment_content=encode($_POST["content"]);
				if($comment_content!='') {
					$result=mysql_query("SELECT * FROM ".$prefix."rolescomments WHERE role_id=".$id." and user_id=".$_SESSION['user_id']." and content='".$comment_content."' and date>=".(time()-3600));
					$a = mysql_fetch_array($result);
					if($a["id"]!='') {
						dynamic_err_one('error',"Заблокировано повторное сохранение комментария.");
					}
					else {
						mysql_query("INSERT into ".$prefix."rolescomments (site_id, role_id, user_id, type, content, date) values (".$subobj.", ".$id.", ".$_SESSION["user_id"].", 3, '".$comment_content."', ".time().")");
						$comment_id=mysql_insert_id($link);
						mysql_query("INSERT into ".$prefix."rolescommentsread (role_id, user_id, comment_id, date) values (".$id.", ".$_SESSION["user_id"].", ".$comment_id.", ".time().")");

						require_once($server_inner_path.$direct."/classes/base_mails.php");
                        $result=mysql_query("SELECT * from ".$prefix."roles where id=".$id." and site_id=".$subobj);
						$a=mysql_fetch_array($result);
						$result6=mysql_query("SELECT * from ".$prefix."users where id=".$_SESSION['user_id']);
						$e=mysql_fetch_array($result6);
						$result5=mysql_query("SELECT * from ".$prefix."sites where id=".$a["site_id"]);
						$d=mysql_fetch_array($result5);

						$myname=usname($e, true);
						$myemail=decode($e["em"]);
						$subject='Комментарий к заявке «'.decode($a["sorter"]).'» проекта «'.decode($d["title"]).'»';
						$message='Добрый день.
Заявку «'.decode($a["sorter"]).'» откомментировал игрок «'.$myname.'».
Ссылка: '.$server_absolute_path_site.'orders/'.$id.'/site='.$a["site_id"].' (вы должны быть залогинены на allrpg.info).

'.decode($comment_content).'

Отказаться от получения уведомлений о новых комментариях Вы можете здесь: '.$server_absolute_path_site.'orders/site='.$a["site_id"].'&action=signtocomments_off (вы должны быть залогинены на allrpg.info).';

						$result2=mysql_query("SELECT * FROM ".$prefix."allrights2 WHERE site_id=".$a["site_id"]." AND (rights=1 OR rights=2) AND (locations='-' OR locations='' OR locations LIKE '%-0-%' OR locations LIKE '%-".$a["locat"]."-%') AND (notifications IS NULL OR notifications='-' OR notifications='' OR notifications LIKE '%-0-%'".getlocatnotifications($a["locat"]).") AND signtocomments='1'");
						while($b=mysql_fetch_array($result2)) {
							$c=getuser_sid($b["user_id"]);
							$contactemail=decode($c["em"]);
							send_mail($myname, $myemail, $contactemail, $subject, $message);
						}
						dynamic_err(array('success',"Комментарий успешно добавлен, мастерам отправлено e-mail уведомление."),'stayhere');
					}
					$comment_content='';
				}
				else {
					dynamic_err_one('error',"Неверно заполнен текст комментария.",array('content'));
				}
			}

			$result=mysql_query("SELECT * FROM ".$prefix."rolescomments WHERE role_id=".$id." and type!=2 and id in (select comment_id from ".$prefix."rolescommentsread where role_id=".$id." and user_id=".$_SESSION["user_id"].") limit 0,1");
			$a = mysql_fetch_array($result);

			$obj_html.='<div class="cb_editor"><h1 class="data_h1">Комментарии '.($siteisopen?'[<a onClick="$(\'#comment_add\').toggle();$(this).html($(this).html()==\'добавить\'?\'скрыть\':\'добавить\');">добавить</a>]':'').($a["id"]!=''?'[<a onClick="$(\'#all_comments\').toggle();$(this).html($(this).html()==\'показать скрытые\'?\'убрать скрытые\':\'показать скрытые\');$(document).scrollTop($(\'#all_comments\').offset().top);">показать скрытые</a>]':'').'</h1>';

			if($siteisopen) {
				$comment_1=createElem(Array(
					'name'	=>	'content',
					'sname'	=>	"Текст комментария",
					'type'	=>	"textarea",
					'read'	=>	10,
					'write'	=>	10,
					'default'	=>	$comment_content,
					'rows'	=>	8,
					'mustbe'	=>	true,
					)
				);

				$obj_html.='
<div id="comment_add"';
				if(!$comment_trouble) {
					$obj_html.='style="display: none"';
				}
				$obj_html.='>
<form action="'.$curdir.$kind.'/" method="post" enctype="multipart/form-data">
<input type="hidden" name="subobj" value="'.$subobj.'">
<input type="hidden" name="object" value="'.$object.'">
<input type="hidden" name="action" value="comment_add">
<input type="hidden" name="id" value="'.$id.'">
<input type="hidden" name="act" value="'.$act.'">
<br>
<div class="fieldname" id="name_content">Текст комментария</div>
<div class="fieldvalue" id="div_content">'.$comment_1->draw(2,"write").'</div><br>
<center><button class="main">Добавить</button></center>
</form><br></div>';
			}

			$obj_html.='<div id="new_comments">';
			$result=mysql_query("SELECT * FROM ".$prefix."rolescomments WHERE role_id=".$id." and type!=2 and id not in (select comment_id from ".$prefix."rolescommentsread where role_id=".$id." and user_id=".$_SESSION["user_id"].") order by date desc");
			while($a = mysql_fetch_array($result)) {
				$obj_html.='<div class="';
				$result2=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$a["user_id"]);
				$b = mysql_fetch_array($result2);
				if($a["type"]==3) {
					$obj_html.='comm_master"><b>Вы</b>';
				}
				else {
					$obj_html.='comm_player"><b>Мастер</b> '.usname($b,true,true);
				}
				$obj_html.=' в <b>'.date("G:i d.m.Y",$a["date"]).'</b> написал';
				if($a["type"]==3) {
					$obj_html.='и';
				}
				elseif($b["gender"]==2) {
					$obj_html.='а';
				}
				$obj_html.=':<br>
'.decode2($a["content"]).'</div><hr>';
				mysql_query("INSERT into ".$prefix."rolescommentsread (role_id, user_id, comment_id, date) values (".$id.", ".$_SESSION["user_id"].", ".$a["id"].", ".time().")");
			}
			$obj_html.='</div>';
			$obj_html.='<div id="all_comments" style="display: none">';
			$result=mysql_query("SELECT * FROM ".$prefix."rolescomments WHERE role_id=".$id." and type!=2 and id in (select comment_id from ".$prefix."rolescommentsread where role_id=".$id." and user_id=".$_SESSION["user_id"].") order by date desc");
			while($a = mysql_fetch_array($result)) {
				$result2=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$a["user_id"]);
				$b = mysql_fetch_array($result2);
				$obj_html.='<div class="';
				if($a["type"]==3) {
					$obj_html.='comm_master"><b>Вы</b>';
				}
				else {
					$obj_html.='comm_player"><b>Мастер</b> '.usname($b,true,true);
				}
				$obj_html.=' в <b>'.date("G:i d.m.Y",$a["date"]).'</b> написал';
				if($a["type"]==3) {
					$obj_html.='и';
				}
				elseif($b["gender"]==2) {
					$obj_html.='а';
				}
				$obj_html.=':<br>
'.decode2($a["content"]).'</div><hr>';
			}
		}
		$obj_html.='</div><br></div>';
	}
	elseif($act=="view" && $id!='' && $history==1) {
		$prev=0;
		$next=0;
		$start=encode($_GET["start"]);
		if($start==0)
		{
			$result=mysql_query("SELECT * FROM ".$prefix."roleshistory WHERE role_id=".$id." order by date desc limit 0,2");
			$a = mysql_fetch_array($result);
			if($a["id"]!='')
			{
				$start=$a["id"];
			}
			$a = mysql_fetch_array($result);
			if($a["id"]!='')
			{
				$next=$a["id"];
			}
		}
		else
		{
			$result=mysql_query("SELECT * FROM ".$prefix."roleshistory WHERE role_id=".$id." order by date desc");
			while($a = mysql_fetch_array($result))
			{
				if($start==$a["id"])
				{
					$a = mysql_fetch_array($result);
					if($a["id"]!='')
					{
						$next=$a["id"];
					}
					break;
				}
				$prev=$a["id"];
			}
		}

		$control='<button href="'.$server_absolute_path.$kind.'/'.$object.'/'.$id.'/act=view&subobj='.$subobj.'">Вернуться к заявке</button>';
		if($prev!='')
		{
			$control.='<button href="'.$server_absolute_path.$kind.'/'.$object.'/'.$id.'/act=view&subobj='.$subobj.'&history=1&start='.$prev.'">Следующее изменение</button>';
		}
		if($next!='')
		{
			$control.='<button href="'.$server_absolute_path.$kind.'/'.$object.'/'.$id.'/act=view&subobj='.$subobj.'&history=1&start='.$next.'">Предыдущее изменение</button>';
		}

		$result3=mysql_query("SELECT * FROM ".$prefix."roles WHERE id=".$id);
		$c = mysql_fetch_array($result3);
		$result2=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$c["changed"]);
		$b = mysql_fetch_array($result2);
		$obj_html.='<center>'.$control.'</center><br><table width="100%" border=0><tr valign=top><td width="50%"><center><b>Сохранено:</b><br>
'.usname($b, true, true).'<br>
'.date("d.m.Y",$c["date"]).' в '.date("G:i",$c["date"]).'</b></center><br>';
	}

	if($act=="add" && $subobj=='') {
		// выбор из доступных игр для подачи заявки

		$result=mysql_query("SELECT * FROM ".$prefix."sites where status2=2 and testing!='1' and datefinish>='".date("Y-m-d")."' order by title asc");
		while($a = mysql_fetch_array($result))
		{
			if($_SESSION["user_id"]!='') {
				$result2=mysql_query("SELECT * FROM ".$prefix."users where id=".$_SESSION["user_id"]);
				$b = mysql_fetch_array($result2);
				if($b["phone2"]=='' || $b["fio"]=='' || $b["city"]==0 || $b["birth"]=='0000-00-00') {
					$include='<a href="'.$server_absolute_path.'profile/redirectobj=order&redirectid=';
					$include2='&redirectparams=roletype:';
				}
				else {
					$include='<a href="'.$server_absolute_path.'order/act=add&subobj=';
					$include2='&roletype=';
				}
			}
			else {
				$include='<a href="'.$server_absolute_path.'register/redirectobj=order&redirectid=';
				$include2='&redirectparams=roletype:';
			}

			$obj_html.='<center><div style="background-color: #d3d3d3; padding: 3px; margin-bottom: 10px;">';
			$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."rolefields where team='0' and site_id=".$a["id"]);
			$b = mysql_fetch_array($result2);
			if($b[0]>0) {
				$obj_html.='<h1>'.$include.$a["id"].$include2.'0">'.$a["title"].'</a></h1>';
			}
			else {
				$obj_html.='<h1>'.$a["title"].'</h1>';
			}
			if($a["datestart"]>0)
			{
				$obj_html.=' ('.datesfmake($a["datestart"],$a["datefinish"],false).')';
			}
			$obj_html.='</div>
<div style="margin-bottom: 20px;">';
			$beforemenu=false;
			$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."rolevacancy WHERE site_id=".$a["id"]);
			$b = mysql_fetch_array($result2);
			if($b[0]>0) {
				$obj_html.='<a href="'.$server_absolute_path.'siteroles/'.$a["id"].'/">Сетка ролей</a>';
				$beforemenu=true;
			}
			$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."rolefields where team='0' and site_id=".$a["id"]);
			$b = mysql_fetch_array($result2);
			if($b[0]>0) {
				if($beforemenu) {
					$obj_html.=' &#149; ';
				}
				$obj_html.=$include.$a["id"].$include2.'0">Подать индивидуальную заявку</a>';
				$beforemenu=true;
			}
			$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."rolefields where team='1' and site_id=".$a["id"]);
			$b = mysql_fetch_array($result2);
			if($b[0]>0) {
				if($beforemenu) {
					$obj_html.=' &#149; ';
				}
				$obj_html.=$include.$a["id"].$include2.'1">Подать командную заявку</a>';
				$beforemenu=true;
			}
			$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."roles WHERE site_id=".$a["id"]);
			$b = mysql_fetch_array($result2);
			if($b[0]>0) {
				if($beforemenu) {
					$obj_html.=' &#149; ';
				}
				$obj_html.='<a href="'.$server_absolute_path.'siteroles/'.$a["id"].'/orders=1">Поданные заявки</a>';
			}
			$obj_html.='</div></center>
';
		}
	}
	elseif($lettomakeorder || $act=='') {
		// Создание объекта

		$result=mysql_query("SELECT * FROM ".$prefix."users where id in (SELECT changed FROM ".$prefix."roles where player_id=".$_SESSION["user_id"].")");
		while($a = mysql_fetch_array($result))
		{
			$allusers[]=Array($a["id"],usname($a,true,true));
			$allusers2[]=Array($a["id"],usname2($a,true));
		}
		if($allusers[0][0]=='') {
        	$result=mysql_query("SELECT * FROM ".$prefix."users where id=".$_SESSION["user_id"]);
			$a = mysql_fetch_array($result);
			$allusers[]=Array($a["id"],usname($a,true,true));
			$allusers2[]=Array($a["id"],usname2($a,true));
		}
		foreach ($allusers2 as $key => $row)
		{
			$allusers2_sort[$key]  = strtolower($row[1]);
		}
		array_multisort($allusers2_sort, SORT_ASC, $allusers2);

		$obj=new netObj(
			'order',
			$prefix."roles",
			"заявку",
			Array("Заявка успешно подана.","Заявка изменена.","Заявка удалена."),
			Array(
				'0'	=>	Array(
					Array("status", "ASC", true, true, Array(2,Array(Array('1','подана'),Array('2','обсуждается'),Array('3','принята'),Array('4','отклонена')))),
					Array("site_id", "ASC", true, true, Array(3,$prefix."sites","id","title")),
					Array("sorter", "ASC", true, true),
					Array("changed", "ASC", true, true, Array(2,$allusers2)),
					Array("date", "ASC", true, true),
				),
			),
			2,
			'',
			50,
			'allinfo'
		);
		
		$obj -> setDefaultSort ('date', 'desc');

		// Создание схемы прав объекта
		if(encode($_GET["history"])!=1)
		{
			if(($subobj=='' || ($subobj!='' && $act=="view" && $actiontype=="change" && !$trouble) || ($subobj!='' && $actiontype=="add" && !$trouble))) {
				$result=mysql_query("SELECT id FROM ".$prefix."sites WHERE id IN (SELECT site_id FROM ".$prefix."roles WHERE player_id=".$_SESSION["user_id"].")");
				while($a = mysql_fetch_array($result)) {
					$mask_orders.=$a["id"].', ';
				}
				if($mask_orders!='') {
					$mask_orders=substr($mask_orders,0,strlen($mask_orders)-2);
					if($subobj!='' && $actiontype=="add" && !$trouble) {
						$mask_orders.=', '.$subobj;
					}
					$mask_orders='site_id in ('.$mask_orders.') and ';
				}
				elseif($subobj!='' && $actiontype=="add" && !$trouble) {
					$mask_orders='site_id='.$subobj.' and ';
				}
			}

			if($oneorderfromplayer) {
				$result3=mysql_query("SELECT * FROM ".$prefix."roles WHERE player_id=".$_SESSION["user_id"]." AND site_id=".$subobj." AND todelete2!='1'");
				$c=mysql_fetch_array($result3);
				if($c["id"]!='' && $act=="add" && $subobj!='') {
					$siteisopen=false;
					$content2.='<center><br><b>Мастера отключили возможность подачи более одной заявки одним пользователем на данный проект.</b><br><br></center>';
					//err_info("Мастера отключили возможность подачи более одной заявки одним пользователем на данный проект.");
				}
			}

			if($_SESSION["user_id"]!='') {
				$obj_r=new netRight(
					true,
					$siteisopen,
					$siteisopen,
					true,
					10,
					$mask_orders.'todelete!=1 and player_id='.$_SESSION["user_id"].' or new_player_sid='.$_SESSION["user_sid"].' and new_player_deny!=1',
					'todelete!=1 and player_id='.$_SESSION["user_id"],
					'todelete!=1 and player_id='.$_SESSION["user_id"]
				);
				$obj->setRight($obj_r);
			}

			if($id!='') {
				$result=mysql_query("SELECT * FROM ".$prefix."roles WHERE id=".$id);
				$a = mysql_fetch_array($result);
				if($a["new_player_sid"]==$_SESSION["user_sid"] && $a["new_player_deny"]!=1) {
					$obj_r->setRights(1);
					$obj_r->setViewRestrict('new_player_sid='.$_SESSION["user_sid"].' and new_player_deny!=1');
					$obj_r->setAdd(false);
					$obj_r->setChange(false);
					$obj_r->setDelete(false);
				}
			}
		}
		else {
			if($_SESSION["user_id"]!='') {
				$obj_r=new netRight(
					true,
					false,
					false,
					false,
					10,
					'todelete!=1 and player_id='.$_SESSION["user_id"],
					'todelete!=1 and player_id='.$_SESSION["user_id"],
					'todelete!=1 and player_id='.$_SESSION["user_id"]
				);
				$obj->setRight($obj_r);
			}
		}

		// Создание полей объекта

		if($id!='') {
			$result=mysql_query("SELECT * FROM ".$prefix."roles WHERE id=".$id);
			$a = mysql_fetch_array($result);
			$roletype=$a["team"];
		}

		$rolefields=virtual_structure("SELECT * FROM ".$prefix."rolefields WHERE site_id=".$subobj." AND team='".$roletype."' ORDER BY rolecode","allinfo","role");

		function getlocatparents($locat) {
			global
				$prefix,
				$subobj;

			$list=array();
			$list[]=$locat;
			$result3=mysql_query("SELECT parent FROM ".$prefix."roleslocat WHERE site_id=".$subobj." and id=".$locat);
			$c = mysql_fetch_array($result3);
			if($c["parent"]>0) {
				$list=array_merge($list,getlocatparents($c["parent"]));
			}
			return $list;
		}

		function locatpath($id,$thislocat) {
			global
				$prefix,
				$subobj;

			$result=mysql_query("SELECT * FROM ".$prefix."roleslocat WHERE id=".$id." AND site_id=".$subobj);
			$a=mysql_fetch_array($result);
			if($a["id"]!='') {
				if($a["parent"]==0) {
					$return=' ('.decode($a["name"]);
				}
				else {
					$return=locatpath($a["parent"]);
					$return.=' –» '.decode($a["name"]);
				}
				if($thislocat) {
					$return.=')';
				}
			}
			else {
				$return=' (локация не указана)';
			}
			return($return);
		}

		$vacancy=Array();
		$result2=mysql_query("SELECT * from ".$prefix."rolevacancy where site_id=".$subobj." and team='".$roletype."' ORDER by name asc, code asc");
		while($b=mysql_fetch_array($result2))
		{
			$result3=mysql_query("SELECT COUNT(id) from ".$prefix."roles where site_id=".$subobj." AND status=3 AND vacancy=".$b["id"]);
			$c=mysql_fetch_array($result3);
			if($c[0]<$b["kolvo"])
			{
				$vacancy[]=Array($b["id"],$b["name"].locatpath($b["locat"],true));
			}
			elseif($a["vacancy"]==$b["id"])
			{
				$vacancy[]=Array($b["id"],$b["name"].locatpath($b["locat"],true));
			}
		}

			$mainfields[]=Array(
					'sname'	=>	"Основные поля",
					'type'	=>	"h1",
					'read'	=>	1,
					'write'	=>	100000,
			);

		if($history!=1) {
			$mainfields[]=Array(
					'name'	=>	"site_id",
					'sname'	=>	"Проект",
					'type'	=>	"select",
					'values'	=>	make5field($prefix."sites","id","title"),
					'read'	=>	1,
					'write'	=>	100000,
			);

			$mainfields[]=Array(
					'name'	=>	"team",
					'sname'	=>	"Тип",
					'type'	=>	"select",
					'values'	=>	Array(Array('0','индивидуальная'),Array('1','командная')),
					'default'	=>	$roletype,
					'read'	=>	1,
					'write'	=>	100000,
			);

			$mainfields[]=Array(
					'name'	=>	"sorter",
					'sname'	=>	"Название",
					'type'	=>	"text",
					'read'	=>	1000,
					'write'	=>	100000,
			);
		}

			$mainfields[]=Array(
					'name'	=>	"money",
					'sname'	=>	"Взнос",
					'type'	=>	"text",
					'default'	=>	decode($a["money"]),
					'read'	=>	1,
					'write'	=>	100000,
			);

			$mainfields[]=Array(
					'name'	=>	"moneydone",
					'sname'	=>	"Взнос сдан",
					'type'	=>	"checkbox",
					'default'	=>	decode($a["moneydone"]),
					'read'	=>	1,
					'write'	=>	100000,
			);

			$result2=mysql_query("SELECT COUNT(id) from ".$prefix."roleslocat where site_id=".$subobj);
			$b=mysql_fetch_array($result2);

			if(!isset($vacancy[0]) && $b[0]>0) {
				$locwrite=10;
			}
			else {
				$locwrite=100000;
			}
			$mainfields[]=Array(
					'name'	=>	"locat",
					'sname'	=>	"Локация / команда",
					'type'	=>	"select",
					'values'	=>	make5fieldtree(false,$prefix."roleslocat","parent",0," AND site_id=".$subobj,"name asc",0,"id","name",1000000),
					'read'	=>	1,
					'write'	=>	$locwrite,
			);

			$mainfields[]=Array(
					'name'	=>	"status",
					'sname'	=>	"Статус",
					'type'	=>	"select",
					'values'	=>	Array(Array('1','подана'),Array('2','обсуждается'),Array('3','принята'),Array('4','отклонена')),
					'read'	=>	1,
					'write'	=>	100000,
			);

		if($history!=1) {
			$mainfields[]=Array(
					'name'	=>	"changed",
					'sname'	=>	"Последнее изменение",
					'type'	=>	"select",
					'values'	=>	$allusers,
					'read'	=>	1,
					'write'	=>	100000,
			);
		}

			$mainfields[]=Array(
					'name'	=>	"vacancy",
					'sname'	=>	"Заявка на роль",
					'type'	=>	"select",
					'values'	=>	$vacancy,
					'default'	=>	encode($_GET["wantrole"]),
					'read'	=>	1,
					'write'	=>	10,
			);

		if($history!=1) {
			if($roletype==1) {
				if(encode($_GET["wantrole"])!='') {
					$result2=mysql_query("SELECT * from ".$prefix."rolevacancy where site_id=".$subobj." and id=".encode($_GET["wantrole"]));
					$b=mysql_fetch_array($result2);
				}

				$mainfields[]=Array(
						'name'	=>	"roleteamkolvo",
						'sname'	=>	"Количество людей в команде",
						'type'	=>	"number",
						'default'	=>	$b["teamkolvo"],
						'read'	=>	1,
						'write'	=>	10,
						'mustbe'	=>	true,
				);
			}

			$mainfields[]=Array(
					'name'	=>	"site_id",
					'sname'	=>	"id сайта",
					'type'	=>	"hidden",
					'default'	=>	$subobj,
					'read'	=>	1,
					'write'	=>	10,
					'mustbe'	=>	true
			);

			$mainfields[]=Array(
					'name'	=>	"team",
					'sname'	=>	"Тип",
					'type'	=>	"hidden",
					'default'	=>	$roletype,
					'read'	=>	1,
					'write'	=>	10,
			);

			$mainfields[]=Array(
					'name'	=>	"date",
					'sname'	=>	"Последнее изменение",
					'type'	=>	"timestamp",
					'read'	=>	1,
					'write'	=>	10,
					'mustbe'	=>	true
			);
		}

		for($i=0;$i<count($mainfields);$i++) {
			$objer='obj_'.($i);
			$$objer=createElem($mainfields[$i]);
			if($history==1 || $print==1) {
				$$objer->setHelp('');
			}
			$obj->setElem($$objer);
		}

		if($id!='') {
			$result=mysql_query("SELECT * from ".$prefix."roles where id=".$id." and site_id=".$subobj);
			$a=mysql_fetch_array($result);
			if($a["vacancy"]!=0) {
				$result3=mysql_query("SELECT * from ".$prefix."roleslinks where (roles LIKE '%-all".$a["vacancy"]."-%' OR roles LIKE '%-".$a["id"]."-%') and content!='' and site_id=".$subobj." and notready!='1' order by date ASC");
				while($c=mysql_fetch_array($result3)) {
					if(strpos($c["roles"],'-'.$id.'-')!==false || ($a["status"]==3 && strpos($c["roles"],'-all'.$a["vacancy"].'-')!==false)) {
						$alllinks.='<b>';
						$alllinks.='Про ';

						if($c["hideother"]=='0') {
							unset($roles2);
							$roles2=substr($c["roles2"],1,strlen($c["roles2"])-2);
							$roles2=explode('-',$roles2);
							foreach($roles2 as $r) {
								$vac=0;
								if(strpos($r,'all')===false) {
									$result2=mysql_query("SELECT * FROM ".$prefix."roles WHERE site_id=".$subobj." and id=".$r);
									$b=mysql_fetch_array($result2);
									$vac=$b["vacancy"];
								}
								else {
									$vac=str_replace('all','',$r);
								}
								$result2=mysql_query("SELECT * FROM ".$prefix."rolevacancy WHERE site_id=".$subobj." and id=".$vac);
								$b=mysql_fetch_array($result2);
								if($b["name"]!='') {
									if(strpos($r,'all')!==false) {
										$result2=mysql_query("SELECT player_id,sorter FROM ".$prefix."roles WHERE site_id=".$subobj." and vacancy=".$vac);
									}
									else {
										$result2=mysql_query("SELECT player_id,sorter FROM ".$prefix."roles WHERE site_id=".$subobj." and vacancy=".$vac);
									}
									if(mysql_affected_rows($link)>0) {
										while($b=mysql_fetch_array($result2)) {
	                                    	$result6=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$b["player_id"]);
	                                    	$f=mysql_fetch_array($result6);
	                                    	$alllinks.='«'.decode($b["sorter"]).'» ('.usname($f,true,true).'), ';
										}
									}
									else {
                                    	$alllinks.='«'.$b["name"].'», ';
									}
								}
								elseif($r==0) {
									$alllinks.='<i>глобальный сюжет</i>, ';
								}
								else {
									$alllinks.='<i>удаленную роль</i>, ';
								}
							}
							$alllinks=substr($alllinks,0,strlen($alllinks)-2);
						}
						else {
							$alllinks.='<i>скрыто</i>';
						}
						$alllinks.='</b><br>';
						$alllinks.=decode($c["content"]);
						$alllinks.='<br><br>';
					}
				}
				$alllinks=substr($alllinks,0,strlen($alllinks)-8);
			}
		}
		if($history!=1 && $alllinks!='') {
			$rolefields[]=Array(
					'sname'	=>	"Загрузы",
					'type'	=>	"h1",
					'read'	=>	10,
					'write'	=>	100000,
			);
			$rolefields[]=Array(
					'name'	=>	"alllinks",
					'sname'	=>	"Полный список загрузов",
					'type'	=>	"wysiwyg",
					'default'	=>	$alllinks,
					'read'	=>	10,
					'write'	=>	100000,
			);
		}

		$dynamic_fields_shown=array();
		$full_locats_tree_new=array();
		if(encode_to_cp1251($_REQUEST["vacancy"])!='' && encode_to_cp1251($_REQUEST["vacancy"])!=$a["vacancy"]) {
			$result3=mysql_query("SELECT * FROM ".$prefix."rolevacancy WHERE site_id=".$subobj." AND id=".encode_to_cp1251($_REQUEST["vacancy"]));
			$c=mysql_fetch_array($result3);
			if($a["locat"]!=$c["locat"]) {
				$full_locats_tree_new=getlocatparents($c["locat"]);
			}
		}
		elseif(encode_to_cp1251($_REQUEST["locat"])!=$a["locat"] && encode_to_cp1251($_REQUEST["vacancy"])=='') {
			$full_locats_tree_new=getlocatparents(encode_to_cp1251($_REQUEST["locat"]));
		}
		$full_locats_tree=getlocatparents($a["locat"]);
		for($i=0;$i<count($rolefields);$i++) {
			$showrolefield=true;
			$result3=mysql_query("SELECT * FROM ".$prefix."rolefields where id=".preg_replace('#virtual#','',$rolefields[$i]["name"]));
			$c = mysql_fetch_array($result3);
			if($act=="add" && $c["hidefieldinadd"]=='1' && $c["rolemustbe"]!='1') {
				$showrolefield=false;
			}
			elseif(str_replace('-','',$c["roleparent"])!='') {
				$showrolefield=false;

				if($act!="add") {
					unset($matches);
					preg_match_all('#-(\d+):(\d+)#',$c["roleparent"],$matches);
					foreach($matches[1] as $key=>$value) {
						if(preg_match('#\[virtual'.$value.'\]\['.$matches[2][$key].'\]#',$a["allinfo"]) || preg_match('#\[virtual'.$value.'\]\[[^\]]*-'.$matches[2][$key].'-[^\]]*\]#',$a["allinfo"])) {
							if(encode_to_cp1251($_REQUEST["virtual".$value])!=$matches[2][$key] || encode_to_cp1251($_REQUEST["virtual".$value][$matches[2][$key]])!='on') {
								$dynamic_fields_shown[]=$c["id"];
							}
							$showrolefield=true;
							break;
						}
						if(encode_to_cp1251($_REQUEST["virtual".$value])==$matches[2][$key] || encode_to_cp1251($_REQUEST["virtual".$value][$matches[2][$key]])=='on') {
							$dynamic_fields_shown[]=$c["id"];
						}
					}
					unset($matches);
					preg_match_all('#-locat:(\d+)#',$c["roleparent"],$matches);
					foreach($matches[1] as $key=>$value) {
						if(in_array($value,$full_locats_tree_new)) {
							$dynamic_fields_shown[]=$c["id"];
						}
						if(in_array($value,$full_locats_tree)) {
							if(!in_array($value,$full_locats_tree_new) && count($full_locats_tree_new)>0) {
								$dynamic_fields_shown[]=$c["id"];
							}
							if($act!="add") {
								$showrolefield=true;
								break;
							}
						}
					}
				}
			}
			if($showrolefield) {
				$objer='obj_'.($i+count($mainfields));
				$$objer=createElem($rolefields[$i]);
				if($$objer->getRead()==10) {
					$$objer->setRead(1);
				}
				if(encode($_GET["history"])==1) {
					$$objer->setHelp('');
				}
				$$objer->setWidth('');
				$obj->setElem($$objer);
			}
		}

		// Исполнение dynamicaction, если необходимо
		if($action=="dynamicaction")
		{
			require_once($server_inner_path.$direct."/dynamicaction.php");
			require_once($server_inner_path.$direct."/classes/base_mails.php");
			if($object=="order") {
				$result=mysql_query("SELECT * from ".$prefix."roles where id=".$id." and site_id=".$subobj);
				$a_id=mysql_fetch_array($result);

				if($actiontype=="change") {
					$c=unmakevirtual($a_id["allinfo"]);
					foreach($rolefields as $f=>$v) {
						if($v["write"]>$obj_r->getRights() && $v["type"]!='h1') {
							$allinf.='['.$v["name"].']['.encode($c[$v["name"]]).']&lt;br&gt;';
						}
					}

					if($a_id["changed"]!=$_SESSION["user_id"])
					{
						$result2=mysql_query("SELECT * from ".$prefix."roleshistory where role_id=".$id." AND initiator_id=".$a_id["changed"]);
						$b=mysql_fetch_array($result2);
						if($b["date"]!='') {
							mysql_query("UPDATE ".$prefix."roleshistory SET allinfo='".$a_id["allinfo"]."', vacancy=".$a_id["vacancy"].", money='".$a_id["money"]."', moneydone='".$a_id["moneydone"]."', locat=".$a_id["locat"].", status=".$a_id["status"].", todelete=".$a_id["todelete"].", alltold='".$a_id["alltold"]."', date=".$a_id["date"]." WHERE id=".$b["id"]);
						}
						else {
							mysql_query("INSERT into ".$prefix."roleshistory (allinfo, vacancy, money, moneydone, locat, status, todelete, alltold, date, role_id, initiator_id) VALUES ('".$a_id["allinfo"]."', ".$a_id["vacancy"].", '".$a_id["money"]."', '".$a_id["moneydone"]."', ".$a_id["locat"].", ".$a_id["status"].", ".$a_id["todelete"].", '".$a_id["alltold"]."', ".$a_id["date"].", ".$id.", ".$a_id["changed"].")");
						}
					}
				}
				if($actiontype!="delete") {
					function set_locat_to_vac() {
						global
							$_REQUEST,
							$_SESSION,
							$prefix,
							$id,
							$vacname,
							$a_id,
							$subobj;

						if(encode($_REQUEST["vacancy"])!='') {
							$result=mysql_query("SELECT * from ".$prefix."rolevacancy where site_id=".$subobj." and id=".encode($_REQUEST["vacancy"]));
							$a=mysql_fetch_array($result);
							if($a["locat"]>0 && ($a["locat"]!=$a_id["locat"])) {
								mysql_query("UPDATE ".$prefix."roles SET locat=".$a["locat"]." WHERE id=".$id);
								err('Локация в заявке установлена в соответствии с ролью в сетке ролей.');
								$vacname=$a["name"];
								return true;
							}
							return false;
						}
					}

					if($actiontype=="add") {
						function dynamic_add_success() {
							global
								$prefix,
								$_REQUEST,
								$_SESSION,
								$id,
								$server_absolute_path_site,
								$server_inner_path,
								$direct,
								$subobj,
								$mainfields,
								$rolefields,
								$vacname,
								$dynamic_fields_shown,
								$a_id,
								$roletype;

							// правим название заявки в соответствие с названием роли, если в заявке названия нет
							$vac_changed=set_locat_to_vac();
							$result=mysql_query("SELECT * FROM ".$prefix."roles WHERE id=".$id);
							$a_id=mysql_fetch_array($result);
							$result=mysql_query("SELECT * FROM ".$prefix."sites WHERE id=".$subobj);
							$site=mysql_fetch_array($result);
							if($roletype==1) {
								$sorter=encode_to_cp1251($_REQUEST["virtual".$site["sorter2"]]);
							}
							else {
								$sorter=encode_to_cp1251($_REQUEST["virtual".$site["sorter"]]);
							}
							if($sorter=='') {
								$sorter=$vacname;
							}
							mysql_query("UPDATE ".$prefix."roles SET sorter='".$sorter."', changed=".$_SESSION['user_id'].", status=1, money='".$site["money"]."', player_id=".$_SESSION['user_id'].", todelete=0, datesent='".$a_id["date"]."' WHERE id=".$id);

							// отправляем уведомления мастерам
							require_once($server_inner_path.$direct."/classes/base_mails.php");
							$user=getuser($_SESSION["user_id"]);

							$myname=usname($user);
							$myemail=decode($user["em"]);
							$subject='Игроком '.$myname.' подана заявка «'.$sorter.'» на проект «'.decode($site["title"]).'»';
							$message='От игрока '.$myname.' (ИНП: ' . $user['sid'] . ') на Ваш проект «'.decode($site["title"]).'» поступила заявка «'.$sorter.'».
Перейти к заявке: '.$server_absolute_path_site.'orders/'.$id.'/site='.$site["id"].'
Отказаться от получения уведомлений о новых заявках Вы можете здесь: '.$server_absolute_path_site.'orders/site='.$site["id"].'&action=signtonew_off (вы должны быть залогинены на allrpg.info).

';
							$old=unmakevirtual($a_id['allinfo']);
							$old=array_merge($a_id,$old);
							$mailfields=array_merge($mainfields,$rolefields);

							foreach($mailfields as $f=>$v)
							{
								$can="read";
								if($v["name"]!="allinfo" && $v["name"]!="sorter" && $v["name"]!="site_id" && $v["name"]!="team" && $v["name"]!="date" && $v["name"]!="money" && $v["name"]!="moneydone" && $v["name"]!="changed") {
									$obj_n=createElem($v);
									$obj_n->setVal($old);
									if(($obj_n->getVal()!='' && $v["type"]!="select") || $obj_n->getType()=="h1" || ($obj_n->getVal()>0 && $v["type"]=="select")) {
										if($obj_n->getType()=="h1") {
											$message.=strtoupper($v["sname"]).'

';
										}
										else {
											$message.=$v["sname"].':
';
										}
										$message.=$obj_n->draw(1,$can);
										if($v["type"]!='' && $v["type"]!='h1'&& $v["type"]!="hidden" && $v["type"]!="timestamp")
										{
											if($can!='')
											{
												$message.='

';
											}
										}
									}
								}
							}
							$message=str_replace(array('<br>','<br />'),'
',$message);
							$result=mysql_query("SELECT * FROM ".$prefix."allrights2 WHERE site_id=".$site["id"]." AND (rights=1 OR rights=2) AND (locations='-' OR locations='' OR locations LIKE '%-0-%' OR locations LIKE '%-".$a_id["locat"]."-%') AND (notifications IS NULL OR notifications='-' OR notifications='' OR notifications LIKE '%-0-%'".getlocatnotifications($a_id["locat"]).") AND signtonew='1'");
							while($a=mysql_fetch_array($result))
							{
								$d=getuser_sid($a["user_id"]);
								$contactemail=decode($d["em"]);
								send_mail($myname, $myemail, $contactemail, $subject, $message);
							}
						}

						if($site["oneorderfromplayer"]=='1') {
							$result=mysql_query("SELECT * FROM ".$prefix."roles WHERE player_id=".$_SESSION["user_id"]." AND site_id=".$site["id"]." AND todelete2!=1");
							$a=mysql_fetch_array($result);
							if($a["id"]!='') {
								dynamic_err_one('error','Мастера закрыли возможность подачи более одной заявки одним игроком на проект.');
							}
							else {
								dynamicaction($obj);
							}
						}
						else {
							dynamicaction($obj);
						}
					}
					else {
						function dynamic_save_success() {
							global
								$prefix,
								$_REQUEST,
								$id,
								$subobj,
								$server_absolute_path_site,
								$_SESSION,
								$server_inner_path,
								$direct,
								$rolefields,
								$obj_r,
								$vacname,
								$allinf,
								$dynamic_fields_shown,
								$a_id,
								$roletype;

							$vac_changed=set_locat_to_vac();

							// сохраняем историю изменений
							$result=mysql_query("SELECT * FROM ".$prefix."roles WHERE id=".$id." AND site_id=".$subobj);
							$a_id=mysql_fetch_array($result);
							$result=mysql_query("SELECT * FROM ".$prefix."sites WHERE id=".$a_id["site_id"]);
							$site=mysql_fetch_array($result);

							$allinf.=$a_id["allinfo"];
							mysql_query("UPDATE ".$prefix."roles SET allinfo='".$allinf."' WHERE id=".$id." AND site_id=".$subobj);

							$result2=mysql_query("SELECT * FROM ".$prefix."roleshistory WHERE role_id=".$id." ORDER BY date DESC LIMIT 0,1");
							$b=mysql_fetch_array($result2);

							$c=unmakevirtual($allinf);
							$d=unmakevirtual($b["allinfo"]);
							$sendchange=false;
							foreach($rolefields as $f=>$v) {
								if($v["write"]<=10)
								{
									$can="read";
									if($v["name"]!="allinfo" && $v["name"]!="sorter" && $v["name"]!="site_id" && $v["name"]!="team" && $v["name"]!="date" && $v["name"]!="money" && $v["name"]!="moneydone" && $v["name"]!="changed") {
										$obj_n=createElem($v);
										$obj_n->setVal($c);
										if(($obj_n->getVal()!='' && $v["type"]!="select") || $obj_n->getType()=="h1" || ($obj_n->getVal()>0 && $v["type"]=="select")) {
											if($obj_n->getType()=="h1") {
												$message2.=strtoupper($v["sname"]).'

';
											}
											else {
												$message2.=$v["sname"];
												if($v["write"]<=$obj_r->getRights() && $c[$v["name"]]!=$d[$v["name"]]) {
													$sendchange=true;
													$message2.=' (изменено)';
												}
												$message2.=':
';
											}
											$message2.=$obj_n->draw(1,$can);
											if($v["type"]!='' && $v["type"]!='h1'&& $v["type"]!="hidden" && $v["type"]!="timestamp")
											{
												if($can!='')
												{
													$message2.='

';
												}
											}
										}
									}
								}
							}

							// отправляем e-mail об изменениях мастерам
							if($sendchange && $a["todelete2"]!=1) {
								require_once($server_inner_path.$direct."/classes/base_mails.php");

								$e=getuser($_SESSION['user_id']);

								$myname=usname($e, true);
								$myemail=decode($e["em"]);
								$subject='Игроком изменена заявка «'.decode($a_id["sorter"]).'» проекта «'.decode($site["title"]).'»';
								$message='Добрый день.
Заявка «'.decode($a_id["sorter"]).'» была изменена игроком «'.$myname.'»  ИНП: ' . $user['sid'] . ' .
Ссылка: '.$server_absolute_path_site.'orders/'.$id.'/site='.$site["id"].' (вы должны быть залогинены на allrpg.info).
Отказаться от получения уведомлений об изменениях заявок Вы можете здесь: '.$server_absolute_path_site.'orders/site='.$site["id"].'&action=signtochange_off (вы должны быть залогинены на allrpg.info).

'.$message2;
								$message=str_replace(array('<br>','<br />'),'
',$message);
								$result2=mysql_query("SELECT * FROM ".$prefix."allrights2 WHERE site_id=".$subobj." AND (rights=1 OR rights=2) AND (locations='-' OR locations='' OR locations LIKE '%-0-%' OR locations LIKE '%-".$a_id["locat"]."-%') AND (notifications IS NULL OR notifications='-' OR notifications='' OR notifications LIKE '%-0-%'".getlocatnotifications($a_id["locat"]).") AND signtochange='1'");
								while($b=mysql_fetch_array($result2)) {
									$c=getuser_sid($b["user_id"]);

									$contactemail=decode($c["em"]);

									send_mail($myname, $myemail, $contactemail, $subject, $message);
								}
							}

							mysql_query("UPDATE ".$prefix."roles SET changed=".$_SESSION['user_id']." WHERE id=".$id);

							$result=mysql_query("SELECT * FROM ".$prefix."roles WHERE id=".$id);
							$a_id=mysql_fetch_array($result);
							if($roletype==1) {
								$sorter=encode_to_cp1251($_REQUEST["virtual".$site["sorter2"]]);
							}
							else {
								$sorter=encode_to_cp1251($_REQUEST["virtual".$site["sorter"]]);
							}
							if($sorter=='') {
								$sorter=$vacname;
							}
							mysql_query("UPDATE ".$prefix."roles SET sorter='".$sorter."' WHERE id=".$id);

							if($vac_changed) {
								if(count($dynamic_fields_shown)>0) {
									dynamic_err(array(array('success','Заявка успешно изменена.'), array('success','Изменен набор полей.')),'stayhere');
								}
								else {
									dynamic_err(array(array('success','Заявка успешно изменена.')),'stayhere');
								}
							}
							elseif(count($dynamic_fields_shown)>0) {
								dynamic_err(array(array('success','Заявка успешно изменена.'), array('success','Изменен набор полей.')),'stayhere');
							}
						}

						dynamicaction($obj);
					}
				}
				else {
					$result=mysql_query("SELECT * FROM ".$prefix."roles WHERE id=".$id." AND player_id=".$_SESSION["user_id"]);
					$a=mysql_fetch_array($result);
					if($a["id"]!='') {
						if($a["todelete2"]==0) {
							mysql_query("UPDATE ".$prefix."roles set todelete=1 where id=".$id);

							$result6=mysql_query("SELECT * from ".$prefix."users where id=".$_SESSION['user_id']);
							$e=mysql_fetch_array($result6);
							$result5=mysql_query("SELECT * from ".$prefix."sites where id=".$a["site_id"]);
							$d=mysql_fetch_array($result5);

							$myname=usname($e, true);
							$myemail=decode($e["em"]);
							$subject='Игроком удалена заявка «'.decode($a["sorter"]).'» проекта «'.decode($d["title"]).'»';
							$message='Добрый день.
Заявка «'.decode($a["sorter"]).'» была удалена игроком «'.$myname.'»  ИНП: ' . $user['sid'] . ' .
Вам необходимо либо подтвердить ее окончательно удаление, либо передать ее другому игроку.
Ссылка: '.$server_absolute_path_site.'orders/'.$id.'/site='.$a["site_id"].' (вы должны быть залогинены на allrpg.info).
Отказаться от получения уведомлений об изменениях заявок Вы можете здесь: '.$server_absolute_path_site.'orders/site='.$a["site_id"].'&action=signtochange_off (вы должны быть залогинены на allrpg.info).';

							$result2=mysql_query("SELECT * FROM ".$prefix."allrights2 WHERE site_id=".$a["site_id"]." AND (rights=1 OR rights=2) AND (locations='-' OR locations='' OR locations LIKE '%-0-%' OR locations LIKE '%-".$a["locat"]."-%') AND (notifications IS NULL OR notifications='-' OR notifications='' OR notifications LIKE '%-0-%'".getlocatnotifications($a["locat"]).") AND signtochange='1'");
							while($b=mysql_fetch_array($result2)) {
								$result3=mysql_query("SELECT * from ".$prefix."users where sid=".$b["user_id"]);
								$c=mysql_fetch_array($result3);

								$contactemail=decode($c["em"]);

								send_mail($myname, $myemail, $contactemail, $subject, $message);
							}
							redirect_construct();
							dynamic_err(array(array('success',"Заявка удалена, мастерам отправлено e-mail оповещение.")),$redirect_path);
						}
						else {
							dynamicaction($obj);
						}
					}
				}
			}
		}

		// Добавление параметра values к select'ам и multiselect'ам.

		// Инициализация элементов поиска, если нужен.

		// Отрисовка всего объекта html'ем в переменную
		$obj_html.=$obj->draw();

		if($act=="add" && $subobj!='') {
			//$obj_html=str_replace('Добавить заявку</span><input type="submit" onSubmit="this.disabled=true" /></span></span>','Подать заявку</span><input type="submit" onClick="if (confirm(\'Информация о Вас, введенная в профиле пользователя, станет доступна мастерам данной игры. Также Вам будут приходить автоматические оповещения об изменениях и комментариях, оставленных мастерами к Вашей заявке. Вы согласны?\')) {return true;} else {return false;}" /></span></span>',$obj_html);
		}

		if($act=="view" && $id!='' && $history==1) {
			$obj_html.='</td><td width="50%">';

			$result=mysql_query("SELECT * FROM ".$prefix."roleshistory WHERE id=".$start);
			$a = mysql_fetch_array($result);
			$b=unmakevirtual($a['allinfo']);
			$b=array_merge($a,$b);

			$result3=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$a["initiator_id"]);
			$c = mysql_fetch_array($result3);

			if($a["id"]!='')
			{
				$obj_html.='<center><b>Сохранено:</b><br>
'.usname($c, true, true).'<br>
'.date("d.m.Y",$a["date"]).' в '.date("G:i",$a["date"]).'</center><br>
<center><div class="cb_editor">';

				$result=mysql_query("SELECT * FROM ".$prefix."roles WHERE id=".$id." and site_id=".$subobj);
				$a = mysql_fetch_array($result);
				$old=unmakevirtual($a['allinfo']);
				$old=array_merge($a,$old);
				$rolefields=array_merge($mainfields,$rolefields);

				foreach($rolefields as $f=>$v)
				{
					if($v["read"]<=10)
					{
						$can="read";
						if($v["name"]!="allinfo") {
							$obj_n=createElem($v);
							$obj_n->setVal($b);
							if(($obj_n->getVal()!='' && $v["type"]!="select") || $obj_n->getType()=="h1" || ($obj_n->getVal()>0 && $v["type"]=="select")) {
								if($obj_n->getType()=="h1") {
									$obj_html.='<h1 class="data_h1">'.$v["sname"].'</h1>';
								}
								else {
									$obj_html.='<div class="fieldname">'.$v["sname"].'</div><div class="fieldvalue read">';
								}
								if($b[$v["name"]]!=$old[$v["name"]]) {
									$obj_html.='<font color="red">';
								}
								$obj_html.=$obj_n->draw(1,$can);
								if($b[$v["name"]]!=$old[$v["name"]]) {
									$obj_html.='</font>';
								}
								if($obj_n->getType()!="h1") {
									$obj_html.='</div><div class="clear"></div><br>';
								}
							}
						}
					}
				}
			}
			$obj_html.='</div></center></td></tr></table>';
		}
	}
	else {
		$obj_html.='<center>Подача заявок на данный проект закрыта.</center>';
	}

	// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
	$content2.=$obj_html;
	$content2=str_replace('<div class="fieldname" id="name_alllinks">Полный список загрузов</div>','',$content2);
}
?>