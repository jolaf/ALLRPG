<?php
include_once("db.inc");
require_once($server_inner_path."classes_objects_allrpg.php");
require_once($server_inner_path.$direct."/classes/classes_objects.php");

start_mysql();
# Установление соединения с MySQL-сервером

session_start();
auth2("users", true);

$year=2014;

$system_id=599;
$nominations=4862;
$jury_id=598;
$wanttovotefor=4857;

for($i=1;$i<20;$i++) {
	$wanttovotefor_arr[$i]=$i;
}

$takelinks_arr[]=4863;
$takelinks_arr[]=4864;
$takelinks_arr[]=4866;
$takelinks_arr[]=4867;
$takelinks_arr[]=4869;
$takelinks_arr[]=4870;
# из каких полей заявки номинанта пытаться выбрать ссылки

// ВЫШЕ ЭТОГО - СПЛОШНЫЕ НАСТРОЙКИ


$gcacademy=false;
$result=mysql_query("SELECT * FROM ".$prefix."roles WHERE site_id=".$jury_id." and player_id=".$_SESSION["user_id"]." and status=3 and todelete!='1' and todelete2!='1'");
$a=mysql_fetch_array($result);
if($a["id"]!='') {
	$gcacademy=true;
}

$gcadmin=false;
$result2=mysql_query("SELECT * FROM ".$prefix."allrights2 WHERE site_id=".$jury_id." and user_id=".$_SESSION["user_sid"]." and rights=1");
$b=mysql_fetch_array($result2);
if($b["id"]!='') {
	$gcadmin=true;
	if(encode($_GET["academy"])=="1") {
		$_SESSION["adm_academy"]=true;
	}
	elseif(encode($_GET["academy"])=="2") {
		unset($_SESSION["adm_academy"]);
	}
}

$result=mysql_query("SELECT * FROM ".$prefix."rolefields WHERE site_id=".$system_id." and id=".$nominations);
$a=mysql_fetch_array($result);

preg_match_all('#\[(\d+)\]\[([^\]]+)\]#',decode($a["rolevalues"]),$matches);
foreach($matches[0] as $key=>$value) {
	$rolevalues[$matches[1][$key]] = array($matches[1][$key],$matches[2][$key]);
}

$jury_count=array();
$result3=mysql_query('SELECT COUNT(id) FROM '.$prefix.'roles WHERE site_id='.$jury_id.' AND todelete!="1" AND todelete2!="1" AND status=3');
$c=mysql_fetch_array($result3);
$alljury=$c[0];
for($i=1;$i<=count($rolevalues);$i++) {
	if(isset($wanttovotefor_arr[$rolevalues[$i][0]])) {
		$result3=mysql_query('SELECT COUNT(id) FROM '.$prefix.'roles WHERE site_id='.$jury_id.' AND allinfo REGEXP "\\\[virtual'.$wanttovotefor.'\\\]\\\[[^]]*-'.$wanttovotefor_arr[$rolevalues[$i][0]].'-[^]]*" AND todelete!="1" AND todelete2!="1" AND status=3');
		$c=mysql_fetch_array($result3);
		$jury_count[$rolevalues[$i][0]]=$c[0];
	}
	else {
		$jury_count[$rolevalues[$i][0]]=$alljury;
	}
}

//критерии по номинациям: номер критерия по порядку, название критерия, высший балл, необязательность поля
$rolevalues[1][2]=Array(
	Array(1,'Оцените идею игры. Насколько она была сильной?  Можно ли сказать, что игра была задумана так, чтобы заставить игроков задуматься или что-то почувствовать?',5),
	Array(2,'Оцените получившуюся игру в целом. Соотнесите реализацию игры с идеей: насколько мастерам удалось воплотить задуманное?',10),
	Array(3,'Насколько активным и значимым для игры было участие игроков в процессе подготовки к игре?',5),
	Array(4,'Поднимались ли, на ваш взгляд, на игре мировоззренческие проблемы? Несла ли игра общечеловеческие ценности?',10),
	Array(5,'Можно ли сказать, что у игры был сюжет? Насколько он был интересным и проработанным?',10),
	Array(6,'Насколько эффективными были правила и механизмы игры?',5),
	Array(7,'Насколько атмосферной, эстетически цельной была игра?',10),
	Array(8,'Насколько удачной была техническая организация игры?',7),
	Array(9,'Стала ли игра основой для массового вторичного творчества участников?',5),
	Array(10,'Можно ли выделить оригинальные приемы мастеров, удачно сработавшие на игре? Укажите, какие.',5,true),
);
$rolevalues[2][2]=Array(
	Array(11,'Оцените идею игры. Насколько она была сильной?  Можно ли сказать, что игра была задумана так, чтобы заставить игроков задуматься или что-то почувствовать?',5),
	Array(12,'Оцените получившуюся игру в целом. Соотнесите реализацию игры с идеей: насколько мастерам удалось воплотить задуманное?',10),
	Array(13,'Насколько активным и значимым для игры было участие игроков в процессе подготовки к игре?',5),
	Array(14,'Поднимались ли, на ваш взгляд, на игре мировоззренческие проблемы? Несла ли игра общечеловеческие ценности?',10),
	Array(15,'Можно ли сказать, что у игры был сюжет? Насколько он был интересным и проработанным?',10),
	Array(16,'Насколько эффективными были правила и механизмы игры?',5),
	Array(17,'Насколько атмосферной, эстетически цельной была игра?',10),
	Array(18,'Насколько удачной была техническая организация игры?',7),
	Array(19,'Стала ли игра основой для массового вторичного творчества участников?',5),
	Array(20,'Можно ли выделить оригинальные приемы мастеров, удачно сработавшие на игре? Укажите, какие.',5,true),
);
$rolevalues[3][2]=Array(
	Array(21,'Оцените идею игры. Насколько она была сильной?  Можно ли сказать, что игра была задумана так, чтобы заставить игроков задуматься или что-то почувствовать?',5),
	Array(22,'Оцените получившуюся игру в целом. Соотнесите реализацию игры с идеей: насколько мастерам удалось воплотить задуманное?',5),
	Array(23,'Насколько активным и значимым для игры было участие игроков в процессе подготовки к игре?',10),
	Array(24,'Поднимались ли, на ваш взгляд, на игре мировоззренческие проблемы? Несла ли игра общечеловеческие ценности?',10),
	Array(25,'Можно ли сказать, что у игры был сюжет? Насколько он был интересным и проработанным?',10),
	Array(26,'Насколько эффективными были правила и механизмы игры?',5),
	Array(27,'Насколько атмосферной, эстетически цельной была игра?',10),
	Array(28,'Насколько удачной была техническая организация игры?',10),
	Array(29,'Стала ли игра основой для массового вторичного творчества участников?',5),
	Array(30,'Насколько игра повлияла на ролевое сообщество в целом?',5),
	Array(31,'Можно ли выделить оригинальные приемы мастеров, удачно сработавшие на игре? Укажите, какие.',5,true),
);

$rolevalues[4][2]=Array(
	Array(32,'Насколько значим вклад номинанта в развитие РИ?',10),
	Array(33,'Насколько легко транслировать эти достижения и применять в будущем?',5),
	Array(34,'Оцените личные вложенные усилия номинанта.',5)
);

$rolevalues[5][2]=Array(
	Array(35,'Насколько инновационной была идея и в чем она состояла? Удалось ли воплотить ее на игре в значительной степени?',10),
	Array(36,'Насколько эта инновация была полезна для игры в целом?',5),
	Array(37,'Насколько повторяемой и переносимой на другие игры является инновация?',10),
	Array(38,'Насколько масштабной была инновация - охватывала ли она значимую часть игроков, в какой степени влияла на игру?',10),
);

$rolevalues[6][2]=Array(
	Array(39,'Наскольно амбициозным был проект мастеров, насколько он реализовался?',10),
	Array(40,'Оцените вклад мастеров в общий успех игры. Есть ли примеры хорошей работы мастеров до, во время и после игры?',10),
	Array(41,'Насколько положительным был общественный резонанс после игры?',5),
	Array(42,'Были ли мастерами применены новые или особенно интересные мастерские приемы и решения? Какие?',5),
);

$rolevalues[7][2]=Array(
	Array(43,'Насколько правила соответствовали идее, движку и миру игры, входили ли данные правила в стройную систему правил?',10),
	Array(44,'Насколько успешно правила реализовались на игре? Насколько удачно мастерам удалось обойтись без "дыр", исключений и тонких моментов?',10),
	Array(45,'Насколько простыми и понятными для игроков были правила?',5),
	Array(46,'Насколько новыми были идеи, заложенные в правила?',5),
	Array(47,'Могут ли эти правила быть перенесены на другие игры (в том числе, в другом сеттинге, другого формата)?',5),
	Array(48,'Можно ли выделить особенно оригинальные и полезные для игры моменты в правилах? Какие?',5,true),
);

$rolevalues[8][2]=Array(
	Array(49,'Насколько правила соответствовали идее, движку и миру игры, входили ли данные правила в стройную систему правил?',10),
	Array(50,'Насколько успешно правила реализовались на игре? Насколько удачно мастерам удалось обойтись без "дыр", исключений и тонких моментов?',10),
	Array(51,'Насколько простыми и понятными для игроков были правила?',5),
	Array(52,'Насколько новыми были идеи, заложенные в правила?',5),
	Array(53,'Могут ли эти правила быть перенесены на другие игры (в том числе, в другом сеттинге, другого формата)?',5),
	Array(54,'Можно ли выделить особенно оригинальные и полезные для игры моменты в правилах? Какие?',5,true),
);

$rolevalues[9][2]=Array(
	Array(55,'Насколько качественным было информационное обеспечение игры (сайт, презентации, работа на конвентах), грамотность и адекватность подачи информации?',10),
	Array(56,'Насколько своевременным был процесс разработки правил, моделей и механизмов игры?',5),
	Array(57,'Насколько эффективной была работа с заявками/игроками, было ли уделено достаточно внимания игрокам.',10),
	Array(58,'Насколько адекватным был подбор игроков на роли.',5),
	Array(59,'Хорошо ли был разработан сюжет до игры (создание микроконфликтов/завязок, проработка макросюжета, тайминга и т.п.)? Использовали ли мастера какие-то интересные сюжетные заготовки?',5),
	Array(60,'Участвовала ли МГ в подготовке антуража (в т.ч. в виде помощи и направления игроков), насколько это помогло игре в целом?',5),
	Array(61,'Насколько удачно было сделано вхождение в игру? Как с организационной (заранее готовы документы, не было очередей на регистрации и за «загрузом» и т.п.), так и с игровой (эффектность старта, задание атмосферности и т.п.) точки зрения?',5),
	Array(62,'Можно ли выделить какие-то отдельные интересные, оригинальные и полезные для игры приемы в работе МГ/мастера?',5,true),
);

$rolevalues[10][2]=Array(
	Array(63,'Вели ли мастера последовательную и серьезную работу по оформлению итогов игры?',5),
	Array(64,'Соответствуют ли материалы, собранные мастерами, изначальной идее игры? Помогают ли они усилить эффект от прошедшей ролевой игры?',5),
	Array(65,'Есть ли примеры специально отобранных мастерами качественных фоторепортажей, серьезных рецензий, выдающихся отчетов игроков? Насколько целостно представлены итоги игры?',10),
	Array(66,'Оцените, насколько транслируем формат подведения итогов, как много игроков смогли с ними ознакомиться?',5),
	Array(67,'Подключали ли мастера игроков в подведению итогов игры? Насколько велика была их роль в оформлении итогов?',5),
	Array(68,'Можно ли говорить о том, что итоги игры презентовались не только внутри ролевого сообщества? Были ли представление игры сделано в формате, понятном внешней аудитории?',10),
);

$rolevalues[11][2]=Array(
	Array(69,'Насколько сюжет позволил раскрыть на игре замысел мастеров?',10),
	Array(70,'Был ли сюжет оригинальным, драматичным, непредсказуемым для игроков? Помогал ли сюжет оказывать влияние на переживания персонажей и эмоции игроков?',10),
	Array(71,'Оцените динамику и плотность сюжета на игре, провисал ли он по времени, соблюдалась ли напряженность в разных точках полигона (или сюжетных линиях), были ли охвачены все игроки? Насколько изящным и эффективным было управление сюжетом игры?',10),
	Array(72,'Если игра по авторскому миру, насколько он был полон, интересен и соответствовал целям мастеров? Если же игра по внешним источникам, насколько удачно они были адаптированы?',5),
	Array(73,'Насколько глубоко и серьезно прорабатывались вводные игроков? Были ли личные сюжеты большинства игроков связаны и переплетены?',5),
	Array(74,'Насколько удачным было управление игротехникой и игротехниками на игре?',5),
	Array(75,'Были ли после игры многочисленные примеры интересных отчетов игроков о судьбе их персонажей на игре?',5),
);

$rolevalues[12][2]=Array(
	Array(76,'Была ли сформулирована идея игры мастерами явно до или после игры? Насколько она была сильной?',5),
	Array(77,'Удалось ли донести именно эту идею до значительной части игроков?',10),
	Array(78,'Поднимались ли на игре серьезные проблемы, которые требовали бы личного выбора и самоопределения игрока?',10),
	Array(79,'Дала ли игра возможность игрокам прочувствовать общечеловеческие ценности?',10),
	Array(80,'Возникали ли на игре не единичные моменты объединения переживаний игрока и персонажа? Была ли в этом заслуга мастеров? Насколько эти моменты были связаны с идеей игры?',5),
	Array(81,'Насколько идея игры лежит в нашем культурном контексте? Насколько игра и опыт, полученный на игре, были актуальными для игроков?',5),
);

$rolevalues[13][2]=Array(
	Array(82,'Была ли игра официально согласована?',5),
	Array(83,'Соответствовал ли полигон игровым задачам и целям мастеров?',5),
	Array(84,'Было ли достаточным медицинское обеспечение? Оцените его уровень.',3),
	Array(85,'Насколько хорошо были налажены информационных потоки внутри МГ (связь, согласованность решений и т.д.).',3),
	Array(86,'Оцените выполнение предыгровых обещаний мастеров, относящихся к АХЧ.',5),
	Array(87,'Насколько удачно был организован процесс чиповки / входа в игру.',3),
	Array(88,'Насколько адекватным был бюджет игры?',3),
	Array(89,'Была ли в наличии необходимая инфраструктура (например, вода, дрова, доски, свет, электричество, сеть).',10),
	Array(90,'Выделите особые успехи в организации игры.',5,true),
);

$rolevalues[14][2]=Array(
	Array(91,'Насколько стилистически цельной была игра, оцените общую эстетику игры.',10),
	Array(92,'Насколько эстетика игры и, в частности, уровень антуража соответствовал концепции и миру игры?',5),
	Array(93,'Насколько полигон соответствовал эстетике игры?',5),
	Array(94,'Насколько адекватным игре, красивым, интересным был внешний вид игроков?',5),
	Array(95,'Какова была степень участия игроков в подготовке антуража игры. Насколько это участие помогло игре в целом?',5),
	Array(96,'Насколько легко были выполнимы требования к антуражу? Можно ли говорить о простом входе для новичка?',3),
	Array(97,'Как много чувств игроков активно затрагивала атмосфера игры (зрение, слух, обоняние и т.п.)?',5),
	Array(98,'Можно ли выделить уникальные эстетические приемы, примененные на игре (например, взаимодействие персонажей через пение, необычное пространство игры, серьезное музыкальное сопровождение, игра со светом, спецэффекты и т.п.)',10,true),
);

// прописываем стартовый набор линков по всем номинантам
if(encode($_GET["install"])==1 && $gcadmin) {
    unset($virtuals);
    $result=mysql_query('SELECT * FROM '.$prefix.'rolefields WHERE site_id='.$system_id.' and (roletype="textarea" OR roletype="text") order by rolecode');
    while($a=mysql_fetch_array($result)) {
    	if(in_array($a["id"],$takelinks_arr)) {
    		$virtuals[]=Array($a["id"],$a["rolename"]);
    	}
    }
    function getalllinks($text) {
    	global
    		$virtuals;

		unset($alllinks);
		$alllinks=Array();
		for($i=0;$i<count($virtuals);$i++) {
			$stri=substr($text,strpos($text,'[virtual'.$virtuals[$i][0])+strlen('[virtual'.$virtuals[$i][0].']')+1,strlen($text));
			$stri=substr($stri,0,strpos($stri,']'));
			preg_match_all('/http:\/\/[^\s;\]<]*/', $stri, $alllinks2);
			foreach($alllinks2[0] as $val) {
				if($val!='') {
					$alllinks[]=Array($virtuals[$i][1],$val);
				}
			}
			preg_match_all('/https:\/\/[^\s;\]<]*/', $stri, $alllinks2);
			foreach($alllinks2[0] as $val) {
				if($val!='') {
					$alllinks[]=Array($virtuals[$i][1],$val);
				}
			}
			preg_match_all('/([\s]*)([_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*([ ]+|)@([ ]+|)([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,}))([\s]*)/i', $stri, $alllinks2);
			foreach($alllinks2[0] as $val) {
				if($val!='') {
					$alllinks[]=Array($virtuals[$i][1],$val);
				}
			}
			/*preg_match_all('/([\s]*)(?(?<=.)nothinghappens|[a-zA-Z0-9-]+\.[^\s;\]<]*)/i', $stri, $alllinks2);
			foreach($alllinks2[0] as $val) {
				if($val!='') {
					$alllinks[]=Array($virtuals[$i][1],'<font color="red">http://'.trim($val).'</font>');
				}
			}*/
		}
		return $alllinks;
	}
    $dd=0;
    $result=mysql_query('SELECT * FROM '.$prefix.'gc'.$year.'links limit 0,1');
    if(mysql_affected_rows==0) {
	    $lastsorter='';
	    $lastnominee=0;
	    $result=mysql_query('SELECT * FROM '.$prefix.'roles WHERE site_id='.$system_id.' and status=3 and todelete!="1" and todelete2!="1" order by sorter');
	    while($a=mysql_fetch_array($result)) {
	    	/*if(strtolower($a["sorter"])!=strtolower($lastsorter)) {
	    		$lastsorter=$a["sorter"];
	    		$lastnominee=$a["id"];
	    	}*/
	    	$lastnominee=$a["id"];
	    	$alllinks=getalllinks(decode($a["allinfo"]));
	    	foreach($alllinks as $f => $v) {
	    		if($v[1]!='') {
	    			echo($v[0].' - '.$v[1].'<br>');
	    			mysql_query('INSERT IGNORE INTO '.$prefix.'gc'.$year.'links (nominee, parent, name, user, content, val, date) VALUES ('.$lastnominee.',0,"Из &laquo;'.$v[0].'&raquo;",'.$a["player_id"].',"'.$v[1].'",0,'.time().')');
	    			$dd++;
	    			if($lastnominee!=$a["id"]) {
	    				mysql_query('INSERT IGNORE INTO '.$prefix.'gc'.$year.'links (nominee, parent, name, user, content, val, date) VALUES ('.$a["id"].',0,"Из &laquo;'.$v[0].'&raquo;",'.$a["player_id"].',"'.$v[1].'",0,'.time().')');
	    			}
	    		}
	    	}

	    }
	    echo("<br>Total: ".$dd);
	}
	else {
		echo('Таблица '.$prefix.'gc'.$year.' содержит данные. Очистите ее, прежде чем проводить инсталляцию.');
	}
}
// для академиков
elseif($gcacademy || $_SESSION["adm_academy"]) {
//elseif($_SESSION["adm_academy"]) {
	$tour=0;
	$result=mysql_query('SELECT * FROM '.$prefix.'gc'.$year.' where user_id=0 order by tour desc limit 0,1');
	$a=mysql_fetch_array($result);
	if($a["id"]!='') {
		$tour=$a["tour"];
	}

	if($subobj=='') {
		if($gcadmin) {
			$content2.='<a href="/gc'.$year.'.php?academy=2"><b>Вернуться к статистике</b></a><br><br>';
		}
		$content2.='<ul>';
		for($i=1;$i<=count($rolevalues);$i++) {
			$shownom=false;
			if(isset($wanttovotefor_arr[$rolevalues[$i][0]])) {
				$result=mysql_query('SELECT * FROM '.$prefix.'roles WHERE site_id='.$jury_id.' and player_id='.$_SESSION["user_id"].' and allinfo REGEXP "\\\[virtual'.$wanttovotefor.'\\\]\\\[[^]]*-'.$wanttovotefor_arr[$rolevalues[$i][0]].'-[^]]*"');
				$a=mysql_fetch_array($result);
				if($a["id"]!='') {
					$shownom=true;
				}
			}
			else {
				$shownom=true;
			}
            if($shownom || $_SESSION["adm_academy"]) {
				$result=mysql_query('SELECT * FROM '.$prefix.'gc'.$year.' where user_id=0 and nomination='.$rolevalues[$i][0].' order by tour desc limit 0,1');
				$a=mysql_fetch_array($result);
				$result=mysql_query('SELECT * FROM '.$prefix.'roles WHERE site_id='.$system_id.' and status=3 and todelete!="1" and todelete2!="1" and allinfo REGEXP "\\\[virtual'.$nominations.'\\\]\\\['.$rolevalues[$i][0].'\\\]"');
				if(($tour==0 && mysql_affected_rows($link)>0) || (isset($a["tour"]) && $a["tour"]==$tour)) {
                    if($a["tour"]>0) {
						$tour=$a["tour"];
						$result=mysql_query('SELECT * from '.$prefix.'roles where id IN (SELECT DISTINCT nominee FROM '.$prefix.'gc'.$year.' where user_id=0 and nomination='.$rolevalues[$i][0].' and tour='.$tour.') and id!=0 and site_id='.$system_id." order by sorter");
					}
					else {
						$result=mysql_query('SELECT DISTINCT(sorter),id FROM '.$prefix.'roles WHERE site_id='.$system_id.' and status=3 and todelete!="1" and todelete2!="1" and allinfo REGEXP "\\\[virtual'.$nominations.'\\\]\\\['.$rolevalues[$i][0].'\\\]" group by sorter order by sorter');
					}
					unset($allnominees);
					while($a=mysql_fetch_array($result)) {
						$allnominees[]=$a["id"];
					}
					$result2=mysql_query('SELECT * FROM '.$prefix.'gc'.$year.' where user_id=0 and nomination='.$rolevalues[$i][0].' and tour='.$tour.' and nominee=0');
					$b=mysql_fetch_array($result2);
					if($b["id"]!='' || $tour==0) {
						$allnominees[]=0;
					}


					$content2.='<li><a href="?subobj='.$rolevalues[$i][0].'">'.$rolevalues[$i][1].'</a>';
					$result=mysql_query('SELECT * FROM '.$prefix.'gc'.$year.' where user_id='.$_SESSION["user_id"].' and nomination='.$rolevalues[$i][0].' and tour='.$tour);
					$a=mysql_fetch_array($result);
					$result2=mysql_query('SELECT * FROM '.$prefix.'users where id='.$_SESSION["user_id"]);
					$b=mysql_fetch_array($result2);
					$gender='';
					if($b["gender"]==2) {
						$gender='а';
					}
					prepare_all_nomination_data($_SESSION["user_id"],$rolevalues[$i][0],$tour);
					if($nominationready) {
						$content2.=' <span style="color: green;">проголосовал'.$gender.'</span>';
					}
					else {
						if($a["id"]!='') {
							$content2.=' <span style="color: orange;">частично проголосовал'.$gender.'</span>';
						}
						else {
							$content2.=' <span style="color: red;">не проголосовал'.$gender.'</span>';
						}
					}
				}
			}
		}
		$content2.='</ul>';
	}
	else {
		$bodybody='onMouseMove="getCoords(event)" ';
		$historyview=false;
		if(encode($_GET["tour"])!='') {
			$tour=encode($_GET["tour"]);
			$historyview=true;
		}
		function changetolink($text) {
			$text = eregi_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)','<a href="\\1" target="_blank">\\1</a>', $text);
			$text = eregi_replace('([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_\+.~#?&//=]+)','\\1<a href="http://\\2" target="_blank">\\2</a>', $text);
			$text = eregi_replace('([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})','<a href="mailto:\\1">\\1</a>', $text);
			return $text;
		}

		function getnomineelinks($a) {
			global
				$year,
				$subobj,
				$system_id,
				$prefix;

			$cont.='<div><b>Ссылки</b></div>
<div style="color: red; margin-bottom: 10px;" class="sm">Внимание: если вы оцените ссылку или добавите новую, вы утеряете все НЕСОХРАНЕННЫЕ результаты голосования! Рекомендуем сначала сохранить данные.</div>
<div class="sm2">';
			$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."gc".$year."links WHERE nominee=".$a["id"]." and (parent IS NULL or parent=0)");
			$s=mysql_fetch_array($result);
			$cont.='<table border=0><tr valign="top"><td style="padding-right: 15px; width: 50%;">';
			$hh=0;
			$hh1=ceil($s[0]/2);
			$query="SELECT t1.*, SUM(COALESCE(t2.val,0)) as totaler FROM ".$prefix."gc".$year."links as t1 LEFT OUTER JOIN ".$prefix."gc".$year."links as t2 ON t2.parent=t1.id WHERE t1.nominee=".$a["id"]." GROUP BY t1.id order by totaler desc";
			//echo($query);
			$result5=mysql_query($query);
			while($e=mysql_fetch_array($result5)) {
				$hh++;
				if($hh==$hh1+1) {
					$cont.='</td><td style="padding-left: 15px; width: 50%;">';
				}
				if($e["totaler"]>0) {
					$e["totaler"]='+'.$e["totaler"];
				}
				$cont.='<a href="';
				if(strpos($e["content"],'@')!==false) {
					$cont.='mailto:';
				}
				$cont.=$e["content"].'" class="the_link" link_id="'.$e["id"].'" nominee_id="'.$a["id"].'" target="_blank" title="';
				if($e["user"]!=0) {
					$result2=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$e["user"]);
					$b=mysql_fetch_array($result2);
					$cont.=usname($b,true).' ';
				}
				$cont.='в '.date("G:i d.m.Y",$e["date"]).'">'.decode($e["name"]).'</a> <span title="';
                $result2=mysql_query("SELECT * FROM ".$prefix."gc".$year."links WHERE parent=".$e["id"]." order by date desc");
				while($b=mysql_fetch_array($result2)) {
					$result3=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$b["user"]);
					$c=mysql_fetch_array($result3);
					if($b["val"]>0) {
						$b["val"]='+'.$b["val"];
					}
					$cont.=usname($c,true).' ('.$b["val"].');';
				}
				$cont.='">('.$e["totaler"].')</span> ';
				$result2=mysql_query("SELECT * FROM ".$prefix."gc".$year."links WHERE parent=".$e["id"]." and user=".$_SESSION["user_id"]);
				$b=mysql_fetch_array($result2);
				if($b["id"]=='' || $b["val"]==0) {
					$cont.='<a href="/gc'.$year.'.php?action=votelinkplus&id='.$e["id"].'&subobj='.$subobj.'" style="color:green">[+]</a> <a href="/gc'.$year.'.php?action=votelinkminus&id='.$e["id"].'&subobj='.$subobj.'" style="color:red">[&#150;]</a>';
				}
				elseif($b["val"]=='-1') {
					$cont.='<a href="/gc'.$year.'.php?action=votelinkplus&id='.$e["id"].'&subobj='.$subobj.'" style="color:green">[+]</a>';
				}
				elseif($b["val"]=='1') {
					$cont.='<a href="/gc'.$year.'.php?action=votelinkminus&id='.$e["id"].'&subobj='.$subobj.'" style="color:red">[&#150;]</a>';
				}
				if($e["user"]==$_SESSION["user_id"] || $_SESSION["adm_academy"]) {
					$cont.=' <a href="/gc'.$year.'.php?action=votelinkdelete&id='.$e["id"].'&subobj='.$subobj.'" style="color: red">[удалить]</a>';
					$cont.=' <a class="link_edit" style="color: green; cursor: pointer;">[редактировать]</a>';
				}
				$cont.='<br>';
			}
			$cont.='</td></tr></table><br>';
			$cont.='<b><span id="link_h_'.$a["id"].'">Добавить</span>:</b><br>
<form action="gc'.$year.'.php" method="post" enctype="multipart/form-data" id="links_'.$a["id"].'">
<input type="hidden" name="subobj" value="'.$subobj.'">
<input type="hidden" name="action" value="votelinkadd" id="link_action">
<input type="hidden" name="id" value="" id="link_id">
<input type="hidden" name="nominee" value="'.$a["id"].'">
Описание: <input type="text" name="text" id="link_text">
Ссылка: <input type="text" name="content" id="link_content">
<input type="submit" value="добавить" id="link_submit">
</form>
';
			$cont.='</div>';
			return $cont;
		}

		function getnomineedata($a) {
			global
				$system_id,
				$prefix;

			$result5=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$a["player_id"]);
			$e=mysql_fetch_array($result5);
			$cont.='<div style="margin-bottom: 10px;"><b>Инициатор</b>: '.usname($e,true,true).'</div>';

            $rolefields=virtual_structure("SELECT * from ".$prefix."rolefields where team='".$a["team"]."' and site_id=".$system_id." order by rolecode","allinfo","role");
			$allvalues=unmakevirtual($a["allinfo"]);
			for($i=0;$i<count($rolefields);$i++) {
				if(($rolefields[$i]["read"]==1 || $rolefields[$i]["read"]==10) && (decode($allvalues[$rolefields[$i]["name"]])!='' || $rolefields[$i]["type"]=='checkbox') && $rolefields[$i]["sname"]!='Номинация') {
					$cont.='<div style="margin-bottom: 10px;">';
					if($rolefields[$i]["type"]=="text" || $rolefields[$i]["type"]=="number") {
						$cont.='<b>'.$rolefields[$i]["sname"].'</b>: ';
						$cont.=changetolink(decodesafe($allvalues[$rolefields[$i]["name"]]));
					}
					elseif($rolefields[$i]["type"]=="textarea") {
						$cont.='<b>'.$rolefields[$i]["sname"].'</b>:<br>';
						$cont.=changetolink(decodesafe($allvalues[$rolefields[$i]["name"]]));
					}
					elseif($rolefields[$i]["type"]=="checkbox") {
						$cont.='<b>'.$rolefields[$i]["sname"].'</b>: ';
						if($allvalues[$rolefields[$i]["name"]]==1) {
							$cont.='<font color="green"><b>&#8730</b></font>';
						}
						else {
							$cont.='<font color="red"><b>X</b></font>';
						}
					}
					elseif($rolefields[$i]["type"]=="select") {
						$cont.='<b>'.$rolefields[$i]["sname"].'</b>: ';
						for($j=0;$j<count($rolefields[$i]["values"]);$j++) {
							if($rolefields[$i]["values"][$j][0]==$allvalues[$rolefields[$i]["name"]]) {
								$cont.=$rolefields[$i]["values"][$j][1];
								break;
							}
						}
					}
					elseif($rolefields[$i]["type"]=="multiselect") {
						$cont.='<b>'.$rolefields[$i]["sname"].'</b>: ';
						for($j=0;$j<count($rolefields[$i]["values"]);$j++) {
							if(eregi('-'.$rolefields[$i]["values"][$j][0].'-',$allvalues[$rolefields[$i]["name"]])) {
								$cont.='<br>'.$rolefields[$i]["values"][$j][1];
							}
						}
					}
					$cont.='</div>';
				}
			}
			return $cont;
		}
		function createSelect($el) {
			global
				$_POST,
				$allnominees,
				$howmanynominees,
				$allnomineesnames,
				$wholenominationdata,
				$tour;

            for($i=0;$i<$howmanynominees;$i++) {
            	$cont.='<input type="hidden" name="nominee['.$el[0].']['.$i.']" id="nominee['.$el[0].']['.$i.']" value="'.$wholenominationdata[$el[0]][$i].'">';
            	if($el[3]) {
            		$cont.='<input type="hidden" id="placeholdcheck'.$el[0].'" value="1">';
            	}
            }
            $cont.='<div style="float: left; width: 262px;">';
            for($i=0;$i<$howmanynominees;$i++) {
            	$cont.='<div id="nomineechoicecontainer['.$el[0].']['.$i.']">';
            	if($wholenominationdata[$el[0]][$i]!='') {
            		$theone=$wholenominationdata[$el[0]][$i];
            		$cont.='<div class="nomineechoicemade" onmousedown="nommove(\''.$allnomineesnames[$theone].'\','.$theone.');nmoverclear(\'nomineechoicecontainer['.$el[0].']['.$i.']\','.($i+1).','.$el[0];
            		if($el[3]) {
            			$cont.=',true';
            		}
            		$cont.=')"><div class="nomineechoicecross"><a style="cursor: pointer; color: white;" onmousedown="event.cancelBubble = true; if(event.stopPropagation) event.stopPropagation(); nmoverclear(\'nomineechoicecontainer['.$el[0].']['.$i.']\','.($i+1).','.$el[0];
            		if($el[3]) {
            			$cont.=',true';
            		}
            		$cont.=')">[x]</a></div>'.$allnomineesnames[$theone].'</div>';
            	}
            	else {
            		$cont.='<div class="nomineechoice">'.($i+1).' место</div>';
            	}
            	$cont.='</div>';
            }
			$cont.='</div><div style="clear: both;"></div>';
			return $cont;
		}
		function createElement($el) {
			global
				$nominationerrorsfields,
				$wholenominationdata;

			$errorfound=false;

			if(!$errorfound && $nominationerrorsfields['comment']) {
				$cont.='<a name="firsterror"></a>';
				$errorfound=true;
			}
			$cont.='<div class="fieldname';
			if($nominationerrorsfields['comment']) {
				$cont.=' finish';
			}
			else {
				$cont.=' finished';
			}
			$cont.='">Развернутый комментарий по номинации';
			if($wholenominationdata['comment']=='') {
				for($i=0;$i<count($el);$i++) {
					$wholenominationdata['comment'].='------------------------------------------------------------------------------------
'.$el[$i][1].'
------------------------------------------------------------------------------------

';
				}
			}
			$cont.='</div><div><textarea name="comment" style="height: 310px">'.$wholenominationdata['comment'].'</textarea></div><br>';

			for($i=0;$i<count($el);$i++) {
				if(!$errorfound && $nominationerrorsfields[$el[$i][0]]) {
					$cont.='<a name="firsterror"></a>';
					$errorfound=true;
				}
				$cont.='<div class="fieldname';
				if($nominationerrorsfields[$el[$i][0]]) {
					$cont.=' finish';
				}
				else {
					$cont.=' finished';
				}
				$cont.='">'.$el[$i][1];
				if($el[$i][3]) {
					$cont.=' (необязательно)';
				}
				$cont.='</div><div>';
				$cont.=createSelect($el[$i]);
				$cont.='</div><br>';
			}
			return $cont;
		}

		if($action=="votelinkplus") {
        	$result2=mysql_query("SELECT * FROM ".$prefix."gc".$year."links WHERE parent=".$id." and user=".$_SESSION["user_id"]);
			$b=mysql_fetch_array($result2);
			if($b["val"]=='') {
				mysql_query("INSERT INTO ".$prefix."gc".$year."links (parent,user,val,date) VALUES (".$id.",".$_SESSION["user_id"].",1,".time().")");
				err('Ссылка оценена как удачная.');
			}
			elseif($b["val"]=='0') {
				mysql_query("UPDATE ".$prefix."gc".$year."links SET val=1 WHERE id=".$b["id"]);
				err('Ссылка оценена как удачная.');
			}
			elseif($b["val"]=='-1') {
				mysql_query("UPDATE ".$prefix."gc".$year."links SET val=0 WHERE id=".$b["id"]);
				err('Ссылка оценена как обычная.');
			}
		}
		elseif($action=="votelinkminus") {
        	$result2=mysql_query("SELECT * FROM ".$prefix."gc".$year."links WHERE parent=".$id." and user=".$_SESSION["user_id"]);
			$b=mysql_fetch_array($result2);
			if($b["val"]=='') {
				mysql_query("INSERT INTO ".$prefix."gc".$year."links (parent,user,val,date) VALUES (".$id.",".$_SESSION["user_id"].",-1,".time().")");
				err('Ссылка оценена как неудачная.');
			}
			elseif($b["val"]=='0') {
				mysql_query("UPDATE ".$prefix."gc".$year."links SET val=-1 WHERE id=".$b["id"]);
				err('Ссылка оценена как неудачная.');
			}
			elseif($b["val"]=='1') {
				mysql_query("UPDATE ".$prefix."gc".$year."links SET val=0 WHERE id=".$b["id"]);
				err('Ссылка оценена как обычная.');
			}
		}
		elseif($action=="votelinkadd") {
			$result2=mysql_query("SELECT * FROM ".$prefix."gc".$year."links WHERE parent=0 and user=".$_SESSION["user_id"]." and nominee=".encode($_POST["nominee"])." and name='".encode($_POST["text"])."' and content='".encode($_POST["content"])."'");
			$b=mysql_fetch_array($result2);
			if($b["id"]=='') {
				mysql_query("INSERT INTO ".$prefix."gc".$year."links (parent,nominee,user,name,content,date) VALUES (0,".encode($_POST["nominee"]).",".$_SESSION["user_id"].",'".encode($_POST["text"])."','".encode($_POST["content"])."',".time().")");
				err('Ссылка успешно добавлена.');
			}
			else {
				err_red('Заблокировано повторное сохранение.');
			}
		}
		elseif($action=="votelinkdelete") {
			$result2=mysql_query("SELECT * FROM ".$prefix."gc".$year."links WHERE id=".$id." and user=".$_SESSION["user_id"]);
			$b=mysql_fetch_array($result2);
			if($b["id"]!='' || $_SESSION["adm_academy"]) {
				mysql_query("DELETE FROM ".$prefix."gc".$year."links WHERE id=".$id);
				err('Ссылка успешно удалена.');
			}
		}
		elseif($action=="votelinkedit") {
			$result2=mysql_query("SELECT * FROM ".$prefix."gc".$year."links WHERE id=".$id." and user=".$_SESSION["user_id"]);
			$b=mysql_fetch_array($result2);
			if($b["id"]!='' || $_SESSION["adm_academy"]) {
				mysql_query("UPDATE ".$prefix."gc".$year."links SET name='".encode($_POST["text"])."', content='".encode($_POST["content"])."' WHERE id=".$id);
				if(mysql_error($link)) {
					err_red(mysql_error($link));
				}
				err('Ссылка успешно изменена.');
			}
		}
		elseif($action=="vote" && !$historyview) {
			$allinfo=makeallinfo($a["id"]);
			$result2=mysql_query('SELECT * FROM '.$prefix.'gc'.$year.' where user_id='.$_SESSION["user_id"].' and nomination='.$rolevalues[$subobj][0].' and tour='.$tour.' and nominee IS NULL');
			$b=mysql_fetch_array($result2);
			if($b["id"]!='') {
				// update
				$query='UPDATE '.$prefix.'gc'.$year.' SET date='.time().', allinfo="'.$allinfo.'" where id='.$b["id"];
			}
			else {
				// insert
				$query='INSERT INTO '.$prefix.'gc'.$year.' (nomination, user_id, allinfo, tour, date) VALUES ('.$rolevalues[$subobj][0].','.$_SESSION["user_id"].',"'.$allinfo.'",'.$tour.','.time().')';
			}
			//echo($query);
			err('Оценки успешно записаны.');
			mysql_query($query);
		}

		$allnominees=Array();
		$content2.='<div id="nomineemove" onMouseUp="hidenommove(event)" style="z-index: 200000"></div>
<div id="mainfixed" style="position: fixed; overflow-y: auto; z-index: 100000; height: 100%; width: 318px;">
<div style="width: 300px; border-right: 1px solid black; border-bottom: 1px solid black; margin-right: 5px; float: left; background-color: white;">
<h1>'.$rolevalues[$subobj][1].'</h1>
<span class="sm"><a href="/gc'.$year.'.php"><span style="font-size: 14px;"><b>&#8592;</b></span> к списку номинаций</a> |
';
		if($historyview) {
			$a["tour"]=$tour;
		}
		else {
			$tour=0;
			$result=mysql_query('SELECT * FROM '.$prefix.'gc'.$year.' where user_id=0 and nomination='.$rolevalues[$subobj][0].' order by tour desc limit 0,1');
			$a=mysql_fetch_array($result);
		}
		if($a["tour"]>0) {
			$tour=$a["tour"];
			$result=mysql_query('SELECT * from '.$prefix.'roles where id IN (SELECT DISTINCT nominee FROM '.$prefix.'gc'.$year.' where user_id=0 and nomination='.$rolevalues[$subobj][0].' and tour='.$tour.') and id!=0 and site_id='.$system_id." order by sorter");
		}
		else {
			$result=mysql_query('SELECT * FROM '.$prefix.'roles WHERE site_id='.$system_id.' and status=3 and todelete!="1" and todelete2!="1" and allinfo REGEXP "\\\[virtual'.$nominations.'\\\]\\\['.$rolevalues[$subobj][0].'\\\]" order by sorter');
		}
		$lastsorter='';
		$nomineedata='';
		$content3='';
		$content4='';

	    if($tour==0) {
			$content2.='[<b>тур '.($tour+1).'</b>] ';
		}
		else {
			$content2.='[<a href="?subobj='.$rolevalues[$subobj][0].'&tour=0" target="blank">тур 1</a>] ';
		}
	    $result2=mysql_query('SELECT DISTINCT tour FROM '.$prefix.'gc'.$year.' where nomination='.$rolevalues[$subobj][0].' and tour>0 order by tour');
		while($b=mysql_fetch_array($result2)) {
			if($b["tour"]==$tour) {
				$content2.='[<b>тур '.($tour+1).'</b>] ';
			}
			else {
				$content2.='[<a href="?subobj='.$rolevalues[$subobj][0].'&tour='.$b["tour"].'" target="blank">тур '.($b["tour"]+1).'</a>] ';
			}
		}
		$content2.='<br><br></span>';

		unset($z);
		while($a=mysql_fetch_array($result)) {
			$z[]=$a;
		}
		$result2=mysql_query('SELECT * FROM '.$prefix.'gc'.$year.' where user_id=0 and nomination='.$rolevalues[$subobj][0].' and tour='.$tour.' and nominee=0');
		$b=mysql_fetch_array($result2);
		if($b["id"]!='' || $tour==0) {
			$z[]=Array('id'=>0,'sorter'=>'Не вручать');
		}
		for($i=0;$i<count($z);$i++) {
			$a=$z[$i];
			if(strtolower($a["sorter"])!=strtolower($lastsorter) && (preg_match('/\[virtual'.$nominations.']\[[^]]*'.$rolevalues[$subobj][0].'[^]]*]/U',$a["allinfo"]) || $a["id"]==0)) {
				$allnominees[]=$a["id"];
				$lastsorter=$a["sorter"];
			}
		}
        prepare_all_nomination_data($_SESSION["user_id"],$subobj,$tour);
		for($i=0;$i<count($z);$i++) {
			$a=$z[$i];
			if(strtolower($a["sorter"])!=strtolower($lastsorter) && (preg_match('/\[virtual'.$nominations.']\[[^]]*'.$rolevalues[$subobj][0].'[^]]*]/U',$a["allinfo"]) || $a["id"]==0)) {
				if($content3!='' && $a["id"]!=0) {
					$content3.='</div>';
				}
				$allnomineesnames[$a["id"]]=decode($a["sorter"]);

				$content2.='<div style="cursor:pointer" onMouseDown="nommove(\''.decode($a["sorter"]).'\','.$a["id"].')" id="nomineename'.$a["id"].'" onMouseUp="hidenommove(event)" id="nomineename'.$a["id"].'" class="nomineename">'.decode($a["sorter"]).'</div>';

				if($a["id"]!=0) {
					$nomineedata=getnomineedata($a);
					$nomineelinks=getnomineelinks($a);

					$content2.='<div class="nomineedata"><a style="cursor: pointer;" onClick="hideAll(\'aboutnominee'.$a["id"].'\');">?</a> <a style="cursor: pointer;" onClick="hideAll(\'nomineelinks'.$a["id"].'\');">&#8734;</a><div class="nomineesum">'.$allresults[$a["id"]].'</div></div>';
					$content3.='
<div id="aboutnominee'.$a["id"].'" class="aboutnominee"><div style="float: right;"><a style="cursor: pointer" onClick="hideAll(\'aboutnominee'.$a["id"].'\');"><b>[X]</b></a></div>'.$nomineedata;
					$content4.='
<div id="nomineelinks'.$a["id"].'" class="nomineelinks"><div style="float: right;"><a style="cursor: pointer" onClick="hideAll(\'nomineelinks'.$a["id"].'\');"><b>[X]</b></a></div>'.$nomineelinks.'</div>';
				}
				else {
					$content2.='<div class="nomineedata"><div class="nomineesum2">'.$allresults[$a["id"]].'</div></div>';
				}
				$lastsorter=$a["sorter"];
			}
			elseif(strtolower($a["sorter"])==strtolower($lastsorter) && (preg_match('/\[virtual'.$nominations.']\[[^]]*'.$rolevalues[$subobj][0].'[^]]*]/U',$a["allinfo"]) || $a["id"]==0)) {
				$content3.='<hr>'.getnomineedata($a);
			}
		}
		if($content3!='') {
			$content3.='</div>';
		}
		$content2.='
</div>
<div>
'.$content3.'
'.$content4.'
</div>
</div>
<div style="margin-left: 320px; margin-right: 15px; padding-top: 5px;">
'.$error;
		if($nominationready) {
			$content2.='<div style="color:green" class="sm2">Голосование успешно заполнено, результаты учитываются.</div><br>';
		}
		else {
			$content2.='<div style="color:red" class="sm2"><b>Голосование не полное, результаты не будут учтены.</b> <a style="cursor:pointer" onClick="if(document.getElementById(\'ded\').style.display==\'block\') {document.getElementById(\'ded\').style.display=\'none\';} else {document.getElementById(\'ded\').style.display=\'block\';}">[?]</a> <a href="#firsterror">[к первому незаполненному критерию]</a></div><div id="ded" style="display:none; color:red" class="sm2">Требуется заполнить все позиции критериев. Критерии, помеченные необязательными, можно не заполнять. Кроме того, необходимо заполнить комментарий.</div><br>';
		}
        if(!$historyview) {
        	$content2.='
<form action="gc'.$year.'.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="subobj" value="'.$subobj.'">
<input type="hidden" name="action" value="vote">
<center><input type="submit" value="Сохранить результаты" class="submiter"></center><br>';
		}
		$content2.='
<script>
var being_dragged = false;
nms=document.getElementById("nomineemove").style;
nm=document.getElementById("nomineemove");
selnom=-1;
selname="";
hmn='.$howmanynominees.';

var coords = [0,0];
function getCoords(e){
	var x, y;
	if (self.pageYOffset) // all except Explorers
	{
		x = self.pageXOffset; // not used in event code
		y = self.pageYOffset; // not used in event code
	}
	else if(document.documentElement && document.documentElement.scrollTop) // Explorer 6 Strict
	{
		x = document.documentElement.scrollLeft;
		y = document.documentElement.scrollTop;
	}
	else if (document.body) // all other Explorers
	{
		x = document.body.scrollLeft;
		y = document.body.scrollTop;
	}
	coords[0] = (document.all) ? x + event.clientX : e.pageX;
	coords[1] = (document.all) ? y + event.clientY : e.pageY;

	if(being_dragged && nm.innerHTML!="") {
		nms.top=(coords[1]-10)+"px";
		nms.left=(coords[0]-50)+"px";
	}

	return true;
}

function nommove(name,nom) {
    hideAll();
    nms.display=\'block\';
    nm.innerHTML=name;
    selnom=nom;
    selname=name;
    nms.top=(coords[1]-10)+"px";
	nms.left=(coords[0]-50)+"px";
    being_dragged=true;
}
function hidenommove(e) {
    being_dragged = false;
    nms.display=\'none\';

    if(navigator.userAgent.match(\'MSIE\') || navigator.userAgent.match(\'Gecko\')) {
    	var elem=document.elementFromPoint(e.clientX,e.clientY);
    }
    else {
    	var elem = document.elementFromPoint(e.pageX,e.pageY);
    }

    while(elem!=null) {
    	if(elem.id!=null && elem.id!="undefined" && elem.id!="") {
    		ei=elem.id;
    		if(ei.search(/nomineechoicecontainer/i)>=0) {
    			criteria=ei.substring(ei.indexOf("[")+1,ei.indexOf("]"));
    			place=ei.substring(ei.lastIndexOf("[")+1,ei.lastIndexOf("]"));
    			nmoversel(elem,parseInt(place)+1,criteria)
    		}
    	}
    	elem=elem.parentNode;
    }
    selname="";
	selnom=-1;
}
function nmoverclear(theid,place,criteria) {
	document.getElementById(theid).innerHTML=\'<div class="nomineechoice">\'+place+\' место</div>\';
	document.getElementById(\'nominee[\'+criteria+\'][\'+(place-1)+\']\').value="";
	if(document.getElementById("placeholdcheck"+criteria)!=undefined) {
		if(document.getElementById("placehold"+criteria).style.display==\'block\') {
			document.getElementById("placehold"+criteria).style.display=\'none\';
			for(s=0;s<hmn;s++) {
				b="nominee["+criteria+"]"+"["+s+"]";
				if(document.getElementById(b).value!="") {
					document.getElementById("placehold"+criteria).style.display=\'block\';
				}
			}
		}
	}
}
function nmoversel(el,place,criteria) {
	nmover=el;
	if(selname!="" && being_dragged==false) {
		letsave=true;
		for(j=0;j<hmn;j++) {
			if(document.getElementById(\'nominee[\'+criteria+\'][\'+j+\']\').value==selnom && document.getElementById(\'nominee[\'+criteria+\'][\'+j+\']\').value!="") {
            	letsave=false;
            }
		}
		if(letsave) {
			nmover.innerHTML=\'<div class="nomineechoicemade" onMouseDown="nommove(\\\'\'+selname+\'\\\',\'+selnom+\');nmoverclear(\\\'nomineechoicecontainer[\'+criteria+\'][\'+(place-1)+\']\\\',\'+place+\',\'+criteria+\')"><div class="nomineechoicecross"><a style="cursor:pointer;color: white;" onMouseDown="event.cancelBubble = true; if(event.stopPropagation) event.stopPropagation(); nmoverclear(\\\'nomineechoicecontainer[\'+criteria+\'][\'+(place-1)+\']\\\',\'+place+\',\'+criteria+\')">[x]</a></div>\'+selname+\'</div>\';
			document.getElementById(\'nominee[\'+criteria+\'][\'+(place-1)+\']\').value=selnom;
			if(document.getElementById("placeholdcheck"+criteria)!=undefined) {
				if(document.getElementById("placehold"+criteria).style.display==\'none\' && document.getElementById("nominee_comment["+criteria+"]").value=="") {
					document.getElementById("placehold"+criteria).style.display=\'block\';
				}
			}
		}
		else {
			alert("Нельзя ставить номинанта на две позиции критерия.");
		}
    }
}

function hideAll(el) {
	if(el!=undefined) {
		d=document.getElementById(el).style.display;
	}';
		for($j=0;$j<count($allnominees);$j++) {
			if($allnominees[$j]!=0) {
				$content2.='
	document.getElementById(\'nomineelinks'.$allnominees[$j].'\').style.display=\'none\';
	document.getElementById(\'aboutnominee'.$allnominees[$j].'\').style.display=\'none\';';
			}
		}
		$content2.='
	if(el==undefined) {
		document.getElementById(\'mainfixed\').style.width="318px";
	}
	else if(d==\'none\' || d==\'\') {
		document.getElementById(\'mainfixed\').style.width="100%";
		document.getElementById(el).style.display=\'block\';
	}
	else {
		document.getElementById(el).style.display=\'none\';
		document.getElementById(\'mainfixed\').style.width="318px";
	}
}
function clearField(elem) {';
		if($tour==0) {
			$content2.='
	document.getElementById("placehold"+elem).style.display=\'none\';';
		}
		$content2.='
}
function backField(elem,type) {';
		if($tour==0) {
			$content2.='
	p="placehold"+elem;
	t="nominee_comment["+elem+"]";
	if(type==2 && document.getElementById(t).value=="") {
		document.getElementById(p).style.display=\'block\';
	}
	else if(type==1 && document.getElementById(t).value=="") {
    	d="nominee["+elem+"]";
    	for(s=0;s<hmn;s++) {
            b=d+"["+s+"]";
            if(document.getElementById(b).value!="") {
    			document.getElementById(p).style.display=\'block\';
    		}
    	}
	}';
		}
		$content2.='
}
</script>
';
        $content2.=createElement($rolevalues[$subobj][2]);
		if(!$historyview) {
			$content2.='
<center><input type="submit" value="Сохранить результаты" class="submiter"></center>
</form>';
		}
		$content2.='
</div>
';
	}
}
elseif($gcadmin) {
	// для оргкомитета

	$tour=encode($_GET["tour"]);
	if($tour=='') {
		$tour=0;
	}
	$jury=encode($_GET["jury"]);
	if($subobj=='') {
		$content2.='<a href="/gc'.$year.'.php?academy=1"><b>Посмотреть механизм голосования</b></a><br><br>';

		$content2.='<ul>';
		for($i=1;$i<=count($rolevalues);$i++) {
			$content2.='<li>'.$rolevalues[$i][1].'<ul>';
			$result=mysql_query('SELECT DISTINCT tour FROM '.$prefix.'gc'.$year.' where nomination='.$rolevalues[$i][0]." order by tour");
			while($a=mysql_fetch_array($result)) {
				$content2.='<li><a href="?subobj='.$rolevalues[$i][0].'&tour='.$a["tour"].'">Тур '.($a["tour"]+1).'</a>';
			}
			$content2.='</ul>';
		}
		$content2.='</ul>';
		$content2.='<br>
<h1>Отчет по проголосовавшим академикам</h1>
<ul>';
		$result=mysql_query('SELECT tour FROM '.$prefix.'gc'.$year.' WHERE user_id!=0 ORDER BY tour DESC LIMIT 0,1');
		$a=mysql_fetch_array($result);
		$tourstotal=$a["tour"];
		for($j=0;$j<=$tourstotal;$j++) {
			unset($allnomineeslist);
			for($i=1;$i<=count($rolevalues);$i++) {
				if($j>0) {
					$result3=mysql_query('SELECT * from '.$prefix.'roles WHERE id IN (SELECT DISTINCT nominee FROM '.$prefix.'gc'.$year.' WHERE user_id=0 AND nomination='.$rolevalues[$i][0].' AND tour='.$j.') AND id!=0 AND site_id='.$system_id." ORDER BY sorter");
				}
				else {
					$result3=mysql_query('SELECT * FROM '.$prefix.'roles WHERE site_id='.$system_id.' AND status=3 AND todelete!="1" AND todelete2!="1" AND allinfo REGEXP "\\\[virtual'.$nominations.'\\\]\\\['.$rolevalues[$i][0].'\\\]" ORDER BY sorter');
				}
               	$lastsorter='';
               	while($c=mysql_fetch_array($result3)) {
                    if(strtolower($lastsorter)!=strtolower($c["sorter"])) {
               			$allnomineeslist[$i][]=array($c["id"],decode($c["sorter"]));
               			$lastsorter=$c["sorter"];
               		}
               	}
               	if($j>0) {
                   	$result3=mysql_query('SELECT * FROM '.$prefix.'gc'.$year.' WHERE nomination='.$rolevalues[$i][0].' AND tour='.$j.' AND nominee=0 AND user_id=0');
                   	$c=mysql_fetch_array($result3);
                   	if($c["id"]!='') {
                   		$allnomineeslist[$i][]=array(0,'Не вручать');
                   	}
               	}
               	else {
               		$allnomineeslist[$i][]=array(0,'Не вручать');
               	}
			}

			$div='tour'.$j;
			$content2.='<li><b>Тур '.($j+1).'</b> (<a style="cursor:pointer" onClick="if(document.getElementById(\''.$div.'\').style.display==\'none\') {document.getElementById(\''.$div.'\').style.display=\'block\'} else {document.getElementById(\''.$div.'\').style.display=\'none\'}">отобразить/скрыть</a>)
<div id="'.$div.'" style="display:none">
<ul>';
			$result=mysql_query('SELECT * FROM '.$prefix.'users WHERE id IN (SELECT player_id FROM '.$prefix.'roles WHERE site_id='.$jury_id.' and status=3 and todelete!="1" and todelete2!="1" order by sorter) or id IN (SELECT id FROM '.$prefix.'users WHERE sid IN (SELECT user_id from '.$prefix.'allrights2 WHERE site_id='.$jury_id.' and rights=1)) order by fio');
			while($a=mysql_fetch_array($result)) {
				$thisisadmin=false;
				$result2=mysql_query('SELECT * from '.$prefix.'allrights2 WHERE site_id='.$jury_id.' and rights=1 and user_id='.$a["sid"]);
				$b=mysql_fetch_array($result2);
				if($b["id"]!='') {
					$thisisadmin=true;
				}
				else {
					$result2=mysql_query('SELECT * from '.$prefix.'roles WHERE player_id='.$a["id"].' and site_id='.$jury_id);
					$b=mysql_fetch_array($result2);
				}
				$content2.='<li>';
				if($thisisadmin) {
					$content2.='<span style="font-size: 8px;">';
				}
				$content2.=usname($a,true);
				$allvotescount=0;
				$allnominationscount=0;
    			$content3='';
				for($i=1;$i<=count($rolevalues);$i++) {
					if((!isset($wanttovotefor_arr[$rolevalues[$i][0]]) || preg_match('/\[virtual'.$wanttovotefor.']\[[^]]*-'.$wanttovotefor_arr[$rolevalues[$i][0]].'-[^]]*/U',$b["allinfo"]) || $thisisadmin) && $allnomineeslist[$i][0]!=false) {
						$allnominationscount++;
						$allnominees=$allnomineeslist[$rolevalues[$i][0]];
						prepare_all_nomination_data($a["id"],$rolevalues[$i][0],$j);
						if($nominationready) {
							$content3.='<li><a href="?subobj='.$rolevalues[$i][0].'&tour='.$j.'&jury='.$a["id"].'">'.$rolevalues[$i][1].'</a> <span title="Академик проголосовал по данной номинации в данном туре" style="color: green; font-weight: bold;">&#8730</span>';
							$allvotescount++;
						}
						else {
							$content3.='<li>'.$rolevalues[$i][1].' <span title="Академик не проголосовал по данной номинации в данном туре" style="color: red; font-weight: bold;">X</span>';
						}
					}
				}
				$div='tour'.$j.'_'.$a["id"].'_'.$rolevalues[$i][0];
				$content2.=' ('.$allvotescount.' / '.$allnominationscount.') (<a style="cursor:pointer" onClick="if(document.getElementById(\''.$div.'\').style.display==\'none\') {document.getElementById(\''.$div.'\').style.display=\'block\'} else {document.getElementById(\''.$div.'\').style.display=\'none\'}">отобразить/скрыть</a>)';
				if($thisisadmin) {
					$content2.='<span style="font-size: 8px;">';
				}
				$content2.='
<ul id="'.$div.'" style="display:none">'.$content3.'</ul>';
			}
			$content2.='</ul>';
		}
		$content2.='</ul>
<br>
<h1>Отчет по комментариям</h1>
<ul>';
		$result=mysql_query('SELECT tour FROM '.$prefix.'gc'.$year.' where user_id=0 order by tour desc limit 0,1');
		$a=mysql_fetch_array($result);
		$tourstotal=$a["tour"];
		for($j=0;$j<=$tourstotal;$j++) {
			$content2.='<li><a href="?subobj=comments&tour='.$j.'">Тур '.($j+1).'</a>';
		}
		$content2.='
</ul>';
	}
	elseif($subobj=="comments") {
		$prevnom=0;
		$string=0;
		$content2.='<center><h1>Отчет по комментариям - тур '.($tour+1).'</h1></center>';
		$result=mysql_query('SELECT * FROM '.$prefix.'gc'.$year.' where tour='.$tour.' and nominee IS NULL order by nomination asc');
 		while($a=mysql_fetch_array($result)) {
         	if($prevnom!=$a["nomination"]) {
         		$content2.='<br><h1>'.$rolevalues[$a["nomination"]][1].'</h1><br>'.$content3;
         		$prevnom=$rolevalues[$a["nomination"]][0];
         		$allelems=$rolevalues[$a["nomination"]][2];
         	}
         	$result2=mysql_query('SELECT * FROM '.$prefix.'users WHERE id='.$a["user_id"]);
 			$b=mysql_fetch_array($result2);
 			unset($vote);
 			$vote=gc_unmakevirtual($a["allinfo"]);
			if($string==0) {
				$string=1;
			}
			else {
				$string=0;
			}
			$content2.='<div class="separated'.$string.'" title="'.usname($b,true).'">'.decodesafe(encode($vote['comment'])).'</div>';
 		}
	}
	else {
    	if($tour>0) {
			$result=mysql_query('SELECT * from '.$prefix.'roles where id IN (SELECT DISTINCT nominee FROM '.$prefix.'gc'.$year.' where user_id=0 and nomination='.$rolevalues[$subobj][0].' and tour='.$tour.') and id!=0 and site_id='.$system_id." order by sorter");
		}
		else {
			$result=mysql_query('SELECT * FROM '.$prefix.'roles WHERE site_id='.$system_id.' and status=3 and todelete!="1" and todelete2!="1" and allinfo REGEXP "\\\[virtual'.$nominations.'\\\]\\\['.$rolevalues[$subobj][0].'\\\]" order by sorter');
		}
		$lastsorter='';
		while($a=mysql_fetch_array($result)) {
			if(decode($a["sorter"])!=$lastsorter) {
				$allnominees[]=$a["id"];
				$allnomineesnames[]=decode($a["sorter"]);
				$lastsorter=decode($a["sorter"]);
			}
		}
		$result=mysql_query('SELECT * FROM '.$prefix.'gc'.$year.' where user_id=0 and nomination='.$rolevalues[$subobj][0].' and tour='.$tour.' and nominee=0');
		$a=mysql_fetch_array($result);
		if($a["id"]!='' || $tour==0) {
			$allnominees[]=0;
			$allnomineesnames[]='Не вручать';
		}
    	if($jury=='') {
    		// номинация-тур
    		$deletevote=encode($_GET["deletevote"]);
			if($deletevote>0) {
				mysql_query('DELETE FROM '.$prefix.'gc'.$year.' where nomination='.$rolevalues[$subobj][0].' and tour='.$tour.' and user_id='.$deletevote);
				if(mysql_affected_rows($link)>0) {
					err('Результаты голосования академика по номинации полностью удалены.');
				}
			}

    		$transfernominee=encode($_GET["transfernominee"]);
    		if($transfernominee!='') {
   				$result=mysql_query('SELECT * from '.$prefix.'gc'.$year.' where nomination='.$rolevalues[$subobj][0].' and tour='.($tour+1).' and nominee='.$transfernominee.' and user_id=0 limit 0,1');
   				$a=mysql_fetch_array($result);
   				if($a["id"]!='') {
   					err_red('Данный номинант уже переведен в следующий тур.');
   				}
   				else {
   					mysql_query('INSERT INTO '.$prefix.'gc'.$year.' (nomination,tour,nominee,user_id) VALUES ('.$rolevalues[$subobj][0].','.($tour+1).','.$transfernominee.',0)');
   					if(mysql_affected_rows($link)>0) {
   						err('Номинант успешно переведен в следующий тур.');
   					}
   				}
    		}
    		$untransfernominee=encode($_GET["untransfernominee"]);
    		if($untransfernominee!='') {
   				$result=mysql_query('SELECT * from '.$prefix.'gc'.$year.' where nomination='.$rolevalues[$subobj][0].' and tour='.($tour+1).' and nominee='.$untransfernominee.' and user_id=0 limit 0,1');
   				$a=mysql_fetch_array($result);
   				if($a["id"]!='') {
   					mysql_query('DELETE FROM '.$prefix.'gc'.$year.' where id='.$a["id"]);
   					err('Номинант успешно исключен из следующего тура.');
   				}
   				else {
   					err_red('Номинант не переведен в следующий тур. Откатывать нечего.');
   				}
    		}

    		$content2.='<table style="width:100%;"><tr><td align=center><br><h1>'.$rolevalues[$subobj][1].' - тур '.($tour+1).'</h1><br>';
            $content2.='<b>Наведите на номер критерия, чтобы посмотреть его описание.</b><br><br>'.$error.'
<table border=1 style="border: 1px solid black;border: 1px solid black; border-collapse:collapse;" class="results">
<tr>
<td><b>Номинанты\Критерии</b></td>';
            $allelems=$rolevalues[$subobj][2];
            $prevelem='';
            for($i=0;$i<count($allelems);$i++) {
            	if($allelems[$i][1]!='') {
					$content2.='<td title="'.decodesafe(encode($allelems[$i][1])).'">'.($i+1).'</td>';
					$prevelem=decodesafe(encode($allelems[$i][1]));
				}
				else {
					$content2.='<td title="'.$prevelem.'">'.($i+1).'</td>';
				}
            }
            $content2.='<td>Итого</td>
</tr>';
			function drawresults($nominee) {
				global
					$maxitogo,
					$allelems,
					$subobj,
					$jury_count,
					$allnominees,
					$allnomineesvotes,
					$allnomineesresults,
					$prefix;

                $itogo=0;
                if(count($allnominees)>5) {
                	$allcount=5;
                }
                else {
                	$allcount=count($allnominees);
                }
                for($i=0;$i<count($allelems);$i++) {
                	$sum=0;
                	for($j=0;$j<count($allnomineesvotes);$j++) {
                		$st=array_search($nominee,$allnomineesvotes[$j][$allelems[$i][0]]);
                		if($st!==false) {
                			$sum+=$allelems[$i][2]*(($allcount+1-$st)/$allcount);
                		}
                	}
                	$sum=$sum/$jury_count[$subobj];
                	$cont.='<td style="font-size: 8px;">'.$sum.'</td>';
                	$itogo+=$sum;
                }
                if($itogo>$maxitogo) {
                	$maxitogo=$itogo;
                }
                $allnomineesresults[]=array($nominee,$itogo);
                $cont.='<td>'.$itogo.'</td></tr>';
				return $cont;
			}

			unset($allgoodjury);
			$result2=mysql_query('SELECT * FROM '.$prefix.'gc'.$year.' WHERE nomination='.$rolevalues[$subobj][0].' AND tour='.$tour.' AND user_id!=0');
			while($b=mysql_fetch_array($result2)) {
				prepare_all_nomination_data($b["user_id"],$rolevalues[$subobj][0],$tour);
				if($nominationready) {
					$allgoodjury[]=$b["user_id"];
					$allnomineesvotes[]=$wholenominationdata;
				}
			}
			for($i=0;$i<count($allnominees);$i++) {
				$content2.='<tr><td>'.$allnomineesnames[$i].'</td>'.drawresults($allnominees[$i]);
			}
			$content2.='</table></td></tr></table>';
			foreach ($allnomineesresults as $key => $row) {
				$allnomineesresultssort[$key]  = $row[1];
			}
			array_multisort($allnomineesresultssort, SORT_DESC, $allnomineesresults);
			$content2.='<br><br><b>Номинанты, проходящие в следующий тур</b>:<br>';
			$i=1;
			$othenomin=true;
			$sumallnominees=0;
			for($j=0;$j<count($allnomineesresults);$j++) {
				$sumallnominees+=$allnomineesresults[$j][1];
			}
			$sumallnominees=$sumallnominees/100*5;
			$bottomborder=$allnomineesresults[0][1]-$sumallnominees;
			for($j=0;$j<count($allnomineesresults);$j++) {
				//if($allnomineesresults[$j][1]<$maxitogo-($maxitogo/100*10) && $othenomin) {
				if($allnomineesresults[$j][1]<$bottomborder && $othenomin) {
					$content2.='<br><b>Остальные номинанты</b>:<br>';
					$othenomin=false;
				}
				$nominname='';
				if($allnomineesresults[$j][0]!=0) {
					$result=mysql_query('SELECT * from '.$prefix.'roles where id='.$allnomineesresults[$j][0]);
					$a=mysql_fetch_array($result);
					$nominname=decode($a["sorter"]);
				}
				else {
					$nominname="Не вручать";
				}
				$content2.='<b>'.$i.'</b> место: '.$nominname.'&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;';
				$result=mysql_query('SELECT * from '.$prefix.'gc'.$year.' where nomination='.$subobj.' and tour='.($tour+1).' and nominee='.$allnomineesresults[$j][0]);
				$a=mysql_fetch_array($result);
				if($a["id"]=='') {
					$content2.='<a style="color: red; cursor: pointer;" OnClick="if (confirm(\'Вы уверены, что хотите перевести номинанта в следующий тур?\')){ document.location=\'?subobj='.$subobj.'&tour='.$tour.'&transfernominee='.$allnomineesresults[$j][0].'\'}">[перевести в следующий тур]</a><br>';
				}
				else {
					$content2.='<a style="color: green; cursor: pointer;" OnClick="if (confirm(\'Вы уверены, что хотите убрать номинанта из тура?\')){ document.location=\'?subobj='.$subobj.'&tour='.$tour.'&untransfernominee='.$allnomineesresults[$j][0].'\'}">[переведен: откатить?]</a><br>';
				}
				$i++;
			}
			$content2.='<br><b>Проголосовавшие академики</b>:<br>';
			$result2=mysql_query('SELECT * FROM '.$prefix.'users where id in (SELECT user_id FROM '.$prefix.'gc'.$year.' where nomination='.$rolevalues[$subobj][0].' and tour='.$tour.') order by fio');
			while($b=mysql_fetch_array($result2)) {
            	$content2.='<a href="/gc'.$year.'.php?subobj='.$subobj.'&tour='.$tour.'&jury='.$b["id"].'">'.usname($b,true).'</a> ';
            	if(in_Array($b["id"],$allgoodjury)) {
            		$content2.='<span title="Академик заполнил все данные" style="color: green; font-weight: bold;">&#8730</span>';
            	}
            	else {
					$content2.='<span title="Академик не заполнил все данные" style="color: red; font-weight: bold;">X</span>';
            	}
            	$content2.='<br>';
			}
			$content2.='<br>';
    	}
    	else {
    		// номинант-номинация-академик
			$user=encode($_GET["jury"]);

			$result=mysql_query('SELECT * FROM '.$prefix.'users where id='.$user);
			$a=mysql_fetch_array($result);

    		$content2.='<center><h1>'.usname($a,true).' - '.$rolevalues[$subobj][1].' - тур '.($tour+1).'</h1></center><br>';

    		$content2.='';
            $allelems=$rolevalues[$subobj][2];
            prepare_all_nomination_data($user,$rolevalues[$subobj][0],$tour);
			$data=$wholenominationdata;

            $content2.='<div class="fieldname">Комментарий</div>'.decodesafe(encode($data['comment'])).'<br>';

            for($i=0;$i<count($allelems);$i++) {
            	$content2.='<div class="fieldname">'.$allelems[$i][1].'</div><ul>';
            	for($j=0;$j<$howmanynominees;$j++) {
            		if(isset($data[$allelems[$i][0]][$j])) {
            			$content2.='<li>'.$allnomineesnames[array_search($data[$allelems[$i][0]][$j],$allnominees)];
            		}
            	}
            	$content2.='</ul>';
            	$content2.='<br>';
            }

            $content2.='<br><a style="color: red; cursor: pointer;" OnClick="if (confirm(\'Вы уверены, что хотите полностью удалить результаты голосования академика по данной номинации?\')){ document.location=\'?subobj='.$subobj.'&tour='.$tour.'&deletevote='.$user.'\'}"><b>удалить</b></a><br><br>';
    	}
	}
}
else {
	$content2.='<div style="float: left; padding-top: 12px; width: 162px;">
<form action="/gc2012.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="action" value="login">
Логин:<br>
<input type="text" name="login" class="inputtext" tabindex="1"><br>
<input type="checkbox" name="remember" id="remember" class="checkbox" checked style="vertical-align: middle" tabindex="3"> <label for="remember" style="font-size: 11pt;">Запомнить</label>
</div>
<div style="float: left; padding-top: 12px; padding-left: 10px; width: 162px;">
Пароль:<br>
<input type="password" name="pass" class="inputtext" tabindex="2"><br>
<a href="'.$server_absolute_path.'start/action=remind" style="font-size: 11pt;">Напомнить пароль?</a>
</div>
<div style="float: left; width: 100px; padding-top: 35px; text-align: right;">
<span class="gui-btn"><span><span>Войти</span><input type="submit" onSubmit="this.disabled=true" tabindex="4"></span></span></form></div>
';
}

if($content2!='') {
	$content='<!doctype html public \'-//w3c//dtd html 4.01//en\' \'http://www.w3.org/tr/html4/strict.dtd\'>
<html>
<head>
<title>Золотой куб '.$year.'</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="author" content="©еть">
<link rel="stylesheet" type="text/css" href="http://www.allrpg.info/libraries/jquery/plugins/jquery-ui-1.10.0.custom/css/smoothness/jquery-ui-1.10.0.custom.min.css">
<script type="text/javascript" src="http://www.allrpg.info/libraries/jquery/jquery-1.9.0.min.js"></script>
<script type="text/javascript" src="http://www.allrpg.info/libraries/jquery/plugins/jquery-ui-1.10.0.custom/js/jquery-ui-1.10.0.custom.min.js"></script>
<script type="text/javascript" src="http://www.allrpg.info/libraries/jquery/plugins/noty/jquery.noty.js"></script>
<script type="text/javascript" src="http://www.allrpg.info/libraries/jquery/plugins/noty/layouts/center.js"></script>
<script type="text/javascript" src="http://www.allrpg.info/libraries/jquery/plugins/noty/layouts/bottomRight.js"></script>
<script type="text/javascript" src="http://www.allrpg.info/libraries/jquery/plugins/noty/themes/default.js"></script>
<link rel="stylesheet" type="text/css" href="http://www.allrpg.info/main_new.css">
<script>
$(document).ready(function(){
	$(".link_edit").click(function() {
		the_link=$(this).prev().prev().prev().prev().prev();
		the_form=$("#links_"+the_link.attr("nominee_id"));
		$("#link_h_"+the_link.attr("nominee_id")).text("Изменить");
		the_form.children("#link_submit").val("Сохранить");
		the_form.children("#link_action").val("votelinkedit");
		the_form.children("#link_text").val(the_link.text());
		the_form.children("#link_content").val(the_link.attr("href"));
		the_form.children("#link_id").val(the_link.attr("link_id"));
	});

	$.noty.defaults.layout="bottomRight";
	$.noty.defaults.closeWith=["click"];
	for(var key in errors) {
		var tmt=errors[key][1].length*25;
		if(tmt<5000) {
			tmt=5000;
		}
		noty({text: errors[key][1],type:errors[key][0],timeout:tmt});
	}
	errors=[];
});
</script>
<style>
h2 {margin: 0px; padding: 0px; margin-bottom: 10px; font-size: 18px;}
ul {padding-bottom: 0px;}
.error_red {width: auto; margin-top: 0px;}
.error {width: auto; margin-top: 0px;}
table.results td {padding: 5px;border: 1px black solid;}
div.fieldname {text-align:justify;}
div.separated0 {border-bottom: 1px solid black; background-color: #999999; color: white; padding-top: 2px; padding-bottom: 2px;}
div.separated1 {border-bottom: 1px solid black; background-color: #cccccc; color: black; padding-top: 2px; padding-bottom: 2px;}
div.criteria {font-style: italic; font-weight: bold; padding-bottom: 5px; padding-top: 5px; background-color: #0000aa; color:white;}
.sm {font-size: 10px;}
.sm2 {font-size: 12px;}
.nomineelinks, .aboutnominee {display:none; border: 1px black solid; border-top: none; padding: 5px; background-color: white;}
.nomineename {padding: 10px; background-color: #003377; border: 1px dotted white; color: white; float: left; width: 262px; font-size: 14px;}
.nomineechoice {padding: 10px; border: 1px dotted black; width: 262px; margin-bottom: 3px; text-align: left; font-size: 14px;}
.nomineechoicemade {padding: 10px; background-color: #003377; border: 1px dotted white; color: white; width: 262px;  margin-bottom: 3px; text-align: left; font-size: 14px;}
.nomineechoicecross {position: absolute; font-weight: bold; font-size: 8px; margin-top: -9px; margin-left: 238px;}
.nomineedata {padding: 10px 0px 10px 2px; float: right; width: 30px;}
.nomineesum {position: absolute; font-size: 11px; color: white; margin-left: -34px; margin-top: -26px; text-align: right; width: 23px;}
.nomineesum2 {position: absolute; font-size: 11px; color: white; margin-left: -34px; margin-top: -7px; text-align: right; width: 23px;}
#nomineemove {display: none; position: absolute; padding: 10px; background-color: #003377; border: 1px dotted white; color: white; width: 242px; top: 0px; left: 0px;}
.submiter {width: 100%; height: 40px; font-weight: bold; font-size: 14px; font-family: Verdana}
.finish {margin-right: 1px; padding: 0px 5px; border-left: 2px dotted red; border-right: 2px dotted red;}
.finished {margin-right: 1px; padding: 0px 5px; border-left: 2px dotted green; border-right: 2px dotted green;}
.placehold {background-color: #990000; color: white; font-size: 10px; padding: 2px; position: absolute; display: block;}
body {background: none; font-size: 80%;}
div.fieldname {float: none; width: auto; margin-bottom: 5px;}
div.sm2 input {width: auto;}
</style>
</head>

<body '.$bodybody.'>
'.$content2;

	$error_array='<script>
errors=[];';
	foreach($_SESSION['errors'] as $error) {
		$error_array.='errors.push(Array("'.$error[0].'","'.str_replace('"','\"',$error[1]).'"));';
	}
	unset($_SESSION['errors']);
	$error_array.='</script>';

	$content.=$error_array.'
</body>
</html>';

	print($content);
}

stop_mysql();
# Разрыв соединения с MySQL-сервером

function gc_unmakevirtual($a)
{
	$result=array();

	if($a!='') {
		preg_match('#\[comment\]\[([^]]*)\]|#iU', $a, $match);
		$result['comment']=decode($match[1]);

		preg_match_all('#\[(\d+)\]\[([^]]*)\]#', $a, $matches);
		foreach($matches[0] as $key=>$value) {
			$ids=explode(',',$matches[2][$key]);
			foreach($ids as $value) {
				if($value!=='') {
					$result[$matches[1][$key]][]=$value;
				}
			}
		}
	}

	return($result);
}
function makeallinfo() {
	global
		$subobj,
		$rolevalues,
		$_POST;

	$elems=$rolevalues[$subobj][2];
	$alldata=$_POST["nominee"];
	$comment=encode($_POST["comment"]);
	$allinfo='[comment]['.$comment.']|';
	for($i=0;$i<count($elems);$i++) {
		$allinfo.='['.$elems[$i][0].'][';
		for($j=0;$j<5;$j++) {
			if($j>0 && isset($alldata[$elems[$i][0]][$j])) {
				$allinfo.=',';
			}
			if(isset($alldata[$elems[$i][0]][$j])) {
				$allinfo.=encode($alldata[$elems[$i][0]][$j]);
			}
		}
		$allinfo.=']|';
	}
	return $allinfo;
}
function prepare_all_nomination_data($user_id,$nomination,$preptour) {
	global
		$year,
		$prefix,
		$allnominees,
		$howmanynominees,
		$nominationready,
		$nominationerrorsfields,
		$wholenominationdata,
		$rolevalues,
		$allresults;

	if(count($allnominees)<5) {
		$howmanynominees=count($allnominees);
	}
	else {
		$howmanynominees=5;
	}
	$nominationready=true;
	$nominationerrorsfields=Array();
	$allresults=Array();
	$result2=mysql_query('SELECT * FROM '.$prefix.'gc'.$year.' WHERE user_id='.$user_id.' AND nomination='.$rolevalues[$nomination][0].' AND tour='.$preptour.' AND nominee IS NULL');
	$b=mysql_fetch_array($result2);
	if($b["id"]=='' && $preptour>0) {
		$result2=mysql_query('SELECT * FROM '.$prefix.'gc'.$year.' where user_id='.$user_id.' and nomination='.$rolevalues[$nomination][0].' and tour=0 and nominee IS NULL');
		$b=mysql_fetch_array($result2);
	}
	$wholenominationdata=gc_unmakevirtual($b["allinfo"]);
	$elems=$rolevalues[$nomination][2];
	$c=$wholenominationdata;
	for($d=0;$d<count($allnominees);$d++) {
		$allresults[$allnominees[$d]]=0;
	}

	if($preptour==0 && $c['comment']=='') {
		$nominationerrorsfields['comment']=true;
		$nominationready=false;
	}

	for($i=0;$i<count($elems);$i++) {
		$data_in_a_nonobligatory_field=false;
		for($d=0;$d<$howmanynominees;$d++) {
			if($elems[$i][3] && $c[$elems[$i][0]][$d]!='') {
				$data_in_a_nonobligatory_field=true;
			}

			if(!$elems[$i][3] && $c[$elems[$i][0]][$d]=='') {
				$nominationerrorsfields[$elems[$i][0]]=true;
				$nominationready=false;
			}
			elseif($elems[$i][3] && $c[$elems[$i][0]][$d]=='' && $data_in_a_nonobligatory_field) {
				$nominationerrorsfields[$elems[$i][0]]=true;
				$nominationready=false;
			}
			else {
				if($c[$elems[$i][0]][$d]!='') {
					$allresults[$c[$elems[$i][0]][$d]]+=$elems[$i][2]*(($howmanynominees-$d)/$howmanynominees);
				}
			}
		}
	}
}
?>