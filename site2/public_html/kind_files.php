<?php
if($_SESSION["user_id"]!='' &&  $workrights["site"]["files"]) {
	// файл-менеджер
    if($dynrequest==1) {
    	dynamic_err(array(),'submit');
    }
	#*************************************************************
	function count_size($prefix, $dir) {

		$intsize+=count_dir($dir);

		return($intsize);
	}
	#*************************************************************
	function count_dir($dir) {
		global
			$leadc1,
			$siteway,
			$leadc2;

	    $folder=substr($dir,strpos($dir,$leadc1.$siteway.$leadc2)+strlen($leadc1.$siteway.$leadc2),strlen($dir));
		$handle = @opendir($dir);
	    while ($file = @readdir ($handle))
		{
			if(preg_match("/^\\.{1,2}$/",$file))
			{
				continue;
			}

			if(is_dir($dir.$file))
			{
				$res2=count_dir($dir.$file."/");
				$intsize+=$res2;
		    }
			else
			{
				$size=filesize($dir.$file);
				if(!($file=="fm.php" && $folder==''))
				{
					$intsize+=$size;
				}
			}
	    }
	    @closedir($handle);

	    return($intsize);
	}
	#*************************************************************
	function decount($intsize){

		$totalsize=$intsize;

		$totalsize2=round($totalsize/1024/1024,2);

		$totalsize.='';
		$j=strlen($totalsize)/3;
		for($i=1;$i<$j;$i++)
		{
			$totalsize=substr($totalsize,0,strlen($totalsize)-($i*3+($i-1))).".".substr($totalsize,strlen($totalsize)-($i*3+($i-1)),($i*3+($i-1)));
		}

		$totalsize2.='';

		$res['strsizebytes']=$totalsize;
		$res['strsizembytes']=$totalsize2;

		return($res);
	}
	#*************************************************************
	function show_dir($dir){
		global
			$folder,
			$server_absolute_path,
			$siteway,
			$curdir,
			$kind,
			$lead1,
			$lead2;

		if($pos == 0)
		{
			if($folder!='')
			{
				$folder2=substr($folder,0,strlen($folder2)-1);
				$pos = strpos($folder2, "/");
				while (!($pos===false)) {
					$folder2=substr($folder2,$pos+1,strlen($folder2));
					$pos = strpos($folder2, "/");
				}
				$folder2.='/';
				$folder2=substr($folder,0,strpos($folder,$folder2));
				$res['contentcat'].='<img src="'.$server_absolute_path.'images/design/filemanager/folder.gif"> <a href="'.$curdir.$kind.'/folder='.$folder2.'"><b>../</b></a><br>';
			}
		}
	    $handle = @opendir($dir);
	    while ($file = @readdir ($handle))
		{
			if(preg_match("/^\\.{1,2}$/",$file))
			{
				continue;
			}

			if(is_dir($dir.$file))
			{
				$res['contentcat'].='<img src="'.$server_absolute_path.'images/design/filemanager/folder.gif"> <a href="'.$curdir.$kind.'/folder='.$folder.$file.'/"><b>'.$folder.$file.'</b></a>';
				$res['contentcat'].=' ('.(count_dir($dir.$file."/")+0).' байт) [<a href="'.$curdir.$kind.'/action=deletecat&name='.$folder.$file.'/&folder='.$folder.'">удалить</a>]<br>';
		    }
			else
			{
				$size=filesize($dir.$file);
				$ext=$file;
				$pos = strpos($ext, ".");
				while (!($pos===false)) {
					$ext=substr($ext,$pos+1,strlen($ext));
					$pos = strpos($ext, ".");
				}
				$handle2 = @fopen($server_absolute_path.'images/design/filemanager/'.$ext.'.gif', "r");
				if ($handle2 === false)
				{
					$ext='default.icon';
				}
				@fclose($handle2);
				$res['content'].='<img src="'.$server_absolute_path.'images/design/filemanager/'.$ext.'.gif"> <a href="'.$lead1.$siteway.$lead2.$folder.$file.'" target="_blank">'.$file.'</a> ('.$size.' байт) [<a href="'.$curdir.$kind.'/action=delete&name='.$folder.$file.'&folder='.$folder.'">удалить</a>] <br>';
			}
	    }
	    @closedir($handle);

	    return($res);
	}
	#*************************************************************
	function uploads($file,$path,$action) {
		global
			$leadc1,
			$siteway,
			$leadc2,
			$dbsize,
			$allspace,
			$HTTP_POST_FILES;

		if($action=="addfile")
		{
			$dirsize=count_dir($leadc1.$siteway.$leadc2);
			$fullsize=$dirsize+$dbsize;

			$f_name=$HTTP_POST_FILES[$file]['name'];

			$size=filesize($HTTP_POST_FILES[$file]['tmp_name']);

			if($f_name!='')
			{
				if($fullsize+$size<$allspace)
				{
					if(strpos($f_name,'.php')!==false || strpos($f_name,'.php4')!==false || strpos($f_name,'.php5')!==false)
					{
						return '!';
					}
					else
					{
						if (file_exists($path.$f_name))
						{
							unlink($path.$f_name) or die('File could not be deleted!');
						}

						move_uploaded_file($HTTP_POST_FILES[$file]['tmp_name'], $path.$f_name) or die('Could not upload main file! File size must be less than 2 Mb. Directory MOD must be 777.');
						chmod($path.$f_name, 0777);

						return '';
					}
				}
				else
				{
					return $f_name.' ('.$size.' байт)<br>
';
				}
			}
		}
		elseif($action=="delete")
		{
			if (file_exists($path.$file))
			{
				unlink($path.$file) or die('File could not be deleted!');
				err('Файл был успешно удален.');
			}
			else
			{
				err('Файл на удаление не был найден!');
			}
		}
		elseif($action=="deletecat")
		{
			$handle = @opendir($file);
			while ($file2 = @readdir ($handle))
			{
				if(preg_match("/^\\.{1,2}$/",$file2))
				{
					continue;
				}

				if(is_dir($file.$file2))
				{
					uploads($file.$file2.'/','','deletecat');
				}
				else
				{
					if(file_exists($file.$file2))
					{
						unlink($file.$file2) or die('File could not be deleted!');
					}
				}
			}
			@closedir($handle);
			rmdir($file);
		}
		elseif($action=="mkdir")
		{
			$oldumask = umask(0);
			mkdir($path.$file, 0777);
			umask($oldumask);
			err('Каталог успешно создан.');
		}
	}

	$folder=encode($_POST["folder"]);
	if($folder=='') {
		$folder=encode($_GET["folder"]);
		if($folder!='') {
			$folder=$folder;
//			$folder=iconv('utf-8', 'windows-1251', $folder);
		}
	}

	$name=encode($_POST["name"]);
	if($name=='') {
		$name=encode($_GET["name"]);
		if($name!='') {
			$name=$name;
//			$name=iconv('utf-8', 'windows-1251', $name);
		}
	}

	$result=mysql_query("SELECT * FROM ".$prefix."sites WHERE id=".$_SESSION["siteid"]);
	$a = mysql_fetch_array($result);
	$siteway=decode($a["path"]);
	$allspace=$a["allspace"];

	switch($action){
		case "addfile" :
			for($i=1;$i<6;$i++) {
				if($HTTP_POST_FILES['file'.$i]['name']!='') {
					$yes.=uploads('file'.$i, $leadc1.$siteway.$leadc2.$folder, 'addfile');
				}
			}
			if($yes=='') {
				err('Файл(-ы) успешно загружен(-ы).');
			}
			elseif($yes=='!') {
				err('Нельзя загружать php-файлы.');
			}
			else
			{
				err('Невозможно выполнить загрузку следующих файлов:<br>
'.$yes.'
т.к. будет превышен ваш допустимый объем пространства.');
			}
			break;
		case "mkdir" : if($name!='') {uploads($name, $leadc1.$siteway.$leadc2.$folder, 'mkdir');} break;
		case "delete" : if($name!='') {uploads($name, $leadc1.$siteway.$leadc2, 'delete');} break;
		case "deletecat" : if($name!='') {uploads($leadc1.$siteway.$leadc2.$name, '', 'deletecat'); err('Каталог успешно удален.');} break;
		case "recreateindex" :
            if(!file_exists($leadc1.$siteway.$leadc2.'index.php')) {
            	if(copy($server_inner_path.$admin.'/update/index.php', $leadc1.$siteway.$leadc2.'index.php')) {
            		err("index.php успешно восстановлен.");
            	}
            	else {
            		err_red("Не удалось восстановить index.php");
            	}
            	if(copy($server_inner_path.$admin.'/update/.htaccess', $leadc1.$siteway.$leadc2.'.htaccess')) {
            		err(".htaccess успешно восстановлен.");
            	}
            	else {
            		err_red("Не удалось восстановить .htaccess");
            	}
            }
			break;
	}
	# Исполнение запрошенного действия

	$dirsize=count_dir($leadc1.$siteway.$leadc2);
	$fullsize=$dirsize;

	$res=show_dir($leadc1.$siteway.$leadc2.$folder);
	$pagetitle=h1line('Структура',$curdir.$kind.'/');
	$content2.='<div class="narrow">'.$res['contentcat'].$res['content'].'</div>';

	if($fullsize<$allspace)
	{
		$content2.='<h1>Создать новый каталог</h1>
<div class="narrow">
<form action="'.$curdir.$kind.'/" method="post" enctype="multipart/form-data">
<input type="hidden" name="folder" value="'.$folder.'">
<input type="hidden" name="action" value="mkdir">
<div class="fieldname" id="name_name">Название</div>
<div class="fieldvalue" id="div_name"><input type="text" class="inputtext" name="name"></div>
<div class="clear"></div><br />
<center><button class="main">Создать новый каталог</button></center>
</form>
</div>
<h1>Загрузка файлов на сервер</h1>
<div class="narrow">
<form action="'.$curdir.$kind.'/" method="post" enctype="multipart/form-data">
<input type="hidden" name="folder" value="'.$folder.'">
<input type="hidden" name="action" value="addfile">
<div class="fieldname" id="name_file1">Файл №1</div>
<div class="fieldvalue" id="div_file1"><input type="file" name="file1" class="inputtext"></div>
<div class="clear"></div><br />
<div class="fieldname" id="name_file2">Файл №2</div>
<div class="fieldvalue" id="div_file2"><input type="file" name="file2" class="inputtext"></div>
<div class="clear"></div><br />
<div class="fieldname" id="name_file3">Файл №3</div>
<div class="fieldvalue" id="div_file3"><input type="file" name="file3" class="inputtext"></div>
<div class="clear"></div><br />
<div class="fieldname" id="name_file4">Файл №4</div>
<div class="fieldvalue" id="div_file4"><input type="file" name="file4" class="inputtext"></div>
<div class="clear"></div><br />
<div class="fieldname" id="name_file5">Файл №5</div>
<div class="fieldvalue" id="div_file5"><input type="file" name="file5" class="inputtext"></div>
<div class="clear"></div><br />
<div class="sm"><font color="red">Суммарный размер загружаемых файлов должен не превышать 2 Мб!</font></div><br />
<center><button class="main">Загрузить файлы</button><br>
';
		if(!file_exists($leadc1.$siteway.$leadc2.'index.php') || !file_exists($leadc1.$siteway.$leadc2.'.htaccess')) {
			$content2.='<button href="'.$server_absolute_path_site.$kind.'/action=recreateindex">Восстановить системные файлы для работы конструктора</button>';
		}
		$content2.='</center></form></div>';
	}
	else {
		err_red("Загрузка новых файлов невозможна, т.к. Ваш лимит дискового пространства исчерпан.");
	}

	$content2.='<h1>Статистика</h1><div class="narrow">';
	$res=decount($fullsize);
	$res2=decount($allspace);
	$content2.='Объем файлов: <b>'.$res['strsizembytes'].'</b> Мб из <b>'.$res2['strsizembytes'].'</b> Мб доступных.</div>';
}
?>