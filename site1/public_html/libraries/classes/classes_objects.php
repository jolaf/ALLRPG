<?php

class netObj {
	var $name; //название объекта (необходимо для корректной работы dynamicaction)
	var $table; //таблица данных объекта
	var $word1; //слово, добавляемое к кнопкам "сохранить", "создать" и "удалить" в объекте
	var $word2=Array(); //массив, хранящий в себе три основные фразы по работе с данными объекта: "Объект успешно добавлен", "Объект успешно изменен", "Объект успешно удален". Слова меняются для корректного отображения смысла на экране.
	var $sort=Array(); //sort: см. ниже
	var $type; //тип: 1 - один экран, равные позиции строчками друг за другом (самый старый); 2 - выбор из равных позиций на cb_editor'е; 3 - выбор из дерева позиций на cb_editor'е (многоуровневый каталог).
	var $size; //ширина объекта
	var $right; //права пользователя на данный объект
	var $elemperpage; //кол-во выводимых на страницу строк в объекте
	var $elems=Array(); //все элементы данного объекта
	var $search=Array(); //массив названий элементов, по которым должен вестись поиск
	var $virtualfield; //опциональный параметр, указывающий на колонку, в которой нужно хранить данные виртуальных полей, сделанных конструктором

/*
sort - по каким рядам таблицы выводить данные при входе в раздел? Количество массивов сортировки должно соответствовать количеству массиву управляющих данных (т.к. у каждого типа в каталоге сортировка может быть по своим, различающимся принципам).
	0 - название ряда/переменной (id автоматически не демонстрируется, если сортировать по нему);
	1 - порядок сортировки "ASC|DESC"
	2 - показывать ли в сводной табличке данную переменную?
	3 - показывать ли в сводной табличке видимое название данной переменной?
	4 - что показывать вместо стандартного значения, взятого из таблиц? Первый тип работает на основе функции find5field. Крайне не рекомендуется даже пытаться вытягивать данные для функции из всяких textfield'ов, multiselect'ов и прочих не однострочных параметров. Второй тип работает на основе забитого в конфигурационном файле массива.
		0 - тип подмены значения: 1 = поиск по табличкам; 2 = выбор из забитого массива; 3 = поиск по табличкам и сортировка по тем же табличкам, а не по основной;
		1 - в какой табличке искать? / забитый массив
		2 - с каким параметром сравнивать значение ссылки, записанное в ту таблицу, с которой мы работаем?
		3 - какую ячейку выцепить из "родительской" таблицы и выдать в качестве результата?
*/

  var $default_sort_column; // Режим сортировки «по умолчанию», если никакая сортировка не выбрана. Равен нужному значению sorting в URI
  
  function setDefaultSort($columnName, $ascDesc) {
    foreach ($this -> sort[0] as $sortNumber => $sortElement)
    {
      if ($sortElement[0] == $columnName)
      {
        $columnName = $sortNumber + 1;
        break;
      }
    }
    if (strtolower($ascDesc) == 'asc')
    {
      $ascDesc = 0;
    } else if (strtolower($ascDesc) == 'desc')
    {
      $ascDesc = 1;
    }
    $this -> default_sort_column = $columnName * 2 + $ascDesc - 1;
  }

	function setName($name) {
		$this->name=$name;
	}

	function setTable($table) {
		$this->table=$table;
	}

	function setWord1($word1) {
		$this->word1=$word1;
	}

	function setWord2($word2) {
		$this->word2=$word2;
	}

	function setSort($sort) {
		$this->sort=$sort;
	}

	function setType($type) {
		$this->type=$type;
	}

	function setSize($size) {
		$this->size=$size;
	}

	function setElemPerPage($elemperpage) {
		$this->elemperpage=$elemperpage;
	}

	function setSearch($search) {
		$new=clone($search);
		$new->setName('search_'.$search->getName());
		$new->setMustBe(false);
		$new->setValue('');
		$new->setDefault('');
		$new->setLinkAtBegin('');
		$new->setLinkAtEnd('');
		if($new->getType()=="multiselect")
		{
			$new->setOne(false);
		}
		$this->search[]=$new;
	}

	function setVirtualField($virtualfield) {
		$this->virtualfield=$virtualfield;
	}

	function setRight($right) {
		$this->right=$right;
	}

	function setElem($elem) {
		$this->elems[]=$elem;
	}

	function setColorpickersCount($colorpickerscount) {
		$this->colorpickerscount=$colorpickerscount;
	}

	function remElem($elem) {
		for($i=0;$i<count($this->elems);$i++)
		{
			if($this->elems[$i]==$elem)
			{
				unset($this->elems[$i]);
				break;
			}
		}
	}

	function getName() {
		return($this->name);
	}

	function getTable() {
		return($this->table);
	}

	function getWord1() {
		return($this->word1);
	}

	function getWord2() {
		return($this->word2);
	}

	function getSort() {
		return($this->sort);
	}

	function getType() {
		return($this->type);
	}

	function getSize() {
		if(stripos($this->size,'px')!==false || stripos($this->size,'%')!==false) {
			return($this->size);
		}
		elseif($this->size>0) {
			return($this->size.'px');
		}
	}

	function getElemPerPage() {
		return($this->elemperpage);
	}

	function getSearch() {
		return($this->search);
	}

	function getVirtualField() {
		return($this->virtualfield);
	}

	function getRight() {
		return($this->right);
	}

	function getElems() {
		return($this->elems);
	}

	function getElem($i) {
		return($this->elems[$i]);
	}
	
	function getDefaultSort() {
    return $this -> default_sort_column;
	}

	function draw() {
		require_once($GLOBALS["server_inner_path"].$GLOBALS["direct"]."/dynamiccreate.php");
		$content=dynamiccreate($this);
		return ($content);
	}

	function netObj($name,$table,$word1,$word2,$sort,$type,$size,$elemperpage,$virtualfield) {
		$this->setName($name);
		$this->setTable($table);
		$this->setWord1($word1);
		$this->setWord2($word2);
		$this->setSort($sort);
		$this->setType($type);
		$this->setSize($size);
		$this->setElemPerPage($elemperpage);
		$this->setVirtualField($virtualfield);
		$this -> default_sort_column = 0;
	}
}

class netObj2 extends netObj {
	var $elems2=Array(); //все элементы наследующего объекта
	var $parent; //в каком столбце хранится id родителя наследующего объекта?
	var $content; //в каком столбце хранится содержимое объекта (например, у страниц = текст, а у разделов = {menu})?
	var $code; //в каком столбце хранится уникальный код, выставляемый пользователем для сортировки по нему?
	var $name2; //в каком столбце хранятся названия объектов?
	var $word3; //слово, добавляемое к кнопкам "сохранить", "создать" и "удалить" при работе с наследующими данными в объекте
	var $word4=Array(); //массив, хранящий в себе три основные фразы по работе с наследующими данными объекта: "Объект успешно добавлен", "Объект успешно изменен", "Объект успешно удален". Слова меняются для корректного отображения смысла на экране.

	function setElem2($elem2) {
		$this->elems2[]=$elem2;
	}

	function remElem2($elem2) {
		for($i=0;$i<count($this->elems2);$i++)
		{
			if($this->elems2[$i]==$elem2)
			{
				unset($this->elems2[$i]);
				break;
			}
		}
	}

	function setParent($parent) {
		$this->parent=$parent;
	}

	function setContent($content) {
		$this->content=$content;
	}

	function setCode($code) {
		$this->code=$code;
	}

	function setName2($name2) {
		$this->name2=$name2;
	}

	function setWord3($word3) {
		$this->word3=$word3;
	}

	function setWord4($word4) {
		$this->word4=$word4;
	}

	function setSearch2($search) {
		$new=clone($search);
		$new->setName('search2_'.$search->getName());
		$new->setMustBe(false);
		$new->setValue('');
		$new->setDefault('');
		$new->setLinkAtBegin('');
		$new->setLinkAtEnd('');
		if($new->getType()=="multiselect")
		{
			$new->setOne(false);
		}
		$this->search[]=$new;
	}

	function getElems2() {
		return($this->elems2);
	}

	function getElem2($i) {
		return($this->elems2[$i]);
	}

	function getParent() {
		return($this->parent);
	}

	function getContent() {
		return($this->content);
	}

	function getCode() {
		return($this->code);
	}

	function getName2() {
		return($this->name2);
	}

	function getWord3() {
		return($this->word3);
	}

	function getWord4() {
		return($this->word4);
	}

	function netObj2($name,$table,$word1,$word2,$word3,$word4,$sort,$type,$size,$elemperpage,$parent,$content,$code,$name2,$virtualfield) {
		$this->netObj($name,$table,$word1,$word2,$sort,$type,$size,$elemperpage,$virtualfield);
		$this->setParent($parent);
		$this->setContent($content);
		$this->setCode($code);
		$this->setName2($name2);
		$this->setWord3($word3);
		$this->setWord4($word4);
	}
}

class netBaseElem {
	var $name; //имя поля, совпадающее с именем ячейки в таблице БД
	var $sname; //видимое имя поля
	var $type; //тип поля
	var $help; //подсказка по полю
	var $default; //значение по умолчанию
	var $value; //значение
	var $read; //уровень прав в объекте, необходимый для чтения поля
	var $write; //уровень прав в объекте, необходимый для записи поля
	var $mustbe; //обязательность поля
	var $width; //ширина
	var $height; //высота
	var $br; //перенос строки после названия элемента (true|false)
	var $virtual; //виртуально это поле или нет (true|false)
	var $linkatbegin; //html перед вставлением value данного элемента (для создания ссылок)
	var $linkatend; //html после вставления value данного элемента (для создания ссылок)
	
	var $valueFunctor; //lamda, которая создает value из строки и имени

  function callFunctor ($row)
  {
    $func = $this -> valueFunctor;
		return $func($this, $row);
  }
  
	function setVal($a,$post,$linenum) {
    
		$value='';
		$name=$this->getName();

		if($post)
		{
			if(isset($linenum)) {
				$value=$_POST[$name][$linenum];
			}
			else
			{
				$value=$_POST[$name];
			}
		}
		else {
			$value= $this -> callFunctor ($a);
		}
		$this->setValue($value);
	}

	function setName($name) {
		$this->name=$name;
	}

	function setSname($sname) {
		$this->sname=$sname;
	}

	function setType($type) {
		$this->type=$type;
	}

	function setHelp($help) {
		$this->help=$help;
	}

	function setDefault($default) {
		$this->default=$default;
	}

	function setValue($value) {
		$this->value=$value;
	}

	function setRead($read) {
		$this->read=$read;
	}

	function setWrite($write) {
		$this->write=$write;
	}

	function setMustbe($mustbe) {
		$this->mustbe=$mustbe;
	}

	function setWidth($width) {
		$this->width=$width;
	}

	function setHeight($height) {
		$this->height=$height;
	}

	function setBr($br) {
		$this->br=$br;
	}

	function setVirtual($virtual) {
		$this->virtual=$virtual;
	}

	function setLinkAtBegin($linkatbegin) {
		$this->linkatbegin=$linkatbegin;
	}

	function setLinkAtEnd($linkatend) {
		$this->linkatend=$linkatend;
	}

	function getName() {
		return($this->name);
	}

	function getSname() {
		return($this->sname);
	}

	function getType() {
		return($this->type);
	}

	function getHelp() {
		return($this->help);
	}

	function getDefault() {
		return($this->default);
	}

	function getValue() {
		return($this->value);
	}

	function getVal() {
		if($this->getValue()!='' && !($this->getType()=="multiselect" && $this->getValue()=='-'))
		{
			return($this->getValue());
		}
		else
		{
			return($this->getDefault());
		}
	}

	function getRead() {
		return($this->read);
	}

	function getWrite() {
		return($this->write);
	}

	function getMustBe() {
		return($this->mustbe);
	}

	function getWidth() {
		if(stripos($this->width,'px')!==false || stripos($this->width,'%')!==false) {
			return($this->width);
		}
		elseif($this->width>0) {
			return($this->width.'px');
		}
	}

	function getHeight() {
		return($this->height);
	}

	function getBr() {
		return($this->br);
	}

	function getVirtual() {
		return($this->virtual);
	}

	function getLinkAtBegin() {
		return($this->linkatbegin);
	}

	function getLinkAtEnd() {
		return($this->linkatend);
	}
	
	function isEmpty(){
    return $this->getVal()=='' || ($this->getType()=="multiselect" && ($this->getVal()=='-' || $this->getVal()=='--')) || ($this->getType()=="select" && $this->getVal()==0);
	}
	
	function isAnySelect() {
    return ($this->getType()=="select" || $this->getType()=="multiselect"); //TODO: make this more polymorhic way.
	}
	
	function isExcelSupported() { //TODO: make this more polymorhic way.
    $type = $this -> getType();
    return $type!="h1" && $type!="file" && $type!="timestamp" && $type!="hidden";
	}

	function netBaseElem($params) {
		$this->setName($params["name"]);
		$this->setSname($params["sname"]);
		$this->setType($params["type"]);
		$this->setHelp($params["help"]);
		$this->setDefault($params["default"]);
		$this->setRead($params["read"]);
		$this->setWrite($params["write"]);
		$this->setMustbe($params["mustbe"]);
		$this->setWidth($params["width"]);
		$this->setHeight($params["height"]);
		$this->setBr($params["br"]);
		$this->setVirtual($params["virtual"]);
		$this->setLinkAtBegin($params["linkatbegin"]);
		$this->setLinkAtEnd($params["linkatend"]);
		if (array_key_exists ('valueExtractor', $params))
		{
      $this -> valueFunctor = $params ['valueExtractor'];
		}
		else {
      $this -> valueFunctor = function ($obj, $row)
      {
        return decode ($row [$obj -> name]);
      };
    }
	}

	function draw() {
		die("Cannot draw base element.");
	}
}

class netHidden extends netBaseElem {
	function netHidden($params) {
		$this->netBaseElem($params);
		$this->setDefault($params["default"]);
	}

	function draw($type, $can, $linenum) {
		if($can=="write")
		{
			$content.=$this->trueDraw($linenum);
		}
		return($content);
	}

	function trueDraw($linenum) {
		$value=$this->getDefault();
		$name=$this->getName();

		if(isset($linenum))
		{
			$linenum+=0;
			$linenum='['.$linenum.']';
		}
		else
		{
			$linenum='';
		}

		$content.='<input type="hidden" name="'.$name.$linenum.'" value="'.$value.'" />';

		return($content);
	}

	function destroy() {
		unset($this);
	}
}

class netH1 extends netBaseElem {

	function netH1($params) {
		$this->netBaseElem($params);
	}

	function draw($type, $can, $linenum) {
		if($type!=1) {
			if($this->getDefault()!='' && str_replace(" ","",$this->getSname())=='') {
				$content.=$this->getDefault();
			}
			else {
				$content.='<h1 class="data_h1">'.$this->getSname().'</h1>
';
			}
		}
		return($content);
	}

	function destroy() {
		unset($this);
	}
}

class netTimeStamp extends netBaseElem {
	var $show; //показывать ли данный таймстамп в соответствующей своей колонке при представлении данных объекта в виде типа 1?

	function setVal($a,$post,$linenum) {
		$value='';
		$name=$this->getName();
		if($a[$name]!='') {
			$value=decode($a[$name]);
		}
		else
		{
			$value=time();
		}
		$this->setValue($value);
	}

	function setShow($show) {
		$this->show=$show;
	}

	function getShow() {
		return ($this->show);
	}

	function netTimeStamp($params) {
		$this->netBaseElem($params);
		$this->setShow($params["show"]);
	}

	function draw($type, $can, $linenum) {
		if($can=="write")
		{
			if($type!=1)
			{
				$content.='<input type="hidden" name="'.$this->getName().'" value="'.$this->getVal().'" class="timestamp"/>';
			}
			else
			{
				if($this->getShow())
				{
					$content.=$this->trueDraw($linenum);
				}
				else
				{
					if(isset($linenum))
					{
						$linenum+=0;
						$linenum='['.$linenum.']';
					}
					else
					{
						$linenum='';
					}

					$content.='<input type="hidden" name="'.$this->getName().$linenum.'" value="'.$this->getVal().'" class="timestamp"/>';
				}
			}
		}
		elseif($can=="read" && $type==1 && $this->getShow())
		{
			$content.=$this->trueDraw($linenum);
		}
		return($content);
	}

	function trueDraw($linenum) {
		$value=$this->getVal();
		$name=$this->getName();

		if(isset($linenum))
		{
			$linenum+=0;
			$linenum='['.$linenum.']';
		}
		else
		{
			$linenum='';
		}

		$content.='<center><i>Время</i>: '.date("G:i",$value).'<br>
<i>Дата</i>: '.date("d.m.Y",$value);
		$content.='<input type="hidden" name="'.$name.$linenum.'" value="'.$value.'" />';

		return($content);
	}

	function destroy() {
		unset($this);
	}
}


/*ПЕРЕРАБОТАТЬ ФУНКЦИЮ ПОД НОВЫЕ УСЛОВИЯ*/

#*************************************************************
function unmakevirtual($a)
{
	$b=Array();

	if($a!='')
	{
		$css=$a;
		$pos = strpos($css, "]&lt;br&gt;");
		while (!($pos===false)) {
			$st1 = substr($css,0,$pos+10);

			$pos2 = strpos($st1, "]");
			$ce1 = substr($st1,1,$pos2-1);
			$st1 = substr($st1,$pos2+1,strlen($st1));
			$pos2 = strpos($st1, "]");
			$ce2 = substr($st1,1,$pos2-1);
			$st1 = substr($st1,$pos2+1,strlen($st1));

			$b[$ce1] = decode3($ce2);

			$css = substr($css,$pos+11,strlen($css));
			$pos = strpos($css, "]&lt;br&gt;");
			if ($pos === false) break;
		}
	}

	return($b);
}

?>