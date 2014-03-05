<?php
include_once("../db.inc");

session_start();

start_mysql();
# Установление соединения с MySQL-сервером

if($_SESSION["admin"]) {
	$i=0;
	echo('<table border=1><tr><td><b>пользователь</b></td><td><b>инп</b></td><td><b>e-mail</b></td><td><b>в портфолио записи</b></td><td><b>заявки</b></td></tr>');
	$result=mysql_query("SELECT u1.* FROM ".$prefix."users u1, ".$prefix."users u2 where u1.fio=u2.fio and u1.id!=u2.id and LENGTH(u1.fio)>10 order by u1.fio, u1.sid");
	while($a=mysql_fetch_array($result))
	{
		echo('<tr><td>'.usname($a,true,true).'</td><td>'.$a["sid"].'</td><td>'.decode($a["em"]));

		$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."played where user_id=".$a["id"]);
		$b=mysql_fetch_array($result2);
		if($b[0]>0)
		{
			echo('</td><td>есть');
		}
		else
		{
			echo('</td><td>нет');
		}

		$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."roles where player_id=".$a["id"]);
		$b=mysql_fetch_array($result2);
		if($b[0]>0)
		{
			echo('</td><td>есть');
		}
		else
		{
			echo('</td><td>нет');
		}

		echo('</td></tr>');
		$i++;
	}
	echo('</table><br>');
	echo("Total: ".$i."<br>");
	# чистка баз форума и сайта от пользователей в статусе "Ожидается e-mail подтверждение".

	$content='Report complete.';

	print($content);
}

# Вывод основного содержания страницы

stop_mysql();
# Разрыв соединения с MySQL-сервером

?>