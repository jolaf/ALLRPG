<?php
if(file_exists('../allrpg.info/db.inc')) {
	$path='../allrpg.info/';
}
else {
	$path='../all-main/';
}
include_once($path.'db.inc');
include_once($path.'classes_objects_allrpg.php');
include_once($path.$direct.'/classes/classes_objects.php');
session_start();
start_mysql();

if($dynrequest==1) {
	dynamic_err(array(),'submit');
}

if(isset($_REQUEST["roles"])) {
	$roles=Array();
	$result=mysql_query("SELECT * FROM ".$prefix."roles where site_id=".$_SESSION["siteid"]." order by team, sorter");
	while($a=mysql_fetch_array($result)) {
		if(encode($_POST["roles"][$a["id"]])=='on') {
			$roles[]=$a["id"];
		}
	}
	$result=mysql_query("SELECT * FROM ".$prefix."sites where id=".$_SESSION["siteid"]);
	$a=mysql_fetch_array($result);
	$docs='<div style="float: left; margin-right: 10px;">';
	if(encode($_POST["doc"])==1) {
		$docs.=decode($a["docs"]);
	}
	elseif(encode($_POST["doc"])==2) {
		$docs.=decode($a["docs2"]);
	}
	elseif(encode($_POST["doc"])==3) {
		$docs.=decode($a["docs3"]);
	}
	$docs.='</div>';

	$content='<html>
<head>
<title>Генератор аусвайсов</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="'.$server_absolute_path.'libraries/ckeditor/contents.css">
</head>

<body>';

    function locatpath($id) {
		global
			$prefix,
			$_SESSION;

		$result=mysql_query("SELECT * FROM ".$prefix."roleslocat WHERE id=".$id." and site_id=".$_SESSION["siteid"]);
		$a=mysql_fetch_array($result);
		if($a["id"]!='') {
			if($a["parent"]==0) {
				$return=decode($a["name"]);
			}
			else {
				$return=locatpath($a["parent"]);
				$return.=' –» '.decode($a["name"]);
			}
		}
		else {
			$return='не указана';
		}
		return($return);
	}

	$locatpermit=make5fieldtree(false,$prefix."roleslocat","parent",0," AND site_id=".$_SESSION["siteid"],"code asc, name asc",0,"id","name",1000000);

    $rolefields=virtual_structure("SELECT * from ".$prefix."rolefields where site_id=".$_SESSION["siteid"]." order by rolecode","allinfo","role");
    $rolefields[]=Array(
			'name'	=>	"locat",
			'sname'	=>	"Локация",
			'type'	=>	"select",
			'values'	=>	$locatpermit,
			'read'	=>	1,
			'write'	=>	100000,
	);
	$rolefields[]=Array(
			'name'	=>	"id",
			'sname'	=>	"№ заявки",
			'type'	=>	"text",
			'read'	=>	1,
			'write'	=>	100000,
	);
    $rolefields[]=Array(
			'name'	=>	"money",
			'sname'	=>	"Взнос",
			'type'	=>	"text",
			'read'	=>	1,
			'write'	=>	100000,
	);
	$rolefields[]=Array(
			'name'	=>	"moneydone",
			'sname'	=>	"Взнос сдан",
			'type'	=>	"checkbox",
			'read'	=>	1,
			'write'	=>	100000,
	);
	$rolefields[]=Array(
			'name'	=>	"fio",
			'sname'	=>	"Ф.И.О.",
			'type'	=>	"text",
			'read'	=>	1,
			'write'	=>	100000,
	);
	$rolefields[]=Array(
			'name'	=>	"nick",
			'sname'	=>	"Никнейм",
			'type'	=>	"text",
			'read'	=>	1,
			'write'	=>	100000,
	);
	$rolefields[]=Array(
			'name'	=>	"photo",
			'sname'	=>	"Фотография",
			'type'	=>	"file",
			'upload'	=>	4,
			'read'	=>	1,
			'write'	=>	100000,
	);
	$rolefields[]=Array(
			'name'	=>	"sickness",
			'sname'	=>	"Медицинские противопоказания",
			'type'	=>	"textarea",
			'read'	=>	1,
			'write'	=>	100000,
	);
	$rolefields[]=Array(
			'name'	=>	"rolelinks",
			'sname'	=>	"Связи",
			'type'	=>	"wysiwyg",
			'read'	=>	1,
			'write'	=>	100000,
	);
    for($i=0;$i<count($roles);$i++) {
    	$subobj=$_SESSION["siteid"];
		$id=$roles[$i];
		$result=mysql_query("SELECT * from ".$prefix."roles where id=".$id." and site_id=".$subobj);
		$a=mysql_fetch_array($result);
		$alllinks='';
		if($a["vacancy"]!=0) {
			$result3=mysql_query("SELECT * from ".$prefix."roleslinks where (roles LIKE '%-all".$a["vacancy"]."-%' OR roles LIKE '%-".$a["id"]."-%') and content!='' and site_id=".$subobj." and notready!='1' order by date desc");
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
		$rolelinks["rolelinks"]=$alllinks;

    	$result=mysql_query("SELECT * FROM ".$prefix."roles where id=".$roles[$i]);
		$a=mysql_fetch_array($result);
		$old=unmakevirtual($a['allinfo']);
		$result2=mysql_query("SELECT * FROM ".$prefix."users where id=".$a["player_id"]);
		$b=mysql_fetch_array($result2);
		$b=array_merge($b,$a,$old,$rolelinks);
		$tbd=$docs;
		foreach($rolefields as $f=>$v) {
			if($v['type']!='h1' && strpos($tbd,'['.$v["sname"].']')!==false && $v["name"]!="allinfo") {
				$obj_n=createElem($v);
				$obj_n->setVal($b);
				$tbd=str_replace('['.$v["sname"].']',$obj_n->draw(1,"read"),$tbd);
			}
		}
		$content.=$tbd;
    }

	$content.='
</body>
</html>
';
stop_mysql();
}
else {
	$content='<html>
<head>
<title>Генератор аусвайсов</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style>

</style>
</head>

<body>
Не выбрано ни одной заявки!
</body>
</html>
';
}

print($content);

?>