<?php

if($object>0) {
	$id=$object;
}
if($id>0) {
	$pagetitle=h1line('События (игры)',$curdir.$kind.'/');
}

$gametypes=Array(
	Array(
		'name'	=>	"gametype",
		'sname'	=>	"Жанр",
		'type'	=>	"multiselect",
		'one'	=>	true,
		'values'	=>	make5field($prefix."gametypes where tipe=1","id","name"),
		'images'	=>	make5field($prefix."gametypes where tipe=1","id","im"),
		'path'	=>	$server_absolute_path.$uploads[6]['path'],
		'read'	=>	10,
		'write'	=>	100000,
	),
	Array(
		'name'	=>	"gametype2",
		'sname'	=>	"Тип",
		'type'	=>	"multiselect",
		'one'	=>	true,
		'values'	=>	make5field($prefix."gametypes where tipe=2","id","name"),
		'images'	=>	make5field($prefix."gametypes where tipe=2","id","im"),
		'path'	=>	$server_absolute_path.$uploads[6]['path'],
		'read'	=>	10,
		'write'	=>	100000,
	),
	Array(
		'name'	=>	"gametype4",
		'sname'	=>	"Дополнительно",
		'type'	=>	"multiselect",
		'values'	=>	make5field($prefix."gametypes where tipe=3","id","name"),
		'images'	=>	make5field($prefix."gametypes where tipe=3","id","im"),
		'path'	=>	$server_absolute_path.$uploads[6]['path'],
		'read'	=>	10,
		'write'	=>	100000,
	),
	Array(
		'name'	=>	"gametype3",
		'sname'	=>	"Мир",
		'type'	=>	"select",
		'one'	=>	true,
		'values'	=>	make5field($prefix."gameworlds","id","name"),
		'read'	=>	10,
		'write'	=>	100000,
	),
);

if($id>0) {
	$result=mysql_query("SELECT * FROM ".$prefix."allgames WHERE id=".$id." AND parent=0");
	$a_id = mysql_fetch_array($result);
}

if($id>0 && $a_id["id"]!='') {
	$b=getuser_sid($a_id['sid']);
	$allusers[]=array($b["sid"],usname($b,true,true));

	$kogdaigra="";
	if($a_id["kogdaigra_id"]!='') {
		$kogdaigra="<a href=\"http://kogda-igra.ru/game/".$a_id["kogdaigra_id"]."\" target=\"_blank\">http://kogda-igra.ru/game/".$a_id["kogdaigra_id"]."</a>";
	}
	else {
		$stres=implode(file("http://kogda-igra.ru/api/allrpg-info/".$a_id["id"]));
		if(strpos($stres,"profile_uri")!==false) {
			$stres=substr($stres,strpos($stres,"profile_uri")+14,strpos($stres,"}")-strpos($stres,"profile_uri")-14-1);
			$stres=str_replace('\/','/',$stres);
			$kogdaigra="<a href=\"http://kogda-igra.ru".$stres."\" target=\"_blank\">http://kogda-igra.ru".$stres."</a>";
			mysql_query("UPDATE ".$prefix."allgames SET kogdaigra_id=".substr($stres,6,strlen($stres))." WHERE id=".$a_id["id"]);
		}
	}

	$allgames_f=Array (
		Array(
			'name'	=>	"name",
			'sname'	=>	decode($a_id["name"]),
			'type'	=>	"h1",
			'read'	=>	10,
			'write'	=>	100,
		),
		Array(
			'name'	=>	"region",
			'sname'	=>	"Регион",
			'type'	=>	"sarissa",
			'parents'	=>	Array(Array('country','Страна')),
			'file'	=>	$helpers_path.'geo.php',
			'table'	=>	$prefix.'geography',
			'parent'	=>	'parent',
			'linkatbegin'	=>	'<a href="'.$server_absolute_path_info.'events/filter8=-{value}-">',
			'linkatend'	=>	'</a>',
			'read'	=>	10,
			'write'	=>	100,
			'width'	=>	200,
			'order'	=>	'name',
			'moreparams2'	=>	" and id!=2562 and parent!=2562",
			'mustbe'	=>	true
		),
		Array(
			'name'	=>	"mg",
			'sname'	=>	"Мастерская группа",
			'type'	=>	"text",
		),
		Array(
			'name'	=>	"datestart",
			'sname'	=>	"Дата начала",
			'type'	=>	"calendar",
		),
		Array(
			'name'	=>	"datefinish",
			'sname'	=>	"Дата окончания",
			'type'	=>	"calendar",
		),
		Array(
			'name'	=>	"site",
			'sname'	=>	"Сайт",
			'type'	=>	"text",
		),
		Array(
			'name'	=>	"orderpage",
			'sname'	=>	"Подать заявку",
			'type'	=>	"text",
		),
		Array(
			'name'	=>	"playernum",
			'sname'	=>	"Количество участников",
			'type'	=>	"number",
		),
		Array(
			'name'	=>	"descr",
			'sname'	=>	"Описание",
			'type'	=>	"wysiwyg",
		),
		Array(
			'name'	=>	"area",
			'sname'	=>	"Полигон",
			'type'	=>	"select",
			'values'	=>	make5field($prefix."areas","id","name"),
			'linkatbegin'	=>	'<a href="'.$server_absolute_path_info.'areas/{value}/">',
			'linkatend'	=>	'</a>',
		),
		Array(
			'name'	=>	"agroup",
			'sname'	=>	"Связанные события",
			'type'	=>	"text",
		),
		Array(
			'name'	=>	"gametype",
			'sname'	=>	"Жанр",
			'type'	=>	"multiselect",
			'one'	=>	true,
			'values'	=>	make5field($prefix."gametypes where tipe=1","id","name"),
			'images'	=>	make5field($prefix."gametypes where tipe=1","id","im"),
			'path'	=>	$server_absolute_path.$uploads[6]['path'],
			'linkatbegin'	=>	'<a href="'.$server_absolute_path_info.'events/filter2=-{value}-">',
			'linkatend'	=>	'</a>',
		),
		Array(
			'name'	=>	"gametype2",
			'sname'	=>	"Тип",
			'type'	=>	"multiselect",
			'one'	=>	true,
			'values'	=>	make5field($prefix."gametypes where tipe=2","id","name"),
			'images'	=>	make5field($prefix."gametypes where tipe=2","id","im"),
			'path'	=>	$server_absolute_path.$uploads[6]['path'],
			'linkatbegin'	=>	'<a href="'.$server_absolute_path_info.'events/filter3=-{value}-">',
			'linkatend'	=>	'</a>',
		),
		Array(
			'name'	=>	"gametype3",
			'sname'	=>	"Мир",
			'type'	=>	"select",
			'one'	=>	true,
			'values'	=>	make5field($prefix."gameworlds","id","name"),
			'linkatbegin'	=>	'<a href="'.$server_absolute_path_info.'events/filter5=-{value}-">',
			'linkatend'	=>	'</a>',
		),
		Array(
			'name'	=>	"gametype4",
			'sname'	=>	"Дополнительно",
			'type'	=>	"multiselect",
			'values'	=>	make5field($prefix."gametypes where tipe=3 order by name","id","name"),
			'images'	=>	make5field($prefix."gametypes where tipe=3 order by name","id","im"),
			'path'	=>	$server_absolute_path.$uploads[6]['path'],
			'linkatbegin'	=>	'<a href="'.$server_absolute_path_info.'events/filter4=-{value}-">',
			'linkatend'	=>	'</a>',
		),
		Array(
			'name'	=>	"kogdaigra",
			'sname'	=>	"Профиль события на <a href=\"http://kogda-igra.ru/\">kogda-igra.ru</a>",
			'type'	=>	"text",
			'default'	=>	$kogdaigra,
		),
		Array(
			'name'	=>	"datearrival",
			'sname'	=>	"Дата заезда",
			'type'	=>	"calendar",
		),
		Array(
			'name'	=>	"logo",
			'sname'	=>	"Логотип",
			'type'	=>	"file",
			'upload'	=>	10,
		),
		Array(
			'name'	=>	"sid",
			'sname'	=>	"Событием управляет",
			'type'	=>	"select",
			'values'	=>	$allusers,
		),
	);

	$allgames2_f=Array (
		Array(
			'name'	=>	"master",
			'sname'	=>	"Тип мастера",
			'type'	=>	"multiselect",
			'values'	=>	make5field($prefix."specializ where gr=2 OR gr=3 order by gr, name","id","name"),
			'images'	=>	make5field($prefix."specializ where gr=2 OR gr=3 order by gr, name","id","im"),
			'path'	=>	$server_absolute_path.$uploads[7]['path'],
		),
	);

	$allgames3_f=Array (
		Array(
			'name'	=>	"fio",
			'sname'	=>	"Ф.И.О.",
			'type'	=>	"text",
		),
		Array(
			'name'	=>	"nick",
			'sname'	=>	"Никнейм",
			'type'	=>	"text",
		),
	);

	$content2.='<div id="events_data">';
	$result2=mysql_query("SELECT * FROM ".$prefix."played WHERE game=".$id." AND user_id=".$_SESSION["user_id"]);
	$b = mysql_fetch_array($result2);
	if($b["id"]=='') {
		if($_SESSION["user_id"]=='') {
			if($a_id["datestart"] < date("Y-m-d")) {
				$additional_commands.='<a href="'.$server_absolute_path.'register/redirectobj=portfolio&redirectid=2&redirectparams=act:add*game:'.$id.'">Добавить в мое портфолио</a>';
			}
			else {
				$additional_commands.='<a href="'.$server_absolute_path.'register/redirectobj=portfolio&redirectid=1&redirectparams=act:add*game:'.$id.'">Добавить в мой календарь</a>';
			}
		}
		else {
			if($a_id["datestart"] < date("Y-m-d")) {
				$additional_commands.='<a href="'.$server_absolute_path_calendar.'portfolio/subobj=past&act=add&game='.$id.'">Добавить в мое портфолио</a>';
			}
			else {
				$additional_commands.='<a href="'.$server_absolute_path_calendar.'portfolio/subobj=future&act=add&game='.$id.'">Добавить в мой календарь</a>';
			}
		}
	}
	else {
		if($a_id["datestart"] < date("Y-m-d")) {
			$additional_commands.='<a href="'.$server_absolute_path_calendar.'portfolio/portfolio/'.$b["id"].'/act=view&subobj=past">Уже в моем портфолио</a>';
		}
		else {
			$additional_commands.='<a href="'.$server_absolute_path_calendar.'portfolio/portfolio/'.$b["id"].'/act=view&subobj=future">Уже в моем календаре</a>';
		}
	}
    if($a_id["datestart"]<=date("Y-m-d")) {
	    $result3=mysql_query("SELECT * FROM ".$prefix."reports where game=".$id." and user_id=".$_SESSION["user_id"]);
		$c = mysql_fetch_array($result3);
		if($c["id"]=='') {
			if($_SESSION["user_id"]=='') {
				$additional_commands.='<a href="'.$server_absolute_path.'register/redirectobj=myreports&redirectid='.$id.'">Написать отчет</a>';
			}
			else {
				$additional_commands.='<a href="'.$server_absolute_path_info.'myreports/act=add&game='.$id.'">Написать отчет</a>';
			}
		}
		else {
			$additional_commands.='<a href="'.$server_absolute_path_info.'reports/reports/'.$c["id"].'/act=view">Отчет написан</a>';
		}
	}
	elseif($a_id["orderpage"]!='') {
    	$additional_commands.='<a href="'.decode($a_id["orderpage"]).'">Подать заявку</a>';
	}
	if($_SESSION["user_id"]!='') {
		$additional_commands.='<a onClick="$(\'#addphotovideo\').toggle();">Добавить фото / видео</a>';
	}
	if($a_id["add_ip"]==get_real_ip() || $a_id["sid"]==$_SESSION["user_sid"] || $_SESSION["admin"] || $_SESSION["candoevents"]) {
		$additional_commands.='<a href="'.$server_absolute_path_info.'myevents/myevents/'.$a_id["id"].'/act=view">Править</a>';
	}
	if($_SESSION["user_id"]!='') {
		$addphotoerror=false;
		if($action=="addphoto") {
            if(encode($_POST["link"])!='' && encode($_POST["name"])!='') {
				$thumb=encode($_POST["thumb"]);
				$plink=encode($_POST["link"]);
				$name=encode($_POST["name"]);
				$author=encode($_POST["author"]);

				$result2=mysql_query("SELECT * from ".$prefix."allgames_gallery where user_id=".$_SESSION["user_id"]." and game_id=".$id." and link='".$plink."' and author='".$author."'");
				$b = mysql_fetch_array($result2);
				if($b["id"]=='' || encode($_POST["changeid"])!='') {
					if(strpos($plink,'vkontakte.ru')!==false && strpos($plink,'video')!==false && strpos($plink,'iframe')===false) {
						dynamic_err_one('error',"Чтобы вставить видео из ВКонтакте, войдите в нужное видео, нажмите «Поделиться - получить код видео» и скопируйте соответствующий код в поле «HTTP-адрес галереи / видео».");
					}
					elseif((strpos($plink,'youtube.com')!==false || strpos($plink,'youtu.be')!==false) && strpos($plink,'object')!==false) {
						dynamic_err_one('error',"Чтобы вставить видео из Youtube, просто скопируйте HTTP-адрес соответствующего видео.");
					}
					elseif(strpos($plink,'rutube.ru')!==false && strpos($plink,'object')!==false) {
						dynamic_err_one('error',"Чтобы вставить видео из Rutube.ru, просто скопируйте HTTP-адрес соответствующего видео.");
					}
					elseif(strpos($plink,'video.yandex.ru')!==false && strpos($plink,'object')===false) {
                        dynamic_err_one('error',"Чтобы вставить видео из Яндекс.видео, войдите в нужное видео, запустите его, нажмите «код для вставки», нажмите «скопировать» возле поля «Код для Я.ру и других блогов» и вставьте его в поле «HTTP-адрес галереи / видео».");
					}
					else {
						if($dynrequest==1) {
            				dynamic_err(array(),'submit');
            			}
						if(encode($_POST["changeid"])!='') {
							$result2=mysql_query("SELECT * from ".$prefix."allgames_gallery where id=".encode($_POST["changeid"]));
							$b = mysql_fetch_array($result2);
							if($b["id"]!='' && ($b["user_id"]==$_SESSION["user_id"] || $_SESSION["admin"])) {
								mysql_query("UPDATE ".$prefix."allgames_gallery set thumb='".$thumb."',name='".$name."',link='".$plink."',author='".$author."',date=".time()." where id=".$b["id"]);
								err("Материал успешно изменен.");
							}
						}
						else {
							mysql_query("INSERT into ".$prefix."allgames_gallery (user_id,game_id,thumb,name,link,author,date) VALUES (".$_SESSION["user_id"].",".$id.",'".$thumb."','".$name."','".$plink."','".$author."',".time().")");
							err("Материал успешно добавлен.");
						}
					}
				}
			}
			else {
				dynamic_err_one('error',"HTTP-адрес или подпись не указаны.");
			}
		}
		elseif($action=="changephoto") {
			$result2=mysql_query("SELECT * from ".$prefix."allgames_gallery where id=".encode($_GET["tochange"]));
			$b = mysql_fetch_array($result2);
			if($b["id"]!='' && ($b["user_id"]==$_SESSION["user_id"] || $_SESSION["admin"])) {
				$_POST["link"]=decode($b["link"]);
				$_POST["name"]=decode($b["name"]);
				$_POST["author"]=decode($b["author"]);
				$_POST["thumb"]=decode($b["thumb"]);
				$changeid=$b["id"];
				$addphotoerror=true;
			}
		}
		elseif($action=="deletephoto") {
			$result2=mysql_query("SELECT * from ".$prefix."allgames_gallery where id=".encode($_GET["todel"]));
			$b = mysql_fetch_array($result2);
			$result3=mysql_query("SELECT * from ".$prefix."allgames where id=".$b["game_id"]);
			$c = mysql_fetch_array($result3);
			if($b["user_id"]==$_SESSION["user_id"] || $c["user_id"]==$_SESSION["user_id"] || $_SESSION["admin"] || $_SESSION["candoevents"]) {
				if($dynrequest==1) {
            		dynamic_err(array(),'submit');
            	}
				mysql_query("DELETE FROM ".$prefix."allgames_gallery where id=".encode($_GET["todel"]));
				if(mysql_affected_rows($link)>0) {
					err("Материал успешно удален.");
				}
			}
		}

		$content2.='<div id="addphotovideo"'.($action=="changephoto"?' style="display: block;"':'').'>
<div style="float: right; margin-top: -1%;"><a onClick="$(\'#addphotovideo\').hide()"><b>[X]</b></a></div>
<form action="'.$curdir.$kind.'/'.$id.'/" method="POST">
<input type="hidden" name="action" value="addphoto">
<input type="hidden" name="changeid" value="'.$changeid.'">
<div class="fieldname" id="name_link">HTTP-адрес галереи / видео</div>
<div class="help" id="help_link">видео: Youtube, Rutube, Vkontakte, Яндекс.видео</div>
<div class="fieldvalue" id="div_link"><input type="text" name="link" class="inputtext mustbe" value="'.encode($_POST["link"]).'"></div>
<div class="fieldname" id="name_name">Подпись</div>
<div class="fieldvalue" id="div_name"><input type="text" name="name" class="inputtext mustbe" value="'.encode($_POST["name"]).'"></div>
<div class="fieldname" id="name_thumb">Превью-картинка</div>
<div class="help" id="help_thumb">только для галереи</div>
<div class="fieldvalue" id="div_thumb"><input type="text" name="thumb" class="inputtext" value="'.encode($_POST["thumb"]).'"></div>
<div class="fieldname" id="name_author">ИНП или ссылка на профиль в соц.сети автора</div>
<div class="fieldvalue" id="div_author"><input type="text" name="author" class="inputtext" value="'.encode($_POST["author"]).'"></div>
<center><button class="main" type="submit">';
		if($changeid!='') {
			$content2.='сохранить изменения';
		}
		else {
			$content2.='добавить';
		}
		$content2.='</button></center>
</form>
</div>';
	}

	$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."comments WHERE game=".$id." AND active='1'");
	$b = mysql_fetch_array($result2);
	$rating=0;
	$result3=mysql_query("SELECT * FROM ".$prefix."comments WHERE game=".$id);
	while($c = mysql_fetch_array($result3)) {
		$rating+=$c["rating"];
	}
	if($rating>0) {
		$rating='+'.$rating;
	}
	$content2.='<b>Рейтинг ('.$rating.')</b><br />
<a href="'.$server_absolute_path_info.'comments/'.$id.'/filter=event"><b>Отзывы по событию ('.$b[0].')</b></a>';

	if($a_id["datestart"]<=date("Y-m-d")) {
		$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."reports where game=".$id);
		$b = mysql_fetch_array($result2);
		if($b[0]>0) {
			$content2.='<br />
<a href="'.$server_absolute_path_info.'reports/action=dynamicindex&search_game['.$id.']=on"><b>Отчеты ('.$b[0].')</b></a>';
		}
		else {
			$content2.='<br />
<b>Отчеты ('.$b[0].')</b>';
		}
	}
	$content2.='<br><br>';
    $result2=mysql_query("SELECT * FROM ".$prefix."allgames_gallery WHERE game_id=".$id." ORDER BY date ASC");
	while($b = mysql_fetch_array($result2)) {
		$is_video=false;
		if((strpos($b["link"],'vkontakte.ru')!==false && strpos($b["link"],'video')!==false) || (strpos($b["link"],'youtube.com')!==false || strpos($b["link"],'youtu.be')!==false) || strpos($b["link"],'video.yandex.ru')!==false || strpos($b["link"],'rutube.ru')!==false) {
			$is_video=true;
		}
		$result3=mysql_query("SELECT * from ".$prefix."allgames where id=".$b["game_id"]);
		$c = mysql_fetch_array($result3);
		if($c["user_id"]=$_SESSION["user_id"] || $b["user_id"]=$_SESSION["user_id"] || $_SESSION["admin"] || $_SESSION["candoevents"]) {
			$content2.='<div style="position: absolute;"><a href="'.$curdir.$kind.'/'.$id.'/action=changephoto&tochange='.$b["id"].'" style="color: red; font-weight: bold; background-color: white;">[изменить]</a><a href="'.$curdir.$kind.'/'.$id.'/action=deletephoto&todel='.$b["id"].'" style="color: red; font-weight: bold;background-color: white;">[удалить]</a></div>';
		}
		if(!$is_video) {
			$content2.='<a href="'.decode($b["link"]).'" title="';
			$result3=mysql_query("SELECT * FROM ".$prefix."users where id=".$b["user_id"]);
			$c = mysql_fetch_array($result3);
			$content2.=usname($c,true).' '.date("d.m.Y в H:i",$b["date"]);
			$content2.='">';
			if($b["thumb"]!='') {
				$content2.='<img src="'.decode($b["thumb"]).'" style="border: 1px black solid; max-width: 100%;">';
			}
			$content2.='</a><br>';
		}
		else {
			if(strpos($b["link"],'vkontakte.ru')!==false && strpos($b["link"],'iframe')!==false) {
				$plink=decode($b["link"]);
				$plink=preg_replace("/width=\"[0-9]*\"/","width=\"250\"",$plink);
				$plink=preg_replace("/height=\"[0-9]*\" /","",$plink);
				$content2.=$plink.'<br>';
			}
			elseif((strpos($b["link"],'youtube.com')!==false || strpos($b["link"],'youtu.be')!==false) && strpos($b["link"],'object')===false) {
				$plink=decode($b["link"]);
				if(strpos($plink,'youtu.be')!==false) {
					preg_match('/youtu.be\/([^\?]*)/',$plink,$match);
					$plink=$match[1];
				}
				else {
					preg_match('/v=([^&]*)/',$plink,$match);
					$plink=$match[1];
					if($plink=='') {
						preg_match('/v\/([^&]*)/',$plink,$match);
						$plink=$match[1];
					}
				}
				if($plink!='') {
					$content2.='<object width="250"><param name="movie" value="http://www.youtube.com/v/'.$plink.'"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><param name="wmode" value="opaque"></param><embed src="http://www.youtube.com/v/'.$plink.'" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="250" wmode="opaque"></embed></object><br>';
				}
			}
			elseif(strpos($b["link"],'rutube.ru')!==false && strpos($b["link"],'object')===false) {
				$plink=decode($b["link"]);
				preg_match('/v=([^&]*)/',$plink,$match);
				$plink=$match[1];
				if($plink!='') {
					$content2.='<object width="250"><param name="movie" value="http://video.rutube.ru/'.$plink.'"></param><param name="wmode" value="opaque"></param><param name="allowFullScreen" value="true"></param><embed src="http://video.rutube.ru/'.$plink.'" type="application/x-shockwave-flash" wmode="opaque" allowfullscreen="true" width="250"></embed></object><br>';
				}
			}
			elseif(strpos($b["link"],'video.yandex.ru')!==false && strpos($b["link"],'object')!==false) {
				$plink=decode($b["link"]);
				$plink=preg_replace("/width=\"[0-9]*\"/","width=\"250\"",$plink);
				$plink=preg_replace("/height=\"[0-9]*\" /","",$plink);
				$content2.=$plink.'<br>';
			}
		}
        if($b["name"]!='' && !$is_video) {
        	$content2.='<a href="'.decode($b["link"]).'">';
        }
        $content2.='<span>'.decode($b["name"]).'</span>';
        if($b["name"]!='' && !$is_video) {
        	$content2.='</a>';
        }
        $content2.='<br>';
        if($b["author"]!='') {
        	if(is_Numeric(decode($b["author"]))) {
        		$result3=mysql_query("SELECT * FROM ".$prefix."users where sid=".decode($b["author"]));
				$c = mysql_fetch_array($result3);
				if($c["id"]!='') {
					$content2.=usname($c,true,true).'<br>';
				}
				else {
        			$content2.=decode($b["author"]).'<br>';
        		}
        	}
        	else {
        		$content2.=social2(decode($b["author"]),'',true).'<br>';
        	}
        }
		$content2.='<br>';
	}
	$content2.='</div>';
	if($a_id["wascancelled"]=='1') {
		err_red('Событие было отменено.');
	}
	if($a_id["moved"]=='1') {
		err_red('Событие перенесено. Новые даты не определены.');
	}

    // движок регистрации
	$act="view";

	// Создание объекта
	$obj=new netObj(
		'allgames',
		$prefix."allgames",
		"",
		Array(),
		Array(),
		2,
		'100%',
		50
	);

	// Создание схемы прав объекта
	$obj_r=new netRight(
		true,
		false,
		false,
		false,
		100,
		'id='.$id,
		'id='.$id,
		''
	);
	$obj->setRight($obj_r);

	for($i=0;$i<count($allgames_f);$i++) {
		$objer='obj_'.$i;
		$$objer=createElem($allgames_f[$i]);
		$obj->setElem($$objer);
		$$objer->setHelp('');
	}

	$a_id["site"]=str_replace('http://','',$a_id["site"]);
	$obj_5->setLinkAtBegin('<a href="http://'.decode($a_id["site"]).'" target="_blank" style="word-wrap: break-word; overflow: hidden; display: block; max-width: 60%;">');
	$obj_5->setLinkAtEnd('</a>');

	$a_id["orderpage"]=str_replace('http://','',$a_id["orderpage"]);
	$obj_6->setLinkAtBegin('<a href="');
	$obj_6->setLinkAtEnd('" target="_blank" style="word-wrap: break-word; oveflow: hidden;">здесь</a>');

	$mgval='';
	$hisgroups=explode(',',decode($a_id["mg"]));
	$hisgroups2=Array();
	for($j=0;$j<count($hisgroups);$j++) {
		if(substr($hisgroups[$j],0,1)==' ') {
			$hisgroups[$j]=substr($hisgroups[$j],1,strlen($hisgroups[$j]));
		}
		$hisgroups2[$j]=str_replace('&','-and-',$hisgroups[$j]);
		$mgval.='<a href="'.$server_absolute_path_info.'mg/'.$hisgroups2[$j].'/">'.$hisgroups[$j].'</a>';
		if($j<count($hisgroups)-1) {
			$mgval.=', ';
		}
	}
	$content2.=$obj->draw();
	$content2=str_replace('id="div_mg">'.decode($a_id["mg"]),'id="div_mg">'.$mgval,$content2);
	if(decode($a_id["agroup"])!=0 && decode($a_id["agroup"])!='') {
		$result2=mysql_query("SELECT * FROM ".$prefix."allgames WHERE agroup=".decode($a_id["agroup"])." AND id!=".$id." AND agroup!=0");
		while($b = mysql_fetch_array($result2)) {
			$agroup.='<a href="'.$curdir.$kind.'/'.$b["id"].'/">'.decode($b["name"]).'</a>, ';
		}
		$agroup=substr($agroup,0,strlen($agroup)-2);
		$content2=str_replace('id="div_agroup">'.decode($a_id["agroup"]),'id="div_agroup">'.$agroup,$content2);
	}
	else {
		$content2=preg_replace('#<div class="fieldname" id="name_agroup"(.*?)<br \/>#','',$content2);
	}

	$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."allgames WHERE parent=".$id." ORDER BY name");
	$a = mysql_fetch_array($result);
	if($a[0]>0) {
        $v=$allgames2_f[0];
        $i=0;
		$result=mysql_query("SELECT * FROM ".$prefix."allgames WHERE parent=".$id." ORDER BY name");
		while($a = mysql_fetch_array($result)) {
			if($a["user_id"]!=0) {
				$b=getuser_sid($a["user_id"]);
				$allmasters[$i]=Array(usname($b),$b["sid"]);
				$result3=mysql_query("SELECT COUNT(id) FROM ".$prefix."comments WHERE whom=".$b["id"]." AND active='1'");
				$c = mysql_fetch_array($result3);
				$allmasters[$i][3]=$b["id"];
				$allmasters[$i][4]=$c[0];

				$result3=mysql_query("SELECT * FROM ".$prefix."allgames WHERE id=".$id);
				$c = mysql_fetch_array($result3);
				if($c["datestart"]<=date("Y-m-d")) {
					$result3=mysql_query("SELECT * FROM ".$prefix."reports WHERE game=".$id." AND user_id=".$b["id"]);
					$c = mysql_fetch_array($result3);
					if($c["id"]!='') {
						$allmasters[$i][5]=$c["id"];
					}
				}
			}
			elseif($a["name"]!='') {
				$allmasters[$i]=array(decode($a["name"]),0);
			}
			$allmasters[$i][2]=$a[$v["name"]];
			$i++;
		}
		foreach ($allmasters as $key => $row) {
			$allmasterssort[$key]  = strtolower($row[0]);
		}
		array_multisort($allmasterssort, SORT_ASC, $allmasters);

		if(count($allmasters)>0) {
			$content2.='<h1>Мастера ('.count($allmasters).')</h1>';
			$content2.='<div class="narrow">';
			$content2.='<table class="menutable" style="width: auto; min-width: 68%;"><tr class="menu"><td style="width: 50%">Мастер</td><td>Тип</td><td>Отзывы</td>';
			if($a_id["datestart"]<=date("Y-m-d")) {
				$content2.='<td>Отчет мастера</td>';
			}
			$content2.='</tr>';

			$j=0;
			for($i=0;$i<count($allmasters);$i++) {
				$content2.='<tr class="';
				if($j%2==0) {
					$content2.='string1';
				}
				else {
					$content2.='string2';
				}
				$j++;
				$content2.='"><td>';
				if($_SESSION["user_id"]!='' && $allmasters[$i][1]>0) {
					$content2.='<a href="'.$server_absolute_path_info.'users/'.$allmasters[$i][1].'/">'.$allmasters[$i][0].'</a>';
				}
				else {
					$content2.=$allmasters[$i][0];
				}
				$content2.='</td><td>';
				for($t=0;$t<count($v["values"]);$t++) {
					if(eregi('-'.$v["values"][$t][0].'-',$allmasters[$i][2])) {
						$content2.=' <img src="'.$v['path'].$v["images"][$t][1].'" title="'.$v["values"][$t][1].'" /> ';
					}
				}
				$content2.='</td><td>';
				if($allmasters[$i][3]!='') {
					$content2.='<nobr><a href="'.$server_absolute_path_info.'comments/'.$allmasters[$i][3].'/filter=person">Отзывы ('.$allmasters[$i][4].')</a></nobr>';
				}
				else {
					$content2.='&nbsp;';
				}
				$content2.='</td>';
				if($a_id["datestart"]<=date("Y-m-d")) {
					if($allmasters[$i][5]!='') {
						$content2.='<td><a href="'.$server_absolute_path_info.'reports/reports/'.$allmasters[$i][5].'/act=view">см. здесь</a></td>';
					}
					else {
						$content2.='<td>&nbsp;</td>';
					}
				}
				$content2.='</tr>';
			}
			$content2.='</table>';
			$content2.='</div>';
		}
	}

	$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."played WHERE game=".$id." AND active='1'");
	$a = mysql_fetch_array($result);
	if($a[0]>0) {
        $i=0;
		$result=mysql_query("SELECT * FROM ".$prefix."played WHERE game=".$id." AND active='1'");
		while($a = mysql_fetch_array($result)) {
			$b = getuser($a['user_id']);
			$result3=mysql_query("SELECT * FROM ".$prefix."allgames WHERE parent=".$id." AND user_id=".$b["sid"]);
			$c = mysql_fetch_array($result3);
			if($c["id"]=='') {
				$allplayers[$i]=array(usname($b),$b["sid"]);

				if($a["specializ2"]!='' && $a["specializ2"]!='-') {
					$allplayers[$i][2]=1;
				}
				elseif($a["specializ3"]!='' && $a["specializ3"]!='-') {
					$allplayers[$i][2]=2;
				}
				else {
					$allplayers[$i][2]=3;
				}

				$result3=mysql_query("SELECT COUNT(id) FROM ".$prefix."comments WHERE whom=".$b["id"]." AND active='1'");
				$c = mysql_fetch_array($result3);
				$allplayers[$i][3]=$b["id"];
				$allplayers[$i][4]=$c[0];

				if($a_id["datestart"]<=date("Y-m-d")) {
					$result3=mysql_query("SELECT * FROM ".$prefix."reports WHERE game=".$id." AND user_id=".$b["id"]);
					$c = mysql_fetch_array($result3);
					if($c["id"]!='') {
						$allplayers[$i][5]=$c["id"];
					}
				}

				$i++;
			}
		}
		foreach ($allplayers as $key => $row) {
			$allplayerssort[$key]  = strtolower($row[0]);
			$allplayerssort2[$key]  = $row[2];
		}
		array_multisort($allplayerssort2, SORT_ASC, $allplayerssort, SORT_ASC, $allplayers);

		if(count($allplayers)>0) {
			$content2.='<h1>Участники ('.count($allplayers).')</h1>';
			$content2.='<div class="narrow">';
			$content2.='<table class="menutable" style="width: auto; min-width: 68%;"><tr class="menu"><td style="width: 50%">Пользователь allrpg.info</td><td>Тип</td><td>Отзывы</td>';
			if($a_id["datestart"]<=date("Y-m-d")) {
				$content2.='<td>Отчет</td>';
			}
			$content2.='</tr>';

			$j=0;
			for($i=0;$i<count($allplayers);$i++) {
				$content2.='<tr class="';
				if($j%2==0) {
					$content2.='string1';
				}
				else {
					$content2.='string2';
				}
				$j++;
				$content2.='"><td>';
				if($_SESSION["user_id"]!='' && $allplayers[$i][1]>0) {
					$content2.='<a href="'.$server_absolute_path_info.'users/'.$allplayers[$i][1].'/">'.$allplayers[$i][0].'</a>';
				}
				else {
					$content2.=$allplayers[$i][0];
				}
				$content2.='</td><td>';
				if($allplayers[$i][2]==1) {
	            	$content2.='мастер';
				}
				elseif($allplayers[$i][2]==2) {
	            	$content2.='полигонщик';
				}
				elseif($allplayers[$i][2]==3) {
	            	$content2.='игрок';
				}
				$content2.='</td><td>';
				if($allplayers[$i][3]!='') {
					$content2.='<nobr><a href="'.$server_absolute_path_info.'comments/'.$allplayers[$i][3].'/filter=person">Отзывы ('.$allplayers[$i][4].')</a></nobr>';
				}
				else {
					$content2.='&nbsp;';
				}
				$content2.='</td>';
				if($a_id["datestart"]<=date("Y-m-d")) {
					if($allplayers[$i][5]!='') {
						$content2.='<td><a href="'.$server_absolute_path_info.'reports/reports/'.$allplayers[$i][5].'/act=view" target="_blank">см. здесь</a></td>';
					}
					else {
						$content2.='<td>&nbsp;</td>';
					}
				}
				$content2.='</tr>';
			}
			$content2.='</table>';
			$content2.='</div>';
		}
	}
	$content2=preg_replace('#<div class="clear"><\/div>#','',$content2);
	$content2.='<div class="clear"></div>';
}
else {
	if(date("m")>=10) {
		if($filter6=='01.01.'.date("Y") && $filter7=='31.12.'.date("Y") && encode($_REQUEST["filter6"])=='' && encode($_REQUEST["filter7"])=='' && encode($_REQUEST["wholeyear"])=='') {
			$filter6='01.01.'.(date("Y")+1);
			$filter7='31.12.'.(date("Y")+1);
			$october=true;
		}
	}

	if(((is_array($filter2) && count($filter2)>0) || $filter2!='') || ((is_array($filter3) && count($filter3)>0) || $filter3!='') || ((is_array($filter4) && count($filter4)>0) || $filter4!='') || ((is_array($filter5) && count($filter5)>0) || $filter5!='') || ((is_array($filter8) && count($filter8)>0) || $filter8!='') || ((($filter6!='01.01.'.date("Y") || $filter7!='31.12.'.date("Y")) && !$october) || (($filter6!='01.01.'.(date("Y")+1) || $filter7!='31.12.'.(date("Y")+1)) && $october))) {
		$filters=true;
		if($action=="dynamicindex" && $dynrequest==1) {
			dynamic_err(array(),'submit');
		}
	}
	elseif($action=="dynamicindex" && $dynrequest==1) {
		dynamic_err_one('error','Фильтры не определены!');
	}

	$selecter2=createElem(Array(
		'name'	=>	"filter2",
		'sname'	=>	"Жанр",
		'type'	=>	"multiselect",
		'values'	=>	make5field($prefix."gametypes where tipe=1 order by name","id","name"),
		'default'	=>	$filter2,
		'read'	=>	10,
		'write'	=>	10,
		)
	);
	if(encode($_POST["filter2"])!='') {
		$selecter2->setVal('',$_POST);
	}
	else {
		$selecter2->setVal('',$_GET);
	}
	$selecter3=createElem(Array(
		'name'	=>	"filter3",
		'sname'	=>	"Тип",
		'type'	=>	"multiselect",
		'values'	=>	make5field($prefix."gametypes where tipe=2 order by name","id","name"),
		'default'	=>	$filter3,
		'read'	=>	10,
		'write'	=>	10,
		)
	);
	if(encode($_POST["filter3"])!='') {
		$selecter3->setVal('',$_POST);
	}
	else {
		$selecter3->setVal('',$_GET);
	}
	$selecter4=createElem(Array(
		'name'	=>	"filter5",
		'sname'	=>	"Мир",
		'type'	=>	"multiselect",
		'values'	=>	make5field($prefix."gameworlds order by name","id","name"),
		'default'	=>	$filter5,
		'read'	=>	10,
		'write'	=>	10,
		)
	);
	if(encode($_POST["filter5"])!='') {
		$selecter4->setVal('',$_POST);
	}
	else {
		$selecter4->setVal('',$_GET);
	}
	$selecter5=createElem(Array(
		'name'	=>	"filter4",
		'sname'	=>	"Дополнительно",
		'type'	=>	"multiselect",
		'values'	=>	make5field($prefix."gametypes where tipe=3 order by name","id","name"),
		'default'	=>	$filter4,
		'read'	=>	10,
		'write'	=>	10,
		)
	);
	if(encode($_POST["filter4"])!='') {
		$selecter5->setVal('',$_POST);
	}
	else {
		$selecter5->setVal('',$_GET);
	}
	$selecter6=createElem(Array(
		'name'	=>	"filter6",
		'sname'	=>	"С",
		'type'	=>	"calendar",
		'default'	=>	$filter6,
		'read'	=>	10,
		'write'	=>	10,
		)
	);
	$selecter7=createElem(Array(
		'name'	=>	"filter7",
		'sname'	=>	"По",
		'type'	=>	"calendar",
		'default'	=>	$filter7,
		'read'	=>	10,
		'write'	=>	10,
		)
	);
	$selecter8=createElem(Array(
		'name'	=>	"filter8",
		'sname'	=>	"Регион",
		'type'	=>	"multiselect",
		'values'	=>	make5field($prefix."geography where id in (SELECT distinct region from ".$prefix."allgames) order by name","id","name"),
		'default'	=>	$filter8,
		'cols'	=>	2,
		'read'	=>	10,
		'write'	=>	10,
		)
	);
	if(encode($_POST["filter8"])!='') {
		$selecter8->setVal('',$_POST);
	}
	else {
		$selecter8->setVal('',$_GET);
	}

    $pagetitle='События (игры)';
	if(substr($filter6,0,5)=='01.01' && substr($filter7,0,5)=='31.12' && substr($filter6,6,strlen($filter6))==substr($filter7,6,strlen($filter7))) {
		$pagetitle.=' '.substr($filter6,6,strlen($filter6)).' года';
	}
	$pagetitle=h1line($pagetitle,$curdir.$kind.'/');

	$content2.='<div class="indexer">
<div id="filters_events" style="'.($filters?'':'display: none;').'">
<form action="'.$curdir.$kind.'/" method="post" enctype="multipart/form-data">
<input type="hidden" name="action" value="dynamicindex">
<table class="menutable searchtable">
<tr>
<td><b>С</b>:<br>'.$selecter6->draw(2,'write').'</td>
<td><b>По</b>:<br>'.$selecter7->draw(2,'write').'</td>
<td colspan=2 width="64%"><b>Регион</b>:<br>'.$selecter8->draw(2,'write').'</td>
</tr>
<tr>
<td width="25%">
<b>Жанр</b>:<br>'.$selecter2->draw(2,"write").'
</td>
<td width="25%">
<b>Тип</b>:<br>'.$selecter3->draw(2,"write").'
</td>
<td width="25%">
<b>Мир</b>:<br>'.$selecter4->draw(2,"write").'
</td>
<td width="25%">
<b>Дополнительно</b>:<br>'.$selecter5->draw(2,"write").'
</td>
</tr>
</table>

<table class="controls"><tr><td><button class="nonimportant" onClick="document.location=\''.$curdir.$kind.'/\'">очистить фильтр</button></td><td><div class="filters_'.($filters?'on':'off').'">'.($filters?'Внимание! Используются фильтры.':'Фильтр на год.').'</div></td><td><button class="main">отфильтровать</button></td></tr></table></form><br></div></div>';

	if($filter2!='' || $filter3!='' || $filter4!='' || $filter5!='' || $filter6!='' || $filter7!='' || $filter8!='') {
		$more=true;

		if($filter2!='') {
			if($more) {
				$query.=' AND ';
			}
			$filter2decode=$selecter2->getVal();
			$filter2decode=substr($filter2decode,1,strlen($filter2decode)-2);
			$filter2decode2=explode("-", $filter2decode);
			$query.='(';
			for($i=0;$i<count($filter2decode2);$i++) {
				$query.="gametype LIKE '%-".$filter2decode2[$i]."-%' OR ";
			}
			$query=substr($query,0,strlen($query)-4);
			$query.=')';
			$more=true;
		}

		if($filter3!='') {
			if($more) {
				$query.=' AND ';
			}
			$filter3decode=$selecter3->getVal();
			$filter3decode=substr($filter3decode,1,strlen($filter3decode)-2);
			$filter3decode2=explode("-", $filter3decode);
			$query.='(';
			for($i=0;$i<count($filter3decode2);$i++) {
				$query.="gametype2 LIKE '%-".$filter3decode2[$i]."-%' OR ";
			}
			$query=substr($query,0,strlen($query)-4);
			$query.=')';
			$more=true;
		}

		if($filter4!='') {
			if($more) {
				$query.=' AND ';
			}
			$filter4decode=$selecter5->getVal();
			$filter4decode=substr($filter4decode,1,strlen($filter4decode)-2);
			$filter4decode2=explode("-", $filter4decode);
			$query.='(';
			for($i=0;$i<count($filter4decode2);$i++) {
				$query.="gametype4 LIKE '%-".$filter4decode2[$i]."-%' OR ";
			}
			$query=substr($query,0,strlen($query)-4);
			$query.=')';
			$more=true;
		}

		if($filter5!='') {
			if($more) {
				$query.=' AND ';
			}
			$filter5decode=$selecter4->getVal();
			$filter5decode=substr($filter5decode,1,strlen($filter5decode)-2);
			$filter5decode2=explode("-", $filter5decode);
			$query.='(';
			for($i=0;$i<count($filter5decode2);$i++) {
				$query.="gametype3=".$filter5decode2[$i]." OR ";
			}
			$query=substr($query,0,strlen($query)-4);
			$query.=')';
			$more=true;
		}

		if($filter8!=0) {
			if($more) {
				$query.=' AND ';
			}
			$filter8decode=$selecter8->getVal();
			$filter8decode=substr($filter8decode,1,strlen($filter8decode));
			$filter8decode=str_replace('-',', ',$filter8decode);
			$filter8decode=substr($filter8decode,0,strlen($filter8decode)-2);
			$query.="region IN (".$filter8decode.")";
			$more=true;
		}

		$querytosend=$query;

		if($filter6!='' || $filter7!='') {
			if($more) {
				$query.=' AND ';
			}

			$query.="((datestart <= '".date("Y-m-d",strtotime($filter6))."' AND datefinish >= '".date("Y-m-d",strtotime($filter6))."') OR (datestart <= '".date("Y-m-d",strtotime($filter7))."' AND datefinish >= '".date("Y-m-d",strtotime($filter7))."') OR (datestart > '".date("Y-m-d",strtotime($filter6))."' AND datefinish < '".date("Y-m-d",strtotime($filter7))."'))";
			$more=true;
		}

		if($more) {
			$query.=' ';
		}
	}
	$content2.='
<center>
<div class="cb_editor">

<h3 id="showfilters_events" '.($filters?'style="display: none;" ':'').'class="ctrlink2"><a onClick="$(\'#filters_events\').toggle(); $(\'#hidefilters_events\').toggle(); $(\'#showfilters_events\').toggle();">показать фильтры</a></h3>
<h3 id="hidefilters_events" '.($filters?'':'style="display: none;" ').'class="ctrlink2"><a onClick="$(\'#filters_events\').toggle(); $(\'#showfilters_events\').toggle(); $(\'#hidefilters_events\').toggle();">скрыть фильтры</a></h3>

<div class="clear"></div><hr>

<table class="menutable">
<tr class="menu">
<td>
';
	if($sorting==0) {
		$sorting=3;
	}

	if($sorting==1) {
		$content2.='<a href="'.$curdir.$kind.'/filter2='.$selecter2->getVal().'&filter3='.$selecter3->getVal().'&filter4='.$selecter5->getVal().'&filter5='.$selecter4->getVal().'&filter6='.$filter6.'&filter7='.$filter7.'&filter8='.$selecter8->getVal().'&sorting=2" title="[сортировать : название/сайт/полигон : по убыванию]" class="arrow_up">Название/сайт/полигон</a>';
	}
	elseif($sorting==2) {
		$content2.='<a href="'.$curdir.$kind.'/filter2='.$selecter2->getVal().'&filter3='.$selecter3->getVal().'&filter4='.$selecter5->getVal().'&filter5='.$selecter4->getVal().'&filter6='.$filter6.'&filter7='.$filter7.'&filter8='.$selecter8->getVal().'&sorting=1" title="[сортировать : название/сайт/полигон : по возрастанию]" class="arrow_down">Название/сайт/полигон</a>';
	}
	else {
		$content2.='<a href="'.$curdir.$kind.'/filter2='.$selecter2->getVal().'&filter3='.$selecter3->getVal().'&filter4='.$selecter5->getVal().'&filter5='.$selecter4->getVal().'&filter6='.$filter6.'&filter7='.$filter7.'&filter8='.$selecter8->getVal().'&sorting=2" title="[сортировать : название/сайт/полигон : по убыванию]">Название/сайт/полигон</a>';
	}
	$content2.='
</td>
<td>
';
	if($sorting==3) {
		$content2.='<a href="'.$curdir.$kind.'/filter2='.$selecter2->getVal().'&filter3='.$selecter3->getVal().'&filter4='.$selecter5->getVal().'&filter5='.$selecter4->getVal().'&filter6='.$filter6.'&filter7='.$filter7.'&filter8='.$selecter8->getVal().'&sorting=4" title="[сортировать : даты : по убыванию]" class="arrow_up">Даты</a>';
	}
	elseif($sorting==4) {
		$content2.='<a href="'.$curdir.$kind.'/filter2='.$selecter2->getVal().'&filter3='.$selecter3->getVal().'&filter4='.$selecter5->getVal().'&filter5='.$selecter4->getVal().'&filter6='.$filter6.'&filter7='.$filter7.'&filter8='.$selecter8->getVal().'&sorting=3" title="[сортировать : даты : по возрастанию]" class="arrow_down">Даты</a>';
	}
	else {
		$content2.='<a href="'.$curdir.$kind.'/filter2='.$selecter2->getVal().'&filter3='.$selecter3->getVal().'&filter4='.$selecter5->getVal().'&filter5='.$selecter4->getVal().'&filter6='.$filter6.'&filter7='.$filter7.'&filter8='.$selecter8->getVal().'&sorting=4" title="[сортировать : даты : по убыванию]">Даты</a>';
	}
	$content2.='
</td>
<td style="width: 30%;">
Мастерская группа
</td>
<td>
Игроки
</td>
<td>
Прочее
</td>
</tr>';

	if($sorting==1)
	{
		$order='name ASC';
	}
	elseif($sorting==2)
	{
		$order='name DESC';
	}
	elseif($sorting==3)
	{
		$order='datestart ASC';
	}
	elseif($sorting==4)
	{
		$order='datestart DESC';
	}

	//$bazecount=$_SESSION["bazecount"];
	//if($bazecount=='') {
		$bazecount=5000;
	//}
	$start=$page*$bazecount;
	$query2="SELECT COUNT(id) FROM ".$prefix."allgames WHERE parent=0".$query;
	$query="SELECT * FROM ".$prefix."allgames where parent=0 ".$query."order by ".$order." limit ".$start.", ".$bazecount;
	$stringnum=1;

	unset($allgames);
	$result=mysql_query($query);
	while($a=mysql_fetch_array($result)) {
		$allgames[]=$a;
	}

	if($sorting==1 || $sorting==2) {
		foreach ($allgames as $key => $row) {
			$eventname[$key]  = $row['name'];
		}
		if($sorting==1) {
			array_multisort($eventname, SORT_ASC, $allgames);
		}
		elseif($sorting==2) {
			array_multisort($eventname, SORT_DESC, $allgames);
		}
	}
	else {
		foreach ($allgames as $key => $row) {
			$datestart[$key]  = $row['datestart'];
		}
		if($sorting==3) {
			array_multisort($datestart, SORT_ASC, $allgames);
		}
		else {
			array_multisort($datestart, SORT_DESC, $allgames);
		}
	}

	for($i=0;$i<count($allgames);$i++) {
		$a=$allgames[$i];

		$content2.='<tr';
		if($stringnum%2==1) {
			$content2.=' class="string1';
		}
		else {
			$content2.=' class="string2';
		}
		if($a["area"]!='') {
			$result3=mysql_query("SELECT * FROM ".$prefix."played where game=".$a["id"]." and user_id=".$_SESSION["user_id"]);
			$c = mysql_fetch_array($result3);
			if($c["id"]!='') {
				if($c["specializ2"]!='' && $c["specializ2"]!='-') {
					$content2.=' master';
				}
				elseif($c["specializ3"]!='' && $c["specializ3"]!='-') {
					$content2.=' poligon';
				}
				else {
					$content2.=' play';
				}
			}
		}
		else {
			$content2.=' event';
		}
		$content2.='"';
        if(date("m",strtotime($a["datestart"]))<date("m",strtotime($allgames[$i+1]["datestart"])) && ($sorting==3 || $sorting==4)) {
        	$content2.=' style="border-bottom: 0.2em rgb(0,0,160) solid;"';
        }
		$content2.='>
<td>';
		$content2.='<b><a href="'.$server_absolute_path_info.'events/'.$a["id"].'/">'.decode($a["name"]).'</a></b><br>';
		if($a["site"]!='') {
			$content2.='<a href="'.$a["site"].'" target="_blank">'.substr($a["site"],0,40);
			if(strlen($a["site"])>40) {
				$content2.='...';
			}
			$content2.='</a><br>';
		}
		if($a["area"]!='') {
			$result2=mysql_query("SELECT * FROM ".$prefix."areas where id=".$a["area"]);
			$b = mysql_fetch_array($result2);
			$content2.='<a href="'.$server_absolute_path_info.'areas/'.$b["id"].'/">'.decode($b["name"]).'</a>';
		}
		$content2.='
</td>
';
		$content2.='<td>
'.datesfmake($a["datestart"],$a["datefinish"]).'
</td>
<td>';
		if($a["area"]!='') {
			$hisgroups=explode(',',$a["mg"]);
			for($j=0;$j<count($hisgroups);$j++)
			{
				if(substr($hisgroups[$j],0,1)==' ')
				{
					$hisgroups[$j]=substr($hisgroups[$j],1,strlen($hisgroups[$j]));
				}
				if($j<count($hisgroups)-1)
				{
					$content2.='<a href="'.$server_absolute_path_info.'mg/'.str_replace('&','-and-',$hisgroups[$j]).'/">'.$hisgroups[$j].'</a>, ';
				}
				else
				{
					$content2.='<a href="'.$server_absolute_path_info.'mg/'.str_replace('&','-and-',$hisgroups[$j]).'/">'.$hisgroups[$j].'</a>';
				}
			}
		}
		else {
			$content2.='&nbsp;';
		}
		$content2.='
</td>
<td>
'.$a["playernum"].'
</td>
<td>';
		if($a["area"]!='') {
			$result4=mysql_query("SELECT COUNT(id) FROM ".$prefix."comments where game=".$a["id"]." and active='1'");
			$d = mysql_fetch_array($result4);
			$rating=0;
			$result3=mysql_query("SELECT * FROM ".$prefix."comments where game=".$a["id"]);
			while($c = mysql_fetch_array($result3))
			{
				$rating+=$c["rating"];
			}
			if($rating>0)
			{
				$rating='+'.$rating;
			}
			$content2.='Рейтинг: '.$rating.'<br><nobr><a href="'.$server_absolute_path_info.'comments/'.$a["id"].'/filter=event">Отзывы ('.$d[0].')</a></nobr>';
			if($a["datestart"]<=date("Y-m-d"))
			{
				$result4=mysql_query("SELECT COUNT(id) FROM ".$prefix."reports where game=".$a["id"]);
				$d = mysql_fetch_array($result4);
				$content2.='<br><nobr><a href="'.$server_absolute_path_info.'reports/action=dynamicindex&search_game['.$id.']=on">Отчеты ('.$d[0].')</a></nobr>';
			}
			$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."allgames_gallery where game_id=".$a["id"]);
			$b = mysql_fetch_array($result2);
			if($b[0]>0) {
				$content2.='<br><nobr><a href="'.$server_absolute_path_info.'events/'.$a["id"].'/">Галереи ('.$b[0].')</a></nobr>';
			}
		}
		else {
			$content2.='<br><br><br>';
		}
		$content2.='</td>
</tr>
';
		$stringnum++;
	}
	$result=mysql_query($query2);
	$a=mysql_fetch_array($result);
	$count=$a[0];
	$content2.='</table></div><br>';

	if($count>$bazecount) {
		$content2.=pagecount('',$count,$bazecount,'filter2='.$selecter2->getVal().'&filter3='.$selecter3->getVal().'&filter4='.$selecter5->getVal().'&filter5='.$selecter4->getVal().'&filter6='.$filter6.'&filter7='.$filter7.'&filter8='.$selecter8->getVal());
	}
}

?>