<?php
include_once("db.inc");
include_once("classes_objects_allrpg.php");

session_start();

start_mysql();
# Установление соединения с MySQL-сервером

$kind=encode($_GET["kind"]);

if($kind==1)
{
	$content.='<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0" xml:base="'.$server_absolute_path.'" xmlns:dc="http://purl.org/dc/elements/1.1/">
<channel>
 <title>allrpg.info – новые участники</title>
 <link>'.$server_absolute_path.'</link>
 <description>allrpg.info</description>
 <language>ru</language>
';

	$result=mysql_query("SELECT * FROM ".$prefix."users order by id desc limit 0,3");
	while($a = mysql_fetch_array($result))
	{
		$content.='<item>
	<link>'.$server_absolute_path_info.'users/'.$a["id"].'/</link>
';

		$content.='<title>'.usname($a,true,true).'</title>
';

		$content.='<description>';
		$all=usname($a,true,true).'
<hr>';
		$content.=rehash($all).'</description>
';

		$content.='<pubDate>'.date("D, d M Y H:i:s O").'</pubDate>
	<guid>'.$server_absolute_path_info.'users/'.$a["id"].'/</guid>
	</item>
';
	}
}
elseif($kind==2)
{
	$content.='<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0" xml:base="'.$server_absolute_path.'" xmlns:dc="http://purl.org/dc/elements/1.1/">
<channel>
 <title>allrpg.info – все новости</title>
 <link>'.$server_absolute_path.'</link>
 <description>allrpg.info</description>
 <language>ru</language>
';

	$result=mysql_query("SELECT * FROM ".$prefix."news where active='1' order by date desc limit 0,20");
	while($a = mysql_fetch_array($result))
	{
		if($a["content"]!='')
		{
			$content.='<item>
';
			if($a["site_id"]!=0)
			{
				$result3=mysql_query("SELECT * FROM ".$prefix."sites where id=".$a["site_id"]);
				$c = mysql_fetch_array($result3);
				$result4=mysql_query("SELECT * FROM ".$prefix."pages where id=".$c["newscode"]);
				$d = mysql_fetch_array($result4);
				if($d["parent"]>0) {
					$result5=mysql_query("SELECT * FROM ".$prefix."pages where id=".$d["parent"]);
					$e = mysql_fetch_array($result5);
					if($e["alias"]!='') {
						$path=$lead1.$c["path"].$lead2.decode($e["alias"]).'/'.$d["code"].'/';
					}
					else {
						$path=$lead1.$c["path"].$lead2.$e["code"].'/'.$d["code"].'/';
					}
				}
				else {
					if($d["alias"]!='') {
						$path=$lead1.$c["path"].$lead2.decode($d["alias"]).'/';
					}
					else {
						$path=$lead1.$c["path"].$lead2.$d["code"].'/';
					}
				}
				$content.='<title>'.decode($c["title"]).': '.decode($a["name"]).'</title>
';
			}
			else
			{
				$path=$server_absolute_path.'news/1/';
				$content.='<title>allrpg.info: '.decode($a["name"]).'</title>
';
			}

			if($a["main"]=='')
			{
				$content.='<link>'.$path.'</link>
';
			}
			else
			{
				$content.='<link>'.$path.$a["id"].'/</link>
';
			}

			$content.='<description>';
			$all='';
			if($a["main"]!='')
			{
				$all=strip_tags(decode($a["main"]));
			}
			else
			{
				$all=strip_tags(decode($a["content"]));
			}

			if($a["author"]!='')
			{
				$all.='<br><br>
<b>Автор</b>: ';
				$result2=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$a["author"]);
				$b = mysql_fetch_array($result2);
				if($b["id"]!='') {
					$all.='<i>'.usname($b,true,true).'</i>';
				}
			}

			if($a["sour"]!='')
			{
				$all.='<br><br>
<b>Источник</b>: ';
				$server_absolute_pathour=$a["sour"];
				if(strpos($server_absolute_pathour,"http://")!==false)
				{
					$all.='<a href="'.decode($a["sour"]).'">'.decode($a["sour"]).'</a>';
				}
				else
				{
					$all.=decode($a["sour"]);
				}
			}
			$all.='
<hr>';

			$content.=rehash($all).'</description>
';

			$content.='<pubDate>'.date("D, d M Y H:i:s O", $a["date"]).'</pubDate>
	<guid>'.$path.$a["id"].'/</guid>
	</item>
';
		}
	}
}
elseif($kind==3)
{
	$content.='<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0" xml:base="'.$server_absolute_path.'" xmlns:dc="http://purl.org/dc/elements/1.1/">
<channel>
 <title>allrpg.info – новые отчеты</title>
 <link>'.$server_absolute_path.'</link>
 <description>allrpg.info</description>
 <language>ru</language>
';

	$result=mysql_query("SELECT * FROM ".$prefix."reports order by id desc limit 0,6");
	while($a = mysql_fetch_array($result))
	{
		$result2=mysql_query("SELECT * from ".$prefix."users where id=".$a["user_id"]);
		$b=mysql_fetch_array($result2);

		$server_absolute_pathol=usname($b,true,true);

		$result2=mysql_query("SELECT * from ".$prefix."allgames where id=".$a["game"]);
		$b=mysql_fetch_array($result2);

		$game=decode($b["name"]);

		$content.='<item>
<link>'.$server_absolute_path_info.'reports/'.$a["id"].'/</link>
';

		$content.='<title>'.$game.'</title>
';

		$content.='<description>';
		$all='';
		$all.='<b>Событие</b>: '.$game.'<br>
<b>Автор</b>: '.$server_absolute_pathol.'<br>
<hr>';
		$content.=rehash($all).'</description>
';

		$content.='<pubDate>'.date("D, d M Y H:i:s O", $a["date"]).'</pubDate>
	<guid>'.$server_absolute_path_info.'reports/'.$a["id"].'/</guid>
	</item>
';
	}
}
elseif($kind==4)
{
	$content.='<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0" xml:base="'.$server_absolute_path.'" xmlns:dc="http://purl.org/dc/elements/1.1/">
<channel>
 <title>allrpg.info – новые события</title>
 <link>'.$server_absolute_path.'</link>
 <description>allrpg.info</description>
 <language>ru</language>
';

	$result=mysql_query("SELECT * FROM ".$prefix."allgames WHERE master='{menu}' order by id desc limit 0,20");
	while($a = mysql_fetch_array($result))
	{
		$content.='<item>
<link>'.$server_absolute_path_info.'events/'.$a["id"].'/</link>
';

		$content.='<title>'.decode($a["name"]).'</title>
';

		$content.='<description>';
		$all='';
		$all.='<b>Событие</b>: '.decode($a["name"]).'<br>';
		$result2=mysql_query("SELECT * FROM ".$prefix."geography WHERE id=".$a["region"]);
		$b = mysql_fetch_array($result2);
		$all.='<b>Регион</b>: '.decode($b["name"]).'<br>';
		$all.='<b>Даты</b>: '.datesfmake($a["datestart"],$a["datefinish"]).'<br>';
		$result2=mysql_query("SELECT * FROM ".$prefix."areas WHERE id=".$a["area"]);
		$b = mysql_fetch_array($result2);
		$all.='<b>Полигон</b>: '.decode($b["name"]).'<br>';
		$all.='<b>Количество игроков</b>: '.decode($a["playernum"]).'<br>';
		$all.='<b>МГ</b>: '.decode($a["mg"]).'<br>';
		$all.='
<hr>';
		$content.=rehash($all).'</description>
';

		$content.='<pubDate>'.date("D, d M Y H:i:s O", $a["date"]).'</pubDate>
	<guid>'.$server_absolute_path_info.'events/'.$a["id"].'/</guid>
	</item>
';
	}
}
else
{
	header("Location: ".$server_absolute_path."error404.php");
}

$content.='</channel>
</rss>';

print($content);

# Вывод основного содержания страницы

stop_mysql();
# Разрыв соединения с MySQL-сервером

?>