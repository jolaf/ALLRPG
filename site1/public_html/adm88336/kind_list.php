<?php

unset($theright);

$kinds[]=Array('start','новости');
$kinds[]=Array('users','пользователи');
$kinds[]=Array('orders','заявки на проекты');
$kinds[]=Array('projects','управление проектами');
$kinds[]=Array('projectsrights','права управления проектами');
$kinds[]=Array('templates','шаблоны дизайна проектов');
$kinds[]=Array('hr');
$kinds[]=Array('tags','теги');
$kinds[]=Array('articles','статьи');
$kinds[]=Array('geography','справочник географии');
$kinds[]=Array('gameworlds','справочник миров игр');
$kinds[]=Array('gametypes','справочник характеристик игр');
$kinds[]=Array('specializations','справочник специализаций');
$kinds[]=Array('areashave','справочник плюсов/минусов полигонов');
$kinds[]=Array('areas','все полигоны');
$kinds[]=Array('eventsgr','группы событий');
$kinds[]=Array('events','события и мастера');
$kinds[]=Array('usercomments','отзывы');
$kinds[]=Array('hr');
$kinds[]=Array('images','иконки блогов');
$kinds[]=Array('files','файлы на сайте');
$kinds[]=Array('banners','баннерная система');
$kinds[]=Array('updatesubs','обновление субдоменов');
$kinds[]=Array('errors','лог ошибок по таймеру');

if($allrights["admin"])
{
	$theright[$kinds[0][0]]=true;
	$theright[$kinds[1][0]]=true;
	$theright[$kinds[2][0]]=true;
	$theright[$kinds[3][0]]=true;
	$theright[$kinds[4][0]]=true;
	$theright[$kinds[5][0]]=true;
	$theright[$kinds[6][0]]=true;
	$theright[$kinds[7][0]]=true;
	$theright[$kinds[8][0]]=true;
	$theright[$kinds[9][0]]=true;
	$theright[$kinds[10][0]]=true;
	$theright[$kinds[11][0]]=true;
	$theright[$kinds[12][0]]=true;
	$theright[$kinds[13][0]]=true;
	$theright[$kinds[14][0]]=true;
	$theright[$kinds[15][0]]=true;
	$theright[$kinds[16][0]]=true;
	$theright[$kinds[17][0]]=true;
	$theright[$kinds[18][0]]=true;
	$theright[$kinds[19][0]]=true;
	$theright[$kinds[20][0]]=true;
	$theright[$kinds[21][0]]=true;
	$theright[$kinds[22][0]]=true;
	$theright[$kinds[23][0]]=true;
}
elseif($allrights["info"]) {
    $theright[$kinds[9][0]]=true;
    $theright[$kinds[14][0]]=true;
	$theright[$kinds[15][0]]=true;
	$theright[$kinds[16][0]]=true;
	if($kind=="start") {
		$kind="events";
	}
}
?>