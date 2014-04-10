<?php

$kinds[0]=Array($server_absolute_path,'ЛИЧНОЕ',true);
$kinds[1]=Array($server_absolute_path_site,'УПРАВЛЕНИЕ',true);
$kinds[2]=Array($server_absolute_path_calendar,'КАЛЕНДАРЬ',true);
$kinds[3]=Array($server_absolute_path_info,'ИНФОРМАЦИЯ',true);

$subkinds[]=Array(0,$server_absolute_path.'news/','Новости');
if(isset($_SESSION["user_sid"])) {
	$subkinds[]=Array(0,$server_absolute_path.'order/','Мои заявки');
	$subkinds[]=Array(0,$server_absolute_path_info.'users/'.$_SESSION["user_sid"].'/','Мой профиль');
	$subkinds[]=Array(0,$server_absolute_path_calendar.'portfolio/','Мое портфолио');
	$subkinds[]=Array(0,'block','Мои сообщения');
	$subkinds[]=Array(0,$server_absolute_path.'inbox/','Входящие');
	$subkinds[]=Array(0,$server_absolute_path.'outbox/','Отправленные');
}


if($_SESSION["user_id"]!='') {
	$subkinds[]=Array(2,$server_absolute_path_calendar.'portfolio/','Мой календарь<hr>портфолио');
}
else {
	$subkinds[]=Array(2,$server_absolute_path.'register/redirectobj=portfolio&redirectid=1','Мой календарь<hr>портфолио');
}
$subkinds[]=Array(2,'block','Добавить');
$subkinds[]=Array(2,$server_absolute_path_info.'myevents/','событие<br>(игру)');
$subkinds[]=Array(2,$server_absolute_path_info.'myareas/','полигон');

$result3=mysql_query("SELECT * FROM ".$prefix."allgames WHERE master='{menu}' order by datestart desc limit 0,1");
$c = mysql_fetch_array($result3);
$dy2=date("Y",strtotime($c["datestart"]));
$result3=mysql_query("SELECT * FROM ".$prefix."allgames WHERE master='{menu}' order by datestart asc limit 0,1");
$c = mysql_fetch_array($result3);
$dy3=date("Y",strtotime($c["datestart"]));
if($dy3<$dy2-11) {
	$dy3=$dy2-11;
}
$z=0;
for($i=$dy3-1;$i<=$dy2;$i++) {
	if($z%5==0) {
		$subkinds[]=Array(2,'block',$i.'<br>'.($i+4>$dy2?$dy2:$i+4));
	}
	$subkinds[]=Array(2,$server_absolute_path_calendar.'wholeyear='.$i,$i);
	$z++;
}

$subkinds[]=Array(3,$server_absolute_path_info.'events/','События (игры)');
$subkinds[]=Array(3,$server_absolute_path_info.'reports/','Отчеты');
$subkinds[]=Array(3,$server_absolute_path.'news/','Новости');
$subkinds[]=Array(3,'block','Прочее');
/*$result=mysql_query("SELECT * FROM ".$prefix."sites where testing!='1' and id IN (SELECT DISTINCT site_id from ".$prefix."news where date>=".(time()-3600*24*30).") order by title asc");
while($a = mysql_fetch_array($result))
{
	$subkinds[]=Array(0,$server_absolute_path.'news/'.$a["id"].'/',decode($a["title"]));
}*/
$subkinds[]=Array(3,$server_absolute_path_info.'articles/','Полезные статьи');
if($_SESSION["user_id"]!='') {
	$subkinds[]=Array(3,$server_absolute_path_info.'users/','Пользователи');
}
$subkinds[]=Array(3,$server_absolute_path_info.'mg/','Мастерские группы');
$subkinds[]=Array(3,$server_absolute_path_info.'areas/','Полигоны');
$subkinds[]=Array(3,'block','Добавить<hr>изменить');
$subkinds[]=Array(3,$server_absolute_path_info.'myevents/','Мои события');
$subkinds[]=Array(3,$server_absolute_path_info.'myeventsgr/','Мои группы событий');
$subkinds[]=Array(3,$server_absolute_path_info.'myareas/','Мои полигоны');
if($_SESSION["user_id"]!='') {
	$subkinds[]=Array(3,$server_absolute_path_info.'myreports/','Мои отчеты');
}
if($_SESSION["candoarticles"]) {
	$subkinds[]=Array(3,$server_absolute_path_info.'myarticles/','Мои статьи');
}
if($_SESSION["user_id"]!='') {
	$subkinds[]=Array(3,$server_absolute_path_info.'mycomments/','Мои отзывы');
	$subkinds[]=Array(3,$server_absolute_path_info.'commentsaboutme/','Отзывы обо мне');
}

if(isset($_GET["site"])) {
	$getsite=encode($_GET["site"]);
}
else {
	$getsite='';
}

if($getsite=="exit") {
	setcookie("siteid", '', time()-20, '/', $cookiedomain);
	setcookie("sitename", '', time()-20, '/', $cookiedomain);
	if($cookiedomain==".allrpg.info") {
		setcookie("siteid", '', time()-20, '/', 'allrpg.info');
		setcookie("sitename", '', time()-20, '/', 'allrpg.info');
	}
	unset($_SESSION["siteid"]);
	unset($_SESSION["sitename"]);
}

if((encode($_COOKIE["siteid"])=='' && encode($_GET["site"])=="") || encode($_GET["site"])=="exit") {
	$thereisprojects=false;
	if($_SESSION["user_sid"]!='') {
		if($_SESSION["admin"] && $_SESSION["seeall"]) {
			$result=mysql_query("SELECT title, id FROM ".$prefix."sites WHERE status!=3 and datestart>'".date("Y-m-d")."' order by title asc");
		}
		else {
			$result=mysql_query("SELECT t2.title, t2.id FROM ".$prefix."allrights2 t1 LEFT JOIN ".$prefix."sites t2 ON t1.site_id=t2.id WHERE t2.status!=3 and t1.user_id=".$_SESSION['user_sid']." order by t2.title asc");
		}
		$subkinds[]=Array(1,'block','Мои активные проекты');
		while($a = mysql_fetch_array($result)) {
			$subkinds[]=Array(1,$server_absolute_path_site.'site='.$a["id"],decode($a["title"]));
			$thereisprojects=true;
		}
	}
}

if((encode($_COOKIE["siteid"])=='' && encode($_GET["site"])=="") || encode($_GET["site"])=="exit" || $kind=="hosting" || $kind=="hosting2" || $kind=="hosting3") {
	$subkinds[]=Array(1,'block','Создать');
	if($_SESSION["user_id"]!='') {
		
			$subkinds[]=Array(1,$server_absolute_path_site.'hosting2/','cистему заявок');
	
	}
	else {
		
			$subkinds[]=Array(1,$server_absolute_path.'register/redirectobj=hosting2','cистему заявок');
		
	}
}
elseif((($getsite>0 || encode($_COOKIE["siteid"])>0) && $getsite!='exit') && $_SESSION["user_sid"]!='') {
	unset($rights);
	if($getsite>0) {
		$site=$getsite;
	}
	elseif(encode($_COOKIE["siteid"])>0) {
		$site=encode($_COOKIE["siteid"]);
	}
	$rights[1]=false;
	$rights[2]=false;
	$rights[3]=false;
	$rights[4]=false;
	$result=mysql_query("SELECT * FROM ".$prefix."allrights2 where site_id=".encode($site)." and user_id=".$_SESSION["user_sid"]);
	while($a = mysql_fetch_array($result))
	{
		$rights[$a["rights"]]=true;
	}
	if($_SESSION["admin"]) {
		$rights[1]=true;
	}

	if(count($rights)>0) {
		setcookie("siteid", $site, time()+60*60*24*30, '/', $cookiedomain);
		$result=mysql_query("SELECT * from ".$prefix."sites where id=".$site);
		$a=mysql_fetch_array($result);
		setcookie("sitename", decode($a["title"]), time()+60*60*24*30, '/', $cookiedomain);
		$_SESSION['siteid']=$a["id"];
		$_SESSION['sitename']=decode($a["title"]);
		$_SESSION['siteway']=$a["path"];
		$_SESSION['sitestatus']=$a["status"];

		$indexexists=false;
		if($a["usetemp"]==0 || $a["usetemp"]==1) {
			if(file_exists($leadc1.decode($a["path"]).$leadc2.'index.php')) {
				$indexexists=true;
			}
		}

		$site_menus=array();

		if($rights[1] || $rights[4]) {
			$site_menus=Array(
				'block_1'=>'Настройки и функции',
				'settings'=>'Основные свойства',
				'allrights'=>'Права управления',
				'rolessetup'=>'Форма заявки',
				'news'=>'Новостная лента',
				'subscribe'=>'E-mail рассылка',
				'exit'=>'Выйти из проекта',
				'block_2'=>'Сайт',
				'pages'=>'Разделы и страницы',
				'files'=>'Файлы проекта',
				'design'=>'Дизайн',
				'groups'=>'Группы доступа',
				'comments'=>'Комментарии',
				'block_3'=>'Заявки',
				'orders'=>'Все заявки',
				'roleslinks'=>'Сюжеты и загрузы',
				'roles'=>'Сетка ролей',
				'locations'=>'Локации<br>(команды)',
				'docs'=>'Генератор аусвайсов',
				'export'=>'Экспорт',
				'block_4'=>'Отчеты',
				'gamereport' => 'Отчет по игрокам',
			);

			if($a["usetemp"]==2) {
				unset($site_menus['block_2']);
				unset($site_menus['files']);
				unset($site_menus['design']);
				unset($site_menus['pages']);
				unset($site_menus['comments']);
				unset($site_menus['groups']);
			}
			if(!$indexexists) {
				unset($site_menus['design']);
				unset($site_menus['pages']);
				unset($site_menus['comments']);
				unset($site_menus['groups']);
			}
			if($rights[4] && !$rights[1]) {
				unset($site_menus['allrights']);
				unset($site_menus['groups']);
				unset($site_menus['subscribe']);
				unset($site_menus['comments']);
				unset($site_menus['locations']);
				unset($site_menus['roles']);
				unset($site_menus['roleslinks']);
				unset($site_menus['orders']);
				unset($site_menus['docs']);
				unset($site_menus['export']);
				unset($site_menus['block_3']);
				//unset($site_menus['gamereport']);
			}
		}
		if($rights[2]) {
			$site_menus=array_merge($site_menus,Array(
				'orders'=>'Все заявки',
				'roleslinks'=>'Сюжеты и загрузы',
				'roles'=>'Сетка ролей',
				'locations'=>'Локации<br>(команды)',
				'exit'=>'Выйти из проекта',
			));
		}
		if($rights[3]) {
			$site_menus=array_merge($site_menus,Array(
				'news'=>'Новостная лента',
				'exit'=>'Выйти из проекта',
			));
		}
		if($a["usetemp"]==1) {
			unset($site_menus['block_3']);
			unset($site_menus['locations']);
			unset($site_menus['roles']);
			unset($site_menus['roleslinks']);
			unset($site_menus['rolessetup']);
			unset($site_menus['orders']);
			unset($site_menus['docs']);
			unset($site_menus['export']);
		}
	}

	$site_menus=array_unique($site_menus);

	foreach($site_menus as $key=>$value) {
		if(!preg_match('#block#',$key)) {
			$subkinds[]=Array(1,$server_absolute_path_site.$key.'/',$value);
			$workrights["site"][$key]=true;
		}
		else {
			$subkinds[]=Array(1,'block',$value);
		}
	}
}
?>