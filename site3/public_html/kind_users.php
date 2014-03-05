<?php

if($_SESSION["user_id"]!='') {

	if($object>0) {
		$id=$object;
	}
	$pagetitle=h1line('Участники allrpg.info',$curdir.$kind.'/');

	if($id>0) {
		$result=mysql_query("SELECT * from ".$prefix."users where sid=".$id);
		$a=mysql_fetch_array($result);
		$id=$a["id"];
	}

	if($id>0 && $a["id"]!='') {
		if($a["hidesome"]!='') {
			if(eregi('-1-',$a["hidesome"])) {
				$hidesome['photo']=100000;
			}
			else {
				$hidesome['photo']=100;
			}
			if(eregi('-2-',$a["hidesome"])) {
				$hidesome['em']=100000;
			}
			else {
				$hidesome['em']=100;
			}
			if(eregi('-3-',$a["hidesome"])) {
				$hidesome['em2']=100000;
			}
			else {
				$hidesome['em2']=100;
			}
			if(eregi('-5-',$a["hidesome"])) {
				$hidesome['phone2']=100000;
			}
			else {
				$hidesome['phone2']=100;
			}
			if(eregi('-6-',$a["hidesome"])) {
				$hidesome['icq']=100000;
			}
			else {
				$hidesome['icq']=100;
			}
			if(eregi('-7-',$a["hidesome"])) {
				$hidesome['skype']=100000;
			}
			else {
				$hidesome['skype']=100;
			}
			if(eregi('-8-',$a["hidesome"])) {
				$hidesome['jabber']=100000;
			}
			else {
				$hidesome['jabber']=100;
			}
		}

		$birthread=10;
		$cityread=10;
		if($a["birth"]=='0000-00-00') {
			$birthread=100000;
		}
		if($a["city"]=='0') {
			$cityread=100000;
		}

		$users_f=Array (
			Array(
				'sname'	=>	usname($a),
				'type'	=>	"h1",
				'name'	=>	"fio",
				'read'	=>	10,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"sid",
				'sname'	=>	"ИНП",
				'type'	=>	"text",
				'default'	=>	$a["sid"],
				'read'	=>	10,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"gender",
				'sname'	=>	"Пол",
				'type'	=>	"select",
				'values'	=>	Array(Array('1','мужской'),Array('2','женский')),
				'default'	=>	$a["gender"],
				'read'	=>	100,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"em",
				'sname'	=>	"E-mail",
				'type'	=>	"email",
				'default'	=>	$a["em"],
				'read'	=>	$hidesome['em'],
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"em2",
				'sname'	=>	"Дополнительный е-mail",
				'type'	=>	"email",
				'default'	=>	$a["em2"],
				'read'	=>	$hidesome['em2'],
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"phone2",
				'sname'	=>	"Контактный телефон",
				'type'	=>	"text",
				'default'	=>	$a["phone2"],
				'read'	=>	$hidesome['phone2'],
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"icq",
				'sname'	=>	"ICQ",
				'type'	=>	"text",
				'default'	=>	$a["icq"],
				'read'	=>	$hidesome['icq'],
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"skype",
				'sname'	=>	"Skype",
				'type'	=>	"text",
				'default'	=>	$a["skype"],
				'read'	=>	$hidesome['skype'],
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"jabber",
				'sname'	=>	"Jabber",
				'type'	=>	"text",
				'default'	=>	$a["jabber"],
				'read'	=>	$hidesome['jabber'],
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"vkontakte",
				'sname'	=>	"ВКонтакте",
				'type'	=>	"text",
				'read'	=>	100,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"livejournal",
				'sname'	=>	"Живой Журнал",
				'type'	=>	"text",
				'read'	=>	100,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"facebook",
				'sname'	=>	"Facebook",
				'type'	=>	"text",
				'read'	=>	100,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"tweeter",
				'sname'	=>	"Twitter",
				'type'	=>	"text",
				'read'	=>	100,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"googleplus",
				'sname'	=>	"Google+",
				'type'	=>	"text",
				'read'	=>	100,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"photo",
				'sname'	=>	"Фотография",
				'type'	=>	"file",
				'upload'	=>	4,
				'default'	=>	$a["photo"],
				'read'	=>	$hidesome['photo'],
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"birth",
				'sname'	=>	"Дата рождения",
				'type'	=>	"calendar",
				'default'	=>	date("d.m.Y", strtotime($a["birth"])),
				'read'	=>	$birthread,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"city",
				'sname'	=>	"Город",
				'type'	=>	"sarissa",
				'parents'	=>	Array(Array('country','Страна'),Array('region','Регион')),
				'file'	=>	$helpers_path.'geo.php',
				'table'	=>	$prefix.'geography',
				'parent'	=>	'parent',
				'default'	=>	$a["city"],
				'read'	=>	$cityread,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"ingroup",
				'sname'	=>	"Состоит в МГ",
				'type'	=>	"text",
				'default'	=>	$a["ingroup"],
				'read'	=>	100,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"prefer",
				'sname'	=>	"Предпочитаемые жанры игр",
				'type'	=>	"multiselect",
				'values'	=>	make5field($prefix."gametypes where tipe=1 order by name","id","name"),
				'images'	=>	make5field($prefix."gametypes where tipe=1 order by name","id","im"),
				'path'	=>	$server_absolute_path.$uploads[6]['path'],
				'default'	=>	$a["prefer"],
				'linkatbegin'	=>	'<a href="'.$server_absolute_path_info.'events/filter2=-{value}-">',
				'linkatend'	=>	'</a>',
				'read'	=>	100,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"prefer2",
				'sname'	=>	"Предпочитаемые типы игр",
				'type'	=>	"multiselect",
				'values'	=>	make5field($prefix."gametypes where tipe=2 order by name","id","name"),
				'images'	=>	make5field($prefix."gametypes where tipe=2 order by name","id","im"),
				'path'	=>	$server_absolute_path.$uploads[6]['path'],
				'default'	=>	$a["prefer2"],
				'linkatbegin'	=>	'<a href="'.$server_absolute_path_info.'events/filter3=-{value}-">',
				'linkatend'	=>	'</a>',
				'read'	=>	100,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"prefer3",
				'sname'	=>	"Предпочитаемые миры игр",
				'type'	=>	"multiselect",
				'values'	=>	make5field($prefix."gameworlds order by name","id","name"),
				'default'	=>	$a["prefer3"],
				'linkatbegin'	=>	'<a href="'.$server_absolute_path_info.'users/filter3=-{value}-">',
				'linkatend'	=>	'</a>',
				'read'	=>	100,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"prefer4",
				'sname'	=>	"Дополнительные предпочтения",
				'type'	=>	"multiselect",
				'values'	=>	make5field($prefix."gametypes where tipe=3 order by name","id","name"),
				'images'	=>	make5field($prefix."gametypes where tipe=3 order by name","id","im"),
				'path'	=>	$server_absolute_path.$uploads[6]['path'],
				'default'	=>	$a["prefer4"],
				'read'	=>	100,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"specializ",
				'sname'	=>	"Основная специализация на играх",
				'type'	=>	"multiselect",
				'values'	=>	make5field($prefix."specializ where gr=1 order by name","id","name"),
				'images'	=>	make5field($prefix."specializ where gr=1 order by name","id","im"),
				'path'	=>	$server_absolute_path.$uploads[7]['path'],
				'default'	=>	$a["specializ"],
				'read'	=>	100,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"additional",
				'sname'	=>	"Дополнительная информация",
				'type'	=>	"textarea",
				'default'	=>	$a["additional"],
				'read'	=>	100,
				'write'	=>	100000,
			),
		);

		$content2.='<div style="float: right; text-align: right;">';
		if($_SESSION["user_id"]==$a["id"]) {
			$additional_commands.='<a href="'.$server_absolute_path.'profile/">Править</a><a href="'.$server_absolute_path_calendar.'portfolio/subobj=past">Настроить портфолио</a><a href="'.$server_absolute_path_calendar.'portfolio/subobj=future">Настроить календарь</a>';
		}
		elseif($_SESSION["user_id"]!='') {
			$additional_commands.='<a href="'.$server_absolute_path.'outbox/act=add&user_id='.$a["id"].'">Отправить личное сообщение</a>';
		}
		else {
			$additional_commands.='<a href="'.$server_absolute_path.'register/redirectobj=pmswrite&redirectparams=user_id:'.$a["id"].'">Отправить личное сообщение</a>';
		}

		$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."comments where whom=".$a["id"]." and active='1'");
		$b = mysql_fetch_array($result2);
		$result4=mysql_query("SELECT COUNT(id) FROM ".$prefix."reports where user_id=".$a["id"]);
		$d = mysql_fetch_array($result4);
		$content2.='<a href="'.$server_absolute_path_info.'comments/'.$a["id"].'/filter=person"><b>Отзывы по пользователю ('.$b[0].')</b></a>';
		if($d[0]>0) {
			$content2.='<br />
<a href="'.$server_absolute_path_info.'reports/action=dynamicindex&search_user_id['.$id.']=on"><b>Отчеты пользователя ('.$d[0].')</b></a>';
		}
		if($_SESSION["user_id"]==$a["id"]) {
			$content2.='<br />
<a href="'.$server_absolute_path.'profile/"><b>Редактировать профиль</b></a>';
		}
		$content2.='</div>';

		// движок
		$act="view";

		// Создание объекта
		$obj=new netObj(
			'users',
			$prefix."users",
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

		for($i=0;$i<count($users_f);$i++) {
			$objer='obj_'.$i;
			$$objer=createElem($users_f[$i]);
			$obj->setElem($$objer);
			$$objer->setHelp('');
		}

		if($a["city"]>0) {
			$result3=mysql_query("SELECT * FROM ".$prefix."geography WHERE id=".$a["city"]);
			$d=mysql_fetch_array($result3);
			$obj_16->setLinkAtBegin ('<a href="'.$server_absolute_path_info.'users/filter8=-'.$d["parent"].'-">');
			$obj_16->setLinkAtEnd('</a>');
		}

		$mgval='';
		$hisgroups=explode(',',decode($a["ingroup"]));
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
		for($i=9;$i<14;$i++) {
			$objer='obj_'.$i;
			$content2=str_replace('id="div_'.$$objer->getName().'">'.$a[$$objer->getName()],'id="div_'.$$objer->getName().'">'.social2($a[$$objer->getName()],$$objer->getName(),"pic"),$content2);
		}
		$content2=str_replace(decode($a["ingroup"]),$mgval,$content2);
		$content2=str_replace(decode($a["birth"]),date("d.m.Y", strtotime($a["birth"])),$content2);

		$content2.='
';

		$portfolio1_f=Array (
			Array(
				'name'	=>	"game",
				'sname'	=>	"Игра",
				'type'	=>	"select",
				'read'	=>	10,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"datestart",
				'sname'	=>	"Дата начала",
				'type'	=>	"select",
				'read'	=>	10,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"role",
				'sname'	=>	"Роль",
				'type'	=>	"text",
				'read'	=>	10,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"locat",
				'sname'	=>	"Локация",
				'type'	=>	"text",
				'read'	=>	10,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"specializ",
				'sname'	=>	"Специализация",
				'type'	=>	"multiselect",
				'values'	=>	make5field($prefix."specializ where gr=1 order by name","id","name"),
				'images'	=>	make5field($prefix."specializ where gr=1 order by name","id","im"),
				'path'	=>	$server_absolute_path.$uploads[7]['path'],
				'read'	=>	10,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"photo",
				'sname'	=>	"Ссылка на фото",
				'type'	=>	"text",
				'read'	=>	10,
				'write'	=>	100000,
			),
		);

		$result4=mysql_query("SELECT COUNT(p.id) FROM ".$prefix."played as p, ".$prefix."allgames as a where p.active='1' and p.user_id=".$id." and ((p.specializ!='' and p.specializ!='-') OR (p.specializ='-' AND p.specializ2='-' AND p.specializ3='-')) and p.game=a.id order by a.datestart asc");
		$d = mysql_fetch_array($result4);
		if($d[0]>0 || $_SESSION["user_id"]==$id) {
			$content2.='<h1>Портфолио как игрока</h1>
<table class="menutable">
<tr class="menu">
<td>
Событие
</td>
<td>
Даты
</td>
<td>
Роль
</td>
<td>
Локация
</td>
<td>
Специализация
</td>
<td>
Фото
</td>
</tr>';
		}

		$i=0;
		$result=mysql_query("SELECT *,p.id as pid FROM ".$prefix."played as p, ".$prefix."allgames as a where p.active='1' and p.user_id=".$id." and ((p.specializ!='' and p.specializ!='-') OR (p.specializ='-' AND p.specializ2='-' AND p.specializ3='-')) and p.game=a.id order by a.datestart asc");
		while($a = mysql_fetch_array($result))
		{
			$content2.='<tr class="';
            if($i%2==0) {
            	$content2.='string1';
            }
            else {
            	$content2.='string2';
            }
			$content2.='">';
			$i++;
			foreach($portfolio1_f as $f=>$v)
			{
				if($v["read"]<=100)
				{
					$content2.='<td>';
					if($v["name"]=='game')
					{
						$content2.='<a href="'.$server_absolute_path_info.'events/'.$a[$v["name"]].'/">'.find5field($prefix."allgames","id",$a[$v["name"]],"name").'</a>';
						if($_SESSION["user_id"]==$id) {
							$content2.='<br><a href="'.$server_absolute_path_calendar.'portfolio/portfolio/'.$a["pid"].'/act=view" style="font-size: 8pt;">[редактировать]</a>';
						}
					}
					elseif($v["name"]=='datestart')
					{
						$content2.=datesfmake($a["datestart"],$a["datefinish"],false);
					}
					elseif($v["name"]=='specializ')
					{
						for($t=0;$t<count($v["values"]);$t++)
						{
							if(eregi('-'.$v["values"][$t][0].'-',$a[$v["name"]]))
							{
								$content2.='<nobr><img src="'.$v['path'].$v["images"][$t][1].'"> '.$v["values"][$t][1].'</nobr><br>';
							}
						}
					}
					elseif($v["name"]=='photo')
					{
						if($a[$v["name"]]!='')
						{
							$content2.='<a href="'.decode($a[$v["name"]]).'">фото</a>';
						}
						elseif($_SESSION["user_id"]==$id) {
							$content2.='<center><a href="'.$server_absolute_path_calendar.'portfolio/portfolio/'.$a["pid"].'/act=view"><b>+</b></a></center>';
						}
					}
					else
					{
						$content2.=decode($a[$v["name"]]);
					}
					$content2.='</td>';
				}
			}
			$content2.='</tr>';
		}

		if($d[0]>0 || $_SESSION["user_id"]==$id) {
			if($_SESSION["user_id"]==$id) {
				$content2.='<tr><td colspan=6><center><button href="'.$server_absolute_path_calendar.'portfolio/act=add&type=1">Добавить</button></center></td></tr>';
			}
			$content2.='</table>';
		}

		$portfolio1_f=Array (
			Array(
				'name'	=>	"game",
				'sname'	=>	"Игра",
				'type'	=>	"select",
				'values'	=>	make5field($prefix."allgames where parent=0 order by name","id","name"),
				'read'	=>	10,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"datestart",
				'sname'	=>	"Дата начала",
				'type'	=>	"select",
				'read'	=>	10,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"specializ2",
				'sname'	=>	"Специализация",
				'type'	=>	"multiselect",
				'values'	=>	make5field($prefix."specializ where gr=2 order by name","id","name"),
				'images'	=>	make5field($prefix."specializ where gr=2 order by name","id","im"),
				'path'	=>	$server_absolute_path.$uploads[7]['path'],
				'read'	=>	10,
				'write'	=>	100000,
			),
		);

		$result4=mysql_query("SELECT COUNT(p.id) FROM ".$prefix."played as p, ".$prefix."allgames as a where p.active='1' and p.user_id=".$id." and p.specializ2!='' and p.specializ2!='-' and p.game=a.id order by a.datestart asc");
		$d = mysql_fetch_array($result4);
		if($d[0]>0 || $_SESSION["user_id"]==$id) {
			$content2.='<h1>Портфолио как мастера</h1>
<table class="menutable">
<tr class="menu">
<td>
Событие
</td>
<td>
Даты
</td>
<td>
Специализация
</td>
</tr>';
		}

		$i=0;
		$result=mysql_query("SELECT *,p.id as pid FROM ".$prefix."played as p, ".$prefix."allgames as a where p.active='1' and p.user_id=".$id." and p.specializ2!='' and p.specializ2!='-' and p.game=a.id order by a.datestart asc");
		while($a = mysql_fetch_array($result))
		{
			$content2.='<tr class="';
            if($i%2==0) {
            	$content2.='string1';
            }
            else {
            	$content2.='string2';
            }
			$content2.='">';
			$i++;
			foreach($portfolio1_f as $f=>$v)
			{
				if($v["read"]<=100)
				{
					$content2.='<td>';
					if($v["name"]=='game')
					{
						$content2.='<a href="'.$server_absolute_path_info.'events/'.$a[$v["name"]].'/">'.find5field($prefix."allgames","id",$a[$v["name"]],"name").'</a>';
						if($_SESSION["user_id"]==$id) {
							$content2.='<br><a href="'.$server_absolute_path_calendar.'portfolio/portfolio/'.$a["pid"].'/act=view" style="font-size: 8pt;">[редактировать]</a>';
						}
					}
					elseif($v["name"]=='datestart')
					{
						$content2.=datesfmake($a["datestart"],$a["datefinish"],false);
					}
					elseif($v["name"]=='specializ2')
					{
						for($t=0;$t<count($v["values"]);$t++)
						{
							if(eregi('-'.$v["values"][$t][0].'-',$a[$v["name"]]))
							{
								$content2.='<nobr><img src="'.$v['path'].$v["images"][$t][1].'"> '.$v["values"][$t][1].'</nobr><br>';
							}
						}
					}
					$content2.='</td>';
				}
			}
			$content2.='</tr>';
		}

		if($d[0]>0 || $_SESSION["user_id"]==$id) {
			if($_SESSION["user_id"]==$id) {
				$content2.='<tr><td colspan=6><center><button href="'.$server_absolute_path_calendar.'portfolio/act=add&type=2">Добавить</button></center></td></tr>';
			}
			$content2.='</table>';
		}

		$portfolio1_f=Array (
			Array(
				'name'	=>	"game",
				'sname'	=>	"Игра",
				'type'	=>	"select",
				'values'	=>	make5field($prefix."allgames where parent=0 order by name","id","name"),
				'read'	=>	10,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"datestart",
				'sname'	=>	"Дата начала",
				'type'	=>	"select",
				'read'	=>	10,
				'write'	=>	100000,
			),
			Array(
				'name'	=>	"specializ3",
				'sname'	=>	"Специализация",
				'type'	=>	"multiselect",
				'values'	=>	make5field($prefix."specializ where gr=3 order by name","id","name"),
				'images'	=>	make5field($prefix."specializ where gr=3 order by name","id","im"),
				'path'	=>	$server_absolute_path.$uploads[7]['path'],
				'read'	=>	10,
				'write'	=>	100000,
			),
		);

		$result4=mysql_query("SELECT COUNT(p.id) FROM ".$prefix."played as p, ".$prefix."allgames as a where p.active='1' and p.user_id=".$id." and p.specializ3!='' and p.specializ3!='-' and p.game=a.id order by a.datestart asc");
		$d = mysql_fetch_array($result4);
		if($d[0]>0 || $_SESSION["user_id"]==$id) {
			$content2.='<h1>Портфолио как полигонщика</h1>
<table class="menutable">
<tr class="menu">
<td>
Событие
</td>
<td>
Даты
</td>
<td>
Специализация
</td>
</tr>';
		}

		$i=0;
		$result=mysql_query("SELECT *,p.id as pid FROM ".$prefix."played as p, ".$prefix."allgames as a where p.active='1' and p.user_id=".$id." and p.specializ3!='' and p.specializ3!='-' and p.game=a.id order by a.datestart asc");
		while($a = mysql_fetch_array($result))
		{
			$content2.='<tr class="';
            if($i%2==0) {
            	$content2.='string1';
            }
            else {
            	$content2.='string2';
            }
			$content2.='">';
			$i++;
			foreach($portfolio1_f as $f=>$v)
			{
				if($v["read"]<=100)
				{
					$content2.='<td>';
					if($v["name"]=='game')
					{
						$content2.='<a href="'.$server_absolute_path_info.'events/'.$a[$v["name"]].'/">'.find5field($prefix."allgames","id",$a[$v["name"]],"name").'</a>';
						if($_SESSION["user_id"]==$id) {
							$content2.='<br><a href="'.$server_absolute_path_calendar.'portfolio/portfolio/'.$a["pid"].'/act=view" style="font-size: 8pt;">[редактировать]</a>';
						}
					}
					elseif($v["name"]=='datestart')
					{
						$content2.=datesfmake($a["datestart"],$a["datefinish"],false);
					}
					elseif($v["name"]=='specializ3')
					{
						for($t=0;$t<count($v["values"]);$t++)
						{
							if(eregi('-'.$v["values"][$t][0].'-',$a[$v["name"]]))
							{
								$content2.='<nobr><img src="'.$v['path'].$v["images"][$t][1].'"> '.$v["values"][$t][1].'</nobr><br>';
							}
						}
					}
					$content2.='</td>';
				}
			}
			$content2.='</tr>';
		}

		if($d[0]>0 || $_SESSION["user_id"]==$id) {
			if($_SESSION["user_id"]==$id) {
				$content2.='<tr><td colspan=6><center><button href="'.$server_absolute_path_calendar.'portfolio/act=add&type=3">Добавить</a></button></center></td></tr>';
			}
			$content2.='</table>';
		}
	}
	else {
        if($filter!='' || $filter2!='' || ((is_array($filter3) && count($filter3)>0) || $filter3!='') || ($filter4>0 && $filter5!='') || ((is_array($filter8) && count($filter8)>0) || $filter8!='')) {
			$filters=true;
			if($action=="dynamicindex" && $dynrequest==1) {
				dynamic_err(array(),'submit');
			}
		}
		elseif($action=="dynamicindex" && $dynrequest==1) {
			dynamic_err_one('error','Фильтры не определены!');
		}

        $selecter1=createElem(Array(
			'name'	=>	"filter",
			'sname'	=>	"Ф.И.О.",
			'type'	=>	"text",
			'default'	=>	$filter,
			'read'	=>	10,
			'write'	=>	10,
			)
		);
		if(encode($_GET["filter"])!='') {
			$filter=$filter;
		}
		elseif(encode($_POST["filter"])!='') {
			$selecter1->setVal('',$_POST);
		}

		$selecter2=createElem(Array(
			'name'	=>	"filter2",
			'sname'	=>	"Никнейм",
			'type'	=>	"text",
			'default'	=>	$filter2,
			'read'	=>	10,
			'write'	=>	10,
			)
		);
		if(encode($_GET["filter2"])!='') {
			$filter2=$filter2;
		}
		elseif(encode($_POST["filter2"])!='') {
			$selecter2->setVal('',$_POST);
		}

		$selecter3=createElem(Array(
				'name'	=>	"filter3",
				'sname'	=>	"Предпочитаемые миры игр",
				'type'	=>	"multiselect",
				'values'	=>	make5field($prefix."gameworlds order by name","id","name"),
				'cols'	=>	2,
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

		if($filter5>0 && ($filter4<1 || $filter4>4)) {
			$_POST["filter4"]=1;
			$_GET["filter4"]=1;
			$filter4=1;
		}

		$selecter4=createElem(Array(
				'name'	=>	'filter4',
				'sname'	=>	'ИНП',
				'type'	=>	"select",
				'default'	=>	$filter4,
				'read'	=>	10,
				'write'	=>	10,
				'values'	=>	Array(Array('1','='),Array('2','<>'),Array('3','>'),Array('4','<')),
				'width'	=>	'35%'
			)
		);
		if(encode($_POST["filter4"])!='') {
			$selecter4->setVal('',$_POST);
		}
		else {
			$selecter4->setVal('',$_GET);
		}

		$selecter5=createElem(Array(
			'name'	=>	"filter5",
			'sname'	=>	"ИНП",
			'type'	=>	"number",
			'default'	=>	$filter5,
			'read'	=>	10,
			'write'	=>	10,
			)
		);
		if(encode($_POST["filter5"])!='') {
			$selecter5->setVal('',$_POST);
		}
		else {
			$selecter5->setVal('',$_GET);
		}

		$selecter8=createElem(Array(
			'name'	=>	"filter8",
			'sname'	=>	"Регион",
			'type'	=>	"multiselect",
			'values'	=>	make5field($prefix."geography where id in (SELECT distinct parent from ".$prefix."geography where id in (SELECT distinct city from ".$prefix."users)) order by name","id","name"),
			'cols'	=>	2,
			'default'	=>	$filter8,
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

		$content2.='<div class="indexer">
<div id="filters_users" style="'.($filters?'':'display: none;').'">
<form action="'.$curdir.$kind.'/" method="post" enctype="multipart/form-data" id="filter_form">
<input type="hidden" name="action" value="dynamicindex">
<table class="menutable searchtable">
<tr>
<td>
<b>ИНП</b>:<br>'.$selecter4->draw(2,"write").' '.$selecter5->draw(2,"write").'
</td>
<td>
<b>Никнейм</b>:<br>'.$selecter2->draw(2,"write").'
</td>
<td style="width: 28%;">
<b>Ф.И.О.</b>:<br>'.$selecter1->draw(2,"write").'
</td>
</tr>
<tr>
<td colspan=2>
<b>Регион</b>:<br>'.$selecter8->draw(2,'write').'
</td>
<td>
<b>Предпочитаемые миры игр</b>:<br>'.$selecter3->draw(2,"write").'
</td>
</tr>
</table>

<table class="controls"><tr><td><button class="nonimportant" onClick="document.location=\''.$curdir.$kind.'/\'">очистить фильтр</button></td><td><div class="filters_'.($filters?'on':'off').'">'.($filters?'Внимание! Используются фильтры.':'Фильтры не используются.').'</div></td><td><button class="main">отфильтровать</button></td></tr></table></form><br></div></div>
';
		$query='';
		$more=true;
		if($filter!='' || $filter2!='' || $filter3!='' || $filter4!='' || $filter5>0 || $filter8!='') {
			if($filter!='') {
				if($more) {
					$query.=' AND ';
				}

				$query.="fio LIKE '%".$filter."%' AND hidesome NOT LIKE '%-10-%'";
				$more=true;
			}

			if($filter2!='') {
				if($more) {
					$query.=' AND ';
				}

				$query.="nick LIKE '%".$filter2."%' AND hidesome NOT LIKE '%-0-%'";
				$more=true;
			}

			if($filter3!=0) {
				if($more) {
				$query.=' AND ';
			}
			$filter3decode=$selecter3->getVal();
			$filter3decode=substr($filter3decode,1,strlen($filter3decode)-2);
			$filter3decode2=explode("-", $filter3decode);
			$query.='(';
			for($i=0;$i<count($filter3decode2);$i++) {
				$query.="prefer3 LIKE '%-".$filter3decode2[$i]."-%' OR ";
			}
			$query=substr($query,0,strlen($query)-4);
			$query.=')';
			$more=true;
			}

			if($filter5>0) {
				if($more) {
					$query.=' AND ';
				}

				$query.="sid";
				if($filter4==1) {
					$query.='=';
				}
				elseif($filter4==2) {
					$query.='!=';
				}
				elseif($filter4==3) {
					$query.='>';
				}
				elseif($filter4==4) {
					$query.='<';
				}
				$query.=$filter5;
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
				$query.="city IN (select id from ".$prefix."geography where parent IN (".$filter8decode."))";
				$more=true;
			}
		}

		if($sorting==0) {
			$sorting=1;
		}

		if($sorting==1) {
			$order='sid DESC';
		}
		elseif($sorting==2) {
			$order='sid ASC';
		}

	    $content2.='
<center>
<div class="cb_editor">

<h3 id="showfilters_users" '.($filters?'style="display: none;" ':'').'class="ctrlink2"><a onClick="$(\'#filters_users\').toggle(); $(\'#hidefilters_users\').toggle(); $(\'#showfilters_users\').toggle();">показать фильтры</a></h3>
<h3 id="hidefilters_users" '.($filters?'':'style="display: none;" ').'class="ctrlink2"><a onClick="$(\'#filters_users\').toggle(); $(\'#showfilters_users\').toggle(); $(\'#hidefilters_users\').toggle();">скрыть фильтры</a></h3>

<div class="clear"></div><hr>

<table class="menutable">
<tr class="menu">
<td>
Фото
</td>
<td>
Ф.И.О.
</td>
<td>
';
		if($sorting==2) {
			$content2.='<a href="'.$curdir.$kind.'/filter='.$selecter1->getVal().'&filter2='.$selecter2->getVal().'&filter3='.$selecter3->getVal().'&filter4='.$selecter4->getVal().'&filter5='.$selecter5->getVal().'&filter8='.$selecter8->getVal().'&sorting=1" title="[сортировать : инп : по убыванию]" class="arrow_up">ИНП</a>';
		}
		elseif($sorting==1) {
			$content2.='<a href="'.$curdir.$kind.'/filter='.$selecter1->getVal().'&filter2='.$selecter2->getVal().'&filter3='.$selecter3->getVal().'&filter4='.$selecter4->getVal().'&filter5='.$selecter5->getVal().'&filter8='.$selecter8->getVal().'&sorting=2" title="[сортировать : инп : по возрастанию]" class="arrow_down">ИНП</a>';
		}
		else {
			$content2.='<a href="'.$curdir.$kind.'/filter='.$selecter1->getVal().'&filter2='.$selecter2->getVal().'&filter3='.$selecter3->getVal().'&filter4='.$selecter4->getVal().'&filter5='.$selecter5->getVal().'&filter8='.$selecter8->getVal().'&sorting=2" title="[сортировать : инп : по возрастанию]">ИНП</a>';
		}
		$content2.='
	</td>
<td>
Никнейм
</td>
<td>
Прочее
</td>
</tr>';

		$bazecount=$_SESSION["bazecount"];
		if($bazecount=='') {
			$bazecount=50;
		}
		$start=$page*$bazecount;
		$query2="SELECT COUNT(id) FROM ".$prefix."users WHERE sid!=0".$query;
		$query="SELECT * FROM ".$prefix."users WHERE sid!=0".$query." order by ".$order." LIMIT ".$start.", ".$bazecount;
		$stringnum=1;
		$result=mysql_query($query);
		while($a = mysql_fetch_array($result)) {
			$content2.='<tr';
			if($stringnum%2==1) {
				$content2.=' class="string1"';
			}
			else {
				$content2.=' class="string2"';
			}
			$content2.='><td>';
			if(strpos($a["hidesome"],'-1-')===false && decode($a["photo"])!='') {
				$content2.='<img src="'.$server_absolute_path.$uploads[4]['path'].$a["photo"].'" height=35 border=0>';
			}
			else {
				$content2.='<img src="'.$server_absolute_path.'identicon.php?hash='.md5(md5($a["em"]).'cetb').'&size=35" height=35>';
			}
			$content2.='</td><td>';
			if(strpos($a["hidesome"],'-10-')===false && decode($a["fio"])!='') {
				$content2.='<a href="'.$server_absolute_path_info.'users/'.$a["sid"].'/">'.decode($a["fio"]).'</a>';
			}
			else {
				$content2.='<a href="'.$server_absolute_path_info.'users/'.$a["sid"].'/"><i>скрыто</i></a>';
			}
			$content2.='</td><td><a href="'.$server_absolute_path_info.'users/'.$a["sid"].'/">'.$a["sid"].'</a></td><td>';
			if(strpos($a["hidesome"],'-0-')===false && decode($a["nick"])!='') {
				$content2.='<a href="'.$server_absolute_path_info.'users/'.$a["sid"].'/">'.decode($a["nick"]).'</a>';
			}
			else {
				$content2.='<a href="'.$server_absolute_path_info.'users/'.$a["sid"].'/"><i>скрыт</i></a>';
			}
			$content2.='</td><td>';

			$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."comments where whom=".$a["id"]." and active='1'");
			$b = mysql_fetch_array($result2);
			$content2.='
	<a href="'.$server_absolute_path_info.'comments/'.$a["id"].'/filter=person"><nobr><b>Отзывы ('.$b[0].')</b></nobr></a></td></tr>';
			$stringnum++;
		}

		$result=mysql_query($query2);
		$a=mysql_fetch_array($result);
		$count=$a[0];
		$content2.='</table></div><br>'.pagecount('',$count,$bazecount,'&filter='.$selecter1->getVal().'&filter2='.$selecter2->getVal().'&filter3='.$selecter3->getVal().'&filter4='.$selecter5->getVal().'&filter5='.$selecter4->getVal().'&filter8='.$selecter8->getVal());

		$content.='</div></center>';
	}
}
?>