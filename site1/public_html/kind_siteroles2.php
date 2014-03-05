<?php
if($object!='') {
	$subobj=$object;
}

$showonlyacceptedroles=false;
$result=mysql_query("SELECT * FROM ".$prefix."sites WHERE id=".$subobj);
$a=mysql_fetch_array($result);
if($a["showonlyacceptedroles"]=='1') {
	$showonlyacceptedroles=true;
}

if($a["alter_rolefield"]>0) {
		$queryelemname='virtual'.$a["alter_rolefield"];
		if($a["alter_byname"]=='1') {
			$alter_byname=true;
		}
		$result=mysql_query("SELECT * FROM ".$prefix."rolefields WHERE site_id=".$subobj." and id=".$a["alter_rolefield"]);
		$a=mysql_fetch_array($result);
		$roletype=$a["roletype"];

		$css=decode($a["rolevalues"]);
		$pos = strpos($css, "]\r\n");
		while (!($pos===false)) {
			$st1 = substr($css,0,$pos+1);

			$pos2 = strpos($st1, "]");
			$ce1 = substr($st1,1,$pos2-1);
			$st1 = substr($st1,$pos2+1,strlen($st1));
			$pos2 = strpos($st1, "]");
			$ce2 = substr($st1,1,$pos2-1);
			$st1 = substr($st1,$pos2+1,strlen($st1));

			$rolevalues[] = Array(decode($ce1),decode($ce2));

			$css = substr($css,$pos+3,strlen($css));
			$pos = strpos($css, "]\r\n");
			if ($pos === false) break;
		}

		$st1 = $css;

		$pos2 = strpos($st1, "]");
		$ce1 = substr($st1,1,$pos2-1);
		$st1 = substr($st1,$pos2+1,strlen($st1));
		$pos2 = strpos($st1, "]");
		$ce2 = substr($st1,1,$pos2-1);
		$st1 = substr($st1,$pos2+1,strlen($st1));

		$rolevalues[] = Array(decode($ce1),decode($ce2));
		if($alter_byname) {
			foreach ($rolevalues as $key => $row) {
				$rolevalues_sort[$key]  = strtolower($row[1]);
			}
			array_multisort($rolevalues_sort, SORT_ASC, $rolevalues);
		}
		$rolevalues=array_merge(Array(Array('0','Не определено')),$rolevalues);

		$result=mysql_query("SELECT * FROM ".$prefix."users u,".$prefix."roles r where u.id = r.player_id AND r.site_id = ".$subobj);
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

		$z=0;
		for($i=0;$i<count($rolevalues);$i++) {
			if($roletype=="multiselect") {
	        	$query="SELECT t1.* FROM ".$prefix."users t3, ".$prefix."roles t1 LEFT JOIN ".$prefix."roleslocat t2 ON t2.id=t1.locat WHERE (t2.rights!=1 || t1.locat='') and t3.id=t1.player_id and t1.todelete!=1 and t1.todelete2!=1 and t1.site_id=".$subobj." and ";
	        	if($rolevalues[$i][0]==0) {
	        		$query.='not (';
	        		for($j=1;$j<count($rolevalues);$j++) {
	        			$query.='t1.allinfo REGEXP "\\\['.$queryelemname.'\\\]\\\[[^]]*-'.$rolevalues[$j][0].'-[^]]*" OR ';
	        		}
	        		$query=substr($query,0,strlen($query)-4);
	        		$query.=')';
	        	}
	        	else {
	        		$query.='t1.allinfo REGEXP "\\\['.$queryelemname.'\\\]\\\[[^]]*-'.$rolevalues[$i][0].'-[^]]*"';
	        	}
	        	$query.=' order by ';
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
				$result=mysql_query($query);
                while($a=mysql_fetch_array($result)) {
	        		$orderslist[$z][0]=$a["id"];
	        		$orderslist[$z][1]=$i;
	        		$z++;
	        	}
			}
			elseif($roletype=="select") {
	        	$query="SELECT t1.* FROM ".$prefix."users t3, ".$prefix."roles t1 LEFT JOIN ".$prefix."roleslocat t2 ON t2.id=t1.locat WHERE (t2.rights!=1 || t1.locat='') and t3.id=t1.player_id and t1.todelete!=1 and t1.todelete2!=1 and t1.site_id=".$subobj." and ";
	        	if($rolevalues[$i][0]==0) {
	        		$query.='not (';
	        		for($j=1;$j<count($rolevalues);$j++) {
	        			$query.='t1.allinfo REGEXP "\\\['.$queryelemname.'\\\]\\\['.$rolevalues[$j][0].']" OR ';
	        		}
	        		$query=substr($query,0,strlen($query)-4);
	        		$query.=')';
	        	}
	        	else {
	        		$query.='t1.allinfo REGEXP "\\\['.$queryelemname.'\\\]\\\['.$rolevalues[$i][0].']"';
	        	}
	        	$query.=' order by ';
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
				$result=mysql_query($query);
                while($a=mysql_fetch_array($result)) {
	        		$orderslist[$z][0]=$a["id"];
	        		$orderslist[$z][1]=$i;
	        		$z++;
	        	}
			}
		}
		$pagetotal=count($orderslist);

		$result2=mysql_query("SELECT * FROM ".$prefix."sites WHERE id=".$subobj);
		$b=mysql_fetch_array($result2);

		$pagetitle=h1line(decode($b["title"]).' – заявки');

		$content2.='<center><div id="cb_editor">';
		$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."rolevacancy WHERE site_id=".$subobj);
		$b=mysql_fetch_array($result2);
		if($b[0]>0) {
			$content2.='<span class="gui-btn"><span><a href="'.$server_absolute_path.'siteroles/'.$subobj.'/">К сетке ролей</a></span></span><br>';
		}
		$content2.='<div style="text-align: right"><b>Всего заявок</b>: '.$pagetotal.'</div>';

		$content2.='<table cellpadding="0" cellspacing="0" border="0" width=100% align=center>
		<tr valign="bottom">
		<td>
		<br>
		<table class="menutable">
		<tr>';
		$content2.='
		<td class="menu"><a href="'.$server_absolute_path.$kind.'/'.$subobj.'/sorting=';
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
		<td class="menu"><a href="'.$server_absolute_path.$kind.'/'.$subobj.'/sorting=';
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
		<td class="menu"><a href="'.$server_absolute_path.$kind.'/'.$subobj.'/sorting=';
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

		$content2.='
		</tr>';

		$i=0;
		for($i=0;$i<count($orderslist);$i++) {
            $result=mysql_query("SELECT t1.*, t3.sid, t3.nick, t3.fio, t3.hidesome FROM ".$prefix."roles t1, ".$prefix."users t3 WHERE t3.id=t1.player_id and t1.id=".$orderslist[$i][0]);
			$a=mysql_fetch_array($result);
			if(!$showonlyacceptedroles || $a["status"]==3) {
				$team='';
				if($a["team"]==1) {
					$team="командная";
				}
				else {
					$team="индивидуальная";
				}
				if($prevfieldid!=$orderslist[$i][1]) {
					$prevfieldid=$orderslist[$i][1];
					$content2.='
			<tr><td class="locations" colspan=3>';
					$content2.=$rolevalues[$orderslist[$i][1]][1];
					$content2.='</td></tr><tr>';
				}
				$content2.='
			<tr>
			<td>
			<a href="'.$server_absolute_path.'siteroles/'.$subobj.'/'.$a["id"].'/orders=1">';
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
			<a href="'.$server_absolute_path.'siteroles/'.$subobj.'/'.$a["id"].'/orders=1">';
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
			<a href="'.$server_absolute_path.'siteroles/'.$subobj.'/'.$a["id"].'/orders=1">';
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
			}
		}

		$content2.='
		</table>
		</td>
		</tr>
		</table>
		</div></center>';
}
if($content2!='') {
	$content2='<div class="narrow">'.$content2.'</div>';
}
?>