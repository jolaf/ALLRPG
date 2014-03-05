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
<div class="text">ПРИВЕТ,<br><u>';
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

	$content2.='<li class="tile_logout blue"><a href="?action=logout"><div class="text">ВЫЙТИ</div></a></li>';

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
		$myorders2='<a href="'.$server_absolute_path.'order/"><h3>ВСЕ МОИ ЗАЯВКИ</h3></a><a href="'.$server_absolute_path.'order/">Перейти</a><br><br><h3>ИЗМЕНЕННЫЕ ЗАЯВКИ</h3>'.$myorders2;
	}
	$content2.='<li class="tile_myorders blue"><a '.($counter>0?'onClick="$(\'.tile_myorders\').css(\'display\',\'none\');$(\'.tile_myorders2\').css(\'display\',\'block\');"':'href="'.$server_absolute_path.'order/order/page=0&sorting=10"').'><div class="counter" title="измененные"><div>'.$counter.'</div></div><div class="text">МОИ ЗАЯВКИ</div></a></li>';
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
			unset($time_change);
			$time_change=array();

			unset($mysiteorders2_changes);
			$mysiteorders2_changes=array();

			if(!preg_match('#'.decode($a["title"]).'#',$mysitesorders2)) {
				if($mysitesorders2!='') {
					$mysitesorders2.='<br>';
				}
				$mysitesorders2.='<a href="'.$server_absolute_path_site.'orders/site='.$a["id"].'"><h3>'.decode($a["title"]).'</h3></a>';
			}
			$no_changes_on_site=true;

			$locatpermit=Array();
			$locatrestrict='';
			$locatcheck='-';

			$result2=mysql_query("SELECT * FROM ".$prefix."allrights2 WHERE user_id=".$_SESSION["user_sid"]." and site_id=".$a["id"]." and (rights=1 OR rights=2)");
			while($b = mysql_fetch_array($result2)) {
		        if($b["locations"]!='' && $b["locations"]!='-' && $b["locations"]!='--' && stripos($b["locations"],'-0-')===false) {
		            $result3=mysql_query("SELECT * FROM ".$prefix."roleslocat WHERE site_id=".$a["id"]." order by parent asc, code asc, name asc");
					while($c = mysql_fetch_array($result3)) {
						if(stripos($b["locations"],'-'.$c["id"].'-')!==false) {
							getlocatchild($c["id"],0,$a["id"]);
						}
					}
				}
				else {
					unset($locatpermit);
					break;
				}
			}
			if(isset($locatpermit[0])) {
				$locatrestrict=' and locat IN ('.implode(',',$locatpermit).') ';
			}

   			$rolef_c=virtual_structure("SELECT * FROM ".$prefix."rolefields WHERE site_id=".$a["id"]." ORDER BY rolecode","allinfo","role");

			$result2=mysql_query("SELECT * FROM ".$prefix."roles WHERE site_id=".$a["id"]." AND todelete2!=1 ".$locatrestrict."ORDER BY sorter ASC");
			while($b = mysql_fetch_array($result2)) {
				$time_change_this=0;
				$rolewaschangedbyplayer='';
				$newcommentsbyplayer='';
				$rolewaschangedbymaster='';
				$newcommentsbymaster='';
				unset($myallinfo);
				unset($allinfoplayer);
				unset($allinfomaster);

				if($b["changed"]!=$_SESSION["user_id"]) {
					$result3=mysql_query("SELECT * FROM ".$prefix."roleshistory WHERE role_id=".$b["id"]." AND initiator_id=".$_SESSION["user_id"]." ORDER BY date DESC LIMIT 0,1");
					$c = mysql_fetch_array($result3);
					$myallinfo=unmakevirtual($c["allinfo"]);

					if($b["changed"]==$b["player_id"]) {
						$allinfoplayer=unmakevirtual($b["allinfo"]);
					}
					else {
						$allinfomaster=unmakevirtual($b["allinfo"]);
					}

					if($allinfoplayer!='' || $allinfomaster!='') {
						if($c["id"]!='') {
							if($allinfoplayer!='') {
								foreach($rolef_c as $f=>$v) {
									if($allinfoplayer[$v["name"]]!=$myallinfo[$v["name"]]) {
										if($b["changed"]==$b["player_id"]) {
											$rolewaschangedbyplayer=date("d.m.Y в G:i",$b["date"]);
											$time_change_this=$b["date"];
										}
										else {
											$rolewaschangedbyplayer=date("d.m.Y в G:i",$c["date"]);
											$time_change_this=$c["date"];
										}
										break;
									}
								}
							}
							elseif($allinfomaster!='') {
								foreach($rolef_c as $f=>$v) {
									if($allinfomaster[$v["name"]]!=$myallinfo[$v["name"]]) {
										$rolewaschangedbymaster=usname(getuser($b["changed"]),true).' '.date("d.m.Y в G:i",$b["date"]);
										$time_change_this=$b["date"];
										break;
									}
								}
							}
							if($rolewaschangedbyplayer=='' && $rolewaschangedbymaster=='') {
								$n=false;
								if($c["status"]!=$b["status"]) {
									$n=true;
								}
								elseif($c["vacancy"]!=$b["vacancy"]) {
									$n=true;
								}
								elseif($c["money"]!=$b["money"]) {
									$n=true;
								}
								elseif($c["moneydone"]!=$b["moneydone"]) {
									$n=true;
								}
								elseif($c["locat"]!=$b["locat"]) {
									$n=true;
								}
								elseif($c["todelete"]!=$b["todelete"]) {
									$n=true;
								}
								elseif($c["alltold"]!=$b["alltold"]) {
									$n=true;
								}
								if($n) {
									if($allinfoplayer!='') {
										$rolewaschangedbyplayer=date("d.m.Y в G:i",$b["date"]);
										if($b["date"]>$time_change_this) {
											$time_change_this=$b["date"];
										}
									}
									elseif($allinfomaster!='') {
										$rolewaschangedbymaster=usname(getuser($b["changed"]),true).' '.date("d.m.Y в G:i",$d["date"]);
										if($d["date"]>$time_change_this) {
											$time_change_this=$d["date"];
										}
									}
								}
							}
						}
						else {
							if($allinfomaster!='') {
								$rolewaschangedbymaster=usname2(getuser($b["changed"]),true).' '.date("d.m.Y в G:i",$b["date"]);
								if($b["date"]>$time_change_this) {
									$time_change_this=$b["date"];
								}
							}
							elseif($allinfoplayer!='') {
								$rolewaschangedbyplayer=date("d.m.Y в G:i",$b["date"]);
								if($b["date"]>$time_change_this) {
									$time_change_this=$b["date"];
								}
							}
						}
					}
				}

				$result4=mysql_query("SELECT user_id,date FROM ".$prefix."rolescomments WHERE id NOT IN (SELECT comment_id from ".$prefix."rolescommentsread where user_id=".$_SESSION["user_id"].") and site_id=".$a["id"]." and role_id=".$b["id"]);
				while($d = mysql_fetch_array($result4)) {
					if($d["user_id"]==$b["player_id"]) {
						$newcommentsbyplayer=date("d.m.Y в G:i",$d["date"]);
						if($d["date"]>$time_change_this) {
							$time_change_this=$d["date"];
						}
					}
					else {
						$newcommentsbymaster=usname2(getuser($d["user_id"]),true).' '.date("d.m.Y в G:i",$d["date"]);
						if($d["date"]>$time_change_this) {
							$time_change_this=$d["date"];
						}
					}
					if($newcommentsbyplayer!='' && $newcommentsbymaster!='') {
						break;
					}
				}

				if($newcommentsbyplayer!='' || $rolewaschangedbyplayer!='' || $newcommentsbymaster!='' || $rolewaschangedbymaster) {
					$no_changes_on_site=false;
					$counter++;

					$mysitesorders2_this='';

					$mysitesorders2_this.='<a href="'.$server_absolute_path_site.'orders/'.$b["id"].'/site='.$a["id"].'" title="';
					if($rolewaschangedbyplayer!='') {
						$mysitesorders2_this.='изменил игрок: '.$rolewaschangedbyplayer.'; ';
					}
					if($rolewaschangedbymaster!='') {
						$mysitesorders2_this.='изменил мастер: '.$rolewaschangedbymaster.'; ';
					}
					if($newcommentsbyplayer!='') {
						$mysitesorders2_this.='комментарии игрока: '.$newcommentsbyplayer.'; ';
					}
					if($newcommentsbymaster!='') {
						$mysitesorders2_this.='комментарии мастера: '.$newcommentsbymaster.'; ';
					}
					$mysitesorders2_this.='статус: ';
					if($b["status"]==1) {
						$mysitesorders2_this.='подана';
					}
					elseif($b["status"]==2) {
						$mysitesorders2_this.='обсуждается';
					}
					elseif($b["status"]==3) {
						$mysitesorders2_this.='принята';
					}
					elseif($b["status"]==4) {
						$mysitesorders2_this.='отклонена';
					}
					$mysitesorders2_this.='; игрок: ';
					$result6=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$b["player_id"]);
					$f = mysql_fetch_array($result6);
					$mysitesorders2_this.=usname($f,true).'">';

					if(str_replace(' ','',decode($b["sorter"]))!='') {
						$mysitesorders2_this.=decode($b["sorter"]);
					}
					else {
						$mysitesorders2_this.='<font color="red">('.usname($f,true).')</font>';
					}
					$mysitesorders2_this.='</a><br>
';

					$time_change[]=$time_change_this;
					$mysiteorders2_changes[]=$mysitesorders2_this;
				}
			}
			if($no_changes_on_site) {
				$time_change[]=time();
				$mysiteorders2_changes[]='<a href="'.$server_absolute_path_site.'orders/site='.$a["id"].'">нет изменений</a><br>';
			}
			//сортируем по времени
			array_multisort($time_change, SORT_DESC, $mysiteorders2_changes);
			foreach($mysiteorders2_changes as $key=>$value) {
				$mysitesorders2.=$value;
			}
		}
	}

	$mysitesorders2='<a href="'.$server_absolute_path_site.'"><h3>ВСЕ МОИ ПРОЕКТЫ</h3></a><a href="'.$server_absolute_path_site.'">Перейти</a><br><br>'.$mysitesorders2;

	$content2.='<li class="tile_mysitesorders black"><a onClick="$(\'.tile_mysitesorders\').css(\'display\',\'none\');$(\'.tile_mysitesorders2\').css(\'display\',\'block\');"><div class="counter" title="измененные заявки"><div>'.$counter.'</div></div><div class="text">ЗАЯВКИ<br>МОИ ПРОЕКТЫ</div></a></li>';
	$content2.='<li class="tile_mysitesorders2 black"><div><div>'.$mysitesorders2.'</div></div></li>';

	$content2.='</div>';

	$content2.='<div class="tile_logged_section3">';

	$content2.='<li class="tile_calendar blue"><a href="'.$server_absolute_path_calendar.'"><div class="text">РОЛЕВОЙ КАЛЕНДАРЬ</div></a></li>';

	$content2.='</div>';

	$content2.='<div class="tile_logged_section4">';

	$content2.='<li class="tile_support blue"><a href="'.$server_absolute_path.'help/"><div class="text">ПОДДЕРЖКА</div></a></li>';
	$content2.='<li class="tile_info black"><a href="'.$server_absolute_path_info.'"><div class="text">ИНФОТЕКА</div></a></li>';

	$content2.='</div>';

	$content2.='<div class="tile_logged_section5">';

	$counter=0;
	$result=mysql_query("SELECT COUNT(id) from ".$prefix."blog_pms where user_id=".$_SESSION["user_id"]." and pmread='0'");
	$a=mysql_fetch_array($result);
	$counter=$a[0];

	$content2.='<li class="tile_messages black"><a href="'.$server_absolute_path.'inbox/"><div class="counter" title="новые"><div>'.$counter.'</div></div><div class="text">СООБЩЕНИЯ</div></a></li>';
	$content2.='<li class="tile_neworders blue"><a href="'.$server_absolute_path_site.'hosting2/"><div class="text">СОЗДАТЬ<br>СИСТЕМУ<br>ЗАЯВОК</div></a></li>';
	$content2.='<li class="tile_neworder black"><a href="'.$server_absolute_path.'order/order/act=add"><div class="text">ПОДАТЬ ЗАЯВКУ</div></a></li>';

	$content2.='</div>';

	$content2.='<div class="clear"></div>';
}
else {
	$content2.='<nav data_height="230" data_width="670">';

	$content2.='<div class="tile_notlogged_section1">';

	$content2.='<li class="tile_enter blue" id="tile_enter"><a><div class="text">ВОЙТИ</div>
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
<button class="main" id="btn_login" tabindex="3">войти</button><button class="nonimportant" id="btn_remind">забыл</button>
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
	$content2.='<li class="tile_register black"><a href="'.$server_absolute_path.'register/"><div class="text">РЕГИСТРАЦИЯ</div></a></li>';

	$content2.='</div>';

	$content2.='<div class="tile_notlogged_section2">';

	$content2.='<li class="tile_neworder_notlogged black"><a href="'.$server_absolute_path.'order/order/act=add"><div class="text">ПОДАТЬ ЗАЯВКУ</div></a></li>';
	$content2.='<li class="tile_neworders_notlogged blue"><a href="'.$server_absolute_path_site.'hosting2/"><div class="text">СОЗДАТЬ<br>СИСТЕМУ<br>ЗАЯВОК</div></a></li>';

	$content2.='</div>';

	$content2.='<div class="tile_notlogged_section3">';

	$content2.='<li class="tile_calendar blue"><a href="'.$server_absolute_path_calendar.'"><div class="text">РОЛЕВОЙ КАЛЕНДАРЬ</div></a></li>';

	$content2.='</div>';

	$content2.='<div class="tile_notlogged_section4">';

	$content2.='<li class="tile_support black"><a href="'.$server_absolute_path.'help/"><div class="text">ПОДДЕРЖКА</div></a></li>';
	$content2.='<li class="tile_info blue"><a href="'.$server_absolute_path_info.'"><div class="text">ИНФОТЕКА</div></a></li>';

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