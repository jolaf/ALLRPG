<?php

if($_SESSION["sitename"]!='') {
	$pagetitle=h1line($_SESSION["sitename"]);
	$result=mysql_query("SELECT * FROM ".$prefix."sites where id=".$_SESSION["siteid"]);
	$a = mysql_fetch_array($result);
	if($site>0) {
		err('Включено управление проектом «'.$_SESSION["sitename"].'».');
	}
}
else {
	$pagetitle=h1line('Проекты');
}
if(encode($_REQUEST['site'])=='exit') {
	err('Вы вышли из управления проектом.');
}

if($_SESSION["user_sid"]!='') {
	$result=mysql_query("SELECT id FROM ".$prefix."allrights2 WHERE user_id=".$_SESSION['user_sid']." limit 0,1");
	$a = mysql_fetch_array($result);
	if($a["id"]!='') {
		$thereisprojects=true;
	}
}

if($_SESSION["user_sid"]!='' && $thereisprojects) {
	$content2.='<div class="narrow">';
	$result=mysql_query("SELECT DISTINCT t1.* FROM ".$prefix."sites t1 LEFT JOIN ".$prefix."allrights2 t2 ON t2.site_id=t1.id WHERE t2.user_id=".$_SESSION["user_sid"]." and t1.datefinish>='".date("Y-m-d")."' order by t1.title asc");
	if(mysql_affected_rows($link)>0) {
		$content2.='
<table class="menutable">
<tr class="menu">
<td>активные проекты</td>
<td>статус</td>
<td>система заявок</td>
<td>права</td>
<td>даты</td>
</tr>';
	while($a = mysql_fetch_array($result)) {
        $rightsinsite=false;
        $result2=mysql_query("SELECT * FROM ".$prefix."allrights2 WHERE user_id=".$_SESSION["user_sid"]." and site_id=".$a["id"]);
		while($b = mysql_fetch_array($result2)) {
			if($b["rights"]==1 || $b["rights"]==4) {
				$rightsinsite=true;
			}
		}
        $content2.='<tr><td><a href="'.$server_absolute_path_site.'site='.$a["id"].'">'.decode($a["title"]).'</a></td><td>';
        if($a["status"]==1) {
        	$content2.='<nobr>';
        	if($rightsinsite) {
        		$content2.='<a href="'.$server_absolute_path_site.'settings/site='.$a["id"].'"><font color="red">нужно активировать</font></a>';
        	}
        	else {
        		if($a["path"]!='') {
        			$content2.='<a href="http://'.$a["path"].'.'.SERVER_DOMAIN.'">';
        		}
        		$content2.='<font color="red">не активирован</font>';
        		if($a["path"]!='') {
        			$content2.='</a>';
        		}
        	}
        	$content2.='</nobr>';
        }
        elseif($a["status"]==2) {
        	if($a["usetemp"]!=2 && $rightsinsite) {
        		$content2.='<a href="'.$server_absolute_path_site.'pages/site='.$a["id"].'"><font color="green">работает</font></a>';
        	}
        	elseif($a["usetemp"]!=2) {
        		$content2.='<a href="http://'.$a["path"].'.'.SERVER_DOMAIN.'"><font color="green">работает</font></a>';
        	}
        	else {
        		$content2.='<font color="green">работает</font>';
        	}
        }
        elseif($a["status"]==3) {
        	if($a["usetemp"]!=2) {
        		$content2.='<a href="http://'.$a["path"].'.'.SERVER_DOMAIN.'"><font color="green">закрыт</font></a>';
        	}
        	else {
        		$content2.='<font color="green">закрыт</font>';
        	}
        }
        $content2.='</td><td>';
	    if($a["usetemp"]!=1) {
	        if($a["status2"]==1) {
	        	$content2.='<nobr>';
		        if($rightsinsite) {
	        		$content2.='<a href="'.$server_absolute_path_site.'settings/site='.$a["id"].'"><font color="red">нужно открыть</font></a>';
	        	}
	        	else {
	        		$content2.='<a href="'.$server_absolute_path_site.'orders/site='.$a["id"].'"><font color="red">нужно открыть</font></a>';
	        	}
	        	$content2.='</nobr>';
	        }
	        elseif($a["status2"]==2) {
	        	$content2.='<a href="'.$server_absolute_path_site.'orders/site='.$a["id"].'"><font color="green">работает</font></a>';
	        }
	    }
	    else {
	    	$content2.='–';
	    }
	    $content2.='</td><td>';
        $result2=mysql_query("SELECT * FROM ".$prefix."allrights2 WHERE user_id=".$_SESSION["user_sid"]." and site_id=".$a["id"]);
		while($b = mysql_fetch_array($result2)) {
	        if($b["rights"]==1) {
	        	$content2.='глав.мастер<br>';
	        }
	        elseif($b["rights"]==2) {
	        	$content2.='мастер<br>';
	        }
	        elseif($b["rights"]==3) {
	        	$content2.='автор новостей<br>';
	        }
	        elseif($b["rights"]==4) {
	        	$content2.='дизайнер<br>';
	        }
	    }
	    $content2.='</td><td><a href="'.$server_absolute_path_site.'settings/site='.$a["id"].'">'.datesfmake($a["datestart"],$a["datefinish"]).'</a></td></tr>';
	}
		$content2.='
</table><br><br>';
	}

    $result=mysql_query("SELECT DISTINCT t1.* FROM ".$prefix."sites t1 LEFT JOIN ".$prefix."allrights2 t2 ON t2.site_id=t1.id WHERE t2.user_id=".$_SESSION["user_sid"]." and t1.datefinish<'".date("Y-m-d")."' order by t1.title asc");
	if(mysql_affected_rows($link)>0) {
		$content2.='
<table class="menutable">
<tr class="menu">
<td>прошедшие проекты</td>
<td>статус</td>
<td>система заявок</td>
<td>права</td>
<td>даты</td>
</tr>';
	while($a = mysql_fetch_array($result)) {
        $rightsinsite=false;
        $result2=mysql_query("SELECT * FROM ".$prefix."allrights2 WHERE user_id=".$_SESSION["user_sid"]." and site_id=".$a["id"]);
		while($b = mysql_fetch_array($result2)) {
			if($b["rights"]==1 || $b["rights"]==4) {
				$rightsinsite=true;
			}
		}
        $content2.='<tr><td><a href="'.$server_absolute_path_site.'site='.$a["id"].'">'.decode($a["title"]).'</a></td><td>';
        if($a["status"]!=3 && $rightsinsite) {
        	$content2.='<nobr><a href="'.$server_absolute_path_site.'settings/site='.$a["id"].'"><font color="red">нужно закрыть</font></a></nobr>';
        }
        elseif($a["status"]!=3) {
        	$content2.='<nobr>';
        	if($a["usetemp"]!=2) {
        		$content2.='<a href="http://'.$a["path"].'.'.SERVER_DOMAIN.'"><font color="red">нужно закрыть</font></a>';
        	}
        	else {
        		$content2.='<font color="red">нужно закрыть</font>';
        	}
        	$content2.='</nobr>';
        }
        elseif($a["status"]==3) {
        	if($a["usetemp"]!=2) {
        		$content2.='<a href="http://'.$a["path"].'.'.SERVER_DOMAIN.'"><font color="green">закрыт</font></a>';
        	}
        	else {
        		$content2.='<font color="green">закрыт</font>';
        	}
        }
        $content2.='</td><td>';
	    if($a["usetemp"]!=1) {
	        if($a["status2"]==1) {
	        	$content2.='<a href="'.$server_absolute_path_site.'orders/site='.$a["id"].'"><font color="green">закрыта</font></a>';
	        }
	        elseif($a["status2"]==2) {
	        	$content2.='<nobr>';
	        	$content2.=$rightsinsite?'<a href="'.$server_absolute_path_site.'settings/site='.$a["id"].'">':'';
	        	$content2.='<font color="red">нужно закрыть</font>';
	        	$content2.=$rightsinsite?'</a>':'';
	        	$content2.='</nobr>';
	        }
	    }
	    else {
	    	$content2.='–';
	    }
	    $content2.='</td><td>';
        $result2=mysql_query("SELECT * FROM ".$prefix."allrights2 WHERE user_id=".$_SESSION["user_sid"]." and site_id=".$a["id"]);
		while($b = mysql_fetch_array($result2)) {
	        if($b["rights"]==1) {
	        	$content2.='глав.мастер<br>';
	        }
	        elseif($b["rights"]==2) {
	        	$content2.='мастер<br>';
	        }
	        elseif($b["rights"]==3) {
	        	$content2.='автор новостей<br>';
	        }
	        elseif($b["rights"]==4) {
	        	$content2.='дизайнер<br>';
	        }
	    }
	    $content2.='</td><td><a href="'.$server_absolute_path_site.'settings/site='.$a["id"].'">'.datesfmake($a["datestart"],$a["datefinish"]).'</a></td></tr>';
	}
		$content2.='
</table>';
	}
	$content2.='
</div>';
}
else {
	$content2.=h1line('Что предлагает allrpg.info?');
	$content2.='<div class="narrow">Вам нужен сайт с системой заявок для Вашего ролевого проекта? Или просто система заявок?<br>
allrpg.info предлагает Вам решить все сложности разом.
<ul>
<li>субдомен на allrpg.info (например, http://test.allrpg.info);
<li>удобная и мощная система заявок;
<ul>
	<li>с настраиваемыми локациями;
	<li>с сеткой ролей;
	<li>с сеткой взаимоотношений ролей (чем связана одна роль с другой), в которой каждый игрок видит свою часть;
	<li>с настраиваемыми e-mail уведомлениями;
	<li>с настраиваемыми фильтрами для выборки;
	<li>с системой комментирования;
	<li>поля Вашей заявки Вы определяете сами;
	<li>информация об игроке автоматически прикрепляется к его заявке;
	<li>с тремя разными вариантами экспорта: Excel, PersonalBrain (удобнейшее представление сетки взаимоотношений) и, конечно же, оффлайн-версия системы заявок allrpg.info со всем функционалом, которую Вы можете взять с собой на полигон;
</ul>
<li>100 мегабайт под Ваши файлы;
<li>конструктор сайтов, помогающий создать сайт даже новичку и при этом дающий отличные возможности более продвинутым web-мастерам;
<ul>
	<li>гибкое управление дизайном;
	<li>автоматическое выведение сетки ролей и сетки поданных заявок на сайте проекта;
	<li>работа со страницами/разделами;
	<li>настраиваемый доступ пользователей к страницам;
	<li>система комментирования страниц сайта пользователями;
	<li>новостная лента;
</ul>
<li>быстрый и стабильный хостинг с регулярным backup\'ом (созданием резервной копии данных);
<li>автоматическое попадание новостей сайта в общую новостную ленту проекта allrpg.info (с ссылкой на Ваш ресурс);
<li>администрация allrpg.info, которая всегда готова помочь и выслушать рекомендации по улучшению проекта.
</ul>
Всё вышеперечисленное предоставляется бесплатно, без баннеров и без какой-либо иной рекламы. Просто мы любим ролевые игры. Так что пробуйте! Надеемся, что Вам понравится. :)
</div>';
}
?>