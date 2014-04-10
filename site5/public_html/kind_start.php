<?php
if(encode_to_cp1251($_GET["viewtype"])==2) {
	unset($_SESSION["calendarstyle"]);
	if($_SESSION["user_id"]!='') {
		mysql_query("UPDATE ".$prefix."users set calendarstyle='0' where id=".$_SESSION["user_id"]);
	}
}
elseif(encode_to_cp1251($_GET["viewtype"])==1) {
	$_SESSION["calendarstyle"]=true;
	if($_SESSION["user_id"]!='') {
		mysql_query("UPDATE ".$prefix."users set calendarstyle='1' where id=".$_SESSION["user_id"]);
	}
}

if(date("m")>=10) {
	if($filter6=='01.01.'.date("Y") && $filter7=='31.12.'.date("Y") && encode($_REQUEST["filter6"])=='' && encode($_REQUEST["filter7"])=='' && encode($_REQUEST["wholeyear"])=='') {
		$filter6='01.01.'.(date("Y")+1);
		$filter7='31.12.'.(date("Y")+1);
		$_REQUEST["wholeyear"]=(date("Y")+1);
		$october=true;
	}
}
$getfromday=date("d",strtotime($filter6));
$getfrommonth=date("m",strtotime($filter6));
$getfromyear=date("Y",strtotime($filter6));
$gettoday=date("d",strtotime($filter7));
$gettomonth=date("m",strtotime($filter7));
$gettoyear=date("Y",strtotime($filter7));

if(encode($_REQUEST["wholeyear"])!='') {
	$filter6='01.01.'.encode($_REQUEST["wholeyear"]);
	$filter7='31.12.'.encode($_REQUEST["wholeyear"]);
	$gettoyear=encode($_REQUEST["wholeyear"]);
	$getfromyear=encode($_REQUEST["wholeyear"]);
	$getfrommonth=1;
	$gettomonth=12;
	$getfromday=1;
	$gettoday=31;
}

$months=Array(
	'1'=>Array(1,'января',31,'Январь'),
	'2'=>Array(2,'февраля',28,'Февраль'),
	'3'=>Array(3,'марта',31,'Март'),
	'4'=>Array(4,'апреля',30,'Апрель'),
	'5'=>Array(5,'мая',31,'Май'),
	'6'=>Array(6,'июня',30,'Июнь'),
	'7'=>Array(7,'июля',31,'Июль'),
	'8'=>Array(8,'августа',31,'Август'),
	'9'=>Array(9,'сентября',30,'Сентябрь'),
	'10'=>Array(10,'октября',31,'Октябрь'),
	'11'=>Array(11,'ноября',30,'Ноябрь'),
	'12'=>Array(12,'декабря',31,'Декабрь')
);
if($gettoyear%4==0)
{
	$months['2'][2]=29;
}
$lastday=$months[settype($gettomonth,"string")][2];
$totalmonths=($gettoyear-$getfromyear)*12+($gettomonth-$getfrommonth+1);

if(substr($filter6,0,5)=='01.01' && substr($filter7,0,5)=='31.12' && substr($filter6,6,strlen($filter6))==substr($filter7,6,strlen($filter7))) {
	$_REQUEST["wholeyear"]=substr($filter6,6,strlen($filter6));
}

$pagetitle='Календарь';
if(encode($_REQUEST["wholeyear"])!='') {
	$pagetitle.=' '.encode($_REQUEST["wholeyear"]);
}
$pagetitle=h1line($pagetitle);

if($_SESSION["calendarstyle"]) {
	$calendarstylelink='viewtype=2';
	$calendarstylephrase='таблицы';
}
else {
	$calendarstylelink='viewtype=1';
	$calendarstylephrase='календаря';
}
if(encode_to_cp1251($_GET["wholeyear"])!='') {
	$calendarstylelink.='&wholeyear='.encode_to_cp1251($_GET["wholeyear"]);
}
else {
	$calendarstylelink.='&filter6='.$filter6.'&filter7='.$filter7;
}                                                                                                                     $additional_commands.='<a href="'.$server_absolute_path_calendar.$calendarstylelink.'">в виде '.$calendarstylephrase.'</a>';

if(((is_array($filter2) && count($filter2)>0) || $filter2!='') || ((is_array($filter3) && count($filter3)>0) || $filter3!='') || ((is_array($filter4) && count($filter4)>0) || $filter4!='') || ((is_array($filter5) && count($filter5)>0) || $filter5!='') || ((is_array($filter8) && count($filter8)>0) || $filter8!='') || ((($filter6!='01.01.'.date("Y") || $filter7!='31.12.'.date("Y")) && !$october) || (($filter6!='01.01.'.(date("Y")+1) || $filter7!='31.12.'.(date("Y")+1)) && $october))) {
	$filters=true;
	if($action=="dynamicindex" && $dynrequest==1) {
		dynamic_err(array(),'submit');
	}
}
elseif($action=="dynamicindex" && $dynrequest==1) {
	dynamic_err_one('error','Фильтры не определены!');
}

$selecter2=createElem(Array(
	'name'	=>	"filter2",
	'sname'	=>	"Жанр",
	'type'	=>	"multiselect",
	'values'	=>	make5field($prefix."gametypes where tipe=1 order by name","id","name"),
	'default'	=>	$filter2,
	'read'	=>	10,
	'write'	=>	10,
	)
);
if(encode($_POST["filter2"])!='') {
	$selecter2->setVal('',$_POST);
}
else {
	$selecter2->setVal('',$_GET);
}
$selecter3=createElem(Array(
	'name'	=>	"filter3",
	'sname'	=>	"Тип",
	'type'	=>	"multiselect",
	'values'	=>	make5field($prefix."gametypes where tipe=2 order by name","id","name"),
	'default'	=>	$filter3,
	'read'	=>	10,
	'write'	=>	10,
	)
);
if(encode($_POST["filter3"])!='') {
	$selecter3->setVal('',$_POST);
}
else {
	$selecter3->setVal('',$_GET);
}
$selecter4=createElem(Array(
	'name'	=>	"filter5",
	'sname'	=>	"Мир",
	'type'	=>	"multiselect",
	'values'	=>	make5field($prefix."gameworlds order by name","id","name"),
	'default'	=>	$filter5,
	'read'	=>	10,
	'write'	=>	10,
	)
);
if(encode($_POST["filter5"])!='') {
	$selecter4->setVal('',$_POST);
}
else {
	$selecter4->setVal('',$_GET);
}
$selecter5=createElem(Array(
	'name'	=>	"filter4",
	'sname'	=>	"Дополнительно",
	'type'	=>	"multiselect",
	'values'	=>	make5field($prefix."gametypes where tipe=3 order by name","id","name"),
	'default'	=>	$filter4,
	'read'	=>	10,
	'write'	=>	10,
	)
);
if(encode($_POST["filter4"])!='') {
	$selecter5->setVal('',$_POST);
}
else {
	$selecter5->setVal('',$_GET);
}
$selecter6=createElem(Array(
	'name'	=>	"filter6",
	'sname'	=>	"С",
	'type'	=>	"calendar",
	'default'	=>	$filter6,
	'read'	=>	10,
	'write'	=>	10,
	)
);
$selecter7=createElem(Array(
	'name'	=>	"filter7",
	'sname'	=>	"По",
	'type'	=>	"calendar",
	'default'	=>	$filter7,
	'read'	=>	10,
	'write'	=>	10,
	)
);
$selecter8=createElem(Array(
	'name'	=>	"filter8",
	'sname'	=>	"Регион",
	'type'	=>	"multiselect",
	'values'	=>	make5field($prefix."geography where id in (SELECT distinct region from ".$prefix."allgames) order by name","id","name"),
	'default'	=>	$filter8,
	'cols'	=>	2,
	'read'	=>	10,
	'write'	=>	10,
	)
);
if(encode($_POST["filter8"])!='') {
	$selecter8->setVal('',$_POST);
}
else {
	$selecter8->setVal('',$_GET);
}

$content2.='<div class="indexer">
<div id="filters_events" style="'.($filters?'':'display: none;').'">
<form action="'.$curdir.$kind.'/" method="post" enctype="multipart/form-data">
<input type="hidden" name="action" value="dynamicindex">
<table class="menutable searchtable">
<tr>
<td><b>С</b>:<br>'.$selecter6->draw(2,'write').'</td>
<td><b>По</b>:<br>'.$selecter7->draw(2,'write').'</td>
<td colspan=2 width="64%"><b>Регион</b>:<br>'.$selecter8->draw(2,'write').'</td>
</tr>
<tr>
<td width="25%">
<b>Жанр</b>:<br>'.$selecter2->draw(2,"write").'
</td>
<td width="25%">
<b>Тип</b>:<br>'.$selecter3->draw(2,"write").'
</td>
<td width="25%">
<b>Мир</b>:<br>'.$selecter4->draw(2,"write").'
</td>
<td width="25%">
<b>Дополнительно</b>:<br>'.$selecter5->draw(2,"write").'
</td>
</tr>
</table>

<table class="controls"><tr><td><button class="nonimportant" onClick="document.location=\''.$curdir.$kind.'/\'">очистить фильтр</button></td><td><div class="filters_'.($filters?'on':'off').'">'.($filters?'Внимание! Используются фильтры.':'Фильтр на год.').'</div></td><td><button class="main">отфильтровать</button></td></tr></table></form><br></div></div>';

	if($filter2!='' || $filter3!='' || $filter4!='' || $filter5!='' || $filter6!='' || $filter7!='' || $filter8!='') {
		$more=true;

		if($filter2!='') {
			if($more) {
				$query.=' AND ';
			}
			$filter2decode=$selecter2->getVal();
			$filter2decode=substr($filter2decode,1,strlen($filter2decode)-2);
			$filter2decode2=explode("-", $filter2decode);
			$query.='(';
			for($i=0;$i<count($filter2decode2);$i++) {
				$query.="gametype LIKE '%-".$filter2decode2[$i]."-%' OR ";
			}
			$query=substr($query,0,strlen($query)-4);
			$query.=')';
			$more=true;
		}

		if($filter3!='') {
			if($more) {
				$query.=' AND ';
			}
			$filter3decode=$selecter3->getVal();
			$filter3decode=substr($filter3decode,1,strlen($filter3decode)-2);
			$filter3decode2=explode("-", $filter3decode);
			$query.='(';
			for($i=0;$i<count($filter3decode2);$i++) {
				$query.="gametype2 LIKE '%-".$filter3decode2[$i]."-%' OR ";
			}
			$query=substr($query,0,strlen($query)-4);
			$query.=')';
			$more=true;
		}

		if($filter4!='') {
			if($more) {
				$query.=' AND ';
			}
			$filter4decode=$selecter5->getVal();
			$filter4decode=substr($filter4decode,1,strlen($filter4decode)-2);
			$filter4decode2=explode("-", $filter4decode);
			$query.='(';
			for($i=0;$i<count($filter4decode2);$i++) {
				$query.="gametype4 LIKE '%-".$filter4decode2[$i]."-%' OR ";
			}
			$query=substr($query,0,strlen($query)-4);
			$query.=')';
			$more=true;
		}

		if($filter5!='') {
			if($more) {
				$query.=' AND ';
			}
			$filter5decode=$selecter4->getVal();
			$filter5decode=substr($filter5decode,1,strlen($filter5decode)-2);
			$filter5decode2=explode("-", $filter5decode);
			$query.='(';
			for($i=0;$i<count($filter5decode2);$i++) {
				$query.="gametype3=".$filter5decode2[$i]." OR ";
			}
			$query=substr($query,0,strlen($query)-4);
			$query.=')';
			$more=true;
		}

		if($filter8!=0) {
			if($more) {
				$query.=' AND ';
			}
			$filter8decode=$selecter8->getVal();
			$filter8decode=substr($filter8decode,1,strlen($filter8decode));
			$filter8decode=str_replace('-',', ',$filter8decode);
			$filter8decode=substr($filter8decode,0,strlen($filter8decode)-2);
			$query.="region IN (".$filter8decode.")";
			$more=true;
		}

		$querytosend=$query;

		if($filter6!='' || $filter7!='') {
			if($more) {
				$query.=' AND ';
			}

			$query.="((datestart <= '".date("Y-m-d",strtotime($filter6))."' AND datefinish >= '".date("Y-m-d",strtotime($filter6))."') OR (datestart <= '".date("Y-m-d",strtotime($filter7))."' AND datefinish >= '".date("Y-m-d",strtotime($filter7))."') OR (datestart > '".date("Y-m-d",strtotime($filter6))."' AND datefinish < '".date("Y-m-d",strtotime($filter7))."'))";
			$more=true;
		}

		if($more) {
			$query.=' ';
		}
	}
	$content2.='
<center>
<div class="cb_editor">

<h3 id="showfilters_events" '.($filters?'style="display: none;" ':'').'class="ctrlink2"><a onClick="$(\'#filters_events\').toggle(); $(\'#hidefilters_events\').toggle(); $(\'#showfilters_events\').toggle();">показать фильтры</a></h3>
<h3 id="hidefilters_events" '.($filters?'':'style="display: none;" ').'class="ctrlink2"><a onClick="$(\'#filters_events\').toggle(); $(\'#showfilters_events\').toggle(); $(\'#hidefilters_events\').toggle();">скрыть фильтры</a></h3>

<div class="clear"></div><hr>

<table class="menutable">
<tr class="menu">
<td style="width: 35%">
';
	if($sorting==0) {
		$sorting=3;
	}

	if($_SESSION["calendarstyle"]) {
		$content2.='Название/сайт/полигон';
	}
	elseif($sorting==1) {
		$content2.='<a href="'.$curdir.$kind.'/filter2='.$selecter2->getVal().'&filter3='.$selecter3->getVal().'&filter4='.$selecter5->getVal().'&filter5='.$selecter4->getVal().'&filter6='.$filter6.'&filter7='.$filter7.'&filter8='.$selecter8->getVal().'&sorting=2" title="[сортировать : название/сайт/полигон : по убыванию]" class="arrow_up">Название/сайт/полигон</a>';
	}
	elseif($sorting==2) {
		$content2.='<a href="'.$curdir.$kind.'/filter2='.$selecter2->getVal().'&filter3='.$selecter3->getVal().'&filter4='.$selecter5->getVal().'&filter5='.$selecter4->getVal().'&filter6='.$filter6.'&filter7='.$filter7.'&filter8='.$selecter8->getVal().'&sorting=1" title="[сортировать : название/сайт/полигон : по возрастанию]" class="arrow_down">Название/сайт/полигон</a>';
	}
	else {
		$content2.='<a href="'.$curdir.$kind.'/filter2='.$selecter2->getVal().'&filter3='.$selecter3->getVal().'&filter4='.$selecter5->getVal().'&filter5='.$selecter4->getVal().'&filter6='.$filter6.'&filter7='.$filter7.'&filter8='.$selecter8->getVal().'&sorting=2" title="[сортировать : название/сайт/полигон : по убыванию]">Название/сайт/полигон</a>';
	}
	$content2.='
</td>
<td style="width: 17%">
';
	if($_SESSION["calendarstyle"]) {
		$content2.='Даты';
	}
	elseif($sorting==3) {
		$content2.='<a href="'.$curdir.$kind.'/filter2='.$selecter2->getVal().'&filter3='.$selecter3->getVal().'&filter4='.$selecter5->getVal().'&filter5='.$selecter4->getVal().'&filter6='.$filter6.'&filter7='.$filter7.'&filter8='.$selecter8->getVal().'&sorting=4" title="[сортировать : даты : по убыванию]" class="arrow_up">Даты</a>';
	}
	elseif($sorting==4) {
		$content2.='<a href="'.$curdir.$kind.'/filter2='.$selecter2->getVal().'&filter3='.$selecter3->getVal().'&filter4='.$selecter5->getVal().'&filter5='.$selecter4->getVal().'&filter6='.$filter6.'&filter7='.$filter7.'&filter8='.$selecter8->getVal().'&sorting=3" title="[сортировать : даты : по возрастанию]" class="arrow_down">Даты</a>';
	}
	else {
		$content2.='<a href="'.$curdir.$kind.'/filter2='.$selecter2->getVal().'&filter3='.$selecter3->getVal().'&filter4='.$selecter5->getVal().'&filter5='.$selecter4->getVal().'&filter6='.$filter6.'&filter7='.$filter7.'&filter8='.$selecter8->getVal().'&sorting=4" title="[сортировать : даты : по убыванию]">Даты</a>';
	}
	$content2.='
</td>
<td style="width: 30%;">
Мастерская группа
</td>
<td style="width: 8%;">
Игроки
</td>
<td style="width: 10%;">
Прочее
</td>
</tr>';

if($sorting==1)
{
	$order='name ASC';
}
elseif($sorting==2)
{
	$order='name DESC';
}
elseif($sorting==3)
{
	$order='datestart ASC';
}
elseif($sorting==4)
{
	$order='datestart DESC';
}

//$bazecount=$_SESSION["bazecount"];
//if($bazecount=='') {
	$bazecount=5000;
//}
$start=$page*$bazecount;
$query2="SELECT COUNT(id) FROM ".$prefix."allgames WHERE parent=0".$query;
$query="SELECT * FROM ".$prefix."allgames where parent=0 ".$query."order by ".$order." limit ".$start.", ".$bazecount;
$stringnum=1;

unset($allgames);
$result=mysql_query($query);
while($a=mysql_fetch_array($result)) {
	$allgames[]=$a;
}

if($sorting==1 || $sorting==2) {
	foreach ($allgames as $key => $row) {
		$eventname[$key]  = $row['name'];
	}
	if($sorting==1) {
		array_multisort($eventname, SORT_ASC, $allgames);
	}
	elseif($sorting==2) {
		array_multisort($eventname, SORT_DESC, $allgames);
	}
}
else {
	foreach ($allgames as $key => $row) {
		$datestart[$key]  = $row['datestart'];
	}
	if($sorting==3) {
		array_multisort($datestart, SORT_ASC, $allgames);
	}
	else {
		array_multisort($datestart, SORT_DESC, $allgames);
	}
}

if($_SESSION["calendarstyle"]) {
	$ag_dates=array();
	$ag_dates_mark=array();
}
for($i=0;$i<count($allgames);$i++) {
	$a=$allgames[$i];
	$mark='';

	if(strtotime($a["datestart"])>strtotime($a["datefinish"])) {
		$a["datefinish"]=$a["datestart"];
	}

	$content2.='<tr class="';
	if($a["area"]!='') {
		$result3=mysql_query("SELECT * FROM ".$prefix."played where game=".$a["id"]." and user_id=".$_SESSION["user_id"]);
		$c = mysql_fetch_array($result3);
		if($c["id"]!='') {
			if($c["specializ2"]!='' && $c["specializ2"]!='-') {
				$content2.=' master';
				$mark='master';
			}
			elseif($c["specializ3"]!='' && $c["specializ3"]!='-') {
				$content2.=' poligon';
				$mark='poligon';
			}
			else {
				$content2.=' play';
				$mark='play';
			}
		}
	}
	else {
		$content2.=' event';
	}
	if($_SESSION["calendarstyle"]) {
		$content2.=' hidden';
	}
	$content2.='"';
	if(date("m",strtotime($a["datestart"]))<date("m",strtotime($allgames[$i+1]["datestart"])) && ($sorting==3 || $sorting==4) && !$_SESSION["calendarstyle"]) {
		$content2.=' style="border-bottom: 0.2em rgb(0,0,160) solid;"';
	}
	elseif($_SESSION["calendarstyle"]) {
		$content2.=' dates="';
		$tm=strtotime($a["datestart"]);
		while($tm<=strtotime($a["datefinish"])) {
			$content2.=$tm.' ';
			$tm+=24*3600;
		}
		$content2=substr($content2,0,strlen($content2)-1).'"';
	}
	$content2.='>
<td>';
	$content2.='<b><a href="'.$server_absolute_path_info.'events/'.$a["id"].'/">'.decode($a["name"]).'</a></b><br>';
	if($a["site"]!='') {
		$content2.='<a href="'.$a["site"].'" target="_blank">'.substr($a["site"],0,40);
		if(strlen($a["site"])>40) {
			$content2.='...';
		}
		$content2.='</a><br>';
	}
	if($a["area"]!='') {
		$result2=mysql_query("SELECT * FROM ".$prefix."areas where id=".$a["area"]);
		$b = mysql_fetch_array($result2);
		$content2.='<a href="'.$server_absolute_path_info.'areas/'.$b["id"].'/">'.decode($b["name"]).'</a>';
	}
	$content2.='
</td>
';
	if($_SESSION["calendarstyle"]) {
		$tm=strtotime($a["datestart"]);
		while($tm<=strtotime($a["datefinish"])) {
			$ag_dates[$tm]++;
			if($mark=='master' || ($mark=='poligon' && $ag_dates_mark[$tm]!='master') || ($mark=='play' && $ag_dates_mark[$tm]!='master' && $ag_dates_mark[$tm]!='poligon')) {
				$ag_dates_mark[$tm]=$mark;
			}
			$tm+=24*3600;
		}
	}
	$content2.='<td>
'.datesfmake($a["datestart"],$a["datefinish"]).'
</td>
<td>';
	if($a["area"]!='') {
		$hisgroups=explode(',',$a["mg"]);
		for($j=0;$j<count($hisgroups);$j++)
		{
			if(substr($hisgroups[$j],0,1)==' ')
			{
				$hisgroups[$j]=substr($hisgroups[$j],1,strlen($hisgroups[$j]));
			}
			if($j<count($hisgroups)-1)
			{
				$content2.='<a href="'.$server_absolute_path_info.'mg/'.str_replace('&','-and-',$hisgroups[$j]).'/">'.$hisgroups[$j].'</a>, ';
			}
			else
			{
				$content2.='<a href="'.$server_absolute_path_info.'mg/'.str_replace('&','-and-',$hisgroups[$j]).'/">'.$hisgroups[$j].'</a>';
			}
		}
	}
	else {
		$content2.='&nbsp;';
	}
	$content2.='
</td>
<td>
'.$a["playernum"].'
</td>
<td>';
	if($a["area"]!='') {
		$result4=mysql_query("SELECT COUNT(id) FROM ".$prefix."comments where game=".$a["id"]." and active='1'");
		$d = mysql_fetch_array($result4);
		$rating=0;
		$result3=mysql_query("SELECT * FROM ".$prefix."comments where game=".$a["id"]);
		while($c = mysql_fetch_array($result3))
		{
			$rating+=$c["rating"];
		}
		if($rating>0)
		{
			$rating='+'.$rating;
		}
		$content2.='Рейтинг: '.$rating.'<br><nobr><a href="'.$server_absolute_path_info.'comments/'.$a["id"].'/filter=event">Отзывы ('.$d[0].')</a></nobr>';
		if($a["datestart"]<=date("Y-m-d"))
		{
			$result4=mysql_query("SELECT COUNT(id) FROM ".$prefix."reports where game=".$a["id"]);
			$d = mysql_fetch_array($result4);
			$content2.='<br><nobr><a href="'.$server_absolute_path_info.'reports/action=dynamicindex&search_game['.$id.']=on">Отчеты ('.$d[0].')</a></nobr>';
		}
		$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."allgames_gallery where game_id=".$a["id"]);
		$b = mysql_fetch_array($result2);
		if($b[0]>0) {
			$content2.='<br><nobr><a href="'.$server_absolute_path_info.'events/'.$a["id"].'/">Галереи ('.$b[0].')</a></nobr>';
		}
	}
	else {
		$content2.='<br><br><br>';
	}
	$content2.='</td>
</tr>
';
	$stringnum++;
}

	if($_SESSION["calendarstyle"]) {
		$content2.='<tr><td colspan=5>';

		$startMonth=date("m",strtotime($filter6))+0;
		$startYear=date("Y",strtotime($filter6))+0;
		$finishMonth=date("m",strtotime($filter7))+0;
		$finishYear=date("Y",strtotime($filter7))+0;

		$ag=0;
		$z=1;
		while($startYear<=$finishYear) {
			while(($startMonth<=$finishMonth && $startYear==$finishYear) || ($startMonth<=12 && $startYear<$finishYear)) {
				$content2.='<div class="calendar_table"><table>
<tr>
<th colspan=7>
'.$months[$startMonth][3].'
</th>
</tr>
<tr>
<th>пн</th><th>вт</th><th>ср</th><th>чт</th><th>пт</th><th>сб</th><th>вс</th>
</tr>';
				$daysInMonth=$months[$startMonth][2];
				if($startYear%4==0 && $startMonth==2) {
					$daysInMonth=29;
				}
				elseif($startMonth==2) {
					$daysInMonth=28;
				}
				$j=1;
				$firstDayOfMonth=date('N',strtotime($startYear.'-'.$startMonth.'-01'));
				$lastDayOfMonth=date('N',strtotime($startYear.'-'.$startMonth.'-'.$daysInMonth));
				if($firstDayOfMonth>1) {
					$content2.='<tr>';
					for($i=1;$i<$firstDayOfMonth;$i++) {
						$content2.='<td></td>';
						$j++;
					}
				}
				for($i=1;$i<=$daysInMonth;$i++) {
					if($j==1) {
						$content2.='<tr>';
					}

					$content2.='<td';
					if(isset($ag_dates_mark[strtotime($startYear.'-'.$startMonth.'-'.$i)])) {
						$content2.=' class="'.$ag_dates_mark[strtotime($startYear.'-'.$startMonth.'-'.$i)].'"';
					}
					if($ag_dates[strtotime($startYear.'-'.$startMonth.'-'.$i)]>0) {
						$content2.=' rel-date="'.strtotime($startYear.'-'.$startMonth.'-'.$i).'">'.$i.'<sup>'.$ag_dates[strtotime($startYear.'-'.$startMonth.'-'.$i)].'</sup>';
					}
					else {
						$content2.='>'.$i;
					}
					$content2.='</td>';

					$j++;
					if($j==8) {
						$j=1;
						$content2.='</tr>';
					}
				}
				if($lastDayOfMonth<7) {
					for($i=$j;$i<=$lastDayOfMonth;$i++) {
						$content2.='<td></td>';
					}
					$content2.='</tr>';
				}
				$content2.='
</table>
</div>';
				$startMonth++;
				$z++;
				if($z==4) {
					$content2.='<div class="clear"></div><br>';
					$z=1;
				}
			}
			$content2.='<div class="clear"></div>';
			$startMonth=1;
			$startYear++;
		}

		$content2.='</td></tr>';
	}

	$content2.='</table></div>';

	$content2.='<br><div class="cb_editor" style="text-align: center; border-top: 0.2em rgb(0,0,160) solid;">
<br><span style="background-color: #990000; color: white;"><b>Таким фоном</b></span> отмечены события, в которых вы <u>играете</u>.<br>
<span style="background-color: #0000CC; color: white;"><b>Таким фоном</b></span> отмечены события, которые вы <u>мастерите</u>.<br>
<span style="background-color: #009900; color: white;"><b>Таким фоном</b></span> отмечены события, в которых вы являетесь <u>полигонщиком</u>.</div>';
?>