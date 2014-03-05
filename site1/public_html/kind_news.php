<?
$pagetitle=h1line('Новости');
if($object=='news') {
	$object=1;
}
$content2.='<div class="narrow">';
if($id!='')
{
	if($object>1) {
		$result=mysql_query("SELECT t1.*, t2.path, t2.title FROM ".$prefix."news t1 LEFT JOIN ".$prefix."sites t2 ON t2.id=t1.site_id WHERE t1.active='1' and t1.site_id=".$object." and t2.testing!='1' and t1.id=".$id);
	}
	elseif($object==1) {
		$result=mysql_query("SELECT * FROM ".$prefix."news WHERE active='1' and site_id=0 and id=".$id);
	}
	$a = mysql_fetch_array($result);
	if($a["id"]!='') {
		$content2.='<h1>'.decode($a["name"]).'</h1>';
		if($object>1) {
			if($a["path"]!='') {
				$content2.='<a href="'.$lead1.$a["path"].$lead2.'">'.decode($a["title"]).'</a>';
			}
			elseif(decode($a["path2"])!='') {
   				if(strpos(decode($a["path2"]),'http://')===false && strpos(decode($a["path2"]),'www.')===false) {
   					$content2.='<a href="http://'.decode($a["path2"]).'">'.decode($a["title"]).'</a>';
   				}
   				else {
   					$content2.='<a href="'.decode($a["path2"]).'">'.decode($a["title"]).'</a>';
   				}
   			}
			else {
				$content2.=decode($a["title"]);
			}
		}
		else {
			$content2.='allrpg.info';
		}
		$content2.='<br><br>';
		if(strip_tags(decode($a["main"]))!='') {
			$content2.=decode($a["main"]).'<br>';
			if(substr(decode($a["main"]),strlen(decode($a["main"]))-4,strlen(decode($a["main"])))!="</p>" && substr(decode($a["main"]),strlen(decode($a["main"]))-6,strlen(decode($a["main"])))!="</div>") {
				$content2.='<br>';
			}
		}
		else {
			$content2.=decode($a["content"]).'<br>';
			if(substr(decode($a["content"]),strlen(decode($a["content"]))-4,strlen(decode($a["content"])))!="</p>" && substr(decode($a["content"]),strlen(decode($a["content"]))-6,strlen(decode($a["content"])))!="</div>") {
				$content2.='<br>';
			}
		}
		$content2.='<div style="text-align: right; font-weight: bold;">';
		if($a["sour"]!='') {
			$content2.='Источник: ';
			if(eregi('http://',$a["sour"])) {
				$content2.='<a href="'.decode($a["sour"]).'">'.decode($a["sour"]).'</a>';
			}
			else {
				$content2.=decode($a["sour"]);
			}
			$content2.='<br>';
		}
		$result2=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$a["author"]);
		$b = mysql_fetch_array($result2);
		$content2.='Автор: '.usname($b,true,true).'<br>';
		$content2.='Опубликовано: '.date("d.m.Y",strtotime($a["date2"]));
		$content2.='</div>';
	}
}
else {
	$result=mysql_query("SELECT COUNT(t1.id) FROM ".$prefix."news t1 LEFT JOIN ".$prefix."sites t2 ON t2.id=t1.site_id WHERE t1.active='1' and (t2.testing!='1' || t1.site_id=0)");
	$a = mysql_fetch_array($result);
	$count=$a[0];

	$result=mysql_query("SELECT COUNT(t1.id) FROM ".$prefix."news t1 LEFT JOIN ".$prefix."sites t2 ON t2.id=t1.site_id WHERE t1.active='1' and (t2.testing!='1' || t1.site_id=0)");
	$a = mysql_fetch_array($result);

	$result=mysql_query("SELECT t1.*, t2.title, t2.path FROM ".$prefix."news t1 LEFT JOIN ".$prefix."sites t2 ON t2.id=t1.site_id WHERE t1.active='1' and (t2.testing!='1' or t1.site_id=0) order by t1.id desc LIMIT ".($page*25).",25");
	while($a = mysql_fetch_array($result)) {
		if($a["site_id"]==0) {
			$a["site_id"]=1;
		}
		$content2.=shownews($a,true);
	}
	$content2.='<br>'.pagecount($object,$count,25);
}
$content2.='<br><br></div>';
?>