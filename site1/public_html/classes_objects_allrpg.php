<?php
function getuser($id) {
	global
		$prefix;

	$result=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$id);
	return mysql_fetch_array($result);
}

function getuser_sid($sid) {
	global
		$prefix;

	$result=mysql_query("SELECT * FROM ".$prefix."users WHERE sid=".$sid);
	return mysql_fetch_array($result);
}

function usname($a, $sid=false, $link=false) {
	global
		$_SESSION,
		$_COOKIE,
		$server_absolute_path_info;

	$result='';
	if($link)
	{
		if($_SESSION["user_id"]!='' || isset($_COOKIE["user_id"]))
		{
			$result.='<a href="'.$server_absolute_path_info.'users/'.$a["sid"].'/">';
		}
	}
	if(strpos($a["hidesome"],'-10-')===false && $a["fio"]!='')
	{
		$result.=decode($a["fio"]);
	}
	if(strpos($a["hidesome"],'-0-')===false && $a["nick"]!='')
	{
		if(strpos($a["hidesome"],'-10-')===false && $a["fio"]!='')
		{
			$result.=' (';
		}
		$result.=decode($a["nick"]);
		if(strpos($a["hidesome"],'-10-')===false && $a["fio"]!='')
		{
			$result.=')';
		}
	}
	if((strpos($a["hidesome"],'-10-')!==false || $a["fio"]=='') && (strpos($a["hidesome"],'-0-')!==false || $a["nick"]=='') && $sid)
	{
		$result.='ИНП '.$a["sid"];
	}
	if($result=='') {
		$result='скрыто';
	}
	if($link)
	{
		if($_SESSION["user_id"]!='' || isset($_COOKIE["user_id"]))
		{
			$result.='</a>';
		}
	}

	return($result);
}
#*************************************************************
function usname2($a, $sid=false, $link=false, $class='') {
	global
		$_SESSION,
		$_COOKIE,
		$server_absolute_path_info;

	$result='';
	if($link)
	{
		if($_SESSION["user_id"]!='' || isset($_COOKIE["user_id"]))
		{
			$result.='<a href="'.$server_absolute_path_info.'users/'.$a["sid"].'/"';
			if($class!='') {
				$result.=' class="'.$class.'"';
			}
			$result.='>';
		}
	}
	if(strpos($a["hidesome"],'-0-')===false && decode($a["nick"])!='' && $a["nick"]!=' ')
	{
		$result.=decode($a["nick"]);
	}
	elseif(strpos($a["hidesome"],'-10-')===false && decode($a["fio"])!='' && $a["fio"]!=' ')
	{
		$result.=decode($a["fio"]);
	}
	elseif($sid)
	{
		$result.='ИНП '.$a["sid"];
	}
	else
	{
		$result.='скрыто';
	}
	if($link)
	{
		if($_SESSION["user_id"]!='' || isset($_COOKIE["user_id"]))
		{
			$result.='</a>';
		}
	}

	return($result);
}
#*************************************************************
function social($soc) {

	$soc=str_replace('http://','',$soc);
	$soc=str_replace('https://','',$soc);
	$soc=str_replace('www.','',$soc);
	$soc=str_replace('/posts','',$soc);
	$soc=str_replace('vkontakte.ru/','',$soc);
	$soc=str_replace('vk.com/','',$soc);
	$soc=str_replace('.livejournal.com','',$soc);
    $soc=str_replace('twitter.com/#!/','',$soc);
    $soc=str_replace('facebook.com','',$soc);
    $soc=str_replace('plus.google.com','',$soc);
    $soc=str_replace('fotki.yandex.ru/users/','',$soc);
	$soc=str_replace('/','',$soc);
	$soc=encode($soc);

	return $soc;
}
#*************************************************************
function social2($path,$type,$pic) {
	global
		$prefix,
		$server_absolute_path;

	if($type=='') {
		if(strpos($path,'vkontakte.ru')!==false) {
			$type="vkontakte";
		}
		if(strpos($path,'vk.com')!==false) {
			$type="vkontakte";
		}
		elseif(strpos($path,'allrpg.info')!==false) {
			$type="allrpg";
		}
		elseif(strpos($path,'fotki.yandex.ru')!==false) {
			$type="yandex";
		}
		elseif(strpos($path,'twitter.com')!==false) {
			$type="tweeter";
		}
		elseif(strpos($path,'livejournal.com')!==false) {
			$type="livejournal";
		}
		elseif(strpos($path,'facebook.com')!==false) {
			$type="facebook";
		}
		elseif(strpos($path,'plus.google.com')!==false) {
			$type="googleplus";
		}
	}

	$path=str_replace(array(
		'http://',
		'https://',
		'www.',
		'/posts',
		'vkontakte.ru/',
		'vk.com/',
		'.livejournal.com',
		'twitter.com/#!/',
		'facebook.com',
		'plus.google.com',
		'fotki.yandex.ru/users/',
		'/',
	),'',$path);

	if($path!='') {
		$rpath='';
		if($type=="vkontakte") {
			$rpath.='<a href="https://vk.com/'.$path.'" target="_blank">';
			if($pic) {
				$rpath.='<img src="'.$server_absolute_path.'images/networks/vkontakte.png"> '.$path;
			}
			else {
				$rpath.='https://vk.com/'.$path;
			}
			$rpath.='</a>';
		}
		elseif($type=="allrpg") {
            $result3=mysql_query("SELECT * FROM ".$prefix."users where sid=".$path);
			$c = mysql_fetch_array($result3);
			$rpath.=usname($c,true,true);
		}
		elseif($type=="tweeter") {
			$rpath.='<a href="http://www.twitter.com/#!/'.$path.'" target="_blank">';
			if($pic) {
				$rpath.='<img src="'.$server_absolute_path.'images/networks/twitter.png"> '.$path;
			}
			else {
				$rpath.='http://www.twitter.com/#!/'.$path;
			}
			$rpath.='</a>';
		}
		elseif($type=="livejournal") {
			$rpath.='<a href="http://'.$path.'.livejournal.com" target="_blank">';
			if($pic) {
				$rpath.='<img src="'.$server_absolute_path.'images/networks/livejournal.png"> '.$path;
			}
			else {
				$rpath.='http://'.$path.'.livejournal.com';
			}
			$rpath.='</a>';
		}
		elseif($type=="facebook") {
			$rpath.='<a href="http://www.facebook.com/'.$path.'" target="_blank">';
			if($pic) {
				$rpath.='<img src="'.$server_absolute_path.'images/networks/facebook.png"> '.$path;
			}
			else {
				$rpath.='http://www.facebook.com/'.$path;
			}
			$rpath.='</a>';
		}
		elseif($type=="googleplus") {
			$rpath.='<a href="https://plus.google.com/'.$path.'/posts" target="_blank">';
			if($pic) {
				$rpath.='<img src="'.$server_absolute_path.'images/networks/google.png"> '.$path;
			}
			else {
				$rpath.='https://plus.google.com/'.$path.'/posts';
			}
			$rpath.='</a>';
		}
		elseif($type=="yandex") {
			$rpath.='<a href="http://fotki.yandex.ru/users/'.$path.'/" target="_blank">';
			if($pic) {
				$rpath.='<img src="'.$server_absolute_path.'images/networks/yandex.png"> '.$path;
			}
			else {
				$rpath.='http://fotki.yandex.ru/users/'.$path.'/';
			}
			$rpath.='</a>';
		}
		else {
			$rpath.=$path;
		}
	}

	return $rpath;
}
function func($link,$image,$title,$js,$include) {
	$result='<div class="func">';
    if($include!='') {
    	$result.=$include;
    }
	$result.='<a ';
	if(!$js) {
		$result.='href="'.$link.'"';
	}
	else {
		$result.='style="cursor: pointer;"'.$link;
	}
	$result.='><img src="'.$image.'" title="'.$title.'"><br>'.$title.'</a></div>';
	return $result;
}

function pagecount($pageobject,$pagetotal,$grads,$moreparams='') {
	global
		$curdir,
		$sorting,
		$page,
		$kind,
		$server_absolute_path,
		$direct;

	if($grads=='') {
		$grads=50;
	}

	$result.='<center><table class="pagecount"><tr><td class="next">';
	if($page<($pagetotal/$grads)-1) {
		$result.='<a href="'.$curdir.$kind.'/';
		if($pageobject!='') {
			$result.=$pageobject.'/';
		}
		$result.='page='.($page+1).'&sorting='.$sorting.$moreparams.'">';
	}
	$result.='&#8592; Следующая';
	if($page<($pagetotal/$grads)-1) {
		$result.='</a>';
	}
	$result.='<br>';
	if($page!=ceil($pagetotal/$grads)-1 && $pagetotal>0) {
		$result.='<a href="'.$curdir.$kind.'/';
		if($pageobject!='') {
			$result.=$pageobject.'/';
		}
		$result.='page='.(ceil($pagetotal/$grads)-1).'&sorting='.$sorting.$moreparams.'" class="sm">';
	}
	else {
		$result.='<span class="sm">';
	}
	$result.='Последняя';
	if($page!=ceil($pagetotal/$grads)-1) {
		$result.='</a>';
	}
	else {
		$result.='</span>';
	}
	$result.='</td><td class="pagenums">';

	$totalobjects=$pagetotal;
	if($pagetotal==0) {
		$pagetotal=1;
	}
	$j=$page-5;
	if($j<1) {
		$j=1;
	}
	$d=ceil($pagetotal/$grads);
	if($d>$j+12) {
		$d=$j+12;
	}
	else {
		$j=$d-12;
	}
	if($j<1) {
		$j=1;
	}
	for($i=$d;$i>=$j;$i--)
	{
		if($i-1!=$page) {
			$result.='<a href="'.$curdir.$kind.'/';
			if($pageobject!='') {
				$result.=$pageobject.'/';
			}
			$result.='page='.($i-1).'&sorting='.$sorting.$moreparams.'">'.$i.'</a>';
		}
		else {
			$result.='<span class="selpage">'.$i.'</span>';
		}
	}
	$result.='<br>';
	$result.='<div class="pagegroup"><img src="'.$server_absolute_path.$direct.'/empty.gif" style="width: 100%;"></div>';

	$result.='<span class="sm">(на экране – до '.$grads.' позиций из '.$totalobjects.')</span></td>';
	$result.='<td class="previous">';
	if(ceil($totalobjects/$grads)-1>0 && $page>0) {
		$result.='<a href="'.$curdir.$kind.'/';
		if($pageobject!='') {
			$result.=$pageobject.'/';
		}
		$result.='page='.($page-1).'&sorting='.$sorting.$moreparams.'">';
	}
	$result.='Предыдущая &#8594;';
	if($page<($pagetotal/$grads)-1) {
		$result.='</a>';
	}
	$result.='<br>';
	if($page!=0 && ceil($pagetotal/$grads)-1>0) {
		$result.='<a href="'.$curdir.$kind.'/';
		if($pageobject!='') {
			$result.=$pageobject.'/';
		}
		$result.='sorting='.$sorting.$moreparams.'" class="sm">';
	}
	else {
		$result.='<span class="sm">';
	}
	$result.='Первая';
	if($page!=0 && ceil($pagetotal/$grads)-1>0) {
		$result.='</a>';
	}
	else {
		$result.='</span>';
	}
	$result.='</td></tr></table></center>';

	return $result;
}

function shownews($a,$showpath) {
	global
		$server_absolute_path,
		$prefix,
		$lead1,
		$lead2;

	if($a["site_id"]==0) {
		$a["site_id"]=1;
	}
	$result.='<div class="news_header">';
	if(strlen(strip_tags(decode($a["content"])))>255 || strip_tags(decode($a["main"])!='')) {
		$result.='<a href="'.$server_absolute_path.'news/'.$a["site_id"].'/'.$a["id"].'/">';
	}
	$result.=decode($a["name"]);
	if(strlen(strip_tags(decode($a["content"])))>255 || strip_tags(decode($a["main"])!='')) {
		$result.='</a>';
	}
	$result.='</div><span class="date" nowrap>'.date("d.m.Y", strtotime($a["date2"])).'</span>';
	if($showpath) {
		if($a["site_id"]==1) {
			$result.='<span class="news_site">/ allrpg.info /</span>';
		}
		else {
	       	$result2=mysql_query("SELECT * FROM ".$prefix."sites where id=".$a["site_id"]);
			$b = mysql_fetch_array($result2);
			if(decode($b["path"])!='') {
				$result.='<span class="news_site">/ <a href="'.$lead1.decode($b["path"]).$lead2.'">'.decode($b["title"]).'</a> /</span>';
			}
			elseif(decode($b["path2"])!='') {
   				$result.='<span class="news_site">/ ';
   				if(!eregi('http://',decode($b["path2"])) && !preg_match('#www\.#',decode($b["path2"]))) {
   					$result.='<a href="http://'.decode($b["path2"]).'">';
   				}
   				else {
   					$result.='<a href="'.decode($b["path2"]).'">';
   				}
   				$result.=decode($b["title"]).'</a> /</span>';
   			}
   			else {
   				$result.='<span class="news_site">/ '.decode($b["title"]).' /</span>';
   			}
		}
	}
	$result.='<br>
<span class="news_text">';
	if(strlen(strip_tags(decode($a["content"])))>255) {
		$result.=substr(strip_tags(decode($a["content"])),0,255).'&#8230;<br><a href="'.$server_absolute_path.'news/'.$a["site_id"].'/'.$a["id"].'/">[читать дальше]</a>';
	}
	else {
		if(strip_tags(decode($a["main"])!='')) {
			if(substr(strip_tags(decode($a["content"])),strlen(strip_tags(decode($a["content"])))-1,1)=='.') {
				$result.=substr(strip_tags(decode($a["content"])),0,strlen(strip_tags(decode($a["content"])))-1).'&#8230;<br><a href="'.$server_absolute_path.'news/'.$a["site_id"].'/'.$a["id"].'/">[читать дальше]</a>';
			}
			else {
				$result.=strip_tags(decode($a["content"])).'&#8230;<br><a href="'.$server_absolute_path.'news/'.$a["site_id"].'/'.$a["id"].'/">[читать дальше]</a>';
			}
		}
		else {
			$result.=strip_tags(decode($a["content"]));
		}
	}
	$result.='</span><br><br>';

	return $result;
}
function showshopnews($a,$showpath) {
	global
		$server_absolute_path_shop,
		$prefix;

	$result.='<span class="date" nowrap>'.date("d.m.Y", strtotime($a["date2"])).'</span><span class="news_header">';
	if(strlen(strip_tags(decode($a["content"])))>255) {
		$result.='<a href="'.$server_absolute_path_shop.$a["place_id"].'/'.$a["id"].'/news=show">';
	}
	$result.=decode($a["name"]);
	if(strlen(strip_tags(decode($a["content"])))>255) {
		$result.='</a>';
	}
	$result.='</span>';
	if($showpath) {
       	$result2=mysql_query("SELECT * FROM ".$prefix."shop_place where id=".$a["place_id"]);
		$b = mysql_fetch_array($result2);
		$result.='<span class="news_site">/ <a href="'.$server_absolute_path_shop.$a["place_id"].'/">'.decode($b["name"]).'</a> / <a href="'.$server_absolute_path_shop.$a["place_id"].'/news=show">все новости</a> /</span>';
	}
	$result.='<br>
<span class="news_text">';
	if(strlen(strip_tags(decode($a["content"])))>255) {
		$result.=substr(strip_tags(decode($a["content"])),0,255).'<a href="'.$server_absolute_path_shop.$a["place_id"].'/'.$a["id"].'/news=show"><b>&#8230;&#8594;</b></a>';
	}
	else {
		$result.=strip_tags(decode($a["content"]));
	}
	$result.='</span><br><br>';

	return $result;
}
function h1line($text,$href) {
	global
		$curdir,
		$kind;

	if(!$href) {
		$href=$curdir.$kind.'/';
	}
	//$content.='<h1>';
	if($href!='') {
		$content.='<a href="'.$href.'">';
	}
	$content.=$text;
	if($href!='') {
		$content.='</a>';
	}
	//$content.='</h1>';

	return $content;
}

#*************************************************************
function virtual_structure($query, $virtualfield, $prefix){

	if($virtualfield!='') {
		$fields[]=Array(
			'name'	=>	$virtualfield,
			'type'	=>	"hidden",
			'read'	=>	10,
			'write'	=>	10,
			'mustbe'	=>	false,
		);
	}

	$result=mysql_query($query);
	while($a=mysql_fetch_array($result)) {
		unset($values);

		$mustbe=$a[$prefix."mustbe"]==1?true:false;

		if($a[$prefix."rights"]==1) {
			$read=100;
			$write=100;
		}
		elseif($a[$prefix."rights"]==2) {
			$read=10;
			$write=100;
		}
		elseif($a[$prefix."rights"]==3) {
			$read=10;
			$write=10;
		}
		elseif($a[$prefix."rights"]==4) {
			$read=1;
			$write=10;
		}

		$width=$a[$prefix."width"];
		$height=$a[$prefix."height"];
		if($width==0) {
			$width='';
		}
		if($height==0) {
			$height='';
		}

		if($a[$prefix."values"]!='') {
			$css=decode($a[$prefix."values"]);
			preg_match_all('#\[(\d+)\]\[([^\]]+)\]#',$css,$matches);
			foreach($matches[1] as $key=>$value) {
				$values[] = Array($value,$matches[2][$key]);
			}
		}

		$fields[]=Array(
			'name'	=>	"virtual".$a["id"],
			'sname'	=>	$a[$prefix."name"],
			'type'	=>	$a[$prefix."type"],
			'default'	=>	decode($a[$prefix."default"]),
			'help'	=>	decode($a[$prefix."help"]),
			'values'	=>	$values,
			'read'	=>	$read,
			'write'	=>	$write,
			'mustbe'	=>	$mustbe,
			'width'	=>	$width,
			'height'	=>	$height,
			'virtual'	=>	true,
		);
	}

	return $fields;
}
function monthname($num) {
	if($num==1) {
		$monthname='января';
	}
	elseif($num==2) {
		$monthname='февраля';
	}
	elseif($num==3) {
		$monthname='марта';
	}
	elseif($num==4) {
		$monthname='апреля';
	}
	elseif($num==5) {
		$monthname='мая';
	}
	elseif($num==6) {
		$monthname='июня';
	}
	elseif($num==7) {
		$monthname='июля';
	}
	elseif($num==8) {
		$monthname='августа';
	}
	elseif($num==9) {
		$monthname='сентября';
	}
	elseif($num==10) {
		$monthname='октября';
	}
	elseif($num==11) {
		$monthname='ноября';
	}
	elseif($num==12) {
		$monthname='декабря';
	}
	return $monthname;
}
#*************************************************************
function datesfmake($datestart,$datefinish,$centered=false)
{
	$a["datestart"]=$datestart;
	$a["datefinish"]=$datefinish;
	if(date("Y",strtotime($a["datestart"]))!=date("Y",strtotime($a["datefinish"]))) {
		$hz=round(date("d",strtotime($a["datestart"]))).'&nbsp;'.monthname(date("m",strtotime($a["datestart"]))).' '.date("Y",strtotime($a["datestart"])).' – '.round(date("d",strtotime($a["datefinish"]))).'&nbsp;'.monthname(date("m",strtotime($a["datefinish"]))).' '.date("Y",strtotime($a["datefinish"]));
	}
	elseif(date("m",strtotime($a["datestart"]))!=date("m",strtotime($a["datefinish"]))) {
		$hz=round(date("d",strtotime($a["datestart"]))).'&nbsp;'.monthname(date("m",strtotime($a["datestart"]))).' – '.round(date("d",strtotime($a["datefinish"]))).'&nbsp;'.monthname(date("m",strtotime($a["datefinish"]))).' '.date("Y",strtotime($a["datefinish"]));
	}
	elseif($datestart==$datefinish) {
		$hz=round(date("d",strtotime($a["datestart"]))).'&nbsp;'.monthname(date("m",strtotime($a["datestart"]))).' '.date("Y",strtotime($a["datestart"]));
	}
	else {
		$hz=round(date("d",strtotime($a["datestart"]))).'-'.round(date("d",strtotime($a["datefinish"]))).'&nbsp;'.monthname(date("m",strtotime($a["datefinish"]))).' '.date("Y",strtotime($a["datefinish"]));
	}

	if($datestart==$datefinish && $centered) {
		$hz='<center>'.$hz.'</center>';
	}
	return $hz;
}
#*************************************************************
function checkRoleFieldVisibility() {
	return true;
}
?>