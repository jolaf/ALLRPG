<?php
$bazecount=$_SESSION["bazecount"];
if($bazecount!='') {
	$bazecount=50;
}

if($object>0) {
	$id=$object;
}

if($id!='') {
	$result=mysql_query("SELECT * from ".$prefix."areas where id=".$id);
	$a=mysql_fetch_array($result);
}

$pagetitle=h1line('Полигоны',$curdir.$kind.'/');

if($id!='' && $a["id"]!='') {
	if($_SESSION["user_sid"]==$a["user_id"] || $admin) {
		$cansee=10;
	}
	else {
		$cansee=100000;
	}

    $content2='<div class="narrow">';
    if($a["add_ip"]==get_real_ip() || $a["user_id"]==$_SESSION["user_sid"] || $_SESSION["admin"] || $_SESSION["candoevents"]) {
		$content2.='<div style="float: right;"><span class="gui-btn"><span><a href="'.$server_absolute_path_info.'myareas/myareas/'.$a["id"].'/act=view">Редактировать полигон</a></span></span></div>';
	}

	$result2=mysql_query("SELECT * from ".$prefix."users where sid=".$a["user_id"]);
	$b=mysql_fetch_array($result2);
	if($b["id"]!='') {
		$author=usname($b,true,true);
	}
	else {
		$author='';
	}

	$areas_f=Array (
		Array(
			'name'	=>	"name",
			'sname'	=>	decode($a["name"]),
			'type'	=>	"h1",
			'read'	=>	10,
		),
		Array(
			'name'	=>	"tipe",
			'sname'	=>	"Тип",
			'type'	=>	"select",
			'values'	=>	Array(Array('1','городской'),Array('2','лесной'),Array('3','турбаза'),Array('4','на воде')),
			'read'	=>	10,
			'write'	=>	1000000,
		),
		Array(
			'name'	=>	"author",
			'sname'	=>	"Полигон внес",
			'type'	=>	"text",
			'default'	=>	$author,
			'read'	=>	10,
			'write'	=>	1000000,
		),
		Array(
			'name'	=>	"city",
			'sname'	=>	"Город",
			'type'	=>	"sarissa",
			'parents'	=>	Array(Array('country','Страна'),Array('region','Регион')),
			'file'	=>	$helpers_path.'geo.php',
			'table'	=>	$prefix.'geography',
			'parent'	=>	'parent',
			'read'	=>	10,
			'write'	=>	1000000,
			'width'	=>	200,
		),
		Array(
			'name'	=>	"descr",
			'sname'	=>	"Описание",
			'type'	=>	"wysiwyg",
			'height'	=>	400,
			'read'	=>	10,
			'write'	=>	1000000,
		),
		Array(
			'name'	=>	"havegood",
			'sname'	=>	"Плюсы",
			'type'	=>	"multiselect",
			'values'	=>	make5field($prefix."areahave where gr=1 order by name","id","name"),
			'images'	=>	make5field($prefix."areahave where gr=1 order by name","id","im"),
			'path'	=>	$server_absolute_path.$uploads[8]['path'],
			'read'	=>	10,
			'write'	=>	1000000,
		),
		Array(
			'name'	=>	"havebad",
			'sname'	=>	"Минусы",
			'type'	=>	"multiselect",
			'values'	=>	make5field($prefix."areahave where gr=2 order by name","id","name"),
			'images'	=>	make5field($prefix."areahave where gr=2 order by name","id","im"),
			'path'	=>	$server_absolute_path.$uploads[8]['path'],
			'read'	=>	10,
			'write'	=>	1000000,
		),
		Array(
			'name'	=>	"map",
			'sname'	=>	"Карта проезда",
			'type'	=>	"file",
			'upload'	=>	9,
			'read'	=>	10,
			'write'	=>	1000000,
		),
		Array(
			'name'	=>	"way",
			'sname'	=>	"Проезд",
			'type'	=>	"wysiwyg",
			'height'	=>	400,
			'read'	=>	10,
			'write'	=>	1000000,
		),
		Array(
			'name'	=>	"coordinates",
			'sname'	=>	"Контакты оф. властей",
			'type'	=>	"wysiwyg",
			'height'	=>	300,
			'help'	=>	"скрыто для всех, кроме автора",
			'read'	=>	$cansee,
			'write'	=>	1000000,
		),
	);

	// движок регистрации
	$act="view";

	// Создание объекта
	$obj=new netObj(
		'areas',
		$prefix."areas",
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
		'user_id='.$_SESSION["user_sid"]
	);
	$obj->setRight($obj_r);

	for($i=0;$i<count($areas_f);$i++) {
		$objer='obj_'.$i;
		$$objer=createElem($areas_f[$i]);
		$obj->setElem($$objer);
		$$objer->setHelp('');
	}

	$content2.=$obj->draw();

	$content2.='</div>
<h1>События на данном полигоне</h1>
<div class="narrow"><ul>
';
	$start=$page*$bazecount;
	$result=mysql_query("SELECT * FROM ".$prefix."allgames where parent=0 AND area=".$id." order by name limit ".$start.", ".$bazecount);
	while($a=mysql_fetch_array($result))
	{
		$content2.='<li><a href="'.$server_absolute_path_info.'events/'.$a["id"].'/">'.decode($a["name"]).'</a>';
	}

	$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."allgames where parent=0 AND area=".$id);
	$a=mysql_fetch_array($result);
	$starttotal=$a[0];

	$content2.='</ul></div><br>';

	$content2.=pagecount($id,$starttotal,$bazecount);
}
else {
	if((is_array($filter8) && count($filter8)>0) || $filter8!='') {
		$filters=true;
		if($action=="dynamicindex" && $dynrequest==1) {
			dynamic_err(array(),'submit');
		}
	}
	elseif($action=="dynamicindex" && $dynrequest==1) {
		dynamic_err_one('error','Фильтры не определены!');
	}
	$selecter8=createElem(Array(
		'name'	=>	"filter8",
		'sname'	=>	"Регион",
		'type'	=>	"multiselect",
		'values'	=>	make5field($prefix."geography where id in (SELECT parent from ".$prefix."geography where id in (SELECT distinct city from ".$prefix."areas)) order by name","id","name"),
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

	$content2.='<div class="indexer">
<div id="filters_areas" style="'.($filters?'':'display: none;').'">
<form action="'.$curdir.$kind.'/" method="post" enctype="multipart/form-data">
<input type="hidden" name="action" value="dynamicindex">
<table class="menutable searchtable">
<tr>
<td><b>Регион</b>:<br>'.$selecter8->draw(2,'write').'</td>
</tr>
</table>

<table class="controls"><tr><td><button class="nonimportant" onClick="document.location=\''.$curdir.$kind.'/\'">очистить фильтр</button></td><td><div class="filters_'.($filters?'on':'off').'">'.($filters?'Внимание! Используются фильтры.':'Фильтры не используются.').'</div></td><td><button class="main">отфильтровать</button></td></tr></table></form><br></div></div>

<div class="cb_editor">

<h3 id="showfilters_areas" '.($filters?'style="display: none;" ':'').'class="ctrlink2"><a onClick="$(\'#filters_areas\').toggle(); $(\'#hidefilters_areas\').toggle(); $(\'#showfilters_areas\').toggle();">показать фильтры</a></h3>
<h3 id="hidefilters_areas" '.($filters?'':'style="display: none;" ').'class="ctrlink2"><a onClick="$(\'#filters_areas\').toggle(); $(\'#showfilters_areas\').toggle(); $(\'#hidefilters_areas\').toggle();">скрыть фильтры</a></h3>

<div class="clear"></div><hr>

<table class="menutable">
<tr class="menu">
<td>
Название
</td>
<td>
Информация
</td>
</tr>';
	$areas_1=createElem(
		Array(
			'name'	=>	"tipe",
			'sname'	=>	"Тип",
			'type'	=>	"select",
			'values'	=>	Array(Array('1','городской'),Array('2','лесной'),Array('3','турбаза'),Array('4','на воде')),
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$areas_3=createElem(
		Array(
			'name'	=>	"city",
			'sname'	=>	"Город",
			'type'	=>	"sarissa",
			'parents'	=>	Array(Array('country','Страна'),Array('region','Регион')),
			'file'	=>	$server_absolute_path.'geo.php',
			'table'	=>	$prefix.'geography',
			'parent'	=>	'parent',
			'read'	=>	10,
			'write'	=>	100,
			'width'	=>	200,
			'mustbe'	=>	true
		)
	);

	if($filter8!=0)
	{
		$query.=" WHERE ";

		if($filter8!=0)
		{
			if($more)
			{
				$query.=' AND ';
			}

			$filter8decode=$selecter8->getVal();
			$filter8decode=substr($filter8decode,1,strlen($filter8decode));
			$filter8decode=str_replace('-',', ',$filter8decode);
			$filter8decode=substr($filter8decode,0,strlen($filter8decode)-2);
			$query.="city IN (select id from ".$prefix."geography where parent IN (".$filter8decode."))";
			$query.="";
			$more=true;
		}
	}

	$bazecount=$_SESSION["bazecount"];
	if($bazecount=='') {
		$bazecount=50;
	}

	$stringnum=1;
	$start=$page*$bazecount;
	$result=mysql_query("SELECT * FROM ".$prefix."areas".$query." order by name LIMIT ".$start.", ".$bazecount);
	while($a = mysql_fetch_array($result))
	{
		$areas_1->setVal($a);
		$areas_3->setVal($a);

		$content2.='<tr';
		if($stringnum%2==1) {
			$content2.=' class="string1"';
		}
		else {
			$content2.=' class="string2"';
		}
		$content2.='><td><a href="'.$server_absolute_path_info.$kind.'/'.$a["id"].'/">'.decode($a["name"]).'</a></td><td>'.$areas_1->draw(2,"read").'<hr>'.$areas_3->draw(2,"read").'</td></tr>';
		$stringnum++;
	}

	$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."areas".$query);
	$a=mysql_fetch_array($result);
	$starttotal=$a[0];

	$content2.='</table></div><br>'.pagecount('',$starttotal,$bazecount,'&filter8='.$selecter8->getVal());
}
?>