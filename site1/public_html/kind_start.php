<?
$itsthemainpage=true;

$nouserdata=false;

if($_SESSION["user_id"]!='') {
	$b=getuser($_SESSION["user_id"]);
	if($b["phone2"]=='' || $b["fio"]=='' || $b["city"]==0 || $b["birth"]=='0000-00-00') {
		$nouserdata=true;
	}
}
else {
	$nouserdata=true;
}

if($_SESSION["user_id"]!='') {
	$content2.='<nav data_height="350" data_width="670">';

	$content2.='<div class="tile_logged_section1">';

	$content2.='<li class="tile_logged blue"><a href="'.$server_absolute_path_info.'users/'.$_SESSION["user_sid"].'/"><div class="avatar_photo"><img src="';

	$a=getuser($_SESSION["user_id"]);
	if($a["photo"]=='' || !file_exists($server_inner_path.$uploads[4]['path'].$a["photo"])) {
		$content2.=$server_absolute_path.'identicon.php?hash='.md5(md5($a["em"]).'cetb').'&size=200';
	}
	else {
		$content2.=$server_absolute_path.$uploads[4]['path'].$a["photo"];
	}
	$content2.='" class="avatar"></div>
<div class="text">Привет,<br><u>';
	if($a["nick"]!='') {
		$content2.=$a["nick"];
	}
	elseif($a["fio"]!='') {
		if(strlen($a["fio"])>=21) {
			$fio=explode(' ',$a["fio"]);
			$content2.=$fio[0].' '.$fio[1];
		}
		else {
			$content2.=$a["fio"];
		}
	}
	else {
		$content2.='ИНП '.$a["sid"];
	}
	$content2.='</u></div></a></li>';

	$content2.='<li class="tile_logout blue"><a href="?action=logout"><div class="text">Выйти</div></a></li>';

	$content2.='</div>';

	$content2.='<div class="tile_logged_section2">';

	$counter=0;
	$result=mysql_query("SELECT DISTINCT t1.status, t1.vacancy, t1.money, t1.locat, t1.id, t1.site_id, t1.allinfo, t1.sorter, t2.title, t2.path, t1.changed, t1.player_id FROM ".$prefix."roles t1 LEFT JOIN ".$prefix."sites t2 ON t2.id=t1.site_id WHERE ((t1.player_id=".$_SESSION['user_id']." and t2.status!=3) or (t1.new_player_sid=".$_SESSION["user_sid"]." and t1.new_player_deny!=1) or (t1.player_id=".$_SESSION['user_id']." and t1.id in (SELECT role_id FROM ".$prefix."rolescomments WHERE id not in (select comment_id from ".$prefix."rolescommentsread where user_id=".$_SESSION["user_id"].") and type!=2))) and t1.todelete!=1 and t2.status!=3 order by t1.id desc");
	while($a = mysql_fetch_array($result)) {
		$rolewaschanged='';
		$newcomments='';

		$rolef_c=virtual_structure("SELECT * from ".$prefix."rolefields where site_id=".$a["site_id"]." order by rolecode","allinfo","role");

		if($a["changed"]!=$_SESSION["user_id"]) {
			$result3=mysql_query("SELECT * FROM ".$prefix."roleshistory WHERE role_id=".$a["id"]." and initiator_id=".$_SESSION["user_id"]." order by date desc limit 0,1");
			$c = mysql_fetch_array($result3);
			if($c["date"]=='') {
				$c["date"]=0;
			}
			else {
				$myallinfo=unmakevirtual($c["allinfo"]);
			}
			$result4=mysql_query("SELECT * FROM ".$prefix."roles WHERE id=".$a["id"]);
		}
		$d = mysql_fetch_array($result4);
		if($d["allinfo"]!='') {
			$allinfomaster=unmakevirtual($d["allinfo"]);

			foreach($rolef_c as $f=>$v) {
				if($myallinfo[$v["name"]]!=$allinfomaster[$v["name"]] && $v["read"]==10) {
					$rolewaschanged=usname(getuser($d["changed"]),true).' '.date("d.m.Y в G:i",$d["date"]);
					break;
				}
			}
			$n=false;
			if($rolewaschanged=='') {
				if($c["status"]!=$d["status"]) {
					$n=true;
				}
				elseif($c["vacancy"]!=$d["vacancy"]) {
					$n=true;
				}
				elseif($c["money"]!=$d["money"]) {
					$n=true;
				}
				elseif($c["locat"]!=$d["locat"]) {
					$n=true;
				}
				if($n) {
					$rolewaschanged=usname(getuser($d["changed"]),true).' '.date("d.m.Y в G:i",$d["date"]);
				}
			}
		}

		$result4=mysql_query("SELECT user_id,date FROM ".$prefix."rolescomments WHERE id NOT IN (SELECT comment_id from ".$prefix."rolescommentsread where user_id=".$_SESSION["user_id"].") and type!=2 and site_id=".$a["site_id"]." and role_id=".$a["id"]);
		$d = mysql_fetch_array($result4);
		if($d["user_id"]>0) {
			$newcomments=usname(getuser($d["user_id"]),true).' '.date("d.m.Y в G:i",$d["date"]);
		}

		if($rolewaschanged!='' || $newcomments!='') {
			$counter++;
			$myorders2.='<a href="'.$server_absolute_path.'order/'.$a["id"].'/" title="';
			if($rolewaschanged!='') {
				$myorders2.='изменена: '.$rolewaschanged.'; ';
			}
			if($newcomments!='') {
				$myorders2.='комментарии: '.$newcomments.'; ';
			}
			$myorders2.='проект: '.decode($a["title"]).'; ';
			$myorders2.='статус: ';
			if($a["status"]==1) {
				$myorders2.='подана';
			}
			elseif($a["status"]==2) {
				$myorders2.='обсуждается';
			}
			elseif($a["status"]==3) {
				$myorders2.='принята';
			}
			elseif($a["status"]==4) {
				$myorders2.='отклонена';
			}
			$myorders2.='">';
			if(str_replace(' ','',decode($a["sorter"]))!='') {
				$myorders2.=decode($a["sorter"]);
			}
			else {
				$result615=mysql_query("SELECT * from ".$prefix."users where id=".$a["player_id"]);
				$t=mysql_fetch_array($result615);
				$myorders2.='<font color="red">('.usname($t,true).')</font>';
			}
			$myorders2.='</a><br>';
		}
	}
	if($counter>0) {
		$myorders2='<a href="'.$server_absolute_path.'order/"><h3>Все мои заявки на игры</h3></a><a href="'.$server_absolute_path.'order/">Перейти</a><br><br><h3>Измененные заявки</h3>'.$myorders2;
	}
	$content2.='<li class="tile_myorders blue"><a '.($counter>0?'onClick="$(\'.tile_myorders\').css(\'display\',\'none\');$(\'.tile_myorders2\').css(\'display\',\'block\');"':'href="'.$server_absolute_path.'order/order/page=0&sorting=10"').'><div class="counter" title="измененные"><div>'.$counter.'</div></div><div class="text">Мои заявки</div></a></li>';
	if($counter>0) {
		$content2.='<li class="tile_myorders2 blue"><div><div>'.$myorders2.'</div></div></li>';
	}

	$counter=0;

	$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."allrights2 WHERE user_id=".$_SESSION['user_sid']." and (rights=1 OR rights=2)");
	$a = mysql_fetch_array($result);
	if($a[0]>0 || $_SESSION["admin"]) {
		function getlocatchild($locat,$level,$site) {
			global
				$prefix,
				$locatpermit,
				$locatcheck;

	   		$result3=mysql_query("SELECT * FROM ".$prefix."roleslocat WHERE site_id=".$site." and id=".$locat);
			$c = mysql_fetch_array($result3);
			if(stripos($locatcheck,'-'.$c["id"].'-')===false) {
	        	$locatpermit[]=Array($c["id"],decode($c["name"]),$level);
		        $locatcheck.=$c["id"].'-';
		        $result3=mysql_query("SELECT * FROM ".$prefix."roleslocat WHERE site_id=".$site." and parent=".$locat);
				while($c = mysql_fetch_array($result3)) {
					getlocatchild($c["id"],$level+1,$site);
				}
			}
		}

		$time_change=array();
		$mysiteorders2_changes=array();

		if($_SESSION["admin"] && $_SESSION["seeall"]) {
			$result=mysql_query("SELECT * FROM ".$prefix."sites WHERE status!=3 ORDER BY title");
		}
		else {
			$result=mysql_query("SELECT s.* FROM ".$prefix."sites s LEFT JOIN ".$prefix."allrights2 a2 ON a2.site_id=s.id WHERE (a2.rights=1 OR a2.rights=2) AND a2.user_id=".$_SESSION["user_sid"]." AND s.status!=3 ORDER BY title");
		}
		while($a = mysql_fetch_array($result)) {

			if(!preg_match('#'.decode($a["title"]).'#',$mysitesorders2)) {
				$mysitesorders2.='<p title="Перейти на список заявок"><a href="'.$server_absolute_path_site.'orders/site='.$a["id"].'">'.decode($a["title"]).'</a></p>';
			}
		}
	}

	$mysitesorders2='<a href="'.$server_absolute_path_site.'"><h3 title="Перейти на список проектов">Полный список проектов</h3></a>'.$mysitesorders2;

	$content2.='<li class="tile_mysitesorders black"><a onClick="$(\'.tile_mysitesorders\').css(\'display\',\'none\');$(\'.tile_mysitesorders2\').css(\'display\',\'block\');"><div class="text">Заявки на мои проекты</div></a></li>';
	$content2.='<li class="tile_mysitesorders2 black"><div><div>'.$mysitesorders2.'</div></div></li>';

	$content2.='</div>';

	$content2.='<div class="tile_logged_section3">';

	$content2.='<li class="tile_calendar blue"><a href="'.$server_absolute_path_calendar.'"><div class="text">Ролевой календарь</div></a></li>';

	$content2.='</div>';

	$content2.='<div class="tile_logged_section4">';

	$content2.='<li class="tile_support blue"><a href="'.$server_absolute_path.'help/"><div class="text">Поддержка</div></a></li>';
	$content2.='<li class="tile_info black"><a href="'.$server_absolute_path_info.'"><div class="text">Инфотека</div></a></li>';

	$content2.='</div>';

	$content2.='<div class="tile_logged_section5">';

	$counter=0;
	$result=mysql_query("SELECT COUNT(id) from ".$prefix."blog_pms where user_id=".$_SESSION["user_id"]." and pmread='0'");
	$a=mysql_fetch_array($result);
	$counter=$a[0];

	$content2.='<li class="tile_messages black"><a href="'.$server_absolute_path.'inbox/"><div class="counter" title="новые"><div>'.$counter.'</div></div><div class="text">Сообщения</div></a></li>';
	$content2.='<li class="tile_neworders blue"><a href="'.$server_absolute_path_site.'hosting2/"><div class="text">Создать<br>систему<br>заявок</div></a></li>';
	$content2.='<li class="tile_neworder black"><a href="'.$server_absolute_path.'order/order/act=add"><div class="text">Подать заявку</div></a></li>';

	$content2.='</div>';

	$content2.='<div class="clear"></div>';
}
else {
	$content2.='<nav data_height="230" data_width="670">';

	$content2.='<div class="tile_notlogged_section1">';

	$content2.='<li class="tile_enter blue" id="tile_enter"><a><div class="text">Войти</div>
<div id="login_choice">
<form action="'.$curdir.'" method="post" enctype="multipart/form-data" id="login_form">
<input type="hidden" name="object" value="'.$object.'">
<input type="hidden" name="id" value="'.$id.'">
<input type="hidden" name="subobj" value="'.$subobj.'">
<input type="hidden" name="action" value="login">';
	if($redirectobj!='') {
		$content2.='<input type="hidden" name="redirectobj" value="'.$redirectobj.'">';
	}
	if($redirectid>0) {
		$content2.='<input type="hidden" name="redirectid" value="'.$redirectid.'">';
	}
	if($redirectparams!='') {
		$content2.='<input type="hidden" name="redirectparams" value="'.$redirectparams.'">';
	}
	$content2.='
<input type="text" name="login" id="login" placehold="Логин" tabindex="1"><br>
<input type="password" name="pass" id="pass" placehold="Пароль" tabindex="2"><br>
<button class="main" id="btn_login" tabindex="3">Войти</button><button class="nonimportant" id="btn_remind">Забыл</button>
</form>
</div>
<div id="login_remind">
<form action="'.$server_absolute_path.'/" method="post" enctype="multipart/form-data" id="remind_form">
<input type="hidden" name="action" value="remind">
<input type="text" id="em" name="em" placehold="Ваш е-mail"><br>
<button class="main" id="btn_make_remind">восстановить</button>
</form>
</div>
</a></li>';
	$content2.='<li class="tile_register black"><a href="'.$server_absolute_path.'register/"><div class="text">Регистрация</div></a></li>';

	$content2.='</div>';

	$content2.='<div class="tile_notlogged_section2">';

	$content2.='<li class="tile_neworder_notlogged black"><a href="'.$server_absolute_path.'order/order/act=add"><div class="text">Подать заявку</div></a></li>';
	$content2.='<li class="tile_neworders_notlogged blue"><a href="'.$server_absolute_path_site.'hosting2/"><div class="text">Создать<br>систему<br>заявок</div></a></li>';

	$content2.='</div>';

	$content2.='<div class="tile_notlogged_section3">';

	$content2.='<li class="tile_calendar blue"><a href="'.$server_absolute_path_calendar.'"><div class="text">Ролевой календарь</div></a></li>';

	$content2.='</div>';

	$content2.='<div class="tile_notlogged_section4">';

	$content2.='<li class="tile_support black"><a href="'.$server_absolute_path.'help/"><div class="text">Поддержка</div></a></li>';
	$content2.='<li class="tile_info blue"><a href="'.$server_absolute_path_info.'"><div class="text">Инфотека</div></a></li>';

	$content2.='</div>';
}

$content2.='</nav>';

if($action=='remind')
{
	require_once($server_inner_path.$direct."/classes/base_mails.php");

	$em=encode($_POST["em"]);
	$result=mysql_query("SELECT * FROM ".$prefix."users WHERE em='".$em."'");
	$a = mysql_fetch_array($result);
	if($em!='' && $a["id"]!='') {
		$pass='';
		$salt = "abcdefghijklmnopqrstuvwxyz123456789";
		srand((double)microtime()*1000000);
		$i = 0;
		while ($i <= 7) {
			$num = rand() % 35;
			$tmp = substr($salt, $num, 1);
			$pass .= $tmp;
			$i++;
		}
		mysql_query("UPDATE ".$prefix."users SET pass='".md5($pass)."' where id=".$a["id"]);

		$myname="allrpg.info";
		$myemail="project@allrpg.info";
		$contactemail=$em;

		$message=decode($a["fio"]).',
Вы запросили восстановление пароля на сайте allrpg.info.
Ваш логин: '.decode($a["login"]).'
Ваш новый пароль: '.$pass;
		$subject='Изменение данных на сайте allrpg.info';

		if($contactemail!='') {
			if(send_mail($myname, $myemail, $contactemail, $subject, $message)) {
				dynamic_err_one('success',"На ваш e-mail отправлено письмо с новым паролем.");
			}
			else {
				dynamic_err_one('error',"При отправке письма на сервере возникли проблемы.");
			}
		}
	}
	else {
		dynamic_err_one('error',"Указанного e-mail'а в базе не обнаружено.");
	}
}
?>