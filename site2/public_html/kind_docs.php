<?php
if($_SESSION["user_id"]!='' && $workrights["site"]["docs"]) {
	if($action=="savedoc" && $_SESSION["siteid"]!='') {
    	mysql_query("UPDATE ".$prefix."sites SET docs='".encode_to_cp1251($_REQUEST["docs"])."',docs2='".encode_to_cp1251($_REQUEST["docs2"])."',docs3='".encode_to_cp1251($_REQUEST["docs3"])."' WHERE id=".$_SESSION["siteid"]);
    	dynamic_err_one('success','HTML-шаблоны успешно сохранены.');
	}

	$pagetitle=h1line('HTML-шаблон аусвайсов',$curdir.$kind.'/');
	$content2.='<div class="narrow">';
	$content2 .= '
<center><div class="cb_editor">
<form action="'.$curdir.$kind.'/" method="post" enctype="multipart/form-data">
<input type="hidden" name="kind" value="'.$kind.'">
<input type="hidden" name="object" value="'.$object.'">
<input type="hidden" name="action" value="savedoc">
';

	$result=mysql_query("SELECT docs,docs2,docs3 FROM ".$prefix."sites where id=".$_SESSION["siteid"]);
	$a=mysql_fetch_array($result);
	$obj_1=createElem(Array(
			'name'	=>	"docs",
			'sname'	=>	"HTML-шаблон аусвайсов",
			'type'	=>	"wysiwyg",
			'height'	=>	400,
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj_1->setVal($a);
	$obj_2=createElem(Array(
			'name'	=>	"docs2",
			'sname'	=>	"HTML-шаблон аусвайсов",
			'type'	=>	"wysiwyg",
			'height'	=>	400,
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj_2->setVal($a);
	$obj_3=createElem(Array(
			'name'	=>	"docs3",
			'sname'	=>	"HTML-шаблон аусвайсов",
			'type'	=>	"wysiwyg",
			'height'	=>	400,
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj_3->setVal($a);
	$content2.='<div><b>HTML-шаблон аусвайсов</b></div>
<div class="sm">в данном поле Вы можете нарисовать HTML-шаблон (или несколько) вашего аусвайса (или иных игровых документов) и расставить в нужных местах переменные, автоматически заменяемые на соответствующие поля из заявок при прорисовке. Если Вы хотите редактировать HTML-шаблон в виде HTML, нажмите кнопку &laquo;HTML-код&raquo;. Доступны переменные:<br>
 [Фотография]
 [Ф.И.О.]
 [Никнейм]
 [Медицинские противопоказания]
 [Взнос]
 [Взнос сдан]
 [Локация]
 [№ заявки]';
    $result=mysql_query("SELECT * FROM ".$prefix."rolefields where roletype!='h1' AND site_id=".$_SESSION["siteid"]." order by team, rolecode");
	while($a=mysql_fetch_array($result)) {
		$content2.=' ['.decode($a["rolename"]).']';
	}
	$content2.=' [Связи]
</div>
<br>

<center>
<script>
	function switch_docs($this) {
		$(".docs_switch").css("fontWeight","normal");
		$this.css("fontWeight","bold");

		$("#div_docs1").hide();
		$("#div_docs2").hide();
		$("#div_docs3").hide();

		$("#div_docs"+$this.attr("id")).show();
	}
	$(document).ready(function(){
		$(".docs_switch").on("click",function() {
			switch_docs($(this));
		});
	});
</script>
<a id="1" class="docs_switch" style="font-weight: bold">Шаблон 1</a> | <a id="2" class="docs_switch">Шаблон 2</a> | <a id="3" class="docs_switch">Шаблон 3</a></center><br />

<div id="div_docs1">
'.$obj_1->draw(2,"write").'
</div>
<div id="div_docs2" style="display: none;">
'.$obj_2->draw(2,"write").'
</div>
<div id="div_docs3" style="display: none;">
'.$obj_3->draw(2,"write").'
</div>
<div class="clear"></div>
<br />
<center><button class="main">Сохранить HTML-шаблоны</button></center>
</form></div></center>
';
	$content2.='<h1>Генератор аусвайсов</h1>';
	$content2.='<center><div class="cb_editor">
<form action="'.$curdir.'docs.php" method="post" enctype="multipart/form-data">
<input type="checkbox" name="allroles_check" id="allroles_check" class="inputcheckbox" onChange="$(\'input[type=checkbox]\').prop(\'checked\', $(this).is(\':checked\')); $(\'.inputcheckbox\').trigger(\'refresh\');" checked> <label for="allroles"><b>Все заявки</b></label><br>
<ul id="allroles">';
	$result=mysql_query("SELECT * FROM ".$prefix."roles where site_id=".$_SESSION["siteid"]." and todelete2!=1 order by team, sorter");
	while($a=mysql_fetch_array($result)) {
		$result2=mysql_query("SELECT * FROM ".$prefix."users where id=".$a["player_id"]);
		$b=mysql_fetch_array($result2);
		$content2.='<li><input type="checkbox" name="roles['.$a["id"].']" id="roles['.$a["id"].']" class="inputcheckbox" checked> <label for="roles['.$a["id"].']">'.decode($a["sorter"]).' ('.usname($b,true).')</label>';
	}
	$content2.='</ul>
<br>
<div class="fieldname" id="name_doc">Использовать:</div>
<div class="fieldvalue" id="div_doc">
<select name="doc">
<option value="1" selected>Шаблон 1</option>
<option value="2">Шаблон 2</option>
<option value="3">Шаблон 3</option>
</select>
</div>
<div class="clear"></div>
<label for="pagebreak">Каждый аусвайс/загруз на своей странице</label><input type="checkbox" name="pagebreak" id="pagebreak" class="inputcheckbox" checked>
<center><button class="main">Сгенерировать аусвайсы</button></center></form></div></center>
</div>';
}
?>