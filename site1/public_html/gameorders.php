<?php
include_once("db.inc");
require_once($server_inner_path.$direct."/classes/classes_objects.php");
require_once($server_inner_path."classes_objects_allrpg.php");
require_once ($server_inner_path."appcode/data/roles_linked.php");

start_mysql();
# Установление соединения с MySQL-сервером

session_start();

$game=encode($_GET["game"]);
$id=encode($_GET["id"]);
$css=encode($_GET["css"]);
$locat=encode($_GET["locat"]);
if($css=='') {
	$css='http://www.allrpg.info/main2.css';
}
$include=encode($_GET["include"]);

if($game!='')
{
	$subobj=$game;

	function locatpath($id) {
    global $subobj;
		$return = implode (' → ', get_location_path ($id, $subobj));
		return $return ? $return : 'не указана';
	}

	function getlocatchild($locat) {
		global
			$prefix,
			$subobj;

		$list.=$locat.',';
		$result3=mysql_query("SELECT * FROM ".$prefix."roleslocat WHERE site_id=".$subobj." and parent=".$locat);
		while($c = mysql_fetch_array($result3)) {
			$list.=getlocatchild($c["id"]).',';
		}
		$list=substr($list,0,strlen($list)-1);
		return $list;
	}

	if(encode($_GET["orders"])!='') {
		$orders=encode($_GET["orders"]);
	}

	$showonlyacceptedroles=false;
	$result=mysql_query("SELECT * FROM ".$prefix."sites WHERE id=".$subobj);
	$a=mysql_fetch_array($result);
	if($a["showonlyacceptedroles"]=='1') {
		$showonlyacceptedroles=true;
	}

	$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."rolevacancy WHERE site_id=".$subobj);
	$a=mysql_fetch_array($result);
	if($a[0]==0) {
		$orders=1;
	}

	$result=mysql_query("SELECT * FROM ".$prefix."roleslocat WHERE site_id=".$subobj." LIMIT 0,1");
	$a=mysql_fetch_array($result);
	if($a["id"]!='') {
		$havelocats=true;
		$alllocats=make5fieldtree(true,$prefix."roleslocat","parent",0," AND site_id=".$subobj,"code asc, name asc",1,"id","name",1000000);
		$alllocats[0][1]='Без названия';
		for($i=0;$i<count($alllocats);$i++) {
			if($alllocats[$i][0]>0) {
				$alllocats[$i][1]=locatpath($alllocats[$i][0]);
			}
			$alllocats_ids.=$alllocats[$i][0].', ';
		}
		$alllocats_ids=substr($alllocats_ids,0,strlen($alllocats_ids)-2);
	}

	$ord_admin=false;
	$result=mysql_query("SELECT * FROM ".$prefix."allrights2 WHERE site_id=".$subobj." and user_id=".$_SESSION["user_sid"]);
	$a=mysql_fetch_array($result);
	if($a["rights"]==1 || $a["rights"]==2 || $_SESSION["admin"]) {
		$ord_admin=true;
	}

	if($orders==1) {
		// заявки
		if($id>0) {
			if($showonlyacceptedroles) {
				$result=mysql_query("SELECT * FROM ".$prefix."roles where id=".$id." and todelete!=1 and todelete2!=1 and status=3 and site_id=".$subobj.($locat>0?' and locat='.$locat:''));
			}
			else {
				$result=mysql_query("SELECT * FROM ".$prefix."roles where id=".$id." and todelete!=1 and todelete2!=1 and site_id=".$subobj.($locat>0?' and locat='.$locat:''));
			}
			$a = mysql_fetch_array($result);

			$result4=mysql_query("SELECT * FROM ".$prefix."roleslocat WHERE id=".$a["locat"]." and site_id=".$subobj);
			$d=mysql_fetch_array($result4);
			if($a["id"]!='' && $d["rights"]!=1) {
				$result3=mysql_query("SELECT * FROM ".$prefix."sites WHERE id=".$subobj);
				$c=mysql_fetch_array($result3);
				$result5=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$a["player_id"]);
				$e=mysql_fetch_array($result5);
				$result6=mysql_query("SELECT * FROM ".$prefix."rolevacancy WHERE id=".$a["vacancy"]." and site_id=".$subobj);
				$f=mysql_fetch_array($result6);
				$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."roles WHERE vacancy=".$f["id"]." and status=3 and todelete!=1 and todelete2!=1");
				$b=mysql_fetch_array($result2);

				$pagetitle=h1line(decode($c["title"]).' – заявки');

				$content2.='<h3 style="text-align: center;">'.decode($a["sorter"]).'</h3>';
				if(($b[0]<$f["kolvo"] && $c["status2"]==2 && $c["testing"]!='1') || ($b[0]>1 && $d["rights"]!=1) || $ord_admin) {
					$gotsmth=false;
					$content2.='<center><hr>';
					if($b[0]<$f["kolvo"] && $c["status2"]==2 && $c["testing"]!='1') {
						if($_SESSION["user_id"]!='') {
							$content2.='<a href="'.$server_absolute_path.'order/act=add&subobj='.$subobj.'&roletype='.$f["team"].'&wantrole='.$f["id"].'" target="_blank">';
						}
						else {
							$content2.='<a href="'.$server_absolute_path.'register/redirectobj=order&redirectid='.$subobj.'&redirectparams=roletype:'.$f["team"].'*wantrole:'.$f["id"].'" target="_blank">';
						}
						$content2.='<b>Подать заявку на такую же роль</b></a>';
						$gotsmth=true;
					}
					if($b[0]>1 && $d["rights"]!=1) {
						if($gotsmth) {
							$content2.=' | ';
						}
						$content2.='<a href="'.$server_absolute_path.'gameorders.php?game='.$subobj.'&orders=1&wantrole='.$f["id"].'&css='.$css.'"><b>Поданные на ту же роль заявки</b></a>';
						$gotsmth=true;
					}
					if($ord_admin) {
						if($gotsmth) {
							$content2.=' | ';
						}
						$content2.='<a href="'.$server_absolute_path_site.'orders/'.$id.'/site='.$subobj.'" target="_blank"><b>Редактировать заявку</b></a>';
						$gotsmth=true;
					}
					$content2.='<hr></center><br>';
				}
				else {
					$content2.='<hr>';
				}
				$content2.='<b>Игрок</b>: '.usname($e,true,true).'<br>';
				if($a["locat"]!='' && $d["name"]!='' && $havelocats) {
					$content2.='<b>Локация / команда</b>: '.locatpath($d["id"]).'<br>';
				}
				$content2.='<b>Тип</b>: ';
				if($a["team"]==1) {
					$content2.='командная';
				}
				else {
					$content2.='индивидуальная';
				}
				$content2.='<br>';
				$content2.='<b>Статус</b>: ';
				if($a["status"]==1) {
					$content2.='подана';
				}
				elseif($a["status"]==2) {
					$content2.='обсуждается';
				}
				elseif($a["status"]==3) {
					$content2.='принята';
				}
				elseif($a["status"]==4) {
					$content2.='отклонена';
				}
				$content2.='<br>';
				if($a["vacancy"]!='' && $f["name"]!='') {
					$content2.='<b>Роль</b>: <a href="'.$server_absolute_path.'gameorders.php?game='.$subobj.'&id='.$f["id"].'&css='.$css.'">'.decode($f["name"]).'</a><br>';
				}
				$content2.='<hr>';

				// динамические поля заявки
				$rolefields=virtual_structure("SELECT * from ".$prefix."rolefields where team='".$a["team"]."' and site_id=".$subobj." order by rolecode","allinfo","role");
				$allvalues=unmakevirtual($a["allinfo"]);
				for($i=0;$i<count($rolefields);$i++) {
					if($rolefields[$i]["read"]==1 && (decode($allvalues[$rolefields[$i]["name"]])!='' || $rolefields[$i]["type"]=='h1' || $rolefields[$i]["type"]=='checkbox')) {
						if($rolefields[$i]["type"]=='h1') {
							$content2.='<h3 align=center>'.$rolefields[$i]["sname"].'</h3>';
						}
						else {
							if($rolefields[$i+1]["type"]!="h1") {
								$content2.='<div style="margin-bottom: 10px;">';
							}
							if($rolefields[$i]["type"]=="text" || $rolefields[$i]["type"]=="number") {
								$content2.='<b>'.$rolefields[$i]["sname"].'</b>: ';
								$content2.=decodesafe($allvalues[$rolefields[$i]["name"]]);
							}
							elseif($rolefields[$i]["type"]=="textarea") {
								$content2.='<b>'.$rolefields[$i]["sname"].'</b>:<br>';
								$content2.=decodesafe($allvalues[$rolefields[$i]["name"]]);
							}
							elseif($rolefields[$i]["type"]=="checkbox") {
								$content2.='<b>'.$rolefields[$i]["sname"].'</b>: ';
								if($allvalues[$rolefields[$i]["name"]]==1) {
									$content2.='<font color="green"><b>&#8730</b></font>';
								}
								else {
									$content2.='<font color="red"><b>X</b></font>';
								}
							}
							elseif($rolefields[$i]["type"]=="select") {
								$content2.='<b>'.$rolefields[$i]["sname"].'</b>: ';
								for($j=0;$j<count($rolefields[$i]["values"]);$j++) {
									if($rolefields[$i]["values"][$j][0]==$allvalues[$rolefields[$i]["name"]]) {
										$content2.=$rolefields[$i]["values"][$j][1];
										break;
									}
								}
							}
							elseif($rolefields[$i]["type"]=="multiselect") {
								$content2.='<b>'.$rolefields[$i]["sname"].'</b>: ';
								for($j=0;$j<count($rolefields[$i]["values"]);$j++) {
									if(eregi('-'.$rolefields[$i]["values"][$j][0].'-',$allvalues[$rolefields[$i]["name"]])) {
										$content2.='<br>'.$rolefields[$i]["values"][$j][1];
									}
								}
							}
							elseif($rolefields[$i]["type"]=="wysiwyg") {
								$content2.='<b>'.$rolefields[$i]["sname"].'</b>: <br>';
								$content2.=decode($allvalues[$rolefields[$i]["name"]]);
							}
							if($rolefields[$i+1]["type"]!="h1") {
								$content2.='</div>';
							}
						}
					}
				}
				$content2.='</div></center>';
			}
		}
		else {
			$result=mysql_query("SELECT * FROM ".$prefix."users u, ".$prefix."roles r where u.id = r.player_id AND r.site_id=".$subobj.($locat>0?' AND r.locat='.$locat:''));
			while($a = mysql_fetch_array($result)) {
				$allusers[]=Array($a["id"],usname($a,true));
			}
			foreach ($allusers as $key => $row) {
				$allusers_sort[$key]  = strtolower($row[1]);
			}
			array_multisort($allusers_sort, SORT_ASC, $allusers);

			$ordfield='FIELD(t3.id';
			for($j=0;$j<count($allusers);$j++) {
				$ordfield.=", ".$allusers[$j][0];
			}
			$ordfield.=')';
			$result=mysql_query("SELECT * FROM ".$prefix."rolefields where site_id=".$subobj." and (id IN (SELECT sorter from ".$prefix."sites where id=".$subobj.") or id IN (SELECT sorter2 from ".$prefix."sites where id=".$subobj.")) order by team asc");
			$a = mysql_fetch_array($result);
			$sorter=decode($a["rolename"]);
			$a = mysql_fetch_array($result);
			if($a["rolename"]!='') {
				$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."roles where team='1' and todelete!=1 and todelete2!=1");
				$b = mysql_fetch_array($result2);
				if($b[0]>0) {
					$sorter.=' / '.decode($a["rolename"]);
				}
			}
			if($sorting==0) {
				$sorting=1;
			}
			if($havelocats) {
				if(encode($_GET["wantrole"])!='' || encode($_GET["wantrole2"])!='') {
          $wantrole = intval ($_GET["wantrole"]);
          if (!$wantrole)
          {
            $wantrole = intval ($_GET["wantrole2"]);
          }
          $acceptedfilter = $showonlyacceptedroles ? " AND t1.status = 3" : '';
					$query="SELECT t1.*, t2.name as locatname, t2.id as locatid, t3.sid, t3.nick, t3.fio, t3.hidesome, t4.name as vacancyname FROM ".$prefix."users t3, ".$prefix."roles t1 LEFT JOIN ".$prefix."roleslocat t2 ON t2.id=t1.locat LEFT JOIN ".$prefix."rolevacancy t4 ON t4.id=t1.vacancy 
					WHERE (t2.rights!=1 || t1.locat='') and t1.todelete!=1 and t1.todelete2!=1 $acceptedfilter and t3.id=t1.player_id and t4.id=$wantrole and t1.site_id=".$subobj.($locat>0?' and t2.id IN ('.getlocatchild($locat).')':'')." order by FIELD(t2.id, ".$alllocats_ids.")";

					$query2="SELECT COUNT(t1.id) FROM ".$prefix."users t3, ".$prefix."roles t1 LEFT JOIN ".$prefix."roleslocat t2 ON t2.id=t1.locat LEFT JOIN ".$prefix."rolevacancy t4 ON t4.id=t1.vacancy WHERE (t2.rights!=1 || t1.locat='') and t1.todelete!=1 and $acceptedfilter t1.todelete2!=1 and t3.id=t1.player_id and t4.id=$wantrole and t1.site_id=".$subobj.($locat>0?' and t2.id IN ('.getlocatchild($locat).')':'');
				}
				else {
					$query="SELECT t1.*, t2.name as locatname, t2.id as locatid, t3.sid, t3.nick, t3.fio, t3.hidesome, t4.name as vacancyname FROM ".$prefix."users t3, ".$prefix."roles t1 LEFT JOIN ".$prefix."roleslocat t2 ON t2.id=t1.locat LEFT JOIN ".$prefix."rolevacancy t4 ON t4.id=t1.vacancy WHERE (t2.rights!=1 || t1.locat='') and t1.todelete!=1 and t1.todelete2!=1 and t3.id=t1.player_id and t1.site_id=".$subobj.($locat>0?' and t2.id IN ('.getlocatchild($locat).')':'')." order by FIELD(t2.id, ".$alllocats_ids.")";

					$query2="SELECT COUNT(t1.id) FROM ".$prefix."users t3, ".$prefix."roles t1 LEFT JOIN ".$prefix."roleslocat t2 ON t2.id=t1.locat LEFT JOIN ".$prefix."rolevacancy t4 ON t4.id=t1.vacancy WHERE (t2.rights!=1 || t1.locat='') and t1.todelete!=1 and t1.todelete2!=1 and t3.id=t1.player_id and t1.site_id=".$subobj.($locat>0?' and t2.id IN ('.getlocatchild($locat).')':'');
				}
				if($sorting==1) {
					$query.=', '.$ordfield.' ASC';
				}
				elseif($sorting==2) {
					$query.=', '.$ordfield.' DESC';
				}
				elseif($sorting==3) {
					$query.=', '.'t1.sorter ASC';
				}
				elseif($sorting==4) {
					$query.=', '.'t1.sorter DESC';
				}
				elseif($sorting==5) {
					$query.=', '.'t1.status ASC';
				}
				elseif($sorting==6) {
					$query.=', '.'t1.status DESC';
				}
				elseif($sorting==7) {
					$query.=', '.'t4.name ASC';
				}
				elseif($sorting==8) {
					$query.=', '.'t4.name DESC';
				}
			}
			else {
				if(encode($_GET["wantrole"])!='') {
					$query="SELECT t1.*, t2.name as locatname, t2.id as locatid, t3.sid, t3.nick, t3.fio, t3.hidesome, t4.name as vacancyname FROM ".$prefix."users t3, ".$prefix."roles t1 LEFT JOIN ".$prefix."roleslocat t2 ON t2.id=t1.locat LEFT JOIN ".$prefix."rolevacancy t4 ON t4.id=t1.vacancy WHERE (t2.rights!=1 || t1.locat='') and t1.todelete!=1 and t1.todelete2!=1 and t3.id=t1.player_id and t4.id=".encode($_GET["wantrole"])." and t1.site_id=".$subobj." order by ";

					$query2="SELECT COUNT(t1.id) FROM ".$prefix."users t3, ".$prefix."roles t1 LEFT JOIN ".$prefix."roleslocat t2 ON t2.id=t1.locat LEFT JOIN ".$prefix."rolevacancy t4 ON t4.id=t1.vacancy WHERE (t2.rights!=1 || t1.locat='') and t1.todelete!=1 and t1.todelete2!=1 and t3.id=t1.player_id and t4.id=".encode($_GET["wantrole"])." and t1.site_id=".$subobj;
				}
				elseif(encode($_GET["wantrole2"])!='') {
					$query="SELECT t1.*, t2.name as locatname, t2.id as locatid, t3.sid, t3.nick, t3.fio, t3.hidesome, t4.name as vacancyname FROM ".$prefix."users t3, ".$prefix."roles t1 LEFT JOIN ".$prefix."roleslocat t2 ON t2.id=t1.locat LEFT JOIN ".$prefix."rolevacancy t4 ON t4.id=t1.vacancy WHERE (t2.rights!=1 || t1.locat='') and t1.todelete!=1 and t1.todelete2!=1 and t3.id=t1.player_id and t4.id=".encode($_GET["wantrole2"])." and t1.status=3 and t1.site_id=".$subobj." order by ";

					$query2="SELECT COUNT(t1.id) FROM ".$prefix."users t3, ".$prefix."roles t1 LEFT JOIN ".$prefix."roleslocat t2 ON t2.id=t1.locat LEFT JOIN ".$prefix."rolevacancy t4 ON t4.id=t1.vacancy WHERE (t2.rights!=1 || t1.locat='') and t1.todelete!=1 and t1.todelete2!=1 and t3.id=t1.player_id and t4.id=".encode($_GET["wantrole2"])." and t1.status=3 and t1.site_id=".$subobj;
				}
				else {
					$query="SELECT t1.*, t2.name as locatname, t2.id as locatid, t3.sid, t3.nick, t3.fio, t3.hidesome, t4.name as vacancyname FROM ".$prefix."users t3, ".$prefix."roles t1 LEFT JOIN ".$prefix."roleslocat t2 ON t2.id=t1.locat LEFT JOIN ".$prefix."rolevacancy t4 ON t4.id=t1.vacancy WHERE (t2.rights!=1 || t1.locat='') and t1.todelete!=1 and t1.todelete2!=1 and t3.id=t1.player_id and t1.site_id=".$subobj.($locat>0?' and t1.locat='.$locat:'')." order by ";

					$query2="SELECT COUNT(t1.id) FROM ".$prefix."users t3, ".$prefix."roles t1 LEFT JOIN ".$prefix."roleslocat t2 ON t2.id=t1.locat LEFT JOIN ".$prefix."rolevacancy t4 ON t4.id=t1.vacancy WHERE (t2.rights!=1 || t1.locat='') and t1.todelete!=1 and t1.todelete2!=1 and t3.id=t1.player_id and t1.site_id=".$subobj.($locat>0?' and t1.locat='.$locat:'');
				}
				if($sorting==1) {
					$query.=$ordfield.' ASC';
				}
				elseif($sorting==2) {
					$query.=$ordfield.' DESC';
				}
				elseif($sorting==3) {
					$query.='t1.sorter ASC';
				}
				elseif($sorting==4) {
					$query.='t1.sorter DESC';
				}
				elseif($sorting==5) {
					$query.='t1.status ASC';
				}
				elseif($sorting==6) {
					$query.='t1.status DESC';
				}
				elseif($sorting==7) {
					$query.='t4.name ASC';
				}
				elseif($sorting==8) {
					$query.='t4.name DESC';
				}
			}
			$result=mysql_query($query);

			$result2=mysql_query("SELECT * FROM ".$prefix."sites WHERE id=".$subobj);
			$b=mysql_fetch_array($result2);

			$pagetitle=h1line(decode($b["title"]).' – заявки');

			$content2.='<center><div id="cb_editor">';
			$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."rolevacancy WHERE site_id=".$subobj);
			$b=mysql_fetch_array($result2);
			if($b[0]>0) {
				$content2.='<span class="gui-btn"><span><a href="'.$server_absolute_path.'gameorders.php?game='.$subobj.'&css='.$css.'">К сетке ролей</a></span></span><br>';
			}
			if(encode($_GET["wantrole"])!='' || encode($_GET["wantrole2"])!='') {
				$content2.='<span class="gui-btn"><span><a href="'.$server_absolute_path.'gameorders.php?game='.$subobj.'&orders=1&css='.$css.'">Все заявки</a></span></span><br>';
			}
			$result2=mysql_query($query2);
			$b=mysql_fetch_array($result2);
			$pagetotal=$b[0];
			$content2.='<div style="text-align: right"><b>Всего заявок</b>: '.$b[0].'</div>';

			$content2.='<table cellpadding="0" cellspacing="0" border="0" width=100% align=center>
			<tr valign="bottom">
			<td>
			<br>
			<table class="menutable">
			<tr>';
			$content2.='
			<td class="menu"><a href="'.$server_absolute_path.'gameorders.php?game='.$subobj.'&orders=1&css='.$css.'&sorting=';
			if($sorting==1) {
				$content2.='2" title="[отсортировать : игрок : по убыванию]" onMouseOver="document.getElementById(\'arrow\').src=\''.$server_absolute_path.$direct.'/down.gif\'" onMouseOut="document.getElementById(\'arrow\').src=\''.$server_absolute_path.$direct.'/up.gif\'">Игрок</a> <img src="'.$server_absolute_path.$direct.'/up.gif" id="arrow" border=0>';
			}
			elseif($sorting==2) {
				$content2.='1" title="[отсортировать : игрок : по возрастанию]" onMouseOver="document.getElementById(\'arrow\').src=\''.$server_absolute_path.$direct.'/up.gif\'" onMouseOut="document.getElementById(\'arrow\').src=\''.$server_absolute_path.$direct.'/down.gif\'">Игрок</a> <img src="'.$server_absolute_path.$direct.'/down.gif" id="arrow" border=0>';
			}
			else {
				$content2.='1" title="[отсортировать : игрок : по возрастанию]">Игрок</a>';
			}
			$content2.='</td>';

			$content2.='
			<td class="menu"><a href="'.$server_absolute_path.'gameorders.php?game='.$subobj.'&orders=1&css='.$css.'&sorting=';
			if($sorting==3) {
				$content2.='4" title="[отсортировать : '.strtolower($sorter).' : по убыванию]" onMouseOver="document.getElementById(\'arrow\').src=\''.$server_absolute_path.$direct.'/down.gif\'" onMouseOut="document.getElementById(\'arrow\').src=\''.$server_absolute_path.$direct.'/up.gif\'">'.$sorter.'</a> <img src="'.$server_absolute_path.$direct.'/up.gif" id="arrow" border=0>';
			}
			elseif($sorting==4) {
				$content2.='3" title="[отсортировать : '.strtolower($sorter).' : по возрастанию]" onMouseOver="document.getElementById(\'arrow\').src=\''.$server_absolute_path.$direct.'/up.gif\'" onMouseOut="document.getElementById(\'arrow\').src=\''.$server_absolute_path.$direct.'/down.gif\'">'.$sorter.'</a> <img src="'.$server_absolute_path.$direct.'/down.gif" id="arrow" border=0>';
			}
			else {
				$content2.='3" title="[отсортировать : '.strtolower($sorter).' : по возрастанию]">'.$sorter.'</a>';
			}
			$content2.='</td>';

			$content2.='
			<td class="menu"><a href="'.$server_absolute_path.'gameorders.php?game='.$subobj.'&orders=1&css='.$css.'&sorting=';
			if($sorting==5) {
				$content2.='6" title="[отсортировать : статус : по убыванию]" onMouseOver="document.getElementById(\'arrow\').src=\''.$server_absolute_path.$direct.'/down.gif\'" onMouseOut="document.getElementById(\'arrow\').src=\''.$server_absolute_path.$direct.'/up.gif\'">Статус</a> <img src="'.$server_absolute_path.$direct.'/up.gif" id="arrow" border=0>';
			}
			elseif($sorting==6) {
				$content2.='5" title="[отсортировать : статус : по возрастанию]" onMouseOver="document.getElementById(\'arrow\').src=\''.$server_absolute_path.$direct.'/up.gif\'" onMouseOut="document.getElementById(\'arrow\').src=\''.$server_absolute_path.$direct.'/down.gif\'">Статус</a> <img src="'.$server_absolute_path.$direct.'/down.gif" id="arrow" border=0>';
			}
			else {
				$content2.='5" title="[отсортировать : статус : по возрастанию]">Статус</a>';
			}
			$content2.='</td>';

			$result3=mysql_query("SELECT COUNT(id) FROM ".$prefix."rolevacancy where site_id=".$subobj);
			$c = mysql_fetch_array($result3);
			$kolvovacancy=$c[0];
			if($kolvovacancy>0) {
				$content2.='
				<td class="menu"><a href="'.$server_absolute_path.'gameorders.php?game='.$subobj.'&orders=1&css='.$css.'&sorting=';
				if($sorting==7) {
					$content2.='8" title="[отсортировать : роль : по убыванию]" onMouseOver="document.getElementById(\'arrow\').src=\''.$server_absolute_path.$direct.'/down.gif\'" onMouseOut="document.getElementById(\'arrow\').src=\''.$server_absolute_path.$direct.'/up.gif\'">Роль</a> <img src="'.$server_absolute_path.$direct.'/up.gif" id="arrow" border=0>';
				}
				elseif($sorting==8) {
					$content2.='7" title="[отсортировать : роль : по возрастанию]" onMouseOver="document.getElementById(\'arrow\').src=\''.$server_absolute_path.$direct.'/up.gif\'" onMouseOut="document.getElementById(\'arrow\').src=\''.$server_absolute_path.$direct.'/down.gif\'">Роль</a> <img src="'.$server_absolute_path.$direct.'/down.gif" id="arrow" border=0>';
				}
				else {
					$content2.='7" title="[отсортировать : роль : по возрастанию]">Роль</a>';
				}
				$content2.='</td>';
			}

			$content2.='
			</tr>';

			$prevlocatid=-1;
			while($a=mysql_fetch_array($result)) {
				if(!$showonlyacceptedroles || $a["status"]==3) {
					$team='';
					if($a["team"]==1) {
						$team="командная";
					}
					else {
						$team="индивидуальная";
					}
					if($prevlocatid!=$a["locatid"] && $havelocats) {
						$prevlocatid=$a["locatid"];
						$content2.='
				<tr><td class="locations" colspan=4>';
						if($a["status"]==4) {
							$content.='<s>';
						}
						if($a["locatid"]==0) {
							$content2.='Локация не определена';
						}
						else {
							$content2.=locatpath($a["locatid"]);
						}
						if($a["status"]==4) {
							$content.='</s>';
						}
						$content2.='</td></tr>';
					}
					$content2.='
				<tr>
				<td>
				<a href="'.$server_absolute_path.'gameorders.php?game='.$subobj.'&id='.$a["id"].'&orders=1&css='.$css.'">';
					if($a["status"]==4) {
						$content2.='<s>';
					}
					$content2.=usname($a,true);
					if($a["status"]==4) {
						$content2.='</s>';
					}
					$content2.='</a>
				</td>
				<td>
				<a href="'.$server_absolute_path.'gameorders.php?game='.$subobj.'&id='.$a["id"].'&orders=1&css='.$css.'">';
					if($a["status"]==4) {
						$content2.='<s>';
					}
					$content2.=decode($a["sorter"]);
					if($a["status"]==4) {
						$content2.='</s>';
					}
					$content2.='</a>
				</td>
				<td>
				<a href="'.$server_absolute_path.'gameorders.php?game='.$subobj.'&id='.$a["id"].'&orders=1&css='.$css.'">';
					if($a["status"]==1) {
						$content2.='подана';
					}
					elseif($a["status"]==2) {
						$content2.='обсуждается';
					}
					elseif($a["status"]==3) {
						$content2.='принята';
					}
					elseif($a["status"]==4) {
						$content2.='<s>отклонена</s>';
					}
					$content2.='</a>
				</td>';
					if($kolvovacancy>0) {
						$content2.='
				<td>
				<a href="'.$server_absolute_path.'gameorders.php?game='.$subobj.'&id='.$a["id"].'&orders=1&css='.$css.'">';
						if($a["status"]==4) {
							$content2.='<s>';
						}
						$content2.=decode($a["vacancyname"]);
						if($a["status"]==1 || $a["status"]==2) {
							$content2.='?';
						}
						if($a["status"]==4) {
							$content2.='<s>';
						}
						$content2.='</a>
				</td>
				</tr>';
					}
				}
			}

			$content2.='
			</table>
			</td>
			</tr>
			</table>
			</div></center>';
		}
	}
	else {
		// сетка ролей
		if($id>0) {
			$result=mysql_query("SELECT * FROM ".$prefix."rolevacancy where id=".$id." and site_id=".$subobj.($locat>0?' and locat='.$locat:''));
			$a = mysql_fetch_array($result);
			if($a["id"]!='') {
				$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."roles WHERE vacancy=".$a["id"]." and todelete!=1 and todelete2!=1");
				$b=mysql_fetch_array($result2);
				$result5=mysql_query("SELECT COUNT(id) FROM ".$prefix."roles WHERE vacancy=".$a["id"]." and status=3 and todelete!=1 and todelete2!=1");
				$e=mysql_fetch_array($result5);
				if($a["taken"]!='') {
					unset($taken);
					$taken2='';
					$taken2=decode($a["taken"]);
					$taken=explode(',',$taken2);
					if($taken[0]=='') {
						unset($taken);
					}
					$e[0]+=count($taken);
				}
				$result3=mysql_query("SELECT * FROM ".$prefix."sites WHERE id=".$subobj);
				$c=mysql_fetch_array($result3);
				$result4=mysql_query("SELECT * FROM ".$prefix."roleslocat WHERE id=".$a["locat"]." and site_id=".$subobj);
				$d=mysql_fetch_array($result4);

				$pagetitle=h1line(decode($c["title"]).' – сетка ролей');

				$content2.='<center><div id="cb_editor" style="text-align: justify"><h3 style="text-align: center;">'.decode($a["name"]).'</h3>';
				if(($e[0]<$a["kolvo"] && $c["status2"]==2 && $c["testing"]!='1') || ($b[0]>0 && $d["rights"]!=1) || ($e[0]>0 && $d["rights"]!=1) || $ord_admin) {
					$gotsmth=false;
					$content2.='<center><hr>';
					if($e[0]<$a["kolvo"] && $c["status2"]==2 && $c["testing"]!='1') {
						if($_SESSION["user_id"]!='') {
							$content2.='<a href="'.$server_absolute_path.'order/act=add&subobj='.$subobj.'&roletype='.$a["team"].'&wantrole='.$id.'" target="_blank">';
						}
						else {
							$content2.='<a href="'.$server_absolute_path.'register/redirectobj=order&redirectid='.$subobj.'&redirectparams=roletype:'.$a["team"].'*wantrole:'.$id.'" target="_blank">';
						}
						$content2.='<b>Подать заявку на данную роль</b></a>';
						$gotsmth=true;
					}
					if($b[0]>0 && $d["rights"]!=1) {
						if($gotsmth) {
							$content2.=' | ';
						}
						$content2.='<a href="'.$server_absolute_path.'gameorders.php?game='.$subobj.'&orders=1&wantrole='.$id.'&css='.$css.'"><b>Заявки на роль </b></a>';
						$gotsmth=true;
					}
					if($ord_admin) {
						if($gotsmth) {
							$content2.=' | ';
						}
						$content2.='<a href="'.$server_absolute_path_site.'roles/'.$id.'/site='.$subobj.'" target="_blank"><b>Редактировать роль</b></a>';
					}
					$content2.='<hr></center>';
				}
				else {
					$content2.='<hr>';
				}
				if($a["locat"]!='' && $d["name"]!='' && $havelocats) {
					$content2.='<b>Локация / команда</b>: '.locatpath($d["id"]).'<br>';
				}
				$content2.='<b>Тип</b>: ';
				if($a["team"]==1) {
					$content2.='командная<br>';
					if($a["teamkolvo"]>0) {
						$content2.='<b>Желаемое количество людей</b>: '.$a["teamkolvo"].'<br>';
					}
				}
				else {
					$content2.='индивидуальная<br>';
				}
				if($e[0]>0) {
					$content2.='<b>Принято заявок</b>: '.$e[0].'<br>';
					if($a["taken"]!='') {
						$content2.='<b>Из них приняты (вне allrpg.info)</b>: '.decode($a["taken"]).'<br>';
					}
				}
				if($a["maybetaken"]!='' && $e[0]<$a["kolvo"]) {
					$content2.='<b>Предварительно занято (вне allrpg.info)</b>: '.decode($a["maybetaken"]).'<br>';
				}
				$content2.='<b>Желаемое количество заявок</b>: '.decode($a["kolvo"]).'<hr>';
				if(decode($a["content"])!='') {
					$content2.='<b>Описание роли</b>:<br>'.decode($a["content"]);
				}
				$content2.='</div></center>';
			}
		}
		else {
			$result=mysql_query("SELECT * FROM ".$prefix."sites where testing!='1' and id=".$subobj);
			$a = mysql_fetch_array($result);
			$orderclosed=false;
			if($a["status2"]=='1') {
				$orderclosed=true;
			}
			$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."rolevacancy where site_id=".$subobj);
			$b = mysql_fetch_array($result2);
			if($a["id"]!='' && $b[0]>0) {
				if($havelocats) {
					$query="SELECT t1.*, t2.name as locatname, t2.id as locatid FROM ".$prefix."roleslocat t2 LEFT JOIN ".$prefix."rolevacancy t1 ON t2.id=t1.locat WHERE t2.site_id=".$subobj.($locat>0?' and t2.id IN ('.getlocatchild($locat).')':'')." AND (t2.description!='' OR t1.id!='') order by FIELD(t2.id, ".$alllocats_ids."), t1.code ASC, t1.name ASC";
				}
				else {
					$query="SELECT t1.* FROM ".$prefix."rolevacancy t1 WHERE t1.site_id=".$subobj." order by t1.code ASC, t1.name ASC";
				}
				$result=mysql_query($query);
				$pagetitle=h1line(decode($a["title"]).' – cетка ролей');
				$content2.='';
				$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."roles WHERE site_id=".$subobj.($locat>0?' and locat='.$locat:'')." and todelete!=1 and todelete2!=1");
				$b=mysql_fetch_array($result2);
				if($b[0]>0) {
					$content2.='<center><span class="gui-btn"><span><a href="'.$server_absolute_path.'gameorders.php?game='.$subobj.'&orders=1&css='.$css.'">К списку всех поданных заявок</a></span></span></center><br>';
				}
				$content2.='';
				$content2.='
<table class="menutable">
<tr>
<td class="menu head_roles">Роль</td>
<td class="menu head_descr">Описание</td>
<td class="menu head_players" colspan=2>Игроки</td>
</tr>';

				$prevlocatid=-1;
				while($a=mysql_fetch_array($result)) {
					$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."roles WHERE vacancy=".$a["id"]." and status=3 and todelete!=1 and todelete2!=1 and site_id=".$subobj);
					$b=mysql_fetch_array($result2);
					$result5=mysql_query("SELECT COUNT(id) FROM ".$prefix."roles WHERE vacancy=".$a["id"]." and status!=4 and todelete!=1 and todelete2!=1 and site_id=".$subobj);
					$e=mysql_fetch_array($result5);
					unset($taken);
					if($a["taken"]!='') {
						$taken2='';
						$taken2=decode($a["taken"]);
						$taken2=eregi_replace(', ',',',$taken2);
						$taken=explode(',',$taken2);
						if($taken[0]=='') {
							unset($taken);
						}
						$b[0]+=count($taken);
					}
					unset($maybetaken);
					if($a["maybetaken"]!='') {
						$maybetaken2='';
						$maybetaken2=decode($a["maybetaken"]);
						$maybetaken2=str_replace(', ',',',$maybetaken2);
						$maybetaken=explode(',',$maybetaken2);
					}
					if($prevlocatid!=$a["locatid"] && $havelocats) {
						$prevlocatid=$a["locatid"];
						$content2.='
<tr><td class="locations" colspan=4>';
						if($a["locatid"]==0) {
							$content2.='Локация не определена';
						}
						else {
							$content2.=locatpath($a["locatid"]);
						}
						$content2.='</td></tr>';
						if($a["locatid"]!=0) {
							$result3=mysql_query("SELECT * FROM ".$prefix."roleslocat WHERE id=".$a["locatid"]);
							$c=mysql_fetch_array($result3);
							if(decode($c["description"])!='') {
								$content2.='<tr><td class="description" colspan=4>'.decode2($c["description"]).'</td></tr>';
							}
						}
					}
					$rows=1;
					if($c["rights"]==1) {
						$rows=0;
						if($b[0]<$a["kolvo"] && !$orderclosed) {
							$rows+=1;
						}
						if($b[0]>0) {
							$rows+=1;
						}
						if($b[0]<$a["kolvo"]) {
							$result4=mysql_query("SELECT COUNT(id) FROM ".$prefix."roles WHERE vacancy=".$a["id"]." and (status=2 || status=1) and todelete!=1 and todelete2!=1 and site_id=".$subobj);
							$d=mysql_fetch_array($result4);
							if($d[0]+count($maybetaken)>0) {
								$rows+=1;
							}
						}
					}
					else {
	 					if($b[0]<$a["kolvo"]) {
							if(!$showonlyacceptedroles) {
								$rows=$e[0]+count($taken)+count($maybetaken);
							}
							else {
								$rows=$b[0];
							}
							if(!$orderclosed) {
								$rows+=1;
							}
						}
						else {
							$rows=$b[0];
						}
					}
					if($rows==0) {
						$rows=1;
					}
					if($a["id"]!='') {
						$content2.='
<tr>
<td';
						if(decode($a["content"])=='') {
							$content2.=' colspan=2';
						}
						if($a["team"]==1 && $a["teamkolvo"]>0) {
							$teamkolvo=' (команда до '.$a["teamkolvo"].' человек)';
						}
						else {
							$teamkolvo='';
						}
						if($rows>1) {
							$content2.=' rowspan='.$rows;
						}
						$content2.='>
<a href="'.$server_absolute_path.'gameorders.php?game='.$subobj.'&id='.$a["id"].'&css='.$css.'">'.decode($a["name"]).$teamkolvo.'</a>';
if($ord_admin) {
						$content2.=' (<a href="'.$server_absolute_path_site.'roles/'.$a["id"].'/site='.$subobj.'">изменить</a>)';
					}
$content2 .= '</td>';
						if(decode($a["content"])!='') {
							$content2.='
<td class="roledescription"';
							if($rows>1) {
								$content2.=' rowspan='.$rows;
							}
							$content2.='>
'.decode($a["content"]).'
</td>';
						}
						$newstring=false;
						if($b[0]<$a["kolvo"] && !$orderclosed) {
							if($_SESSION["user_id"]) {
								$content2.='<td colspan=2><a href="'.$server_absolute_path.'order/act=add&subobj='.$subobj.'&roletype='.$a["team"].'&wantrole='.$a["id"].'" target="_blank">';
							}
							else {
								$content2.='<td colspan=2><a href="'.$server_absolute_path.'register/redirectobj=order&redirectid='.$subobj.'&redirectparams=roletype:'.$a["team"].'*wantrole:'.$a["id"].'" target="_blank">';
							}
							$content2.='подать заявку</a>';
							if($a["kolvo"]-$b[0]>1) {
								$content2.='&nbsp;(до '.($a["kolvo"]-$b[0]).')';
							}
							$content2.='</td></tr>';
							$newstring=true;
						}
						if($c["rights"]==1) {
		                    if($b[0]+count($taken)>0) {
		                    	if($newstring) {
		                    		$content2.='<tr>';
		                    	}
		                    	$content2.='<td colspan=2>Набрано: '.($b[0]+count($taken)).'</td></tr>';
		                    	$newstring=true;
		                    }
		                    if($b[0]+count($taken)<$a["kolvo"]) {
			                    if($d[0]+count($maybetaken)>0) {
			                    	if($newstring) {
		                    			$content2.='<tr>';
		                   			}
			                    	$content2.='<td colspan=2>Предварительно набрано: '.($d[0]+count($maybetaken)).'</td></tr>';
			                    	$newstring=true;
			                    }
			                    else {
			                    	if($newstring) {
		                    			$content2.='<tr>';
		                   			}
			                    	$content2.='<td colspan=2>Набрано: '.$b[0].'</td></tr>';
			                    	$newstring=true;
			                    }
			                }
						}
						else {
							$result3=mysql_query("SELECT player_id, sorter FROM ".$prefix."roles WHERE vacancy=".$a["id"]." and status=3 and todelete!=1 and todelete2!=1 and site_id=".$subobj);
							while($c=mysql_fetch_array($result3)) {
								$result4=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$c["player_id"]);
								$d=mysql_fetch_array($result4);
								if($newstring) {
		                    		$content2.='<tr>';
		                    	}
								if($a["kolvo"]>1 || $b[0]>1) {
									$content2.='<td>'.decode($c["sorter"]).'</td><td>'.usname($d,true,true).'</td></tr>';
								}
								else {
									$content2.='<td colspan=2>'.usname($d,true,true).'</td></tr>';
								}
								$newstring=true;
							}
							if($taken[0]!='') {
								for($i=0;$i<count($taken);$i++) {
									if($newstring) {
		                    			$content2.='<tr>';
		                    		}
									$content2.='<td colspan=2>'.$taken[$i].'</td></tr>';
									$newstring=true;
								}
							}

							if($b[0]<$a["kolvo"] && !$showonlyacceptedroles) {
								$result3=mysql_query("SELECT player_id, sorter FROM ".$prefix."roles WHERE vacancy=".$a["id"]." and (status=2 || status=1) and todelete!=1 and todelete2!=1 and site_id=".$subobj);
								while($c=mysql_fetch_array($result3)) {
									$result4=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$c["player_id"]);
									$d=mysql_fetch_array($result4);
									if($newstring) {
			                    		$content2.='<tr>';
			                    	}
									if($a["kolvo"]>1) {
										$content2.='<td>'.decode($c["sorter"]).'</td><td>'.usname($d,true,true).'?</td></tr>';
									}
									else {
										$content2.='<td colspan=2>'.usname($d,true,true).'?</td></tr>';
									}
									$newstring=true;
								}
								if($a["maybetaken"]!='') {
									for($i=0;$i<count($maybetaken);$i++) {
										if($newstring) {
			                    			$content2.='<tr>';
			                    		}
										$content2.='<td colspan=2>'.$maybetaken[$i].'?</td></tr>';
										$newstring=true;
									}
								}
							}
							if($b[0]==0 && $orderclosed && count($maybetaken)==0) {
								$content2.='<td colspan=2>&nbsp;</td></tr>';
							}
						}
					}
				}

				$content2.='
</table>';
			}
		}
	}
	if($content2!='') {
		$content2='<div class="narrow">'.$content2.'</div>';
	}
	if($css=='internal') {
		$content2=str_replace('class="menutable"','style="border-spacing: 0px; border-collapse: collapse; padding: 0px; border: 0px; margin: 0px;"',$content2);
		$content2=str_replace('class="narrow"','style="margin: 0px; padding: 0px; font-family: Arial; font-size: 11pt; color: rgb(35, 31, 32);"',$content2);
		$content2=str_replace('class="menu"','style="border-top: 1px solid rgb(214, 214, 214); border-bottom: 1px solid rgb(214, 214, 214); background: rgb(246, 246, 246); width: auto; color: #A12600; text-decoration: none; font-size: 10.5pt; font-weight: bold;"',$content2);
		$content2=str_replace('class="menu head_roles"','style="border-top: 1px solid rgb(214, 214, 214); border-bottom: 1px solid rgb(214, 214, 214); background: rgb(246, 246, 246); width: auto; color: #A12600; text-decoration: none; font-size: 10.5pt; font-weight: bold;"',$content2);
		$content2=str_replace('class="menu head_descr"','style="border-top: 1px solid rgb(214, 214, 214); border-bottom: 1px solid rgb(214, 214, 214); background: rgb(246, 246, 246); width: 70%; color: #A12600; text-decoration: none; font-size: 10.5pt; font-weight: bold;"',$content2);
		$content2=str_replace('class="menu head_players"','style="border-top: 1px solid rgb(214, 214, 214); border-bottom: 1px solid rgb(214, 214, 214); background: rgb(246, 246, 246); width: auto; color: #A12600; text-decoration: none; font-size: 10.5pt; font-weight: bold;"',$content2);
		$content2=str_replace('class="locations"','style="border-bottom: 1px solid rgb(214, 214, 214); padding: 13px; text-align: center; font-size: 1.05em; font-weight: bold; background-color: rgb(246, 246, 246);"',$content2);
		$content2=str_replace('class="description"','style="border-bottom: 1px solid rgb(214, 214, 214); padding: 3px; vertical-align: top; background-color: rgb(246, 246, 246); text-align: justify;"',$content2);
		$content2=str_replace('<tr','<tr style="vertical-align: top;"',$content2);
		$content2=str_replace('td rowspan','td style="border-bottom: 1px solid rgb(214, 214, 214); padding: 3px;" rowspan',$content2);
		$content2=str_replace('td colspan','td style="border-bottom: 1px solid rgb(214, 214, 214); padding: 3px;" colspan',$content2);
	}
}

if(!$include) {
	$content='<!doctype html public \'-//w3c//dtd html 4.01//en\' \'http://www.w3.org/tr/html4/strict.dtd\'>
<html>
<head>
<title>Ролевые игры, хостинг и система заявок</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="author" content="©еть">';
	if($css!='internal') {
		$content.='
<link rel="stylesheet" type="text/css" href="'.$css.'">';
	}
	$content.='
</head>

<body>
'.$content2.'
</body>
</html>';
	print($content);
}
else {
	print($content2);
}

stop_mysql();
# Разрыв соединения с MySQL-сервером

?>