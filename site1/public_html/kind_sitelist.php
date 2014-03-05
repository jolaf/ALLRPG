<?php
$pagetitle=h1line('Список проектов');
$futureprojects='';
$futureprojectsclosed='';
$passedprojects='';
$result=mysql_query("SELECT * FROM ".$prefix."sites where testing!='1' ORDER by title asc");
while($a = mysql_fetch_array($result)) {
	$content4='<div style="background-color: #d3d3d3; padding: 3px; margin-bottom: 10px;">';
	if($a["path"]!='') {
		$content4.='<h1><a href="'.$lead1.$a["path"].$lead2.'" target="_blank">'.$a["title"].'</a></h1>';
	}
	elseif(decode($b["path2"])!='') {
		$content4.='<h1>';
		if(!eregi('http://',decode($a["path2"])) && !eregi('www.',decode($a["path2"]))) {
			$content4.='<a href="http://'.decode($a["path2"]).'">';
		}
		else {
			$content4.='<a href="'.decode($a["path2"]).'">';
		}
		$content4.='" target="_blank">'.$a["title"].'</a></h1>';
	}
	else {
		$content4.='<h1>'.$a["title"].'</h1>';
	}
	if($a["usetemp"]!=2) {
		if($a["status"]==1)
		{
			$content4.=' (не активирован)';
		}
		elseif($a["status"]==2)
		{
			$content4.=' (работает)';
		}
		elseif($a["status"]==3)
		{
			$content4.=' (закрыт)';
		}
	}
	else {
		$content4.=' (только система заявок)';
	}
	$content4.='</div>';

	$beforemenu=false;
	$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."rolevacancy WHERE site_id=".$a["id"]);
	$b = mysql_fetch_array($result2);
	if($b[0]>0) {
		$content4.='<div style="margin-bottom: 10px;"><a href="'.$server_absolute_path.'siteroles/'.$a["id"].'/"><b>Сетка ролей</b></a>';
		$beforemenu=true;
	}

	if($a["status2"]==2) {
		if($beforemenu) {
			$content4.=' &#149; ';
		}
		else {
			$content4.='<div style="margin-bottom: 10px;">';
		}
		if($_SESSION["user_id"]!='') {
			$content4.='<a href="'.$server_absolute_path.'order/myorders/act=add&subobj='.$a["id"].'">';
		}
		else {
			$content4.='<a href="'.$server_absolute_path.'register/redirectobj=order&redirectid='.$a["id"].'">';
		}
		$content4.='<b>Подать заявку</b></a>';
		$beforemenu=true;
	}

	$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."roles WHERE site_id=".$a["id"]);
	$b = mysql_fetch_array($result2);
	if($b[0]>0) {
		if($beforemenu) {
			$content4.=' &#149; ';
		}
		else {
			$content4.='<div style="margin-bottom: 10px;">';
		}
		if($a["alter_rolefield"]>0) {
			$content4.='<a href="'.$server_absolute_path.'siteroles2/'.$a["id"].'/"><b>Поданные заявки</b></a>';
		}
		else {
			$content4.='<a href="'.$server_absolute_path.'siteroles/'.$a["id"].'/orders=1"><b>Поданные заявки</b></a>';
		}
		$beforemenu=true;
	}
    if($beforemenu) {
		$content4.='</div>';
	}

	$content4.='
Администраторы:<br>';
	$result2=mysql_query("SELECT * FROM ".$prefix."allrights2 WHERE rights=1 and site_id=".$a["id"]);
	while($b = mysql_fetch_array($result2))
	{
		$result3=mysql_query("SELECT * FROM ".$prefix."users WHERE sid=".$b["user_id"]);
		$c = mysql_fetch_array($result3);
		$content4.=usname($c, true, true).'<br>';
	}

	$content3='';
	$result2=mysql_query("SELECT * FROM ".$prefix."allrights2 WHERE rights=2 and site_id=".$a["id"]);
	while($b = mysql_fetch_array($result2))
	{
		$result3=mysql_query("SELECT * FROM ".$prefix."users WHERE sid=".$b["user_id"]);
		$c = mysql_fetch_array($result3);
		$content3.=usname($c, true, true).'<br>';
	}
	if($content3!='')
	{
		$content4.='<br>Работают с заявками:<br>'.$content3;
	}

	$content3='';
	$result2=mysql_query("SELECT * FROM ".$prefix."allrights2 WHERE rights=4 and site_id=".$a["id"]);
	while($b = mysql_fetch_array($result2))
	{
		$result3=mysql_query("SELECT * FROM ".$prefix."users WHERE sid=".$b["user_id"]);
		$c = mysql_fetch_array($result3);
		$content3.=usname($c, true, true).'<br>';
	}
	if($content3!='')
	{
		$content4.='<br>Дизайнеры:<br>'.$content3;
	}

	$content3='';
	$result2=mysql_query("SELECT * FROM ".$prefix."allrights2 WHERE rights=3 and site_id=".$a["id"]);
	while($b = mysql_fetch_array($result2))
	{
		$result3=mysql_query("SELECT * FROM ".$prefix."users WHERE sid=".$b["user_id"]);
		$c = mysql_fetch_array($result3);
		$content3.=usname($c, true, true).'<br>';
	}
	if($content3!='')
	{
		$content4.='<br>Новости:<br>'.$content3;
	}
	$content4.='<br>';

	if($a["datefinish"]>=date("Y-m-d") && $a["status2"]==2) {
		$futureprojects.=$content4;
	}
	elseif($a["datefinish"]>=date("Y-m-d") && $a["status2"]!=2) {
		$futureprojectsclosed.=$content4;
	}
	else {
		$passedprojects.=$content4;
	}
}
$futureprojects=substr($futureprojects,0,strlen($futureprojects)-4);
$futureprojectsclosed=substr($futureprojectsclosed,0,strlen($futureprojectsclosed)-4);
$passedprojects=substr($passedprojects,0,strlen($passedprojects)-4);
$content2='<h1>Будущие проекты</h1>
<div class="narrow" style="text-align: center;">
'.$futureprojects.'
</div>
<br><hr>
<h1>Будущие проекты с закрытым приемом заявок</h1>
<div class="narrow" style="text-align: center">
'.$futureprojectsclosed.'
</div>
<br><hr>
<h1>Прошедшие проекты</h1>
<div class="narrow" style="text-align: center">
'.$passedprojects.'
</div>';
?>