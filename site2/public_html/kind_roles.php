<?php
if($_SESSION["user_id"]!='' && $workrights["site"]["roles"]) {
require_once ($server_inner_path."appcode/data/roles_setup.php");
require_once ($server_inner_path."appcode/data/common.php");
	// сетка ролей

	$result2=mysql_query("SELECT id, taken from ".$prefix."rolevacancy where site_id=".$_SESSION["siteid"]);
	while($b = mysql_fetch_array($result2))
	{
		$result=mysql_query("SELECT COUNT(id) FROM ".$prefix."roles WHERE site_id=".$_SESSION["siteid"]." AND status=3 AND vacancy=".$b["id"]);
		$a = mysql_fetch_array($result);
		unset($taken);
		$taken2='';
		$taken2=decode($b["taken"]);
		$taken=explode(',',$taken2);
		if($taken[0]=='') {
			unset($taken);
		}
		$vacancycount[]=Array($b["id"],$a[0]+count($taken));
	}
	foreach ($vacancycount as $key => $row)
	{
		$vacancycount_sort[$key]  = strtolower($row[1]);
	}
	array_multisort($vacancycount_sort, SORT_ASC, $vacancycount);

	// Создание объекта
	$obj=new netObj(
		'roles',
		$prefix."rolevacancy",
		"роль",
		Array("Роль добавлена.","Роль изменена.","Роль удалена."),
		Array(
			'0'	=>	Array(
				Array("locat", "ASC", true, true, Array('3', $prefix."roleslocat", "id", "name")),
				Array("code", "ASC", true, true),
				Array("name", "ASC", true, true),
				Array("kolvo", "ASC", true, true),
				Array("id", "ASC", true, true, Array(2,$vacancycount)),
			),
		),
		2,
		'100%',
		5000
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
			'name'	=>	"locat",
			'sname'	=>	"Локация / команда",
			'type'	=>	"select",
			'help'	=>	'если поле не заполнить, в заявках не будет автоматически выставляться в соответствие с ролью локация. Дерево локаций / команд можно настроить в разделе «<a href="'.$server_absolute_path_site.'locations/">Дерево локаций / команд</a>».',
			'values'	=>	make5fieldtree(false,$prefix."roleslocat","parent",0," AND site_id=".$_SESSION["siteid"],"code asc, name asc",0,"id","name",1000000),
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_1);
	
	$types_enabled = get_enabled_roles_types ($_SESSION["siteid"]);
	
	if ($id)
	{
    $v = get_vacancy ($id);
    if ($v['team'])
    {
      $types_enabled [] = 'team';
    }
    else
    {
      $types_enabled [] = 'individual';
    }
	}
	
	$team_values = array();
	if (in_array('individual', $types_enabled))
	{
    $team_values[] = Array('0','индивидуальная');
	}
	if (in_array('team', $types_enabled))
	{
    $team_values[] = Array('1','командная');
	}

	$obj_2=createElem(Array(
			'name'	=>	"team",
			'sname'	=>	"Тип",
			'type'	=>	"select",
			'values'	=>	$team_values,
			'default'	=>	0,
			'read'	=>	10,
			'write'	=>	100,
			'help'	=>	'Вы можете создавать роли только тех типов, для которых настроены <a href="'.$server_absolute_path_site.'rolessetup/">поля заявок</a>',
			'mustbe'	=>	true,
		)

	);
	$obj->setElem($obj_2);

	$obj_3=createElem(Array(
			'name'	=>	"name",
			'sname'	=>	"Название роли",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=> true,
		)
	);
	$obj->setElem($obj_3);

	$obj_4=createElem(Array(
			'name'	=>	"kolvo",
			'sname'	=>	"Желаемое количество заявок",
			'type'	=>	"number",
			'read'	=>	10,
			'write'	=>	100,
			'default'	=> 1,
			'mustbe'	=> true,
		)
	);
	$obj->setElem($obj_4);

	$obj_13=createElem(Array(
			'name'	=>	"autonewrole",
			'sname'	=>	"Автоматическое разбитие на отдельные роли",
			'type'	=>	"checkbox",
			'read'	=>	10,
			'write'	=>	100,
			'help'	=>	'если «желаемое количество заявок» более 1, заявка на данную роль при выставлении ей статуса «обсуждается» или «принята» будет автоматически выделена в новую роль в той же локации.',
		)
	);
	$obj->setElem($obj_13);

	$obj_9=createElem(Array(
			'name'	=>	"teamkolvo",
			'sname'	=>	"Желаемое количество людей в команде",
			'type'	=>	"number",
			'read'	=>	10,
			'write'	=>	100,
			'help'	=>	'для того чтобы корректно отслеживать требуемое и имеющееся количество людей в командных заявках, при создании командной роли обозначьте в этом поле, сколько людей в среднем должно стоять за командной заявкой. Это количество будет автоматически присоединяться к названию роли в сетке ролей.',
			'default'	=> 0,
		)
	);
	$obj->setElem($obj_9);

	$obj_10=createElem(Array(
			'name'	=>	"maybetaken",
			'sname'	=>	"Предварительно занята",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	100,
			'help'	=> 'укажите через запятую в произвольной форме людей, которые предварительно заняли данную роль, но <b>не подали заявку через allrpg.info</b>.',
		)
	);
	$obj->setElem($obj_10);

	$obj_11=createElem(Array(
			'name'	=>	"taken",
			'sname'	=>	"Занята",
			'type'	=>	"text",
			'read'	=>	10,
			'write'	=>	100,
			'help'	=> 'укажите через запятую в произвольной форме людей, которые официально заняли данную роль, но <b>не подали заявку через allrpg.info</b>.',
		)
	);
	$obj->setElem($obj_11);

	$obj_5=createElem(Array(
			'name'	=>	"id",
			'sname'	=>	"Количество принятых заявок",
			'type'	=>	"select",
			'values'	=>	$vacancycount,
			'read'	=>	10,
			'write'	=>	100000,
		)
	);
	$obj->setElem($obj_5);

	$obj_6=createElem(Array(
			'name'	=>	"content",
			'sname'	=>	"Описание роли",
			'type'	=>	"textarea",
			'read'	=>	10,
			'write'	=>	100,
			'rows'	=>	10,
		)
	);
	$obj->setElem($obj_6);

	$obj_12=createElem(Array(
			'name'	=>	"code",
			'sname'	=>	"Очередность",
			'type'	=>	"number",
			'help'	=>	"очередность в сетке ролей. Опциональное поле. Чем меньше здесь цифра, тем выше в сетке ролей будет выводиться роль.",
			'default'	=>	100,
			'read'	=>	10,
			'write'	=>	100,
		)
	);
	$obj->setElem($obj_12);

	$obj_7=createElem(Array(
			'name'	=>	"site_id",
			'sname'	=>	"id сайта",
			'type'	=>	"hidden",
			'default'	=>	$_SESSION["siteid"],
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_7);

	$obj_8=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Последнее изменение",
			'type'	=>	"timestamp",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
	);
	$obj->setElem($obj_8);

	if($id!='') {
		$result3=mysql_query("SELECT * from ".$prefix."roleslinks where parent in (SELECT id from ".$prefix."roleslinks where vacancies LIKE '%-".$id."-%' and site_id=".$_SESSION["siteid"].") order by date desc");
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
						$alllinks.='<i>удаленную роль</i>, ';
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
			$result2=mysql_query("SELECT * FROM ".$prefix."roleslinks WHERE id=".$c["parent"]);
			$b=mysql_fetch_array($result2);
			$alllinks.='<span style="font-size:70%;">сюжет «<a href="'.$server_absolute_path_site.'roleslinks/'.$b["id"].'/valuestype=0">'.decode($b["name"]).'</a>»</span><br>';
			$alllinks.=decode($c["content"]);
			$alllinks.='<br><br>';
		}
		$alllinks=substr($alllinks,0,strlen($alllinks)-8);
	}
	if($alllinks!='') {
		$obj_13=createElem(Array(
				'sname'	=>	"Загрузы",
				'type'	=>	"h1",
				'read'	=>	10,
				'write'	=>	100000,
			)
		);
		$obj->setElem($obj_13);

		$obj_14=createElem(Array(
				'name'	=>	"alllinks",
				'sname'	=>	"Полный список загрузов",
				'type'	=>	"wysiwyg",
				'default'	=>	$alllinks,
				'read'	=>	10,
				'write'	=>	100000,
			)
		);
		$obj->setElem($obj_14);
	}

	// Исполнение dynamicaction, если необходимо
	if($action=="dynamicaction")
	{
		require_once($server_inner_path.$direct."/dynamicaction.php");
		if($object=="roles")
		{
			if($actiontype=="add")
			{
				function dynamic_add_success() {
					global
						$prefix,
						$_SESSION,
						$id;

					mysql_query("UPDATE ".$prefix."rolevacancy SET site_id=".$_SESSION["siteid"]." WHERE id=".$id);
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
	$pagetitle=h1line('Настройка сетки ролей',$curdir.$kind.'/');
	$content2.='<div class="narrow">'.$obj_html.'</div>';
}
?>