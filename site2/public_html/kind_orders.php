<?php
if($_SESSION["user_id"]!='' && $workrights["site"]["orders"]) {
	// заявки игроков

	if($id!='') {
		$result=mysql_query("SELECT * FROM ".$prefix."roles WHERE id=".$id." and site_id=".$_SESSION["siteid"]);
		$a_id = mysql_fetch_array($result);
	}

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

	function getlocatparents($locat) {
		global
			$prefix,
			$_SESSION;

		$list=array();
		$list[]=$locat;
		$result3=mysql_query("SELECT parent FROM ".$prefix."roleslocat WHERE site_id=".$_SESSION["siteid"]." and id=".$locat);
		$c = mysql_fetch_array($result3);
		if($c["parent"]>0) {
			$list=array_merge($list,getlocatparents($c["parent"]));
		}
		return $list;
	}

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
				$return.=' → '.decode($a["name"]);
			}
		}
		else {
			$return='не указана';
		}
		return($return);
	}

	function locatpath2($id,$thislocat) {
		global
			$prefix,
			$_SESSION;

		$result=mysql_query("SELECT * FROM ".$prefix."roleslocat WHERE id=".$id." and site_id=".$_SESSION["siteid"]);
		$a=mysql_fetch_array($result);
		if($a["id"]!='') {
			if($a["parent"]==0) {
				$return=' ('.decode($a["name"]);
			}
			else {
				$return=locatpath2($a["parent"]);
				$return.=' → '.decode($a["name"]);
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

	$history=encode($_REQUEST["history"]);
	$print=encode($_REQUEST["print"]);

	if(encode($_REQUEST["roletype"])!='') {
		$roletype=encode($_REQUEST["roletype"]);
	}
	else {
		$roletype='0';
	}

	if(encode($_REQUEST["team"])!='') {
		$roletype=encode($_REQUEST["team"]);
	}

	if($_SESSION["sitestatus"]==3) {
		$canedit=false;
	}
	else {
		$canedit=true;
	}

	$donotshowthisrole=false;
	if($id!='') {
		$roletype=$a_id["team"];
		if($a_id["site_id"]!=$_SESSION["siteid"]) {
			$canedit=false;
			$donotshowthisrole=true;
		}
	}
	if(($id==0 && $act!="add") || ($id!=0 && $act=="delete")) {
		$result=mysql_query("SELECT * FROM ".$prefix."sites WHERE id=".$_SESSION["siteid"]);
		$a = mysql_fetch_array($result);
		$result2=mysql_query("SELECT * FROM ".$prefix."rolefields WHERE id=".$a["sorter"]." and site_id=".$_SESSION["siteid"]);
		$b = mysql_fetch_array($result2);
		$sorter=decode($b["rolename"]);
		if($a["sorter2"]>0) {
			$result2=mysql_query("SELECT * FROM ".$prefix."rolefields WHERE id=".$a["sorter2"]." and site_id=".$_SESSION["siteid"]);
			$b = mysql_fetch_array($result2);
			$sorter.=' / '.decode($b["rolename"]);
		}
	}
	else {
		$sorter="Сортировка";
	}

	$rolefields=virtual_structure("SELECT * from ".$prefix."rolefields where site_id=".$_SESSION["siteid"]." and team='".$roletype."' order by rolecode","allinfo","role");

	if($action=="exportroles" || $action=="exporttooffline" || $action=="exporttobrain" || $action=="printview_on" || $action=="printview_off" || $action=="nouserinfo_on" || $action=="nouserinfo_off" || $action=="signtonew_on" || $action=="signtonew_off" || $action=="signtochange_on" || $action=="signtochange_off" || $action=="signtocomments_on" || $action=="signtocomments_off" || $action=="viewdeleted_on" || $action=="viewdeleted_off"  || $action=="newplayer" || $action=="comment_add") {
		if($dynrequest==1) {
			dynamic_err(array(),'submit');
		}
		require_once("orders_inc.php");
	}

	// Создание объекта
	$result=mysql_query("SELECT id,changed,date FROM ".$prefix."roles where site_id=".$_SESSION["siteid"]);
	while($a = mysql_fetch_array($result)) {
		$result2=mysql_query("SELECT user_id,date FROM ".$prefix."rolescomments where site_id=".$_SESSION["siteid"]." and role_id=".$a["id"]." order by date desc limit 0,1");
		$b = mysql_fetch_array($result2);
		if($a["date"]>$b["date"]) {
			$result3=mysql_query("SELECT * FROM ".$prefix."users where id=".$a["changed"]);
		}
		else {
			$result3=mysql_query("SELECT * FROM ".$prefix."users where id=".$b["user_id"]);
		}
		$c = mysql_fetch_array($result3);

		$allchanged[]=Array($a["date"],date("d.m.Y в H:i",$a["date"]>$b["date"]?$a["date"]:$b["date"]).'<br />'.usname2($c,true));
		$allchanged_sort[]=$a["date"]>$b["date"]?$a["date"]:$b["date"];
	}
	array_multisort($allchanged_sort, SORT_ASC, $allchanged);

	$result=mysql_query("SELECT DISTINCT u.*, g1.name AS g_city, g2.name AS g_area
	FROM
		(
			SELECT player_id AS ID1 FROM ".$prefix."roles WHERE site_id = ".$_SESSION["siteid"]."
			UNION
			SELECT CHANGED AS ID1 FROM ".$prefix."roles WHERE site_id = ".$_SESSION["siteid"]."
		) T
		INNER JOIN ".$prefix."users u ON u.id = T.ID1
		LEFT JOIN ".$prefix."geography g1 ON g1.id = u.city
		LEFT JOIN ".$prefix."geography g2 ON g2.id = g1.parent");

	while($a = mysql_fetch_array($result)) {
		$allusers[]=Array($a["id"],usname($a,true));
		$allusers2[]=Array($a["id"],usname2($a,true));
		$allusers3[]=Array($a["id"],usname($a,true,true));
		$allusers4[]=Array($a["id"],($a["photo"]!=''?'<img src="'.$server_absolute_path.$uploads[4]["path"].$a["photo"].'">':'').usname($a,true,true).', ИНП '.$a["sid"].', '.($a["gender"]==2?'женщина':'мужчина').', дата рождения '.date("d.m.Y", strtotime($a["birth"])).', '.decode($a["g_city"]).', '.decode($a["g_area"]).'<br /><br />'.((strpos($a["hidesome"],'-2-')===false&&$a["em"]!='')?'<a href="mailto:'.decode($a["em"]).'">'.decode($a["em"]).'</a>':'').((strpos($a["hidesome"],'-2-')===false&&$a["em"]!=''&&((strpos($a["hidesome"],'-3-')===false&&$a["em2"]!='')||$a["phone2"]!=''||$a["icq"]!=''||$a["skype"]!=''||$a["jabber"]!='')?', ':'')).((strpos($a["hidesome"],'-3-')===false&&$a["em2"]!='')?'<a href="mailto:'.decode($a["em2"]).'">'.decode($a["em2"]).'</a>':'').((strpos($a["hidesome"],'-3-')===false&&$a["em2"]!=''&&($a["phone2"]!=''||$a["icq"]!=''||$a["skype"]!=''||$a["jabber"]!='')?', ':'')).($a["phone2"]!=''?'тел: '.decode($a["phone2"]):'').($a["phone2"]!=''&&($a["icq"]!=''||$a["skype"]!=''||$a["jabber"]!='')?', ':'').($a["icq"]!=''?'ICQ: '.decode($a["icq"]):'').($a["icq"]!=''&&($a["skype"]!=''||$a["jabber"]!='')?', ':'').($a["skype"]!=''?'skype: '.decode($a["skype"]):'').($a["skype"]!=''&&($a["jabber"]!='')?', ':'').($a["jabber"]!=''?'jabber: '.decode($a["jabber"]):''));
	}
	foreach ($allusers as $key => $row)
	{
		$allusers_sort[$key]  = strtolower($row[1]);
	}
	array_multisort($allusers_sort, SORT_ASC, $allusers);
	foreach ($allusers2 as $key => $row)
	{
		$allusers2_sort[$key]  = strtolower($row[1]);
	}

    if(($id==0 && $act!="add") || ($id!=0 && $act=="delete")) {
    	$result=mysql_query("SELECT * from ".$prefix."roles where site_id=".$_SESSION["siteid"]);
		while($a=mysql_fetch_array($result)) {
        	if($a["vacancy"]!=0) {
	        	$result2=mysql_query("SELECT COUNT(id) from ".$prefix."roleslinks WHERE roles LIKE '%-all".$a["vacancy"]."-%' or roles LIKE '%-".$a["id"]."-%' or roles2 LIKE '%-all".$a["vacancy"]."-%' or roles2 LIKE '%-".$a["id"]."-%'");
	        	$b=mysql_fetch_array($result2);
				$linkcount[]=Array($a["id"],$b[0]);
			}
			else {
				$linkcount[]=Array($a["id"],'0');
			}
		}

		foreach ($linkcount as $key => $row)
		{
			$linkcount_sort[$key]  = $row[1];
		}
		array_multisort($linkcount_sort, SORT_ASC, $linkcount);
	}

	$obj=new netObj(
		'orders',
		$prefix."roles",
		"заявку",
		Array("Заявка успешно добавлена.","Заявка изменена.","Заявка удалена."),
		Array(
			'0'	=>	Array(
				Array("status", "ASC", true, true, Array(2, Array(Array('1','подана'),Array('2','обсуждается'),Array('3','принята'),Array('4','отклонена')))),
				Array("locat", "ASC", true, true, Array(3, $prefix."roleslocat", "id", "name")),
				Array("sorter", "ASC", true, true),
				Array("player_id", "ASC", true, true, Array(2, $allusers2)),
				Array("datesent", "desc", true, true),
				Array("date", "ASC", true, true, Array(2, $allchanged)),
				Array("id", "ASC", true, true, Array(2, $linkcount)),
			),
		),
		2,
		'100%',
		5000,
		'allinfo'
	);
	
	$obj -> setDefaultSort ('date', 'desc');

	$locatpermit=Array();
	$locatrestrict='';
	$locatrestrict2='';
	$locatcheck='-';
	function getlocatchild($locat,$level) {
		global
			$prefix,
			$locatpermit,
			$locatcheck;

   		$result3=mysql_query("SELECT * FROM ".$prefix."roleslocat WHERE site_id=".$_SESSION["siteid"]." and id=".$locat);
		$c = mysql_fetch_array($result3);
		if(strpos($locatcheck,'-'.$c["id"].'-')===false) {
        	$locatpermit[]=Array($c["id"],decode($c["name"]),$level);
	        $locatcheck.=$c["id"].'-';
	        $result3=mysql_query("SELECT * FROM ".$prefix."roleslocat WHERE site_id=".$_SESSION["siteid"]." and parent=".$locat);
			while($c = mysql_fetch_array($result3)) {
				getlocatchild($c["id"],$level+1);
			}
		}
	}
	$result2=mysql_query("SELECT * FROM ".$prefix."allrights2 WHERE user_id=".$_SESSION["user_sid"]." and site_id=".$_SESSION["siteid"]." and (rights=1 OR rights=2)");
	while($b = mysql_fetch_array($result2)) {
        if($b["locations"]!='' && $b["locations"]!='-' && $b["locations"]!='--' && strpos($b["locations"],'-0-')===false) {
            $result3=mysql_query("SELECT * FROM ".$prefix."roleslocat WHERE site_id=".$_SESSION["siteid"]." order by parent asc, code asc, name asc");
			while($c = mysql_fetch_array($result3)) {
				if(strpos($b["locations"],'-'.$c["id"].'-')!==false) {
					getlocatchild($c["id"],0);
				}
			}
		}
		else {
			unset($locatpermit);
			break;
		}
	}
	if(isset($locatpermit[0])) {
		$locatrestrict=' and locat IN (';
		$locatrestrict2=' and id IN (';
        for($i=0;$i<count($locatpermit);$i++) {
        	$locatrestrict.=$locatpermit[$i][0].', ';
        	$locatrestrict2.=$locatpermit[$i][0].', ';
        }
        $locatrestrict=substr($locatrestrict,0,strlen($locatrestrict)-2);
        $locatrestrict2=substr($locatrestrict2,0,strlen($locatrestrict2)-2);
		$locatrestrict.=')';
		$locatrestrict2.=')';
	}

	if(!$_SESSION["viewdeleted"]) {
		$noviewdeleted=' and todelete2!=1';
	}
	else {
		$noviewdeleted=' and todelete2=1';
	}
	if($history==1 || $print==1)
	{
		$obj_r=new netRight(
			true,
			true,
			false,
			false,
			100,
			'site_id='.$_SESSION["siteid"].$noviewdeleted.$locatrestrict,
			'site_id='.$_SESSION["siteid"].$noviewdeleted.$locatrestrict,
			'site_id='.$_SESSION["siteid"].' and todelete2!=1'.$locatrestrict
		);
		$obj->setRight($obj_r);
	}
	else
	{
		$obj_r=new netRight(
			true,
			true,
			$canedit,
			$canedit,
			100,
			'site_id='.$_SESSION["siteid"].$noviewdeleted.$locatrestrict,
			'site_id='.$_SESSION["siteid"].$noviewdeleted.$locatrestrict,
			'site_id='.$_SESSION["siteid"].' and todelete2!=1'.$locatrestrict
		);
		$obj->setRight($obj_r);
	}
	// Создание полей объекта

	$vacancy=Array();
	$result2=mysql_query("SELECT * from ".$prefix."rolevacancy where site_id=".$_SESSION["siteid"]." and team='".$roletype."' ORDER by name asc, code asc");
	while($b=mysql_fetch_array($result2))
	{
		$result3=mysql_query("SELECT COUNT(id) from ".$prefix."roles where site_id=".$_SESSION["siteid"]." AND status=3 AND vacancy=".$b["id"]);
		$c=mysql_fetch_array($result3);
		if($c[0]<$b["kolvo"])
		{
			$vacancy[]=Array($b["id"],$b["name"].locatpath2($b["locat"],true));
		}
		else
		{
			$vacancy[]=Array($b["id"],$b["name"].locatpath2($b["locat"],true).' (набрано)');
		}
	}

	$mainfields=Array (
			Array(
				'sname'	=>	"Основные поля",
				'type'	=>	"h1",
				'read'	=>	10,
				'write'	=>	100000,
			)
	);

	if($history!=1) {
		$mainfields[]=Array(
				'name'	=>	"id",
				'sname'	=>	"Загрузы",
				'type'	=>	"select",
				'read'	=>	100000,
				'write'	=>	100000,
		);

		$mainfields[]=Array(
				'name'	=>	"player_id",
				'sname'	=>	"Игрок",
				'type'	=>	"select",
				'values'	=>	$allusers4,
				'read'	=>	10,
				'write'	=>	100000,
				'mustbe'	=>	true,
		);

		$mainfields[]=Array(
				'name'	=>	"team",
				'sname'	=>	"Командная / индивидуальная",
				'type'	=>	"select",
				'values'	=>	Array(Array('0','индивидуальная'),Array('1','командная')),
				'default'	=>	$roletype,
				'read'	=>	10,
				'write'	=>	100000,
		);
	}

		$mainfields[]=Array(
				'name'	=>	"money",
				'sname'	=>	"Взнос",
				'type'	=>	"text",
				'default'	=>	decode($a["money"]),
				'read'	=>	10,
				'write'	=>	100,
		);

		$mainfields[]=Array(
				'name'	=>	"moneydone",
				'sname'	=>	"Взнос сдан",
				'type'	=>	"checkbox",
				'read'	=>	10,
				'write'	=>	100,
		);

		$mainfields[]=Array(
				'name'	=>	"alltold",
				'sname'	=>	"Игрок прогружен",
				'type'	=>	"checkbox",
				'read'	=>	100,
				'write'	=>	100,
		);

		if($history!=1 && $print!=1) {
			$mainfields[]=Array(
					'name'	=>	"sorter",
					'sname'	=>	$sorter,
					'type'	=>	"text",
					'read'	=>	100000,
					'write'	=>	100000,
			);
		}

		if(!isset($locatpermit[0])) {
			$locatpermit=make5fieldtree(false,$prefix."roleslocat","parent",0," AND site_id=".$_SESSION["siteid"].$locatrestrict2,"code asc, name asc",0,"id","name",1000000);
		}
		$mainfields[]=Array(
				'name'	=>	"locat",
				'sname'	=>	"Локация / команда",
				'type'	=>	"select",
				'values'	=>	$locatpermit,
				'read'	=>	10,
				'write'	=>	100,
		);

		$mainfields[]=Array(
				'name'	=>	"status",
				'sname'	=>	"Статус",
				'type'	=>	"select",
				'values'	=>	Array(Array('1','подана'),Array('2','обсуждается'),Array('3','принята'),Array('4','отклонена')),
				'read'	=>	10,
				'write'	=>	100,
				'mustbe'	=>	true,
		);

	if($history!=1 && $print!=1) {
		$mainfields[]=Array(
				'name'	=>	"datesent",
				'sname'	=>	"Создана",
				'type'	=>	"timestamp",
				'read'	=>	10,
				'write'	=>	100000,
		);
	}

	if($history!=1) {
		$mainfields[]=Array(
				'name'	=>	"date",
				'sname'	=>	"Изменена",
				'type'	=>	"select",
				'values'	=>	$allchanged,
				'read'	=>	10,
				'write'	=>	100000,
		);
	}

	$mainfields[]=Array(
			'name'	=>	"vacancy",
			'sname'	=>	"Заявка на роль",
			'type'	=>	"select",
			'values'	=>	$vacancy,
			'read'	=>	10,
			'write'	=>	100,
	);

	$mainfields[]=Array(
			'name'	=>	"todelete",
			'sname'	=>	"Удалена игроком",
			'type'	=>	"checkbox",
			'read'	=>	1000000,
			'write'	=>	1000000,
	);

	if($roletype==1) {
		$mainfields[]=Array(
				'name'	=>	"roleteamkolvo",
				'sname'	=>	"Количество людей в команде",
				'type'	=>	"number",
				'read'	=>	10,
				'write'	=>	100,
				'mustbe'	=>	true,
		);

		if($id!='') {
			$result3=mysql_query("SELECT teamkolvo from ".$prefix."rolevacancy where site_id=".$_SESSION["siteid"]." AND id=".$a["vacancy"]);
			$c=mysql_fetch_array($result3);
			$mainfields[]=Array(
					'name'	=>	"roleteamkolvoneeded",
					'sname'	=>	"Желаемое количество людей в команде на данную роль",
					'type'	=>	"number",
					'read'	=>	10,
					'write'	=>	100000,
					'default'	=>	$c["teamkolvo"]
			);
		}
	}

	if($history!=1 && $print!=1) {
		$mainfields[]=Array(
				'name'	=>	"site_id",
				'sname'	=>	"id сайта",
				'type'	=>	"hidden",
				'default'	=>	$_SESSION["siteid"],
				'read'	=>	10,
				'write'	=>	100,
				'mustbe'	=>	true
		);

		$mainfields[]=Array(
				'name'	=>	"team",
				'sname'	=>	"Командная/индивидуальная",
				'type'	=>	"hidden",
				'default'	=>	$roletype,
				'read'	=>	10,
				'write'	=>	100,
		);

		$mainfields[]=Array(
				'name'	=>	"date",
				'sname'	=>	"Изменена",
				'type'	=>	"timestamp",
				'read'	=>	10,
				'write'	=>	100,
				'mustbe'	=>	true
		);
	}

	for($i=0;$i<count($mainfields);$i++) {
		$objer='obj_'.($i);
		$$objer=createElem($mainfields[$i]);
		$obj->setElem($$objer);
		if($history==1 || $print==1) {
			$$objer->setHelp('');
		}
	}

	if($history!=1 && $print!=1) {
		$obj->setSearch($obj_4);
		$obj->setSearch($obj_9);
		$obj->setSearch($obj_10);
		$obj->setSearch($obj_14);
		$obj->setSearch($obj_15);
		$obj->setSearch($obj_6);
		$obj->setSearch($obj_7);
		$obj->setSearch($obj_5);
		$obj->setSearch($obj_8);
	}
	
	$vacancy_id = $a_id["vacancy"];
	
	function get_links_for_vacancy($role_id, $vacancy_id, $site_id)
	{
    global $prefix, $server_absolute_path_site;
    $result3=mysql_query("
    SELECT * 
    from {$prefix}roleslinks 
    where (
        roles LIKE '%-all{$vacancy_id}-%' 
        OR roles LIKE '%-{$role_id}-%' 
        OR roles2 LIKE '%-all{$vacancy_id}-%' 
        OR roles2 LIKE '%-$role_id}-%'
      ) 
      and site_id={$site_id} 
      and content!='' 
      and parent IN (SELECT id from {$prefix}roleslinks WHERE vacancies LIKE '%-{$vacancy_id}-%')
    order by date ASC");
      
			while($c=mysql_fetch_array($result3)) {
			
        $link_to_zagruz = $server_absolute_path_site.'roleslinks/'.$c["id"];
        $zagruz_for = '';
        $zagruz_about = '';
        $dosee='';

				$roles=substr($c["roles"],1,strlen($c["roles"])-2);
				$roles2=substr($c["roles2"],1,strlen($c["roles2"])-2);
				$roles=explode('-',$roles);
				$roles2=explode('-',$roles2);

				foreach($roles as $r) {
					$query="";
					if(strpos($r,'all')!==false) {
						$result2=mysql_query("SELECT * FROM {$prefix}rolevacancy WHERE site_id={$site_id} and id=".str_replace('all','',$r));
						$b=mysql_fetch_array($result2);
						if($b["name"]!='') {
							$zagruz_for.='<a href="'.$server_absolute_path_site.'roles/'.$b["id"].'/">'.$b["name"].'</a>, ';
							$query="SELECT * from {$prefix}roles where vacancy=".$b["id"]." and site_id=".$_SESSION["siteid"];
						}
						elseif($r==0) {
							$zagruz_for.='<i>глобального сюжета</i>, ';
						}
						else {
							$zagruz_for.='<i>удаленной роли</i>, ';
						}
					}
					else {
						$query="SELECT * from {$prefix}roles where id=".$r." and site_id=$site_id";
						$result2=mysql_query($query);
						$b=mysql_fetch_array($result2);
						$zagruz_for.='<a href="'.$server_absolute_path_site.'orders/'.$b["id"].'/">';
						if($b["sorter"]!='') {
							$zagruz_for.=decode($b["sorter"]);
						}
						else {
							$zagruz_for.='<i>удаленной заявки</i>';
						}
						$zagruz_for.='</a>, ';
					}
					if($query!='') {
						$result5=mysql_query($query);
						while($e=mysql_fetch_array($result5)) {
							if(strpos($c["roles"],'-'.$e["id"].'-')!==false) {
                if ($dosee != '')
                {
                  $dosee .= ", ";
                }
								$dosee.='<a href="'.$server_absolute_path_site.'orders/'.$e["id"].'/">'.decode($e["sorter"]).'</a>';
								if($b["hideother"]=='1') {
									$dosee.=' (игрок не знает, на кого конкретно у него данный загруз)';
								}
							}
							elseif(strpos($c["roles"],'-'.$r.'-')!==false) {
							  if ($dosee != '')
                {
                  $dosee .= ", ";
                }
								$dosee.='<a href="'.$server_absolute_path_site.'orders/'.$e["id"].'/">'.decode($e["sorter"]).'</a>';
								if($e["status"]<3) {
									$dosee.=' (увидит, как только заявка будет принята)';
								}
								if($b["hideother"]=='1') {
									$dosee.=' (игрок не знает, на кого конкретно у него данный загруз)';
								}
							}
						}
					}
				}
				
				foreach($roles2 as $r) {
					if(strpos($r,'all')!==false) {
						$result2=mysql_query("SELECT * FROM {$prefix}rolevacancy WHERE site_id={$site_id} and id=".str_replace('all','',$r));
						$b=mysql_fetch_array($result2);
						if($b["name"]!='') {
							$zagruz_about.='<a href="'.$server_absolute_path_site.'roles/'.$b["id"].'/">'.$b["name"].'</a>, ';
						}
						elseif($r==0) {
							$zagruz_about.='<i>глобальный сюжет</i>, ';
						}
						else {
							$zagruz_about.='<i>удаленную роль</i>, ';
						}
					}
					else {
						$result2=mysql_query("SELECT * FROM {$prefix}roles WHERE site_id={$site_id} and id=".$r);
						$b=mysql_fetch_array($result2);
						$alllinks.='<a href="'.$server_absolute_path_site.'orders/'.$b["id"].'/">';
						if($b["sorter"]!='') {
							$zagruz_about.=decode($b["sorter"]);
						}
						else {
							$zagruz_about.='<i>удаленную заявку</i>';
						}
						$zagruz_about.='</a>, ';
					}
				}
				
				$result2=mysql_query("SELECT * FROM {$prefix}roleslinks WHERE id=".$c["parent"]);
				$b=mysql_fetch_array($result2);
				$sujet_name = decode($b["name"]);
				
				$link_text = '<b>«<a href="'.$server_absolute_path_site.'roleslinks/'.$b["id"].'/valuestype=0">' . $sujet_name . '</a>» — загруз (<a href="'.$link_to_zagruz.'/valuestype=1">изменить</a>) </b> <br> ' .
				' <span style="font-size:70%;"> для ' . 
				 substr($zagruz_for,0,strlen($zagruz_for)-2).' про ' . substr($zagruz_about,0,strlen($zagruz_about)-2).' (его видят: '.$dosee.')</span><br>'
				. decode($c["content"]);
				
				$alllinks.=$link_text . '<br>';
			}
			return substr($alllinks,0,strlen($alllinks)-8);
	}
	
	if($history!=1 && $id!='') {
		$rolefields[]=Array(
				'sname'	=>	'Сюжеты и загрузы',
				'type'	=>	"h1",
				'read'	=>	10,
				'write'	=>	100000,
		);
		if($vacancy_id) {
			$alllinks = get_links_for_vacancy($id, $vacancy_id, $_SESSION["siteid"]);
			$rolefields[]=Array(
				'name'	=>	"alllinks",
				'sname'	=>	"Полный список загрузов",
				'type'	=>	"wysiwyg",
				'default'	=>	$alllinks,
				'read'	=>	10,
				'write'	=>	100000,
      );
		}
		else
		{
      $rolefields[]=Array(
				'name'	=>	"alllinks",
				'sname'	=>	"",
				'type'	=>	"text",
				'default'	=> 'Для полноценного использования системы сюжетов и загрузов необходимо приписать заявку к роли',
				'read'	=>	10,
				'write'	=>	100000,
      );
		}
	}

	$dynamic_fields_shown=array();
	$full_locats_tree_new=array();
	if(encode_to_cp1251($_REQUEST["vacancy"])!='' && encode_to_cp1251($_REQUEST["vacancy"])!=$a_id["vacancy"]) {
		$result=mysql_query("SELECT * FROM {$prefix}rolevacancy WHERE site_id=".$_SESSION["siteid"]." AND id=".encode_to_cp1251($_REQUEST["vacancy"]));
		$a=mysql_fetch_array($result);
		if($a["locat"]!=$a_id["locat"]) {
			$full_locats_tree_new=getlocatparents($a["locat"]);
		}
	}
	elseif(encode_to_cp1251($_REQUEST["locat"])!=$a_id["locat"] && encode_to_cp1251($_REQUEST["vacancy"])=='') {
		$full_locats_tree_new=getlocatparents(encode_to_cp1251($_REQUEST["locat"]));
	}
	$full_locats_tree=getlocatparents($a_id["locat"]);
	for($i=0;$i<count($rolefields);$i++) {
		$showrolefield=true;
		$result=mysql_query("SELECT * FROM ".$prefix."rolefields WHERE id=".preg_replace("#virtual#","",$rolefields[$i]["name"])." AND site_id=".$_SESSION["siteid"]);
		$a=mysql_fetch_array($result);
		if(str_replace('-','',$a["roleparent"])!='') {
			$showrolefield=false;

			unset($matches);
			preg_match_all('#-(\d+):(\d+)#',$a["roleparent"],$matches);
			foreach($matches[1] as $key=>$value) {
				if(preg_match('#\[virtual'.$value.'\]\['.$matches[2][$key].'\]#',$a_id["allinfo"]) || preg_match('#\[virtual'.$value.'\]\[[^\]]*-'.$matches[2][$key].'-[^\]]*\]#',$a_id["allinfo"])) {
					if(encode_to_cp1251($_REQUEST["virtual".$value])!=$matches[2][$key] || encode_to_cp1251($_REQUEST["virtual".$value][$matches[2][$key]])!='on') {
						$dynamic_fields_shown[]=$a["id"];
					}
					if($act!="add") {
						$showrolefield=true;
						break;
					}
				}
				if(encode_to_cp1251($_REQUEST["virtual".$value])==$matches[2][$key] || encode_to_cp1251($_REQUEST["virtual".$value][$matches[2][$key]])=='on') {
					$dynamic_fields_shown[]=$a["id"];
				}
			}
			unset($matches);
			preg_match_all('#-locat:(\d+)#',$a["roleparent"],$matches);
			foreach($matches[1] as $key=>$value) {
				if(in_array($value,$full_locats_tree_new)) {
					$dynamic_fields_shown[]=$a["id"];
				}
				if(in_array($value,$full_locats_tree)) {
					if(!in_array($value,$full_locats_tree_new) && count($full_locats_tree_new)>0) {
						$dynamic_fields_shown[]=$a["id"];
					}
					if($act!="add") {
						$showrolefield=true;
						break;
					}
				}
			}
		}
		if($showrolefield) {
			$objer='obj_'.($i+count($mainfields));
			$$objer=createElem($rolefields[$i]);
			$obj->setElem($$objer);
			if($history==1 || $print==1) {
				$$objer->setHelp('');
			}
			else {

				if($a["filter"]=='1') {
					$obj->setSearch($$objer);
				}
			}
		}
	}

	// Исполнение dynamicaction, если необходимо
	if($action=="dynamicaction")
	{
		require_once($server_inner_path.$direct."/dynamicaction.php");
		require_once($server_inner_path.$direct."/classes/base_mails.php");
		if($object=="orders")
		{
			if($actiontype=="change")
			{
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
				$result=mysql_query("SELECT * from ".$prefix."rolevacancy where id=".encode($_REQUEST["vacancy"])." and site_id=".$_SESSION["siteid"]);
				$a=mysql_fetch_array($result);
				if($a["autonewrole"]=='1' && $a["kolvo"]>1 && (encode($_REQUEST["status"])==2 || encode($_REQUEST["status"])==3)) {
					// выделяем заявку в новую роль
					$result2=mysql_query("SELECT * from ".$prefix."sites where id=".$_SESSION["siteid"]);
					$b=mysql_fetch_array($result2);
					$newrolesorter='';
					if($a["team"]=='1') {
                    	$newrolesorter=encode_to_cp1251($_REQUEST["virtual".$b["sorter2"]]);
					}
					else {
						$newrolesorter=encode_to_cp1251($_REQUEST["virtual".$b["sorter"]]);
					}
					mysql_query("INSERT into ".$prefix."rolevacancy (locat, team, name, code, kolvo, autonewrole, teamkolvo, site_id, date) VALUES (".$a["locat"].", '".$a["team"]."', '".$newrolesorter."', ".($a["code"]+1).", 1, '0', ".$a["teamkolvo"].", ".$_SESSION["siteid"].", ".time().")");
                    $newroleid=mysql_insert_id($link);
					mysql_query("UPDATE ".$prefix."rolevacancy SET kolvo=".($a["kolvo"]-1)." where id=".encode($_REQUEST["vacancy"]));
					$_POST["vacancy"]=$newroleid;
					$_REQUEST["vacancy"]=$newroleid;
					err("Заявка успешно выделена в новую роль в той же локации.");
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
						$a_id;

					if(encode($_REQUEST["vacancy"])!='') {
						$result=mysql_query("SELECT * from ".$prefix."rolevacancy where site_id=".$_SESSION["siteid"]." and id=".encode($_REQUEST["vacancy"]));
						$a=mysql_fetch_array($result);
						if($a["locat"]>0 && ($a["locat"]!=$a_id["locat"] || $a["locat"]!=encode_to_cp1251($_REQUEST["locat"]))) {
							mysql_query("UPDATE ".$prefix."roles SET locat=".$a["locat"]." WHERE id=".$id);
							err('Локация в заявке установлена в соответствии с ролью в сетке ролей.');
							$vacname=$a["name"];
							return true;
						}
						return false;
					}
				}

				function dynamic_add_success() {
					global
						$prefix,
						$_SESSION,
						$id,
						$_REQUEST,
						$dynamic_fields_shown;

					$vac_changed=set_locat_to_vac();
					$result=mysql_query("SELECT * from ".$prefix."sites where id=".$_SESSION["siteid"]);
					$a=mysql_fetch_array($result);
					$result2=mysql_query("SELECT * from ".$prefix."roles where id=".$id);
					$b=mysql_fetch_array($result2);
					if($roletype==1) {
						$sorter=encode_to_cp1251($_REQUEST["virtual".$a["sorter2"]]);
					}
					else {
						$sorter=encode_to_cp1251($_REQUEST["virtual".$a["sorter"]]);
					}
					if($sorter=='') {
						$sorter=$vacname;
					}
					mysql_query("UPDATE ".$prefix."roles SET sorter='".$sorter."', changed=".$_SESSION['user_id'].", status=1, money='".$a["money"]."', player_id=".$_SESSION['user_id'].", todelete=0, datesent='".$b["date"]."' WHERE id=".$id);
				}


				if($actiontype=="change") {
                    function dynamic_save_success() {
                    	global
                    		$prefix,
                    		$_SESSION,
                    		$a_id,
                    		$id,
                    		$_REQUEST,
                    		$rolefields,
                    		$server_absolute_path_site,
                    		$server_absolute_path,
                    		$dynamic_fields_shown;

	                    $vac_changed=set_locat_to_vac();
	                    $result=mysql_query("SELECT * from ".$prefix."roles where id=".$id." and site_id=".$_SESSION["siteid"]);
						$a=mysql_fetch_array($result);
						$result2=mysql_query("SELECT * from ".$prefix."roleshistory where role_id=".$id." order by date desc limit 0,1");
						$b=mysql_fetch_array($result2);
						$c=unmakevirtual($a["allinfo"]);
						$d=unmakevirtual($b["allinfo"]);
						$sendchange=false;
						$sendchangeplayer=false;
						foreach($rolefields as $f=>$v) {
							if($c[$v["name"]]!=$d[$v["name"]] && $v["type"]!='h1') {
								$sendchange=true;
								if($v["read"]<=10) {
	                            	$sendchangeplayer=true;
	                            	break;
								}
							}
						}
						if($sendchange) {
							$result6=mysql_query("SELECT * from ".$prefix."users where id=".$_SESSION['user_id']);
							$e=mysql_fetch_array($result6);
							$myname=usname($e, true);
							$myemail=decode($e["em"]);

							$result2=mysql_query("SELECT * from ".$prefix."sites where id=".$_SESSION["siteid"]);
							$b=mysql_fetch_array($result2);

							$subject='Мастером изменена заявка «'.decode($a["sorter"]).'» проекта «'.decode($b["title"]).'»';

							$result2=mysql_query("SELECT * from ".$prefix."users where id=".$a["player_id"]);
							$b=mysql_fetch_array($result2);

							$message='Добрый день.
Заявка «'.decode($a["sorter"]).'» игрока «'.usname($b,true).'» была изменена мастером «'.$myname.'».
Ссылка: '.$server_absolute_path_site.'orders/'.$id.'/site='.$_SESSION["siteid"].' (вы должны быть залогинены на allrpg.info).
Отказаться от получения уведомлений об изменении заявок Вы можете здесь: '.$server_absolute_path_site.'orders/site='.$_SESSION["siteid"].'&action=signtochange_off (вы должны быть залогинены на allrpg.info).';

							$result2=mysql_query("SELECT * FROM ".$prefix."allrights2 WHERE site_id=".$_SESSION["siteid"]." AND (rights=1 OR rights=2) AND (locations='-' OR locations='' OR locations LIKE '%-0-%' OR locations LIKE '%-".$a["locat"]."-%') AND (notifications IS NULL OR notifications='-' OR notifications='' OR notifications LIKE '%-0-%'".getlocatnotifications($a["locat"]).") AND signtochange='1' AND user_id!=".$_SESSION["user_sid"]);
							while($b=mysql_fetch_array($result2)) {
								$c=getuser_sid($b["user_id"]);
								$contactemail=decode($c["em"]);
								send_mail($myname, $myemail, $contactemail, $subject, $message);
							}
						}
						if($sendchangeplayer && $a["todelete"]!=1) {
							$result6=mysql_query("SELECT * from ".$prefix."users where id=".$_SESSION['user_id']);
							$e=mysql_fetch_array($result6);
							$myname=usname($e, true);
							$myemail=decode($e["em"]);

							$subject='Ваша заявка «'.decode($a["sorter"]).'» была изменена';

							$result2=mysql_query("SELECT * from ".$prefix."users where id=".$a["player_id"]);
							$b=mysql_fetch_array($result2);

							$message='Добрый день.
Ваша заявка «'.decode($a["sorter"]).'» была изменена мастером «'.$myname.'».
Ссылка: '.$server_absolute_path.'order/'.$id.'/ (вы должны быть залогинены на allrpg.info).';

							$contactemail=decode($b["em"]);

							send_mail($myname, $myemail, $contactemail, $subject, $message);
						}

						mysql_query("UPDATE ".$prefix."roles SET changed=".$_SESSION['user_id']." WHERE id=".$id);

						$result2=mysql_query("SELECT * from ".$prefix."roles where id=".$id);
						$b=mysql_fetch_array($result2);
						$result=mysql_query("SELECT * from ".$prefix."sites where id=".$b["site_id"]);
						$a=mysql_fetch_array($result);
						if($roletype==1) {
							$sorter=encode_to_cp1251($_REQUEST["virtual".$a["sorter2"]]);
						}
						else {
							$sorter=encode_to_cp1251($_REQUEST["virtual".$a["sorter"]]);
						}
						if($sorter=='') {
							$sorter=$vacname;
						}
						mysql_query("UPDATE ".$prefix."roles SET sorter='".$sorter."' where id=".$id);

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
				}
				dynamicaction($obj);
			}
			else {
				function dynamic_delete_success() {
					global
						$prefix,
						$_SESSION,
						$id,
						$a_id,
						$server_absolute_path_site;

					$result6=mysql_query("SELECT * from ".$prefix."users where id=".$_SESSION['user_id']);
					$e=mysql_fetch_array($result6);
					$myname=usname($e, true);
					$myemail=decode($e["em"]);

					$result2=mysql_query("SELECT * from ".$prefix."sites where id=".$_SESSION["siteid"]);
					$b=mysql_fetch_array($result2);

					$subject='Мастером удалена заявка «'.decode($a_id["sorter"]).'» проекта «'.decode($b["title"]).'»';

					$result2=mysql_query("SELECT * from ".$prefix."users where id=".$a_id["player_id"]);
					$b=mysql_fetch_array($result2);

					$message='Добрый день.
Заявка «'.decode($a["sorter"]).'» игрока «'.usname($b,true).'» была удалена мастером «'.$myname.'».
Отказаться от получения уведомлений об изменении заявок Вы можете здесь: '.$server_absolute_path_site.'orders/site='.$_SESSION["siteid"].'&action=signtochange_off (вы должны быть залогинены на allrpg.info).';

					$result2=mysql_query("SELECT * from ".$prefix."allrights2 WHERE site_id=".$_SESSION["siteid"]." and (rights=1 OR rights=2) AND (locations='-' OR locations='' OR locations LIKE '%-0-%' OR locations LIKE '%-".$a_id["locat"]."-%') AND (notifications IS NULL OR notifications='-' OR notifications='' OR notifications LIKE '%-0-%'".getlocatnotifications($a_id["locat"]).") AND signtochange='1' AND user_id!=".$_SESSION["user_sid"]);
					while($b=mysql_fetch_array($result2)) {
						$result3=mysql_query("SELECT * from ".$prefix."users where sid=".$b["user_id"]);
						$c=mysql_fetch_array($result3);

						$contactemail=decode($c["em"]);

						send_mail($myname, $myemail, $contactemail, $subject, $message);
					}
				}

				if($a_id["todelete"]==1) {
					mysql_query("DELETE from ".$prefix."roleshistory where role_id=".$id);
					mysql_query("DELETE from ".$prefix."rolescomments where role_id=".$id);
					mysql_query("DELETE from ".$prefix."rolescommentsread where role_id=".$id);
					dynamicaction($obj);
				}
				else {
					mysql_query("UPDATE ".$prefix."roles SET todelete2=1 WHERE id=".$id." and site_id=".$_SESSION["siteid"]);
					err_red("Заявка успешно удалена из списка заявок, игроку отправлено e-mail оповещение.");

					$result6=mysql_query("SELECT * from ".$prefix."users where id=".$_SESSION['user_id']);
					$e=mysql_fetch_array($result6);
					$myname=usname($e, true);
					$myemail=decode($e["em"]);

					$result2=mysql_query("SELECT * from ".$prefix."users where id=".$a_id["player_id"]);
					$b=mysql_fetch_array($result2);
					$contactemail=decode($b["em"]);
					$subject='Ваша заявка «'.decode($a_id["sorter"]).'» была удалена';

					$message='Добрый день.
Ваша заявка «'.decode($a_id["sorter"]).'» была удалена мастером «'.$myname.'».
Ссылка: '.$server_absolute_path.'order/'.$id.'/ (вы должны быть залогинены на allrpg.info).';

					send_mail($myname, $myemail, $contactemail, $subject, $message);
					dynamic_delete_success();
				}
			}
		}
	}

	if($act=="view" && $id!='' && ($actiontype=='' || $trouble) && $history!=1 && $print!=1 && $canedit) {
		$result=mysql_query("SELECT * FROM ".$prefix."roles WHERE id=".$id." and site_id=".$_SESSION["siteid"]);
		$a = mysql_fetch_array($result);

		$result2=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$a["player_id"]);
		$b = mysql_fetch_array($result2);

		$additional_commands.='<a onClick="if (confirm(\'Прежде чем перейти к истории изменений заявки, не забудьте сохранить изменения! Перейти?\')) {document.location=\''.$server_absolute_path_site.$kind.'/'.$id.'/history=1\';}">История изменений заявки</a>';
		$additional_commands.='<a onClick="if (confirm(\'Прежде чем перейти к версии для печати, не забудьте сохранить изменения! Перейти?\')) {document.location=\''.$server_absolute_path_site.$kind.'/'.$id.'/print=1\';}">Версия для печати</a>';
		$additional_commands.='<a onClick="newplayer('.$b["sid"].')">Предложить заявку другому игроку</a>';

		if($a_id["new_player_sid"]>0 && $action!="newplayer") {
			$result6=mysql_query("SELECT * from ".$prefix."users where sid=".$a_id["new_player_sid"]);
			$e=mysql_fetch_array($result6);
			if($a_id["new_player_deny"]==0 && $e["id"]!='') {
				err("Пользователь «".usname($e,true)."» не дал ответа на предложение о приеме заявки.");
			}
			elseif($e["id"]=='') {
				err_red("Пользователя, которому была предложена заявка, не существует.");
			}
			else {
				err_red("Пользователь «".usname($e,true)."» отказался принять данную заявку. Чтобы убрать данное сообщение, нажмите «предложить заявку другому игроку» и введите ИНП игрока, которому на данный момент принадлежит заявка (возврат владельцу).");
			}
		}
	}
	elseif($act=="view" && $id!='' && $print==1 && $history!=1) {
		$content='<html>
<head>
<title>allrpg.info</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="author" content="©еть">
<link rel="stylesheet" href="'.$server_absolute_path.'main_new.css" rev="contents" type="text/css">
<style>
body {background-color: white; background: none;}
.cb_editor {margin-top: 10px;}
</style>
</head>

<body>
<!--maincontent-->
</body>
</html>';
	}
	elseif($act=="view" && $id!='' && $history==1 && $print!=1) {
		$prev=0;
		$next=0;
		$start=encode($_REQUEST["start"]);
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

		$control='<a href="'.$server_absolute_path_site.$kind.'/'.$id.'/">вернуться к заявке</a> ';
		if($prev!='')
		{
			$control.='| <a href="'.$server_absolute_path_site.$kind.'/'.$id.'/history=1&start='.$prev.'">следующее изменение</a> ';
		}
		if($next!='')
		{
			$control.='| <a href="'.$server_absolute_path_site.$kind.'/'.$id.'/history=1&start='.$next.'">предыдущее изменение</a>';
		}

		$result2=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$a_id["changed"]);
		$b = mysql_fetch_array($result2);
		$obj_html.='<center>'.$control.'</center><br><table width="100%" border=0><tr valign=top><td width="50%"><center><b>Сохранено:</b><br>
'.usname($b, true, true).'<br>
'.date("d.m.Y",$a_id["date"]).' в '.date("H:i",$a_id["date"]).'</b><br><br>';
	}

	if($act=="view" && $id!='' && ($actiontype=='' || $trouble) && $history!=1 && $print!=1) {
		$result=mysql_query("SELECT * FROM ".$prefix."rolescomments WHERE role_id=".$id." and id in (select comment_id from ".$prefix."rolescommentsread where role_id=".$id." and user_id=".$_SESSION["user_id"].") limit 0,1");
		$a = mysql_fetch_array($result);

		$obj_html.='<div class="cb_editor"><h1 class="data_h1">Комментарии [<a onClick="$(\'#comment_add\').toggle();$(this).html($(this).html()==\'добавить\'?\'скрыть\':\'добавить\');">добавить</a>]'.($a["id"]!=''?'[<a onClick="$(\'#all_comments\').toggle();$(this).html($(this).html()==\'показать скрытые\'?\'убрать скрытые\':\'показать скрытые\');$(document).scrollTop($(\'#all_comments\').offset().top);">показать скрытые</a>]':'').'</h1>';

		if($a_id["todelete"]!=1) {
			$comment_2_selecter=Array(Array(1,'игроку'),Array(2,'другим мастерам'));
		}
		else {
			$comment_2_selecter=Array(Array(2,'другим мастерам'));
		}

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
		$comment_2=createElem(Array(
			'name'	=>	'type',
			'sname'	=>	"Тип комментария",
			'type'	=>	"select",
			'read'	=>	10,
			'write'	=>	10,
			'default'	=>	$comment_type,
			'values'	=>	$comment_2_selecter,
			'mustbe'	=>	true,
			)
		);

		$obj_html.='
<div id="comment_add"';
		if(!$comment_trouble) {
			$obj_html.='style="display: none"';
		}
		$obj_html.='>
<form action="'.$curdir.$kind.'/'.$id.'/" method="post" enctype="multipart/form-data">
<input type="hidden" name="kind" value="'.$kind.'">
<input type="hidden" name="object" value="'.$object.'">
<input type="hidden" name="action" value="comment_add">
<input type="hidden" name="id" value="'.$id.'">
<input type="hidden" name="act" value="'.$act.'">
';
		$obj_html.='<div class="fieldname" id="name_content">Текст комментария</div><div class="fieldvalue" id="div_content">'.$comment_1->draw(2,"write").'</div>
<div class="clear"></div>
<br />
<div class="fieldname" id="name_type">Тип комментария</div><div class="fieldvalue" id="div_type">'.$comment_2->draw(2,"write").'</div>
<div class="clear"></div>
<br />
<center><button class="main">Добавить</button></center>
</form><br></div>';

		$obj_html.='<div id="new_comments">';
		$result=mysql_query("SELECT * FROM ".$prefix."rolescomments WHERE role_id=".$id." and id not in (select comment_id from ".$prefix."rolescommentsread where role_id=".$id." and user_id=".$_SESSION["user_id"].") order by date desc");
		while($a = mysql_fetch_array($result)) {
			$result2=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$a["user_id"]);
			$b = mysql_fetch_array($result2);
			$obj_html.='<div class="';
			if($a["type"]==1) {
				$obj_html.='comm_master';
			}
			elseif($a["type"]==2) {
				$obj_html.='comm_masters';
			}
			elseif($a["type"]==3) {
				$obj_html.='comm_player';
			}
			$obj_html.='">'.($a["type"]==3?'<b>Игрок</b>':'<b>Мастер</b>').' '.usname($b,true,true).' в <b>'.date("G:i d.m.Y",$a["date"]).'</b> написал'.($b["gender"]==2?'а':'');
			if($a["type"]==2) {
				$obj_html.=' другим мастерам';
			}
			elseif($a["type"]==1) {
				$obj_html.=' игроку';
			}
			$obj_html.=':<br>
'.decode2($a["content"]).'</div><hr>';
			mysql_query("INSERT into ".$prefix."rolescommentsread (role_id, user_id, comment_id, date) values (".$id.", ".$_SESSION["user_id"].", ".$a["id"].", ".time().")");
		}
		$obj_html.='</div>';
		$obj_html.='<div id="all_comments" style="display: none">';
		$result=mysql_query("SELECT * FROM ".$prefix."rolescomments WHERE role_id=".$id." and id in (select comment_id from ".$prefix."rolescommentsread where role_id=".$id." and user_id=".$_SESSION["user_id"].") order by date desc");
		while($a = mysql_fetch_array($result)) {
			$result2=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$a["user_id"]);
			$b = mysql_fetch_array($result2);
			$obj_html.='<div class="';
			if($a["type"]==1) {
				$obj_html.='comm_master';
			}
			elseif($a["type"]==2) {
				$obj_html.='comm_masters';
			}
			elseif($a["type"]==3) {
				$obj_html.='comm_player';
			}
			$obj_html.='">'.($a["type"]==3?'<b>Игрок</b>':'<b>Мастер</b>').' '.usname($b,true,true).' в <b>'.date("G:i d.m.Y",$a["date"]).'</b> написал'.($b["gender"]==2?'а':'');
			if($a["type"]==2) {
				$obj_html.=' другим мастерам';
			}
			elseif($a["type"]==1) {
				$obj_html.=' игроку';
			}
			$obj_html.=':<br>
'.decode2($a["content"]).'</div><hr>';
		}
		$obj_html.='</div><br></div>';
	}

	if($history!=1 && $id!='' && !$_SESSION["nouserinfo"]) {
		$result=mysql_query("SELECT * from ".$prefix."users u,".$prefix."roles r where u.id = r.player_id AND r.id=".$id." AND r.site_id=".$_SESSION["siteid"]);
		$a=mysql_fetch_array($result);

		$users_f=Array (
			Array(
				'sname'	=>	"Дополнительно об игроке",
				'type'	=>	"h1",
				'read'	=>	100,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"vkontakte",
				'sname'	=>	"ВКонтакте",
				'type'	=>	"text",
				'default'	=>	social2($a["vkontakte"],"vkontakte",true),
				'read'	=>	100,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"livejournal",
				'sname'	=>	"Живой Журнал",
				'type'	=>	"text",
				'default'	=>	social2($a["livejournal"],"livejournal",true),
				'read'	=>	100,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"facebook",
				'sname'	=>	"Facebook",
				'type'	=>	"text",
				'default'	=>	social2($a["facebook"],"facebook",true),
				'read'	=>	100,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"tweeter",
				'sname'	=>	"Tweeter",
				'type'	=>	"text",
				'default'	=>	social2($a["tweeter"],"tweeter",true),
				'read'	=>	100,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"googleplus",
				'sname'	=>	"Google+",
				'type'	=>	"text",
				'default'	=>	social2($a["googleplus"],"googleplus",true),
				'read'	=>	100,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"sickness",
				'sname'	=>	"Медицинские противопоказания",
				'type'	=>	"textarea",
				'default'	=>	$a["sickness"],
				'read'	=>	100,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"ingroup",
				'sname'	=>	"Состоит в мастерской группе",
				'type'	=>	"text",
				'default'	=>	$a["ingroup"],
				'read'	=>	100,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"prefer",
				'sname'	=>	"Предпочитаемые жанры игр",
				'type'	=>	"multiselect",
				'values'	=>	make5field($prefix."gametypes where tipe=1 order by name","id","name"),
				'images'	=>	make5field($prefix."gametypes where tipe=1 order by name","id","im"),
				'path'	=>	$server_absolute_path.$uploads[6]['path'],
				'default'	=>	$a["prefer"],
				'read'	=>	100,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"prefer2",
				'sname'	=>	"Предпочитаемые типы игр",
				'type'	=>	"multiselect",
				'values'	=>	make5field($prefix."gametypes where tipe=2 order by name","id","name"),
				'images'	=>	make5field($prefix."gametypes where tipe=2 order by name","id","im"),
				'path'	=>	$server_absolute_path.$uploads[6]['path'],
				'default'	=>	$a["prefer2"],
				'read'	=>	100,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"prefer3",
				'sname'	=>	"Предпочитаемые миры игр",
				'type'	=>	"multiselect",
				'values'	=>	make5field($prefix."gameworlds order by name","id","name"),
				'default'	=>	$a["prefer3"],
				'read'	=>	100,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"prefer4",
				'sname'	=>	"Дополнительные предпочтения",
				'type'	=>	"multiselect",
				'values'	=>	make5field($prefix."gametypes where tipe=3 order by name","id","name"),
				'images'	=>	make5field($prefix."gametypes where tipe=3 order by name","id","im"),
				'path'	=>	$server_absolute_path.$uploads[6]['path'],
				'default'	=>	$a["prefer4"],
				'read'	=>	100,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"specializ",
				'sname'	=>	"Основная специализация на играх",
				'type'	=>	"multiselect",
				'values'	=>	make5field($prefix."specializ where gr=1 order by name","id","name"),
				'images'	=>	make5field($prefix."specializ where gr=1 order by name","id","im"),
				'path'	=>	$server_absolute_path.$uploads[7]['path'],
				'default'	=>	$a["specializ"],
				'read'	=>	100,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"additional",
				'sname'	=>	"Дополнительная информация",
				'type'	=>	"textarea",
				'default'	=>	$a["additional"],
				'read'	=>	100,
				'write'	=>	100000,
			),
		);

		for($i=0;$i<count($users_f);$i++) {
			$objer='obj_'.($i+count($mainfields)+count($rolefields));
			$$objer=createElem($users_f[$i]);
			$obj->setElem($$objer);
		}
	}

	if($id!='' && ($actiontype=='' || $trouble) && $history!=1 && $print!=1) {
		if($a_id["todelete"]==1) {
			err_red("Игрок удалил у себя данную заявку.");
		}
		if($a_id["todelete2"]==1) {
			err_red("Мастера удалили данную заявку.");
		}
	}

	// Добавление параметра values к select'ам и multiselect'ам.

	// Инициализация элементов поиска, если нужен.

	// Отрисовка всего объекта html'ем в переменную
	$obj_html.=$obj->draw();

	if($act=="view" && $id!='' && $history==1) {
		$obj_html.='</td><td>';

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
'.date("d.m.Y",$a["date"]).' в '.date("G:i",$a["date"]).'</b><br><br>
<div class="cb_editor">';

			$result=mysql_query("SELECT * FROM ".$prefix."roles WHERE id=".$id." and site_id=".$_SESSION["siteid"]);
			$a = mysql_fetch_array($result);
			$old=unmakevirtual($a['allinfo']);
			$old=array_merge($a,$old);

			$rolefields=array_merge($mainfields,$rolefields);

			foreach($rolefields as $f=>$v)
			{
				if($v["read"]<=100)
				{
					$can="read";
					if($v["name"]!="allinfo") {
						$obj_n=createElem($v);
						$obj_n->setVal($b);
						if($obj_n->getVal()!='' || $obj_n->getType()=="h1") {
							if(!($v["type"]=="select" && $obj_n->getVal()==0)) {
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
			$obj_html.='</div></center>';
		}
		$obj_html.='</td></tr></table>';
	}
	if(($act=='' && $id=='') || ($id!='' && $actiontype!='' && !$trouble)) {
		$additional_commands.='<a onClick="$(\'#filters_settings\').toggle();">Настройки уведомлений</a>';
		$ctrllinks.='
<div id="filters_settings">';
		$ctrllinks.='<a href="'.$server_absolute_path_site.$kind.'/action=';
		if($_SESSION["viewdeleted"]) {
			$ctrllinks.='viewdeleted_off">Уйти из удаленных заявок';
		}
		else {
			$ctrllinks.='viewdeleted_on">Посмотреть удаленные заявки';
		}
		

		$ctrllinks.='</a>';
		$result=mysql_query("SELECT signtonew,signtocomments,signtochange FROM ".$prefix."allrights2 WHERE user_id=".$_SESSION["user_sid"]." and site_id=".$_SESSION["siteid"]);
		$a = mysql_fetch_array($result);
		
		$ctrllinks.= '<br>Уведомления о новых заявках: ';
		if($a["signtonew"]=='1') {
			$ctrllinks.='включены (<a href="'.$server_absolute_path_site.$kind.'/action=signtonew_off">выключить</a>)';
		}
		else {
			$ctrllinks.='отключены (<a href="'.$server_absolute_path_site.$kind.'/action=signtonew_on">включить</a>)';
		}
		$ctrllinks.='<br>Уведомления об изменениях: ';
		if($a["signtochange"]=='1') {
      $ctrllinks.='включены (<a href="'.$server_absolute_path_site.$kind.'/action=signtochange_off">выключить</a>)';
		}
		else {
			$ctrllinks.='отключены (<a href="'.$server_absolute_path_site.$kind.'/action=signtochange_on">включить</a>)';
		}

		$ctrllinks.='<br>Уведомления о комментариях: ';
		
		if($a["signtocomments"]=='1') {
		  $ctrllinks.='включены (<a href="'.$server_absolute_path_site.$kind.'/action=signtocomments_off">выключить</a>)';
		}
		else {
			$ctrllinks.='отключены (<a href="'.$server_absolute_path_site.$kind.'/action=signtocomments_on">включить</a>)';
		}

		$ctrllinks.='<br /><br /></div>';

		$additional_commands.='<a onClick="$(\'#filters_stats\').toggle();">Статистика</a>';

		$ctrllinks.='
<div id="filters_stats">
<center><table><tr><td colspan="2" style="text-align:center"><a href="/gamereport/">Отчет об игроках</a> более подробен и точен.</td></tr><tr valign=top><td>';

		$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."roles WHERE status!=4 and todelete2!='1' and site_id=".$_SESSION["siteid"]);
		$a = mysql_fetch_array($result);
		$ctrllinks.='<b>Всего заявок</b>: '.$a[0].'<br>';
		$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."roles WHERE status!=4 and todelete2!='1' and team='0' and site_id=".$_SESSION["siteid"]);
		$a = mysql_fetch_array($result);
		if($a[0]>0) {
			$ctrllinks.='&nbsp;&nbsp;индивидуальных: '.$a[0].'<br>';
		}
		$sumindpeople=$a[0];
		$result=mysql_query("SELECT taken FROM ".$prefix."rolevacancy WHERE team='0' and taken!='' and site_id=".$_SESSION["siteid"]);
		while($a = mysql_fetch_array($result)) {
			unset($taken);
			$taken2='';
			$taken2=decode($a["taken"]);
			$taken2=str_ireplace(', ',',',$taken2);
			$taken=explode(',',$taken2);
			if($taken[0]=='') {
				unset($taken);
			}
			$sumindpeople+=count($taken);
		}
		$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."roles WHERE status!=4 and team='1' and site_id=".$_SESSION["siteid"]);
		$a = mysql_fetch_array($result);
		if($a[0]>0) {
			$ctrllinks.='&nbsp;&nbsp;командных: '.$a[0].'<br>';
		}
		$sumroleteamkolvo=0;
		$result=mysql_query("SELECT roleteamkolvo FROM ".$prefix."roles WHERE status!=4 and team='1' and site_id=".$_SESSION["siteid"]);
		while($a = mysql_fetch_array($result)) {
			$sumroleteamkolvo+=$a["roleteamkolvo"];
		}
		$result=mysql_query("SELECT taken FROM ".$prefix."rolevacancy WHERE team='1' and taken!='' and site_id=".$_SESSION["siteid"]);
		while($a = mysql_fetch_array($result)) {
			unset($taken);
			$taken2='';
			$taken2=decode($a["taken"]);
			$taken2=str_ireplace(', ',',',$taken2);
			$taken=explode(',',$taken2);
			if($taken[0]=='') {
				unset($taken);
			}
			$sumroleteamkolvo+=count($taken);
		}
		$ctrllinks.='<b>Всего игроков</b>: '.($sumindpeople+$sumroleteamkolvo).'<br>';
		if($sumroleteamkolvo>0) {
			if($sumindpeople>0) {
				$ctrllinks.='&nbsp;&nbsp;отдельных игроков: '.$sumindpeople.'<br>';
			}
			$ctrllinks.='&nbsp;&nbsp;игроков в командах: '.$sumroleteamkolvo.'<br>';
		}
		$ctrllinks.='<span title="Система автоматически анализирует цифры. В случае если вы используете нестандартный взнос или нестандартную форму записи в поле взнос, система может ошибиться."><b>Сдано взносов</b>: ';
		$summoneydone=0;
		$result=mysql_query("SELECT money FROM ".$prefix."roles WHERE moneydone='1' and todelete2!=1 and site_id=".$_SESSION["siteid"]);
		while($a = mysql_fetch_array($result)) {
			$money=str_replace('р.','',$a["money"]);
			$money=str_replace('р','',$money);
			$money=$money+0;
			$summoneydone+=$money;
		}
		$ctrllinks.=$summoneydone.'р.</span>';

		$result=mysql_query("SELECT money FROM ".$prefix."roles WHERE moneydone='1' and todelete2=1 and site_id=".$_SESSION["siteid"]);
		if(mysql_affected_rows($link)>0) {
			$ctrllinks.='<br><span title="Система автоматически анализирует цифры. В случае если вы используете нестандартный взнос или нестандартную форму записи в поле взнос, система может ошибиться."><b>Взносов в удаленных заявках</b>: ';
			$summoneydone=0;

			while($a = mysql_fetch_array($result)) {
				$money=str_replace('р.','',$a["money"]);
				$money=str_replace('р','',$money);
				$money=$money+0;
				$summoneydone+=$money;
			}
			$ctrllinks.=$summoneydone.'р.</span>';
		}

		$ctrllinks.='</td><td>';

		$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."rolevacancy WHERE site_id=".$_SESSION["siteid"]);
		$a = mysql_fetch_array($result);
		$ctrllinks.='<b>Всего вакансий</b>: '.$a[0].'<br>';
		$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."rolevacancy WHERE team='0' and site_id=".$_SESSION["siteid"]);
		$a = mysql_fetch_array($result);
		if($a[0]>0) {
			$ctrllinks.='&nbsp;&nbsp;индивидуальных: '.$a[0].'<br>';
		}
		$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."rolevacancy WHERE team='1' and site_id=".$_SESSION["siteid"]);
		$a = mysql_fetch_array($result);
		if($a[0]>0) {
			$ctrllinks.='&nbsp;&nbsp;командных: '.$a[0].'<br>';
		}
		$sumkolvo=0;
		$result=mysql_query("SELECT kolvo FROM ".$prefix."rolevacancy WHERE team='0' and site_id=".$_SESSION["siteid"]);
		while($a = mysql_fetch_array($result)) {
			$sumkolvo+=$a["kolvo"];
		}
		$sumteamkolvo=0;
		$result=mysql_query("SELECT teamkolvo, kolvo FROM ".$prefix."rolevacancy WHERE team='1' and site_id=".$_SESSION["siteid"]);
		while($a = mysql_fetch_array($result)) {
			$sumteamkolvo+=($a["teamkolvo"]*$a["kolvo"]);
		}
		$ctrllinks.='<b>Нужно игроков</b>: '.($sumteamkolvo+$sumkolvo).'<br>';
		if($sumteamkolvo>0) {
			if($sumkolvo>0) {
				$ctrllinks.='&nbsp;&nbsp;отдельных игроков: '.$sumkolvo.'<br>';
			}
			$ctrllinks.='&nbsp;&nbsp;игроков в командах: '.$sumteamkolvo.'<br>';
		}
		$ctrllinks.='<span title="Система автоматически анализирует цифры. В случае если вы используете нестандартный взнос или нестандартную форму записи в поле взнос, система может ошибиться."><b>Нужно взносов</b>: ';
		$summoneydone=0;
		$result=mysql_query("SELECT money FROM ".$prefix."roles WHERE site_id=".$_SESSION["siteid"]);
		while($a = mysql_fetch_array($result)) {
			$money=str_replace('р.','',$a["money"]);
			$money=str_replace('р','',$money);
			$money=$money+0;
			$summoneydone+=$money;
		}
		$ctrllinks.=$summoneydone.'р.</span>';

		$result=mysql_query("SELECT money FROM ".$prefix."roles WHERE todelete2=1 and site_id=".$_SESSION["siteid"]);
		if(mysql_affected_rows($link)>0) {
			if($_SESSION["viewdeleted"]) {
				$ctrllinks.='<br><a href="'.$server_absolute_path_site.'orders/action=viewdeleted_off"><b>Уйти из удаленных заявок</b></a>';
			}
			else {
				$ctrllinks.='<br><a href="'.$server_absolute_path_site.'orders/action=viewdeleted_on"><b>Посмотреть удаленные заявки</b></a>';
			}
		}

		$ctrllinks.='</td></tr></table></center><br /></div>';

		$additional_commands.='<a onClick="$(\'#filters_export\').toggle();">экспорт</a>';

		$ctrllinks.='
<div id="filters_export">
<b>Экспорт в</b>: <a href="'.$server_absolute_path_site.$kind.'/action=exportroles" target="_blank">Excel</a>, <a href="'.$server_absolute_path_site.$kind.'/action=exporttooffline" target="_blank">allrpg.offline</a>, <a href="'.$server_absolute_path_site.$kind.'/action=exporttobrain" target="_blank">PersonalBrain</a> (<a href="'.$server_absolute_path_offline.'">инструкции</a>)<br /><br /></div>';

		$obj_html=str_replace('<div class="indexer">', $ctrllinks.'<div class="indexer">', $obj_html);

		$obj_html=str_replace('<a href="'.$curdir.$kind.'/'.$object.'/act=add" class="ctrlink">[+] добавить заявку</a>', '<a href="'.$curdir.$kind.'/'.$object.'/act=add&roletype=0" class="ctrlink">[+] добавить индивидуальную заявку</a>', $obj_html);

		$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."rolefields WHERE team=1 and site_id=".$_SESSION["siteid"]);
		$a = mysql_fetch_array($result);
		if($a[0]>0) {
			$obj_html=str_replace('<a href="'.$curdir.$kind.'/'.$object.'/act=add&roletype=0" class="ctrlink">[+] добавить индивидуальную заявку</a>', '<a href="'.$curdir.$kind.'/'.$object.'/act=add&roletype=0" class="ctrlink">[+] добавить индивидуальную заявку</a><a href="'.$curdir.$kind.'/'.$object.'/act=add&roletype=1" class="ctrlink">[+] добавить командную заявку</a>', $obj_html);
		}
	}

	// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
	$pagetitle=h1line('Все заявки',$curdir.$kind.'/');
	if(!$donotshowthisrole) {
		$content2.='<div class="narrow">'.$obj_html.'</div>';
		if(($id!='' || $act=="add") && $actiontype=='') {
			$content2=preg_replace('#<div class="fieldname"[^>]+>Игрок</div>#','',$content2);
			if($id!='' && $print!=1 && $history!=1 && $vacancy_id) {
				$content2=preg_replace('#<h1 class="data_h1">Сюжеты и загрузы<\/h1>#','<h1 class="data_h1">Сюжеты и загрузы</h1><a href="'.$server_absolute_path_site.'roleslinks/act=add&vacancies=-'.$a_id["vacancy"].'-" id="create_alllinks" target="_blank">создать сюжет</a>',$content2);
			}
		}
	}
}
?>