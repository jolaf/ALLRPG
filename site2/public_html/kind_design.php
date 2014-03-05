<?php
if($_SESSION["user_id"]!='' &&  $workrights["site"]["design"]) {
	// дизайн

	if($action=="setdesign")
	{
		$result=mysql_query("SELECT * FROM ".$prefix."sites where id=".$_SESSION["siteid"]);
		$a = mysql_fetch_array($result);

		if($step==1)
		{
			$htmlcodeindex=encode_to_cp1251($_REQUEST["htmlcodeindex"]);

			$result2=mysql_query("SELECT * FROM ".$prefix."temps where id=".$htmlcodeindex);
			$b = mysql_fetch_array($result2);

			$now=time();

			if($a["htmlcodeindex"]!=$htmlcodeindex)
			{
				mysql_query("UPDATE ".$prefix."sites SET htmlcodeindex=".$htmlcodeindex.", htmlcode='".$b["htmlcode"]."', css='".$b["css"]."', usercss='".$b["usercss"]."', menualign=".$b["menualign"].", submenualign=".$b["submenualign"].", newsformat1='".$b["newsformat1"]."', newsformat2='".$b["newsformat2"]."', separ='".$b["separ"]."', separkind='".$b["separkind"]."', separsub='".$b["separsub"]."', htmlmade='".$now."' where id=".$_SESSION["siteid"]);
				dynamic_err_one('success','Шаблон успешно установлен.');
			}
			else
			{
				$res=decodetempcss($a["css"]);
				$vals_f=$res[0];
				$vals_f_a=$res[1];

				$res2=decodetempcss($b["css"]);
				$vals2_f=$res2[0];
				$vals2_f_a=$res2[1];

				for($i=0;$i<count($vals2_f);$i++)
				{
					$valsdouble='';

					for($j=0;$j<count($vals_f);$j++)
					{
						if($vals_f[$j]['name']==$vals2_f[$i]['name'])
						{
							$valsdouble=$vals_f[$j];
							break;
						}
					}

					if($valsdouble!='')
					{
						if($vals2_f[$i]['type']=='text' && $vals2_f[$i]['additional']=='file')
						{
							$css.='[2]['.$vals2_f[$i]['name'].']['.$vals_f_a[$valsdouble['name']].']['.$vals2_f[$i]['sname'].']&lt;br&gt;';
						}
						elseif($vals2_f[$i]['type']=='colorpicker')
						{
							$css.='[3]['.$vals2_f[$i]['name'].']['.$vals_f_a[$valsdouble['name']].']['.$vals2_f[$i]['sname'].']&lt;br&gt;';
						}
						else
						{
							$css.='[1]['.$vals2_f[$i]['name'].']['.$vals_f_a[$valsdouble['name']].']['.$vals2_f[$i]['sname'].']&lt;br&gt;';
						}
					}
					else
					{
						if($vals2_f[$i]['type']=='text' && $vals2_f[$i]['additional']=='file')
						{
							$css.='[2]['.$vals2_f[$i]['name'].']['.$vals2_f_a[$vals2_f[$i]['name']].']['.$vals2_f[$i]['sname'].']&lt;br&gt;';
						}
						elseif($vals2_f[$i]['type']=='colorpicker')
						{
							$css.='[3]['.$vals2_f[$i]['name'].']['.$vals2_f_a[$vals2_f[$i]['name']].']['.$vals2_f[$i]['sname'].']&lt;br&gt;';
						}
						else
						{
							$css.='[1]['.$vals2_f[$i]['name'].']['.$vals2_f_a[$vals2_f[$i]['name']].']['.$vals2_f[$i]['sname'].']&lt;br&gt;';
						}
					}
				}

				mysql_query("UPDATE ".$prefix."sites SET htmlcode='".$b["htmlcode"]."', css='".encode($css)."', htmlmade='".$now."' where id=".$_SESSION["siteid"]);
				dynamic_err_one('success','Шаблон успешно обновлен.');
			}
		}
		elseif($step==2)
		{
			$res=decodetempcss($a["css"]);
			$vals_f=$res[0];
			$vals_f_a=$res[1];

			for($i=0;$i<count($vals_f);$i++)
			{
				if($vals_f[$i]['type']=='text' && $vals_f[$i]['additional']=='file')
				{
					$css.='[2]['.$vals_f[$i]['name'].']['.encode_to_cp1251($_REQUEST[$vals_f[$i]['name']]).']['.$vals_f[$i]['sname'].']&lt;br&gt;';
				}
				elseif($vals_f[$i]['type']=='colorpicker')
				{
					$css.='[3]['.$vals_f[$i]['name'].']['.encode_to_cp1251($_REQUEST[$vals_f[$i]['name']]).']['.$vals_f[$i]['sname'].']&lt;br&gt;';
				}
				else
				{
					$css.='[1]['.$vals_f[$i]['name'].']['.encode_to_cp1251($_REQUEST[$vals_f[$i]['name']]).']['.$vals_f[$i]['sname'].']&lt;br&gt;';
				}
			}

			mysql_query("UPDATE ".$prefix."sites SET css='".encode($css)."' where id=".$_SESSION["siteid"]);
			$lead=$lead1.$_SESSION['siteway'].$lead2;
			dynamic_err_one('success','Переменные шаблона успешно установлены.<br>
<a href="'.$lead.'" target="_blank">Посмотреть на получившееся оформление сайта</a>.');
		}
		elseif($step==3)
		{
			$usercss=encode_to_cp1251($_REQUEST["usercss"]);
			mysql_query("UPDATE ".$prefix."sites SET usercss='".$usercss."' where id=".$_SESSION["siteid"]);
			dynamic_err_one('success','Пользовательский CSS успешно прописан.');
		}
		elseif($step==4)
		{
			if(encode_to_cp1251($_REQUEST['htmlcode'])!='')
			{
				mysql_query("UPDATE ".$prefix."sites SET htmlcode='".encode_to_cp1251($_REQUEST["htmlcode"])."', htmlcodeindex=65000, menualign=".encode_to_cp1251($_REQUEST["menualign"]).", newsformat1='".encode_to_cp1251($_REQUEST["newsformat1"])."', newsformat2='".encode_to_cp1251($_REQUEST["newsformat2"])."', separ='".encode_to_cp1251($_REQUEST["separ"])."' where id=".$_SESSION["siteid"]);
				dynamic_err_one('success','Шаблон успешно заменен.');
			}
			else {
				dynamic_err_one('error','Шаблон не заменен. Не заполнен код шаблона!');
			}
		}
	}

	$result=mysql_query("SELECT * FROM ".$prefix."sites where id=".$_SESSION["siteid"]);
	$a = mysql_fetch_array($result);
	$result2=mysql_query("SELECT * FROM ".$prefix."temps where id=".$a["htmlcodeindex"]);
	$b = mysql_fetch_array($result2);
	$yourtemplate=false;
	if($a["htmlcode"]=='') {
		$step=1;
	}
	elseif($a["htmlcodeindex"]==65000 && encode($_REQUEST["step"])=='') {
		$step=4;
	}
	if($a["htmlcodeindex"]==65000) {
		$yourtemplate=true;
	}

	if($a["htmlcode"]!='') {
		$content2.='<div class="funcbar"><a href="'.$server_absolute_path_site.$kind.'/step=1">Выбрать шаблон сайта</a> | <a href="'.$server_absolute_path_site.$kind.'/step=2">Переменные шаблона</a> | <a href="'.$server_absolute_path_site.$kind.'/step=3">CSS</a> | <a href="'.$server_absolute_path_site.$kind.'/step=4">Изменить код шаблона</a></div>';
	}

	if($a["htmlmade"]<$b["date"] && $a["htmlcodeindex"]!=65000) {
		err_red('Ваш шаблон устарел. На сайте имеется более новая версия для вашего шаблона. Если вы хотите установить более новую версию шаблона, выберите ваш шаблон в списке ниже и нажмите «Применить шаблон».');
	}

	if($step==1) {
		$pagetitle=h1line('Выбрать шаблон сайта');

		$content2.='
<div class="narrow">
<!--tempselect-->
';

		$tempselect.='<div style="background-color: #f3f3f3; padding: 3px">';
		if($a["htmlcodeindex"]==65000) {
			$tempselect.='<b>Сейчас на сайте используется: <i>шаблон, определенный пользователем.</i></b>';
		}
		else {
			$tempselect.='<b>Сейчас на сайте используется: <i>'.$b["name"].'</i></b>';
		}
		$tempselect.='</div><br />';

		$tempselect.='
<form action="'.$curdir.$kind.'/" method="post" enctype="multipart/form-data" onSubmit="if (confirm(\'Вы уверены, что хотите полностью сменить шаблон для своего сайта? Ваши изменения нынешнего шаблона не сохранятся!\')) {return true;} else {return false;}">
<input type="hidden" name="kind" value="'.$kind.'">
<input type="hidden" name="action" value="setdesign">
<input type="hidden" name="step" value="'.$step.'">
<div class="fieldname" id="name_do">Установить</div>
<div class="fieldvalue" id="div_do">
<select name="htmlcodeindex"><option style="font-weight: bold">- Выбрать -</option>
';
		$result=mysql_query("SELECT * FROM ".$prefix."temps order by name");
		while($a = mysql_fetch_array($result))
		{
			$result2=mysql_query("SELECT * FROM ".$prefix."sites where id=".$_SESSION["siteid"]);
			$b = mysql_fetch_array($result2);
			if($a["id"]==$b["htmlcodeindex"])
			{
				$tempselect.='<option value='.$a["id"].' selected>'.$a["name"].'</a></option>
';
			}
			else
			{
				$tempselect.='<option value='.$a["id"].'>'.$a["name"].'</a></option>
';
			}
			$content2.='<div style="float:right"><a href="'.$server_absolute_path.'temps/temps='.$a["id"].'" target="_blank">[посмотреть шаблон]</a></div>';
			$content2.='<h1>'.decode($a["name"]).'</h1>
';
			$content2.=decode($a["descr"]);
		}
		$content2.='</div>';

		$tempselect.='</select></div><br>
<center><button class="main">Применить шаблон</button></center>
</form><br />
';
		$content2=eregi_replace("<!--tempselect-->", $tempselect, $content2);
	}
	elseif($step==2)
	{
		$pagetitle=h1line('Переменные шаблона');

		$res=decodetempcss($a["css"]);
		$vals_f=$res[0];
		$vals_f_a=$res[1];

		$content2.='
<div class="narrow">
<form action="'.$curdir.$kind.'/" method="post" enctype="multipart/form-data">
<input type="hidden" name="kind" value="'.$kind.'">
<input type="hidden" name="action" value="setdesign">
<input type="hidden" name="step" value="'.$step.'">
';
		$lead=$lead1.$_SESSION['siteway'].$lead2;
		for($i=0;$i<count($vals_f);$i++)
		{
			$obj=createElem($vals_f[$i]);
			$obj->setVal($vals_f_a);
			$content2.='<div class="fieldname" id="name_'.$obj->getName().'" tabindex="'.($i+1).'">'.$obj->getSname().'</div><div class="fieldvalue" id="div_'.$obj->getName().'">'.$obj->draw(2,"write");

			if($vals_f[$i]['type']=='text' && $vals_f[$i]['additional']=='file')
			{
				if($vals_f_a[$vals_f[$i]['name']]!='')
				{
					if(strpos($vals_f_a[$vals_f[$i]['name']],'http://')!==false) {
						$handle2 = @fopen($vals_f_a[$vals_f[$i]['name']], "r");
					}
					else {
						$handle2 = @fopen($lead.$vals_f_a[$vals_f[$i]['name']], "r");
					}
					if ($handle2 === false) {
						$content2.=' <i><font color="#dd0000">Файл отсутствует.</font></i>';
					}
					else {
						if(strpos($vals_f_a[$vals_f[$i]['name']],'http://')!==false) {
							$content2.=' <a href="'.$vals_f_a[$vals_f[$i]['name']].'" target="_blank"><b>ПОСМОТРЕТЬ</b></a>';
						}
						else {
							$content2.=' <a href="'.$lead.$vals_f_a[$vals_f[$i]['name']].'" target="_blank"><b>ПОСМОТРЕТЬ</b></a>';
						}
					}
					@fclose($handle2);
				}
				else {
					$content2.=' <i><font color="#dd0000">Файл не определен.</font></i>';
				}
			}
			$content2.='
</div>
<div class="clear"></div>
<br />';
		}
		$content2.='<center><button class="main">Сохранить переменные</button></center>
</form>
</div>';
	}
	elseif($step==3) {
		$pagetitle=h1line('CSS');
		$content2.='<div class="narrow">Если вы владеете языком CSS (Cascade Style Sheet), вы можете здесь ввести все необходимые вам на сайте CSS-переменные, и они автоматически будут установлены в html-код каждой страницы вашего сайта.<br><br>
<form action="'.$curdir.$kind.'/" method="post" enctype="multipart/form-data">
<input type="hidden" name="kind" value="'.$kind.'">
<input type="hidden" name="action" value="setdesign">
<input type="hidden" name="step" value="'.$step.'">
<textarea rows=30 name="usercss">'.decode($a["usercss"]).'</textarea><br><br>
<center><button class="main">Сохранить CSS</button></center>
</form></div>';
	}
	elseif($step==4) {
		$pagetitle=h1line('Изменить код шаблона');
		$content2.='<div class="narrow">
Вы можете изменить html-код шаблона полностью по своему усмотрению. Однако, если Вы хотите не утерять случайно возможностей allrpg.info, обязательно прочитайте инструкцию по системным переменным, идущую ниже.<br><br>
<font color="red"><b>Все системные переменные в html-коде должны быть заключены в &lt;!--переменная--&gt;, например: &lt;!--maintext--&gt;</b></font>
<ul>
<li>самые нужные:
<ul>
<li><b>maintext</b> – <font color="red">крайне важная переменная, автоматически заменяется на контент</font> каждой конкретной страницы из раздела «<a href="'.$server_absolute_path_site.'pages/">Создать/изменить разделы/страницы</a>» или на соответствующий контент ленты новостей / сетки ролей и заявок;
<li><b>title</b> – заменяется на «Название проекта» из «<a href="'.$server_absolute_path_site.'settings/">Основных свойств</a>»;
<li><b>innerpath</b> – заменяется на «путеводитель» (состоит из указания названия раздела, подраздела и страницы, на которой в данный момент находится пользователь). Раздел, подраздел и страница данного «путеводителя» превращены в ссылки, поэтому <u>нельзя</u> использовать внутри тега title для указания полного пути на сайте;
<li><b>innerpathnolinks</b> – то же, что и innerpath, но без ссылок, поэтому <u>можно</u> использовать внутри тега title для указания полного пути на сайте;
<li><b>navlist</b> – заменяется на маркированный список, включающий в себя ВСЕ разделы, подразделы и страницы. Идеально подходит для создания продвинутых javascript-меню;
<li><b>navmootools</b> – заменяется на код, необходимый для работы javascript-меню mootools (внешний вид Вы можете исправить через раздел «<a href="'.$server_absolute_path_site.'design/step=3">CSS</a>»). Определить, нужно Вам вертикальное расположение меню или горизонтальное, Вы можете в пункте «Расположение меню» ниже. Остальные пункты, касающиеся расположения меню и подменю, при использовании mootools не актуальны;
</ul>
<li>дополнительные:
<ul>
<li><b>id</b> – заменяется на уникальный id страницы, на которой находится пользователь;
<li><b>sub</b> – заменяется на порядковый номер или alias подраздела, в котором находится пользователь;
<li><b>kind</b> – заменяется на порядковый номер или alias раздела, в котором находится пользователь;
<li><b>idname</b> – заменяется на название страницы, на которой находится пользователь;
<li><b>subname</b> – заменяется на название подраздела, в котором находится пользователь;
<li><b>kindname</b> – заменяется на название раздела, в котором находится пользователь;
<li><b>allsubs</b> – заменяется на скрытые div\'ы, в которых прописаны все подразделы всех разделов сайта (можно использовать для создания своего javascript-меню);
</ul>
<li>технические:
<ul>
<li><b>lastchangedate</b> – заменяется на дату последнего изменения информации на странице;
<li><b>comments</b> – заменяется на модуль комментирования страниц (в разделе «<a href="'.$server_absolute_path_site.'settings/">Основные свойства</a>» должна стоять галочка напротив пункта «Включить систему комментариев страниц») на тех страницах, которым не было проставлено «Отключить систему комментариев на данной странице» в разделе «<a href="'.$server_absolute_path_site.'pages/">Создать/изменить разделы/страницы</a>».
</ul>
</ul>
Переменные, не указанные в этом списке, или нужны для конструктора (автоматически заменяются на данные из раздела «<a href="'.$server_absolute_path_site.'design/step=2">Переменные шаблона</a>»), или являются переменными старых версий шаблонов.<br />
<font color="red">Мы <b>настоятельно</b> не рекомендуем вам пользоваться данной функцией, если Вы не владеете языком HTML или если Вы не прочли внимательно описание методики изменения шаблона выше!</font><br /><br />

<form action="'.$curdir.$kind.'/" method="post" enctype="multipart/form-data" onSubmit="if (confirm(\'Вы уверены, что хотите полностью изменить код шаблона для своего сайта?\')) {return true;} else {return false;}">
<input type="hidden" name="kind" value="'.$kind.'">
<input type="hidden" name="action" value="setdesign">
<input type="hidden" name="step" value="'.$step.'">
<div class="fieldname" id="name_htmlcode" tabindex="1">HTML-код шаблона</div>
<div class="fieldvalue" id="div_htmlcode">
<textarea rows=30 name="htmlcode" id="div_htmlcode">'.decode($a["htmlcode"]).'</textarea>
</div>

<div class="clear"></div><br>

<div class="fieldname" id="name_newsformat1" tabindex="2">Конструктор внешнего вида новостной ленты</div>
<div class="help" id="help_newsformat1">В данном поле можно сформировать представление каждой новости в общей новостной ленте. Кроме html-тегов, можно использовать следующие команды (автоматически заменяются системой сайта на те или иные данные):<ul><li><b>&lt;!--linkstart--&gt;</b> – открывающий тег ссылки на подробный текст новости (ссылка сформируется, если у новости есть не только общий, но и подробный текст); <li><b>&lt;!--linkfinish--&gt;</b> – закрывающий тег ссылки на подробный текст новости (ссылка сформируется, если у новости есть не только общий, но и подробный текст); <li><b>&lt;!--date--&gt;</b> – дата новости; <li><b>&lt;!--name--&gt;</b> – название новости; <li><b>&lt;!--text--&gt;</b> – текст новости вкратце; <li><b>&lt;!--moreinfo--&gt;</b> – автоматическая ссылка «Подробнее...» (формируется, если у новости есть не только общий, но и подробный текст); <li><b>&lt;!--author--&gt;</b> – ник автора новости; <li><b>&lt;!--source--&gt;</b> – источник новости.</ul></div>
<div class="fieldvalue" id="div_newsformat1">
<textarea rows=10 name="newsformat1">'.decode($a["newsformat1"]).'</textarea>
</div>

<div class="clear"></div><br>

<div class="fieldname" id="name_newsformat2" tabindex="3">Конструктор внешнего вида новости</div>
<div class="help" id="help_newsformat2">В данном поле можно сформировать представление раскрытой новости. Кроме html-тегов, можно использовать следующие команды (автоматически заменяются системой сайта на те или иные данные):<ul><li><b>&lt;!--date--&gt;</b> – дата новости; <li><b>&lt;!--name--&gt;</b> – название новости; <li><b>&lt;!--text--&gt;</b> – текст новости полностью; <li><b>&lt;!--author--&gt;</b> – ник автора новости; <li><b>&lt;!--source--&gt;</b> – источник новости.</ul></div>
<div class="fieldvalue" id="div_newsformat2">
<textarea rows=10 name="newsformat2">'.decode($a["newsformat2"]).'</textarea>
</div>

<div class="clear"></div><br>

<div class="fieldname" id="name_separ" tabindex="4">Разделитель для «путеводителя»</div>
<div class="help" id="help_separ">Путеводитель – набор последовательных ссылок, первая из которых ведет на родительский раздел, вторая – на родительский подраздел, третья – на конкретную страницу. По умолчанию используется следующий разделитель: &#150;&#187; (с пробелами до и после разделителя).</div>
<div class="fieldvalue" id="div_separ">
<input type="text" name="separ" class="inputtext" value="'.decode($a["separ"]).'">
</div>

<div class="clear"></div><br>

<div class="fieldname" id="name_menualign" tabindex="5">Расположение меню</div>
<div class="help" id="help_menualign">Как выстраивать пункты меню: вертикально или горизонтально?</div>
<div class="fieldvalue" id="div_menualign">
<select name="menualign">
<option value="1"';
		if(encode_to_cp1251($_REQUEST["menualign"])==1 || $a["menualign"]==1)
		{
			$content2.=' selected';
		}
		$content2.='>вертикально</option>
<option value="2"';
		if(encode_to_cp1251($_REQUEST["menualign"])==2 || $a["menualign"]==2)
		{
			$content2.=' selected';
		}
		$content2.='>горизонтально</option>
</select>
</div>
<br>
<center><button class="main">Изменить код шаблона</button></center>
</form>
</div>';
	}
}
#*************************************************************
function decodetempcss($css) {

	$css=decode($css);
	$pos = strpos($css, "]\r\n");
	while (!($pos===false)) {
		$st1 = substr($css,0,$pos+1);

		$pos2 = strpos($st1, "]");
		$ce1 = substr($st1,1,$pos2-1);
		$st1 = substr($st1,$pos2+1,strlen($st1));
		$pos2 = strpos($st1, "]");
		$ce2 = substr($st1,1,$pos2-1);
		$st1 = substr($st1,$pos2+1,strlen($st1));
		$pos2 = strpos($st1, "]");
		$ce3 = substr($st1,1,$pos2-1);
		$st1 = substr($st1,$pos2+1,strlen($st1));
		$pos2 = strpos($st1, "]");
		$ce4 = substr($st1,1,$pos2-1);
		$st1 = substr($st1,$pos2+1,strlen($st1));

		if($ce1==1)
		{
			$vals_f[] = Array('type'=>"text",'name'=>$ce2,'sname'=>$ce4);
		}
		elseif($ce1==2)
		{
			$vals_f[] = Array('type'=>"text",'name'=>$ce2,'sname'=>$ce4,'additional'=>'file');
		}
		elseif($ce1==3)
		{
			$vals_f[] = Array('type'=>"colorpicker",'name'=>$ce2,'sname'=>$ce4,'default'=>'');
		}

		$vals_f_a[$ce2]=decode($ce3);

		$css = substr($css,$pos+3,strlen($css));
		$pos = strpos($css, "]\r\n");
		if ($pos === false) break;
	}

	if($css!='')
	{
		$st1 = $css;

		$pos2 = strpos($st1, "]");
		$ce1 = substr($st1,1,$pos2-1);
		$st1 = substr($st1,$pos2+1,strlen($st1));
		$pos2 = strpos($st1, "]");
		$ce2 = substr($st1,1,$pos2-1);
		$st1 = substr($st1,$pos2+1,strlen($st1));
		$pos2 = strpos($st1, "]");
		$ce3 = substr($st1,1,$pos2-1);
		$st1 = substr($st1,$pos2+1,strlen($st1));
		$pos2 = strpos($st1, "]");
		$ce4 = substr($st1,1,$pos2-1);
		$st1 = substr($st1,$pos2+1,strlen($st1));

		if($ce1==1)
		{
			$vals_f[] = Array('type'=>"text",'name'=>$ce2,'sname'=>$ce4);
		}
		elseif($ce1==2)
		{
			$vals_f[] = Array('type'=>"text",'name'=>$ce2,'sname'=>$ce4,'additional'=>'file');
		}
		elseif($ce1==3)
		{
			$vals_f[] = Array('type'=>"colorpicker",'name'=>$ce2,'sname'=>$ce4,'default'=>'#FFFFFF');
		}

		$vals_f_a[$ce2]=decode($ce3);
	}

	$result[0]=$vals_f;
	$result[1]=$vals_f_a;
	return($result);
}
?>