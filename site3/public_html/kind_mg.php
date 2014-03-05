<?php
if($object!='' && $object!='mg') {
	$filter2=$object;
}

$filter2=$filter2;
//$filter2=iconv('utf-8', 'windows-1251', $filter2);
$filter2=str_replace('-and-','&',$filter2);


if($filter2!='') {
	$query="(ingroup='".$filter2."' OR ingroup LIKE '%".$filter2."%' OR ingroup LIKE '%".$filter2.",%' OR ingroup LIKE '%, ".$filter2."%' OR ingroup LIKE '%,".$filter2."%')";
	$query2="mg='".$filter2."' OR mg LIKE '%".$filter2."%' OR mg LIKE '%".$filter2.",%' OR mg LIKE '%, ".$filter2."%' OR mg LIKE '%,".$filter2."%'";
}
else {
	$query="ingroup!=''";
	$query2="mg!=''";
}

$filter2=str_ireplace('&quot;','',$filter2);
$filter2=str_ireplace('МГ ','',$filter2);
$filter2=str_ireplace('МО ','',$filter2);
$filter2=str_ireplace('ТГ ','',$filter2);
$filter2=str_ireplace('ТО ','',$filter2);
$filter2=str_ireplace('ТК ','',$filter2);
$filter2=str_ireplace('ТМ ','',$filter2);

$result=mysql_query("SELECT * from ".$prefix."users where ".$query." and ingroup!='нет' and ingroup!='не состою' and ingroup!='-' order by ingroup, fio");
while($a=mysql_fetch_array($result))
{
	$a["ingroup"]=str_ireplace('&quot;','',$a["ingroup"]);
	$a["ingroup"]=str_ireplace('МГ ','',$a["ingroup"]);
	$a["ingroup"]=str_ireplace('МО ','',$a["ingroup"]);
	$a["ingroup"]=str_ireplace('ТГ ','',$a["ingroup"]);
	$a["ingroup"]=str_ireplace('ТО ','',$a["ingroup"]);
	$a["ingroup"]=str_ireplace('ТК ','',$a["ingroup"]);
	$a["ingroup"]=str_ireplace('ТМ ','',$a["ingroup"]);
	$hisgroups=explode(',',$a["ingroup"]);
	for($j=0;$j<count($hisgroups);$j++)
	{
		if(substr($hisgroups[$j],0,1)==' ')
		{
			$hisgroups[$j]=substr($hisgroups[$j],1,strlen($hisgroups[$j]));
		}
		if($filter2=='' || strtoupper($filter2)==strtoupper($hisgroups[$j]))
		{
			$allgroup[]=Array("ingroup"=>$hisgroups[$j],"hidesome"=>$a["hidesome"],"nick"=>$a["nick"],"sid"=>$a["sid"],"fio"=>$a["fio"],"id"=>$a["id"]);
		}
	}
}
$result=mysql_query("SELECT * from ".$prefix."allgames where ".$query2." order by mg");
while($a=mysql_fetch_array($result))
{
	$a["mg"]=str_ireplace('&quot;','',$a["mg"]);
	$a["mg"]=str_ireplace('МГ ','',$a["mg"]);
	$a["mg"]=str_ireplace('МО ','',$a["mg"]);
	$a["mg"]=str_ireplace('ТГ ','',$a["mg"]);
	$a["mg"]=str_ireplace('ТО ','',$a["mg"]);
	$a["mg"]=str_ireplace('ТК ','',$a["mg"]);
	$a["mg"]=str_ireplace('ТМ ','',$a["mg"]);
	$hisgroups=explode(',',$a["mg"]);
	for($j=0;$j<count($hisgroups);$j++)
	{
		if(substr($hisgroups[$j],0,1)==' ')
		{
			$hisgroups[$j]=substr($hisgroups[$j],1,strlen($hisgroups[$j]));
		}
		if($filter2=='' || strtoupper($filter2)==strtoupper($hisgroups[$j]))
		{
			$allgroup[]=Array("ingroup"=>$hisgroups[$j]);
		}
	}
}
foreach ($allgroup as $key => $row)
{
	$ingroup[$key]  = $row['ingroup'];
}
array_multisort($ingroup, SORT_ASC, $allgroup);

$prev=$allgroup[0]["ingroup"];
$people='';
$r=0;
for($j=0;$j<count($allgroup);$j++)
{
	$a=$allgroup[$j];
	if(isset($a["ingroup"]) && $a["ingroup"]!='')
	{
		if(strtoupper($a["ingroup"])!=strtoupper($prev))
		{
			$games='';
			$result2=mysql_query("SELECT * from ".$prefix."allgames where mg='".$prev."' OR mg LIKE '%".$prev.",%' OR mg LIKE '%, ".$prev."%' OR mg LIKE '%,".$prev."%' order by name asc");
			while($b=mysql_fetch_array($result2))
			{
				$games.='<a href="'.$server_absolute_path_info.'events/'.$b["id"].'/">'.decode($b["name"]).'</a><br>';
			}
			$allgroup2[$r]=Array("ingroup"=>decode($prev),"people"=>$people,"games"=>$games);
			$r++;
			$people='';
			$prev=$a["ingroup"];
		}
		if($a["id"]!='')
		{
			$people.='<nobr>'.usname($a, true, true).'</nobr><br>
';
		}
	}
}

$games='';
$result2=mysql_query("SELECT * from ".$prefix."allgames where mg LIKE '%".decode($prev)."%' order by name asc");
while($b=mysql_fetch_array($result2))
{
	$games.='<a href="'.$server_absolute_path_info.'events/'.$b["id"].'/">'.decode($b["name"]).'</a><br>';
}
$allgroup2[$r]=Array("ingroup"=>decode($prev),"people"=>$people,"games"=>$games);
$r++;
$people='';
$prev='';
$totalmgs=count($allgroup2);

if($filter2=='' || $allgroup2[0]["ingroup"]!='') {
	$bazecount=$_SESSION["bazecount"];
    if($bazecount=='') {
    	$bazecount=50;
    }
	$pagetitle=h1line('Мастерские группы',$curdir.$kind.'/');

	$content2.='<div class="narrow">
<table class="menutable">
<tr class="menu">
<td>
Название
</td>
<td>
Состав
</td>
<td>
Игры
</td>
</tr>';
	$stringnum=1;
	$start=$page*$bazecount;
	for($j=$start;$j<$start+$bazecount;$j++)
	{
		$a=$allgroup2[$j];
		if(isset($a["ingroup"]) && $a["ingroup"]!='')
		{
			$content2.='<tr';
			if($stringnum%2==1) {
				$content2.=' class="string1"';
			}
			else {
				$content2.=' class="string2"';
			}
			$content2.='><td>'.$a["ingroup"].'</td><td>'.$a["people"].'</td><td>'.$a["games"].'</td></tr>';
			$stringnum++;
		}
	}
	$content2.='</table></div><br>';

	if($filter2!='') {
		$content2.=pagecount($filter2,$totalmgs,$bazecount);
	}
	else {
		$content2.=pagecount('',$totalmgs,$bazecount);
	}
}
?>