<?php
#*************************************************************
function show_dir($dir, $pos) {
	if($pos == 0)
	{
		$res['content']="<hr><pre>";
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
			for($i=0;$i<$pos;$i++)
			{
				$res['content'].="&nbsp;";
			}
			$res['content'].="<b>$file</b>\n";
			$res2=show_dir($dir.$file."/", $pos+3);
			$res['content'].=$res2['content'];
			$res['intsize']=$res['intsize']+$res2['intsize'];
	    }
		else
		{
			$size=filesize($dir.$file);
			for($i=0;$i<$pos;$i++)
			{
				$res['content'].="&nbsp;";
			}
			$res['content'].="$file ";
			$res['content'].="($size bytes)<br>";
            $res['intsize']=$res['intsize']+$size;
        }
    }
    @closedir($handle);

    if($pos == 0)
	{
		$res['content'].="</pre><hr>";
	}

	$totalsize=$res['intsize'];

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
function uploads($file,$type,$action,$id,$kz) {
	global
		$uploads;

	$copy=$uploads[0]["copywayto"].$type["path"];

	if($action!="delete")
	{
		if(isset($kz) && $kz>0)
		{
			$f_name=$_FILES[$file]['name'][$kz];
		}
		else
		{
			$f_name=$_FILES[$file]['name'];
		}

		if($f_name!='')
		{
			$ext='';
			$j=$type["extensions"];
			for($i=0;$i<count($j);$i++)
			{
				$test=substr($f_name,strlen($f_name)-strlen($j[$i]),strlen($j[$i]));
				if(strtoupper($j[$i])==strtoupper($test))
				{
					$ext=$j[$i];
				}
				$allowext.='*.'.$j[$i].' ';
			}
			if($ext!='')
			{
				if($type["randomname"])
				{
					if($type["thumbmake"] && $type["isimage"])
					{
						$random=makeRandomFileName($type["table"], $type["filesqlname"], $type["thumbsqlname"], $ext);
					}
					else
					{
						$random=makeRandomFileName($type["table"], $type["filesqlname"], '', $ext);
					}
				}
				else
				{
					$random=$f_name;
				}

				if(isset($kz) && $kz>0)
				{
					move_uploaded_file($_FILES[$file]['tmp_name'][$kz], $copy.$random) or die('Could not upload main file! File size must be less than 2 Mb. Directory MOD must be 777.');
				}
				else
				{
					move_uploaded_file($_FILES[$file]['tmp_name'], $copy.$random) or die('Could not upload main file! File size must be less than 2 Mb. Directory MOD must be 777.');
				}
				chmod($copy.$random, 0777);

				if($type["isimage"])
				{
					if($type["resize"])
					{
						$random3=makeRandomFileName($type["table"], $type["filesqlname"], '', $ext);

						// The file
						$filename = $copy.$random;
						$maxw=$type["maxwidth"];
						$maxh=$type["maxheight"];

						// Get new dimensions
						list($width, $height) = getimagesize($filename);
						if($width>$maxw || $height>$maxh)
						{
							$havetoberesized=true;
							$widsqrt=$width/$maxw;
							$heisqrt=$height/$maxh;
							if($widsqrt>$heisqrt)
							{
								$new_width = $width / $widsqrt;
								$new_height = $height / $widsqrt;
							}
							else
							{
								$new_width = $width / $heisqrt;
								$new_height = $height / $heisqrt;
							}
						}
						else
						{
							$new_width = $width;
							$new_height = $height;
						}

						$d=false;
						if($ext=="gif")
						{
							$image = @imagecreatefromgif($filename);
							$d=true;
						}
						elseif($ext=="jpg" || $ext=="jpeg" || $ext=="jpe")
						{
							$image = @imagecreatefromjpeg($filename);
							$d=true;
						}
						elseif($ext=="png")
						{
							$image = @imagecreatefrompng($filename);
							$d=true;
						}
						else
						{
							err_red('Система поддерживает изменение размеров только у изображений форматов: *.jpg, *.gif и *.png!');
						}

						if($d && $havetoberesized)
						{
							if(!$image)
							{
								 err_red('Не удалось изменить размер изображения!');
							}
							else
							{
								$image_p = imagecreatetruecolor($new_width, $new_height);
								imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
								$thumbname=$random3;
								if($ext=="gif")
								{
									imagegif($image_p,$copy.$thumbname);
								}
								elseif($ext=="jpg" || $ext=="jpeg" || $ext=="jpe")
								{
									imagejpeg($image_p, $copy.$thumbname, 80);
								}
								elseif($ext=="png")
								{
									imagepng($image_p,$copy.$thumbname);
								}
								unlink($copy.$random) or die('Parent file was not deleted while resizing!');
								$random=$thumbname;
							}
						}
					}
				}

				if (file_exists($copy.$random))
				{
					if($action=="change")
					{
						$z=$type["filesqlname"];
						$query="SELECT ".$z." FROM ".$type["table"]." WHERE id=".$id;
						$result=mysql_query($query);
						$a=mysql_fetch_array($result);
						if($a[$z]!='')
						{
							if (file_exists($copy.$a[$z]))
							{
								unlink($copy.$a[$z]) or die('Previous version of file could not be deleted!');
							}
							else
							{
								err_red('Предыдущая версия данного файла не обнаружена!');
							}
						}
						mysql_query("UPDATE ".$type["table"]." SET ".$type["filesqlname"]."='$random' WHERE id=".$id);
					}
					else
					{
						err_red('Не определено, что нужно делать с изображением в базах данных!');
					}
				}
				else
				{
					err_red('Файл не был обнаружен при попытке записи в БД!');
				}

				if($type["thumbmake"] && $type["isimage"] && $random!='')
				{
					$random2=makeRandomFileName($type["table"], $type["filesqlname"], $type["thumbsqlname"], $ext);

					// The file
					$filename = $copy.$random;
					$maxw=$type["thumbwidth"];
					$maxh=$type["thumbheight"];

					// Get new dimensions
					list($width, $height) = getimagesize($filename);
					if($width>$maxw || $height>$maxh)
					{
						$widsqrt=$width/$maxw;
						$heisqrt=$height/$maxh;
						if($widsqrt>$heisqrt)
						{
							$new_width = $width / $widsqrt;
							$new_height = $height / $widsqrt;
						}
						else
						{
							$new_width = $width / $heisqrt;
							$new_height = $height / $heisqrt;
						}
					}
					else
					{
						$new_width = $width;
						$new_height = $height;
					}

					$d=false;
					if($ext=="gif")
					{
						$image = @imagecreatefromgif($filename);
						$d=true;
					}
					elseif($ext=="jpg" || $ext=="jpeg" || $ext=="jpe")
					{
						$image = @imagecreatefromjpeg($filename);
						$d=true;
					}
					elseif($ext=="png")
					{
						$image = @imagecreatefrompng($filename);
						$d=true;
					}
					else
					{
						err_red('Система поддерживает создание thumbnale\'ов только у изображений форматов: *.jpg, *.gif и *.png!');
					}

					if($d)
					{
						if(!$image)
						{
							 err_red('Не удалось создать thumbnale изображения!');
						}
						else
						{
							$image_p = imagecreatetruecolor($new_width, $new_height);
							imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
							$thumbname=$random2;
							if($ext=="gif")
							{
								imagegif($image_p,$copy.$thumbname);
							}
							elseif($ext=="jpg" || $ext=="jpeg" || $ext=="jpe")
							{
								imagejpeg($image_p,$copy.$thumbname);
							}
							elseif($ext=="png")
							{
								imagepng($image_p,$copy.$thumbname);
							}
							$random2=$thumbname;

							if (file_exists($copy.$random2))
							{
								if($action=="change")
								{
									$z=$type["thumbsqlname"];
									$query="SELECT ".$z." FROM ".$type["table"]." WHERE id=".$id;
									$result=mysql_query($query);
									$a=mysql_fetch_array($result);
									if($a[$z]!='')
									{
										if (file_exists($copy.$a[$z]))
										{
											unlink($copy.$a[$z]) or die('Previous version of thumbnale could not be deleted!');
										}
										else
										{
											err_red('Предыдущая версия thumbnale\'а данного изображения не обнаружена!');
										}
									}
									mysql_query("UPDATE ".$type["table"]." SET ".$type["thumbsqlname"]."='$random2' WHERE id=".$id);

									return true;
								}
								else
								{
									err_red('Не определено, что нужно делать с изображением в базах данных!');
								}
							}
							else
							{
								err_red('Thumbnale не был обнаружен при попытке записи в БД!');
							}
						}
					}
				}
				return true;
			}
			else
			{
				err_red('Неверное расширение файла!<br> Список допустимых расширений: '.$allowext);
			}
		}
		else
		{
			err_red('Файл для загрузки не был определен!');
		}
	}
	else
	{
		if($type["thumbmake"])
		{
			$z=$type["thumbsqlname"];
			$query="SELECT ".$z." FROM ".$type["table"]." WHERE id=".$id;
			$result=mysql_query($query);
			$a=mysql_fetch_array($result);
			if($a[$z]!='')
			{
				if (file_exists($copy.$a[$z]))
				{
					unlink($copy.$a[$z]) or die('Thumbnale could not be deleted!');
					mysql_query("UPDATE ".$type["table"]." SET ".$type["thumbsqlname"]."='' WHERE id=".$id);
				}
				else
				{
					err_red('Thubmnale на удаление обнаружен не был!');
				}
			}
		}
		$z=$type["filesqlname"];
		$query="SELECT ".$z." FROM ".$type["table"]." WHERE id=".$id;
		$result=mysql_query($query);
		$a=mysql_fetch_array($result);
		if($a[$z]!='')
		{
			if (file_exists($copy.$a[$z]))
			{
				unlink($copy.$a[$z]) or die('File could not be deleted!');
				mysql_query("UPDATE ".$type["table"]." SET ".$type["filesqlname"]."='' WHERE id=".$id);

				return true;
			}
			else
			{
				err_red('Файл на удаление обнаружен не был!');
			}
		}
	}
}
#*************************************************************
function makeRandomFileName($where,$what,$what2,$ext) {
	$pass='';
	$salt = "abcdefghijklmnopqrstuvwxyz0123456789";
	srand((double)microtime()*1000000);
	$i = 0;
	while ($i <= 10) {
		$num = rand() % 36;
		$tmp = substr($salt, $num, 1);
		$pass .= $tmp;
		$i++;
	}
	$pass.=".".$ext;
	$result=mysql_query("SELECT * FROM ".$where." WHERE ".$what."=".$pass);
	$a=mysql_fetch_array($result);
	if($what2!='')
	{
		$result2=mysql_query("SELECT * FROM ".$where." WHERE ".$what2."=".$pass);
		$b=mysql_fetch_array($result2);
		if ($a!='' || $b!='')
		{
			$pass=makeRandomFileName($where,$what,$what2,$ext);
		}
	}
	else
	{
		if ($a!='')
		{
			$pass=makeRandomFileName($where,$what,$what2,$ext);
		}
	}
	return $pass;
}

?>