<?php
if($this_type==1 || ($this_type!=1 && !(($id!='' && $act=='view' && $stayhere) || ($id=='' && $act=="add"))))
{
	$filter_id=encode($_GET["filter_id"]);
	if($action=="filterdelete" && $filter_id!='') {
		unset($_SESSION['indexers'][$kind]["savedfilters"][$obj->getName()][$filter_id]);
		for($i=$filter_id+1;$i<=count($_SESSION['indexers'][$kind]["savedfilters"][$obj->getName()]);$i++) {
			if($_SESSION['indexers'][$kind]["savedfilters"][$obj->getName()][$i-1]=='' && $_SESSION['indexers'][$kind]["savedfilters"][$obj->getName()][$i]!='') {
				$_SESSION['indexers'][$kind]["savedfilters"][$obj->getName()][$i-1]=$_SESSION['indexers'][$kind]["savedfilters"][$obj->getName()][$i];
				unset($_SESSION['indexers'][$kind]["savedfilters"][$obj->getName()][$i]);
			}
		}
		dynamic_err(array(array('success',"Выборка фильтров удалена.")),'');
	}
	if($action=="filtersave") {
		$filter_name=encode($_GET["filtername"]);
//		$filter_name=iconv('UTF-8','windows-1251',encode($_GET["filtername"]));
		if($filter_name!='') {
			for($i=0;$i<count($_SESSION['indexers'][$kind]["savedfilters"][$obj->getName()]);$i++) {
				if($_SESSION['indexers'][$kind]["savedfilters"][$obj->getName()][$i]!='') {
					if($_SESSION['indexers'][$kind]["savedfilters"][$obj->getName()][$i]['searchname']==$filter_name) {
						dynamic_err_one('error',"Такое название выборки фильтров уже занято. Выборка фильтров не сохранена.");
						$foundsavedfilter=true;
						break;
					}
				}
			}
		}
		else {
			dynamic_err_one('error',"Для сохранения выборки фильтров необходимо ввести ее имя!");
		}
		if(!$foundsavedfilter && $filter_name!='') {
			$nextsavedfilter=0;
			for($i=0;$i<1000;$i++) {
				if($_SESSION['indexers'][$kind]["savedfilters"][$obj->getName()][$i]=='') {
					$nextsavedfilter=$i;
					break;
				}
				else {
					$nextsavedfilter=$i+1;
				}
			}
			$_SESSION['indexers'][$kind]["savedfilters"][$obj->getName()][$nextsavedfilter]=$_SESSION['indexers'][$kind][$obj->getName()];
			$_SESSION['indexers'][$kind]["savedfilters"][$obj->getName()][$nextsavedfilter]['searchname']=$filter_name;
			dynamic_err_one('success',"Выборка фильтров сохранена.");
		}
	}
	if($this_type==3)
	{
		$this_content=$obj->getContent();
	}
	$h=0;
	$textfieldsexistinsearch=false;
	for($i=0;$i<count($this_search);$i++)
	{
		if($this_search[$i]->getType()=="text" || $this_search[$i]->getType()=="login" || $this_search[$i]->getType()=="textarea" || $this_search[$i]->getType()=="email" || $this_search[$i]->getType()=="wysiwyg")
		{
			$textfieldsexistinsearch=true;
			break;
		}
	}
	if($textfieldsexistinsearch)
	{
		$indexer[$h][]=createElem(Array(
				'name'	=>	"search_alltextfields",
				'sname'	=>	"Поиск в текстовых полях",
				'type'	=>	"text",
				'read'	=>	10,
				'write'	=>	10,
				'br'	=>	true,
			)
		);
		$indexerobjs[$h][]='';
		for($i=0;$i<count($this_search);$i++)
		{
			if($this_search[$i]->getType()=="text" || $this_search[$i]->getType()=="login" || $this_search[$i]->getType()=="textarea" || $this_search[$i]->getType()=="email" || $this_search[$i]->getType()=="wysiwyg")
			{
				$indexer[$h][]=createElem(Array(
						'name'	=>	$this_search[$i]->getName(),
						'sname'	=>	$this_search[$i]->getSname(),
						'type'	=>	"checkbox",
						'read'	=>	10,
						'write'	=>	10,
					)
				);
				$indexerobjs[$h][]=$this_search[$i];
			}
		}
		$h++;
	}

	if($this_type==3) {
		$showchilds=createElem(Array(
				'name'	=>	'showchilds',
				'sname'	=>	'Показывать ветки наследующих объектов',
				'type'	=>	"file",
				'read'	=>	10,
				'write'	=>	10,
			)
		);
		$obj->setSearch($showchilds);
		$this_search=$obj->getSearch();
	}
	for($i=0;$i<count($this_search);$i++)
	{
		if($this_search[$i]->getType()!="text" && $this_search[$i]->getType()!="login" && $this_search[$i]->getType()!="textarea" && $this_search[$i]->getType()!="email" && $this_search[$i]->getType()!="wysiwyg" && $this_search[$i]->getType()!="h1" && $this_search[$i]->getType()!="hidden")
		{
			if($this_search[$i]->getType()=="sarissa") {
				$indexer[$h][]=$this_search[$i];
				$indexerobjs[$h][]=$this_search[$i];
			}
			elseif($this_search[$i]->getType()=="multiselect") {
				$indexer[$h][]=$this_search[$i];
				$indexerobjs[$h][]=$this_search[$i];
				$indexer[$h][]=createElem(Array(
						'name'	=>	$this_search[$i]->getName().'select',
						'sname'	=>	$this_search[$i]->getSname(),
						'type'	=>	"multiselect",
						'read'	=>	10,
						'write'	=>	10,
						'default'	=>	'-1-',
						'values'	=>	Array(Array('1','любое сочетание'),Array('2','точное совпадение')),
						'one'	=>	true
					)
				);
				$indexerobjs[$h][]='';
			}
			elseif($this_search[$i]->getType()=="select") {
				$s=count($this_search[$i]->getValues());
				if($s>30)
				{
					$indexer[$h][]=createElem(Array(
							'name'	=>	$this_search[$i]->getName(),
							'sname'	=>	$this_search[$i]->getSname(),
							'type'	=>	"multiselect",
							'read'	=>	10,
							'write'	=>	10,
							'cols'	=>	ceil($s/30),
							'width'	=>	$this_search[$i]->getWidth(),
							'values'	=>	$this_search[$i]->getValues()
						)
					);
				}
				else
				{
					$indexer[$h][]=createElem(Array(
							'name'	=>	$this_search[$i]->getName(),
							'sname'	=>	$this_search[$i]->getSname(),
							'type'	=>	"multiselect",
							'read'	=>	10,
							'write'	=>	10,
							'width'	=>	$this_search[$i]->getWidth(),
							'values'	=>	$this_search[$i]->getValues()
						)
					);
				}
				$indexerobjs[$h][]=$this_search[$i];
			}
			elseif($this_search[$i]->getType()=="calendar") {
				$indexer[$h][]=createElem(Array(
						'name'	=>	$this_search[$i]->getName().'select',
						'sname'	=>	$this_search[$i]->getSname(),
						'type'	=>	"select",
						'read'	=>	10,
						'write'	=>	10,
						'values'	=>	Array(Array('1','='),Array('2','<>'),Array('3','>'),Array('4','<')),
						'br'	=>	true
					)
				);
				$indexerobjs[$h][]='';
				$this_search[$i]->setDefault(date("Y-m-d"));
				$indexer[$h][]=$this_search[$i];
				$indexerobjs[$h][]=$this_search[$i];
			}
			elseif($this_search[$i]->getType()=="file") {
				$indexer[$h][]=createElem(Array(
						'name'	=>	$this_search[$i]->getName(),
						'sname'	=>	$this_search[$i]->getSname(),
						'type'	=>	"checkbox",
						'read'	=>	10,
						'write'	=>	10,
					)
				);
				$indexerobjs[$h][]=$this_search[$i];
			}
			elseif($this_search[$i]->getType()=="checkbox") {
				$indexer[$h][]=createElem(Array(
						'name'	=>	$this_search[$i]->getName(),
						'sname'	=>	$this_search[$i]->getSname(),
						'type'	=>	"multiselect",
						'read'	=>	10,
						'write'	=>	10,
						'default'	=>	'-0-',
						'values'	=>	Array(Array('0','не важно'),Array('1','<font color="green"><b>&#8730</b></font>'),Array('2','<font color="red"><b>X</b></font>')),
						'one'	=>	true
					)
				);
				$indexerobjs[$h][]=$this_search[$i];
			}
			elseif($this_search[$i]->getType()=="number") {
				if($this_search[$i]->getVirtual())
				{
					$indexer[$h][]=createElem(Array(
							'name'	=>	$this_search[$i]->getName().'select',
							'sname'	=>	$this_search[$i]->getSname(),
							'type'	=>	"select",
							'read'	=>	10,
							'write'	=>	10,
							'values'	=>	Array(Array('1','='),Array('2','<>')),
							'br'	=>	true,
							'width'	=>	'39%'
						)
					);
				}
				else
				{
					$indexer[$h][]=createElem(Array(
							'name'	=>	$this_search[$i]->getName().'select',
							'sname'	=>	$this_search[$i]->getSname(),
							'type'	=>	"select",
							'read'	=>	10,
							'write'	=>	10,
							'values'	=>	Array(Array('1','='),Array('2','<>'),Array('3','>'),Array('4','<')),
							'br'	=>	true,
							'width'	=>	'39%'
						)
					);
				}
				$indexerobjs[$h][]='';
				$indexer[$h][]=$this_search[$i];
				$indexerobjs[$h][]=$this_search[$i];
			}
			elseif($this_search[$i]->getType()=="timestamp") {
				$indexer[$h][]=createElem(Array(
						'name'	=>	$this_search[$i]->getName().'select',
						'sname'	=>	$this_search[$i]->getSname(),
						'type'	=>	"select",
						'read'	=>	10,
						'write'	=>	10,
						'values'	=>	Array(Array('1','='),Array('2','<>'),Array('3','>'),Array('4','<')),
						'br'	=>	true
					)
				);
				$indexerobjs[$h][]='';
				$indexer[$h][]=createElem(Array(
						'name'	=>	$this_search[$i]->getName(),
						'sname'	=>	$this_search[$i]->getSname(),
						'type'	=>	"calendar",
						'read'	=>	10,
						'write'	=>	10,
						'default'	=>	date("Y-m-d"),
						'br'	=>	true
					)
				);
				$indexerobjs[$h][]=$this_search[$i];
			}
			$h++;
		}
	}

	//print_r($indexer);

	if($action=="dynamicindex")
	{
		// сохраняем все значения фильтров в $_SESSION['indexers'][$kind]['objname'] и создаем дополнительный запрос для $query, который тоже храним в $_SESSION['indexers'][$kind]['objname']

		// определяем POST или GET-запрос на поиск к нам пришел
		if(encode($_POST["action"])=="dynamicindex") {
			$dataarray=$_POST;
		}
		elseif(encode($_GET["action"])=="dynamicindex") {
			$dataarray=$_GET;
		}
		else {
			dynamic_err_one('error',"Ошибка при определении массива для фильтров.");
		}

		$alltextfields_query=encode_to_cp1251($dataarray["search_alltextfields"]);
		$alltextfields=split(" ",$alltextfields_query);
		if($this_right->getViewRestrict()=='')
		{
			$searchquery=' WHERE';
		}
		$firstsearchquery=true;
		for($j=0;$j<count($indexer);$j++)
		{
			for($h=0;$h<count($indexer[$j]);$h++)
			{
				$elemis2=false;
				if(stripos($indexer[$j][$h]->getName(),"search2_")!==false) {
					$elemis2=true;
				}
				if($indexerobjs[$j][$h]!='')
				{
					if($indexerobjs[$j][$h]->getType()=='multiselect' || $indexerobjs[$j][$h]->getType()=='select')
					{
						$res='-';
						$vals=$indexer[$j][$h]->getValues();
						$selectbreaks=true;
						for($i=0;$i<count($vals);$i++)
						{
							if($dataarray[$indexer[$j][$h]->getName()][$vals[$i][0]]=='on')
							{
								$res.=$vals[$i][0].'-';
								if($indexerobjs[$j][$h]->getType()=="select") {
									if(!$firstsearchquery)
									{
										if($selectbreaks)
										{
											$searchquery.=' AND (';
											$selectbreaks=false;
										}
										else
										{
											$searchquery.=' OR';
										}
									}

									if($firstsearchquery && $selectbreaks) {
										$searchquery.=' (';
										$selectbreaks=false;
									}
									if($elemis2)
									{
										$queryelemname=str_ireplace("search2_","",$indexer[$j][$h]->getName());
									}
									else
									{
										$queryelemname=str_ireplace("search_","",$indexer[$j][$h]->getName());
									}
									if($this_type==3)
									{
										$searchquery.='(';
									}
									if($indexerobjs[$j][$h]->getVirtual())
									{
										// о боже, это еще и виртуальный селект!
										$searchquery.=' t1.'.$obj->getVirtualField().' LIKE "%['.$queryelemname.']['.$vals[$i][0].']%"';
									}
									else
									{
										$searchquery.=' t1.'.$queryelemname.'='.$vals[$i][0];
									}
									if($this_type==3)
									{
										if($elemis2)
										{
											$searchquery.=' AND t1.'.$this_content.'!="{menu}")';
										}
										else
										{
											$searchquery.=' AND t1.'.$this_content.'="{menu}")';
										}
									}
									$firstsearchquery=false;
								}
								elseif($indexerobjs[$j][$h]->getType()=="multiselect") {
									if($elemis2)
									{
										$queryelemname=str_ireplace("search2_","",$indexer[$j][$h]->getName());
									}
									else
									{
										$queryelemname=str_ireplace("search_","",$indexer[$j][$h]->getName());
									}
									if(!$firstsearchquery)
									{
										if($selectbreaks)
										{
											$searchquery.=' AND (';
											$selectbreaks=false;
										}
										else
										{
											// если здесь поставить AND, то при поиске в мультиселектах нужно будет совпадение со всеми поисковыми галочками, выставленными пользователями. Если OR, то хотя бы с одной из них.
											if($dataarray[$indexer[$j][$h+1]->getName()]=='2')
											{
												$searchquery.=' AND';
											}
											else
											{
												$searchquery.=' OR';
											}
										}
									}
									if($firstsearchquery && $selectbreaks) {
										$searchquery.=' (';
										$selectbreaks=false;
									}
									if($this_type==3)
									{
										$searchquery.='(';
									}
									if($indexerobjs[$j][$h]->getVirtual())
									{
										// о боже, это еще и виртуальный множественный выбор!
										$searchquery.=' t1.'.$obj->getVirtualField().' REGEXP "\\\['.$queryelemname.'\\\]\\\[[^]]*-'.$vals[$i][0].'-[^]]*"';
									}
									else
									{
										$searchquery.=' t1.'.$queryelemname.' LIKE "%-'.$vals[$i][0].'-%"';
									}
									if($this_type==3)
									{
										if($elemis2)
										{
											$searchquery.=' AND t1.'.$this_content.'!="{menu}")';
										}
										else
										{
											$searchquery.=' AND t1.'.$this_content.'="{menu}")';
										}
									}
									$firstsearchquery=false;
								}
							}
							if(!isset($vals[$i+1]) && !$selectbreaks) {
								$searchquery.=')';
							}
						}
						$_SESSION['indexers'][$kind][$obj->getName()][$indexer[$j][$h]->getName()]=$res;
						if($indexerobjs[$j][$h]->getType()=='multiselect')
						{
							$h++;
							$_SESSION['indexers'][$kind][$obj->getName()][$indexer[$j][$h]->getName()]='-'.encode_to_cp1251($dataarray[$indexer[$j][$h]->getName()]).'-';
						}
					}
					else
					{
						$_SESSION['indexers'][$kind][$obj->getName()][$indexer[$j][$h]->getName()]=encode_to_cp1251($dataarray[$indexer[$j][$h]->getName()]);
						if($indexer[$j][$h]->getType()=="checkbox" && $dataarray[$indexer[$j][$h]->getName()]=="on")
						{
							if(($indexerobjs[$j][$h]->getType()=="text" || $indexerobjs[$j][$h]->getType()=="login" || $indexerobjs[$j][$h]->getType()=="textarea" || $indexerobjs[$j][$h]->getType()=="email" || $indexerobjs[$j][$h]->getType()=="wysiwyg") && $alltextfields[0]!='')
							{
								if($textfieldsexistinsearch && $firstsearchquery)
								{
									$searchquery.=' (';
								}
								if($elemis2)
								{
									$queryelemname=str_ireplace("search2_","",$indexer[$j][$h]->getName());
								}
								else
								{
									$queryelemname=str_ireplace("search_","",$indexer[$j][$h]->getName());
								}

								if(!$firstsearchquery)
								{
									$searchquery.=' OR';
								}
								if($this_type==3)
								{
									$searchquery.='(';
								}
								if($indexerobjs[$j][$h]->getVirtual())
								{
									// о боже, это еще и виртуальное текстовое поле!
									for($x=0;$x<count($alltextfields);$x++)
									{
										$searchquery.=' t1.'.$obj->getVirtualField().' REGEXP "\\\['.$queryelemname.'\\\]\\\[[^]]*'.$alltextfields[$x].'[^]]*" OR';
										$firstsearchquery=false;
									}
									$searchquery=substr($searchquery,0,strlen($searchquery)-3);
								}
								else
								{
									$searchquery.=' t1.'.$queryelemname.' REGEXP "';
									for($x=0;$x<count($alltextfields);$x++)
									{
										$searchquery.=$alltextfields[$x].'|';
										$firstsearchquery=false;
									}
									$searchquery=substr($searchquery,0,strlen($searchquery)-1);
									$searchquery.='"';
								}
								if($this_type==3)
								{
									if($elemis2)
									{
										$searchquery.=' AND t1.'.$this_content.'!="{menu}")';
									}
									else
									{
										$searchquery.=' AND t1.'.$this_content.'="{menu}")';
									}
								}
								$theresmorefieldstosearch=false;
								if(isset($indexer[$j][$h+1]))
								{
									for($z=$h+1;$z<count($indexer[$j]);$z++) {
										if($dataarray[$indexer[$j][$z]->getName()]=="on")
										{
											$theresmorefieldstosearch=true;
										}
									}
								}
								if(!$theresmorefieldstosearch) {
									$searchquery.=')';
								}
							}
							elseif($indexerobjs[$j][$h]->getType()=="file" && $dataarray[$indexer[$j][$h]->getName()]=="on" && $indexer[$j][$h]->getName()!='search_showchilds')
							{
								$upload=$indexerobjs[$j][$h]->getUpload();
								$queryelemname=$upload["filesqlname"];
								if(!$firstsearchquery)
								{
									$searchquery.=' AND';
								}
								if($this_type==3)
								{
									$searchquery.=' (';
								}
								$searchquery.=' t1.'.$queryelemname.'!=""';
								$firstsearchquery=false;
								if($this_type==3)
								{
									if($elemis2)
									{
										$searchquery.=' AND t1.'.$this_content.'!="{menu}")';
									}
									else
									{
										$searchquery.=' AND t1.'.$this_content.'="{menu}")';
									}
								}
							}
						}
						elseif($indexer[$j][$h]->getType()=="multiselect" && $indexerobjs[$j][$h]->getType()=="checkbox" && encode_to_cp1251($dataarray[$indexer[$j][$h]->getName()])!='') {
							if(encode_to_cp1251($dataarray[$indexer[$j][$h]->getName()])>0)
							{
								if($elemis2)
								{
									$queryelemname=str_ireplace("search2_","",$indexer[$j][$h]->getName());
								}
								else
								{
									$queryelemname=str_ireplace("search_","",$indexer[$j][$h]->getName());
								}
								if(!$firstsearchquery)
								{
									$searchquery.=' AND';
								}
								if($this_type==3)
								{
									$searchquery.=' (';
								}
								if($indexerobjs[$j][$h]->getVirtual())
								{
									// о боже, это еще и виртуальный checkbox!
									if($dataarray[$indexer[$j][$h]->getName()]=='1')
									{
										$searchquery.=' t1.'.$obj->getVirtualField().' LIKE "%['.$queryelemname.'][1]%"';
									}
									elseif($dataarray[$indexer[$j][$h]->getName()]=='2')
									{
										$searchquery.=' t1.'.$obj->getVirtualField().' LIKE "%['.$queryelemname.'][0]%"';
									}
								}
								else
								{
									if($dataarray[$indexer[$j][$h]->getName()]=='1')
									{
										$searchquery.=' t1.'.$queryelemname.'="1"';
									}
									elseif($dataarray[$indexer[$j][$h]->getName()]=='2')
									{
										$searchquery.=' t1.'.$queryelemname.'!="1"';
									}
								}
								if($this_type==3)
								{
									if($elemis2)
									{
										$searchquery.=' AND t1.'.$this_content.'!="{menu}")';
									}
									else
									{
										$searchquery.=' AND t1.'.$this_content.'="{menu}")';
									}
								}
								$firstsearchquery=false;
							}
							$_SESSION['indexers'][$kind][$obj->getName()][$indexer[$j][$h]->getName()]='-'.encode_to_cp1251($dataarray[$indexer[$j][$h]->getName()]).'-';
						}
						elseif($indexer[$j][$h]->getType()=="sarissa" && $dataarray[$indexer[$j][$h]->getName()]!='') {
							if($elemis2)
							{
								$queryelemname=str_ireplace("search2_","",$indexer[$j][$h]->getName());
							}
							else
							{
								$queryelemname=str_ireplace("search_","",$indexer[$j][$h]->getName());
							}
							if(!$firstsearchquery)
							{
								$searchquery.=' AND';
							}
							if($this_type==3)
							{
								$searchquery.=' (';
							}
							$searchquery.=' t1.'.$queryelemname.'='.encode_to_cp1251($dataarray[$indexer[$j][$h]->getName()]);
							if($this_type==3)
							{
								if($elemis2)
								{
									$searchquery.=' AND t1.'.$this_content.'!="{menu}")';
								}
								else
								{
									$searchquery.=' AND t1.'.$this_content.'="{menu}")';
								}
							}
							$firstsearchquery=false;
						}
					}
				}
				else
				{
					$_SESSION['indexers'][$kind][$obj->getName()][$indexer[$j][$h]->getName()]=encode_to_cp1251($dataarray[$indexer[$j][$h]->getName()]);
					if($indexerobjs[$j][$h+1]->getType()=='calendar')
					{
						if($dataarray[$indexer[$j][$h]->getName()]!='' && $dataarray[$indexer[$j][$h+1]->getName()]!='')
						{
							if($elemis2)
							{
								$queryelemname=str_ireplace("search2_","",$indexer[$j][$h+1]->getName());
							}
							else
							{
								$queryelemname=str_ireplace("search_","",$indexer[$j][$h+1]->getName());
							}
							if(!$firstsearchquery)
							{
								$searchquery.=' AND';
							}
							if($this_type==3)
							{
								$searchquery.=' (';
							}
							$searchquery.=' t1.'.$queryelemname;
							if($dataarray[$indexer[$j][$h]->getName()]=='1') {$searchquery.='=';}
							elseif($dataarray[$indexer[$j][$h]->getName()]=='2') {$searchquery.='!=';}
							elseif($dataarray[$indexer[$j][$h]->getName()]=='3') {$searchquery.='>';}
							elseif($dataarray[$indexer[$j][$h]->getName()]=='4') {$searchquery.='<';}
							$searchquery.='"'.$dataarray[$indexer[$j][$h+1]->getName()].'"';
							if($this_type==3)
							{
								if($elemis2)
								{
									$searchquery.=' AND t1.'.$this_content.'!="{menu}")';
								}
								else
								{
									$searchquery.=' AND t1.'.$this_content.'="{menu}")';
								}
							}
							$firstsearchquery=false;
						}
						$h++;
						$_SESSION['indexers'][$kind][$obj->getName()][$indexer[$j][$h]->getName()]=encode_to_cp1251($dataarray[$indexer[$j][$h]->getName()]);
					}
					elseif($indexerobjs[$j][$h+1]->getType()=='timestamp')
					{
						if($dataarray[$indexer[$j][$h]->getName()]!='' && $dataarray[$indexer[$j][$h+1]->getName()]!='')
						{
							if($elemis2)
							{
								$queryelemname=str_ireplace("search2_","",$indexer[$j][$h+1]->getName());
							}
							else
							{
								$queryelemname=str_ireplace("search_","",$indexer[$j][$h+1]->getName());
							}
							if(!$firstsearchquery)
							{
								$searchquery.=' AND';
							}
							if($this_type==3)
							{
								$searchquery.=' (';
							}
							$searchquery.=' (t1.'.$queryelemname;
							$thistime1=strtotime($dataarray[$indexer[$j][$h+1]->getName()]);
							$thistime2=$thistime1+(60*60*24);
							if($dataarray[$indexer[$j][$h]->getName()]=='1') {$searchquery.='>='.$thistime1.' AND t1.'.$queryelemname.'<'.$thistime2;}
							elseif($dataarray[$indexer[$j][$h]->getName()]=='2') {$searchquery.='<'.$thistime1.' OR t1.'.$queryelemname.'>='.$thistime2;}
							elseif($dataarray[$indexer[$j][$h]->getName()]=='3') {$searchquery.='>='.$thistime2;}
							elseif($dataarray[$indexer[$j][$h]->getName()]=='4') {$searchquery.='<'.$thistime1;}
							$searchquery.=')';
							if($this_type==3)
							{
								if($elemis2)
								{
									$searchquery.=' AND t1.'.$this_content.'!="{menu}")';
								}
								else
								{
									$searchquery.=' AND t1.'.$this_content.'="{menu}")';
								}
							}
							$firstsearchquery=false;
						}
						$h++;
						$_SESSION['indexers'][$kind][$obj->getName()][$indexer[$j][$h]->getName()]=encode_to_cp1251($dataarray[$indexer[$j][$h]->getName()]);
					}
					elseif($indexerobjs[$j][$h+1]->getType()=='number')
					{
						if($dataarray[$indexer[$j][$h+1]->getName()]!=0 || ($dataarray[$indexer[$j][$h+1]->getName()]==0 && $dataarray[$indexer[$j][$h]->getName()]!=''))
						{
							if($dataarray[$indexer[$j][$h]->getName()]=='') {$dataarray[$indexer[$j][$h]->getName()]='1';}
							if($elemis2)
							{
								$queryelemname=str_ireplace("search2_","",$indexer[$j][$h+1]->getName());
							}
							else
							{
								$queryelemname=str_ireplace("search_","",$indexer[$j][$h+1]->getName());
							}
							if(!$firstsearchquery)
							{
								$searchquery.=' AND';
							}
							if($this_type==3)
							{
								$searchquery.=' (';
							}
							if($indexerobjs[$j][$h+1]->getVirtual())
							{
								// о боже, это еще и виртуальное числовое поле!
								if($dataarray[$indexer[$j][$h]->getName()]=='1') {$searchquery.=' t1.'.$obj->getVirtualField().' LIKE "%['.$queryelemname.']['.$dataarray[$indexer[$j][$h+1]->getName()].']%"';}
								elseif($dataarray[$indexer[$j][$h]->getName()]=='2') {$searchquery.=' (t1.'.$obj->getVirtualField().' NOT LIKE "['.$queryelemname.']['.$dataarray[$indexer[$j][$h+1]->getName()].']" AND t1.'.$obj->getVirtualField().' LIKE "%['.$queryelemname.']%"';}
								// в теории тут могли бы быть еще операции сравнения "больше-меньше"
							}
							else
							{
								$searchquery.=' t1.'.$queryelemname;
								if($dataarray[$indexer[$j][$h]->getName()]=='1') {$searchquery.='=';}
								elseif($dataarray[$indexer[$j][$h]->getName()]=='2') {$searchquery.='!=';}
								elseif($dataarray[$indexer[$j][$h]->getName()]=='3') {$searchquery.='>';}
								elseif($dataarray[$indexer[$j][$h]->getName()]=='4') {$searchquery.='<';}
								$searchquery.=$dataarray[$indexer[$j][$h+1]->getName()];
							}
							if($this_type==3)
							{
								if($elemis2)
								{
									$searchquery.=' AND t1.'.$this_content.'!="{menu}")';
								}
								else
								{
									$searchquery.=' AND t1.'.$this_content.'="{menu}")';
								}
							}
							$firstsearchquery=false;
						}
						$_SESSION['indexers'][$kind][$obj->getName()][$indexer[$j][$h]->getName()]=encode_to_cp1251($dataarray[$indexer[$j][$h]->getName()]);
						$h++;
						$_SESSION['indexers'][$kind][$obj->getName()][$indexer[$j][$h]->getName()]=encode_to_cp1251($dataarray[$indexer[$j][$h]->getName()]);
					}
				}
			}
		}
		if($searchquery!=' WHERE' && $searchquery!='') {
			$_SESSION['indexers'][$kind][$obj->getName()]['searchquery']=$searchquery;
			if($dynrequest==1) {
				redirect_construct();
				dynamic_err(array(),$redirect_path);
			}
		}
		else {
			$_SESSION['indexers'][$kind][$obj->getName()]['searchquery']='';
			dynamic_err_one('error','Фильтры не определены!');
		}
	}
	elseif($action=="dynamicindexclear")
	{
		// вычищаем все значения фильтров из $_SESSION['indexers'][$kind]['objname']
		unset($_SESSION['indexers'][$kind][$obj->getName()]);
		redirect_construct();
		redirect($redirect_path);
	}
	if($filter_id!='' && $object==$obj->getName() && $action!="filterdelete") {
		$_SESSION['indexers'][$kind][$obj->getName()]=$_SESSION['indexers'][$kind]["savedfilters"][$obj->getName()][$filter_id];
	}
	for($j=0;$j<count($indexer);$j++)
	{
		for($h=0;$h<count($indexer[$j]);$h++)
		{
			$indexer[$j][$h]->setVal($_SESSION['indexers'][$kind][$obj->getName()]);
		}
	}
	//print_r($_SESSION['indexers'][$kind]);

	$content.='<center><div class="cb_editor">
<div class="indexer"';
	/*if($obj->getSize()!='')
	{
		$content.=' style="width: '.$obj->getSize().';"';
	}*/
	$content.='>';
	$filters_off=false;
	if($_SESSION['indexers'][$kind][$obj->getName()]['searchquery']=='' && $action!="dynamicindexclear" && $action!="dynamicindex" && $action!="filterdelete") {
		$filters_off=true;
	}

	$content.='
<div id="filters_'.$obj->getName().'"'.($filters_off?' style="display: none;"':'').'>
<form action="'.$curdir.$kind.'/" method="post" enctype="multipart/form-data" id="filter_form">
<input type="hidden" name="kind" value="'.$kind.'">
<input type="hidden" name="action" value="dynamicindex">
<input type="hidden" name="object" value="'.$obj->getName().'">
<input type="hidden" name="sorting" value="'.$sorting.'">
<table class="menutable searchtable">
<tr>
';
	$j=0;
	$h=0;
	$fl=false;
	if(count($indexer)==2) {
		$width='50%';
	}
	elseif(count($indexer)==1) {
		$width='100%';
	}
	else {
		$width='33%';
	}
	for($i=0;$i<count($indexer);$i++) {
		$content.='<td style="width:'.$width.'">';
		for($h=0;$h<count($indexer[$i]);$h++)
		{
			if($h==0)
			{
				if($indexer[$i][$h]->getType()=="multiselect")
				{
					$content.='<b>'.$indexer[$i][$h]->getSname().'</b>:<br>';
					$content.=$indexer[$i][$h]->draw(1,"write");
					if(isset($indexer[$i][$h+1]))
					{
						if($indexer[$i][$h+1]->getType()=="multiselect")
						{
							$content.='<hr>';
						}
					}
				}
				else
				{
					$content.='<b>'.$indexer[$i][$h]->getSname().'</b>:<br>'.$indexer[$i][$h]->draw(2,"write");
				}
			}
			else
			{
				$content.=$indexer[$i][$h]->draw(1,"write");
				if($indexer[$i][$h]->getType()=="checkbox")
				{
					$content.='<label for="'.$indexer[$i][$h]->getName().'"> '.$indexer[$i][$h]->getSname().'</label>';
				}
			}
			if($indexer[$i][0]->getName()=="search_alltextfields")
			{
				if(($h+1)<count($indexer[$i]))
				{
					$content.='<br>';
				}
			}
			else
			{
				if(($h+1)<count($indexer[$i]))
				{
					$content.=' ';
				}
			}
		}
		$content.='</td>';
		$j++;
		if($j==3)
		{
			$j=0;
			$fl=true;
			$content.='</tr><tr>';
		}
	}
	if($fl && $j!=0)
	{
		while($j<3)
		{
			$content.='<td></td>';
			$j++;
		}
	}
	$content.='</tr></td></tr></table>
<table class="controls"><tr><td><button class="nonimportant" onClick="window.location=\''.$curdir.$kind.'/object='.$obj->getName().'&action=dynamicindexclear&sorting='.$sorting.'\'">очистить фильтр</button></td><td>';
	if($_SESSION['indexers'][$kind][$obj->getName()]["searchquery"]!='')
	{
		$content.='<div class="filters_on"';
		/*if($filter_id=='' || count($_SESSION['indexers'][$kind]["savedfilters"][$obj->getName()])>0 || $action=="filterdelete") {
			$content.=' 0px 5px 0px 5px';
		}
		else {
			$content.=' 5px';
		}*/
		$content.='>Внимание! Используются фильтры.</div>';
		/*if(count($_SESSION['indexers'][$kind]["savedfilters"][$obj->getName()])>0 || $filter_id=='' || $action=="filterdelete") {
			$content.='<div style="border: 1px dotted red; padding: 0px 5px 0px 5px; text-align: center; margin-top: 3px;" id="mysearchnames_'.$obj->getName().'">';
		}
		if($filter_id=='' || $action=="filterdelete") {
			$content.='
<script language="JavaScript">
function promptCallback(name) {
	if(name!=null && name!="") {
		document.location="'.$curdir.$kind.'/object='.$object.'&action=filtersave&filtername="+name;
	}
}
</script>
			<a onClick="IEprompt(\'Введите название для данной выборки фильтров!\', \'Выборка фильтров №'.(count($_SESSION['indexers'][$kind]["savedfilters"][$obj->getName()])+1).'\')" style="cursor: pointer;"><b>сохранить</b></a>';
		}
		if(count($_SESSION['indexers'][$kind]["savedfilters"][$obj->getName()])>0) {
			if($filter_id=='' || $action=="filterdelete") {
				$content.=' | ';
			}
			$content.='<a onClick="document.getElementById(\'savedfilters_'.$obj->getName().'\').style.display=\'block\'; document.getElementById(\'mysearchnames_'.$obj->getName().'\').style.display=\'none\';" style="cursor: pointer;"><b>мои выборки</b></a></div><div style="border: 1px dotted red; padding: 0px 5px 0px 5px; text-align: center; display: none; margin-top: 3px;" id="savedfilters_'.$obj->getName().'"><a style="cursor: pointer;" onClick="document.getElementById(\'savedfilters_'.$obj->getName().'\').style.display=\'none\';  document.getElementById(\'mysearchnames_'.$obj->getName().'\').style.display=\'block\'" class="cross" style="float: right;">X</a>';
			$f_array=array_keys($_SESSION['indexers'][$kind]["savedfilters"][$obj->getName()]);
			for($i=0;$i<count($_SESSION['indexers'][$kind]["savedfilters"][$obj->getName()]);$i++) {
				$f_id=$f_array[$i];
				$content.='<a href="'.$curdir.$kind.'/object='.$object.'&filter_id='.$f_id.'"><b>'.$_SESSION['indexers'][$kind]["savedfilters"][$obj->getName()][$f_id]["searchname"].'</b></a> [<a href="'.$curdir.$kind.'/object='.$object.'&action=filterdelete&filter_id='.$f_id.'">удалить</a>]<br>';
			}
			$content.='</div>';
		}
		if(count($_SESSION['indexers'][$kind]["savedfilters"][$obj->getName()])==0 && ($filter_id=='' || $action=="filterdelete")) {
			$content.='</div>';
		}*/
	}
	else
	{
		$content.='<div class="filters_off"';
		/*if(count($_SESSION['indexers'][$kind]["savedfilters"][$obj->getName()])>0) {
			$content.=' 0px 5px 0px 5px';
		}
		else {
			$content.=' 5px';
		}*/
		$content.='>Фильтры не используются.</div>';
		/*if(count($_SESSION['indexers'][$kind]["savedfilters"][$obj->getName()])>0) {
			$content.='<img src="'.$GLOBALS["server_absolute_path"].$GLOBALS["direct"].'/empty.gif" width=1 height=3><div style="border: 1px dotted green; padding: 0px 5px 0px 5px; text-align: center;" id="mysearchnames_'.$obj->getName().'"><a onClick="document.getElementById(\'savedfilters_'.$obj->getName().'\').style.display=\'block\'; document.getElementById(\'mysearchnames_'.$obj->getName().'\').style.display=\'none\';" style="cursor: pointer;"><b>мои выборки</b></a></div>
<div style="border: 1px dotted green; padding: 0px 5px 0px 5px; text-align: center; display: none;" id="savedfilters_'.$obj->getName().'"><a style="cursor: pointer;" onClick="document.getElementById(\'savedfilters_'.$obj->getName().'\').style.display=\'none\'; document.getElementById(\'mysearchnames_'.$obj->getName().'\').style.display=\'block\'" class="cross" style="float: right;">X</a>';
			$f_array=array_keys($_SESSION['indexers'][$kind]["savedfilters"][$obj->getName()]);
			for($i=0;$i<count($_SESSION['indexers'][$kind]["savedfilters"][$obj->getName()]);$i++) {
				$f_id=$f_array[$i];
				$content.='<a href="'.$curdir.$kind.'/object='.$obj->getName().'&filter_id='.$f_id.'"><b>'.$_SESSION['indexers'][$kind]["savedfilters"][$obj->getName()][$f_id]["searchname"].'</b></a> [<a href="'.$curdir.$kind.'/object='.$object.'&action=filterdelete&filter_id='.$f_id.'">удалить</a>]<br>';
			}
			$content.='</div>';
		}*/
	}
	$content.='</td><td><button class="main">отфильтровать</button></td></tr></table></form><br></div></div>
<h3 id="showfilters_'.$obj->getName().'"'.($filters_off?'':' style="display: none;"').' class="ctrlink2"><a onClick="$(\'#filters_'.$obj->getName().'\').toggle(); $(\'#hidefilters_'.$obj->getName().'\').toggle(); $(\'#showfilters_'.$obj->getName().'\').toggle();">показать фильтры</a></h3>
<h3 id="hidefilters_'.$obj->getName().'"'.($filters_off?' style="display: none;"':'').' class="ctrlink2"><a onClick="$(\'#filters_'.$obj->getName().'\').toggle(); $(\'#showfilters_'.$obj->getName().'\').toggle(); $(\'#hidefilters_'.$obj->getName().'\').toggle();">скрыть фильтры</a></h3>';
}
else {
	$content.='<center><div class="cb_editor">';
}
?>