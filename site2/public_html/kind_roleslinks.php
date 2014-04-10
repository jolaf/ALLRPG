<?php
function trim_text($input, $length, $ellipses = true) {
    //no need to trim, already shorter than trim length
    if (strlen($input) <= $length) {
        return $input;
    }
  
    //find last space within length
    $last_space = strrpos(substr($input, 0, $length), ' ');
    $trimmed_text = substr($input, 0, $last_space);
  
    //add ellipses (...)
    if ($ellipses) {
        $trimmed_text .= '...';
    }
  
    return $trimmed_text;
}

if($_SESSION["user_id"]!='' && $workrights["site"]["roleslinks"]) {
	// сюжеты и загрузы

	// Создание объекта
	$obj=new netObj2(
		'roleslinks',
		$prefix."roleslinks",
		"сюжет",
		Array("Сюжет добавлен.","Сюжет изменен.","Сюжет удален."),
		"загруз",
		Array("Загруз добавлен.","Загруз изменен.","Загруз удален."),
		Array(
			'0'	=>	Array(
				Array("name", "ASC", true, true),
				Array("vacancies", "ASC", true, true),
			),
			'1'	=>	Array(
				Array("id", "ASC", true, true, Array(2, $fromwhomtowhom)),
			),
		),
		3,
		'100%',
		5000,
		'parent',
		'content',
		'name',
		'name'
	);

	// Создание схемы прав объекта
	if($_SESSION["siteid"]!='') {
		$obj_r=new netRight(
			true,
			true,
			true,
			true,
			100,
			'site_id='.$_SESSION["siteid"],
			'site_id='.$_SESSION["siteid"],
			'site_id='.$_SESSION["siteid"]
		);
		$obj->setRight($obj_r);
	}

	// Создание полей объекта
	$obj_1=createElem(Array(
			'name'	=>	"name",
			'sname'	=>	"Название сюжета",
			'type'	=>	"text",
			'help'	=>	'только для мастеров',
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true,
		)
	);
	$obj->setElem($obj_1);

	$obj_2=createElem(Array(
			'name'	=>	"vacancies",
			'sname'	=>	"Участники сюжета",
			'type'	=>	"multiselect",
			'values'	=>	array_merge(Array(Array('0','Глобальный сюжет')),make5field($prefix."rolevacancy where site_id=".$_SESSION["siteid"]." order by name asc","id","name")),
			'help'	=>	'список ролей можно настроить в разделе «<a href="'.$server_absolute_path_site.'roles/">Прописать сетку ролей</a>».',
			'read'	=>	10,
			'write'	=>	100,
			'default'	=>	$_REQUEST['vacancies'],
			'mustbe'	=>	true,
		)
	);
	$obj->setElem($obj_2);

	$obj_3=createElem(Array(
			'name'	=>	"descr",
			'sname'	=>	"Описание сюжета",
			'type'	=>	"textarea",
			'help'	=>	'только для мастеров',
			'rows'	=>	10,
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_3);

	$obj_4=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Последнее изменение",
			'type'	=>	"timestamp",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_4);

	$obj_13=createElem(Array(
			'name'	=>	"content",
			'type'	=>	"hidden",
			'default'	=>	"{menu}",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_13);

	$obj_5=createElem(Array(
			'name'	=>	"parent",
			'sname'	=>	"Сюжет",
			'type'	=>	"select",
			'values'	=>	make5field($prefix."roleslinks where site_id=".$_SESSION["siteid"]." and parent=0 order by name asc","id","name"),
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true,
		)
	);
	$obj->setElem2($obj_5);

    if(($id!='' || $actiontype=="add") && $actiontype!="delete") {
    	if($id>0 && encode_to_cp1251($_REQUEST["parent"])=='') {
    		$result=mysql_query("SELECT * from ".$prefix."roleslinks where id IN (SELECT parent from ".$prefix."roleslinks where site_id=".$_SESSION["siteid"]." and id=".$id.")");
    	}
    	else {
    		$result=mysql_query("SELECT * from ".$prefix."roleslinks where id=".encode_to_cp1251($_REQUEST["parent"]));
    	}
		$a=mysql_fetch_array($result);
		$values=substr($a["vacancies"],1,strlen($a["vacancies"])-2);
		$values=explode('-',$values);
		foreach($values as $v) {
	  		if($v!=0) {
		  		$result2=mysql_query("SELECT * from ".$prefix."rolevacancy where site_id=".$_SESSION["siteid"]." and id=".$v);
		  		$b=mysql_fetch_array($result2);
		  		$roles[]=Array('all'.$v,'все принятые на роль «'.decode3($b["name"]).'» заявки');
				$result2=mysql_query("SELECT * from ".$prefix."roles where vacancy=".$v." and site_id=".$_SESSION["siteid"]." and todelete2!=1 order by sorter asc");
				while($b=mysql_fetch_array($result2)) {
					$result3=mysql_query("SELECT * from ".$prefix."users where id=".$b["player_id"]);
					$c=mysql_fetch_array($result3);
					$roles[]=Array($b["id"],str_replace('&#39','`',decode3($b["sorter"])).' ('.str_replace('&#39','`',decode3(usname($c,true))).')');
				}
			}
			else {
				$roles[]=Array('all0','<i>глобальный сюжет</i>');
			}

		}
    }
	$obj_6=createElem(Array(
			'name'	=>	"roles",
			'sname'	=>	"Для",
			'type'	=>	"multiselect",
			'values'	=>	$roles,
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true,
		)
	);
	$obj->setElem2($obj_6);

	$obj_7=createElem(Array(
			'name'	=>	"roles2",
			'sname'	=>	"Про",
			'type'	=>	"multiselect",
			'values'	=>	$roles,
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true,
		)
	);
	$obj->setElem2($obj_7);

	$obj_8=createElem(Array(
			'name'	=>	"hideother",
			'sname'	=>	"Скрыть, про кого загруз",
			'type'	=>	"checkbox",
			'help'	=>	'скрыть от игроков, видящих данный загруз, про какие конкретно он роли и игроков.',
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem2($obj_8);

	/*$obj_9=createElem(Array(
			'name'	=>	"type",
			'sname'	=>	"Тип",
			'type'	=>	"select",
			'values'	=>	Array(Array(1,'положительная'),Array(2,'отрицательная'),Array(3,'нейтральная'),Array(4,'информационная')),
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem2($obj_9);*/

	$obj_10=createElem(Array(
			'name'	=>	"content",
			'sname'	=>	"Загруз",
			'type'	=>	"wysiwyg",
			'read'	=>	10,
			'write'	=>	100,
			'height'	=>	200,
			'mustbe'	=> true,
		)
	);
	$obj->setElem2($obj_10);

	$obj_16=createElem(Array(
			'name'	=>	"notready",
			'sname'	=>	"не готов",
			'type'	=>	"checkbox",
			'help'	=>	'неготовый загруз вообще не показывается игрокам.',
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem2($obj_16);

	$obj_11=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Последнее изменение",
			'type'	=>	"timestamp",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem2($obj_11);

	$obj_12=createElem(Array(
			'name'	=>	"id",
			'sname'	=>	"Заявки",
			'type'	=>	"select",
			'read'	=>	100000,
			'write'	=>	100000,
		)
	);
	$obj->setElem2($obj_12);
	
	if ($_SESSION["siteid"] == 592 || $_SESSION["siteid"] == 596)
	{

	$obj->setElem(createElem(Array(
      'name'	=>	"todo",
			'sname'	=>	"Что осталось сделать",
			'help'	=>	'ЭКСПЕРИМЕНТАЛЬНОЕ ПОЛЕ. Исчезнет в любой момент. Добавляйте сюда задачи для себя и других мастеров, или оставьте пустым, если все сделано',
			'type'	=>	"textarea",
			'read'	=>	10,
			'write'	=>	100,
			'height'	=>	200,
		)
	));
	
		$obj->setElem2(createElem(Array(
      'name'	=>	"todo",
			'sname'	=>	"Что осталось сделать",
			'help'	=>	'ЭКСПЕРИМЕНТАЛЬНОЕ ПОЛЕ. Исчезнет в любой момент.  Добавляйте сюда задачи для себя и других мастеров, или оставьте пустым, если все сделано',
			'type'	=>	"textarea",
			'read'	=>	10,
			'write'	=>	100,
			'height'	=>	200,
		)
	));
	}

	// Исполнение dynamicaction, если необходимо
	if($action=="dynamicaction")
	{
		require_once($server_inner_path.$direct."/dynamicaction.php");
		if($object=="roleslinks")
		{
			function dynamic_add_success() {
				global
					$prefix,
					$_SESSION,
					$id;

				mysql_query("UPDATE ".$prefix."roleslinks SET site_id=".$_SESSION["siteid"]." WHERE id=".$id);
			}
			dynamicaction($obj);
		}
	}

	// Добавление параметра values к select'ам и multiselect'ам.
	$result=mysql_query("SELECT * FROM ".$prefix."roleslinks WHERE site_id=".$_SESSION["siteid"]." and parent!=0");
	while($a=mysql_fetch_array($result)) {
		$fromwhomtowhomtext='';

		unset($roles);
		unset($roles2);
		$roles=substr($a["roles"],1,strlen($a["roles"])-2);
		$roles2=substr($a["roles2"],1,strlen($a["roles2"])-2);
		$roles=explode('-',$roles);
		$roles2=explode('-',$roles2);
		$fromwhomtowhomtext='</b>Для <b>';
		foreach($roles as $r) {
			if(strpos($r,'all')!==false) {
				$result2=mysql_query("SELECT * FROM ".$prefix."rolevacancy WHERE site_id=".$_SESSION["siteid"]." and id=".str_replace('all','',$r));
				$b=mysql_fetch_array($result2);
				if($b["name"]!='') {
					$fromwhomtowhomtext.=$b["name"].', ';
				}
				elseif($r==0) {
					$fromwhomtowhomtext.='<i>глобального сюжета</i>, ';
				}
				else {
					$fromwhomtowhomtext.='<i>удаленную роль</i>, ';
				}
			}
			else {
				$result2=mysql_query("SELECT * FROM ".$prefix."roles WHERE site_id=".$_SESSION["siteid"]." and id=".$r);
				$b=mysql_fetch_array($result2);
				if($b["sorter"]!='') {
					$fromwhomtowhomtext.=$b["sorter"].', ';
				}
				else {
					$fromwhomtowhomtext.='<i>заявка удалена</i>, ';
				}
			}
		}
		$fromwhomtowhomtext=substr($fromwhomtowhomtext,0,strlen($fromwhomtowhomtext)-2).'</b> про <b>';
		foreach($roles2 as $r) {
			if(strpos($r,'all')!==false) {
				$result2=mysql_query("SELECT * FROM ".$prefix."rolevacancy WHERE site_id=".$_SESSION["siteid"]." and id=".str_replace('all','',$r));
				$b=mysql_fetch_array($result2);
				if($b["name"]!='') {
					$fromwhomtowhomtext.=$b["name"].', ';
				}
				elseif($r==0) {
					$fromwhomtowhomtext.='<i>глобальный сюжет</i>, ';
				}
				else {
					$fromwhomtowhomtext.='<i>удаленную роль</i>, ';
				}
			}
			else {
				$result2=mysql_query("SELECT * FROM ".$prefix."roles WHERE site_id=".$_SESSION["siteid"]." and id=".$r);
				$b=mysql_fetch_array($result2);
				if($b["sorter"]!='') {
					$fromwhomtowhomtext.=$b["sorter"].', ';
				}
				else {
					$fromwhomtowhomtext.='<i>заявка удалена</i>, ';
				}
			}
		}
		$fromwhomtowhomtext=substr($fromwhomtowhomtext,0,strlen($fromwhomtowhomtext)-2).'</b>';
		if($a["notready"]=='1') {
			$fromwhomtowhomtext.='; <font color="red">не готов</font>';
		}
				if (trim($a["todo"]))
		{
      $todo = trim_text ($a["todo"], 30);
      $fromwhomtowhomtext.= "; <font color=\"orange\">TODO</font>: <span title=\"{$a['todo']}\">$todo</span>";
		}
		$fromwhomtowhom[]=Array($a["id"],$fromwhomtowhomtext);
	}
	$obj->setSort(Array(
		'0'	=>	Array(
			Array("name", "ASC", true, true),
		),
		'1'	=>	Array(
			Array("id", "ASC", true, false, Array(2, $fromwhomtowhom)),
		),
	));

	$obj->setSearch($obj_1);
	$obj->setSearch($obj_2);

	if($id!='' && $act=="view" && $valuestype==0 && $actiontype=='') {
		$result3=mysql_query("SELECT * from ".$prefix."roleslinks where parent=".$id);
		while($c=mysql_fetch_array($result3)) {
			$alllinks.='<b>';
			/*if($b["type"]==1) {
				$alllinks.='Положительная с';
			}
			elseif($b["type"]==2) {
				$alllinks.='Отрицательная с';
			}
			elseif($b["type"]==3) {
				$alllinks.='Нейтральная с';
			}
			elseif($b["type"]==4) {
				$alllinks.='Информационная с';
			}
			else {
				$alllinks.='С';
			}*/
			$alllinks.='<a href="'.$server_absolute_path_site.'roleslinks/'.$c["id"].'/valuestype=1">Загруз</a> ';
			/*«<a href="'.$server_absolute_path_site.'roleslinks/'.$b["id"].'/">';
			if(decode($b["name"])!='') {
				$alllinks.=decode($b["name"]);
			}
			else {
				$alllinks.='<i>без названия</i>';
			}
			$alllinks.='</a>»*/
			$alllinks.='для ';

			unset($roles);
			unset($roles2);
			$roles=substr($c["roles"],1,strlen($c["roles"])-2);
			$roles2=substr($c["roles2"],1,strlen($c["roles2"])-2);
			$roles=explode('-',$roles);
			$roles2=explode('-',$roles2);
			$dosee='его видят: мастера';
			foreach($roles as $r) {
				$query="";
				if(strpos($r,'all')!==false) {
					$result2=mysql_query("SELECT * FROM ".$prefix."rolevacancy WHERE site_id=".$_SESSION["siteid"]." and id=".str_replace('all','',$r));
					$b=mysql_fetch_array($result2);
					if($b["name"]!='') {
						$alllinks.='<a href="'.$server_absolute_path_site.'roles/'.$b["id"].'/">'.$b["name"].'</a>, ';
						$query="SELECT * from ".$prefix."roles where vacancy=".$b["id"]." and site_id=".$_SESSION["siteid"];
					}
					elseif($r==0) {
						$alllinks.='<i>глобального сюжета</i>, ';
					}
					else {
						$alllinks.='<i>удаленной роли</i>, ';
					}
				}
				else {
					$query="SELECT * from ".$prefix."roles where id=".$r." and site_id=".$_SESSION["siteid"];
					$result2=mysql_query($query);
					$b=mysql_fetch_array($result2);
					$alllinks.='<a href="'.$server_absolute_path_site.'orders/'.$b["id"].'/">';
					if($b["sorter"]!='') {
						$alllinks.=decode($b["sorter"]);
					}
					else {
						$alllinks.='<i>удаленной заявки</i>';
					}
					$alllinks.='</a>, ';
				}
				if($query!='') {
					$result5=mysql_query($query);
					while($e=mysql_fetch_array($result5)) {
						if(strpos($c["roles"],'-'.$e["id"].'-')!==false) {
							$dosee.=', <a href="'.$server_absolute_path_site.'orders/'.$e["id"].'/">'.decode($e["sorter"]).'</a>';
							if($b["hideother"]=='1') {
								$dosee.=' (игрок не знает, на кого конкретно у него данный загруз)';
							}
						}
						elseif(strpos($c["roles"],'-'.$r.'-')!==false) {
							$dosee.=', <a href="'.$server_absolute_path_site.'orders/'.$e["id"].'/">'.decode($e["sorter"]).'</a>';
							if($e["status"]<3) {
								$dosee.=' (увидит, как только заявка будет принята)';
							}
							if($b["hideother"]=='1') {
								$dosee.=' (игрок не знает, на кого конкретно у него данный загруз)';
							}
						}
					}
				}
			}
			$alllinks=substr($alllinks,0,strlen($alllinks)-2).' про ';
			foreach($roles2 as $r) {
				if(strpos($r,'all')!==false) {
					$result2=mysql_query("SELECT * FROM ".$prefix."rolevacancy WHERE site_id=".$_SESSION["siteid"]." and id=".str_replace('all','',$r));
					$b=mysql_fetch_array($result2);
					if($b["name"]!='') {
						$alllinks.='<a href="'.$server_absolute_path_site.'roles/'.$b["id"].'/">'.$b["name"].'</a>, ';
					}
					elseif($r==0) {
						$alllinks.='<i>глобальный сюжет</i>, ';
					}
					else {
						$fromwhomtowhomtext.='<i>удаленную роль</i>, ';
					}
				}
				else {
					$result2=mysql_query("SELECT * FROM ".$prefix."roles WHERE site_id=".$_SESSION["siteid"]." and id=".$r);
					$b=mysql_fetch_array($result2);
					$alllinks.='<a href="'.$server_absolute_path_site.'orders/'.$b["id"].'/">';
					if($b["sorter"]!='') {
						$alllinks.=decode($b["sorter"]);
					}
					else {
						$alllinks.='<i>удаленную заявку</i>';
					}
					$alllinks.='</a>, ';
				}
			}
			$alllinks=substr($alllinks,0,strlen($alllinks)-2).' ('.$dosee.')</b><br>';
			$alllinks.=decode($c["content"]);
			$alllinks.='<br><br>';
		}
		$alllinks=substr($alllinks,0,strlen($alllinks)-8);
	}
	if($alllinks!='') {
		$obj_14=createElem(Array(
				'sname'	=>	"Загрузы",
				'type'	=>	"h1",
				'read'	=>	10,
				'write'	=>	100000,
			)
		);
		$obj->setElem($obj_14);

		$obj_15=createElem(Array(
				'name'	=>	"alllinks",
				'sname'	=>	"Полный список загрузов",
				'type'	=>	"wysiwyg",
				'default'	=>	$alllinks,
				'read'	=>	10,
				'write'	=>	100000,
			)
		);
		$obj->setElem($obj_15);
	}

	// Отрисовка всего объекта html'ем в переменную
	$obj_html.=$obj->draw();

	// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
	$pagetitle=h1line('Сюжеты и загрузы',$curdir.$kind.'/');
	$content2.='<div class="narrow">'.$obj_html.'</div>';
	$content2=str_replace('<select name="parent"','<select name="parent" onChange="getMultiList(\''.$helpers_path.'roleslinks_lists.php\',[\'roles\',\'roles2\'],this.value,\'\');"',$content2);
}
?>