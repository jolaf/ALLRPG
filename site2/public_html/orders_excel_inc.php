<?php
{
		if($_SESSION["user_sid"]!='') {
			function roleexport()
			{
        global $prefix;
				function excel($line) {
                	$line = str_replace('<font color="red"><b>X</b></font>',"нет",$line);
					$line = str_replace('<font color="green"><b>&#8730</b></font>',"да",$line);
					$line = str_replace('&#39',"'",$line);
					$line = str_replace('&nbsp;'," ",$line);
					$line = str_replace("<br>",chr(10),$line);
					$line = strip_tags($line);
					$line = str_replace(chr(13),'',$line);
					$line = str_replace(chr(10).chr(10),chr(10),$line);
					$line = str_replace('{drn}',chr(10).chr(10),$line);
					$line = str_replace(chr(10).chr(10).chr(10),chr(10).chr(10),$line);
					$line = str_replace('"', '""', $line);
					$line = "\t".'"'.$line.'"';
					return $line;
				}
				
				function load_comments ($role_id)
				{
          global $prefix;
          $role_id = intval ($role_id);
          
          $obj_html = '';
          $result3=mysql_query("SELECT * FROM {$prefix}rolescomments WHERE role_id=$role_id order by date desc");
					while($c = mysql_fetch_array($result3)) {
						
						
						$obj_html .= ($c["type"]==3) ? 'Игрок' : 'Мастер';
						
						$b = mysql_fetch_array(mysql_query("SELECT * FROM {$prefix}users WHERE id=".$c["user_id"]));
						
						$obj_html.=' '.usname($b,true).' в '.date("G:i d.m.Y",$c["date"]).' написал';
						if($b["gender"]==2) {
							$obj_html.='а';
						}
						if($c["type"]==2) {
							$obj_html.=' другим мастерам';
						}
						elseif($c["type"]==1) {
							$obj_html.=' игроку';
						}
						$obj_html.=':
'.decode($c["content"]).'{drn}';
					}
          return $obj_html;
				}
				
				          function load_links ($role_id, $vacancy_id, $site_id)
          {
            global $prefix;
            $alllinks = '';
            $result3=mysql_query("SELECT * from {$prefix}roleslinks where (roles LIKE '%-all{$vacancy_id}-%' OR roles LIKE '%-{$role_id}-%' OR roles2 LIKE '%-all{$vacancy_id}-%' OR roles2 LIKE '%-{$role_id}-%') and site_id={$site_id} and content!='' and parent IN (SELECT id from {$prefix}roleslinks WHERE vacancies LIKE '%-{$vacancy_id}-%') order by date desc");
						while($c=mysql_fetch_array($result3)) {
							$alllinks.='Загруз для ';

							unset($roles);
							unset($roles2);
							$roles=substr($c["roles"],1,strlen($roles)-1);
							$roles2=substr($c["roles2"],1,strlen($roles2)-1);
							$roles=explode('-',$roles);
							$roles2=explode('-',$roles2);
							$dosee='его видят: мастера';
							foreach($roles as $r) {
								$query="";
								if(strpos($r,'all')!==false) {
									$result2=mysql_query("SELECT * FROM {$prefix}rolevacancy WHERE site_id={$site_id} and id=".str_replace('all','',$r));
									$b=mysql_fetch_array($result2);
									if($b["name"]!='') {
										$alllinks.=$b["name"].', ';
										$query="SELECT * from {$prefix}roles where vacancy=".$b["id"]." and site_id=".$_SESSION["siteid"];
									}
									elseif($r==0) {
										$alllinks.='глобального сюжета, ';
									}
									else {
										$alllinks.='удаленной роли, ';
									}
								}
								else {
									$query="SELECT * from {$prefix}roles where id=".$r." and site_id=".$_SESSION["siteid"];
									$result2=mysql_query($query);
									$b=mysql_fetch_array($result2);
									if($b["sorter"]!='') {
										$alllinks.=decode($b["sorter"]);
									}
									else {
										$alllinks.='удаленной заявки';
									}
									$alllinks.=', ';
								}
								if($query!='') {
									$result5=mysql_query($query);
									while($e=mysql_fetch_array($result5)) {
										if(strpos($c["roles"],'-'.$e["id"].'-')!==false) {
											$dosee.=', '.decode($e["sorter"]);
											if($b["hideother"]=='1') {
												$dosee.=' (игрок не знает, на кого конкретно у него данный загруз)';
											}
										}
										elseif(strpos($c["roles"],'-'.$r.'-')!==false) {
											$dosee.=', '.decode($e["sorter"]);
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
									$result2=mysql_query("SELECT * FROM {$prefix}rolevacancy WHERE site_id=".$_SESSION["siteid"]." and id=".str_replace('all','',$r));
									$b=mysql_fetch_array($result2);
									if($b["name"]!='') {
										$alllinks.=$b["name"].', ';
									}
									elseif($r==0) {
										$alllinks.='глобальный сюжет, ';
									}
									else {
										$alllinks.='удаленную роль, ';
									}
								}
								else {
									$result2=mysql_query("SELECT * FROM {$prefix}roles WHERE site_id=".$_SESSION["siteid"]." and id=".$r);
									$b=mysql_fetch_array($result2);
									if($b["sorter"]!='') {
										$alllinks.=decode($b["sorter"]);
									}
									else {
										$alllinks.='удаленную заявку';
									}
									$alllinks.=', ';
								}
							}
							$alllinks=substr($alllinks,0,strlen($alllinks)-2).' ('.$dosee.'){drn}';
							$result2=mysql_query("SELECT * FROM {$prefix}roleslinks WHERE id=".$c["parent"]);
							$b=mysql_fetch_array($result2);
							$alllinks.='сюжет «'.decode($b["name"]).'»{drn}';
							$alllinks.=decode($c["content"]);
							$alllinks.='{drn}{drn}';
						}
						$alllinks=substr($alllinks,0,strlen($alllinks)-8);
						
						return $alllinks;
          }

				$result=mysql_query("SELECT * from {$prefix}roles where site_id=".$_SESSION["siteid"]." and team='0' and todelete2!=1 and todelete!=1 order by status asc");
				$rolefields=virtual_structure("SELECT * from {$prefix}rolefields where site_id=".$_SESSION["siteid"]." and team='0' order by rolecode","allinfo","role");

				$header.="Взнос"."\t"."Взнос сдан"."\t"."Прогруз"."\t"."Локация"."\t"."Роль"."\t"."Статус"."\t"."ИНП"."\t"."Ф.И.О."."\t"."Никнейм"."\t"."Пол"."\t"."Дата рождения"."\t"."Медицинские противопоказания"."\t"."E-mail"."\t"."E-mail 2"."\t"."ICQ"."\t"."Телефон";
				for ($i = 0; $i < count($rolefields); $i++) {
					if($rolefields[$i]["type"]!="h1" && $rolefields[$i]["type"]!="file" && $rolefields[$i]["type"]!="timestamp" && $rolefields[$i]["type"]!="hidden")
					{
						$header.="\t".$rolefields[$i]["sname"];
					}
				}
				
				

				$header.="\tКомментарии\tСвязи";

				while($a = mysql_fetch_array($result)) {
          $id=$a["id"];

          $obj_html=load_comments($id);
					
					$vacancy_id = intval($a["vacancy"]);
					$site_id = intval ($_SESSION["siteid"]);
					$alllinks= $vacancy_id ? load_links ($id, $vacancy_id, $site_id) : '';
					

					$a=array_merge($a,unmakevirtual($a["allinfo"]));
					$line = '';
					if(decode($a["money"])=='') {
						$line.="'\t";
					}
					else {
						$line.=decode($a["money"])."\t";
					}
					if($a["moneydone"]=='1') {
						$line.="да"."\t";
					}
					else {
						$line.="нет"."\t";
					}
					if($a["alltold"]=='1') {
						$line.="да"."\t";
					}
					else {
						$line.="нет"."\t";
					}
					$line.=locatpath($a["locat"])."\t";
					if($a["vacancy"]!='') {
						$result2=mysql_query("SELECT * from {$prefix}rolevacancy where site_id=".$_SESSION["siteid"]." and id=".$a["vacancy"]);
						$b = mysql_fetch_array($result2);
						$line.=decode($b["name"])."\t";
					}
					else {
						$line.="\t";
					}
					if($a["status"]==1) {
						$line.="подана"."\t";
					}
					elseif($a["status"]==2) {
						$line.="обсуждается"."\t";
					}
					elseif($a["status"]==3) {
						$line.="принята"."\t";
					}
					elseif($a["status"]==4) {
						$line.="отклонена"."\t";
					}
					else {
						$line.="ошибка"."\t";
					}

					$result2=mysql_query("SELECT * from {$prefix}users where id=".$a["player_id"]);
					$b = mysql_fetch_array($result2);
					$line.=decode($b["sid"])."\t".decode($b["fio"])."\t".decode($b["nick"])."\t".($b["gender"]==1?'мужской':'женский')."\t".date("d.m.Y",strtotime($b["birth"]))."\t".decode2($b["sickness"]);
					if(strpos($b["hidesome"],'-2-')===false)
					{
						$line.="\t".decode($b["em"]);
					}
					else
					{
						$line.="\t"."скрыто";
					}
					if(strpos($b["hidesome"],'-3-')===false)
					{
						$line.="\t".decode($b["em2"]);
					}
					else
					{
						$line.="\t"."скрыто";
					}
					if(strpos($b["hidesome"],'-6-')===false && decode($b["icq"])!='')
					{
						$line.="\t".decode($b["icq"]);
					}
					elseif(strpos($b["hidesome"],'-6-')!==false)
					{
						$line.="\t"."скрыто";
					}
					elseif(decode($b["icq"])=='')
					{
						$line.="\t"."не указан";
					}
					if(strpos($b["hidesome"],'-5-')===false && decode($b["phone2"])!='')
					{
						$line.="\t".decode($b["phone2"]);
					}
					elseif(strpos($b["hidesome"],'-5-')!==false)
					{
						$line.="\t"."скрыто";
					}
					elseif(decode($b["phone2"])=='')
					{
						$line.="\t"."не указан";
					}
					foreach($rolefields as $f=>$v)
					{
						if($v["type"]!="h1" && $v["type"]!="file" && $v["type"]!="timestamp" && $v["type"]!="hidden")
						{
							$obj=createElem($v);
							$obj->setVal($a);
							$line.=excel($obj->draw(1,"read"));
						}
					}

					$line.=excel($obj_html);
					$line.=excel($alllinks);
					
					$data .= $line."\n";
				}

				$result=mysql_query("SELECT COUNT(id) from {$prefix}roles where site_id=".$_SESSION["siteid"]." and team='1' and todelete2!=1 order by status asc");
				$a = mysql_fetch_array($result);
				if($a[0]>0) {
					$result=mysql_query("SELECT * from {$prefix}roles where site_id=".$_SESSION["siteid"]." and team='1' and todelete2!=1 order by status asc");
					$rolefields=virtual_structure("SELECT * from {$prefix}rolefields where site_id=".$_SESSION["siteid"]." and team='1' order by rolecode","allinfo","role");

					$data.="Взнос"."\t"."Взнос сдан"."\t"."Прогруз"."\t"."Локация"."\t"."Роль"."\t"."Статус"."\t"."ИНП"."\t"."Ф.И.О."."\t"."Никнейм"."\t"."Пол"."\t"."Дата рождения"."\t"."Медицинские противопоказания"."\t"."E-mail"."\t"."E-mail 2"."\t"."ICQ"."\t"."Телефон";
					for ($i = 0; $i < count($rolefields); $i++) {
						if($rolefields[$i]["type"]!="h1" && $rolefields[$i]["type"]!="file" && $rolefields[$i]["type"]!="timestamp" && $rolefields[$i]["type"]!="hidden")
						{
							$data.="\t".$rolefields[$i]["sname"];
						}
					}
					$data.="\tКомментарии\tСвязи";

					while($a = mysql_fetch_array($result)) {
	                    $id=$a["id"];

	                    $obj_html='';
	                    $result3=mysql_query("SELECT * FROM {$prefix}rolescomments WHERE role_id=".$id." order by date desc");
						while($c = mysql_fetch_array($result3)) {
							$result2=mysql_query("SELECT * FROM {$prefix}users WHERE id=".$c["user_id"]);
							$b = mysql_fetch_array($result2);
							if($c["type"]==3) {
								$obj_html.='Команда';
							}
							else {
								$obj_html.='Мастер';
							}
							$obj_html.=' '.usname($b,true).' в '.date("G:i d.m.Y",$c["date"]).' написал';
							if($b["gender"]==2) {
								$obj_html.='а';
							}
							if($c["type"]==2) {
								$obj_html.=' другим мастерам';
							}
							elseif($c["type"]==1) {
								$obj_html.=' команде';
							}
							$obj_html.=':
'.decode($c["content"]).'{drn}';
						}

						$alllinks='';
						if($a["vacancy"]!=0) {
							$result3=mysql_query("SELECT * from {$prefix}roleslinks where (roles LIKE '%-all".$a["vacancy"]."-%' OR roles LIKE '%-".$id."-%' OR roles2 LIKE '%-all".$a["vacancy"]."-%' OR roles2 LIKE '%-".$id."-%') and site_id=".$_SESSION["siteid"]." and content!='' and parent IN (SELECT id from {$prefix}roleslinks WHERE vacancies LIKE '%-".$a["vacancy"]."-%') order by date desc");
							while($c=mysql_fetch_array($result3)) {
								$alllinks.='Загруз для ';

								unset($roles);
								unset($roles2);
								$roles=substr($c["roles"],1,strlen($roles)-1);
								$roles2=substr($c["roles2"],1,strlen($roles2)-1);
								$roles=explode('-',$roles);
								$roles2=explode('-',$roles2);
								$dosee='его видят: мастера';
								foreach($roles as $r) {
									$query="";
									if(strpos($r,'all')!==false) {
										$result2=mysql_query("SELECT * FROM {$prefix}rolevacancy WHERE site_id=".$_SESSION["siteid"]." and id=".str_replace('all','',$r));
										$b=mysql_fetch_array($result2);
										if($b["name"]!='') {
											$alllinks.=$b["name"].', ';
											$query="SELECT * from {$prefix}roles where vacancy=".$b["id"]." and site_id=".$_SESSION["siteid"];
										}
										elseif($r==0) {
											$alllinks.='глобального сюжета, ';
										}
										else {
											$alllinks.='удаленной роли, ';
										}
									}
									else {
										$query="SELECT * from {$prefix}roles where id=".$r." and site_id=".$_SESSION["siteid"];
										$result2=mysql_query($query);
										$b=mysql_fetch_array($result2);
										if($b["sorter"]!='') {
											$alllinks.=decode($b["sorter"]);
										}
										else {
											$alllinks.='удаленной заявки';
										}
										$alllinks.=', ';
									}
									if($query!='') {
										$result5=mysql_query($query);
										while($e=mysql_fetch_array($result5)) {
											if(strpos($c["roles"],'-'.$e["id"].'-')!==false) {
												$dosee.=', '.decode($e["sorter"]);
												if($b["hideother"]=='1') {
													$dosee.=' (игрок не знает, на кого конкретно у него данный загруз)';
												}
											}
											elseif(strpos($c["roles"],'-'.$r.'-')!==false) {
												$dosee.=', '.decode($e["sorter"]);
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
										$result2=mysql_query("SELECT * FROM {$prefix}rolevacancy WHERE site_id=".$_SESSION["siteid"]." and id=".str_replace('all','',$r));
										$b=mysql_fetch_array($result2);
										if($b["name"]!='') {
											$alllinks.=$b["name"].', ';
										}
										elseif($r==0) {
											$alllinks.='глобальный сюжет, ';
										}
										else {
											$alllinks.='удаленную роль, ';
										}
									}
									else {
										$result2=mysql_query("SELECT * FROM {$prefix}roles WHERE site_id=".$_SESSION["siteid"]." and id=".$r);
										$b=mysql_fetch_array($result2);
										if($b["sorter"]!='') {
											$alllinks.=decode($b["sorter"]);
										}
										else {
											$alllinks.='удаленную заявку';
										}
										$alllinks.=', ';
									}
								}
								$alllinks=substr($alllinks,0,strlen($alllinks)-2).' ('.$dosee.'){drn}';
								$result2=mysql_query("SELECT * FROM {$prefix}roleslinks WHERE id=".$c["parent"]);
								$b=mysql_fetch_array($result2);
								$alllinks.='сюжет «'.decode($b["name"]).'»{drn}';
								$alllinks.=decode($c["content"]);
								$alllinks.='{drn}{drn}';
							}
							$alllinks=substr($alllinks,0,strlen($alllinks)-8);
						}

						$a=array_merge($a,unmakevirtual($a["allinfo"]));
						$line = '';
						if(decode($a["money"])=='') {
							$line.="'\t";
						}
						else {
							$line.=decode($a["money"])."\t";
						}
						if($a["moneydone"]=='1') {
							$line.="да"."\t";
						}
						else {
							$line.="нет"."\t";
						}
						if($a["alltold"]=='1') {
							$line.="да"."\t";
						}
						else {
							$line.="нет"."\t";
						}
						$line.=locatpath($a["locat"])."\t";
						if($a["vacancy"]!='') {
							$result2=mysql_query("SELECT * from {$prefix}rolevacancy where site_id=".$_SESSION["siteid"]." and id=".$a["vacancy"]);
							$b = mysql_fetch_array($result2);
							$line.=decode($b["name"])."\t";
						}
						else {
							$line.="\t";
						}
						if($a["status"]==1) {
							$line.="подана"."\t";
						}
						elseif($a["status"]==2) {
							$line.="обсуждается"."\t";
						}
						elseif($a["status"]==3) {
							$line.="принята"."\t";
						}
						elseif($a["status"]==4) {
							$line.="отклонена"."\t";
						}
						else {
							$line.="ошибка"."\t";
						}

						$result2=mysql_query("SELECT * from {$prefix}users where id=".$a["player_id"]);
						$b = mysql_fetch_array($result2);
						$line.=decode($b["sid"])."\t".decode($b["fio"])."\t".decode($b["nick"])."\t".($b["gender"]==1?'мужской':'женский')."\t".date("d.m.Y",strtotime($b["birth"]))."\t".decode($b["sickness"]);
						if(strpos($b["hidesome"],'-2-')===false)
						{
							$line.="\t".decode($b["em"]);
						}
						else
						{
							$line.="\t"."скрыто";
						}
						if(strpos($b["hidesome"],'-3-')===false)
						{
							$line.="\t".decode($b["em2"]);
						}
						else
						{
							$line.="\t"."скрыто";
						}
						if(strpos($b["hidesome"],'-6-')===false && decode($b["icq"])!='')
						{
							$line.="\t".decode($b["icq"]);
						}
						elseif(strpos($b["hidesome"],'-6-')!==false)
						{
							$line.="\t"."скрыто";
						}
						elseif(decode($b["icq"])=='')
						{
							$line.="\t"."не указан";
						}
						if(strpos($b["hidesome"],'-5-')===false && decode($b["phone2"])!='')
						{
							$line.="\t".decode($b["phone2"]);
						}
						elseif(strpos($b["hidesome"],'-5-')!==false)
						{
							$line.="\t"."скрыто";
						}
						elseif(decode($b["phone2"])=='')
						{
							$line.="\t"."не указан";
						}
						foreach($rolefields as $f=>$v)
						{
							if($v["type"]!="h1" && $v["type"]!="file" && $v["type"]!="timestamp" && $v["type"]!="hidden")
							{
								$obj=createElem($v);
								$obj->setVal($a);
								$line.=excel($obj->draw(1,"read"));
							}
						}

						$line.=excel($obj_html);
						$line.=excel($alllinks);
						$data .= $line."\n";
					}
				}
				$data = str_replace("\r","",$data);
				if ($data == "") {
					$data = "\n(0) Records Found!\n";
				}

				header("Content-type: application/vnd.ms-excel");
				header("Content-Disposition: attachment; filename=allroles.xls");
				header("Pragma: no-cache");
				header("Expires: 0");

				$header = iconv("UTF-8","windows-1251//TRANSLIT",$header);
				$data = iconv("UTF-8","windows-1251//TRANSLIT",$data);
				print "$header\n$data";
				//print($data);
				exit;
			}
			$result=mysql_query("SELECT * from {$prefix}allrights2 where user_id=".$_SESSION['user_sid']." and site_id=".$_SESSION["siteid"]." and (rights=1 || rights=2)");
			$a=mysql_fetch_array($result);
			if($a["id"]!='' || $_SESSION["admin"])
			{
				roleexport($result);
				exit;
			}
		}
	}
?>