<?
//портфолио/календарь
if($_SESSION["user_id"]=="") {
	if($subobj=="future") {
		$subobj=1;
	}
	if($subobj=="past") {
		$subobj=2;
	}
	$redirectparams='redirectparams=';
	if($act!="") {
		$redirectparams.='act:'.$act;
	}
	if(encode($_GET["game"])!="") {
		if($redirectparams!='') {
			$redirectparams.='*';
		}
		$redirectparams.='game:'.encode($_GET["game"]);
	}
	redirect($server_absolute_path.'register/redirectobj=portfolio&redirectid='.$subobj.'&'.$redirectparams);
}
if($subobj=="future") {
	$messages=Array("Событие успешно занесено в Ваш календарь.","Запись в Вашем календаре изменена.","Событие удалено из Вашего календаря.");
    $wasplayer='Буду играть (специализации)';
    $wasmaster='Буду мастерить (специализации)';
    $waspoligoner='Буду полигонщиком (специализации)';
}
elseif($subobj=="past") {
	$messages=Array("Событие успешно занесено в Ваше портфолио.","Запись в Вашем портфолио изменена.","Событие удалено из Вашего портфолио.");
	$wasplayer='Играл (специализации)';
    $wasmaster='Мастерил (специализации)';
    $waspoligoner='Был полигонщиком (специализации)';
}
else {
	$messages=Array("Событие успешно занесено в Ваше портфолио/календарь.","Запись в Вашем портфолио/календаре изменена.","Событие удалено из Вашего портфолио/календаря.");
	$wasplayer='Играл / буду играть (специализации)';
    $wasmaster='Мастерил / буду мастерить (специализации)';
    $waspoligoner='Был / буду полигонщиком (специализации)';
}

$porttype=encode($_POST["type"]);
if($porttype=='') {
	$porttype=encode($_GET["type"]);
}
// Создание объекта
$obj=new netObj(
	'portfolio',
	$prefix."played",
	"событие",
	$messages,
	Array(),
	2,
	'100%',
	50
);

// Создание схемы прав объекта
if($_SESSION["user_id"]!='') {
	$obj_r=new netRight(
		true,
		true,
		true,
		true,
		100,
		'user_id='.$_SESSION["user_id"],
		'user_id='.$_SESSION["user_id"],
		'user_id='.$_SESSION["user_id"]
	);
	$obj->setRight($obj_r);
}

// Создание полей объекта

if($subobj=="future") {
	$moreparams=Array(Array('futurepast',1));
}
elseif($subobj=="past") {
	$moreparams=Array(Array('futurepast',2));
}
else {
	$moreparams=Array(Array('futurepast',0));
}
$obj_1=createElem(Array(
		'name'	=>	"game",
		'sname'	=>	"Событие из инфотеки",
		'type'	=>	"sarissa",
		'parents'	=>	'search',
		'file'	=>	$helpers_path.'gameslist.php',
		'table'	=>	$prefix.'allgames',
		'order'	=>	'name',
		'moreparams'	=>	$moreparams,
		'help'	=>	"в данном списке представлены только те события, которые есть в <a href=\"".$server_absolute_path_info."events/\">инфотеке</a>. Добавить событие в этот список Вы можете <a href=\"".$server_absolute_path_info."myevents/\">здесь</a>.",
		'read'	=>	10,
		'write'	=>	100,
		'default'	=>	encode($_REQUEST["game"]),
		'mustbe'	=>	true,
	)
);
$obj->setElem($obj_1);

$obj_2=createElem(Array(
		'name'	=>	"togame",
		'sname'	=>	"Карточка события в инфотеке",
		'type'	=>	"text",
		'read'	=>	10,
		'write'	=>	100000,
	)
);
$obj->setElem($obj_2);

if(!($act=="add" && ($porttype==2 || $porttype==3))) {
	$obj_3=createElem(Array(
			'name'	=>	"role",
			'sname'	=>	"Роль",
			'type'	=>	"text",
			'help'	=>	"не имеет смысла заполнять, если Вы не игрок.",
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_3);
}

$obj_4=createElem(Array(
		'name'	=>	"locat",
		'sname'	=>	"Локация",
		'type'	=>	"text",
		'read'	=>	10,
		'write'	=>	100,
	)
);
$obj->setElem($obj_4);

if(!($act=="add" && ($porttype==2 || $porttype==3))) {
	$obj_5=createElem(Array(
			'name'	=>	"specializ",
			'sname'	=>	$wasplayer,
			'type'	=>	"multiselect",
			'values'	=>	make5field($prefix."specializ where gr=1 order by name","id","name"),
			'images'	=>	make5field($prefix."specializ where gr=1 order by name","id","im"),
			'path'	=>	$server_absolute_path.$uploads[7]['path'],
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_5);
}

if(!($act=="add" && ($porttype==1 || $porttype==3))) {
	$obj_6=createElem(Array(
			'name'	=>	"specializ2",
			'sname'	=>	$wasmaster,
			'type'	=>	"multiselect",
			'values'	=>	make5field($prefix."specializ where gr=2 order by name","id","name"),
			'images'	=>	make5field($prefix."specializ where gr=2 order by name","id","im"),
			'path'	=>	$server_absolute_path.$uploads[7]['path'],
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_6);
}

if(!($act=="add" && ($porttype==2 || $porttype==1))) {
	$obj_7=createElem(Array(
			'name'	=>	"specializ3",
			'sname'	=>	$waspoligoner,
			'type'	=>	"multiselect",
			'values'	=>	make5field($prefix."specializ where gr=3 order by name","id","name"),
			'images'	=>	make5field($prefix."specializ where gr=3 order by name","id","im"),
			'path'	=>	$server_absolute_path.$uploads[7]['path'],
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_7);
}

$obj_8=createElem(Array(
		'name'	=>	"photo",
		'sname'	=>	"Ссылка на фото",
		'type'	=>	"text",
		'help'	=>	"введите http-адрес Вашей связанной с данным событием фотографии, размещенной в Интернет.",
		'read'	=>	10,
		'write'	=>	100,
	)
);
$obj->setElem($obj_8);

$obj_9=createElem(Array(
		'name'	=>	"active",
		'sname'	=>	"Видимо всем",
		'type'	=>	"checkbox",
		'help'	=>	"показывать всем в моем профиле в инфотеке, что я участвую в данном событии, и показывать меня в карточке события в инфотеке, как участника.",
		'default'	=>	"1",
		'read'	=>	10,
		'write'	=>	100,
	)
);
$obj->setElem($obj_9);

$obj_16=createElem(Array(
		'name'	=>	"user_id",
		'sname'	=>	"id пользователя",
		'type'	=>	"hidden",
		'default'	=>	$_SESSION["user_id"],
		'read'	=>	10,
		'write'	=>	100,
		'mustbe'	=>	true
	)
);
$obj->setElem($obj_16);

$obj_11=createElem(Array(
		'name'	=>	"date",
		'sname'	=>	"Последнее изменение",
		'type'	=>	"timestamp",
		'read'	=>	10,
		'write'	=>	100,
		'mustbe'	=>	true
	)
);
$obj->setElem($obj_11);

$obj_10=createElem(Array(
		'name'	=>	"subobj",
		'type'	=>	"hidden",
		'default'	=>	$subobj,
		'read'	=>	10,
		'write'	=>	100000,
	)
);
$obj->setElem($obj_10);

// Исполнение dynamicaction, если необходимо
if($action=="dynamicaction")
{
	require_once($server_inner_path.$direct."/dynamicaction.php");
	if($object=="portfolio")
	{
		if($actiontype=="add" || $actiontype=="change") {
			function dynamic_save_success() {
				global
					$prefix,
					$_SESSION,
					$id;

				mysql_query("UPDATE ".$prefix."played SET user_id=".$_SESSION["user_id"]." where id=".$id);
			}
			function dynamic_add_success() {
				global
					$prefix,
					$_SESSION,
					$id;

				mysql_query("UPDATE ".$prefix."played SET user_id=".$_SESSION["user_id"]." where id=".$id);
			}
			dynamicaction($obj);
		}
		elseif($actiontype=="delete") {
			dynamicaction($obj);
		}
	}
}

if($_SESSION["user_id"]!='') {
	if($subobj=="future") {
		$pagetitle=h1line('Настройка календаря',$curdir.$kind.'/subobj=future');
	}
	elseif($subobj=="past") {
		$pagetitle=h1line('Настройка портфолио',$curdir.$kind.'/subobj=past');
	}
	else {
		$pagetitle=h1line('Настройка календаря/портфолио',$curdir.$kind.'/');
	}

	if($act=="add" || ($act=="view" && $id!='' && ($actiontype=="" || $trouble))) {
		if($id!=0)
		{
			$result=mysql_query("SELECT * from ".$prefix."played where id=".$id);
			$a=mysql_fetch_array($result);
			if($a["game"]>0) {
				$obj_2->setDefault('<a href="'.$server_absolute_path_info.'events/'.$a["game"].'/" target="_blank">открыть</a>');
			}
		}
		$content2.='<div class="narrow">'.$obj->draw().'</div>';
	}
	else
	{
		unset($allgames);
		$content2.='<div class="cb_editor">
	<center><a href="'.$curdir.$kind.'/portfolio/act=add&subobj='.$subobj.'" class="ctrlink">[+] отметить событие в ';
		if($subobj=="future") {
			$content2.='календаре';
		}
		elseif($subobj=="past") {
			$content2.='портфолио';
		}
		else {
			$content2.='календаре/портфолио';
		}
		$content2.='</a></center><br />
	<table class="menutable">';

		$wasnotyet=true;
		if($subobj=="future") {
			$result=mysql_query("SELECT p.id, a.name, a.datestart, a.datefinish FROM ".$prefix."played as p LEFT JOIN ".$prefix."allgames as a ON a.id=p.game where p.user_id=".$_SESSION["user_id"]." and p.game>0 and a.datestart>='".date("Y-m-d")."' order by a.datestart desc");
		}
		elseif($subobj=="past") {
	        $result=mysql_query("SELECT p.id, a.name, a.datestart, a.datefinish FROM ".$prefix."played as p LEFT JOIN ".$prefix."allgames as a ON a.id=p.game where p.user_id=".$_SESSION["user_id"]." and p.game>0 and a.datestart<'".date("Y-m-d")."' order by a.datestart desc");
		}
		else {
	        $result=mysql_query("SELECT p.id, a.name, a.datestart, a.datefinish FROM ".$prefix."played as p LEFT JOIN ".$prefix."allgames as a ON a.id=p.game where p.user_id=".$_SESSION["user_id"]." and p.game>0 order by a.datestart desc");
		}
		while($a=mysql_fetch_array($result))
		{
			$allgames[]=$a;
		}

		if($subobj=="future") {
			$result=mysql_query("SELECT id, event, datestart, datefinish FROM ".$prefix."played where user_id=".$_SESSION["user_id"]." and event!='' and datestart>='".date("Y-m-d")."' order by datestart desc");
		}
		elseif($subobj=="past") {
			$result=mysql_query("SELECT id, event, datestart, datefinish FROM ".$prefix."played where user_id=".$_SESSION["user_id"]." and event!='' and datestart<'".date("Y-m-d")."' order by datestart desc");
		}
		else {
			$result=mysql_query("SELECT id, event, datestart, datefinish FROM ".$prefix."played where user_id=".$_SESSION["user_id"]." and event!='' order by datestart desc");
		}
		while($a=mysql_fetch_array($result))
		{
			$allgames[]=$a;
		}

		foreach ($allgames as $key => $row)
		{
			$datestart[$key]  = $row['datestart'];
		}
		array_multisort($datestart, SORT_DESC, $allgames);

		$content2.='<tr class="menu"><td>Событие</td><td>Даты</td></tr>';
		if($subobj=='') {
			$content2.='<tr class="menu"><td colspan=2>ЕЩЕ БУДЕТ</td></tr>';
		}

		for($i=0;$i<count($allgames);$i++) {
			$a=$allgames[$i];

			if($a["datestart"]<date("Y-m-d") && $wasnotyet && $subobj=='')
			{
				$content2.='<tr class="menu"><td colspan=2>УЖЕ БЫЛО</td></tr>';
				$wasnotyet=false;
			}
			if($a["event"]!='') {
				$content2.='<tr';
	            if($i%2==0) {
	            	$content2.=' class="string1"';
	            }
	            else {
	            	$content2.=' class="string2"';
	            }
				$content2.='><td><a href="'.$curdir.$kind.'/portfolio/'.$a["id"].'/act=view&subobj='.$subobj.'">'.decode($a["event"]).' (персональное событие)</a></td><td><a href="'.$curdir.$kind.'/portfolio/'.$a["id"].'/act=view&subobj='.$subobj.'">'.datesfmake($a["datestart"],$a["datefinish"]).'</a></td></tr>';
			}
			else {
				$content2.='<tr';
	            if($i%2==0) {
	            	$content2.=' class="string1"';
	            }
	            else {
	            	$content2.=' class="string2"';
	            }
				$content2.='><td><a href="'.$curdir.$kind.'/portfolio/'.$a["id"].'/act=view&subobj='.$subobj.'">'.decode($a["name"]).'</a></td><td><a href="'.$curdir.$kind.'/portfolio/'.$a["id"].'/act=view&subobj='.$subobj.'">'.datesfmake($a["datestart"],$a["datefinish"]).'</a></td></tr>';
			}
		}

		$content2.='
	</table></div>';
	}
	if($id>0) {
		$content2=str_replace('&actiontype=delete','&actiontype=delete&subobj='.$subobj,$content2);
	}
}
?>