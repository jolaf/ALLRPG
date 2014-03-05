<?php
include_once("../db.inc");
include_once("../classes_objects_allrpg.php");

session_start();

start_mysql();
# Установление соединения с MySQL-сервером

if($kind=="start") {
	$kind='';
}

$onload=Array(
	Array("kind","string",''),
	Array("sub","string",''),
	Array("id","integer",0),
	Array("page","integer",0),
	Array("orders","integer",0),
	Array("action","string",''),
	Array("sorting","integer",0),
	Array("temps","integer",0),
);
/* Правила обработки внешних GET и POST переменных, попадающих в код сайта, сайтом
	0 - название переменной;
	1 - тип переменной;
	2 - значение по умолчанию.
*/

for($a=0;$a<count($onload);$a++)
{
	if(isset($_POST[$onload[$a][0]])) {
		$$onload[$a][0]=encode($_POST[$onload[$a][0]]);
	}
	if(!isset($$onload[$a][0]) && isset($_GET[$onload[$a][0]])) {
		$$onload[$a][0]=encode($_GET[$onload[$a][0]]);
	}

	if($onload[$a][1]=="string") {
		if(!isset($$onload[$a][0]))
		{
			$$onload[$a][0]=$onload[$a][2];
		}
		else
		{
			$$onload[$a][0]=encode($$onload[$a][0]);
		}
	}
	elseif($onload[$a][1]=="integer")
	{
		if(!isset($$onload[$a][0]))
		{
			$$onload[$a][0]=$onload[$a][2];
		}
		else
		{
			settype($$onload[$a][0], "integer");
			if(!is_int($$onload[$a][0]))
			{
				$$onload[$a][0]=$onload[$a][2];
			}
		}
	}
}

if($temps!=0) {
	$_SESSION["temps"]=$temps;
}

$siteway='test';
$navprefix=$server_absolute_path.'temps/';

$result=mysql_query("SELECT * FROM ".$prefix."sites WHERE path='".$siteway."'");
$a = mysql_fetch_array($result);

$siteid=$a["id"];
$defcode=$a["defcode"];
$newscode=$a["newscode"];
$rolescode=$a["rolescode"];
$defrolefield=$a["sorter"];
$commentson=$a["commentson"];

$result=mysql_query("SELECT * FROM ".$prefix."temps WHERE id=".$_SESSION["temps"]);
$a = mysql_fetch_array($result);

$newsformat1=decode($a["newsformat1"]);
$newsformat2=decode($a["newsformat2"]);
$sep=decode($a["separ"]);
$res=decodetempcss($a["css"]);

$content='';

if($kind=="css") {
	header('Content-type: text/css');

	$content=decode($a['usercss']);
	if($a["menualign"]==1) {
		$content.='
.menukind {display: block;}';
	}
	else {
		$content.='
#nav a {float: left;}
#nav li {float: left;}
.mainMenuParentBtn {background: url('.$server_absolute_path.$direct.'/mootools/arrow_down.gif) right center no-repeat;}
.mainMenuParentBtnFocused {background: url('.$server_absolute_path.$direct.'/mootools/arrow_down_over.gif) right center no-repeat;}';
	}

	if($a["submenualign"]==1) {
		$content.='
.menusub {display: block;}';
	}

	$vals_f=$res[0];
	$vals_f_a=$res[1];

	for($i=0;$i<count($vals_f);$i++) {
		$content=str_ireplace("<!--".$vals_f[$i]['name']."-->", decode($vals_f_a[$vals_f[$i]['name']]), $content);
	}

	print($content);
	stop_mysql();
	exit;
}

$content=decode($a['htmlcode']);
$content=str_ireplace('<head>','<head>
<base href="'.$navprefix.'">',$content);

if($a["menualign"]==1) {
	$menualign='
';
	$navmenualign="vertical";
}
else {
	$menualign=decode($a["separkind"]);
	$navmenualign="horizontal";
}

if($a["submenualign"]==1) {
	$submenualign='
';
}
else {
	$submenualign=decode($a["separsub"]);
}

$result=mysql_query("SELECT * FROM ".$prefix."sites WHERE id=".$siteid);
$a = mysql_fetch_array($result);

if(strpos($content,'<!--navmootools-->')!==false) {
	$content=str_ireplace("</head>", '<link rel="stylesheet" href="'.$server_absolute_path.$direct.'/mootools/MenuMatic.css" type="text/css" media="screen" charset="utf-8" />
<!--[if lt IE 7]>
	<link rel="stylesheet" href="'.$server_absolute_path.$direct.'/mootools/MenuMatic-ie6.css" type="text/css" media="screen" charset="utf-8" />
<![endif]-->
</head>',$content);
}
$content=str_ireplace("</head>", '<link rel="stylesheet" type="text/css" href="'.$navprefix.'site.css">
</head>', $content);
$content=str_ireplace("<!--title-->", decode($a['title']), $content);

# всякое-старое, для совместимости
$content=str_ireplace("<!--style-->", '', $content);
$content=str_ireplace("<!--description-->", '', $content);
$content=str_ireplace("<!--keywords-->", '', $content);
# всякое-старое, для совместимости

$vals_f=$res[0];
$vals_f_a=$res[1];

for($i=0;$i<count($vals_f);$i++)
{
	if($vals_f[$i]['type']=='text' && $vals_f_a[$vals_f[$i]['name']]=='' && $vals_f[$i]['additional']=='file')
	{
		$content=str_ireplace('<img src="<!--'.$vals_f[$i]['name'].'-->">', '', $content);
		$content=str_ireplace(' background="<!--'.$vals_f[$i]['name'].'-->"', '', $content);
	}
	else
	{
		$content=str_ireplace("<!--".$vals_f[$i]['name']."-->", decode($vals_f_a[$vals_f[$i]['name']]), $content);
	}
}

# Система распределенных и наследуемых прав
$thisuseraccessgroups="";
# Система распределенных и наследуемых прав

$navmenu='<ul id="nav">';
$result=mysql_query("SELECT * FROM ".$prefix."pages WHERE parent=0 and content='{menu}' and site_id=".$siteid.$thisuseraccessgroups." order by code asc");
while($a = mysql_fetch_array($result)) {
	$navmenu.='<li><a href="'.menulink($a).'">'.decode($a["name"]).'</a>';
	$opened=false;
	$result2=mysql_query("SELECT * FROM ".$prefix."pages WHERE parent=".$a["id"]." and content='{menu}' and site_id=".$siteid.$thisuseraccessgroups." order by code asc");
	if(mysql_affected_rows($link)>0) {
		$navmenu.='<ul>';
		$opened=true;
		while($b = mysql_fetch_array($result2)) {
        	$navmenu.='<li><a href="'.menulink($b).'">'.decode($b["name"]).'</a>';
            $result3=mysql_query("SELECT * FROM ".$prefix."pages WHERE parent=".$b["id"]." and content!='{menu}' and site_id=".$siteid.$thisuseraccessgroups." and code!='1' order by name");
			if(mysql_affected_rows($link)>0) {
				$navmenu.='<ul>';
                while($c = mysql_fetch_array($result3)) {
                	$navmenu.='<li><a href="'.menulink($b).$c["id"].'/">';
                	if($c["name"]!='') {
                		$navmenu.=decode($c["name"]);
                	}
                	else {
                		$navmenu.='<i>без названия</i>';
                	}
                	$navmenu.='</a></li>';
                }
				$navmenu.='</ul>';
			}
        	$navmenu.='</li>';
		}
	}
	$result3=mysql_query("SELECT * FROM ".$prefix."pages WHERE parent=".$a["id"]." and content!='{menu}' and site_id=".$siteid.$thisuseraccessgroups." and code!='1' order by name");
	if(mysql_affected_rows($link)>0) {
		if(!$opened) {
			$navmenu.='<ul>';
			$opened=true;
		}
  		while($c = mysql_fetch_array($result3)) {
        	$navmenu.='<li><a href="'.menulink($a).$c["id"].'/">';
        	if($c["name"]!='') {
        		$navmenu.=decode($c["name"]);
        	}
        	else {
        		$navmenu.='<i>без названия</i>';
        	}
        	$navmenu.='</a></li>';
        }
	}
	if($opened) {
		$navmenu.='</ul>';
	}
	$navmenu.='</li>';
}
$navmenu.='</ul>';

$content=str_ireplace('<!--navlist-->',$navmenu,$content);

if(strpos($content,'<!--navmootools-->')!==false) {
	$navmootools='
<script src="http://www.google.com/jsapi"></script><script>google.load("mootools", "1.2.1");</script>

<!-- Load the MenuMatic Class -->
<script src="'.$server_absolute_path.$direct.'/mootools/MenuMatic_0.68.3.js" type="text/javascript" charset="utf-8"></script>

<!-- Create a MenuMatic Instance -->
<script type="text/javascript" >
	window.addEvent(\'domready\', function() {
		var myMenu = new MenuMatic(';
	if($navmenualign=="vertical") {
		$navmootools.='{ orientation:\'vertical\' }';
	}
	$navmootools.=');
	});
</script>';
	$content=str_ireplace('<!--navmootools-->',$navmootools,$content);
}

if($kind=='') {
	$result=mysql_query("SELECT * FROM ".$prefix."pages WHERE id=".$defcode." and content='{menu}' and site_id=".$siteid.$thisuseraccessgroups);
	$a = mysql_fetch_array($result);
	if($a["id"]=='') {
		$result=mysql_query("SELECT * FROM ".$prefix."pages WHERE content='{menu}' and parent=0 and site_id=".$siteid.$thisuseraccessgroups." order by code, name");
		$a = mysql_fetch_array($result);
	}
	if($a["parent"]==0) {
		$kindid=$a["id"];
		if($a["alias"]!='') {
			$kind=decode($a["alias"]);
		}
		else {
			$kind=$a["code"];
		}
	}
	else {
		if($a["alias"]!='') {
			$sub=decode($a["alias"]);
		}
		else {
			$sub=$a["code"];
		}
		$subid=$a["id"];
		$result=mysql_query("SELECT * FROM ".$prefix."pages WHERE id=".$a["parent"]." and site_id=".$siteid.$thisuseraccessgroups);
		$a = mysql_fetch_array($result);
		$kindid=$a["id"];
		$kind=$a["code"];
	}
}
elseif(is_numeric($kind)) {
	$result=mysql_query("SELECT * FROM ".$prefix."pages WHERE code=".$kind." and parent=0 and site_id=".$siteid.$thisuseraccessgroups);
	$a = mysql_fetch_array($result);
	$kindid=$a["id"];
	$kind=$a["code"];
	if($sub!='' && is_numeric($sub)) {
		$result2=mysql_query("SELECT * FROM ".$prefix."pages WHERE code=".$sub." and parent=".$a["id"]." and site_id=".$siteid.$thisuseraccessgroups);
		$b = mysql_fetch_array($result2);
		if($b["id"]!='') {
			$sub=$b["code"];
			$subid=$b["id"];
		}
		else {
			$id=$sub;
			unset($sub);
		}
	}
	elseif($sub!='') {
		echo('<!--ошибка построения пути сайта: alias внутри цифрового kindа-->');
	}
}
else {
	$result=mysql_query("SELECT * FROM ".$prefix."pages WHERE alias='".$kind."' and site_id=".$siteid.$thisuseraccessgroups);
	$a = mysql_fetch_array($result);
	if($a["parent"]==0) {
		// этот алиас ведет на kind
		$kindid=$a["id"];
		$kind=decode($a["alias"]);
		if($sub!='' && is_numeric($sub)) {
        	$result2=mysql_query("SELECT * FROM ".$prefix."pages WHERE code=".$sub." and parent=".$a["id"]." and site_id=".$siteid.$thisuseraccessgroups);
			$b = mysql_fetch_array($result2);
			if($b["id"]!='') {
				$sub=$b["code"];
				$subid=$b["id"];
			}
			else {
				$id=$sub;
				unset($sub);
			}
		}
		elseif($sub!='') {
			echo('<!--ошибка построения пути сайта: alias внутри другого aliasа-->');
		}
	}
	elseif($a["parent"]>0) {
		// этот алиас ведет на sub
        if($sub!='') {
        	$id=$sub;
        }
        $subid=$a["id"];
		$sub=decode($a["alias"]);
        $result2=mysql_query("SELECT * FROM ".$prefix."pages WHERE id=".$a["parent"]." and parent=0 and site_id=".$siteid.$thisuseraccessgroups);
		$b = mysql_fetch_array($result2);
		$kindid=$b["id"];
		if($b["alias"]!='') {
			$kind=decode($b["alias"]);
		}
		else {
			$kind=$b["code"];
		}
	}
}
/*echo('kind: '.$kind.'<br>');
echo('sub: '.$sub.'<br>');
echo('id: '.$id.'<br>');
echo('orders: '.$orders.'<br>');*/

if($kind!='') {
	if(is_numeric($sub) || $sub=='') {
		if($sub!='') {
			$siteway2=$navprefix.$kind.'/'.$sub.'/';
		}
		else {
			$siteway2=$navprefix.$kind.'/';
		}
	}
	else {
		$siteway2=$navprefix.$sub.'/';
	}
}
else {
	$siteway2=$navprefix;
}

$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."pages WHERE parent=0 AND active='1' and site_id=".$siteid.$thisuseraccessgroups);
$a = mysql_fetch_array($result);
$ato=$a[0];
$i=1;

$result=mysql_query("SELECT * FROM ".$prefix."pages WHERE parent=0 AND active='1' and site_id=".$siteid.$thisuseraccessgroups." order by code asc");
while($a = mysql_fetch_array($result)) {
	$menu.='<a href="'.menulink($a).'" class="menukind" id="kind'.$a["code"].'">'.$a["name"].'</a>';

	if($i<$ato) {
		if($ext!='' && $menualign==' | ') {
			$menu.='';
		}
		else {
			$menu.=$menualign;
		}
	}
	else {
		$menu.='';
	}
	$i++;
}

if($kindid!='') {
	$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."pages WHERE parent=".$kindid." AND active='1' AND content='{menu}' and site_id=".$siteid.$thisuseraccessgroups);
	$a = mysql_fetch_array($result);
	$ato=$a[0];
	$i=1;

	$result=mysql_query("SELECT * FROM ".$prefix."pages WHERE parent=".$kindid." AND active='1' AND content='{menu}' and site_id=".$siteid.$thisuseraccessgroups." order by code asc");
	while($a = mysql_fetch_array($result)) {
		$submenu.='<a href="'.menulink($a).'" class="menusub" id="kind'.$kind.'sub'.$a["code"].'">'.$a["name"].'</a>';

		if($i<$ato) {
			$submenu.=$submenualign.'
';
		}
		else {
			$submenu.='
';
		}
		$i++;
	}
}

$commentson2=false;

if(!($kindid==$newscode && $subid=='') && !($kindid==$rolescode && $subid=='') && $subid!=$newscode && $subid!=$rolescode) {
	if($id!='') {
		$result=mysql_query("SELECT * FROM ".$prefix."pages WHERE parent=".$kindid." and site_id=".$siteid." and active='1' and id=".$id.$thisuseraccessgroups);
		$a = mysql_fetch_array($result);
		if($subid!='') {
			$result=mysql_query("SELECT * FROM ".$prefix."pages WHERE parent=".$subid." and site_id=".$siteid." and active='1' and id=".$id.$thisuseraccessgroups);
			$a = mysql_fetch_array($result);
		}
		if($a["id"]!='') {
			$content2.=decode($a["content"]);
			$lastchangedate=$a["date"];
			if($a["nocomments"]!='1')
			{
				$commentson2=true;
			}
		}
	}
	else {
		$result=mysql_query("SELECT * FROM ".$prefix."pages WHERE parent=".$kindid." and site_id=".$siteid." and active='1' and code='1' and content!='{menu}'".$thisuseraccessgroups);
		$a = mysql_fetch_array($result);
		if($subid!='') {
			$result=mysql_query("SELECT * FROM ".$prefix."pages WHERE parent=".$subid." and site_id=".$siteid." and active='1' and code='1' and content!='{menu}'".$thisuseraccessgroups);
			$a = mysql_fetch_array($result);
		}
		if($a["id"]!='')
		{
			$content2.=decode($a["content"]);
			$lastchangedate=$a["date"];
			$id=$a["id"];
			if($a["nocomments"]!='1')
			{
				$commentson2=true;
			}
		}
		else {
			$result2=mysql_query("SELECT * FROM ".$prefix."pages WHERE parent=".$kindid." and site_id=".$siteid." and active='1' and content='{menu}'".$thisuseraccessgroups." order by code");
			$b = mysql_fetch_array($result2);
			$result=mysql_query("SELECT * FROM ".$prefix."pages WHERE parent=".$b["id"]." and site_id=".$siteid." and active='1' and code='1' and content!='{menu}'".$thisuseraccessgroups);
			$a = mysql_fetch_array($result);
			if($a["id"]!='') {
				$subid=$b["id"];
				$sub=$b["code"];
				$content2.=decode($a["content"]);
				$lastchangedate=$a["date"];
				$id=$a["id"];
				if($a["nocomments"]!='1')
				{
					$commentson2=true;
				}
			}
		}
	}
}
elseif(($kindid==$newscode && $subid=='') || $subid==$newscode)
{
	$result=mysql_query("SELECT * FROM ".$prefix."pages where id=".$kindid." and active='1' and site_id=".$siteid.$thisuseraccessgroups);
	$a = mysql_fetch_array($result);
	if($subid!='') {
		$result2=mysql_query("SELECT * FROM ".$prefix."pages where id=".$subid." and active='1' and site_id=".$siteid.$thisuseraccessgroups);
		$b = mysql_fetch_array($result2);
	}
	if($a["id"]!='' && ($b["id"]!='' || $subid==''))
	{
		$itsapage=false;
		if($subid!='') {
			if($id=='') {
				$result3=mysql_query("SELECT * FROM ".$prefix."pages where parent=".$subid." and active='1' and content!='{menu}' and site_id=".$siteid.$thisuseraccessgroups);
				$c = mysql_fetch_array($result3);
			}
			else {
				$result3=mysql_query("SELECT * FROM ".$prefix."pages where parent=".$subid." and id=".$id." and active='1' and content!='{menu}' and site_id=".$siteid.$thisuseraccessgroups);
				$c = mysql_fetch_array($result3);
				if($c["id"]!='') {
					$itsapage=true;
				}
			}
			$content2.=decode($c["content"]);
		}
		elseif($kindid!='') {
			if($id=='') {
				$result3=mysql_query("SELECT * FROM ".$prefix."pages where parent=".$kindid." and active='1' and content!='{menu}' and site_id=".$siteid.$thisuseraccessgroups);
				$c = mysql_fetch_array($result3);
			}
			else {
				$result3=mysql_query("SELECT * FROM ".$prefix."pages where parent=".$kindid." and id=".$id." and active='1' and content!='{menu}' and site_id=".$siteid.$thisuseraccessgroups);
				$c = mysql_fetch_array($result3);
				if($c["id"]!='') {
					$itsapage=true;
				}
			}
			$content2.=decode($c["content"]);
		}
		if($id>0 && !$itsapage)
		{
			$result=mysql_query("SELECT * FROM ".$prefix."news WHERE id=".$id);
			$a = mysql_fetch_array($result);
			if($a["active"]=='1' && $a["main"]!='')
			{
				$content2.=$newsformat2;
				$content2=str_ireplace("<!--date-->", date("d.m.Y", strtotime($a["date2"])), $content2);
				$content2=str_ireplace("<!--name-->", decode($a["name"]), $content2);
				$content2=str_ireplace("<!--text-->", decode($a["main"]), $content2);
				if(strpos($content2,"<!--author-->")!==false && $a["author"]!='')
				{
					$result2=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$a["author"]);
					$b = mysql_fetch_array($result2);
					$content2=str_ireplace("<!--author-->", usname($b,true,true), $content2);
				}
				if(strpos($content2,"<!--source-->")!==false && $a["sour"]!='')
				{
					$sour=$a["sour"];
					if(strpos($sour,"http://")!==false)
					{
						$content2=str_ireplace("<!--source-->", '<a href="'.decode($a["sour"]).'">'.decode($a["sour"]).'</a>', $content2);
					}
					else
					{
						$content2=str_ireplace("<!--source-->", decode($a["sour"]), $content2);
					}
				}
				elseif(strpos($content2,"<!--source-->")!==false)
				{
					$content2=str_ireplace("<!--source-->", 'не указан', $content2);
				}
				$lastchangedate=$a["date"];
				$content2=str_ireplace("<!--linkstart-->", '', $content2);
				$content2=str_ireplace("<!--linkfinish-->", '', $content2);
				$content2=str_ireplace("<!--moreinfo-->", '', $content2);
				$content2=str_ireplace("<!--source-->", '', $content2);
				$content2=str_ireplace("<!--author-->", '', $content2);
				$content2=str_ireplace("<!--name-->", '', $content2);
				$content2=str_ireplace("<!--date-->", '', $content2);
				$content2=str_ireplace("<!--text-->", '', $content2);
			}
		}
		else
		{
			$start=$page*20;
			$result=mysql_query("SELECT * FROM ".$prefix."news where active='1' and site_id=".$siteid." order by date2 desc limit ".$start.",20");
			while($a = mysql_fetch_array($result))
			{
				if($a["active"]=='1' && $a["content"]!='')
				{
					$linkstart='<a href="'.$siteway2.$a["id"].'/">';
					$linkfinish='</a>';
					$content2.=$newsformat1;
					$content2=str_ireplace("<!--date-->", date("d.m.Y", strtotime($a["date2"])), $content2);
					$content2=str_ireplace("<!--name-->", decode($a["name"]), $content2);
					$content2=str_ireplace("<!--text-->", decode($a["content"]), $content2);
					if(strpos($content2,"<!--author-->")!==false && $a["author"]!='')
					{
						$result2=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$a["author"]);
						$b = mysql_fetch_array($result2);
						$content2=str_ireplace("<!--author-->", usname($b,true,true), $content2);
					}
					if(strpos($content2,"<!--source-->")!==false && $a["sour"]!='')
					{
						$sour=$a["sour"];
						if(strpos($sour,"http://")!==false)
						{
							$content2=str_ireplace("<!--source-->", '<a href="'.decode($a["sour"]).'">'.decode($a["sour"]).'</a>', $content2);
						}
						else
						{
							$content2=str_ireplace("<!--source-->", decode($a["sour"]), $content2);
						}
					}
					elseif(strpos($content2,"<!--source-->")!==false)
					{
						$content2=str_ireplace("<!--source-->", 'не указан', $content2);
					}
					if($a["main"]!='' && strpos($content2,"<!--moreinfo-->")!==false)
					{
						$content2=str_ireplace("<!--moreinfo-->", $linkstart.'Подробнее&#8230;'.$linkfinish, $content2);
					}
					if($a["main"]!='' && strpos($content2,"<!--linkstart-->")!==false && strpos($content2,"<!--linkfinish-->")!==false)
					{
						$content2=str_ireplace("<!--linkstart-->", $linkstart, $content2);
						$content2=str_ireplace("<!--linkfinish-->", $linkfinish, $content2);
					}
					if($lastchangedate=='') {
						$lastchangedate=$a["date"];
					}
					$content2=str_ireplace("<!--linkstart-->", '', $content2);
					$content2=str_ireplace("<!--linkfinish-->", '', $content2);
					$content2=str_ireplace("<!--moreinfo-->", '', $content2);
					$content2=str_ireplace("<!--source-->", '', $content2);
					$content2=str_ireplace("<!--author-->", '', $content2);
					$content2=str_ireplace("<!--name-->", '', $content2);
					$content2=str_ireplace("<!--date-->", '', $content2);
					$content2=str_ireplace("<!--text-->", '', $content2);
				}
			}
			$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."news WHERE active='1' and site_id=".$siteid);
			$a=mysql_fetch_array($result);
			$starttotal=$a[0];

			$content2.='<center>';
			if($start-20>=0)
			{
				$content2.='<a href="'.$siteway2.'page='.($page-1).'">Следующие 20 новостей</a>';
				if($start+20<$starttotal)
				{
					$content2.=' | ';
				}
			}
			if($start+20<$starttotal)
			{
				$content2.='<a href="'.$siteway2.'page='.($page+1).'">Предыдущие 20 новостей</a>';
			}
			$content2.='</center>';
		}
	}
}
elseif($kindid==$rolescode || $subid==$rolescode) {
	$lastchangedate=time();
	$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."rolevacancy WHERE site_id=".$siteid);
	$a=mysql_fetch_array($result);
	if($a[0]==0) {
		$orders=1;
	}

	$itsapage=false;
	if($subid!='') {
		if($id=='') {
			$result3=mysql_query("SELECT * FROM ".$prefix."pages where parent=".$subid." and active='1' and content!='{menu}' and site_id=".$siteid.$thisuseraccessgroups);
			$c = mysql_fetch_array($result3);
		}
		else {
			$result3=mysql_query("SELECT * FROM ".$prefix."pages where parent=".$subid." and id=".$id." and active='1' and content!='{menu}' and site_id=".$siteid.$thisuseraccessgroups);
			$c = mysql_fetch_array($result3);
			if($c["id"]!='') {
				$itsapage=true;
			}
		}
		$content2.=decode($c["content"]);
	}
	elseif($kindid!='') {
		if($id=='') {
			$result3=mysql_query("SELECT * FROM ".$prefix."pages where parent=".$kindid." and active='1' and content!='{menu}' and site_id=".$siteid.$thisuseraccessgroups);
			$c = mysql_fetch_array($result3);
		}
		else {
			$result3=mysql_query("SELECT * FROM ".$prefix."pages where parent=".$kindid." and id=".$id." and active='1' and content!='{menu}' and site_id=".$siteid.$thisuseraccessgroups);
			$c = mysql_fetch_array($result3);
			if($c["id"]!='') {
				$itsapage=true;
			}
		}
		$content2.=decode($c["content"]);
	}

	$result=mysql_query("SELECT * FROM ".$prefix."roleslocat WHERE site_id=".$siteid." LIMIT 0,1");
	$a=mysql_fetch_array($result);
	if($a["id"]!='') {
		$havelocats=true;
		$alllocats=make5fieldtree(true,$prefix."roleslocat","parent",0," AND site_id=".$siteid,"code asc, name asc",1,"id","name",1000000);
		$alllocats[0][1]='Без названия';
		for($i=0;$i<count($alllocats);$i++) {
			if($alllocats[$i][0]>0) {
				$alllocats[$i][1]=locatpath($alllocats[$i][0]);
			}
			$alllocats_ids.=$alllocats[$i][0].', ';
		}
		$alllocats_ids=substr($alllocats_ids,0,strlen($alllocats_ids)-2);
	}

	if($orders==1) {
		// заявки
		if($id>0 && !$itsapage) {
			$result=mysql_query("SELECT * FROM ".$prefix."roles where id=".$id." and site_id=".$siteid);
			$a = mysql_fetch_array($result);

			$lastchangedate=$a["date"];

			$result4=mysql_query("SELECT * FROM ".$prefix."roleslocat WHERE id=".$a["locat"]." and site_id=".$siteid);
			$d=mysql_fetch_array($result4);
			if($a["id"]!='' && $d["rights"]!=1) {
				$result3=mysql_query("SELECT * FROM ".$prefix."sites WHERE id=".$siteid);
				$c=mysql_fetch_array($result3);
				$result5=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$a["player_id"]);
				$e=mysql_fetch_array($result5);
				$result6=mysql_query("SELECT * FROM ".$prefix."rolevacancy WHERE id=".$a["vacancy"]." and site_id=".$siteid);
				$f=mysql_fetch_array($result6);
				$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."roles WHERE vacancy=".$f["id"]." and status=3");
				$b=mysql_fetch_array($result2);

				$content2.='<center><div id="cb_editor" style="text-align: justify"><h3 style="text-align: center;">'.decode($a["sorter"]).'</h3>';
				if(($b[0]<$f["kolvo"] && $c["status2"]==2 && $c["testing"]!='1') || ($b[0]>1 && $d["rights"]!=1)) {
					$content2.='<center><hr>';
					if($b[0]<$f["kolvo"] && $c["status2"]==2 && $c["testing"]!='1') {
						if(isset($_COOKIE["user_id"])) {
							$content2.='<a href="'.$server_absolute_path.'order/act=add&subobj='.$siteid.'&roletype='.$f["team"].'&wantrole='.$f["id"].'">';
						}
						else {
							$content2.='<a href="'.$server_absolute_path.'register/redirectobj=order&redirectid='.$siteid.'&redirectparams=roletype:'.$f["team"].'*wantrole:'.$f["id"].'">';
						}
						$content2.='<b>Подать заявку на такую же роль</b></a>';
					}
					if($b[0]>1 && $d["rights"]!=1) {
						if($b[0]<$f["kolvo"] && $c["status2"]==2 && $c["testing"]!='1') {
							$content2.=' | ';
						}
						$content2.='<a href="'.$siteway2.'orders=1&wantrole='.$f["id"].'"><b>Поданные на ту же роль заявки</b></a>';
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
					$content2.='<b>Роль</b>: <a href="'.$siteway2.$f["id"].'/">'.decode($f["name"]).'</a><br>';
				}
				$content2.='<hr>';

				// динамические поля заявки
				$rolefields=virtual_structure("SELECT * from ".$prefix."rolefields where team='".$a["team"]."' and site_id=".$siteid." order by rolecode","allinfo","role");
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
			$result=mysql_query("SELECT * FROM ".$prefix."users where id in (SELECT player_id FROM ".$prefix."roles where site_id=".$siteid.")");
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

			$result=mysql_query("SELECT * FROM ".$prefix."rolefields where site_id=".$siteid." and (id IN (SELECT sorter from ".$prefix."sites where id=".$siteid.") or id IN (SELECT sorter2 from ".$prefix."sites where id=".$siteid.")) order by team asc");
			$a = mysql_fetch_array($result);
			$sorter=decode($a["rolename"]);
			$a = mysql_fetch_array($result);
			if($a["rolename"]!='') {
				$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."roles where team='1'");
				$b = mysql_fetch_array($result2);
				if($b[0]>0) {
					$sorter.=' / '.decode($a["rolename"]);
				}
			}
			if($sorting==0) {
				$sorting=1;
			}
			if($havelocats) {
				if(encode($_GET["wantrole"])!='') {
					$query="SELECT t1.*, t2.name as locatname, t2.id as locatid, t3.sid, t3.nick, t3.fio, t3.hidesome, t4.name as vacancyname FROM ".$prefix."users t3, ".$prefix."roles t1 LEFT JOIN ".$prefix."roleslocat t2 ON t2.id=t1.locat LEFT JOIN ".$prefix."rolevacancy t4 ON t4.id=t1.vacancy WHERE (t2.rights!=1 || t1.locat='') and t3.id=t1.player_id and t4.id=".encode($_GET["wantrole"])." and t1.site_id=".$siteid." order by FIELD(t2.id, ".$alllocats_ids.")";

					$query2="SELECT COUNT(t1.id) FROM ".$prefix."users t3, ".$prefix."roles t1 LEFT JOIN ".$prefix."roleslocat t2 ON t2.id=t1.locat LEFT JOIN ".$prefix."rolevacancy t4 ON t4.id=t1.vacancy WHERE (t2.rights!=1 || t1.locat='') and t3.id=t1.player_id and t4.id=".encode($_GET["wantrole"])." and t1.site_id=".$siteid;
				}
				elseif(encode($_GET["wantrole2"])!='') {
					$query="SELECT t1.*, t2.name as locatname, t2.id as locatid, t3.sid, t3.nick, t3.fio, t3.hidesome, t4.name as vacancyname FROM ".$prefix."users t3, ".$prefix."roles t1 LEFT JOIN ".$prefix."roleslocat t2 ON t2.id=t1.locat LEFT JOIN ".$prefix."rolevacancy t4 ON t4.id=t1.vacancy WHERE (t2.rights!=1 || t1.locat='') and t3.id=t1.player_id and t4.id=".encode($_GET["wantrole2"])." and t1.status=3 and t1.site_id=".$siteid." order by FIELD(t2.id, ".$alllocats_ids.")";

					$query2="SELECT COUNT(t1.id) FROM ".$prefix."users t3, ".$prefix."roles t1 LEFT JOIN ".$prefix."roleslocat t2 ON t2.id=t1.locat LEFT JOIN ".$prefix."rolevacancy t4 ON t4.id=t1.vacancy WHERE (t2.rights!=1 || t1.locat='') and t3.id=t1.player_id and t4.id=".encode($_GET["wantrole2"])." and t1.status=3 and t1.site_id=".$siteid;
				}
				else {
					$query="SELECT t1.*, t2.name as locatname, t2.id as locatid, t3.sid, t3.nick, t3.fio, t3.hidesome, t4.name as vacancyname FROM ".$prefix."users t3, ".$prefix."roles t1 LEFT JOIN ".$prefix."roleslocat t2 ON t2.id=t1.locat LEFT JOIN ".$prefix."rolevacancy t4 ON t4.id=t1.vacancy WHERE (t2.rights!=1 || t1.locat='') and t3.id=t1.player_id and t1.site_id=".$siteid." order by FIELD(t2.id, ".$alllocats_ids.")";

					$query2="SELECT COUNT(t1.id) FROM ".$prefix."users t3, ".$prefix."roles t1 LEFT JOIN ".$prefix."roleslocat t2 ON t2.id=t1.locat LEFT JOIN ".$prefix."rolevacancy t4 ON t4.id=t1.vacancy WHERE (t2.rights!=1 || t1.locat='') and t3.id=t1.player_id and t1.site_id=".$siteid;
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
					$query="SELECT t1.*, t2.name as locatname, t2.id as locatid, t3.sid, t3.nick, t3.fio, t3.hidesome, t4.name as vacancyname FROM ".$prefix."users t3, ".$prefix."roles t1 LEFT JOIN ".$prefix."roleslocat t2 ON t2.id=t1.locat LEFT JOIN ".$prefix."rolevacancy t4 ON t4.id=t1.vacancy WHERE (t2.rights!=1 || t1.locat='') and t3.id=t1.player_id and t4.id=".encode($_GET["wantrole"])." and t1.site_id=".$siteid." order by ";

					$query2="SELECT COUNT(t1.id) FROM ".$prefix."users t3, ".$prefix."roles t1 LEFT JOIN ".$prefix."roleslocat t2 ON t2.id=t1.locat LEFT JOIN ".$prefix."rolevacancy t4 ON t4.id=t1.vacancy WHERE (t2.rights!=1 || t1.locat='') and t3.id=t1.player_id and t4.id=".encode($_GET["wantrole"])." and t1.site_id=".$siteid;
				}
				elseif(encode($_GET["wantrole2"])!='') {
					$query="SELECT t1.*, t2.name as locatname, t2.id as locatid, t3.sid, t3.nick, t3.fio, t3.hidesome, t4.name as vacancyname FROM ".$prefix."users t3, ".$prefix."roles t1 LEFT JOIN ".$prefix."roleslocat t2 ON t2.id=t1.locat LEFT JOIN ".$prefix."rolevacancy t4 ON t4.id=t1.vacancy WHERE (t2.rights!=1 || t1.locat='') and t3.id=t1.player_id and t4.id=".encode($_GET["wantrole2"])." and t1.status=3 and t1.site_id=".$siteid." order by ";

					$query2="SELECT COUNT(t1.id) FROM ".$prefix."users t3, ".$prefix."roles t1 LEFT JOIN ".$prefix."roleslocat t2 ON t2.id=t1.locat LEFT JOIN ".$prefix."rolevacancy t4 ON t4.id=t1.vacancy WHERE (t2.rights!=1 || t1.locat='') and t3.id=t1.player_id and t4.id=".encode($_GET["wantrole2"])." and t1.status=3 and t1.site_id=".$siteid;
				}
				else {
					$query="SELECT t1.*, t2.name as locatname, t2.id as locatid, t3.sid, t3.nick, t3.fio, t3.hidesome, t4.name as vacancyname FROM ".$prefix."users t3, ".$prefix."roles t1 LEFT JOIN ".$prefix."roleslocat t2 ON t2.id=t1.locat LEFT JOIN ".$prefix."rolevacancy t4 ON t4.id=t1.vacancy WHERE (t2.rights!=1 || t1.locat='') and t3.id=t1.player_id and t1.site_id=".$siteid." order by ";

					$query2="SELECT COUNT(t1.id) FROM ".$prefix."users t3, ".$prefix."roles t1 LEFT JOIN ".$prefix."roleslocat t2 ON t2.id=t1.locat LEFT JOIN ".$prefix."rolevacancy t4 ON t4.id=t1.vacancy WHERE (t2.rights!=1 || t1.locat='') and t3.id=t1.player_id and t1.site_id=".$siteid;
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
			$content2.='<br><center><div id="cb_editor">';
			$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."rolevacancy WHERE site_id=".$siteid);
			$b=mysql_fetch_array($result2);
			if($b[0]>0) {
				$content2.='<b>[<a href="'.$siteway2.'">К СЕТКЕ РОЛЕЙ</a>]</b><br>';
			}
			if(encode($_GET["wantrole"])!='' || encode($_GET["wantrole2"])!='') {
				$content2.='<b>[<a href="'.$siteway2.'orders=1">ВСЕ ЗАЯВКИ</a>]</b><br>';
			}
			$result2=mysql_query($query2);
			$b=mysql_fetch_array($result2);
			$pagetotal=$b[0];
			$content2.='<div style="text-align: right"><b>Всего заявок</b>: '.$b[0].'</div>';

			$content2.='
			<br>
			<table class="menutable">
			<tr>';
			$content2.='
			<td class="menu"><a href="'.$siteway2.'orders=1&sorting=';
			if($sorting==1) {
				$content2.='2" title="[отсортировать]" onMouseOver="document.getElementById(\'arrow\').src=\'down.gif\'" onMouseOut="document.getElementById(\'arrow\').src=\'up.gif\'">Игрок</a> <img src="up.gif" id="arrow" border=0>';
			}
			elseif($sorting==2) {
				$content2.='1" title="[отсортировать]" onMouseOver="document.getElementById(\'arrow\').src=\'up.gif\'" onMouseOut="document.getElementById(\'arrow\').src=\'down.gif\'">Игрок</a> <img src="down.gif" id="arrow" border=0>';
			}
			else {
				$content2.='1" title="[отсортировать]">Игрок</a>';
			}
			$content2.='</td>';

			$content2.='
			<td class="menu"><a href="'.$siteway2.'orders=1&sorting=';
			if($sorting==3) {
				$content2.='4" title="[отсортировать]" onMouseOver="document.getElementById(\'arrow\').src=\'down.gif\'" onMouseOut="document.getElementById(\'arrow\').src=\'up.gif\'">'.$sorter.'</a> <img src="up.gif" id="arrow" border=0>';
			}
			elseif($sorting==4) {
				$content2.='3" title="[отсортировать]" onMouseOver="document.getElementById(\'arrow\').src=\'up.gif\'" onMouseOut="document.getElementById(\'arrow\').src=\'down.gif\'">'.$sorter.'</a> <img src="down.gif" id="arrow" border=0>';
			}
			else {
				$content2.='3" title="[отсортировать]">'.$sorter.'</a>';
			}
			$content2.='</td>';

			$content2.='
			<td class="menu"><a href="'.$siteway2.'orders=1&sorting=';
			if($sorting==5) {
				$content2.='6" title="[отсортировать]" onMouseOver="document.getElementById(\'arrow\').src=\'down.gif\'" onMouseOut="document.getElementById(\'arrow\').src=\'up.gif\'">Статус</a> <img src="up.gif" id="arrow" border=0>';
			}
			elseif($sorting==6) {
				$content2.='5" title="[отсортировать]" onMouseOver="document.getElementById(\'arrow\').src=\'up.gif\'" onMouseOut="document.getElementById(\'arrow\').src=\'down.gif\'">Статус</a> <img src="down.gif" id="arrow" border=0>';
			}
			else {
				$content2.='5" title="[отсортировать]">Статус</a>';
			}
			$content2.='</td>';

			$result3=mysql_query("SELECT COUNT(id) FROM ".$prefix."rolevacancy where site_id=".$siteid);
			$c = mysql_fetch_array($result3);
			$kolvovacancy=$c[0];
			if($kolvovacancy>0) {
				$content2.='
				<td class="menu"><a href="'.$siteway2.'orders=1&sorting=';
				if($sorting==7) {
					$content2.='8" title="[отсортировать]" onMouseOver="document.getElementById(\'arrow\').src=\'down.gif\'" onMouseOut="document.getElementById(\'arrow\').src=\'up.gif\'">Роль</a> <img src="up.gif" id="arrow" border=0>';
				}
				elseif($sorting==8) {
					$content2.='7" title="[отсортировать]" onMouseOver="document.getElementById(\'arrow\').src=\'up.gif\'" onMouseOut="document.getElementById(\'arrow\').src=\'down.gif\'">Роль</a> <img src="down.gif" id="arrow" border=0>';
				}
				else {
					$content2.='7" title="[отсортировать]">Роль</a>';
				}
				$content2.='</td>';
			}

			$content2.='
			</tr>';

			$prevlocatid=-1;
			while($a=mysql_fetch_array($result)) {
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
					$content2.='</td></tr><tr>';
				}
				$content2.='
			<tr>
			<td>
			<a href="'.$siteway2.$a["id"].'/orders=1">';
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
			<a href="'.$siteway2.$a["id"].'/orders=1">';
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
			<a href="'.$siteway2.$a["id"].'/orders=1">';
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
			<a href="'.$siteway2.$a["id"].'/orders=1">';
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

			$content2.='
			</table>
			</div></center>';

			$result=mysql_query("SELECT * FROM ".$prefix."roles where site_id=".$siteid." order by date desc limit 0,1");
			$a = mysql_fetch_array($result);
			$lastchangedate=$a["date"];
		}
	}
	else {
		// сетка ролей
		if($id>0 && !$itsapage) {
			$result=mysql_query("SELECT * FROM ".$prefix."rolevacancy where id=".$id." and site_id=".$siteid);
			$a = mysql_fetch_array($result);

			$lastchangedate=$a["date"];

			if($a["id"]!='') {
				$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."roles WHERE vacancy=".$a["id"]);
				$b=mysql_fetch_array($result2);
				$result5=mysql_query("SELECT COUNT(id) FROM ".$prefix."roles WHERE vacancy=".$a["id"]." and status=3");
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
				$result3=mysql_query("SELECT * FROM ".$prefix."sites WHERE id=".$siteid);
				$c=mysql_fetch_array($result3);
				$result4=mysql_query("SELECT * FROM ".$prefix."roleslocat WHERE id=".$a["locat"]." and site_id=".$siteid);
				$d=mysql_fetch_array($result4);
				$content2.='<center><div id="cb_editor" style="text-align: justify"><h3 style="text-align: center;">'.decode($a["name"]).'</h3>';
				if(($e[0]<$a["kolvo"] && $c["status2"]==2 && $c["testing"]!='1') || ($b[0]>0 && $d["rights"]!=1) || ($e[0]>0 && $d["rights"]!=1)) {
					$content2.='<center><hr>';
					if($e[0]<$a["kolvo"] && $c["status2"]==2 && $c["testing"]!='1') {
						if(isset($_COOKIE["user_id"])) {
							$content2.='<a href="'.$server_absolute_path.'order/act=add&subobj='.$siteid.'&roletype='.$a["team"].'&wantrole='.$id.'">';
						}
						else {
							$content2.='<a href="'.$server_absolute_path.'register/redirectobj=order&redirectid='.$siteid.'&redirectparams=roletype:'.$a["team"].'*wantrole:'.$id.'">';
						}
						$content2.='<b>Подать заявку на данную роль</b></a>';
					}
					if($b[0]>0 && $d["rights"]!=1) {
						if($e[0]<$a["kolvo"] && $c["status2"]==2 && $c["testing"]!='1') {
							$content2.=' | ';
						}
						$content2.='<a href="'.$siteway2.'orders=1&wantrole='.$id.'"><b>Поданные на роль заявки</b></a>';
					}
					if($e[0]>0 && $d["rights"]!=1) {
						if(($e[0]<$a["kolvo"] && $c["status2"]==2 && $c["testing"]!='1') || ($b[0]>0 && $d["rights"]!=1)) {
							$content2.=' | ';
						}
						$content2.='<a href="'.$siteway2.'orders=1&wantrole2='.$id.'"><b>Принятые на роль заявки</b></a>';
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
			if($havelocats) {
				$query="SELECT t1.*, t2.name as locatname, t2.id as locatid FROM ".$prefix."rolevacancy t1 LEFT JOIN ".$prefix."roleslocat t2 ON t2.id=t1.locat WHERE t1.site_id=".$siteid." order by FIELD(t2.id, ".$alllocats_ids."), t1.code ASC, t1.name ASC";
			}
			else {
				$query="SELECT t1.* FROM ".$prefix."rolevacancy t1 WHERE t1.site_id=".$siteid." order by t1.code ASC, t1.name ASC";
			}
			$result=mysql_query($query);
			$content2.='<br><center><div id="cb_editor">';
			$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."roles WHERE site_id=".$siteid);
			$b=mysql_fetch_array($result2);
			if($b[0]>0) {
				$content2.='<b>[<a href="'.$siteway2.'orders=1">К СПИСКУ ВСЕХ ПОДАННЫХ ЗАЯВОК</a>]</b><br>';
			}
			$content2.='
			<br>
			<table class="menutable">
			<tr>
			<td class="menu">Роль</td>
			<td class="menu">Описание</td>
			<td class="menu" colspan=2>Игроки</td>
			</tr>';

			$prevlocatid=-1;
			while($a=mysql_fetch_array($result)) {
				$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."roles WHERE vacancy=".$a["id"]." and status=3 and todelete!=1 and todelete2!=1");
				$b=mysql_fetch_array($result2);
				$result5=mysql_query("SELECT COUNT(id) FROM ".$prefix."roles WHERE vacancy=".$a["id"]." and status!=4 and todelete!=1 and todelete2!=1");
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
					$content2.='</td></tr><tr>';
					if($a["locatid"]!=0) {
						$result3=mysql_query("SELECT * FROM ".$prefix."roleslocat WHERE id=".$a["locatid"]);
						$c=mysql_fetch_array($result3);
						if(decode($c["description"])!='') {
							$content2.='<tr><td colspan=4 class="description">'.decode2($c["description"]).'</td></tr>';
						}
					}
				}
				$rows=1;
				if($c["rights"]==1) {
					$rows=0;
					if($b[0]<$a["kolvo"]) {
						$rows+=1;
					}
					if($b[0]>0) {
						$rows+=1;
					}
					if($b[0]<$a["kolvo"]) {
						$result4=mysql_query("SELECT COUNT(id) FROM ".$prefix."roles WHERE vacancy=".$a["id"]." and (status=2 || status=1) and todelete!=1 and todelete2!=1");
						$d=mysql_fetch_array($result4);
						if($d[0]+count($maybetaken)>0) {
							$rows+=1;
						}
					}
				}
				else {
 					if($b[0]<$a["kolvo"]) {
						$rows=$e[0]+count($taken)+count($maybetaken);
						$rows+=1;
					}
					else {
						$rows=$b[0];
					}
				}
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
				$content2.=' rowspan='.$rows.'>
			<a href="'.$siteway2.$a["id"].'/">'.decode($a["name"]).$teamkolvo.'</a>
			</td>';
				if(decode($a["content"])!='') {
					$content2.='
			<td rowspan='.$rows.' style="text-align: justify">
			'.decode($a["content"]).'
			</td>';
				}
				$newstring=false;
				if($b[0]<$a["kolvo"]) {
					if(isset($_COOKIE["user_id"])) {
						$content2.='<td colspan=2><a href="'.$server_absolute_path.'order/act=add&subobj='.$siteid.'&roletype='.$a["team"].'&wantrole='.$a["id"].'">';
					}
					else {
						$content2.='<td colspan=2><a href="'.$server_absolute_path.'register/redirectobj=order&redirectid='.$siteid.'&redirectparams=roletype:'.$a["team"].'*wantrole:'.$a["id"].'">';
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
	                }
				}
				else {
					$result3=mysql_query("SELECT player_id, sorter FROM ".$prefix."roles WHERE vacancy=".$a["id"]." and status=3 and todelete!=1 and todelete2!=1");
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

					if($b[0]<$a["kolvo"]) {
						$result3=mysql_query("SELECT player_id, sorter FROM ".$prefix."roles WHERE vacancy=".$a["id"]." and (status=2 || status=1) and todelete!=1 and todelete2!=1");
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
				}
			}

			$content2.='
			</table>
			</div></center>';

			$result=mysql_query("SELECT * FROM ".$prefix."rolevacancy where site_id=".$siteid." order by date desc limit 0,1");
			$a = mysql_fetch_array($result);
			$lastchangedate=$a["date"];
		}
	}
}

$result=mysql_query("SELECT * FROM ".$prefix."sites WHERE id=".$siteid);
$a = mysql_fetch_array($result);
if($a["status"]==2 || $a["status"]==3)
{
	$content=str_ireplace("<!--maintext-->", $content2, $content);
}
else
{
	$content2='<h1 style="text-align: center">На данный момент сайт не работает.<br> Приносим свои извинения.</h1>';
	$content=str_ireplace("<!--maintext-->", $content2, $content);
	$commentson2=false;
}

if($sub=='') {
	$sub=0;
}
if($kind=='') {
	$kind=0;
}
$content=str_ireplace("<!--mainmenu-->", $menu, $content);
$content=str_ireplace("<!--submenu-->", $submenu, $content);
$content=str_ireplace("<!--id-->", $id.'', $content);
$content=str_ireplace("<!--sub-->", $sub.'', $content);
$content=str_ireplace("<!--kind-->", $kind.'', $content);
if($sub==0) {
	$sub='';
}
if($kind==0) {
	$kind='';
}

$separ='<span class="innerpath">';

$result=mysql_query("SELECT * FROM ".$prefix."pages WHERE id=".$kindid.$thisuseraccessgroups);
$a = mysql_fetch_array($result);

if($kindid==$rolescode)
{
	if($orders==1) {
		$separ.='<a href="'.menulink($a).'orders=1">Заявки</a>';
		$separ2="Заявки";
	}
	else {
		$separ.='<a href="'.menulink($a).'">Сетка ролей</a>';
		$separ2="Сетка ролей";
	}
	$content=str_ireplace("<!--kindname-->", $separ2, $content);
}
elseif($kindid!='')
{
	$content=str_ireplace("<!--kindname-->", decode($a["name"]), $content);
	$separ.='<a href="'.menulink($a).'">'.decode($a["name"]).'</a>';
	$separ2=decode($a["name"]);
}

$result2=mysql_query("SELECT * FROM ".$prefix."pages WHERE id=".$subid.$thisuseraccessgroups);
$b = mysql_fetch_array($result2);

if($subid==$rolescode)
{
	if($orders==1) {
		$separ.='<a href="'.menulink($b).'orders=1">Заявки</a>';
		$separ2.=$sep."Заявки";
		$content=str_ireplace("<!--subname-->", "Заявки", $content);
	}
	else {
		$separ.='<a href="'.menulink($b).'">Сетка ролей</a>';
		$separ2.=$sep."Сетка ролей";
		$content=str_ireplace("<!--subname-->", "Сетка ролей", $content);
	}
}
elseif($subid!='')
{
	$content=str_ireplace("<!--subname-->", decode($b["name"]), $content);
	if(decode($b["name"])!='') {
		$separ.=$sep.'<a href="'.menulink($b).'">'.decode($b["name"]).'</a>';
		$separ2.=$sep.decode($b["name"]);
	}
}
$sip='';
if($subid!='') {
	$sip=menulink($b);
}
else {
	$sip=menulink($a);
}

if($id>0)
{
	if(($kindid==$newscode || $subid==$newscode) && !$itsapage)
	{
		$result=mysql_query("SELECT * FROM ".$prefix."news WHERE active='1' and id=".$id);
		$a = mysql_fetch_array($result);
		if(decode($a["name"])!='') {
			$content=str_ireplace("<!--idname-->", decode($a["name"]), $content);
			$separ.=$sep.'<a href="'.$sip.$id.'/">'.decode($a["name"]).'</a>';
			$separ2.=$sep.decode($a["name"]);
		}
		else {
			$result=mysql_query("SELECT * FROM ".$prefix."pages where id=".$id." and active='1' and content!='{menu}' and site_id=".$siteid.$thisuseraccessgroups);
			$a = mysql_fetch_array($result);
			if(decode($a["name"])!='') {
				$content=str_ireplace("<!--idname-->", decode($a["name"]), $content);
				$separ.=$sep.'<a href="'.$sip.$id.'/">'.decode($a["name"]).'</a>';
				$separ2.=$sep.decode($a["name"]);
			}
		}
	}
	elseif(($kindid==$rolescode || $subid==$rolescode) && !$itsapage)
	{
		if($orders==1) {
			$result=mysql_query("SELECT * FROM ".$prefix."roles WHERE id=".$id);
			$a = mysql_fetch_array($result);
			$content=str_ireplace("<!--idname-->", decode($a["sorter"]), $content);
			if(decode($a["sorter"])!='')
			{
				$separ.=$sep.'<a href="'.$sip.$id.'/">'.decode($a["sorter"]).'</a>';
				$separ2.=$sep.decode($a["sorter"]);
			}
		}
		else {
			$result=mysql_query("SELECT * FROM ".$prefix."rolevacancy WHERE id=".$id);
			$a = mysql_fetch_array($result);
			$content=str_ireplace("<!--idname-->", decode($a["name"]), $content);
			if(decode($a["name"])!='')
			{
				$separ.=$sep.'<a href="'.$sip.$id.'/">'.decode($a["name"]).'</a>';
				$separ2.=$sep.decode($a["name"]);
			}
		}
	}
	else
	{
		$result=mysql_query("SELECT * FROM ".$prefix."pages WHERE id=".$id);
		$a = mysql_fetch_array($result);
		$content=str_ireplace("<!--idname-->", decode($a["name"]), $content);
		if(decode($a["name"])!='') {
			$separ.=$sep.'<a href="'.$sip.$id.'/">'.decode($a["name"]).'</a>';
			$separ2.=$sep.decode($a["name"]);
		}
		elseif($a["code"]!=1) {
			$separ.=$sep.'<a href="'.$sip.$id.'/">без названия</a>';
			$separ2.=$sep.'без названия';
		}
	}
}
$separ.='</span>';
$content=str_ireplace("<!--innerpath-->", $separ, $content);
$content=str_ireplace("<!--innerpathnolinks-->", $separ2, $content);

$content=str_ireplace("<!--lastchangedate-->", '<span class="lastchangedate">'.date("d.m.Y в H:i", $lastchangedate).'</span>', $content);

if(eregi("<!--allsubs-->",$content))
{
	$result2=mysql_query("SELECT * FROM ".$prefix."pages WHERE parent=0 AND active='1' AND content='{menu}' and site_id=".$siteid.$thisuseraccessgroups." order by code asc");
	while($b = mysql_fetch_array($result2))
	{
		$allsubs.='<div id="popupsub'.$b["code"].'" style="position: absolute; display: none;">';
		$result=mysql_query("SELECT * FROM ".$prefix."pages WHERE parent=".$b["id"]." AND active='1' AND content='{menu}' and site_id=".$siteid.$thisuseraccessgroups." order by code asc");
		while($a = mysql_fetch_array($result))
		{
			$allsubs.='<a href="'.$menulink($a).'" class="menusub" id="kind'.$kind.'sub'.$a["code"].'">'.$a["name"].'</a>';

			if($i<$ato)
			{
				$allsubs.=$submenualign.'
';
			}
			else
			{
				$allsubs.='
';
			}
			$i++;
		}
		$allsubs.='</div>';
	}
	$content=str_ireplace("<!--allsubs-->", $allsubs, $content);
}

if($action=="commentadd")
{
	if(isset($_COOKIE["user_id"]) && isset($_COOKIE["pass"]))
	{
		$result=mysql_query("SELECT * FROM ".$prefix."users where id=".encode($_COOKIE["user_id"])." and pass='".encode($_COOKIE["pass"])."'");
		$a = mysql_fetch_array($result);
		if($a["id"]!='')
		{
			$result2=mysql_query("SELECT * FROM ".$prefix."pages where id=".$id);
			$b = mysql_fetch_array($result2);
			if($b["name"]=='')
			{
				if($sub!=0 && $sub!='')
				{
					$result2=mysql_query("SELECT * FROM ".$prefix."pages where id=".$subid);
					$b = mysql_fetch_array($result2);
					if($b["name"]!='')
					{
						$false_id=$subid;
					}
					else
					{
						$false_id=$kindid;
					}
				}
				else
				{
					$false_id=$kindid;
				}
			}
			else
			{
				$false_id=$id;
			}
			$dat=time();
			mysql_query("INSERT INTO ".$prefix."pagecomments (user_id,false_id,page_id,content,site_id,date) VALUES (".$a["id"].", ".$false_id.", ".$id.", '".encode(strip_tags($_POST["content"]))."', ".$siteid.", ".$dat.")");
			$commentadded=true;
		}
	}
}
if(strpos($content,"<!--comments-->")!==false && $commentson=='1' && $commentson2)
{
	$comments='<br><br><hr>';
	if($commentadded)
	{
		$comments.='<center><font color="red"><b>Ваш комментарий успешно добавлен.</b></font></center><hr>';
	}

	if(isset($_COOKIE["user_id"]) && isset($_COOKIE["pass"]))
	{
		$result=mysql_query("SELECT * FROM ".$prefix."users where id=".encode($_COOKIE["user_id"])." and pass='".encode($_COOKIE["pass"])."'");
		$a = mysql_fetch_array($result);
		if($a["id"]!='')
		{
			$comments.='
<form action="'.$siteway2.'" method="post" enctype="multipart/form-data">
<input type="hidden" name="kind" value="'.$kind.'">';
			if($subid!='')
			{
				$comments.='<input type="hidden" name="sub" value="'.$sub.'">';
			}
			$comments.='
<input type="hidden" name="id" value="'.$id.'">
<input type="hidden" name="action" value="commentadd">
<b>Добавить комментарий:</b><br>
<textarea name="content" class="comment_field"></textarea><br><br>
<center><input type=submit value="отправить комментарий" class="comment_send"></center>
</form>
<hr>';
		}
		else
		{
			$comments.='
<b>Для того чтобы оставить комментарий к странице, вам необходимо:</b>
<ul>
<li><a href="http://www.allrpg.info/register/">зарегистрироваться</a> на сайте allrpg.info;
<li>залогиниться на сайте <a href="http://www.allrpg.info">allrpg.info</a>, поставив галочку «запомнить».
</ul>
<hr>
';
		}
	}
	else
	{
		$comments.='
<b>Для того чтобы оставить комментарий к странице, вам необходимо:</b>
<ul>
<li><a href="http://www.allrpg.info/register/">зарегистрироваться</a> на сайте allrpg.info;
<li>залогиниться на сайте <a href="http://www.allrpg.info">allrpg.info</a>, поставив галочку «запомнить».
</ul>
<hr>
';
	}

	$start=$page*20;
	$result=mysql_query("SELECT * FROM ".$prefix."pagecomments where site_id=".$siteid." and page_id=".$id." order by date desc limit ".$start.",20");
	while($a = mysql_fetch_array($result))
	{
		$result2=mysql_query("SELECT * FROM ".$prefix."users where id=".$a["user_id"]);
		$b = mysql_fetch_array($result2);

		$comments.='<span class="comment_user">'.usname($b,true,true).'</span> <span class="comment_date">('.date("d.m.Y в H:i",$a["date"]).')</span><br>
<span class="comment_content">'.decode($a["content"]).'</span><hr>';
	}
	$comments.='<center><span class="comment_count">Страницы комментариев: ';
	$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."pagecomments where site_id=".$siteid." and page_id=".$id);
	$a=mysql_fetch_array($result);
	$totcomms=$a[0];
	$j=1;
	for($i=0;$i<$totcomms;$i=$i+20)
	{
		if($j-1!=$page)
		{
			$comments.='<a href="'.$siteway2.$id.'/page='.($j-1).'">'.$j.'</a> | ';
		}
		else
		{
			$comments.=$j.' | ';
		}
		$j++;
	}
	if($j>1)
	{
		$comments=substr($comments,0,strlen($comments)-3);
	}
	else
	{
		$comments.='1';
	}
	$comments.='</span></center>';
}
$content=str_ireplace("<!--comments-->", $comments, $content);

$result=mysql_query("SELECT * FROM ".$prefix."sites WHERE id=".$siteid);
$a = mysql_fetch_array($result);
if($a["usetemp"]==1) {
	if(file_exists("index.html")) {
		header("Location: index.html");
	}
	elseif(file_exists("index.htm")) {
		header("Location: index.htm");
	}
	else {
		header("Location: ".$server_absolute_path."error404.php");
	}
}
else
{
	if($content2=='')
	{
		$result=mysql_query("SELECT * FROM ".$prefix."pages WHERE parent=0 and site_id=".$siteid." and (code=".$kind." or alias='".$kind."')".$thisuseraccessgroups);
		$a = mysql_fetch_array($result);
		if($a["http"]!='')
		{
			header("Location: ".$a["http"]);
		}
		else
		{
			$result=mysql_query("SELECT * FROM ".$prefix."pages WHERE id=".$id." and site_id=".$siteid.$thisuseraccessgroups);
			$a = mysql_fetch_array($result);
			if($a["http"]!='')
			{
				header("Location: ".$a["http"]);
			}
			else
			{
				header("Location: ".$server_absolute_path."error404.php");
			}
		}
	}
	else
	{
		print($content);
	}
}
# Вывод основного содержания страницы

stop_mysql();
# Разрыв соединения с MySQL-сервером

$globaltimer=microtime(true)-$globaltimer;
echo('
<!-- execution time: '.$globaltimer.'s-->');

#*************************************************************
function menulink($a) {
	global
		$prefix,
		$thisuseraccessgroups,
		$navprefix,
		$siteid;

	$content=$navprefix;
   	if($a["http"]=='') {
    	if($a["alias"]!='') {
			$content.=decode($a["alias"]).'/';
		}
		else {
			if($a["parent"]==0) {
				$content.=$a["code"].'/';
			}
			else {
				$result2=mysql_query("SELECT * FROM ".$prefix."pages WHERE parent=0 and content='{menu}' and site_id=".$siteid.$thisuseraccessgroups." and id=".$a["parent"]);
				$b = mysql_fetch_array($result2);
				if($b["alias"]!='') {
					$content.=decode($b["alias"]).'/'.$a["code"].'/';
				}
				else {
					$content.=$b["code"].'/'.$a["code"].'/';
				}
			}
		}
	}
	elseif(strpos($a["http"],"http://")!==false) {
		$content=decode($a["http"]);
	}
	else {
		$content.=decode($a["http"]);
	}

	return $content;
}
#*************************************************************
function decodetempcss($css) {

	$css=decode($css);
	$pos = strpos($css, "]\r\n");
	while (!($pos===false)) {
		$st1 = substr($css,0,$pos+1);

		$pos2 = strpos($st1, "]");
		$ce1 = substr($st1,1,$pos2-1);
		$st1 = substr($st1,$pos2+1,strlen($st1));
		$pos2 = strpos($st1, "]");
		$ce2 = substr($st1,1,$pos2-1);
		$st1 = substr($st1,$pos2+1,strlen($st1));
		$pos2 = strpos($st1, "]");
		$ce3 = substr($st1,1,$pos2-1);
		$st1 = substr($st1,$pos2+1,strlen($st1));
		$pos2 = strpos($st1, "]");
		$ce4 = substr($st1,1,$pos2-1);
		$st1 = substr($st1,$pos2+1,strlen($st1));

		if($ce1==1)
		{
			$vals_f[] = Array('type'=>"text",'name'=>$ce2,'sname'=>$ce4);
		}
		elseif($ce1==2)
		{
			$vals_f[] = Array('type'=>"text",'name'=>$ce2,'sname'=>$ce4,'additional'=>'file');
		}
		elseif($ce1==3)
		{
			$vals_f[] = Array('type'=>"colorpicker",'name'=>$ce2,'sname'=>$ce4,'default'=>'');
		}

		$vals_f_a[$ce2]=decode($ce3);

		$css = substr($css,$pos+3,strlen($css));
		$pos = strpos($css, "]\r\n");
		if ($pos === false) break;
	}

	if($css!='')
	{
		$st1 = $css;

		$pos2 = strpos($st1, "]");
		$ce1 = substr($st1,1,$pos2-1);
		$st1 = substr($st1,$pos2+1,strlen($st1));
		$pos2 = strpos($st1, "]");
		$ce2 = substr($st1,1,$pos2-1);
		$st1 = substr($st1,$pos2+1,strlen($st1));
		$pos2 = strpos($st1, "]");
		$ce3 = substr($st1,1,$pos2-1);
		$st1 = substr($st1,$pos2+1,strlen($st1));
		$pos2 = strpos($st1, "]");
		$ce4 = substr($st1,1,$pos2-1);
		$st1 = substr($st1,$pos2+1,strlen($st1));

		if($ce1==1)
		{
			$vals_f[] = Array('type'=>"text",'name'=>$ce2,'sname'=>$ce4);
		}
		elseif($ce1==2)
		{
			$vals_f[] = Array('type'=>"text",'name'=>$ce2,'sname'=>$ce4,'additional'=>'file');
		}
		elseif($ce1==3)
		{
			$vals_f[] = Array('type'=>"colorpicker",'name'=>$ce2,'sname'=>$ce4,'default'=>'#FFFFFF');
		}

		$vals_f_a[$ce2]=decode($ce3);
	}

	$result[0]=$vals_f;
	$result[1]=$vals_f_a;
	return($result);
}
#*************************************************************
function locatpath($id) {
	global
		$prefix,
		$siteid;

	$result=mysql_query("SELECT * FROM ".$prefix."roleslocat WHERE id=".$id." and site_id=".$siteid);
	$a=mysql_fetch_array($result);
	if($a["parent"]==0) {
		$return=decode($a["name"]);
	}
	else {
		$return=locatpath($a["parent"]);
		$return.=' –» '.decode($a["name"]);
	}
	return($return);
}
?>