<?php

class netSarissa extends netBaseElem {
	var $file; //абсолютный путь до файла для получения xml-информации sarissы
	var $file2; //относительный путь до файла для получения xml-информации sarissы
	var $parents; //массив видимых и внутренних названий полей, являющихся родительскими для финального поля, чье значение записывается в БД
	var $searchfield; //поле, с которым идет сравнение выбранной переменной при поиске
	var $table; //таблица, из которой тянутся данные
	var $parent; //ячейка $table, в которой хранится idшка родительского объекта
	var $order; //сортировка результатов выборки объектов для всех select'ов
	var $moreparams; //дополнительные параметры, которые нужно передавать в $file для получения нужного xml
	var $moreparams2; //дополнительные параметры, которые нужно применять в запросах к sql при выводе данных по уже сохраненным позициям

	function setFile($file) {
		$this->file=$file;
	}

	function setFile2($file2) {
		$this->file2=$file2;
	}

	function setParents($parents) {
		$this->parents=$parents;
	}

	function setSearchfield($searchfield) {
		$this->searchfield=$searchfield;
	}

	function setTable($table) {
		$this->table=$table;
	}

	function setParent($parent) {
		$this->parent=$parent;
	}

	function setOrder($order) {
		$this->order=$order;
	}

	function setMoreParams($moreparams) {
		$this->moreparams=$moreparams;
	}

	function setMoreParams2($moreparams2) {
		$this->moreparams2=$moreparams2;
	}

	function getFile() {
		return ($this->file);
	}

	function getFile2() {
		return ($this->file2);
	}

	function getParents() {
		return ($this->parents);
	}

	function getSearchfield() {
		return ($this->searchfield);
	}

	function getTable() {
		return ($this->table);
	}

	function getParent() {
		return ($this->parent);
	}

	function getOrder() {
		return ($this->order);
	}

	function getMoreParams() {
		return ($this->moreparams);
	}

	function getMoreParams2() {
		return ($this->moreparams2);
	}

	function netSarissa($params) {
		$this->netBaseElem($params);
		$this->setFile($params["file"]);
		$this->setFile2($params["file2"]);
		$this->setParents($params["parents"]);
		if($params["searchfield"]=='') {
			$params["searchfield"]="id";
		}
		$this->setSearchfield($params["searchfield"]);
		$this->setTable($params["table"]);
		$this->setParent($params["parent"]);
		$this->setOrder($params["order"]);
		$this->setMoreParams($params["moreparams"]);
		$this->setMoreParams2($params["moreparams2"]);
	}

	function draw($type, $can, $linenum) {
		if($this->getFile2()!='') {
			include_once($this->getFile2());
		}
		$name=$this->getName();
		$parent=$this->getParent();

		$alls[$name]=$this->getVal();
		$parents_value=$this->getParents();

		$result=mysql_query("SELECT * FROM ".$this->getTable()." WHERE ".$this->getSearchfield()."=".$alls[$name]);
		$a=mysql_fetch_array($result);
		if($parents_value!='search') {
			$parents[$name]=$a[$parent];
			$check=$parents[$name];

			for($j=1; $j<=count($parents_value); $j++)
			{
				$check2=count($parents_value)-$j;
				$check2=$parents_value[$check2][0];
				$alls[$check2]=$check;

				$result=mysql_query("SELECT * FROM ".$this->getTable()." WHERE ".$this->getSearchfield()."=".$check);
				$a=mysql_fetch_array($result);
				$parents[$check2]=$a[$parent];
				$check=$a[$parent];
			}
		}

		if($can=="write")
		{
			$content.=$this->trueDraw($alls, $parents, $linenum);
		}
		else
		{
			$linkatbegin=$this->getLinkAtBegin();
			if($linkatbegin!='') {
                if(stripos($linkatbegin,'{value}')!==false) {
                	$linkatbegin=str_replace('{value}',$alls[$name],$linkatbegin);
                }
				$content.=$linkatbegin;
			}
			if($parents_value!='search') {
				for($j=0; $j<count($parents_value); $j++)
				{
					$content.='<span class="sarissafieldname">'.$parents_value[$j][1].'</span>: ';
					$result2=mysql_query("SELECT * FROM ".$this->getTable()." WHERE ".$this->getSearchfield()."=".$alls[$parents_value[$j][0]]);
					$b=mysql_fetch_array($result2);

					if (function_exists(printOut))
					{
						$content.=printOut($b[$this->getSearchfield()]).'<br />
';
					}
					else
					{
						$content.=decode($b["name"]).'<br />
';
					}
				}
			}

			if($parents_value!='search') {
				$content.='<span class="sarissafieldname">'.$this->getSname().'</span>: ';
			}
			else {
				//$content.='<div class="fieldname">'.$this->getSname().'</div>';
			}

			$result2=mysql_query("SELECT * FROM ".$this->getTable()." WHERE ".$this->getSearchfield()."=".$alls[$name]);
			$b=mysql_fetch_array($result2);
			$printout="printOut".$this->getName();
			if(function_exists($printout)) {
				$content.=$printout($b[$this->getSearchfield()]);
			}
			else {
				$content.=decode($b["name"]);
			}
			if($this->getLinkAtEnd()!='') {
				$content.=$this->getLinkAtEnd();
			}
		}
		return($content);
	}

	function trueDraw($alls, $parents, $linenum) {
		if($this->getFile2()!='') {
			include_once($this->getFile2());
		}
		$name=$this->getName();
		$parents_value=$this->getParents();
		$moreparams_value=$this->getMoreParams();
		$moreparams2_value=$this->getMoreParams2();

		$GLOBALS['sarissa']+=1;

		if(isset($linenum))
		{
			$linenum+=0;
			$linenum='['.$linenum.']';
		}
		else
		{
			$linenum='';
		}

		for($j=0;$j<count($moreparams_value);$j++)
		{
			$moreparams.=$moreparams_value[$j][0].'='.$moreparams_value[$j][1].'&';
		}

		if($parents_value!='search') {
			for($j=0;$j<count($parents_value);$j++)
			{
				$content.='<select class="sarissa'.($j==0&&$this->getMustBe()?' mustbe':'').'"';
				/*if($this->getWidth()!='')
				{
					$content.=' style="width: '.$this->getWidth().';"';
				}*/
				$content.=' id="AJAX'.$parents_value[$j][0].$GLOBALS['sarissa'].'" action="'.$this->getFile().'?'.$moreparams.'" target="';
				if($parents_value[$j+1][0]!='')
				{
					$content.='AJAX'.$parents_value[$j+1][0].$GLOBALS['sarissa'];
				}
				else
				{
					$content.='AJAX'.$name.$GLOBALS['sarissa'];
				}
				$content.='"';
				if($moreparams!='') {
					$content.=' moreparams="'.$moreparams.'"';
				}
				if($parents_value[$j+1][1]!='') {
					$content.=' defaultchoicename="'.$parents_value[$j+1][1].'"';
				}
				if($alls[$name]==0 && $j>0)
				{
					$content.=' disabled="true"><br />';
				}
				else
				{
					if($j==0 && $alls[$name]==0) {
						$parents[$parents_value[$j][0]]=0;
					}
					$content.='><option value="">- '.$parents_value[$j][1].' -</option>';
					if($this->getOrder()=='')
					{
						$query="SELECT * FROM ".$this->getTable()." WHERE ".$this->getParent()."=".$parents[$parents_value[$j][0]].$moreparams2_value;
					}
					else
					{
						$query="SELECT * FROM ".$this->getTable()." WHERE ".$this->getParent()."=".$parents[$parents_value[$j][0]].$moreparams2_value." order by ".$this->getOrder();
					}
					$result2=mysql_query($query);
					while($b=mysql_fetch_array($result2))
					{
						$content.='<option value="'.$b[$this->getSearchfield()].'"';
						if($b[$this->getSearchfield()]==$alls[$parents_value[$j][0]])
						{
							$content.=' selected';
						}
						$printout="printOut".$this->getName();
						if(function_exists($printout))
						{
							$content.='>'.$printout($b[$this->getSearchfield()]).'</option>';
						}
						else
						{
							$content.='>'.decode($b["name"]).'</option>';
						}
					}
				}
				$content.='</select><br />';
			}
		}
		else {
			$content.='<input type="text" class="sarissa'.($this->getMustBe()?' mustbe':'').'"';
			/*if($this->getWidth()!='') {
				$content.=' style="width: '.$this->getWidth().';"';
			}*/
			$content.=' class="inputtext" placehold="Введите текст для поиска" autocomplete="off" id="AJAXsearch'.$GLOBALS['sarissa'].'" action="'.$this->getFile().'?'.$moreparams.'" target="AJAX'.$this->getName().$GLOBALS['sarissa'].'" value=""><br>';
		}
		if($alls[$name]==0)
		{
			$content.='<select';
			/*if($this->getWidth()!='')
			{
				$content.=' style="width: '.$this->getWidth().';"';
			}*/
			$content.=' id="AJAX'.$name.$GLOBALS['sarissa'].'" name="'.$name.$linenum.'" disabled="true"></select>';
		}
		else
		{
			$content.='<select';
			/*if($this->getWidth()!='')
			{
				$content.=' style="width: '.$this->getWidth().';"';
			}*/
			$content.=' id="AJAX'.$name.$GLOBALS['sarissa'].'" name="'.$name.$linenum.'"><option value="">- Выбрать -</option>';
			if($parents_value!='search') {
				if($this->getOrder()=='')
				{
					$query="SELECT * FROM ".$this->getTable()." WHERE parent=".$parents[$name].$moreparams2_value;
				}
				else
				{
					$query="SELECT * FROM ".$this->getTable()." WHERE parent=".$parents[$name].$moreparams2_value." order by ".$this->getOrder();
				}
			}
			else {
				$query="SELECT * FROM ".$this->getTable()." WHERE ".$this->getSearchfield()."=".$alls[$name];
			}
			$result2=mysql_query($query);
			while($b=mysql_fetch_array($result2))
			{
				$content.='<option value="'.$b[$this->getSearchfield()].'"';
				if($b[$this->getSearchfield()]==$alls[$name])
				{
					$content.=' selected';
				}
				$printout="printOut".$this->getName();
				if(function_exists($printout))
				{
					$content.='>'.$printout($b[$this->getSearchfield()]).'</option>';
				}
				else
				{
					$content.='>'.decode($b["name"]).'</option>';
				}
			}
			$content.='</select>';
		}

		return($content);
	}

	function destroy() {
		unset($this);
	}
}

?>