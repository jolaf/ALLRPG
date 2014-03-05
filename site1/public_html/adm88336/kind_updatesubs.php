<?php

// Создание объекта
$obj=new netObj(
	'updatesubs',
	$prefix."updatesubs",
	"обновление",
	Array("Обновление успешно загружено.","Обновление успешно изменено.","Обновление успешно удалено."),
	Array(
		'0'=>Array(
			Array("date", "DESC", false, true),
			Array("name", "ASC", true, true),
		)
	),
	2,
	700,
	50
);

// Создание схемы прав объекта
if($allrights["admin"])
{
	$obj_r=new netRight(
		true,
		true,
		true,
		true,
		100,
		'',
		'',
		''
	);
	$obj->setRight($obj_r);
}
else
{
	$obj_r=new netRight(
		false,
		false,
		false,
		false,
		100,
		'',
		'',
		''
	);
	$obj->setRight($obj_r);
}

// Создание полей объекта
$obj_3=createElem(Array(
			'name'	=>	"updater",
			'sname'	=>	"index.php",
			'type'	=>	"file",
			'help'	=>	"обновляет index.php и .htaccess на всех субдоменах.",
			'upload'	=>	3,
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_3);

// Исполнение dynamicaction, если необходимо
if($action=="dynamicaction")
{
	if($object=="updatesubs")
	{
		$first='';
		if(move_uploaded_file($HTTP_POST_FILES['updater']['tmp_name'], $server_inner_path.$admin.'/update/index.php')) {
			$handle2 = @fopen($server_inner_path.$admin.'/update/index.php', "r");
			$first=@fread($handle2,filesize($server_inner_path.$admin.'/update/index.php'));
			@fclose($handle2);

			$result=mysql_query("SELECT * FROM ".$prefix."sites");
			while($a = mysql_fetch_array($result)) {
				if($a["path"]!='' && ($a["usetemp"]==0 || $a["usetemp"]==1) && file_exists($leadc1.decode($a["path"]).$leadc2.'index.php')) {
					$handle2 = @fopen($leadc1.decode($a["path"]).$leadc2.'index.php', "w");
					if ($handle2 === false) {
						$errors=error_get_last();
						echo($leadc1.decode($a["path"]).$leadc2.'index.php - '.$errors['message'].'<br />');
						@fclose($handle2);
					}
					else{
						@fwrite($handle2,$first);
						echo('<!--'.decode($a["path"]).'-->
');
						@fclose($handle2);
						copy($server_inner_path.$admin.'/update/.htaccess', $leadc1.decode($a["path"]).$leadc2.'.htaccess');
						chmod($leadc1.decode($a["path"]).$leadc2.'.htaccess', 0777);
					}
				}
			}
			err("Субдомены успешно обновлены.");
		}
		else {
			err_red("Файл не перемещен.");
		}
	}
}

// Исполнение дополнительных действий после dynamicaction, если необходимо
if(!$trouble && count($trouble2)==0)
{

}

// Добавление параметра values к select'ам и multiselect'ам.

// Инициализация элементов поиска, если нужен.
//$obj->setSearch($obj_1);

// Отрисовка всего объекта html'ем в переменную
$obj_html.=$obj->draw();

// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
$content2.='<h1>ОБНОВЛЕНИЕ СУБДОМЕНОВ</h1>'.$obj_html;
?>