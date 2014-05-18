<?php
	if($action=="exportroles") {
    require 'orders_excel_inc.php';
	}
	elseif($action=="exporttooffline") {
		$result=mysql_query("SELECT * from {$prefix}allrights2 where user_id=".$_SESSION['user_sid']." and site_id=".$_SESSION["siteid"]." and (rights=1 || rights=2)");
		$a=mysql_fetch_array($result);
		if($a["id"]!='' || $_SESSION["admin"]) {
			$result=mysql_query("SELECT * from {$prefix}sites where id=".$_SESSION["siteid"]);
			$a=mysql_fetch_array($result);

			//в массивах структуры булево означает, что эта колонка типа int
			$data='<?php
$sites_structure=Array(Array("allrpg_id",true),Array("title"),Array("sorter",true),Array("sorter2",true),Array("money"),Array("date",true));
$sites[]=Array(
'.$a["id"].',
"'.$a["title"].'",
'.$a["sorter"].',
'.$a["sorter2"].',
"'.$a["money"].'",
'.$a["date"].'
);

$roles_structure=Array(Array("allrpg_id",true),Array("site_id",true),Array("player"),Array("playerprofile"),Array("gender",true),Array("em"),Array("em2"),Array("phone2"),Array("icq"),Array("skype"),Array("jabber"),Array("birth"),Array("city"),Array("sickness"),Array("player_changed"),Array("team",true),Array("vacancy",true),Array("money"),Array("moneydone"),Array("sorter"),Array("locat",true),Array("allinfo"),Array("status",true),Array("todelete",true),Array("todelete2",true),Array("alltold"),Array("roleteamkolvo",true),Array("datesent"),Array("date",true));
';
			$result=mysql_query("SELECT * from {$prefix}roles where site_id=".$_SESSION["siteid"]);
			while($a=mysql_fetch_array($result)) {
				$data.='
$roles[]=Array(
'.$a["id"].',
'.$_SESSION["siteid"].',
';
				$result2=mysql_query("SELECT * from {$prefix}users where id=".$a["player_id"]);
				$b=mysql_fetch_array($result2);
				$data.='"'.usname($b,true).'",
"'.$server_absolute_path_info.'users/'.$b["sid"].'/",
'.$b["gender"].',
"';
				if(strpos($b["hidesome"],'-2-')===false) {
					$data.=$b["em"];
				}
				$data.='",
"';
                      if(strpos($b["hidesome"],'-3-')===false) {
					$data.=$b["em2"];
				}
				$data.='",
"'.$b["phone2"].'",
"'.$b["icq"].'",
"'.$b["skype"].'",
"'.$b["jabber"].'",
"'.$b["birth"].'",
"';
				$result3=mysql_query("SELECT * from {$prefix}geography where id=".$b["city"]);
				$c=mysql_fetch_array($result3);
				$city=decode($c["name"]);
				$result3=mysql_query("SELECT * from {$prefix}geography where id=".$c["parent"]);
				$c=mysql_fetch_array($result3);
				$city=decode($c["name"]).' – '.$city;
				$result3=mysql_query("SELECT * from {$prefix}geography where id=".$c["parent"]);
				$c=mysql_fetch_array($result3);
				$city=decode($c["name"]).' – '.$city;
				$data.=$city.'",
"'.$b["sickness"].'",
"0",
'.$a["team"].',
'.$a["vacancy"].',
"'.$a["money"].'",
"'.$a["moneydone"].'",
"'.$a["sorter"].'",
'.$a["locat"].',
"'.$a["allinfo"].'",
'.$a["status"].',
'.$a["todelete"].',
'.$a["todelete2"].',
"'.$a["alltold"].'",
'.$a["roleteamkolvo"].',
"'.$a["datesent"].'",
'.$a["date"].'
);
';
			}
			$data.='
$rolescomments_structure=Array(Array("allrpg_id",true),Array("site_id",true),Array("role_id",true),Array("user"),Array("type",true),Array("content"),Array("date",true));
';
			$result=mysql_query("SELECT * from {$prefix}rolescomments where site_id=".$_SESSION["siteid"]);
			while($a=mysql_fetch_array($result)) {
				$data.='
$rolescomments[]=Array(
'.$a["id"].',
'.$_SESSION["siteid"].',
'.$a["role_id"].',
';
				$result2=mysql_query("SELECT * from {$prefix}users where id=".$a["user_id"]);
				$b=mysql_fetch_array($result2);
				$data.='"'.usname($b,true).'",
'.$a["type"].',
"'.$a["content"].'",
'.$a["date"].'
);
';
			}
			$data.='
$roleslocat_structure=Array(Array("allrpg_id",true),Array("parent",true),Array("name"),Array("code",true),Array("content"),Array("description"),Array("site_id",true),Array("date",true));
';
			$result=mysql_query("SELECT * from {$prefix}roleslocat where site_id=".$_SESSION["siteid"]);
			while($a=mysql_fetch_array($result)) {
				$data.='
$roleslocat[]=Array(
'.$a["id"].',
'.$a["parent"].',
"'.$a["name"].'",
'.$a["code"].',
"'.$a["content"].'",
"'.$a["description"].'",
'.$_SESSION["siteid"].',
'.$a["date"].'
);
';
			}
			$data.='
$rolevacancy_structure=Array(Array("allrpg_id",true),Array("locat",true),Array("team"),Array("name"),Array("code",true),Array("kolvo",true),Array("teamkolvo",true),Array("maybetaken"),Array("taken"),Array("content"),Array("site_id",true),Array("date",true));
';
			$result=mysql_query("SELECT * from {$prefix}rolevacancy where site_id=".$_SESSION["siteid"]);
			while($a=mysql_fetch_array($result)) {
				$data.='
$rolevacancy[]=Array(
'.$a["id"].',
'.$a["locat"].',
"'.$a["team"].'",
"'.$a["name"].'",
'.$a["code"].',
'.$a["kolvo"].',
'.$a["teamkolvo"].',
"'.$a["maybetaken"].'",
"'.$a["taken"].'",
"'.$a["content"].'",
'.$_SESSION["siteid"].',
'.$a["date"].'
);
';
			}
			$data.='
$roleslinks_structure=Array(Array("allrpg_id",true),Array("parent",true),Array("descr"),Array("site_id",true),Array("vacancies"),Array("hideother"),Array("name"),Array("type",true),Array("content"),Array("roles"),Array("roles2"),Array("date",true));
';
			$result=mysql_query("SELECT * from {$prefix}roleslinks where site_id=".$_SESSION["siteid"]);
			while($a=mysql_fetch_array($result)) {
				$data.='
$roleslinks[]=Array(
'.$a["id"].',
'.$a["parent"].',
"'.$a["descr"].'",
'.$_SESSION["siteid"].',
"'.$a["vacancies"].'",
"'.$a["hideother"].'",
"'.$a["name"].'",
'.$a["type"].',
"'.$a["content"].'",
"'.$a["roles"].'",
"'.$a["roles2"].'",
'.$a["date"].'
);
';
			}
			$data.='
$rolefields_structure=Array(Array("allrpg_id",true),Array("site_id",true),Array("rolename"),Array("roletype"),Array("rolemustbe"),Array("roledefault"),Array("rolerights",true),Array("rolehelp"),Array("rolevalues"),Array("rolecode",true),Array("rolewidth",true),Array("roleheight",true),Array("filter"),Array("team"),Array("date",true));
';
			$result=mysql_query("SELECT * from {$prefix}rolefields where site_id=".$_SESSION["siteid"]);
			while($a=mysql_fetch_array($result)) {
				$data.='
$rolefields[]=Array(
'.$a["id"].',
'.$_SESSION["siteid"].',
"'.$a["rolename"].'",
"'.$a["roletype"].'",
"'.$a["rolemustbe"].'",
"'.$a["roledefault"].'",
'.$a["rolerights"].',
"'.$a["rolehelp"].'",
"'.$a["rolevalues"].'",
'.$a["rolecode"].',
'.$a["rolewidth"].',
'.$a["roleheight"].',
"'.$a["filter"].'",
"'.$a["team"].'",
'.$a["date"].'
);
';
			}
			$data.='
?>';
			$result=mysql_query("SELECT * from {$prefix}sites where id=".$_SESSION["siteid"]);
			$a=mysql_fetch_array($result);

			header("Content-type: text/html");
			if($a["path"]!='') {
				header("Content-Disposition: attachment; filename=".decode($a["path"])."_".date('d_m_Y_H_i', time()).".php");
			}
			else {
				header("Content-Disposition: attachment; filename=project".$a["id"]."_".date('d_m_Y_H_i', time()).".php");
			}
			header("Pragma: no-cache");
			header("Expires: 0");
			print "$header$data";
			exit;
		}
	}
	elseif($action=="exporttobrain") {
		$result=mysql_query("SELECT * from {$prefix}allrights2 where user_id=".$_SESSION['user_sid']." and site_id=".$_SESSION["siteid"]." and (rights=1 || rights=2)");
		$a=mysql_fetch_array($result);
		if($a["id"]!='' || $_SESSION["admin"]) {
			$data='<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE BrainData SYSTEM "http://www.thebrain.com/dtd/BrainData1.dtd">
<BrainData>
<Thoughts>';
			function createguid($objname,$objid) {
               	global
               		$guidbase,
               		$allguids;

               	$mess='1234567890ABCDEF';

				srand((double)microtime()*1000000);
				$i = 1;
				while ($i <= 8) {
					$num = rand() % 16;
					$tmp = substr($mess, $num, 1);
					$pass .= $tmp;
					$i++;
				}
				$pass.='-';
				srand((double)microtime()*1000000);
				$i = 1;
				while ($i <= 4) {
					$num = rand() % 16;
					$tmp = substr($mess, $num, 1);
					$pass .= $tmp;
					$i++;
				}
				$pass.='-';
				srand((double)microtime()*1000000);
				$i = 1;
				while ($i <= 4) {
					$num = rand() % 16;
					$tmp = substr($mess, $num, 1);
					$pass .= $tmp;
					$i++;
				}
				$pass.='-';
				srand((double)microtime()*1000000);
				$i = 1;
				while ($i <= 4) {
					$num = rand() % 16;
					$tmp = substr($mess, $num, 1);
					$pass .= $tmp;
					$i++;
				}
				$pass.='-';
				srand((double)microtime()*1000000);
				$i = 1;
				while ($i <= 12) {
					$num = rand() % 16;
					$tmp = substr($mess, $num, 1);
					$pass .= $tmp;
					$i++;
				}
				if($guidbase[$pass]) {
					$guidresult=createguid($objname,$objid);
				}
				else {
					$guidresult=$pass;
					$guidbase[$pass]=true;
					if($objname!='') {
						$allguids[$objname][$objid]=$pass;
					}
				}
				return $guidresult;
			}
			function encodeforbrain($text) {
	           	$text=decode2($text);
           		$text=str_ireplace('<','&amp;lt;',$text);
           		$text=str_ireplace('>','&amp;gt;',$text);
           		$text=str_ireplace('"','&amp;quot;',$text);
           		$text=str_ireplace("'",'&amp;amp;',$text);
           		return $text;
			}
   			function makeentry($text,$parentguid) {
				global
					$entries;

     			$text='<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
</head>
<body>
'.$text.'
</body>
</html>';
				$text=encodeforbrain($text);

				$entries.='
<Entry>
	<guid>'.$createguid.'</guid>
	<EntryObjects>
		<EntryObject>
			<objectType>0</objectType>
			<objectID>'.$parentguid.'</objectID>
		</EntryObject>
	</EntryObjects>
	<body>'.$text.'
	</body>
	<creationDateTime>'.date("Y-m-d H:i:s").'.406</creationDateTime>
</Entry>';
				return true;
			}

   			function findguid($objname,$objid) {
				global
					$allguids;

				$result=$allguids[$objname][$objid];
				return $result;
			}

			$thoughts='';
			$links='';
			$entries='';

			$result=mysql_query("SELECT * from {$prefix}sites where id=".$_SESSION["siteid"]);
			$a=mysql_fetch_array($result);

			$thoughts.='
<Thought>
	<guid>'.createguid('locat',0).'</guid>
	<name>'.encodeforbrain($a["title"]).'</name>
	<creationDateTime>'.date("Y-m-d H:i:s").'.406</creationDateTime>
	<isType>false</isType>
	<color>0</color>
	<accessControlType>0</accessControlType>
</Thought>';
      		$thoughts.='
<Thought>
	<guid>'.createguid('links',0).'</guid>
	<name>Внешний сюжет</name>
	<creationDateTime>'.date("Y-m-d H:i:s").'.406</creationDateTime>
	<isType>false</isType>
	<color>0</color>
	<accessControlType>0</accessControlType>
</Thought>';
     		$links.='
<Link>
	<guid>'.createguid().'</guid>
	<idA>'.findguid('locat',0).'</idA>
	<idB>'.findguid('links',0).'</idB>
	<dir>3</dir>
	<creationDateTime>'.date("Y-m-d H:i:s").'.406</creationDateTime>
	<isType>false</isType>
	<color>0</color>
	<thickness>0</thickness>
	<meaning>0</meaning>
	<linkTypeID></linkTypeID>
</Link>';

			function exportalldatatobrain($parentobj,$siteid) {
				global
					$thoughts,
					$links,
					$entries,
					$guidbase;

				$result=mysql_query("SELECT * from {$prefix}roleslocat where site_id=".$siteid." and parent=".$parentobj);
				while($a=mysql_fetch_array($result)) {
    				$newguid=createguid('locat',$a["id"]);
        			$thoughts.='
<Thought>
	<guid>'.$newguid.'</guid>
	<name>'.encodeforbrain($a["name"]).'</name>
	<creationDateTime>'.date("Y-m-d H:i:s").'.406</creationDateTime>
	<isType>false</isType>
	<color>0</color>
	<accessControlType>0</accessControlType>
</Thought>';
      				$links.='
<Link>
	<guid>'.createguid().'</guid>
	<idA>'.findguid('locat',$parentobj).'</idA>
	<idB>'.$newguid.'</idB>
	<dir>1</dir>
	<creationDateTime>'.date("Y-m-d H:i:s").'.406</creationDateTime>
	<isType>false</isType>
	<color>0</color>
	<thickness>0</thickness>
	<meaning>0</meaning>
	<linkTypeID></linkTypeID>
</Link>';
      				exportalldatatobrain($a["id"],$siteid);
				}

				$result=mysql_query("SELECT * from {$prefix}rolevacancy where site_id=".$siteid." and locat=".$parentobj);
				while($a=mysql_fetch_array($result)) {
    				$newguid=createguid('vacancy',$a["id"]);
        			$thoughts.='
<Thought>
	<guid>'.$newguid.'</guid>
	<name>'.encodeforbrain($a["name"]).'</name>
	<creationDateTime>'.date("Y-m-d H:i:s").'.406</creationDateTime>
	<isType>false</isType>
	<color>0</color>
	<accessControlType>0</accessControlType>
</Thought>';
      				$links.='
<Link>
	<guid>'.createguid().'</guid>
	<idA>'.findguid('locat',$parentobj).'</idA>
	<idB>'.$newguid.'</idB>
	<dir>1</dir>
	<creationDateTime>'.date("Y-m-d H:i:s").'.406</creationDateTime>
	<isType>false</isType>
	<color>0</color>
	<thickness>0</thickness>
	<meaning>0</meaning>
	<linkTypeID></linkTypeID>
</Link>';
      			}

     			$result2=mysql_query("SELECT * from {$prefix}roles where site_id=".$siteid." and status<4 and locat=".$parentobj);
				while($b=mysql_fetch_array($result2)) {
					$result3=mysql_query("SELECT * from {$prefix}users where id=".$b["player_id"]);
					$c=mysql_fetch_array($result3);
     				$newguid=createguid('role',$b["id"]);
          			$thoughts.='
<Thought>
	<guid>'.$newguid.'</guid>
	<name>'.encodeforbrain($b["sorter"]." (".usname2($c).")").'</name>
	<creationDateTime>'.date("Y-m-d H:i:s").'.406</creationDateTime>
	<isType>false</isType>
	<color>';
	                if($b["status"]==3) {
	                    $thoughts.='ff00cc00';
	                }
	                else {
	                    $thoughts.='ffffff00';
	                }
	                $thoughts.='</color>
	<accessControlType>0</accessControlType>
</Thought>';
      				if($b["vacancy"]>0) {
      					$links.='
<Link>
	<guid>'.createguid().'</guid>
	<idA>'.findguid('vacancy',$b["vacancy"]).'</idA>
	<idB>'.$newguid.'</idB>
	<dir>1</dir>
	<creationDateTime>'.date("Y-m-d H:i:s").'.406</creationDateTime>
	<isType>false</isType>
	<color>0</color>
	<thickness>0</thickness>
	<meaning>0</meaning>
	<linkTypeID></linkTypeID>
</Link>';
   					}
   					elseif($b["locat"]>0) {
   						$links.='
<Link>
	<guid>'.createguid().'</guid>
	<idA>'.findguid('locat',$b["locat"]).'</idA>
	<idB>'.$newguid.'</idB>
	<dir>1</dir>
	<creationDateTime>'.date("Y-m-d H:i:s").'.406</creationDateTime>
	<isType>false</isType>
	<color>0</color>
	<thickness>0</thickness>
	<meaning>0</meaning>
	<linkTypeID></linkTypeID>
</Link>';
      				}
      				else {
      					$links.='
<Link>
	<guid>'.createguid().'</guid>
	<idA>'.findguid('locat',0).'</idA>
	<idB>'.$newguid.'</idB>
	<dir>1</dir>
	<creationDateTime>'.date("Y-m-d H:i:s").'.406</creationDateTime>
	<isType>false</isType>
	<color>0</color>
	<thickness>0</thickness>
	<meaning>0</meaning>
	<linkTypeID></linkTypeID>
</Link>';
      				}
      				$textentry='
<b>Взнос сдан</b>: ';
					if($b["moneydone"]=='1') {
						$textentry.='да';
					}
					else {
						$textentry.='нет';
					}
					$textentry.='<br>
<b>Взнос</b>: '.$b["money"];
      				makeentry($textentry,$newguid);
				}

				if($parentobj==0) {
					$result=mysql_query("SELECT * from {$prefix}roleslinks where site_id=".$siteid." and parent=0");
					while($a=mysql_fetch_array($result)) {
                        $newguid=createguid('links',$a["id"]);
                        $thisguidname=encodeforbrain($a["name"]);
                        $thoughts.='
<Thought>
	<guid>'.$newguid.'</guid>
	<name>'.$thisguidname.'</name>
	<creationDateTime>'.date("Y-m-d H:i:s").'.406</creationDateTime>
	<isType>false</isType>
	<color>0</color>
	<accessControlType>0</accessControlType>
</Thought>';
      					$textentry='
'.$a["descr"];
      					makeentry($textentry,$newguid);

						$result3=mysql_query("SELECT * from {$prefix}roleslinks where site_id=".$siteid." and parent=".$a["id"]);
						while($c=mysql_fetch_array($result3)) {
							$newguid2=createguid('links',$b["id"]);
	                        $thisguidname=encodeforbrain(substr(strip_tags(decode($c["content"])),0,15).'...');
	                        $thoughts.='
<Thought>
	<guid>'.$newguid2.'</guid>
	<name>'.$thisguidname.'</name>
	<creationDateTime>'.date("Y-m-d H:i:s").'.406</creationDateTime>
	<isType>false</isType>
	<color>0</color>
	<accessControlType>0</accessControlType>
</Thought>';
      						$textentry='
'.strip_tags(decode($c["content"]));
							makeentry($textentry,$newguid2);

							$links.='
<Link>
	<guid>'.createguid().'</guid>
	<idA>'.findguid('links',$a["id"]).'</idA>
	<idB>'.$newguid2.'</idB>
	<dir>3</dir>
	<creationDateTime>'.date("Y-m-d H:i:s").'.406</creationDateTime>
	<isType>false</isType>
	<color>0</color>
	<thickness>0</thickness>
	<meaning>0</meaning>
	<linkTypeID></linkTypeID>
</Link>';

							unset($roles);
							unset($roles2);
                            $roles=substr($c["roles"],1,strlen($c["roles"])-2);
							$roles2=substr($c["roles2"],1,strlen($c["roles2"])-2);
							$roles=explode('-',$roles);
							$roles2=explode('-',$roles2);
							foreach($roles as $r) {
								if(strpos($r,'all')!==false) {
									$result2=mysql_query("SELECT * FROM {$prefix}rolevacancy WHERE site_id=".$siteid." and id=".str_replace('all','',$r));
									$b=mysql_fetch_array($result2);
									if($b["id"]!='') {
										$result5=mysql_query("SELECT * from {$prefix}roles where vacancy=".$b["id"]." and site_id=".$siteid);
										while($e=mysql_fetch_array($result5)) {
											if(strpos($c["roles"],'-'.$e["id"].'-')!==false || strpos($c["roles"],'-'.$r.'-')!==false) {
                                            	$links.='
<Link>
	<guid>'.createguid().'</guid>
	<idA>'.findguid('role',$e["id"]).'</idA>
	<idB>'.$newguid2.'</idB>
	<dir>3</dir>
	<creationDateTime>'.date("Y-m-d H:i:s").'.406</creationDateTime>
	<isType>false</isType>
	<color>0</color>
	<thickness>0</thickness>
	<meaning>0</meaning>
	<linkTypeID></linkTypeID>
</Link>';
											}
										}
									}
									elseif($r==0) {
										$links.='
<Link>
	<guid>'.createguid().'</guid>
	<idA>'.findguid('links',0).'</idA>
	<idB>'.$newguid2.'</idB>
	<dir>3</dir>
	<creationDateTime>'.date("Y-m-d H:i:s").'.406</creationDateTime>
	<isType>false</isType>
	<color>0</color>
	<thickness>0</thickness>
	<meaning>0</meaning>
	<linkTypeID></linkTypeID>
</Link>';
									}
									else {
										// ошибка: удаленная роль
									}
								}
								else {
									$query="SELECT * from {$prefix}roles where id=".$r." and site_id=".$_SESSION["siteid"];
									$result2=mysql_query($query);
									$b=mysql_fetch_array($result2);
									if($b["id"]!='') {
										$links.='
<Link>
	<guid>'.createguid().'</guid>
	<idA>'.findguid('role',$b["id"]).'</idA>
	<idB>'.$newguid2.'</idB>
	<dir>3</dir>
	<creationDateTime>'.date("Y-m-d H:i:s").'.406</creationDateTime>
	<isType>false</isType>
	<color>0</color>
	<thickness>0</thickness>
	<meaning>0</meaning>
	<linkTypeID></linkTypeID>
</Link>';
									}
								}
							}
							foreach($roles2 as $r) {
								if(strpos($r,'all')!==false) {
									$result2=mysql_query("SELECT * FROM {$prefix}rolevacancy WHERE site_id=".$siteid." and id=".str_replace('all','',$r));
									$b=mysql_fetch_array($result2);
									if($b["id"]!='') {
										$result5=mysql_query("SELECT * from {$prefix}roles where vacancy=".$b["id"]." and site_id=".$siteid);
										while($e=mysql_fetch_array($result5)) {
											if(strpos($c["roles2"],'-'.$e["id"].'-')!==false || strpos($c["roles2"],'-'.$r.'-')!==false) {
                                            	$links.='
<Link>
	<guid>'.createguid().'</guid>
	<idA>'.findguid('role',$e["id"]).'</idA>
	<idB>'.$newguid2.'</idB>
	<dir>3</dir>
	<creationDateTime>'.date("Y-m-d H:i:s").'.406</creationDateTime>
	<isType>false</isType>
	<color>0</color>
	<thickness>0</thickness>
	<meaning>0</meaning>
	<linkTypeID></linkTypeID>
</Link>';
											}
										}
									}
									elseif($r==0) {
										$links.='
<Link>
	<guid>'.createguid().'</guid>
	<idA>'.findguid('links',0).'</idA>
	<idB>'.$newguid2.'</idB>
	<dir>3</dir>
	<creationDateTime>'.date("Y-m-d H:i:s").'.406</creationDateTime>
	<isType>false</isType>
	<color>0</color>
	<thickness>0</thickness>
	<meaning>0</meaning>
	<linkTypeID></linkTypeID>
</Link>';
									}
									else {
										// ошибка: удаленная роль
									}
								}
								else {
									$query="SELECT * from {$prefix}roles where id=".$r." and site_id=".$_SESSION["siteid"];
									$result2=mysql_query($query);
									$b=mysql_fetch_array($result2);
									if($b["id"]!='') {
										$links.='
<Link>
	<guid>'.createguid().'</guid>
	<idA>'.findguid('role',$b["id"]).'</idA>
	<idB>'.$newguid2.'</idB>
	<dir>3</dir>
	<creationDateTime>'.date("Y-m-d H:i:s").'.406</creationDateTime>
	<isType>false</isType>
	<color>0</color>
	<thickness>0</thickness>
	<meaning>0</meaning>
	<linkTypeID></linkTypeID>
</Link>';
									}
								}
							}
                        }
      				}
      			}
			}
			exportalldatatobrain(0,$_SESSION["siteid"]);

			$data.='
'.$thoughts.'
</Thoughts>
  <Links>
  	'.$links.'
  </Links>
  <Entries>
  	'.$entries.'
  </Entries>
</BrainData>';
			$result=mysql_query("SELECT * from {$prefix}sites where id=".$_SESSION["siteid"]);
			$a=mysql_fetch_array($result);

			header("Content-type: text/html");
			if($a["path"]!='') {
				header("Content-Disposition: attachment; filename=".decode($a["path"])."_".date('d_m_Y_H_i', time()).".xml");
			}
			else {
				header("Content-Disposition: attachment; filename=project".$a["id"]."_".date('d_m_Y_H_i', time()).".xml");
			}
			header("Pragma: no-cache");
			header("Expires: 0");
			print "$header$data";
			exit;
		}
	}
	elseif($action=="signtonew_on") {
		mysql_query("UPDATE {$prefix}allrights2 set signtonew='1' WHERE user_id=".$_SESSION["user_sid"]." and site_id=".$_SESSION["siteid"]);
		err("Вы будете получать уведомления по e-mail о новых заявках.");
	}
	elseif($action=="signtonew_off") {
		mysql_query("UPDATE {$prefix}allrights2 set signtonew='0' WHERE user_id=".$_SESSION["user_sid"]." and site_id=".$_SESSION["siteid"]);
		err("Вы более не будете получать уведомления по e-mail о новых заявках.");
	}
	elseif($action=="signtochange_on") {
		mysql_query("UPDATE {$prefix}allrights2 set signtochange='1' WHERE user_id=".$_SESSION["user_sid"]." and site_id=".$_SESSION["siteid"]);
		err("Вы будете получать уведомления по e-mail об изменения в заявках.");
	}
	elseif($action=="signtochange_off") {
		mysql_query("UPDATE {$prefix}allrights2 set signtochange='0' WHERE user_id=".$_SESSION["user_sid"]." and site_id=".$_SESSION["siteid"]);
		err("Вы более не будете получать уведомления по e-mail об изменениях в заявках.");
	}
	elseif($action=="signtocomments_on") {
		mysql_query("UPDATE {$prefix}allrights2 set signtocomments='1' WHERE user_id=".$_SESSION["user_sid"]." and site_id=".$_SESSION["siteid"]);
		err("Вы будете получать уведомления по e-mail о новых комментариях в заявках.");
	}
	elseif($action=="signtocomments_off") {
		mysql_query("UPDATE {$prefix}allrights2 set signtocomments='0' WHERE user_id=".$_SESSION["user_sid"]." and site_id=".$_SESSION["siteid"]);
		err("Вы более не будете получать уведомления по e-mail о новых комментариях в заявках.");
	}
	elseif($action=="viewdeleted_on") {
		$_SESSION["viewdeleted"]=true;
		err("Вы просматриваете только удаленные заявки.");
	}
	elseif($action=="viewdeleted_off") {
		unset($_SESSION["viewdeleted"]);
		err("Вы просматриваете все заявки, кроме удаленных.");
	}
	elseif($action=="newplayer") {
		$result2=mysql_query("SELECT * FROM {$prefix}roles WHERE id=".$id);
		$b = mysql_fetch_array($result2);

		$sid=encode($_REQUEST["sid"]);
		$result6=mysql_query("SELECT * from {$prefix}users where sid=".$sid);
		$e=mysql_fetch_array($result6);
		if($e["id"]!='' && $e["id"]!=$b["player_id"]) {
			mysql_query("UPDATE {$prefix}roles set new_player_sid=".$sid.", new_player_deny=0 where id=".$id);
			err("Заявка предложена пользователю «".usname($e,true)."».");
		}
		elseif($e["id"]==$b["player_id"]) {
			mysql_query("UPDATE {$prefix}roles set new_player_sid=0, new_player_deny=0 where id=".$id);
			err("Предложение заявки другому игроку было отменено.");
		}
		else {
			err_red("Пользователя с таким ИНП не существует.");
		}
	}
	elseif($action=="comment_add") {
		$comment_content=encode($_REQUEST["content"]);
		$comment_type=encode($_REQUEST["type"]);
		if($comment_content!='' && ($comment_type==1 || $comment_type==2)) {
			$result3=mysql_query("SELECT * FROM {$prefix}rolescomments WHERE role_id=".$id." and user_id=".$_SESSION['user_id']." and content='".$comment_content."' and date>=".(time()-3600));
			$c = mysql_fetch_array($result3);
			if($c["id"]!='') {
				err_red("Заблокировано повторное сохранение комментария.");
			}
			else {
        $targets = get_notification_targets($_SESSION["siteid"], $a["locat"], 'signtocomments', $_SESSION["user_sid"]);
        $target_emails = array();
        foreach ($targets as $target)
        {
          $target_emails[] = $target['em'];
        }
        
				require_once($server_inner_path.$direct."/classes/base_mails.php");
				$result=mysql_query("SELECT * from {$prefix}roles where id=".$id." and site_id=".$_SESSION["siteid"]);
				$a=mysql_fetch_array($result);
				if($comment_type==1 && $a["todelete"]!=1) {
					mysql_query("INSERT into {$prefix}rolescomments (site_id, role_id, user_id, type, content, date) values (".$_SESSION["siteid"].", ".$id.", ".$_SESSION["user_id"].", ".$comment_type.", '".$comment_content."', ".time().")");
					$comment_id=mysql_insert_id($link);
					mysql_query("INSERT into {$prefix}rolescommentsread (role_id, user_id, comment_id, date) values (".$id.", ".$_SESSION["user_id"].", ".$comment_id.", ".time().")");

					$result6=mysql_query("SELECT * from {$prefix}users where id=".$_SESSION['user_id']);
					$e=mysql_fetch_array($result6);
					$myname=usname($e, true);
					$myemail=decode($e["em"]);

					$result2=mysql_query("SELECT * from {$prefix}users where id=".$a["player_id"]);
					$b=mysql_fetch_array($result2);

					$contactemail=$b["em"];
					$subject='Вашу заявку на allrpg.info откомментировал мастер';

					$message='Добрый день.
Вашу заявку на allrpg.info откомментировал мастер.

'.decode($comment_content).'

Ссылка: '.$server_absolute_path.'order/'.$id.'/ (вы должны быть залогинены на allrpg.info).';

					send_mail($myname, $myemail, $contactemail, $subject, $message);
				

					err("Комментарий успешно добавлен, игроку отправлено e-mail оповещение. Уведомлены: " . implode(', ', $target_emails));
				}
				elseif($comment_type==1 && $a["todelete"]==1) {
					err_red("Комментарий не записан, т.к. игрок удалил у себя эту заявку.");
				}
				elseif($comment_type==2) {
					mysql_query("INSERT into {$prefix}rolescomments (site_id, role_id, user_id, type, content, date) values (".$_SESSION["siteid"].", ".$id.", ".$_SESSION["user_id"].", ".$comment_type.", '".$comment_content."', ".time().")");
					$comment_id=mysql_insert_id($link);
					mysql_query("INSERT into {$prefix}rolescommentsread (role_id, user_id, comment_id, date) values (".$id.", ".$_SESSION["user_id"].", ".$comment_id.", ".time().")");
					err("Комментарий успешно добавлен, отправлены e-mail оповещения. Уведомлены: " . implode(', ', $target_emails));
				}

    			$result6=mysql_query("SELECT * from {$prefix}users where id=".$_SESSION['user_id']);
				$e=mysql_fetch_array($result6);
				$myname=usname($e, true);
				$myemail=decode($e["em"]);

				$result2=mysql_query("SELECT * from {$prefix}sites where id=".$_SESSION["siteid"]);
				$b=mysql_fetch_array($result2);

				$subject='Комментарий к заявке «'.decode($a["sorter"]).'» проекта «'.decode($b["title"]).'»';

				$result2=mysql_query("SELECT * from {$prefix}users where id=".$a["player_id"]);
				$b=mysql_fetch_array($result2);

				$message='Добрый день.
Заявку «'.decode($a["sorter"]).'» игрока «'.usname($b,true).'» откомментировал мастер «'.$myname.'».

'.decode($comment_content).'

Ссылка: '.$server_absolute_path_site.'orders/'.$id.'/site='.$_SESSION["siteid"];

        foreach ($target_emails as $contactemail)
        {
          send_mail($myname, $myemail, $contactemail, $subject, $message);
        }
			}
			$comment_content='';
			$comment_type=0;
		}
		else {
			err_red("Неверно заполнены поля комментария.");
			$comment_trouble=true;
		}
	}
?>