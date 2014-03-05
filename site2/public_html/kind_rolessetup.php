<?php
if($_SESSION["user_id"]!='' && $workrights["site"]["rolessetup"]) {
	// настроить поля заявок

	if(encode($_GET["roletype"])==1) {
		$_SESSION["roletype"]=1;
	}
	elseif(encode($_GET["roletype"])==2) {
		$_SESSION["roletype"]=2;
	}
	if(!isset($_SESSION["roletype"])) {
		$_SESSION["roletype"]=2;
	}

	if($_SESSION["roletype"]==1) {
		$team=1;
		$messages=Array("Поле командной заявки добавлено.","Поле командной заявки успешно изменено.","Поле командной заявки удалено.");
	}
	else {
		$team=0;
		$messages=Array("Поле индивидуальной заявки добавлено.","Поле индивидуальной заявки успешно изменено.","Поле индивидуальной заявки удалено.");
	}

	// Создание объекта
	$obj=new netObj(
		'rolessetup',
		$prefix."rolefields",
		"поле заявки",
		$messages,
		Array(
			'0'	=>	Array(
				Array("rolecode", "ASC", true, true),
				Array("rolename", "ASC", true, true),
				Array("roletype", "ASC", true, true, Array(2,Array(Array('select','Выпадающий список (select)'),Array('checkbox','Галочка (checkbox)'),Array('h1','Заголовок (h1)'),Array('wysiwyg','Интерфейс свободного ввода текста'),Array('multiselect','Множественный выбор'),Array('number','Поле для ввода чисел'),Array('text','Строка для текста (text)'),Array('textarea','Текстовый блок (textarea)')))),
				Array("rolemustbe", "ASC", true, true),
			),
		),
		2,
		'100%',
		50
	);

	// Создание схемы прав объекта
	if($_SESSION["siteid"]!='') {
		$obj_r=new netRight(
			true,
			true,
			true,
			true,
			100,
			"site_id=".$_SESSION["siteid"]." and team='".$team."'",
			"site_id=".$_SESSION["siteid"]." and team='".$team."'",
			"site_id=".$_SESSION["siteid"]." and team='".$team."'"
		);
		$obj->setRight($obj_r);
	}

	$result=mysql_query("SELECT COUNT(id) from ".$prefix."rolefields where site_id=".$_SESSION["siteid"]." and team='".$team."'");
	$a=mysql_fetch_array($result);
	if($actiontype=="add")
	{
		$a[0]+=1;
	}

	$result2=mysql_query("SELECT roletype from ".$prefix."rolefields where site_id=".$_SESSION["siteid"]." and id=".$id);
	$b=mysql_fetch_array($result2);
	if($b["roletype"]=='wysiwyg') {
		$roletypearr=Array(Array('h1','Заголовок (h1)'),Array('text','Строка для текста (text)'),Array('select','Выпадающий список (select)'),Array('textarea','Текстовый блок (textarea)'),Array('checkbox','Галочка (checkbox)'),Array('wysiwyg','Интерфейс свободного ввода текста'),Array('multiselect','Множественный выбор'),Array('number','Поле для ввода чисел'));
	}
	else {
		$roletypearr=Array(Array('h1','Заголовок (h1)'),Array('text','Строка для текста (text)'),Array('select','Выпадающий список (select)'),Array('textarea','Текстовый блок (textarea)'),Array('checkbox','Галочка (checkbox)'),Array('multiselect','Множественный выбор'),Array('number','Поле для ввода чисел'));
	}

	$roleparents=array();
	$result2=mysql_query("SELECT id,rolename,rolevalues FROM ".$prefix."rolefields WHERE site_id=".$_SESSION["siteid"]." AND (roletype='select' OR roletype='multiselect')");
	while($b=mysql_fetch_array($result2)) {
		$b["rolevalues"]=decode($b["rolevalues"]);
		preg_match_all('#\[(\d+)\]\[([^\]]+)\]#',$b["rolevalues"],$matches);
		foreach($matches[1] as $key=>$value) {
			$roleparents[]=array(($b["id"].':'.$value),'<b>'.decode($b['rolename']).'</b>: '.$matches[2][$key]);
		}
	}
	$alllocats=make5fieldtree(false,$prefix."roleslocat","parent",0," AND site_id=".$_SESSION["siteid"],"name asc",0,"id","name",1000000);
	foreach($alllocats as $locat) {
		$roleparents[]=array(('locat:'.$locat[0]),'<b>Локация</b>: '.$locat[1]);
	}

	// Создание полей объекта
	$obj_1=createElem(Array(
			'name'	=>	"rolename",
			'sname'	=>	"Название поля",
			'type'	=>	"text",
			'help'	=>	"не более 255 символов.",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true,
		)
	);
	$obj->setElem($obj_1);

	$obj_2=createElem(Array(
			'name'	=>	"roletype",
			'sname'	=>	"Тип",
			'type'	=>	"select",
			'values'	=>	$roletypearr,
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true,
		)
	);
	$obj->setElem($obj_2);

	$obj_3=createElem(Array(
			'name'	=>	"rolemustbe",
			'sname'	=>	"Обязательность",
			'type'	=>	"checkbox",
			'help'	=>	"обязан ли пользователь заполнить данное поле?",
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_3);

	$obj_7=createElem(Array(
			'name'	=>	"rolevalues",
			'sname'	=>	"Значения",
			'type'	=>	"textarea",
			'help'	=>	"необходимо <b>только для «выпадающего списка» и «множественного выбора»</b>.<br>
Заполняется следующим образом:<br>
<u>[идентификатор][значение]<br>
[идентификатор][значение]</u><br>
Например:<br>
[1][от друзей]<br>
[2][с дружественного сайта]",
			'height'	=>	'80',
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_7);

	$obj_4=createElem(Array(
			'name'	=>	"roledefault",
			'sname'	=>	"Значение по умолчанию",
			'type'	=>	"textarea",
			'help'	=>	"какое значение выводить по умолчанию? Для типа «<b>checkbox</b>»: если вы хотите, чтобы галочка по умолчанию была отмечена, введите «1» в это поле. Для типа «<b>множественный выбор</b>»: если вы хотите, чтобы какие-либо из галочек множественного выбора были проставлены по умолчанию, введите в это поле идентификатор данных галочек со знаком минуса с двух сторон. Например, если вы хотите, чтобы галочки были проставлены у выборов с идентификаторами 1, 2 и 5, введите в это поле: -1-2-5- Для типа «<b>выпадающий список</b>»: введите в это поле идентификатор того варианта, который должен выставляться по умолчанию, например: 2",
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_4);

	$obj_15=createElem(Array(
			'name'	=>	"roleparent",
			'sname'	=>	"Показывать, если",
			'type'	=>	"multiselect",
			'values'	=>	$roleparents,
			'help'	=>	"поле появляется в заявке, только если в заявке уже проставлены соответствующие значения другого «выпадающего списка» или «множественного выбора»",
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_15);

	$obj_5=createElem(Array(
			'name'	=>	"rolerights",
			'sname'	=>	"Права доступа игроков",
			'type'	=>	"select",
			'values'	=>	Array(Array('1','скрытое, только для мастеров'),Array('2','игрок видит, меняет только мастер'),Array('3','игрок видит и может менять'),Array('4','игрок видит и может менять + поле видно в заявке в сетке ролей')),
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true,
		)
	);
	$obj->setElem($obj_5);

	$obj_6=createElem(Array(
			'name'	=>	"rolehelp",
			'sname'	=>	"Подсказка",
			'type'	=>	"textarea",
			'help'	=>	"вспомогательный текст по полю для игроков в свободной форме.",
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_6);

	$obj_8=createElem(Array(
			'name'	=>	"rolecode",
			'sname'	=>	"Очередность",
			'type'	=>	"number",
			'help'	=>	"каким по счету нужно показывать данное поле в заявке? Поле с очередностью 0 окажется на самом верху страницы.",
			'default'	=>	($a[0]+1),
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true,
		)
	);
	$obj->setElem($obj_8);

	$obj_10=createElem(Array(
			'name'	=>	"roleheight",
			'sname'	=>	"Высота",
			'type'	=>	"number",
			'help'	=>	"в пикселях.",
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_10);

	$obj_11=createElem(Array(
			'name'	=>	"filter",
			'sname'	=>	"Включить в фильтры",
			'type'	=>	"checkbox",
			'help'	=>	'если поставить галочку, мастера смогут осуществлять поиск по данному полю в «<a href="'.$server_absolute_path_site.'orders/">Поданных заявках</a>».',
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_11);

	$obj_14=createElem(Array(
			'name'	=>	"hidefieldinadd",
			'sname'	=>	"Скрыть данное поле при подаче заявки",
			'type'	=>	"checkbox",
			'help'	=>	'если поставить галочку, игроки не будут видеть данное поле при подаче заявки. Такие поля не могут быть обязательными к заполнению.',
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_14);

	$obj_12=createElem(Array(
			'name'	=>	"team",
			'sname'	=>	"Командная/индивидуальная",
			'type'	=>	"hidden",
			'default'	=>	$team,
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true,
		)
	);
	$obj->setElem($obj_12);

	$obj_13=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Последнее изменение",
			'type'	=>	"timestamp",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_13);

	$obj_14=createElem(Array(
			'name'	=>	"site_id",
			'sname'	=>	"id сайта",
			'type'	=>	"hidden",
			'default'	=>	$_SESSION["siteid"],
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_14);

	// Исполнение dynamicaction, если необходимо
	if($action=="dynamicaction")
	{
		require_once($server_inner_path.$direct."/dynamicaction.php");
		if($object=="rolessetup")
		{
			if($actiontype=="add") {
				function dynamic_add_success() {
					global
						$prefix,
						$_SESSION,
						$id;

					mysql_query("UPDATE ".$prefix."rolefields SET site_id=".$_SESSION["siteid"]." WHERE id=".$id);

					dynamic_save_success();
				}
			}
			function dynamic_save_success() {
				global
					$prefix,
					$_SESSION,
					$id,
					$team;

				if($team=='') {
					$team=($_SESSION["roletype"]==1?1:0);
				}

				$code=1;
				$result=mysql_query("SELECT * FROM ".$prefix."rolefields WHERE site_id=".$_SESSION["siteid"]." AND team='".$team."' ORDER BY rolecode ASC, date DESC");
				while($a=mysql_fetch_array($result)) {
					mysql_query("UPDATE ".$prefix."rolefields SET rolecode=".$code." WHERE id=".$a["id"]);
					$code++;
				}
			}

			dynamicaction($obj);
		}
	}

	// Добавление параметра values к select'ам и multiselect'ам.

	// Инициализация элементов поиска, если нужен.

	// Отрисовка всего объекта html'ем в переменную
	$obj_html.=$obj->draw();

	// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
	$pagetitle=h1line('Настройка формы заявки',$curdir.$kind.'/');
	if(($id!='' || $act=="add") && $actiontype=='') {
		err_red('Внимание! Не надо вносить в форму заявки какие-либо поля, связанные с информацией по игроку. К каждой поданной заявке автоматически прикладываются все данные, заполненные игроком в его профиле!');
		$content2.='<div class="narrow">';
	}
	else {
		$content2.='<div class="narrow"><center><div style="text-align: center;"><b>Вы изменяете поля ';
		if($_SESSION["roletype"]==1) {
			$content2.='командных заявок</b><br /><a href="'.$server_absolute_path_site.$kind.'/roletype=2">переключиться на поля для индивидуальных заявок</a>';
		}
		if($_SESSION["roletype"]==2) {
			$content2.='индивидуальных заявок</b><br /><a href="'.$server_absolute_path_site.$kind.'/roletype=1">переключиться на поля для командных заявок</a>';
		}
		$content2.='</div></center><br />';
	}
	$content2.=$obj_html.'</div>';
}
?>