<?php
if($_SESSION["user_id"]!='') {
	$bazecount=$_SESSION["bazecount"];
	if($bazecount=='') {
		$bazecount=50;
	}

	if($object>0) {
		$id=$object;
	}

	$pagetitle=h1line('Мои отзывы');

	if($action=='delete' && $id>0) {
		$result2=mysql_query("SELECT * FROM ".$prefix."comments where id=".$id." AND user_id=".$_SESSION["user_id"]);
		$b = mysql_fetch_array($result2);
		if($b["id"]!='')
		{
			mysql_query("DELETE from ".$prefix."comments where id=".$id);
			err('Ваш отзыв успешно удален.');
		}
		else
		{
			err_red('У вас нет прав на выполнение данной операции.');
		}
	}
	if($action=="edit" && $id>0) {
		$result2=mysql_query("SELECT * FROM ".$prefix."comments where id=".$id." AND user_id=".$_SESSION["user_id"]);
		$b = mysql_fetch_array($result2);
		if($b["id"]!='') {
			if($actiontype=="do") {
				if(encode(strip_tags($_POST["content"]))!='')
				{
					if($dynrequest==1) {
						dynamic_err(array(),'submit');
					}
					mysql_query("UPDATE ".$prefix."comments SET content='".encode($_POST["content"])."', rating='".encode($_POST["rating"])."', date='".time()."' where id=".$id." AND user_id=".$_SESSION["user_id"]);
					err('Изменения успешно внесены.');
				}
				else {
					if($dynrequest==1) {
						dynamic_err_one('error','Введите текст отзыва!');
					}
				}
			}
			if($b["whom"]>0)
			{
				$filter="person";
			}
			elseif($b["game"]>0)
			{
				$filter="game";
			}

			$result=mysql_query("SELECT * FROM ".$prefix."comments where id=".$id." and user_id=".$_SESSION["user_id"]);
			$a=mysql_fetch_array($result);
			if($a["id"]!='' && $_SESSION["user_id"]!='') {
				$content2.='<form action="'.$curdir.$kind.'/" method="post" enctype="multipart/form-data">
<input type="hidden" name="action" value="edit">
<input type="hidden" name="filter" value="'.$filter.'">
<input type="hidden" name="id" value="'.$id.'">
<input type="hidden" name="actiontype" value="do">
';
				if($actiontype!="do") {
					$_POST["content"]=decode($a["content"]);
					$_POST["rating"]=decode($a["rating"]);
				}

				$obj_1=createElem(Array(
						'name'	=>	"content",
						'sname'	=>	"Текст Вашего отзыва",
						'type'	=>	"textarea",
						'read'	=>	10,
						'write'	=>	100,
						'rows'	=>	5,
						'mustbe'	=>	true,
						'default'	=>	$_POST["content"],
					)
				);

				$obj_2=createElem(Array(
						'name'	=>	"rating",
						'sname'	=>	"Рейтинг",
						'type'	=>	"select",
						'values'	=>	Array(Array('-1','-1'),Array('1','+1')),
						'read'	=>	10,
						'write'	=>	100,
						'default'	=>	$_POST["rating"],
					)
				);

				$content2.='<div class="fieldname" id="name_content">Текст Вашего отзыва</div><div class="fieldvalue" id="div_content">'.$obj_1->draw(2, "write").'</div>';

				if($filter=="game")
				{
					$content2.='<div class="fieldname" id="name_rating">Рейтинг</div><div class="fieldvalue" id="div_rating">'.$obj_2->draw(2, "write").'</div>';
				}

				$content2.='
<center><button class="main">изменить отзыв</button></center>
</form>';
				}
		}
		else {
			err_red('У вас нет прав на выполнение данной операции.');
		}
	}
	else
	{
		$start=$page*$bazecount;
		$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."comments where user_id=".$_SESSION["user_id"]);
		$b = mysql_fetch_array($result2);
		if($b[0]>$start) {
			$content2.='<table class="menutable">';
			$i=2;
			$result2=mysql_query("SELECT * FROM ".$prefix."comments where user_id=".$_SESSION["user_id"]." order by date desc LIMIT ".$start.",".$bazecount);
			while($b = mysql_fetch_array($result2))
			{
				$content2.='<tr';
				if($i%2==0) {
					$content2.=' class="string1"';
				}
				else {
					$content2.=' class="string2"';
				}
				$content2.='><td>';
				if($b["whom"]>0)
				{
					$result=mysql_query("SELECT * FROM ".$prefix."users where id=".$b["whom"]);
					$a = mysql_fetch_array($result);
					$content2.='<b>Пользователь</b>: '.usname($a, true, true).'<br>';
				}
				elseif($b["game"]>0)
				{
					$result=mysql_query("SELECT * FROM ".$prefix."allgames where id=".$b["game"]);
					$a = mysql_fetch_array($result);
					$content2.='<b>Событие</b>: <a href="'.$server_absolute_path_info.'events/'.$a["id"].'/">'.$a["name"].'</a><br>';
				}
				$content2.='
<b>Текст отзыва</b>: <br>
'.strip_tags(decode($b["content"])).'<br>';
				if($b["game"]>0) {
					if($b["rating"]=='-1') {
						$rating="-1";
					}
					elseif($b["rating"]=='1') {
						$rating="+1";
					}
					else {
						$rating="0";
					}
					$content2.='<b>Проставлен рейтинг</b>: '.$rating;
				}
				$content2.='</td><td align=right valign=middle>
<a href="'.$server_absolute_path_info.$kind.'/'.$b["id"].'/action=edit"><b><nobr>редактировать отзыв</nobr></b></a><br>
<a href="'.$server_absolute_path_info.$kind.'/'.$b["id"].'/action=delete"><b>удалить отзыв</b></a></td></tr>';
				$i++;
			}
			$content2.='</table><br>';
			$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."comments where user_id=".$_SESSION["user_id"]);
			$a=mysql_fetch_array($result);
			$starttotal=$a[0];

			$content2.=pagecount('',$starttotal,$bazecount);
		}
		else {
			$content2.='<center>Вы не написали ни одного отзыва.</center>';
		}
	}
}
?>