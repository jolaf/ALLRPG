<?php
function dynamiccreate($obj) {
	global
		$kind,
		$object,
		$id,
		$page,
		$act,
		$action,
		$actiontype,
		$valuestype,
		$sorting,
		$trouble,
		$trouble2,
		$formfile,
		$stayhere,
		$server_absolute_path,
		$direct,
		$admin,
		$curdir,
		$onloadfunc,
		$error,
		$redirect_path,
		$dynrequest;

	$grads=$obj->getElemPerPage();
	$this_type=$obj->getType();
	$this_right=$obj->getRight();
	$this_sort=$obj->getSort();
	$this_search=$obj->getSearch();
	$this_table=$obj->getTable();
	$filds=$obj->getElems();

	if($trouble || $trouble2[-1])
	{
		if($actiontype=="add")
		{
			$act="add";
		}
	}

	if(!isset($stayhere)) {
		$stayhere=true;
		if($act=="view" && !$trouble && $actiontype=="change") {
			$stayhere=false;
		}
		elseif(!$trouble && $actiontype=="add") {
			$stayhere=false;
		}
		elseif(!$trouble && $actiontype=="delete") {
			$stayhere=false;
		}
	}

	if($this_search[0]!='' && $this_right->getView()) {
		require_once("filters.php");
	}

	if(((($this_type==2 || $this_type==3) && ($act=='' || ($act=="view" && !$stayhere))) || $this_type==1) && $this_right->getView())
	{
		$order='';
		$homos='t1.';
		$homos2='';
		$homos3='';
		$homcount=2;
		$sorting2=0;

		if($this_right->getViewRestrict()!='')
		{
			$homos3=str_ireplace(' and ',' and '.$homos,$this_right->getViewRestrict());
			$homos3=str_ireplace(' or ',' or '.$homos,$homos3);
			$homos3=" WHERE ".$homos.$homos3;
		}
		
		if (intval($sorting) == 0)
		{
      $sorting = $obj -> getDefaultSort();
		}

		if($sorting!='')
		{
			$sorting2=round($sorting/2)-1;
			if($sorting%2==1)
			{
				$sorting3="ASC";
			}
			else
			{
				$sorting3="DESC";
			}
		}

		for($i=0;$i<count($this_sort[0]);$i++)
		{
			if($this_sort[0][$i][4][0]==3)
			{
				$homos2.=' LEFT JOIN '.$this_sort[0][$i][4][1].' t'.$homcount.' ON t1.'.$this_sort[0][$i][0].'=t'.$homcount.'.'.$this_sort[0][$i][4][2];
				if($i==$sorting2 && $sorting!=0)
				{
					$order='t'.$homcount.'.'.$this_sort[0][$i][4][3].' '.$sorting3.', '.$order;
				}
				else
				{
					$order.='t'.$homcount.'.'.$this_sort[0][$i][4][3].' '.$this_sort[0][$i][1].', ';
				}
				$homcount++;
			}
			elseif($this_sort[0][$i][4][0]==2)
			{
				$ordfield='FIELD('.$homos.$this_sort[0][$i][0];
				for($j=0;$j<count($this_sort[0][$i][4][1]);$j++) {
					$ordfield.=", '".$this_sort[0][$i][4][1][$j][0]."'";
				}
				$ordfield.=')';
				if($i==$sorting2 && $sorting!=0)
				{
					$order=$ordfield.' '.$sorting3.', '.$order;
				}
				else
				{
					$order.=$ordfield.' '.$this_sort[0][$i][1].', ';
				}
			}
			else
			{
				if($i==$sorting2 && $sorting!=0)
				{
					$order=$homos.$this_sort[0][$i][0].' '.$sorting3.', '.$order;
				}
				else
				{
					$order.=$homos.$this_sort[0][$i][0].' '.$this_sort[0][$i][1].', ';
				}
			}
		}
		$order=substr($order,0,strlen($order)-2);
		if($this_type!=3)
		{
			if($this_right->getViewRestrict()!='' && $_SESSION['indexers'][$kind][$obj->getName()]['searchquery']!='')
			{
				$query="SELECT t1.* FROM ".$this_table." t1".$homos2.$homos3.' AND'.$_SESSION['indexers'][$kind][$obj->getName()]['searchquery']." ORDER by ".$order." LIMIT ".($page*$grads).", ".$grads;
				$result=mysql_query("SELECT COUNT(t1.id) FROM ".$this_table." t1".$homos2.$homos3.' AND'.$_SESSION['indexers'][$kind][$obj->getName()]['searchquery']." ORDER by ".$order);
			}
			else
			{
				$query="SELECT t1.* FROM ".$this_table." t1".$homos2.$homos3.$_SESSION['indexers'][$kind][$obj->getName()]['searchquery']." ORDER by ".$order." LIMIT ".($page*$grads).", ".$grads;
				$result=mysql_query("SELECT COUNT(t1.id) FROM ".$this_table." t1".$homos2.$homos3.$_SESSION['indexers'][$kind][$obj->getName()]['searchquery']." ORDER by ".$order);
			}
			$a = mysql_fetch_array($result);
			$pagetotal=$a[0];
		}
		else
		{
			$type3_foundids=' ';
			$type3_foundparents=' ';
			if($this_right->getViewRestrict()!='' && $_SESSION['indexers'][$kind][$obj->getName()]['searchquery']!='')
			{
				$query="SELECT t1.* FROM ".$this_table." t1".$homos2.$homos3.' AND'.$_SESSION['indexers'][$kind][$obj->getName()]['searchquery']." ORDER by ".$order;
			}
			else
			{
				$query="SELECT t1.* FROM ".$this_table." t1".$homos2.$homos3.$_SESSION['indexers'][$kind][$obj->getName()]['searchquery']." ORDER by ".$order;
			}
			$result=mysql_query($query);
			function type3_foundparents($this_table, $parent, $id) {
				$result=mysql_query("SELECT * FROM ".$this_table." where id=".$id);
				$a = mysql_fetch_array($result);
				if($a[$parent]>0)
				{
					$type3_foundparents=type3_foundparents($this_table, $parent, $a[$parent]);
					$type3_foundparents.=$a[$parent].', ';
				}
				return $type3_foundparents;
			}
			function type3_foundchilds($this_table, $parent, $id) {
				$result=mysql_query("SELECT * FROM ".$this_table." where ".$parent."=".$id);
				while($a = mysql_fetch_array($result)) {
					$type3_foundchilds.=type3_foundchilds($this_table, $parent, $a["id"]);
					$type3_foundchilds.=$a["id"].', ';
				}
				return $type3_foundchilds;
			}
			while($a = mysql_fetch_array($result))
			{
				$type3_foundids.=$a["id"].', ';
				if($_SESSION['indexers'][$kind][$obj->getName()]['searchquery']!='' && $a[$obj->getParent()]>0) {
					$type3_foundparents.=type3_foundparents($this_table, $obj->getParent(), $a["id"]);
				}
				elseif($_SESSION['indexers'][$kind][$obj->getName()]['searchquery']!='' && $a[$obj->getParent()]==0 && $_SESSION['indexers'][$kind][$obj->getName()]['search_showchilds']=='on') {
					$type3_foundids.=type3_foundchilds($this_table, $obj->getParent(), $a["id"]);
				}
			}
			$countquery="SELECT COUNT(id) FROM ".$this_table." where ";
			if($type3_foundids!=' ')
			{
				$countquery.="id IN (".substr($type3_foundids,0,strlen($type3_foundids)-2).")";
				if($type3_foundparents!=' ')
				{
					$countquery.=" OR id IN (".substr($type3_foundparents,0,strlen($type3_foundparents)-2).")";
				}
			}
			$result=mysql_query($countquery);
			$a = mysql_fetch_array($result);
			$pagetotal=$a[0];
		}
		//echo("<!--".$query."-->");
	}

	$fieldnum=1;

	if($this_type==1 && $this_right->getView())
	{
		$type1columns=1;
		if($this_right->getDelete())
		{
			$type1columns+=1;
		}

		if(!($this_search[0]!='' && $this_right->getView())) {
			$content.='<center><div class="cb_editor">';
		}

		$content.='<!-- start '.$obj->getName().' object -->
<table class="maininfotable">
<tr class="menu">
';
		for($i=0;$i<($type1columns-1);$i++)
		{
			$content.='<td class="small">
&nbsp;
</td>
';
		}
		$content.='<td class="small">№</td>';

		$help='';
		$h=1;
		foreach($filds as $f=>$v)
		{
			if($v->getRead()<=$this_right->getRights())
			{
				$letdo=true;
				if($v->getType()=="timestamp")
				{
					if($v->getShow()==false) {
						$letdo=false;
					}
				}
				if($v->getType()=="hidden")
				{
					$letdo=false;
				}
				if($letdo)
				{
					$fs=false;
					$content.='<td>
';
					for($u=0;$u<count($this_sort['0']);$u++)
					{
						if($v->getName()==$this_sort['0'][$u][0])
						{
							if($sorting==($u*2+1))
							{
								$content.='<a href="'.$curdir.$kind.'/'.$obj->getName().'/page='.$page.'&sorting='.($u*2+2).'" title="[отсортировать : '.strtolower($v->getSname()).' : по убыванию]" class="arrow_up">'.$v->getSname().'</a>';
							}
							elseif($sorting==($u*2+2))
							{
								$content.='<a href="'.$curdir.$kind.'/'.$obj->getName().'/page='.$page.'&sorting='.($u*2+1).'" title="[отсортировать : '.strtolower($v->getSname()).' : по возрастанию]" class="arrow_down">'.$v->getSname().'</a>';
							}
							else
							{
								$content.='<a href="'.$curdir.$kind.'/'.$obj->getName().'/page='.$page.'&sorting='.($u*2+2).'" title="[отсортировать : '.strtolower($v->getSname()).' : по убыванию]">'.$v->getSname().'</a>';
							}
							$fs=true;
						}
					}
					if(!$fs)
					{
						$content.=$v->getSname();
					}
					if($v->getHelp()!='') {
						$content.='<span class="sup">';
						$help.='<span class="sup">';
						$content.=' '.$h;
						$help.=$h;
						$help.='</span> – ';
						$help.=$v->getHelp().'<br />';
						$h++;
						$content.='</span>';
					}
					$content.='
</td>
';
					$type1columns+=1;
				}
			}
		}
		if($havetobehelp)
		{
			//$help='<span class="sup">*</span> – поле необходимо заполнить.<br />'.$help;
		}
		$content.='</tr>
';
		if($this_right->getAdd())
		{
			$content.='
<form action="'.$curdir.$kind.'/" method="post" enctype="multipart/form-data" id="form_'.$obj->getName().'_add">
<input type="hidden" name="kind" value="'.$kind.'" />
<input type="hidden" name="object" value="'.$obj->getName().'" />
<input type="hidden" name="action" value="dynamicaction" />
<input type="hidden" name="page" value="'.$page.'" />
<input type="hidden" name="sorting" value="'.$sorting.'" />
<input type="hidden" name="actiontype" value="add" />
<input type="hidden" name="valuestype" value="'.$valuestype.'" />';
			$content.='<tr id="line_-1"';
			if($trouble2[-1])
			{
				$content.=' class="type1red"';
			}
			$content.='>';

			if($this_right->getDelete())
			{
				$content.='
<td class="small">
<img src="'.$server_absolute_path.$direct.'/empty.gif" width="20" />
</td>';
			}
			$content.='
<td class="small">
<img src="'.$server_absolute_path.$direct.'/empty.gif" width="20" />
</td>
';
			foreach($filds as $f=>$v)
			{
				if($v->getType()!='')
				{
					if($v->getWrite()<=$this_right->getRights())
					{
						$can="write";
					}
					elseif($v->getRead()<=$this_right->getRights())
					{
						$can="read";
					}
					else
					{
						$can="";
					}

					if($can!='' && !($v->getType()=="timestamp" && !$v->getShow()) && $v->getType()!="hidden")
					{
						$content.='<td>
';
					}
					if($trouble2[-1])
					{
						$v->setVal('',true);
					}
					else
					{
						$v->setVal('');
					}
					if(!(($v->getType()=="select" || $v->getType()=="multiselect") && count($v->getValues())==0 && $v->getMustBe()==false))
					{
						$content.=$v->draw($this_type,$can);
					}
					if($can!='' && !($v->getType()=="timestamp" && !$v->getShow()) && $v->getType()!="hidden")
					{
						$content.='
</td>
';
					}
				}
			}

			$content.='</tr>
<tr class="menu">
<td colspan='.$type1columns.' style="text-align: right">
<button class="main">Добавить '.$obj->getWord1().'</button>
</td>
</tr>
</form>
';
		}

		if($this_right->getView())
		{
			$linenum=1;
			if($this_right->getChange())
			{
				$content.='<tr>
<td colspan='.$type1columns.' style="text-align: center"><h3 style="margin: 10px">ИЗМЕНИТЬ</h3></td>
</tr>
<form action="'.$curdir.$kind.'/" method="post" enctype="multipart/form-data" id="form_'.$obj->getName().'_save">
<input type="hidden" name="kind" value="'.$kind.'" />
<input type="hidden" name="object" value="'.$obj->getName().'" />
<input type="hidden" name="action" value="dynamicaction" />
<input type="hidden" name="actiontype" value="change" />
<input type="hidden" name="act" value="view" />
<input type="hidden" name="page" value="'.$page.'" />
<input type="hidden" name="sorting" value="'.$sorting.'" />
<input type="hidden" name="valuestype" value="'.$valuestype.'" />';
			}

			$result=mysql_query($query);
			while($a = mysql_fetch_array($result))
			{
				if($obj->getVirtualField()!='')
				{
					$a=array_merge($a,unmakevirtual($a[$obj->getVirtualField()]));
				}

				$content.='<tr id="line_'.$linenum.'" class="';
				if($trouble2[$a["id"]]) {
					$content.='type1red';
				}
				else {
					if($linenum%2==0) {
						$content.='string2';
					}
					else {
						$content.='string1';
					}
				}
				$content.='">';
				if($this_right->getChange())
				{
					$content.='
<input type="hidden" name="id['.$linenum.']" value="'.$a["id"].'" />
';
				}

				if($this_right->getDelete())
				{
					$content.='<td class="type1delete">
<a class="trash custom-appearance careful" title="удалить '.$obj->getWord1().'" href="'.$curdir.$kind.'/'.$obj->getName().'/'.$a["id"].'/action=dynamicaction&actiontype=delete&act=view&page='.$page.'&sorting='.$sorting.'">
  <span class="lid"></span>
  <span class="can"></span>
</a>
</td>';
				}

				$content.='<td class="type1num">'.$linenum.'</td>';

				foreach($filds as $f=>$v)
				{
					if($v->getType()!='')
					{
						if($v->getWrite()<=$this_right->getRights() && $this_right->getChange())
						{
							$can="write";
						}
						elseif($v->getRead()<=$this_right->getRights())
						{
							$can="read";
						}
						else
						{
							$can="";
						}

						if($can!='' && !($v->getType()=="timestamp" && !$v->getShow()) && $v->getType()!="hidden")
						{
							$content.='<td>
';
						}
						if($trouble2[$a["id"]])
						{
							if($can=="read") {
								$v->setVal($a);
							}
							else {
								$v->setVal('',true,$linenum);
							}
						}
						else
						{
							$v->setVal($a);
						}
						$oldid=$id;
						$oldobject=$object;
						$object=$obj->getName();
						if(!(($v->getType()=="select" || $v->getType()=="multiselect") && count($v->getValues())==0 && $v->getMustBe()==false))
						{
							$id=$a["id"];
							$content.=$v->draw($this_type,$can,$linenum);
						}
						$id=$oldid;
						$object=$oldobject;
						if($can!='' && !($v->getType()=="timestamp" && !$v->getShow()) && $v->getType()!="hidden")
						{
							$content.='
</td>
';
						}
					}
				}

				$content.='
</tr>';
				$linenum++;
			}
			$content.='<tr class="menu">
<td colspan='.$type1columns.' style="text-align: right">';
			if($this_right->getChange())
			{
				$content.='
<input type="hidden" name="linecount" value="'.$linenum.'" />
<button class="main">Сохранить изменения</button></form>';
			}
			else
			{
				$content.='&nbsp;';
			}
			$content.='</td>
</tr>';
		}
		if($help!='')
		{
			$content.='
<tr>
<td colspan='.$type1columns.' style="text-align: justify" class="sm2"><a name="help"></a>'.$help.'</td>
</tr>
<tr class="menu">
<td colspan='.$type1columns.'>&nbsp;</td>
</tr>';
		}
		$content.='
</table>';
	}
	else
	{
		if($id!='' && (($action!="dynamicaction" && $actiontype!="delete") || $stayhere)) {
			$result=mysql_query("SELECT * FROM ".$this_table." WHERE id=".$id);
			$a = mysql_fetch_array($result);
			if($a["id"]=='') {
				$this_right->setView(false);
				$this_right->setChange(false);
				$this_right->setDelete(false);
			}

			if($this_right->getViewRestrict()!='') {
				$result=mysql_query("SELECT * FROM ".$this_table." WHERE id=".$id." AND ".$this_right->getViewRestrict());
				$a = mysql_fetch_array($result);
				if($a["id"]=='') {
					$this_right->setView(false);
				}
			}
			if($this_right->getChangeRestrict()!='') {
				$result=mysql_query("SELECT * FROM ".$this_table." WHERE id=".$id." AND ".$this_right->getChangeRestrict());
				$a = mysql_fetch_array($result);
				if($a["id"]=='') {
					$this_right->setChange(false);
				}
			}
			if($this_right->getDeleteRestrict()!='') {
				$result=mysql_query("SELECT * FROM ".$this_table." WHERE id=".$id." AND ".$this_right->getDeleteRestrict());
				$a = mysql_fetch_array($result);
				if($a["id"]=='') {
					$this_right->setDelete(false);
				}
			}
		}

		if($act=="add" && $this_right->getAdd())
		{
			$content.='
<!-- start '.$obj->getName().' object -->
';
			if(!($this_search[0]!='' && $this_right->getView())) {
				$content.='<center><div class="cb_editor">';
			}

			$content.='
<form action="'.$curdir.$kind.'/" method="post" enctype="multipart/form-data" id="form_'.$obj->getName().'">
<input type="hidden" name="kind" value="'.$kind.'" />
<input type="hidden" name="object" value="'.$obj->getName().'" />
<input type="hidden" name="action" value="dynamicaction" />
<input type="hidden" name="actiontype" value="add" />
<input type="hidden" name="valuestype" value="'.$valuestype.'" />

';
		}
		elseif($act=="view" && $stayhere && $this_right->getView())
		{
			if($this_right->getViewRestrict()!='')
			{
				$result=mysql_query("SELECT * FROM ".$this_table." WHERE id=".$id." AND (".$this_right->getViewRestrict().")");
			}
			else
			{
				$result=mysql_query("SELECT * FROM ".$this_table." WHERE id=".$id);
			}
			$a = mysql_fetch_array($result);

			if($obj->getVirtualField()!='')
			{
				$a=array_merge($a,unmakevirtual($a[$obj->getVirtualField()]));
			}

			$content.='
<!-- start '.$obj->getName().' object -->
';
			if(!($this_search[0]!='' && $this_right->getView())) {
				$content.='<center><div class="cb_editor">';
			}

			if($this_right->getChange())
			{
				$content.='
<form action="'.$curdir.$kind.'/" method="post" enctype="multipart/form-data" id="form_'.$obj->getName().'">
<input type="hidden" name="kind" value="'.$kind.'" />
<input type="hidden" name="object" value="'.$obj->getName().'" />
<input type="hidden" name="action" value="dynamicaction" />
<input type="hidden" name="actiontype" value="change" />
<input type="hidden" name="act" value="view">
<input type="hidden" name="id" value="'.$id.'" />
<input type="hidden" name="valuestype" value="'.$valuestype.'" />';
			}

		}

		if(($act=="view" && $this_right->getView() && $stayhere) || ($act=="add" && $this_right->getAdd()))
		{
			if($valuestype=="1")
			{
				$h=$obj->getElems2();
			}
			else
			{
				$h=$filds;
			}
			foreach($h as $f=>$v)
			{
				if((($act=="add" && $this_right->getAdd()) || ($act=="view" && $stayhere && $this_right->getChange())) && $v->getWrite()<=$this_right->getRights())
				{
					$can="write";
				}
				elseif($v->getRead()<=$this_right->getRights())
				{
					$can="read";
				}
				else
				{
					$can="";
				}
				
				 $field_name = $v->getName();

				if($trouble && $can=="write")
				{
					$v->setVal('',true);
				}
				else
				{
					$v->setVal($a);
				}
				
				$is_empty = $v -> isEmpty();
				$empty_readonly = ($can=="read" && $is_empty);
				
				if($can && (!$empty_readonly || ($v->getType()=="h1")) && !($v -> isAnySelect() && count($v->getValues())==0 && $v->getMustBe()==false))
				{
					if($v->getType()!="hidden" && $v->getType()!="timestamp" && $v->getType()!="h1") {
						$content.='<div class="fieldname" id="name_'.$v->getName().'" tabindex="'.$fieldnum.'">'.$v->getSname().'</div>';
						$fieldnum++;
						if($v->getHelp()!='') {
							$content.='<div class="help" id="help_'.$v->getName().'">'.$v->getHelp().'</div>';
						}
						$content.='<div class="fieldvalue';
						if($can!='write') {
							$content.=' read';
						}
						$content.='" id="div_'.$v->getName().'">';
						$content.=$v->draw($this_type,$can);
						$content.='</div>';
					}
					else {
						$content.=$v->draw($this_type,$can);
					}

					if($v->getType()!="hidden" && $v->getType()!="timestamp" && $v->getType()!="h1")
					{
						if($can!='') {
							if(($v->getType()=="multiselect" && $v->getOne()==false) || ($v->getType()=="wysiwyg" && $can=="write")) {
								$content.='<div class="clear"></div><br />
';
							}
							else {
								$content.='<div class="clear"></div><br />
';
							}
						}
					}
				}
			}
		}

		if($act=="add")
		{
			if($this_right->getAdd()) {
				$content.='<center><button class="main">Добавить ';
				if($valuestype=="0") {
					$content.=$obj->getWord1();
				}
				else {
					$content.=$obj->getWord3();
				}
				$content.='</button></center>
</form>';
			}
		}
		elseif($act=="view" && $stayhere)
		{
			if($this_right->getChange())
			{
				$content.='<center><button class="main">Сохранить ';
				if($valuestype=="0") {
					$content.=$obj->getWord1();
				}
				else {
					$content.=$obj->getWord3();
				}
				$content.='</button>';
			}

			if($this_right->getDelete())
			{
				if($this_right->getChange()) {
					$content.=' ';
				}
				else {
					$content.='<center>';
				}
				$content.='<button class="careful" href="'.$curdir.$kind.'/'.$obj->getName().'/'.$a["id"].'/action=dynamicaction&actiontype=delete&valuestype='.$valuestype.'">Удалить ';
				if($valuestype==0)
				{
					$content.=$obj->getWord1();
				}
				elseif($valuestype==1)
				{
					$content.=$obj->getWord3();
				}
				$content.='</button>';
			}
            if(($this_right->getChange() || $this_right->getDelete()) && $this_right->getView()) {
				$content.='</center>
</form>';
			}
		}
		else
		{
			if($this_right->getView())
			{
				$valuestype=0;

				if($this_type==3)
				{
					$filds2=$obj->getElems2();
				}

				if(!($this_search[0]!='' && $this_right->getView())) {
					$content.='<center><div class="cb_editor">';
				}
				if($this_right->getAdd())
				{
					$content.='<a href="'.$curdir.$kind.'/'.$obj->getName().'/act=add" class="ctrlink">[+] добавить '.$obj->getWord1().'</a>';
					if($this_type==3)
					{
						$content.='<a href="'.$curdir.$kind.'/'.$obj->getName().'/act=add&valuestype=1" class="ctrlink">[+] добавить '.$obj->getWord3().'</a>';
					}
				}
				$content.='<div class="clear"></div><hr>';

				for($i=0;$i<count($this_sort[0]);$i++)
				{
					$this_type==3 && $order.=$this_sort[0][$i][0].' '.$this_sort[0][$i][1].', ';
					if($this_sort[0][$i][2])
					{
						$show[]=$this_sort[0][$i][0];
						$nam[]=$this_sort[0][$i][3];
						$spec[]=$this_sort[0][$i][4];
					}
				}
				$this_type==3 && $order=substr($order,0,strlen($order)-2);

				if($this_type==3)
				{
					$content.='
<ul class="ollist"><br>
';
					for($i=0;$i<count($this_sort[1]);$i++)
					{
						$order2.=$this_sort[1][$i][0].' '.$this_sort[1][$i][1].', ';
						if($this_sort[1][$i][2])
						{
							$show2[]=$this_sort[1][$i][0];
							$nam2[]=$this_sort[1][$i][3];
							$spec2[]=$this_sort[1][$i][4];
						}
					}
					$order2=substr($order2,0,strlen($order2)-2);

					if($type3_foundparents!=' ')
					{
						$qq="(id IN (".substr($type3_foundids,0,strlen($type3_foundids)-2).") OR id IN (".substr($type3_foundparents,0,strlen($type3_foundparents)-2)."))";
					}
					else
					{
						$qq="id IN (".substr($type3_foundids,0,strlen($type3_foundids)-2).")";
					}
					$ok=make5fieldtree(true,$this_table,$obj->getParent(),0," AND ".$obj->getContent()."='{menu}' AND ".$qq, $obj->getCode()." asc LIMIT ".($page*$grads).", ".$grads,1,"id",$obj->getName2(),1000000);

					$ok7=make5field($this_table." WHERE ".$obj->getParent()."=0 AND ".$obj->getContent()."!='{menu}' ORDER by ".$order2,"id",$obj->getName2());

					$result=mysql_query("SELECT COUNT(id) FROM ".$this_table." WHERE ".$obj->getParent()."=0 AND ".$qq);
					$a = mysql_fetch_array($result);
					$pagetotal=$a[0];

					$levelnow=0;

					if($ok[1][0]!='' || $ok7[1][0]!='')
					{
						$content.='<li><b>ВЕРХНИЙ УРОВЕНЬ</b>
				';
						for($j=1;$j<count($ok);$j++)
						{
							$result=mysql_query("SELECT * FROM ".$this_table." WHERE id=".$ok[$j][0]);
							$a = mysql_fetch_array($result);

							if($ok[$j][2]>$levelnow)
							{
								$content.='<ol class="ollist">
						';
								$levelnow=$ok[$j][2];
							}
							elseif($ok[$j][2]<$levelnow)
							{
								$close=$levelnow-$ok[$j][2];
								for($n=0;$n<$close;$n++)
								{
									$content.='
					</ol>
				';
								}
								$levelnow=$ok[$j][2];
							}
							if(stripos($type3_foundids,' '.$a["id"].',')!==false || stripos($type3_foundparents,' '.$a["id"].',')!==false)
							{
								$ss='';
								$content.='<li>';
								if(stripos($type3_foundids,' '.$a["id"].',')!==false)
								{
									$content.='<a href="'.$curdir.$kind.'/'.$obj->getName().'/'.$a["id"].'/act=view">';
								}

								for($i=0;$i<count($show);$i++)
								{
									if($i>0)
									{
										$ss.='. ';
									}

									foreach($filds as $f=>$v)
									{
										if($v->getName()==$show[$i])
										{
											if($nam[$i])
											{
												$ss.=$v->getSname().': ';
											}
											$ss.='<b>';
											if($a[$show[$i]]=='') {$ss.='<i>не определено</i>';}
											else {
												if($spec[$i]!='')
												{
													if($spec[$i][0]==1 || $spec[$i][0]==3)
													{
														$ss.=find5field($prefix.$spec[$i][1],$spec[$i][2],$a[$show[$i]],$spec[$i][3]);
													}
													elseif($spec[$i][0]==2)
													{
														for($d=0;$d<count($spec[$i][1]);$d++)
														{
															if($spec[$i][1][$d][0]==$a[$show[$i]])
															{
																$ss.=$spec[$i][1][$d][1];
																break;
															}
														}
													}
												}
												else
												{
													if($v->getType()=="checkbox")
													{
														if($a[$show[$i]]==1)
														{
															$ss.='<font color="green"><b>&#8730</b></font>';
														}
														else
														{
															$ss.='<font color="red"><b>X</b></font>';
														}
													}
													elseif($v->getType()=="calendar")
													{
														$ss.=date("d.m.Y",strtotime($a[$show[$i]]));
													}
													elseif($v->getType()=="timestamp")
													{
														$ss.=date("d.m.Y",$a[$show[$i]]).' в '.date("G:i",$a[$show[$i]]);
													}
													else
													{
														$ss.=decode2($a[$show[$i]]);
													}
												}
											}
											$ss.='</b>';
											break;
										}
									}
								}
								$content.=$ss.'.';
								if(stripos($type3_foundids,' '.$a["id"].',')!==false)
								{
									$content.='</a>';
								}
								$content.='
';
							}
							$ok2=make5field($this_table." WHERE ".$obj->getParent()."=".$ok[$j][0]." AND ".$obj->getContent()."!='{menu}' ORDER by ".$order2,"id",$obj->getName2());

							if($ok2[0][0]!='')
							{
								$content.='<ul class="ullist">';
								for($s=0;$s<count($ok2);$s++)
								{
									$result=mysql_query("SELECT * FROM ".$this_table." WHERE id=".$ok2[$s][0]);
									$a = mysql_fetch_array($result);

									if(stripos($type3_foundids,' '.$a["id"].',')!==false || stripos($type3_foundparents,' '.$a["id"].',')!==false)
									{
										$ss='';
										$content.='<li>';
										if(stripos($type3_foundids,' '.$a["id"].',')!==false)
										{
											$content.='<a href="'.$curdir.$kind.'/'.$obj->getName().'/'.$a["id"].'/act=view&valuestype=1">';
										}

										for($i=0;$i<count($show2);$i++)
										{
											if($i>0)
											{
												$ss.='; ';
											}

											foreach($filds2 as $f=>$v)
											{
												if($v->getName()==$show2[$i])
												{
													if($nam2[$i])
													{
														$ss.=$v->getSname().': ';
													}
													$ss.='<b>';
													if($a[$show2[$i]]=='')
													{
														if($show2[$i]==$obj->getName2() && ($a[$obj->getCode()]=='default' || $a[$obj->getCode()]=='1'))
														{
															$ss.='<i>по умолчанию</i>';
														}
														else
														{
															$ss.='<i>не определено</i>';
														}
													}
													else {
														if($spec2[$i]!='')
														{
															if($spec2[$i][0]==1 || $spec2[$i][0]==3)
															{
																$ss.=find5field($prefix.$spec2[$i][1],$spec2[$i][2],$a[$show2[$i]],$spec2[$i][3]);
															}
															elseif($spec2[$i][0]==2)
															{
																for($d=0;$d<count($spec2[$i][1]);$d++)
																{
																	if($spec2[$i][1][$d][0]==$a[$show2[$i]])
																	{
																		$ss.=$spec2[$i][1][$d][1];
																		break;
																	}
																}
															}
														}
														else
														{
															if($v->getType()=="checkbox")
															{
																if($a[$show2[$i]]==1)
																{
																	$ss.='<font color="green"><b>&#8730</b></font>';
																}
																else
																{
																	$ss.='<font color="red"><b>X</b></font>';
																}
															}
															elseif($v->getType()=="calendar")
															{
																$ss.=date("d.m.Y",strtotime($a[$show2[$i]]));
															}
															elseif($v->getType()=="timestamp")
															{
																$ss.=date("d.m.Y",$a[$show2[$i]]).' в '.date("G:i",$a[$show2[$i]]);
															}
															else
															{
																$ss.=decode2($a[$show2[$i]]);
															}
														}
													}
													$ss.='</b>';
													break;
												}
											}
										}
										$content.=$ss.'.';
										if(stripos($type3_foundids,' '.$a["id"].',')!==false)
										{
											$content.='</a>';
										}
										$content.='
';
									}
								}
								$content.='
</ul>';
							}
						}
                        for($j=0;$j<count($ok7);$j++)
						{
							$result=mysql_query("SELECT * FROM ".$this_table." WHERE id=".$ok7[$j][0]);
							$a = mysql_fetch_array($result);

							if(stripos($type3_foundids,' '.$a["id"].',')!==false || stripos($type3_foundparents,' '.$a["id"].',')!==false)
							{
								$ss='';
								$content.='<li class="ullist">';
								if(stripos($type3_foundids,' '.$a["id"].',')!==false)
								{
									$content.='<a href="'.$curdir.$kind.'/'.$obj->getName().'/'.$a["id"].'/act=view&valuestype=1">';
								}

								for($i=0;$i<count($show2);$i++)
								{
									if($i>0)
									{
										$ss.='; ';
									}

									foreach($filds2 as $f=>$v)
									{
										if($v->getName()==$show2[$i])
										{
											if($nam2[$i])
											{
												$ss.=$v->getSname().': ';
											}
											$ss.='<b>';
											if($a[$show2[$i]]=='')
											{
												if($show2[$i]==$obj->getName2() && ($a[$obj->getCode()]=='default' || $a[$obj->getCode()]=='1'))
												{
													$ss.='<i>по умолчанию</i>';
												}
												else
												{
													$ss.='<i>не определено</i>';
												}
											}
											else {
												if($spec2[$i]!='')
												{
													if($spec2[$i][0]==1 || $spec2[$i][0]==3)
													{
														$ss.=find5field($prefix.$spec2[$i][1],$spec2[$i][2],$a[$show2[$i]],$spec2[$i][3]);
													}
													elseif($spec2[$i][0]==2)
													{
														for($d=0;$d<count($spec2[$i][1]);$d++)
														{
															if($spec2[$i][1][$d][0]==$a[$show2[$i]])
															{
																$ss.=$spec2[$i][1][$d][1];
																break;
															}
														}
													}
												}
												else
												{
													if($v->getType()=="checkbox")
													{
														if($a[$show2[$i]]==1)
														{
															$ss.='<font color="green"><b>&#8730</b></font>';
														}
														else
														{
															$ss.='<font color="red"><b>X</b></font>';
														}
													}
													elseif($v->getType()=="calendar")
													{
														$content.=date("d.m.Y",strtotime($a[$show2[$i]]));
													}
													elseif($v->getType()=="timestamp")
													{
														$ss.=date("d.m.Y",$a[$show2[$i]]).' в '.date("G:i",$a[$show2[$i]]);
													}
													else
													{
														$ss.=decode2($a[$show2[$i]]);
													}
												}
											}
											$ss.='</b>';
											break;
										}
									}
								}
								$content.=$ss.'.';
								if(stripos($type3_foundids,' '.$a["id"].',')!==false)
								{
									$content.='</a>';
								}
								$content.='
';
							}
						}
					$content.='
</ul>';
					}
				$content.='
</ul>';
				}
				else
				{
					$result=mysql_query($query);

					$content.='<table class="menutable"><tr class="menu">';
					for($i=0;$i<count($this_sort[0]);$i++)
					{
						foreach($filds as $f=>$v)
						{
							if($v->getName()==$this_sort[0][$i][0] && $this_sort[0][$i][2])
							{
								if($sorting==($i*2+1))
								{
									$content.='<td><a href="'.$curdir.$kind.'/'.$obj->getName().'/page='.$page.'&sorting='.($i*2+2).'" title="[отсортировать : '.strtolower($v->getSname()).' : по убыванию]" class="arrow_up">'.$v->getSname().'</a></td>';
								}
								elseif($sorting==($i*2+2))
								{
									$content.='<td><a href="'.$curdir.$kind.'/'.$obj->getName().'/page='.$page.'&sorting='.($i*2+1).'" title="[отсортировать : '.strtolower($v->getSname()).' : по возрастанию]" class="arrow_down">'.$v->getSname().'</a></td>';
								}
								else
								{
									$content.='<td><a href="'.$curdir.$kind.'/'.$obj->getName().'/page='.$page.'&sorting='.($i*2+2).'" title="[отсортировать : '.strtolower($v->getSname()).' : по убыванию]">'.$v->getSname().'</a></td>';
								}
								break;
							}
						}
					}
					$content.='</tr>';
					$stringnum=1;
					while($a = mysql_fetch_array($result))
					{
						$content.='<tr>';
						for($i=0;$i<count($show);$i++)
						{
							$content.='<td><a href="'.$curdir.$kind.'/'.$obj->getName().'/'.$a["id"].'/act=view">';
							foreach($filds as $f=>$v)
							{
								if($v->getName()==$show[$i])
								{
									if($a[$show[$i]]=='') {$content.='<i>не определено</i>';}
									else {
										if($spec[$i]!='')
										{
											if($spec[$i][0]==1 || $spec[$i][0]==3)
											{
												$content.=find5field($prefix.$spec[$i][1],$spec[$i][2],$a[$show[$i]],$spec[$i][3]);
											}
											elseif($spec[$i][0]==2)
											{
												for($d=0;$d<count($spec[$i][1]);$d++)
												{
													if($spec[$i][1][$d][0]==$a[$show[$i]])
													{
														$content.=$spec[$i][1][$d][1];
														break;
													}
												}
											}
										}
										else
										{
											if($v->getType()=="checkbox")
											{
												if($a[$show[$i]]==1)
												{
													$content.='<font color="green"><b>&#8730</b></font>';
												}
												else
												{
													$content.='<font color="red"><b>X</b></font>';
												}
											}
											elseif($v->getType()=="calendar")
											{
												$content.=date("d.m.Y",strtotime($a[$show[$i]]));
											}
											elseif($v->getType()=="timestamp")
											{
												$content.=date("d.m.Y",$a[$show[$i]]).' в '.date("G:i",$a[$show[$i]]);
											}
											else
											{
												$content.=decode2($a[$show[$i]]);
											}
										}
									}
									break;
								}
							}
							$content.='</a></td>';
						}
						$content.='</tr>';
						$stringnum++;
					}
					$content.='</table>';
				}
			}
		}
	}

	if((($id!='' && !$stayhere && (($actiontype=="delete" && encode($_GET["ill"])=='') || $actiontype=="add" || ($actiontype=="change" && $trouble==false))) || ($id=='' && $act!="add")) || $this_type==1)
	{
		if($pagetotal>$grads) {
			if($this_type!=3) {
				$content.='<br /><br />';
			}
			else {
				$content.='<br />';
			}

			$content.='<center><table class="pagecount"><tr><td class="next">';
			if($page<($pagetotal/$grads)-1) {
				$content.='<a href="'.$curdir.$kind.'/'.$obj->getName().'/page='.($page+1).'&sorting='.$sorting.'">';
			}
			$content.='&#8592; Следующая';
			if($page<($pagetotal/$grads)-1) {
				$content.='</a>';
			}
			$content.='<br />';
			if($page!=ceil($pagetotal/$grads)-1 && $pagetotal>0) {
				$content.='<a href="'.$curdir.$kind.'/'.$obj->getName().'/page='.(ceil($pagetotal/$grads)-1).'&sorting='.$sorting.'" class="sm">';
			}
			else {
				$content.='<span class="sm">';
			}
			$content.='Последняя';
			if($page!=ceil($pagetotal/$grads)-1) {
				$content.='</a>';
			}
			else {
				$content.='</span>';
			}
			$content.='</td><td class="pagenums">';

			$totalobjects=$pagetotal;
			if($pagetotal==0) {
				$pagetotal=1;
			}
			$j=$page-5;
			if($j<1) {
				$j=1;
			}
			$d=ceil($pagetotal/$grads);
			if($d>$j+12) {
				$d=$j+12;
			}
			else {
				$j=$d-12;
			}
			if($j<1) {
				$j=1;
			}
			for($i=$d;$i>=$j;$i--)
			{
				if($i-1!=$page) {
					$content.='<a href="'.$curdir.$kind.'/'.$obj->getName().'/page='.($i-1).'&sorting='.$sorting.'">'.$i.'</a>';
				}
				else {
					$content.='<span class="selpage">'.$i.'</span>';
				}
			}
			$content.='<br />';
			$content.='<div class="pagegroup"><img src="'.$server_absolute_path.$direct.'/empty.gif" style="width: 100%;"></div>';

			$content.='<span class="sm">(на экране – до '.$grads.' позиций из '.$totalobjects.')</span></td>';
			$content.='<td class="previous">';
			if(ceil($totalobjects/$grads)-1>0 && $page>0) {
				$content.='<a href="'.$curdir.$kind.'/'.$obj->getName().'/page='.($page-1).'&sorting='.$sorting.'">';
			}
			$content.='Предыдущая &#8594;';
			if($page<($pagetotal/$grads)-1) {
				$content.='</a>';
			}
			$content.='<br />';
			if($page!=0 && ceil($pagetotal/$grads)-1>0) {
				$content.='<a href="'.$curdir.$kind.'/'.$obj->getName().'/sorting='.$sorting.'" class="sm">';
			}
			else {
				$content.='<span class="sm">';
			}
			$content.='Первая';
			if($page!=0 && ceil($pagetotal/$grads)-1>0) {
				$content.='</a>';
			}
			else {
				$content.='</span>';
			}
			$content.='</td></tr></table></center>';
		}
	}

	if($content!='') {
		$content.='</div></center>';
		$content.='
<!-- end '.$obj->getName().' object -->
';
	}

	return($content);
}
?>