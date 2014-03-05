<?php
if($dynrequest==1) {
 	if(strlen($qwerty)<3 || encode_to_cp1251($qwerty)=='Введите не менее 3 символов для поиска') {
 		dynamic_err_one('error','Требуется более 3 символов.');
 	}
 	else {
 		dynamic_err(array(),'submit');
 	}
}

$pagetitle=h1line('Инфотека');

$content2.='<div class="narrow">
<a name="mainsearchlink"></a>
<h1>Поиск по инфотеке</h1>';

if($_SESSION["user_id"]!='') {
	$mainsearch=createElem(Array(
			'name'	=>	"mainsearch",
			'sname'	=>	"в разделах",
			'type'	=>	"multiselect",
			'values'	=>	Array(Array('10','искать везде'),Array('2','среди Ф.И.О. и никнеймов пользователей'),Array('1','среди событий, их описаний и их организаторов'),Array('3','среди мастерских групп'),Array('4','среди названий полигонов'),Array('5','среди названий и текстов отчетов'),Array('6','среди названий, текстов и авторов статей')),
			'read'	=>	10,
			'write'	=>	100,
			'default'	=>	'-10-',
			'width'	=>	'86%'
		)
	);
}
else {
	$mainsearch=createElem(Array(
			'name'	=>	"mainsearch",
			'sname'	=>	"в разделах",
			'type'	=>	"multiselect",
			'values'	=>	Array(Array('10','искать везде'),Array('1','среди событий и их описаний'),Array('3','среди мастерских групп'),Array('4','среди названий полигонов'),Array('5','среди названий и текстов отчетов'),Array('6','среди названий и текстов статей')),
			'read'	=>	10,
			'write'	=>	100,
			'default'	=>	'-10-',
			'width'	=>	'86%'
		)
	);
}

$content2.='<form action="'.$curdir.'#mainsearchlink" method="post" enctype="multipart/form-data">
<input type="hidden" name="action" value="mainsearch">
<input type="text" class="qwerty" autocomplete="off" name="qwerty" value="'.($action=="mainsearch"?$qwerty:'').'" placehold="Введите не менее 3 символов для поиска"><br>
<button class="main" style="float: right">Найти</button>';
$mainsearch->setVal('',true);
$content2.='<div class="fieldvalue" id="name_mainsearch"></div><div class="fieldvalue" style="padding-left:0px">'.$mainsearch->draw(1,"write");
$innersearch=false;
for($i=1;$i<=10;$i++) {
	if(encode($_POST["mainsearch"][$i])=="on") {
		$innersearch=true;
	}
}
if(!$innersearch || encode($_POST["mainsearch"][10])=="on") {
	$_POST["mainsearch"][1]="on";
	$_POST["mainsearch"][3]="on";
	$_POST["mainsearch"][4]="on";
	$_POST["mainsearch"][5]="on";
	$_POST["mainsearch"][6]="on";
	if($_SESSION["user_id"]!='') {
    	$_POST["mainsearch"][2]="on";
	}
}

$content2.='</div></form>';

function showphrase($content,$phrase) {
	$content=strip_tags(decode($content));
	$phrasepos=stripos($content,$phrase);
	if($phrasepos>10) {
		$content='&#8230;'.substr($content,$phrasepos-10,strlen($content));
	}
	if(strlen($content)-150>0) {
		$content=substr($content,0,150).'&#8230;';
	}
	$content=str_ireplace($phrase,'<b>'.substr($content,stripos($content,$phrase),strlen($phrase)).'</b>',$content);
	$content='<div>'.$content.'</div>';

	return $content;
}

if($action=="mainsearch" && strlen($qwerty)>2) {
	unset($searchresults);

	if(encode($_POST["mainsearch"][2])=="on" && $_SESSION["user_id"]!='') {
		$i=0;
		if(encode($_POST["mainsearch"][2])=="on") {
			$result=mysql_query("SELECT * FROM ".$prefix."users WHERE (fio LIKE '%".$qwerty."%' and hidesome NOT LIKE '%-10-%') OR (nick LIKE '%".$qwerty."%' and hidesome NOT LIKE '%-0-%')");
		}
		else {
			$result=mysql_query("SELECT * FROM ".$prefix."users WHERE ((fio LIKE '%".$qwerty."%' and hidesome NOT LIKE '%-10-%') OR (nick LIKE '%".$qwerty."%' and hidesome NOT LIKE '%-0-%')) and (id IN (SELECT creator_id FROM ".$prefix."blogs WHERE deleted=0) OR id IN (SELECT creator_id FROM ".$prefix."blog_rights WHERE moderated='1' and rights=3))");
		}
		while($a = mysql_fetch_array($result)) {
			$searchresults[$i]=Array('<b>'.usname($a,true,true).'</b>');
			if(encode($_POST["mainsearch"][1])=="on") {
				$result3=mysql_query("SELECT * FROM ".$prefix."allgames WHERE user_id=".$a["sid"]." and parent>0");
				while($c = mysql_fetch_array($result3)) {
					$result2=mysql_query("SELECT * FROM ".$prefix."allgames WHERE id=".$c["parent"]);
					$b = mysql_fetch_array($result2);
					$searchresults[$i][1][]='организатор события «<a href="'.$server_absolute_path_info.'events/'.$b["id"].'/">'.decode($b["name"]).'</a>»';
				}
			}
			if(encode($_POST["mainsearch"][3])=="on" && decode($a["ingroup"])!='') {
                unset($hisgroups);
                $hisgroups=explode(',',decode($a["ingroup"]));
				for($j=0;$j<count($hisgroups);$j++) {
					if(substr($hisgroups[$j],0,1)==' ')	{
						$hisgroups[$j]=str_replace('&','-and-',substr($hisgroups[$j],1,strlen($hisgroups[$j])));
					}
					$searchresults[$i][1][]='состоит в мастерской группе <a href="'.$server_absolute_path_info.'mg/'.$hisgroups[$j].'/">'.$hisgroups[$j].'</a>';
				}
				unset($hisgroups);
			}
			if(encode($_POST["mainsearch"][5])=="on") {
				$result3=mysql_query("SELECT * FROM ".$prefix."reports WHERE user_id=".$a["id"]);
				while($c = mysql_fetch_array($result3)) {
					$result2=mysql_query("SELECT * FROM ".$prefix."allgames WHERE id=".$c["game"]);
					$b = mysql_fetch_array($result2);
					if($c["name"]=='') {
						$c["name"]="<i>без названия</i>";
					}
					else {
						$c["name"]=decode($c["name"]);
					}
					$searchresults[$i][1][]='автор отчета «<a href="'.$server_absolute_path_info.'reports/'.$c["id"].'/">'.$c["name"].'</a>» по событию «<a href="'.$server_absolute_path_info.'events/'.$b["id"].'/">'.$b["name"].'</a>»';
				}
			}
            if(encode($_POST["mainsearch"][6])=="on") {
				$result3=mysql_query("SELECT * FROM ".$prefix."articles WHERE author LIKE '% ".$a["sid"].",%' or author LIKE '".$a["sid"].",%' or author LIKE '%, ".$a["sid"]."' or author LIKE '%,".$a["sid"]."' or author='".$a["sid"]."' order by date desc");
				while($c = mysql_fetch_array($result3)) {
					$author='';

					if($c["author"]!='') {
						$author=', автор(-ы): ';
						$tryauthor=explode(',', decode($c["author"]));
						if(count($tryauthor)>1) {
							$searchresults[$i][1][]='со-автор статьи «<a href="'.$server_absolute_path_info.'articles/'.$c["id"].'/subobj='.$c["parent"].'">'.decode($c["name"]).'</a>»';
						}
						else {
							$searchresults[$i][1][]='автор статьи «<a href="'.$server_absolute_path_info.'articles/'.$c["id"].'/subobj='.$c["parent"].'">'.decode($c["name"]).'</a>»';
						}
					}
				}
			}

			$i++;
		}
	}

	if(encode($_POST["mainsearch"][1])=="on") {
		if(encode($_POST["mainsearch"][2])=="on" && $_SESSION["user_id"]!='') {
			$result=mysql_query("SELECT * FROM ".$prefix."allgames WHERE name LIKE '%".$qwerty."%' and parent>0");
			while($a = mysql_fetch_array($result)) {
				$result2=mysql_query("SELECT * FROM ".$prefix."allgames WHERE id=".$a["parent"]);
				$b = mysql_fetch_array($result2);
				$searchresults[]='<b>'.decode($a["name"]).'</b>, организатор события «<a href="'.$server_absolute_path_info.'events/'.$b["id"].'/">'.decode($b["name"]).'</a>»';
			}
		}
		$result=mysql_query("SELECT * FROM ".$prefix."allgames WHERE (name LIKE '%".$qwerty."%' OR descr LIKE '%".$qwerty."%') and parent=0 order by id desc");
		while($a = mysql_fetch_array($result)) {
			if(strpos($a["name"],$qwerty)!==false) {
				$searchresults[]='Событие «<a href="'.$server_absolute_path_info.'events/'.$a["id"].'/"><b>'.decode($a["name"]).'</b></a>»';
			}
			else {
				$searchresults[]='Описание события «<a href="'.$server_absolute_path_info.'events/'.$a["id"].'/"><b>'.decode($a["name"]).'</b></a>»'.showphrase($a["descr"],$qwerty);
			}
		}
		$result=mysql_query("SELECT * FROM ".$prefix."sites where testing!='1' and title LIKE '%".$qwerty."%'");
		while($a = mysql_fetch_array($result)) {
			$searchlink='Проект «<a href="'.($a["usetemp"]!=2?'http://'.$a["path"].'.allrpg.info':$server_absolute_path.'siteroles/'.$a["id"].'/').'"><b>'.decode($a["title"]).'</b></a>»';
			if($a["usetemp"]!=1 && date($a["datefinish"])>=date("Y-m-d") && $a["status2"]==2) {
				$searchlink.='<div>';
				if($_SESSION["user_id"]!='') {
					$result2=mysql_query("SELECT * FROM ".$prefix."users where id=".$_SESSION["user_id"]);
					$b = mysql_fetch_array($result2);
					if($b["phone2"]=='' || $b["fio"]=='' || $b["city"]==0 || $b["birth"]=='0000-00-00') {
						$searchlink.='<a href="'.$server_absolute_path.'profile/redirectobj=order&redirectid='.$a["id"].'">';
					}
					else {
						$searchlink.='<a href="'.$server_absolute_path.'order/act=add&subobj='.$a["id"].'">';
					}
				}
				else {
					$searchlink.='<a href="'.$server_absolute_path.'register/redirectobj=order&redirectid='.$a["id"].'">';
				}
				$searchlink.='Подать заявку</a></div>';
			}
			$searchresults[]=$searchlink;
		}
	}

	if(encode($_POST["mainsearch"][3])=="on")
	{
		$result=mysql_query("SELECT * FROM ".$prefix."users WHERE ingroup LIKE '%".$qwerty."%'");
		while($a = mysql_fetch_array($result))
		{
			$should=true;
			$rightmg='';
			$hisgroups=explode(',',$a["ingroup"]);
			for($j=0;$j<count($hisgroups);$j++)
			{
				if(substr($hisgroups[$j],0,1)==' ')
				{
					$hisgroups[$j]=substr($hisgroups[$j],1,strlen($hisgroups[$j]));
				}
				if(stripos($hisgroups[$j],$qwerty)!==false)
				{
					$rightmg=$hisgroups[$j];
				}
				$rightmg=str_ireplace('&quot;','',$rightmg);
				$rightmg=str_ireplace('МГ ','',$rightmg);
				$rightmg=str_ireplace('МО ','',$rightmg);
				$rightmg=str_ireplace('ТГ ','',$rightmg);
				$rightmg=str_ireplace('ТО ','',$rightmg);
				$rightmg=str_ireplace('ТК ','',$rightmg);
				$rightmg=str_ireplace('ТМ ','',$rightmg);
			}
			if($rightmg!='')
			{
				for($j=0;$j<count($foss);$j++)
				{
					if(strtolower($foss[$j])==strtolower($rightmg))
					{
						$should=false;
					}
				}
				if($should)
				{
					$rightmg2=str_replace('&','-and-',$rightmg);
					$searchresults[]='Мастерская группа <a href="'.$server_absolute_path_info.'mg/'.$rightmg2.'/"><b>'.$rightmg.'</b></a>';
					$foss[]=$rightmg;
					$i++;
				}
			}
		}

		$result=mysql_query("SELECT * FROM ".$prefix."allgames WHERE mg LIKE '%".$qwerty."%'");
		while($a = mysql_fetch_array($result))
		{
			$should=true;
			$rightmg='';
			$hisgroups=explode(',',$a["mg"]);
			for($j=0;$j<count($hisgroups);$j++)
			{
				if(substr($hisgroups[$j],0,1)==' ')
				{
					$hisgroups[$j]=substr($hisgroups[$j],1,strlen($hisgroups[$j]));
				}
				if(stripos($hisgroups[$j],$qwerty)!==false)
				{
					$rightmg=$hisgroups[$j];
				}
				$rightmg=str_ireplace('&quot;','',$rightmg);
				$rightmg=str_ireplace('МГ ','',$rightmg);
				$rightmg=str_ireplace('МО ','',$rightmg);
				$rightmg=str_ireplace('ТГ ','',$rightmg);
				$rightmg=str_ireplace('ТО ','',$rightmg);
				$rightmg=str_ireplace('ТК ','',$rightmg);
				$rightmg=str_ireplace('ТМ ','',$rightmg);
			}
			if($rightmg!='')
			{
				for($j=0;$j<count($foss);$j++)
				{
					if(strtolower($foss[$j])==strtolower($rightmg))
					{
						$should=false;
					}
				}
				if($should)
				{
					$rightmg2=str_replace('&','-and-',$rightmg);
					$searchresults[]='Мастерская группа <a href="'.$server_absolute_path_info.'mg/'.$rightmg2.'/"><b>'.$rightmg.'</b></a>';
					$rightmg=str_replace('-and-','&',$rightmg);
					$foss[]=$rightmg;
					$i++;
				}
			}
		}
	}

	if(encode($_POST["mainsearch"][4])=="on")
	{
		$result=mysql_query("SELECT * FROM ".$prefix."areas WHERE name LIKE '%".$qwerty."%'");
		while($a = mysql_fetch_array($result)) {
			$searchresults[]='Полигон <a href="'.$server_absolute_path_info.'areas/'.$a["id"].'/"><b>'.decode($a["name"]).'</b></a>';
		}
	}

	if(encode($_POST["mainsearch"][5])=="on") {
		$result=mysql_query("SELECT * FROM ".$prefix."reports WHERE content LIKE '%".$qwerty."%' or name LIKE '%".$qwerty."%'");
		while($a = mysql_fetch_array($result))
		{
			$author='';
			$result2=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$a["user_id"]);
			$b = mysql_fetch_array($result2);
			$author=', автор '.usname($b,true,true);

			$event='';
			$result2=mysql_query("SELECT * FROM ".$prefix."allgames WHERE id=".$a["game"]);
			$b = mysql_fetch_array($result2);
			$event=' по событию «<a href="'.$server_absolute_path_info.'events/'.$b["id"].'/">'.decode($b["name"]).'</a>»';

			if($a["name"]!='') {
				$a["name"]='<b>'.decode($a["name"]).'</b>';
			}
			else {
				$a["name"]='<i>без названия</i>';
			}
			if(eregi($qwerty,$a["name"])) {
				$searchresults[]='Отчет «<a href="'.$server_absolute_path_info.'reports/'.$a["id"].'/">'.$a["name"].'</a>»'.$event.$author;
			}
			else {
				$searchresults[]='Текст отчета «<a href="'.$server_absolute_path_info.'reports/'.$a["id"].'/">'.$a["name"].'</a>»'.$event.$author.showphrase($a["content"],$qwerty);
			}
		}
	}
	if(encode($_POST["mainsearch"][6])=="on") {
		$result=mysql_query("SELECT * FROM ".$prefix."articles WHERE content LIKE '%".$qwerty."%' or name LIKE '%".$qwerty."%' order by date desc");
		while($a = mysql_fetch_array($result)) {
			$author='';

			if($a["author"]!='')
			{
				$author=', автор(-ы): ';
				$tryauthor=explode(',', decode($a["author"]));
				for($i=0;$i<count($tryauthor);$i++)
				{
					if($i>0)
					{
						$author.=', ';
					}
					$checker=$tryauthor[$i];
					settype($tryauthor[$i], "integer");
					if(is_int($tryauthor[$i]) && $tryauthor[$i]!=0 && $tryauthor[$i]!='')
					{
						$result2=mysql_query("SELECT * FROM ".$prefix."users WHERE sid=".$tryauthor[$i]);
						$b = mysql_fetch_array($result2);
						$author.=usname($b, true, true);
					}
					else
					{
						$author.=decode($checker);
					}
				}
			}

			if(eregi($qwerty,$a["content"])) {
				$searchresults[]='Текст статьи «<a href="'.$server_absolute_path_info.'articles/'.$a["id"].'/subobj='.$a["parent"].'"><b>'.decode($a["name"]).'</b></a>»'.$author.showphrase($a["content"],$qwerty);
			}
			else {
				$searchresults[]='Статья «<a href="'.$server_absolute_path_info.'articles/'.$a["id"].'/subobj='.$a["parent"].'"><b>'.decode($a["name"]).'</b></a>»'.$author;
			}
		}

		if($_SESSION["user_id"]!='') {
			$result=mysql_query("SELECT * FROM ".$prefix."articles WHERE author LIKE '%".$qwerty."%' order by date desc");
			while($a = mysql_fetch_array($result)) {
				$author='';

				if($a["author"]!='') {
					$author=', автор(-ы): ';
					$tryauthor=explode(',', decode($a["author"]));
					for($i=0;$i<count($tryauthor);$i++) {
						if($i>0) {
							$author.=', ';
						}
						$checker=$tryauthor[$i];
						settype($tryauthor[$i], "integer");
						if(is_int($tryauthor[$i]) && $tryauthor[$i]!=0 && $tryauthor[$i]!='') {
							$result2=mysql_query("SELECT * FROM ".$prefix."users WHERE sid=".$tryauthor[$i]);
							$b = mysql_fetch_array($result2);
							$author.=usname($b, true, true);
						}
						else {
							if(eregi($qwerty,$checker)) {
								$author.='<b>'.decode($checker).'</b>';
							}
							else {
								$author.=decode($checker);
							}
						}
					}
				}

				$searchresults[]='Статья «<a href="'.$server_absolute_path_info.'articles/'.$a["id"].'/subobj='.$a["parent"].'">'.decode($a["name"]).'</a>»'.$author;
			}
		}
	}
}
elseif($action=="tagsearch" && $qwerty!='') {
	unset($searchresults);

	$string=$qwerty;
	$string=str_ireplace(" И "," AND ",$string);
	$string=str_ireplace(" ИЛИ "," OR ",$string);
	$string=str_ireplace(" НЕ "," AND NOT ",$string);
	if(substr($string,0,9)==" AND NOT ") {
		$string=substr($string,5,strlen($string));
	}
	$result=mysql_query("SELECT * FROM ".$prefix."tags order by char_length(name) desc");
	while($a = mysql_fetch_array($result)) {
		if(strpos($string,decode($a["name"]))!==false) {
			$string=str_replace(decode($a["name"]),"tags LIKE '%-".$a["id"]."-%'",$string);
		}
	}
	$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."articles where ".$string);
	$a = mysql_fetch_array($result);
	if($a[0]>0) {
		$encodetag=$string;
	}

	$result=mysql_query("SELECT * FROM ".$prefix."articles WHERE ".$encodetag." order by id desc");
	while($a = mysql_fetch_array($result)) {
		$author='';

		if($a["author"]!='') {
			$author=', автор(-ы): ';
			$tryauthor=explode(',', decode($a["author"]));
			for($i=0;$i<count($tryauthor);$i++) {
				if($i>0) {
					$author.=', ';
				}
				$checker=$tryauthor[$i];
				settype($tryauthor[$i], "integer");
				if(is_int($tryauthor[$i]) && $tryauthor[$i]!=0 && $tryauthor[$i]!='') {
					$result2=mysql_query("SELECT * FROM ".$prefix."users WHERE sid=".$tryauthor[$i]);
					$b = mysql_fetch_array($result2);
					$author.=usname($b, true, true);
				}
				else {
					$author.=decode($checker);
				}
			}
		}
		$tags=', <b>теги</b>: ';
		$result2=mysql_query("SELECT * FROM ".$prefix."tags order by name");
		while($b = mysql_fetch_array($result2)) {
   			if(eregi('-'.$b["id"].'-',$a["tags"])) {
   				$tags.=strtolower(decode($b["name"])).', ';
   			}
		}
		$tags=substr($tags,0,strlen($tags)-2);

		$searchresults[]='Статья «<a href="'.$server_absolute_path_info.'articles/'.$a["id"].'/subobj='.$a["parent"].'"><b>'.$a["name"].'</b></a>»'.$author.$tags;
	}
}

if($action=="mainsearch") {
	$content2.='
<h1>Результаты поиска</h1>';
	if($action=="mainsearch" && strlen($qwerty)>2) {
		if(count($searchresults)>0) {
			$content2.='<ol class="searchresult">';
			for($i=0;$i<count($searchresults);$i++) {
				if(is_array($searchresults[$i])) {
					$content2.='<li>'.$searchresults[$i][0];
					if(count($searchresults[$i][1])>0) {
						$content2.='<ul>';
						for($j=0;$j<count($searchresults[$i][1]);$j++) {
							$content2.='<li>'.$searchresults[$i][1][$j];
						}
						$content2.='</ul>';
					}
				}
				else {
					$content2.='<li>'.$searchresults[$i];
				}
			}
			$content2.='</ol>';
		}
		else {
			$content2.='
<b>Совпадений не найдено</b>.<br>
Поиск по пользователям работает исключительно для залогиненных пользователей.<br>
Кроме того, поиск по Инфотеке работает только с точными формами слов и словосочетаний.<br>
Если Вы пытались найти словосочетание, попробуйте оставить одно основное слово и убрать у него окончание. Если Вы пытались найти одно слово, просто уберите окончание. Например: вместо «отчет по Ведьмаку» введите «Ведьмак», а вместо «битвы» – «битв». Это расширит диапазон поиска.<br>
Если это не помогает (допустим, Вам нужен отчет, название которого Вы не знаете, но знаете автора и событие), Вы сможете найти нужную информацию с помощью фильтров в соответствующем разделе.
';
		}
	}
	else {
 		$content2.='<b>Пожалуйста, введите не менее 3 символов для поиска!</b>
';
	}
}

$content2.='<br><a name="tagsearch"></a>
<h1>Поиск статей по тегам</h1>

<form action="'.$curdir.'#tagsearch" method="post" enctype="multipart/form-data">
<input type="hidden" name="action" value="tagsearch">
<input type="text" class="qwerty" name="qwerty" value="'.($action=="tagsearch"?$qwerty:'').'" placehold="Выберите теги для поиска" id="qwerty2">
<div id="qwerty2_question"><b><a onClick="$(\'#qwerty2_help\').toggle();">?</a></b></div>
<br>
<div id="qwerty2_help" style="text-align: left">
Кроме тегов можно использовать следующие команды для формирования выборки:
<ul>
<li>«<b>и</b>» – будут выводиться только те статьи, в которых есть все перечисленные через «и» теги;
<li>«<b>или</b>» – будут выводиться те статьи, в которых есть хотя бы один из перечисленных через «или» тегов;
<li>«<b>не</b>» – не будут выводиться статьи, содержащие приведенный после «не» тег;
<li>«<b>(</b>» – открывает группировку;
<li>«<b>)</b>» – закрывает группировку.
</ul>
Пример: <b>(правила и боевка) не (страйкбол или бугурт)</b> – будут выведены все статьи, содержащие и тег «правила», и тег «боевка» одновременно, но не содержащие при этом ни тега «страйкбол», ни тега «бугурт».
</div>
<div class="cloud">
<script>
function dotag(name) {
	if($("#qwerty2").val()=="Выберите теги для поиска") {
		$("#qwerty2").val(name);
		$("#qwerty2").css("color","black");
	}
	else {
		$("#qwerty2").val($("#qwerty2").val()+" и "+name);
	}
	$("#qwerty2").trigger("change");
}
</script>
<div style="float: right; text-align: right;"><button class="main">Найти</button><br><button class="nonimportant" onClick="$(\'#qwerty2\').val(\'\');$(\'#qwerty2\').trigger(\'change\');">Очистить</button></div>';

$result=mysql_query("SELECT * FROM ".$prefix."tags order by name asc");
while($a = mysql_fetch_array($result))
{
	$result2=mysql_query("SELECT COUNT(id) FROM ".$prefix."articles where tags like '%-".$a["id"]."-%'");
	$b = mysql_fetch_array($result2);
	$alltags[$a["id"]]=$b[0];
	$totaltags+=$b[0];
}

$result=mysql_query("SELECT * FROM ".$prefix."tags order by name asc");
while($a = mysql_fetch_array($result))
{
	$size=1+(($alltags[$a["id"]]/$totaltags)*8);
	$content2.='<a onClick="dotag(\''.$a["name"].'\')" style="font-size: '.$size.'em;">'.decode($a["name"]).'</a> ';
}
$content2.='</div></form>
';

if($action=="tagsearch") {
	$content2.='
<h1>Результаты поиска</h1>
';
	if($action=="tagsearch" && $qwerty!='' && encode_to_cp1251($qwerty)!='Выберите теги для поиска') {
		if(count($searchresults)>0) {
			$content2.='<ol class="searchresult">';
			for($i=0;$i<count($searchresults);$i++) {
				$content2.='<li>'.$searchresults[$i];
			}
			$content2.='</ol>';
		}
		else {
			$content2.='
<b>Сообщения по выборке не найдены или выборка сделана в неверном формате.</b><br>
Если Вы вводили выборку вручную, а не выбирали из облака тегов, Вы могли ошибиться в написании тегов или в формате ввода.<br>
Ознакомьтесь с помощью по вводу тегов (щелкните на знак вопроса справа от поля «Выборка»).
';
		}
	}
	else {
 		$content2.='<b>Пожалуйста, выберите теги для поиска!</b>
';
	}
}

$content2.='<br><h1>Последние новости [<a href="'.$server_absolute_path.'news/">читать все</a>]</h1>';
$result=mysql_query("SELECT * FROM ".$prefix."news WHERE active='1' order by id desc LIMIT 0,5");
while($a = mysql_fetch_array($result)) {
	$content2.=shownews($a,true);
}
$content2.='</div>';
?>