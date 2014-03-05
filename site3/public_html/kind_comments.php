<?php
$bazecount=$_SESSION["bazecount"];
if($bazecount=='') {
	$bazecount=50;
}

if($object>0) {
	$id=$object;
}

if($actiontype!='' && encode(strip_tags($_POST["content"]))=='' && $dynrequest==1) {
	dynamic_err_one('error','Необходимо ввести текст отзыва.');
}
else {
	if($actiontype=="event") {
		$result=mysql_query("SELECT * FROM ".$prefix."comments where game=".encode($_POST["event"])." and user_id=".$_SESSION["user_id"]);
		$a = mysql_fetch_array($result);
		if($a["id"]!='' && $dynrequest==1) {
			dynamic_err_one('error','Вы уже добавляли отзыв по данному событию.');
		}
		else {
			if($dynrequest==1) {
				dynamic_err(array(),'submit');
			}
			mysql_query("INSERT INTO ".$prefix."comments (user_id, game, content, rating, active, date) values (".$_SESSION["user_id"].", ".encode($_POST["event"]).", '".encode($_POST["content"])."', '".encode($_POST["rating"])."', '1', '".time()."')");
			err('Отзыв успешно добавлен.');
		}
	}
	elseif($actiontype=="person") {
		$result=mysql_query("SELECT * FROM ".$prefix."comments where whom=".encode($_POST["whom"])." and user_id=".$_SESSION["user_id"]);
		$a = mysql_fetch_array($result);
		if($a["id"]!='' && $dynrequest==1) {
			dynamic_err_one('error','Вы уже добавляли отзыв по данному пользователю.');
		}
		else {
			if($dynrequest==1) {
				dynamic_err(array(),'submit');
			}
			mysql_query("INSERT INTO ".$prefix."comments (user_id, whom, content, active, date) values (".$_SESSION["user_id"].", ".encode($_POST["whom"]).", '".encode($_POST["content"])."', '1', '".time()."')");
			err('Отзыв успешно добавлен.');
		}
	}
}

if($filter=="event") {
	$pagetitle=h1line('Отзывы по событию (игре)');
}
elseif($filter=="person") {
	$pagetitle=h1line('Отзывы по участнику');
}
else {
	$pagetitle=h1line('Отзывы');
}

if($filter=="event")
{
	$result2=mysql_query("SELECT * FROM ".$prefix."allgames where id=".$id." and parent=0");
	$b=mysql_fetch_array($result2);
	$content2.='<h1><a href="'.$server_absolute_path_info.'events/'.$b["id"].'/">'.$b["name"].'</a></h1>';
}
elseif($filter=="person")
{
	$b=getuser($id);
	$content2.='<h1>'.usname($b, true, true).'</h1>';
}

$rating=0;
if($filter=="event")
{
	$result2=mysql_query("SELECT * FROM ".$prefix."comments where game=".$id." order by date desc");
	while($b = mysql_fetch_array($result2))
	{
		$rating+=$b["rating"];
	}
	if($rating>0)
	{
		$rating='+'.$rating;
	}
	$content2.='<b>Общий рейтинг: '.$rating.'</b><br />';
}

$start=$page*$bazecount;
if($filter=="event")
{
	$result=mysql_query("SELECT * FROM ".$prefix."comments where game=".$id." and active='1' order by date desc LIMIT ".$start.",".$bazecount);
}
elseif($filter=="person")
{
	$result=mysql_query("SELECT * FROM ".$prefix."comments where whom=".$id." and active='1' order by date desc LIMIT ".$start.",".$bazecount);
}
$content2.='<table class="menutable">';
$i=0;
while($a=mysql_fetch_array($result))
{
	$content2.='<tr ';
	if($i%2==0) {
		$content2.=' class="string2"';
	}
	else {
		$content2.=' class="string1"';
	}
	$i++;
	$content2.='><td style="text-align: center; vertical-align: middle;">';
	$b=getuser($a["user_id"]);
	if($_SESSION["user_id"]!='') {
		$content2.='<a href="'.$server_absolute_path_info.'users/'.$b["sid"].'/">';
	}
	if($b["photo"]!='' && strpos($b["hidesome"],'-1-')===false) {
		$content2.='<img src="'.$server_absolute_path.$uploads[4]['path'].$b["photo"].'" height=50>
';
	}
	else {
		$content2.='<img src="'.$server_absolute_path.'identicon.php?hash='.md5(md5($b["em"]).'cetb').'&size=200" height=50>
';
	}
	if($_SESSION["user_id"]!='') {
		$content2.='</a>';
	}

	$content2.='</td><td><b>Пользователь</b>: '.usname($b, true, true).'<br>
';
	$content2.='<b>Отзыв</b>: '.strip_tags(decode($a["content"])).'
';
	if($filter=="event")
	{
		$a["rating"]=$a["rating"]+0;
		if($a["rating"]>0) {
			$a["rating"]='+'.$a["rating"];
		}
		$content2.='<br><b>Проставил рейтинг</b>: '.decode($a["rating"]).'
';
	}
	$content2.='
</td></tr>';
}
$content2.='</table><br>';

if($filter=="event") {
	$result=mysql_query("SELECT * FROM ".$prefix."comments where game=".$id." and user_id=".$_SESSION["user_id"]." order by date desc");
}
elseif($filter=="person") {
	$result=mysql_query("SELECT * FROM ".$prefix."comments where whom=".$id." and user_id=".$_SESSION["user_id"]." order by date desc");
}
$a=mysql_fetch_array($result);
if($a["id"]!='' || $_SESSION["user_id"]=='') {
	if($_SESSION["user_id"]=='') {
		err_red('Для того чтобы ввести отзыв, вам нужно залогиниться.');
	}
	else {
		err_red('Вы уже оставили отзыв. Содержимое отзыва и рейтинг (у событий) вы можете изменить в рубрике «<a href="'.$server_absolute_path_info.'mycomments/">Отзывы в инфотеке, оставленные мной</a>».');
	}
	$content2.='<br>';
}
else
{
	$content2.='<form action="'.$curdir.$kind.'/" method="post" enctype="multipart/form-data">
<input type="hidden" name="action" value="comment">
<input type="hidden" name="filter" value="'.$filter.'">';
	if($filter=="event") {
		$content2.='
<input type="hidden" name="actiontype" value="event">
<input type="hidden" name="event" value="'.$id.'">';
	}
	elseif($filter=="person") {
		$content2.='
<input type="hidden" name="actiontype" value="person">
<input type="hidden" name="whom" value="'.$id.'">';
	}
	$content2.='
<input type="hidden" name="id" value="'.$id.'">
';
	$obj=createElem(Array(
			'name'	=>	'content',
			'sname'	=>	'Текст вашего отзыва',
			'type'	=>	'textarea',
			'rows'	=>	6,
			'read'	=>	10,
			'write'	=>	10,
			'mustbe'	=>	true,
		)
	);
	$content2.='<div class="fieldname" id="name_content">Текст вашего отзыва:</div>
<div class="fieldvalue" id="div_content">
'.$obj->draw(2,"write").'
</div>';
	if($filter=="event")
	{
		$content2.='
<div class="fieldname" id="name_rating">Рейтинг:</div>
<div class="fieldvalue" id="div_rating">
<select name="rating"><option>- Выберите -</option><option value="-1"';
		if(encode($_POST["rating"])=="-1")
		{
			$content2.=" selected";
		}
		$content2.='>-1</option><option value="1"';
		if(encode($_POST["rating"])=="1")
		{
			$content2.=" selected";
		}
		$content2.='>+1</option></select></div>';
	}
	$content2.='
<center><button class="main">Добавить отзыв</button></center>
</form>';
}
$content2.='</center><br>';

if($filter=="event") {
	$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."comments where game=".$id." and active='1' order by date desc");
}
elseif($filter=="person") {
	$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."comments where whom=".$id." and active='1' order by date desc");
}
$a=mysql_fetch_array($result);
$starttotal=$a[0];
if($starttotal>$bazecount) {
	$content2.=pagecount('',$starttotal,$bazecount,'&id='.$id.'&filter='.$filter);
}
?>