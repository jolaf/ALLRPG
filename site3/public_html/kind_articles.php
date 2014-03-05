<?php
if($subobj=='') {
	$subobj=0;
}

if($id==0) {
	$id=$object;
}

if($id>0) {
	$result=mysql_query("SELECT * FROM ".$prefix."articles WHERE id=".$id);
	$a = mysql_fetch_array($result);
	$subobj=$a["parent"];
}

$mainpath='<a href="'.$server_absolute_path_info.'articles/"><b>Статьи</b></a> ';

$pagetitle=h1line('Статьи',$curdir.$kind.'/');

$content2.='<div class="narrow">
<div style="background-color: #f3f3f3; padding: 3px; margin-bottom: 10px;"><b>Вы здесь</b>: <!--mainpath--></div>';

if($subobj!=0)
{
	$result=mysql_query("SELECT * FROM ".$prefix."articles WHERE id=".$subobj." and content='{menu}' and active='1'");
	$a = mysql_fetch_array($result);
	$subobjid=$a["id"];
	$mainpath2='<a href="'.$server_absolute_path_info.'articles/subobj='.$a["id"].'"><b>'.decode($a["name"]).'</b></a>';
	if($id!='') {
		$result2=mysql_query("SELECT * FROM ".$prefix."articles WHERE id=".$id." and content!='' and active='1'");
		$b = mysql_fetch_array($result2);
		if($b["id"]!='') {
			$mainpath2.=' <b>&#8594;</b> <a href="'.$server_absolute_path_info.'articles/'.$id.'/subobj='.$a["id"].'"><b>'.decode($b["name"]).'</b></a>';
		}
	}
	while($a["parent"]!=0)
	{
		$result=mysql_query("SELECT * FROM ".$prefix."articles WHERE id=".$a["parent"]." and content='{menu}'");
		$a = mysql_fetch_array($result);
		if($a["active"]=='1')
		{
			$mainpath2='<a href="'.$server_absolute_path_info.'articles/subobj='.$a["id"].'"><b>'.decode($a["name"]).'</b></a> <b>&#8594;</b> '.$mainpath2;
		}
	}
	if($mainpath2!='')
	{
		$mainpath.='<b>&#8594;</b> '.$mainpath2;
	}
}

if($id==0)
{
	if($subobj==0) {
		$subobjid=0;
	}
	else {
		$result=mysql_query("SELECT * FROM ".$prefix."articles WHERE id=".$subobj." and content='{menu}' and active='1'");
		$a = mysql_fetch_array($result);
		$subobjid=$a["id"];
		$content2.='<h1>'.decode($a["name"]).'</h1>';
	}

	$showit=true;
	$was=false;
	$result=mysql_query("SELECT * FROM ".$prefix."articles WHERE parent=".$subobjid." and content='{menu}' and active='1' order by code asc");
	while($a = mysql_fetch_array($result))
	{
		if($showit)
		{
			$content2.='<h3>Подразделы:</h3><table class="menutable">';
			$showit=false;
		}
		$content2.='<tr><td width=50>';
		if($a["im"]!='')
		{
			$content2.='<a href="'.$server_absolute_path_info.'articles/subobj='.$a["id"].'"><img src="'.$server_absolute_path.$uploads[11]['path'].$a["im"].'" title="'.decode($a["name"]).'"></a>';
		}
		else
		{
			$content2.='&nbsp;';
		}
		$content2.='</td><td valign=middle><a href="'.$server_absolute_path_info.'articles/subobj='.$a["id"].'"><b>'.decode($a["name"]).'</b></a><br>'.decode($a["content2"]).'</td></tr>';
		$was=true;
	}
	if($was)
	{
		$content2.='</table><br>';
	}

	$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."articles WHERE parent=".$subobjid." and content!='{menu}' and active='1' order by date desc");
	$a = mysql_fetch_array($result);
	if($a[0]>0) {
		$content2.='<table class="menutable"><tr class="menu"><td width="50%">Название</td><td>Автор</td><td>Дата</td></tr>';
	}
	$was=false;
	$result=mysql_query("SELECT * FROM ".$prefix."articles WHERE parent=".$subobjid." and content!='{menu}' and active='1' order by date desc");
	while($a = mysql_fetch_array($result))
	{
		$content2.='<tr><td><a href="'.$server_absolute_path_info.'articles/'.$a["id"].'/subobj='.$subobjid.'"><b>'.decode($a["name"]).'</b></a><br>'.decode($a["content2"]).'</td><td>';
		$tryauthor=explode(',', decode($a["author"]));
		for($i=0;$i<count($tryauthor);$i++)
		{
			if($i>0)
			{
				$content2.='<br>';
			}
			$checker=$tryauthor[$i];
			settype($tryauthor[$i], "integer");
			if(is_int($tryauthor[$i]) && $tryauthor[$i]!=0 && $tryauthor[$i]!='')
			{
				$result2=mysql_query("SELECT * FROM ".$prefix."users WHERE sid=".$tryauthor[$i]);
				$b = mysql_fetch_array($result2);
				$content2.=usname($b, true, true);
			}
			else
			{
				$content2.=decode($checker);
			}
		}
		$content2.='</td><td>'.date("d.m.Y",$a["date"]).' в '.date("G:i",$a["date"]).'</td></tr>';
		$was=true;
	}
	if($was)
	{
		$content2.='</table>';
	}
}
else
{
	$result=mysql_query("SELECT * FROM ".$prefix."articles WHERE id=".$id);
	$a = mysql_fetch_array($result);
	if($a["active"]=='1')
	{
		$content2.='<h1>'.decode($a["name"]).'</h1>';
		if($a["date"]!='') {
			$content2.='<span class="date">'.date("d.m.Y",$a["date"]).' в '.date("G:i",$a["date"]).'</span><br><br>';
		}
		if($a["author"]!='')
		{
			$content2.='<b>Автор(-ы)</b>: ';
			$tryauthor=explode(',', decode($a["author"]));
			for($i=0;$i<count($tryauthor);$i++)
			{
				if($i>0)
				{
					$content2.=', ';
				}
				$checker=$tryauthor[$i];
				settype($tryauthor[$i], "integer");
				if(is_int($tryauthor[$i]) && $tryauthor[$i]!=0 && $tryauthor[$i]!='')
				{
					$result2=mysql_query("SELECT * FROM ".$prefix."users WHERE sid=".$tryauthor[$i]);
					$b = mysql_fetch_array($result2);
					$content2.=usname($b, true, true);
				}
				else
				{
					$content2.=decode($checker);
				}
			}
			$content2.='<br>';
		}
		if($a["user_id"]>0) {
			$result2=mysql_query("SELECT * FROM ".$prefix."users WHERE sid=".$a["user_id"]);
			$b = mysql_fetch_array($result2);
			$content2.='<b>Внес статью</b>: '.usname($b, true, true).'<br>';
		}
		if($a["tags"]!='' && $a["tags"]!='-') {
			$content2.='<b>Теги</b>: ';
			$result2=mysql_query("SELECT * FROM ".$prefix."tags");
			while($b = mysql_fetch_array($result2))
			{
				if(strpos($a["tags"],'-'.$b["id"].'-')!==false) {
					$content2.=decode($b["name"]).', ';
				}
			}
			$content2=substr($content2,0,strlen($content2)-2);
			$content2.='<br>';
		}
		$content2.='<br>
'.decode($a["content"]);
	}

	if($a["nocomments"]!='1')
	{
		if($action=="commentadd") {
			if($dynrequest==1) {
				if(encode($_REQUEST["content"])=='') {
					dynamic_err_one('error','Введите текст комментария.');
				}
				else {
					dynamic_err(array(),'submit');
				}
			}
			$result3=mysql_query("SELECT * FROM ".$prefix."users where id=".$_SESSION["user_id"]);
			$c = mysql_fetch_array($result3);
			if($a["user_id"]!='') {
				$result2=mysql_query("SELECT * from ".$prefix."users where sid=".$a['user_id']);
				$b=mysql_fetch_array($result2);
				$myname=usname($c,true);
				$myemail=decode($c["em"]);
				$contactemail=decode($b["em"]);
				$subobjject='Новый комментарий по статье «'.decode($a["name"]).'» на allrpg.info';
				$message='Пользователь '.usname($c,true).' оставил комментарий к размещенной вами статье «'.decode($a["name"]).'»:<br>
'.decode2(encode($_POST["content"])).'<br>
Увидеть все комментарии к статье вы можете здесь: <a href="http://www.allrpg.info/'.$server_absolute_path_info.'articles/'.$id.'/subobj='.$subobj.'"></a>.';
				require_once($server_inner_path.$direct."/classes/base_mails.php");
			}
			if((($a["user_id"]!='' && $myname!='' && $myemail!='' && $contactemail!='') || $a["user_id"]=='' || $a["user_id"]==0) && encode($_POST["content"])!='') {
				$result4=mysql_query("SELECT * FROM ".$prefix."pagecomments where user_id=".$_SESSION["user_id"]." and page_id=".$id." and content='".encode(strip_tags($_POST["content"]))."' and site_id=0");
				$d = mysql_fetch_array($result4);
				if($d["id"]!='') {
					err_red("Заблокировано повторное сохранение.");
				}
				else
				{
					if(mysql_query("INSERT INTO ".$prefix."pagecomments (user_id,page_id,content,site_id,date) VALUES (".$_SESSION["user_id"].", ".$id.", '".encode(strip_tags($_POST["content"]))."', 0, ".time().")")) {
						if($contactemail!='') {
							send_mail($myname, $myemail, $contactemail, $subobjject, $message, true);
						}
						err("Комментарий успешно добавлен.");
					}
				}
			}
		}
		$comments='</div><h1>Комментарии</h1><div class="narrow">';

		$result=mysql_query("SELECT * FROM ".$prefix."users where id=".$_SESSION["user_id"]);
		$a = mysql_fetch_array($result);
		if($a["id"]!='')
		{
			$comments.='
<form action="'.$curdir.$kind.'/" method="post" enctype="multipart/form-data">
';
			if($subobj!=0) {
				$comments.='<input type="hidden" name="subobj" value="'.$subobj.'">';
			}
			$comments.='
<input type="hidden" name="id" value="'.$id.'">
<input type="hidden" name="action" value="commentadd">
<div class="fieldname" id="name_content">Текст комментария:</div>
<div class="fieldvalue" id="div_content"><textarea name="content" rows=3 class="mustbe"></textarea></div>
<center><button class="main">Добавить комментарий</button></center>
</form>
<br>
<hr>';
		}
		else
		{
			$comments.='
<center><p class="error"><br><b>Для того чтобы оставить комментарий к странице, вам необходимо <a href="http://www.allrpg.info/register/">зарегистрироваться</a> и залогиниться.</b><br><br></p></center>
<hr>
	';
		}

		$bazecount=$_SESSION["bazecount"];
		if($bazecount=='') {
			$bazecount=50;
		}
		$result=mysql_query("SELECT * FROM ".$prefix."pagecomments where site_id=0 and page_id=".$id." order by date desc limit ".($page*$bazecount).",".$bazecount);
		while($a = mysql_fetch_array($result))
		{
			$result2=mysql_query("SELECT * FROM ".$prefix."users where id=".$a["user_id"]);
			$b = mysql_fetch_array($result2);

			$comments.='<span class="comment_user">'.usname($b,true,true).'</span> <span class="comment_date">('.date("d.m.Y в H:i",$a["date"]).')</span><br>
	<span class="comment_content">'.decode($a["content"]).'</span><hr>';
		}
		$content2.=$comments;

		$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."pagecomments where site_id=0 and page_id=".$id);
		$a=mysql_fetch_array($result);
		$totcomms=$a[0];
		if($totcomms>$bazecount) {
			$content2.='<br>'.pagecount('',$totcomms,$bazecount,'&id='.$id.'&subobj='.$subobj);
		}
	}
}
$content2.='</div>';

$content2=str_replace('<!--mainpath-->',$mainpath,$content2);
?>