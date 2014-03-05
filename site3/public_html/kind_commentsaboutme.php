<?php
if($_SESSION["user_id"]!='') {
	$bazecount=$_SESSION["bazecount"];
	if($bazecount=='') {
		$bazecount=50;
	}

	if($object>0) {
		$id=$object;
	}

	$pagetitle=h1line('Отзывы обо мне');

	$mygames='';
	$result2=mysql_query("SELECT parent FROM ".$prefix."allgames where user_id=".$_SESSION["user_sid"]." and master LIKE '%-27-%'");
	while($b = mysql_fetch_array($result2))
	{
		$mygames.=$b["parent"].',';
	}
	$mygames=substr($mygames,0,strlen($mygames)-1);

	if($action=='activityon' || $action=='activityoff') {
		if($mygames!='') {
			$result2=mysql_query("SELECT * FROM ".$prefix."comments where id=".$id." AND (whom=".$_SESSION["user_id"]." OR game IN (".$mygames."))");
		}
		else {
			$result2=mysql_query("SELECT * FROM ".$prefix."comments where id=".$id." AND whom=".$_SESSION["user_id"]);
		}
		$b = mysql_fetch_array($result2);
		if($b["id"]!='') {
			if($b["active"]=='1' && $action=='activityoff') {
				mysql_query("UPDATE ".$prefix."comments set active=0 where id=".$id);
				err('Отзыв успешно скрыт для просмотра.');
			}
			elseif($b["active"]=='0' && $action=='activityon') {
				mysql_query("UPDATE ".$prefix."comments set active=1 where id=".$id);
				err('Отзыв успешно открыт для просмотра.');
			}
		}
		else {
			err_red('У вас нет прав на выполнение данной операции.');
		}
	}

	$start=$page*$bazecount;
	if($mygames!='') {
		$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."comments where whom=".$_SESSION["user_id"]." OR game IN (".$mygames.") order by date desc");
	}
	else {
		$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."comments where whom=".$_SESSION["user_id"]." order by date desc");
	}
	$b = mysql_fetch_array($result2);
	if($b[0]>0) {
		$content2.='<table class="menutable">';
		$i=2;
		if($mygames!='') {
			$result2=mysql_query("SELECT * FROM ".$prefix."comments where whom=".$_SESSION["user_id"]." OR game IN (".$mygames.") order by date desc LIMIT ".$start.",".$bazecount);
		}
		else {
			$result2=mysql_query("SELECT * FROM ".$prefix."comments where whom=".$_SESSION["user_id"]." order by date desc LIMIT ".$start.",".$bazecount);
		}
		while($b = mysql_fetch_array($result2))
		{
			$content2.='<tr';
			if($i%2==0) {
				$content2.=' class="string2"';
			}
			else {
				$content2.=' class="string1"';
			}
			$content2.='><td>';
			if($b["game"]>0)
			{
				$result=mysql_query("SELECT * FROM ".$prefix."allgames where id=".$b["game"]." and parent=0");
				$a = mysql_fetch_array($result);

				$content2.='<b>Событие</b>: <a href="'.$server_absolute_path_info.'events/'.$a["id"].'/">'.decode($a["name"]).'</a><br>
';
				$rating=0;
				$result=mysql_query("SELECT * FROM ".$prefix."comments where game=".$b["game"]);
				while($a = mysql_fetch_array($result))
				{
					$rating+=$a["rating"];
				}
				$rating+=0;
				if($rating>0)
				{
					$rating='+'.$rating;
				}
				$content2.='<b>Общий рейтинг события</b>: '.$rating.'<br><br>';
			}

			$result=mysql_query("SELECT * FROM ".$prefix."users where id=".$b["user_id"]);
			$a = mysql_fetch_array($result);
			$content2.='<b>Пользователь</b>: '.usname($a, true, true).'<br>
	<b>Текст отзыва</b>: <br>
	'.strip_tags(decode($b["content"])).'
	<br>';
			if($b["game"]>0)
			{
				$content2.='
	<b>Проставленный рейтинг</b>: ';
				$b["rating"]+=0;
				if($b["rating"]>0)
				{
					$b["rating"]='+'.$b["rating"];
				}
				$content2.=$b["rating"];
			}
			$content2.='</td><td valign=middle>
<a href="'.$server_absolute_path_info.$kind.'/action=';
			if($b["active"]==1) {
				$content2.='activityoff&id='.$b["id"].'"><b><nobr>скрыть отзыв';
			}
			elseif($b["active"]==0) {
				$content2.='activityon&id='.$b["id"].'"><b><nobr>открыть отзыв';
			}
			$content2.='</nobr></b></a></td></tr>';
			$i++;
		}
		$content2.='</table><br>';

		$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."comments where whom=".$_SESSION["user_id"]." OR game IN (".$mygames.")");
		$a=mysql_fetch_array($result);
		$starttotal=$a[0];

		if($starttotal>$bazecount) {
			$content2.=pagecount('',$starttotal,$bazecount);
		}
	}
	else {
		$content2.='<center>Вам не написали ни одного отзыва.</center>';
	}
}
?>