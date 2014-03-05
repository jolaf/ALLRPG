<?php
if($_SESSION["candoarticles"]) {
	$bazecount=$_SESSION["bazecount"];
	if($bazecount=='') {
		$bazecount=50;
	}

	//мои статьи в базе

	// Создание объекта
	$obj=new netObj(
		'myarticles',
		$prefix."articles",
		"статью",
		Array("Статья успешно добавлена.","Статья успешно изменена.","Статья успешно удалена."),
		Array(
			'0'=>Array(
				Array("code", "ASC", false, true),
				Array("name", "ASC", true, true),
				Array("date", "ASC", true, true),
			)
		),
		2,
		'100%',
		$bazecount
	);

	// Создание схемы прав объекта
	if($_SESSION["candoarticles"]) {
		$obj_r=new netRight(
			true,
			true,
			true,
			true,
			100,
			'user_id='.$_SESSION["user_sid"],
			'user_id='.$_SESSION["user_sid"],
			'user_id='.$_SESSION["user_sid"]
		);
		$obj->setRight($obj_r);
	}

	if($id>0)
	{
		$result=mysql_query("SELECT * from ".$prefix."articles where id=".$id);
		$a=mysql_fetch_array($result);
		$result2=mysql_query("SELECT * from ".$prefix."articles where id=".$a["parent"]);
		$b=mysql_fetch_array($result2);
		$lin='<a href="'.$server_absolute_path_info.'articles/'.$id.'/subobj='.$b["id"].'" target="_blank">'.$server_absolute_path_info.'articles/'.$id.'/subobj='.$b["id"].'</a>';
	}

	$obj_9=createElem(Array(
				'name'	=>	"lin",
				'sname'	=>	"Ссылка на статью на сайте",
				'type'	=>	"text",
				'default'	=>	$lin,
				'read'	=>	10,
				'write'	=>	100000,
			)
	);
	$obj->setElem($obj_9);

	$obj_10=createElem(Array(
				'name'	=>	"parent",
				'sname'	=>	"Разместить в разделе",
				'type'	=>	"select",
				'read'	=>	10,
				'write'	=>	100,
				'mustbe'	=>	true
			)
	);
	$obj->setElem($obj_10);

	$obj_11=createElem(Array(
				'name'	=>	"code",
				'type'	=>	"hidden",
				'default'	=>	'0',
				'read'	=>	10,
				'write'	=>	100,
				'mustbe'	=>	true
			)
	);
	$obj->setElem($obj_11);

	$obj_12=createElem(Array(
				'name'	=>	"name",
				'sname'	=>	"Название статьи",
				'type'	=>	"text",
				'help'	=>	"не более 255 символов.",
				'read'	=>	10,
				'write'	=>	100,
				'mustbe'	=>	true,
			)
	);
	$obj->setElem($obj_12);

	$obj_13=createElem(Array(
				'name'	=>	"content2",
				'sname'	=>	"Дополнительный текст под названием статьи",
				'type'	=>	"textarea",
				'read'	=>	10,
				'write'	=>	100,
			)
	);
	$obj->setElem($obj_13);

	$obj_14=createElem(Array(
				'name'	=>	"author",
				'sname'	=>	"Автор (-ы)",
				'type'	=>	"text",
				'help'	=>	"в данное поле можно ввести Ф.И.О. автора или же указать его ИНП на allrpg.info (одно из двух). Во втором случае имя автора статьи на сайте будет превращено в ссылку, ведущую на его карточку в <a href=\"http://".$server_absolute_path_info."users/\">инфотеке</a>. Можно также указывать несколько фамилий или ИНП через запятую. Если автор не известен, так и напишите.",
				'read'	=>	10,
				'write'	=>	100,
				'mustbe'	=>	true,
			)
	);
	$obj->setElem($obj_14);

	$obj_15=createElem(Array(
				'name'	=>	"active",
				'sname'	=>	"Показывать статью",
				'type'	=>	"checkbox",
				'default'	=>	1,
				'read'	=>	10,
				'write'	=>	100,
			)
	);
	$obj->setElem($obj_15);

	$obj_16=createElem(Array(
				'name'	=>	"content",
				'sname'	=>	"Содержимое",
				'type'	=>	"wysiwyg",
				'height'	=>	400,
				'read'	=>	10,
				'write'	=>	100,
			)
	);
	$obj->setElem($obj_16);

	$obj_17=createElem(Array(
				'name'	=>	"nocomments",
				'sname'	=>	"Отключить комментарии к статье",
				'type'	=>	"checkbox",
				'read'	=>	10,
				'write'	=>	100,
			)
	);
	$obj->setElem($obj_17);

	$obj_18=createElem(Array(
				'name'	=>	"tags",
				'sname'	=>	"Теги",
				'type'	=>	"multiselect",
				'values'	=>	make5field($prefix."tags order by code asc, name asc","id","name"),
				'read'	=>	10,
				'write'	=>	100,
				'cols'	=>	3,
			)
	);
	$obj->setElem($obj_18);

	$obj_19=createElem(Array(
				'name'	=>	"date",
				'sname'	=>	"Последнее изменение",
				'type'	=>	"timestamp",
				'read'	=>	100,
				'write'	=>	100,
				'mustbe'	=>	true,
				'show'	=>	true,
			)
	);
	$obj->setElem($obj_19);

	// Исполнение dynamicaction, если необходимо
	if($action=="dynamicaction")
	{
		require_once($server_inner_path.$direct."/dynamicaction.php");
		if($object=="myarticles")
		{
			function dynamic_add_success() {
				global
					$prefix,
					$_SESSION,
					$id;

				mysql_query("UPDATE ".$prefix."articles SET user_id=".$_SESSION['user_sid']." WHERE id=".$id);
			}
			dynamicaction($obj);
		}
	}

	// Добавление параметра values к select'ам и multiselect'ам.
	$obj_10->setValues(make5fieldtree(false,$prefix."articles","parent",0," AND content='{menu}'","code asc",1,"id","name",1000000));

	$pagetitle=h1line('Мои статьи',$curdir.$kind.'/');
	$content2.='<div class="narrow">'.$obj->draw().'</div>';
}
?>